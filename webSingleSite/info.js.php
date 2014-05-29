<?php
require 'localConfig.php';
require_once (defined('COMPOSER_ROOT_DIR') ? COMPOSER_ROOT_DIR : APP_ROOT_DIR).'/vendor/autoload.php';
require_once APP_ROOT_DIR.'/core/php/autoload.php';

use repositories\SiteRepository;

/**
 *
 * 
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

//$siteRepository = new SiteRepository();
//$site = $siteRepository->loadById($CONFIG->singleSiteID);
	
$data  = array();
$data['httpDomain'] = $CONFIG->webSiteDomain;
$data['httpDomainSite'] = $CONFIG->webSiteDomain;
$data['twitter'] = $CONFIG->contactTwitter;
$data['isSingleSiteMode'] = true;
if ($CONFIG->hasSSL) {
	$data['hasSSL'] = true;
	$data['httpsDomain'] = $CONFIG->webIndexDomainSSL;
	$data['httpsDomainSite'] = $CONFIG->webSiteDomainSSL;
} else {
	$data['hasSSL'] = false;
}

print "var config = ".json_encode($data);

