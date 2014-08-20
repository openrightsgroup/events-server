<?php

namespace site\controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use models\CuratedListModel;
use repositories\CuratedListRepository;
use repositories\builders\filterparams\GroupFilterParams;
use site\forms\CuratedListEditForm;
use repositories\UserAccountRepository;
use repositories\GroupRepository;
use repositories\builders\UserAccountRepositoryBuilder;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CuratedListGroupController {

	
	protected $parameters = array();
	
	protected function build($slug, $gslug, Request $request, Application $app) {
		$this->parameters = array();

		$curatedlistRepository = new CuratedListRepository();
		$this->parameters['curatedlist'] =  $curatedlistRepository->loadBySlug($app['currentSite'], $slug);
		if (!$this->parameters['curatedlist']) {
			return false;
		}
		
		$groupRepository = new GroupRepository();
		$this->parameters['group'] =  $groupRepository->loadBySlug($app['currentSite'], $gslug);
		if (!$this->parameters['group']) {
			return false;
		}
		
		$this->parameters['currentUserCanEditCuratedList'] = $this->parameters['curatedlist']->canUserEdit(userGetCurrent());
		
		return true;

	}
	
	function remove($slug,$gslug,Request $request, Application $app) {

		if (!$this->build($slug,$gslug, $request, $app)) {
			$app->abort(404, "curatedlist does not exist.");
		}
		
		if ($this->parameters['currentUserCanEditCuratedList'] && $request->request->get('CSFRToken') == $app['websession']->getCSFRToken()) {
			$curatedlistRepository = new CuratedListRepository();
			$curatedlistRepository->removeGroupFromCuratedList($this->parameters['group'], $this->parameters['curatedlist'], userGetCurrent());
		}
		
		if ($request->request->get('returnTo','group') == 'group') {
			return $app->redirect("/group/".$this->parameters['group']->getSlugForURL());
		} elseif ($request->request->get('returnTo','group') == 'curatedlist') {
			return $app->redirect("/curatedlist/".$this->parameters['curatedlist']->getSlug());
		}
		
	}
	
	function add($slug,$gslug,Request $request, Application $app) {		
		if (!$this->build($slug,$gslug, $request, $app)) {
			$app->abort(404, "curatedlist does not exist.");
		}
		
		if ($this->parameters['currentUserCanEditCuratedList'] && $request->request->get('CSFRToken') == $app['websession']->getCSFRToken()) {
			$curatedlistRepository = new CuratedListRepository();
			$curatedlistRepository->addGrouptoCuratedList($this->parameters['group'], $this->parameters['curatedlist'], userGetCurrent());			
		}
		
		if ($request->request->get('returnTo','group') == 'group') {
			return $app->redirect("/group/".$this->parameters['group']->getSlugForURL());
		} elseif ($request->request->get('returnTo','group') == 'curatedlist') {
			return $app->redirect("/curatedlist/".$this->parameters['curatedlist']->getSlug());
		}
		
	}
	
	
}

