<?php

namespace import;
use models\ImportURLModel;
use models\SiteModel;
use models\GroupModel;
use models\CountryModel;
use repositories\SiteRepository;
use repositories\GroupRepository;
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
class ImportURLRun {

	/** @var ImportURLModel **/
	protected $importURL;
	
	/** @var SiteModel **/
	protected $site;

	/** @var GroupModel **/
	protected $group;
	
	/** @var CountryModel **/
	protected $country;
	
	/** @var AreaModel **/
	protected $area;
	
	protected $realurl;
		
	public static $FLAG_ADD_UIDS = 1;
	public static $FLAG_SET_TICKET_URL_AS_URL = 2;

	protected $flags = array();

	protected $temporaryFileStorage;
	protected $temporaryFileStorageFromTesting;
	
	function __construct(ImportURLModel $importURL, SiteModel $site = null) {
		$this->importURL = $importURL;
		$this->realurl = $importURL->getUrl();
		if ($site) {
			$this->site = $site;
		} else {
			$siteRepo = new SiteRepository();
			$this->site = $siteRepo->loadById($importURL->getSiteId());
		}
		if ($importURL->getCountryId()) {
			$countryRepo = new CountryRepository();
			$this->country = $countryRepo->loadById($importURL->getCountryId());
		}
		if ($importURL->getAreaId()) {
			$areaRepo = new AreaRepository();
			$this->area = $areaRepo->loadById($importURL->getAreaId());
		}
		$groupRepository = new GroupRepository();
		$this->group = $groupRepository->loadById($importURL->getGroupId());
	}

	public function getImportURL() {
		return $this->importURL;
	}
	
	public function getSite() {
		return $this->site;
	}

	public function getGroup() {
		return $this->group;
	}

	public function getCountry() {
		return $this->country;
	}	

	public function getArea() {
		return $this->area;
	}	
	
	public function downloadURLreturnFileName() {
		global $CONFIG;
		if ($this->temporaryFileStorageFromTesting) return $this->temporaryFileStorageFromTesting;
		if ($this->temporaryFileStorage) return $this->temporaryFileStorage;
		
		$runAgain = false;
		$downloadCount = 0;
		$url =  $this->getRealUrl();
		$tempName = tempnam("/tmp", "hacimport");
		do {
			$runAgain = false;
			$downloadCount++;
			$fp = fopen($tempName, "w");		
			$ch = curl_init();      
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_USERAGENT, 'OpenACalendar from ican.openacalendar.org, install '.$CONFIG->webIndexDomain);
			curl_exec($ch);
			$response = curl_getinfo( $ch );
			curl_close($ch);
			fclose($fp);
			if ($response['http_code'] == 301 || $response['http_code'] == 302) {
				$url = $response['redirect_url'];
				$runAgain = true;
			}
		} while ($runAgain && $downloadCount < 5);
		
		$this->temporaryFileStorage = $tempName;
		return $tempName;
	}
	
	public function deleteLocallyStoredURL() {
		if ($this->temporaryFileStorage) {
			unlink($this->temporaryFileStorage);
			$this->temporaryFileStorage = null;
		}
	}
	
	public function setTemporaryFileStorageForTesting($temporaryFileStorageFromTesting) {
		$this->temporaryFileStorageFromTesting = $temporaryFileStorageFromTesting;
		return $this;
	}

	public function setFlag($flag) {  $this->flags[$flag] = true; }
	public function hasFlag($flag) { return isset($this->flags[$flag]) && $this->flags[$flag]; }


    public function setRealUrl($realurl)
    {
        $this->realurl = $realurl;
		$this->deleteLocallyStoredURL();
    }

    public function getRealUrl()
    {
        return $this->realurl;
    }	
	
	function __destruct() {
		$this->deleteLocallyStoredURL();
	}
	
}

