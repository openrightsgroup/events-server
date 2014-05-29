<?php

namespace siteapi1\controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use models\SiteModel;
use models\CountryModel;
use models\EventModel;
use repositories\CountryRepository;
use repositories\builders\GroupRepositoryBuilder;
use repositories\EventRepository;
use repositories\builders\EventRepositoryBuilder;
use repositories\builders\HistoryRepositoryBuilder;
use api1exportbuilders\EventListICalBuilder;
use api1exportbuilders\EventListJSONBuilder;
use api1exportbuilders\EventListJSONPBuilder;
use api1exportbuilders\EventListATOMBeforeBuilder;
use api1exportbuilders\EventListATOMCreateBuilder;

use repositories\builders\filterparams\EventFilterParams;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CountryController {
	
	
	protected $parameters = array();
	
	protected function build($slug, Request $request, Application $app) {
		$this->parameters = array();
		
		$gr = new CountryRepository();
		$this->parameters['country'] = $gr->loadByTwoCharCode($slug);
		if (!$this->parameters['country']) {
			return false;
		}
		
		// TODO could check this country is or was valid for this site?
		
		return true;
	}
	
	
	

	function eventsIcal($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Country does not exist.");
		}
		
		$ical = new EventListICalBuilder($app['currentSite'], $app['currentTimeZone'], $this->parameters['country']->getTitle());
		$ical->getEventRepositoryBuilder()->setCountry($this->parameters['country']);
		$ical->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$ical->build();
		return $ical->getResponse();
				
	}

	function eventsJson($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Country does not exist.");
		}

		
		$json = new EventListJSONBuilder($app['currentSite'], $app['currentTimeZone']);
		$json->getEventRepositoryBuilder()->setCountry($this->parameters['country']);
		$json->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$json->build();
		return $json->getResponse();
				
	}	

	function eventsJsonp($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Country does not exist.");
		}

		
		$jsonp = new EventListJSONPBuilder($app['currentSite'], $app['currentTimeZone']);
		$jsonp->getEventRepositoryBuilder()->setCountry($this->parameters['country']);
		$jsonp->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$jsonp->build();
		if (isset($_GET['callback'])) $jsonp->setCallBackFunction($_GET['callback']);
		return $jsonp->getResponse();
				
	}	

	function eventsAtomBefore($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Country does not exist.");
		}

		$days = isset($_GET['days']) ? $_GET['days'] : null;
		$atom = new EventListATOMBeforeBuilder($app['currentSite'], $app['currentTimeZone']);
		$atom->setDaysBefore($days);
		$atom->setTitle($this->parameters['country']->getTitle());
		$atom->getEventRepositoryBuilder()->setCountry($this->parameters['country']);
		$atom->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$atom->build();
		return $atom->getResponse();
	}	
	

	function eventsAtomCreate($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Country does not exist.");
		}

		
		$atom = new EventListATOMCreateBuilder($app['currentSite'], $app['currentTimeZone']);
		$atom->setTitle($this->parameters['country']->getTitle());
		$atom->getEventRepositoryBuilder()->setCountry($this->parameters['country']);
		$atom->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$atom->build();
		return $atom->getResponse();
	}	
	
	
	
}


