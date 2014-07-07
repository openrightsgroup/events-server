<?php

namespace siteapi2\controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use repositories\GroupRepository;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class GroupController {
	
	/** @var \models\GroupModel **/
	protected $group;

	protected function build($slug, Request $request, Application $app) {

		
		
		$repo = new GroupRepository();
		$this->group = $repo->loadBySlug($app['currentSite'], $slug);
		if (!$this->group) {
			return false;
		}
		
		return true;
		
		
	}
	


	public function infoJson ($slug, Request $request, Application $app) {
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Does not exist.");
		}
		
		$out = array(
			'group'=>array(
				'slug'=>$this->group->getSlug(),
				'slugForURL'=>$this->group->getSlugForUrl(),
				'title'=>$this->group->getTitle(),
			),			
		);
		
		return json_encode($out);
	}
	
	
}

