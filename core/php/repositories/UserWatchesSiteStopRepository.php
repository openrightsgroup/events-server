<?php

namespace repositories;

use models\UserAccountModel;
use models\UserWatchesSiteStopModel;
use models\SiteModel;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class UserWatchesSiteStopRepository {
	
	/**
	 * This will always return something. If one doesn't exist, one will be created.
	 * @return UserWatchesSiteStopModel
	 */
	public function getForUserAndSite(UserAccountModel $user, SiteModel $site) {
		global $DB;
		
		$stat = $DB->prepare("SELECT * FROM user_watches_site_stop WHERE user_account_id=:uid AND site_id=:sid");
		$stat->execute(array('uid'=>$user->getId(),'sid'=>$site->getId()));
		if ($stat->rowCount() > 0) {
			$uwss = new UserWatchesSiteStopModel();
			$uwss->setFromDataBaseRow($stat->fetch());
			return $uwss;
		}
		
		$uwss = new UserWatchesSiteStopModel();
		$uwss->setUserAccountId($user->getId());
		$uwss->setSiteId($site->getId());
		$uwss->setAccessKey(createKey(2,150));
		
		// TODO check not already used
		
		$stat = $DB->prepare("INSERT INTO user_watches_site_stop (user_account_id, site_id, access_key, created_at) ".
				"VALUES (:user_account_id, :site_id, :access_key, :created_at)");
		$stat->execute(array(
				'user_account_id'=>$uwss->getUserAccountId(),
				'site_id'=>$uwss->getSiteId(),
				'access_key'=>$uwss->getAccessKey(),
				'created_at'=>\TimeSource::getFormattedForDataBase()
			));
		
		return $uwss;
		
	}
	
	/** @return UserWatchesSiteStopModel **/
	public function loadByUserAccountIDAndSiteIDAndAccessKey($userId, $siteId, $access) {
		global $DB;
		$stat = $DB->prepare("SELECT * FROM user_watches_site_stop WHERE user_account_id=:uid AND site_id=:sid");
		$stat->execute(array('uid'=>$userId,'sid'=>$siteId));
		if ($stat->rowCount() > 0) {
			$uwss = new UserWatchesSiteStopModel();
			$uwss->setFromDataBaseRow($stat->fetch());
			return $uwss;
		}
	}
	
}