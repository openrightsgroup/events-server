<?php



/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ConfigCheck {
	
	/** @var Config **/
	protected $config;
	
	function __construct(Config $config) {
		$this->config = $config;
	}
	
	public function getErrors($field) {
		$out = array();
		
		if (in_array($field, array('webIndexDomain','webSiteDomain')) && $this->config->isSingleSiteMode && $this->config->webIndexDomain != $this->config->webSiteDomain) {
			$out[] = 'In single site mode, webIndexDomain and webSiteDomain should be the same!';
		}
		
		if ($field == 'emailFrom' && !filter_var($this->config->emailFrom, FILTER_VALIDATE_EMAIL)) {
			$out[] = 'This must be a valid email address!';
		}
		
		if ($field == 'contactEmail' && !filter_var($this->config->contactEmail, FILTER_VALIDATE_EMAIL)) {
			$out[] = 'This must be a valid email address!';
		}
		
		if ($field == 'logToStdError' && $this->config->logToStdError && !$this->config->logFile) {
			$out[] = 'For logToStdError to work logFile must be set';
		}
		
		return $out;
	}

	
	
	
}

