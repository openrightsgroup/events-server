<?php


namespace models;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class TagHistoryModel extends TagModel {

	protected $tag_slug;
	//protected $user_account_id;
	protected $created_at; 
	protected $user_account_id;
	protected $user_account_username;
	
	protected $title_changed = 0;
	protected $description_changed = 0;
	protected $is_deleted_changed = 0;

	protected $is_new = 0;



	public function setFromDataBaseRow($data) {
		$this->id = $data['tag_id'];
		$this->tag_slug = isset($data['tag_slug']) ? $data['tag_slug'] : null;
		$this->title = $data['title'];
		$this->description = $data['description'];
		$this->is_deleted = $data['is_deleted'];
		$utc = new \DateTimeZone("UTC");
		$this->created_at = new \DateTime($data['created_at'], $utc);
		$this->user_account_id = $data['user_account_id'];
		$this->user_account_username = isset($data['user_account_username']) ? $data['user_account_username'] : null;
		$this->title_changed = $data['title_changed'];
		$this->description_changed = $data['description_changed'];
		$this->is_deleted_changed = $data['is_deleted_changed'];
		$this->is_new = isset($data['is_new']) ? $data['is_new'] : 0;
	}
	
	public function getCreatedAt() {
		return $this->created_at;
	}


	public function getCreatedAtTimeStamp() {
		return $this->created_at->getTimestamp();
	}
	
	public function getTagSlug() {
		return $this->tag_slug;
	}

	public function setTagSlug($tag_slug) {
		$this->tag_slug = $tag_slug;
	}

	public function getUserAccountId() {
		return $this->user_account_id;
	}

	public function setUserAccountId($user_account_id) {
		$this->user_account_id = $user_account_id;
	}

	public function getUserAccountUsername() {
		return $this->user_account_username;
	}

	public function setUserAccountUsername($user_account_username) {
		$this->user_account_username = $user_account_username;
	}

	public function isAnyChangeFlagsUnknown() {
		return $this->title_changed == 0 || $this->description_changed == 0 || $this->is_deleted_changed == 0;
	}
	
	public function setChangedFlagsFromNothing() {
		$this->title_changed = $this->title ? 1 : -1;
		$this->description_changed = $this->description ? 1 : -1;
		$this->is_deleted_changed = $this->is_deleted ? 1 : -1;
		$this->is_new = 1;
	}
	
	public function setChangedFlagsFromLast(TagHistoryModel $last) {
		$this->title_changed  = ($this->title  != $last->title  )? 1 : -1;
		$this->description_changed  = ($this->description  != $last->description  )? 1 : -1;
		$this->is_deleted_changed  = ($this->is_deleted  != $last->is_deleted  )? 1 : -1;
		$this->is_new = 0;
	}
	
	public function getTitleChanged() {
		return ($this->title_changed != -1);
	}

	public function getDescriptionChanged() {
		return ($this->description_changed != -1);
	}

	public function getIsDeletedChanged() {
		return ($this->is_deleted_changed != -1);
	}

	public function getIsNew() {
		return ($this->is_new == 1);
	}

}

