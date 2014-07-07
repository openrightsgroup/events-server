<?php

use models\UserAccountModel;
use repositories\UserAccountRepository;
use repositories\UserNotificationPreferenceRepository;

/**
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class UserNotificationPreferenceRepositoryTest extends \PHPUnit_Framework_TestCase {
	
	function testGetDefault() {
			
		$DB = getNewTestDB();

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		
		
		$prefRepo = new UserNotificationPreferenceRepository();
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(true, $pref->getIsEmail());
		
		
	}
	
	
	function testSetOldWayFalseThenGet() {
			
		$DB = getNewTestDB();

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		
		
		$prefRepo = new UserNotificationPreferenceRepository();
		
		### Set
		$stat = $DB->prepare("ALTER TABLE user_account_information ADD is_email_watch_notify boolean default '0' NOT NULL");
		$stat->execute();
		$stat = $DB->prepare("ALTER TABLE user_account_information ADD is_email_watch_prompt boolean default '0' NOT NULL");
		$stat->execute();
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(false, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(false, $pref->getIsEmail());
		
	}
	
	function testSetOldWayTrueThenGet() {
			
		$DB = getNewTestDB();

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		
		
		$prefRepo = new UserNotificationPreferenceRepository();
		
		### Set
		$stat = $DB->prepare("ALTER TABLE user_account_information ADD is_email_watch_notify boolean default '1' NOT NULL");
		$stat->execute();
		$stat = $DB->prepare("ALTER TABLE user_account_information ADD is_email_watch_prompt boolean default '1' NOT NULL");
		$stat->execute();
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(true, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(true, $pref->getIsEmail());
		
	}
	
	function testSetThenGet() {
			
		$DB = getNewTestDB();

		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		
		
		$prefRepo = new UserNotificationPreferenceRepository();
		
		### Set
		$prefRepo->editEmailPreference($user, 'org.openacalendar', 'WatchPrompt', true);
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(true, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(true, $pref->getIsEmail());
		
		### Set
		$prefRepo->editEmailPreference($user, 'org.openacalendar', 'WatchPrompt', false);
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(false, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(true, $pref->getIsEmail());
		
		
		### Set
		$prefRepo->editEmailPreference($user, 'org.openacalendar', 'WatchNotify', false);
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(false, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(false, $pref->getIsEmail());

		
		
		### Set
		$prefRepo->editEmailPreference($user, 'org.openacalendar', 'WatchPrompt', true);
		
		### Test
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchPrompt');
		$this->assertEquals(true, $pref->getIsEmail());
		$pref = $prefRepo->load($user, 'org.openacalendar', 'WatchNotify');
		$this->assertEquals(false, $pref->getIsEmail());
		
		
	}
	
	
	
	
}

