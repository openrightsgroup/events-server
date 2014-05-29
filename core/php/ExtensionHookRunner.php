<?php


use models\VenueModel;
use models\UserAccountModel;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ExtensionHookRunner {
	
	
	public function beforeVenueSave(VenueModel $venue, UserAccountModel $user) {
		global $app, $CONFIG;
		foreach($CONFIG->extensions as $extensionName) {
			$app['extension'.strtolower($extensionName)]->beforeVenueSave($venue, $user);
		}
	}
	
	
}


