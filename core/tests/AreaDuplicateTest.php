<?php

use models\UserAccountModel;
use models\SiteModel;
use models\AreaModel;
use models\CountryModelModel;
use repositories\UserAccountRepository;
use repositories\SiteRepository;
use repositories\CountryRepository;
use repositories\AreaRepository;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class AreaDuplicateTest extends \PHPUnit_Framework_TestCase {
	
	function test1() {
		\TimeSource::mock(2014,1,1,0,0,0);
		$DB = getNewTestDB();
		addCountriesToTestDB();
		$countryRepo = new CountryRepository();
		$areaRepo = new AreaRepository();
		
		$user = new UserAccountModel();
		$user->setEmail("test@jarofgreen.co.uk");
		$user->setUsername("test");
		$user->setPassword("password");
		
		$userRepo = new UserAccountRepository();
		$userRepo->create($user);
		
		$site = new SiteModel();
		$site->setTitle("Test");
		$site->setSlug("test");
		
		$siteRepo = new SiteRepository();
		$siteRepo->create($site, $user, array( $countryRepo->loadByTwoCharCode('GB') ), getSiteQuotaUsedForTesting());

		$area1 = new AreaModel();
		$area1->setTitle("test");
		$area1->setDescription("test test");

		$area2 = new AreaModel();
		$area2->setTitle("test this looks similar");
		$area2->setDescription("test test");

		$areaRepo->create($area1, null, $site, $countryRepo->loadByTwoCharCode('GB') , $user);
		$areaRepo->create($area2, null, $site, $countryRepo->loadByTwoCharCode('GB') , $user);

		$area1 = $areaRepo->loadById($area1->getId());
		$area2 = $areaRepo->loadById($area2->getId());


		// Test before



		// Mark
		\TimeSource::mock(2014,1,1,2,0,0);
		$areaRepo->markDuplicate($area2, $area1, $user);


		// Test Duplicate


	}
}




