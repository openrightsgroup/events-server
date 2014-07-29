<?php



namespace repositories;

use models\MediaModel;
use models\UserAccountModel;
use models\EventModel;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class MediaInEventRepository{
	
	
	public function add(MediaModel $media, EventModel $event, UserAccountModel $user) {
		global $DB;
		
		// check event not already in list
		$stat = $DB->prepare("SELECT * FROM media_in_event WHERE event_id=:event_id AND ".
				" media_id=:media_id AND removed_at IS NULL ");
		$stat->execute(array(
			'event_id'=>$event->getId(),
			'media_id'=>$media->getId(),
		));
		if ($stat->rowCount() > 0) {
			return;
		}
		
		// Add!
		$stat = $DB->prepare("INSERT INTO media_in_event (event_id,media_id,added_by_user_account_id,added_at,addition_approved_at) ".
				"VALUES (:event_id,:media_id,:added_by_user_account_id,:added_at,:addition_approved_at)");
		$stat->execute(array(
			'event_id'=>$event->getId(),
			'media_id'=>$media->getId(),
			'added_by_user_account_id'=>$user->getId(),
			'added_at'=>  \TimeSource::getFormattedForDataBase(),
			'addition_approved_at'=>  \TimeSource::getFormattedForDataBase(),
		));
		
	}


	public function remove(MediaModel $media, EventModel $event, UserAccountModel $user) {
		global $DB;
		$stat = $DB->prepare("UPDATE media_in_event SET removed_by_user_account_id=:removed_by_user_account_id,".
				" removed_at=:removed_at, removal_approved_at=:removal_approved_at WHERE ".
				" event_id=:event_id AND media_id=:media_id AND removed_at IS NULL ");
		$stat->execute(array(
				'event_id'=>$event->getId(),
				'media_id'=>$media->getId(),
				'removed_at'=>  \TimeSource::getFormattedForDataBase(),
				'removal_approved_at'=>  \TimeSource::getFormattedForDataBase(),
				'removed_by_user_account_id'=>$user->getId(),
			));
	}
}

