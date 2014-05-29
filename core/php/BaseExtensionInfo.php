<?php

use models\VenueModel;
use models\UserAccountModel;
use Silex\Application;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
abstract class BaseExtensionInfo {
	
	function __construct(Application $app) {
		
	}

	public function getTitle() {
		return null;
	}
	
	public function getDescription() {
		return null;
	}
	
	
}

