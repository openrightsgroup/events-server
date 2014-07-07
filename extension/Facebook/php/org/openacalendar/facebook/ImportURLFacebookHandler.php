<?php

namespace org\openacalendar\facebook;

use import\ImportURLHandlerBase;
use import\ImportedEventsToEvents;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookAuthorizationException;
use models\ImportedEventModel;
use models\ImportURLResultModel;
use repositories\ImportedEventRepository;

/**
 *
 * @package org.openacalendar.facebook
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ImportURLFacebookHandler extends ImportURLHandlerBase {
	
	protected $eventId;


	
	
	public function canHandle() {
		global $app;
		
		$extension = $app['extensions']->getExtensionById('org.openacalendar.facebook');
		$appID = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('app_id'));
		$appSecret = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('app_secret'));
		$userToken = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('user_token'));
		
		$urlBits = parse_url($this->importURLRun->getRealURL());
		
		if ($urlBits['host']== 'facebook.com' || $urlBits['host']== 'www.facebook.com')  {
			
			$bits =  explode("/",$urlBits['path']);
			
			if ($bits[1] == 'events' && $bits[2] && $appID && $appSecret && $userToken) {
				$this->eventId = $bits[2];
				return true;
			}
			
		}
		
		return false;
	}

	protected $countNew, $countExisting, $countSaved, $countInPast, $countToFarInFuture, $countNotValid;
	
	/** @var \import\ImportedEventsToEvents **/
	protected $importedEventsToEvents;

	public function handle() {
		global $app;

		$this->countNew = 0;
		$this->countExisting = 0;
		$this->countSaved = 0;
		$this->countInPast = 0;
		$this->countToFarInFuture = 0;
		$this->countNotValid = 0;
		
		$extension = $app['extensions']->getExtensionById('org.openacalendar.facebook');
		$appID = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('app_id'));
		$appSecret = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('app_secret'));
		$userToken = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('user_token'));
		
		FacebookSession::setDefaultApplication($appID, $appSecret);
		
		$this->importedEventsToEvents = new ImportedEventsToEvents();
		$this->importedEventsToEvents->setFromImportURlRun($this->importURLRun);
		
		$iurlr = new ImportURLResultModel();
		$iurlr->setIsSuccess(true);
		$iurlr->setMessage("Facebook data found");
				
		if ($this->eventId && $appID && $appSecret && $userToken) {
		
			try {
				$fbData = $this->getFBDataForEventID($this->eventId);
				if ($fbData) {
					$this->processFBData($this->eventId, $fbData);
				}
			} catch (FacebookAuthorizationException $err) {
				$iurlr->setIsSuccess(false);
				$iurlr->setMessage("Facebook API error: ". $err->getCode()." ".$err->getMessage());
			}
		
		}

		// Now run the thing to make imported events real events!
		$this->importedEventsToEvents->run();

		$iurlr->setNewCount($this->countNew);
		$iurlr->setExistingCount($this->countExisting);
		$iurlr->setSavedCount($this->countSaved);
		$iurlr->setInPastCount($this->countInPast);
		$iurlr->setToFarInFutureCount($this->countToFarInFuture);
		$iurlr->setNotValidCount($this->countNotValid);
		return $iurlr;
		
	}	
	
	protected function getFBDataForEventID($id) {
		global $app;
		
		$extension = $app['extensions']->getExtensionById('org.openacalendar.facebook');
		$userToken = $app['appconfig']->getValue($extension->getAppConfigurationDefinition('user_token'));
		$session = new FacebookSession($userToken);
		
		$url = '/' . strval($this->eventId);
		$request = new FacebookRequest($session, 'GET', $url);
		$response = $request->execute();
		$graphObject = $response->getGraphObject();

		// This tests 2 things
		// 1) Can some events not have a start and a end time set? Saw comments that suggested this.
		// 2) How do we know this FB event is an event? It could be group or a page. 
		if ($graphObject->getProperty('start_time')) {
		
			return array(
				'name' => $graphObject->getProperty('name'),
				'description' => $graphObject->getProperty('description'),
				'ticket_uri' => $graphObject->getProperty('ticket_uri'),
				'start_time' => $graphObject->getProperty('start_time'),
				'end_time' => $graphObject->getProperty('end_time'),
				'timezone' => $graphObject->getProperty('timezone'),
				'is_date_only' => $graphObject->getProperty('is_date_only'),
				'url' => 'https://www.facebook.com/events/'.$id,
			);
		
		}
		
	}
	
	protected function processFBData($id, $fbData) {
		global $CONFIG;
		$start = new \DateTime($fbData['start_time'], new \DateTimeZone('UTC'));
		if ($fbData['end_time']) {
			$end = new \DateTime($fbData['end_time'], new \DateTimeZone('UTC'));
		} else {
			$end = clone $start;
		}
		if ($start && $end && $start <= $end) { 
			if ($end->getTimeStamp() < \TimeSource::time()) {
				$this->countInPast++;
			} else if ($start->getTimeStamp() > \TimeSource::time()+$CONFIG->importURLAllowEventsSecondsIntoFuture) {
				$this->countToFarInFuture++;
			} else {
		
				$importedEventRepo = new \repositories\ImportedEventRepository();
				$importedEvent = $importedEventRepo->loadByImportURLIDAndImportId($this->importURLRun->getImportURL()->getId() ,$id);

				$changesToSave = false;
				if (!$importedEvent) {
					++$this->countNew;
					$importedEvent = new ImportedEventModel();						
					$importedEvent->setImportId($id);
					$importedEvent->setImportUrlId($this->importURLRun->getImportURL()->getId());
					$this->setImportedEventFromFBData($importedEvent, $fbData);							
					$changesToSave = true;
				} else {
					++$this->countExisting;
					$changesToSave = $this->setImportedEventFromFBData($importedEvent, $fbData);
					// if was deleted, undelete
					if ($importedEvent->getIsDeleted()) {
						$importedEvent->setIsDeleted(false);
						$changesToSave = true;
					}
				}
				if ($changesToSave && $this->countSaved < $this->limitToSaveOnEachRun) {
					++$this->countSaved;
					$this->importedEventsToEvents->addImportedEvent($importedEvent);

					if ($importedEvent->getId()) {
						if ($importedEvent->getIsDeleted()) {
							$importedEventRepo->delete($importedEvent);
						} else {
							$importedEventRepo->edit($importedEvent);
						}
					} else {
						$importedEventRepo->create($importedEvent);
					}
				}		

			}
		}
	}
	
	protected function setImportedEventFromFBData(ImportedEventModel $importedEvent, $fbData) {
		$changesToSave = false;
		if ($importedEvent->getDescription() != $fbData['description']) {
			$importedEvent->setDescription($fbData['description']);
			$changesToSave = true;
		}
		$start = new \DateTime($fbData['start_time'], new \DateTimeZone('UTC'));
		if ($fbData['end_time']) {
			$end = new \DateTime($fbData['end_time'], new \DateTimeZone('UTC'));
		} else {
			$end = clone $start;
		}
		if ($fbData['is_date_only']) {
			$start->setTime(0,0,0);
			$end->setTime(23, 59, 59);
		}
		if (!$importedEvent->getStartAt() || $importedEvent->getStartAt()->getTimeStamp() != $start->getTimeStamp()) {
			$importedEvent->setStartAt($start);
			$changesToSave = true;
		}
		if (!$importedEvent->getEndAt() || $importedEvent->getEndAt()->getTimeStamp() != $end->getTimeStamp()) {
			$importedEvent->setEndAt($end);
			$changesToSave = true;
		}
		if ($importedEvent->getTitle() != $fbData['name']) {
			$importedEvent->setTitle($fbData['name']);
			$changesToSave = true;
		}
		if ($importedEvent->getUrl() != $fbData['url']) {
			$importedEvent->setUrl($fbData['url']);
			$changesToSave = true;
		}
		if ($importedEvent->getTimezone() != $fbData['timezone']) {
			$importedEvent->setTimezone($fbData['timezone']);
			$changesToSave = true;
		}
		if ($importedEvent->getTicketUrl() != $fbData['ticket_uri']) {
			$importedEvent->setTicketUrl($fbData['ticket_uri']);
			$changesToSave = true;
		}
		return $changesToSave;
	}
	
}

