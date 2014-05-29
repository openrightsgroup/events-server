<?php



namespace repositories;

use models\EventModel;
use models\EventHistoryModel;
use models\EventRecurSetModel;
use models\UserAccountModel;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class EventRecurSetRepository {

	
	public function getForEvent(EventModel $event) {
		global $DB;
		
		$eventRecurSet = $this->loadForEvent($event);
		if (!$eventRecurSet) {
			
			try {
				$DB->beginTransaction();
				
				$stat = $DB->prepare("INSERT INTO event_recur_set (created_at) VALUES (:created_at) RETURNING id");
				$stat->execute(array( 'created_at'=>  \TimeSource::getFormattedForDataBase() ));
				$data = $stat->fetch();
				$eventRecurSet = new EventRecurSetModel();
				$eventRecurSet->setId($data['id']);

				$stat = $DB->prepare("UPDATE event_information SET event_recur_set_id = :ersi WHERE id = :id");
				$stat->execute(array('ersi'=>$eventRecurSet->getId(), 'id'=>$event->getId()));
				
				$DB->commit();
			} catch (Exception $e) {
				$DB->rollBack();
			}
		}
		
		return $eventRecurSet;
		
	}
	
	
	public function loadForEvent(EventModel $event) {
		global $DB;
		if ($event->getEventRecurSetId()) {
			$stat = $DB->prepare("SELECT event_recur_set.* FROM event_recur_set WHERE id =:id");
			$stat->execute(array( 'id'=>$event->getEventRecurSetId() ));
			if ($stat->rowCount() > 0) {
				$eventRecurSet = new EventRecurSetModel();
				$eventRecurSet->setFromDataBaseRow($stat->fetch());
				return $eventRecurSet;
			}
		}
	}
	
	
	
}


