<?php

namespace site\controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use models\CuratedListModel;
use repositories\CuratedListRepository;
use repositories\builders\filterparams\EventFilterParams;
use site\forms\CuratedListEditForm;
use repositories\UserAccountRepository;
use repositories\EventRepository;
use repositories\builders\UserAccountRepositoryBuilder;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CuratedListEventController {

	
	protected $parameters = array();
	
	protected function build($slug, $eslug, Request $request, Application $app) {
		$this->parameters = array();

		$curatedlistRepository = new CuratedListRepository();
		$this->parameters['curatedlist'] =  $curatedlistRepository->loadBySlug($app['currentSite'], $slug);
		if (!$this->parameters['curatedlist']) {
			return false;
		}
		
		$eventRepository = new EventRepository();
		$this->parameters['event'] =  $eventRepository->loadBySlug($app['currentSite'], $eslug);
		if (!$this->parameters['event']) {
			return false;
		}
		
		$this->parameters['currentUserCanEditCuratedList'] = $this->parameters['curatedlist']->canUserEdit(userGetCurrent());
		
		return true;

	}
	
	function remove($slug,$eslug,Request $request, Application $app) {
		global $WEBSESSION;


		if (!$this->build($slug,$eslug, $request, $app)) {
			$app->abort(404, "curatedlist does not exist.");
		}
		
		if ($this->parameters['currentUserCanEditCuratedList'] && isset($_POST['CSFRToken']) && $_POST['CSFRToken'] == $WEBSESSION->getCSFRToken()) {
			$curatedlistRepository = new CuratedListRepository();
			$curatedlistRepository->removeEventFromCuratedList($this->parameters['event'], $this->parameters['curatedlist'], userGetCurrent());
		}
		
		return $app->redirect("/curatedlist/".$this->parameters['curatedlist']->getId());
	}
	
	function add($slug,$eslug,Request $request, Application $app) {
		global $WEBSESSION;
		
		if (!$this->build($slug,$eslug, $request, $app)) {
			$app->abort(404, "curatedlist does not exist.");
		}
		
		if ($this->parameters['currentUserCanEditCuratedList'] && isset($_POST['CSFRToken']) && $_POST['CSFRToken'] == $WEBSESSION->getCSFRToken()) {
			$curatedlistRepository = new CuratedListRepository();
			$curatedlistRepository->addEventtoCuratedList($this->parameters['event'], $this->parameters['curatedlist'], userGetCurrent());			
		}
		
		return $app->redirect("/curatedlist/".$this->parameters['curatedlist']->getId());
		
	}
	
	
}

