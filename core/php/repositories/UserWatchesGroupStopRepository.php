<?php

namespace repositories;

use models\UserAccountModel;
use models\UserWatchesGroupStopModel;
use models\SiteModel;
use models\GroupModel;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk> 
 */
class UserWatchesGroupStopRepository {
	
	/**
	 * This will always return something. If one doesn't exist, one will be created.
	 * @return UserWatchesSiteStopModel
	 */
	public function getForUserAndGroup(UserAccountModel $user, GroupModel $group) {
		global $DB;
		
		$stat = $DB->prepare("SELECT * FROM user_watches_group_stop WHERE user_account_id=:uid AND group_id=:gid");
		$stat->execute(array('uid'=>$user->getId(),'gid'=>$group->getId()));
		if ($stat->rowCount() > 0) {
			$uwgs = new UserWatchesGroupStopModel();
			$uwgs->setFromDataBaseRow($stat->fetch());
			return $uwgs;
		}
		
		$uwgs = new UserWatchesGroupStopModel();
		$uwgs->setUserAccountId($user->getId());
		$uwgs->setGroupId($group->getId());
		$uwgs->setAccessKey(createKey(2,150));
		
		// TODO check not already used
		
		$stat = $DB->prepare("INSERT INTO user_watches_group_stop (user_account_id, group_id, access_key, created_at) ".
				"VALUES (:user_account_id, :group_id, :access_key, :created_at)");
		$stat->execute(array(
				'user_account_id'=>$uwgs->getUserAccountId(),
				'group_id'=>$uwgs->getGroupId(),
				'access_key'=>$uwgs->getAccessKey(),
				'created_at'=>\TimeSource::getFormattedForDataBase()
			));
		
		return $uwgs;
		
	}
	
	/** @return UserWatchesSiteStopModel **/
	public function loadByUserAccountIDAndGroupIDAndAccessKey($userId, $groupId, $access) {
		global $DB;
		$stat = $DB->prepare("SELECT * FROM user_watches_group_stop WHERE user_account_id=:uid AND group_id=:gid");
		$stat->execute(array('uid'=>$userId,'gid'=>$groupId));
		if ($stat->rowCount() > 0) {
			$uwss = new UserWatchesGroupStopModel();
			$uwss->setFromDataBaseRow($stat->fetch());
			return $uwss;
		}
	}
	
}