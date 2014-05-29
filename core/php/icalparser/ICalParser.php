<?php

namespace icalparser;

/**
 * This is the iCal-class
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ICalParser
{
 
	/** @var ICalParserTimeZone **/
	protected $timezone;
	
	protected $events = array();
	
	public function __construct() {
		$this->timezone = new ICalParserTimeZone();
	}
	
    /**
     * @param {string} $filename The path to the iCal-file
     * @return Object The iCal-Object
     */
    public function parseFromFile($filename)
    {
		
		if (!$filename) {
			return false;
		}
        
		$lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (count($lines) < 1) {
			return false;
		}
		
        if (stristr($lines[0], 'BEGIN:VCALENDAR') === false) {
            return false;
        } else {
    
			# pass1: put multi lines back into one line.
			$linesCompacted = array();
			foreach($lines as $line) {
				if (substr($line,0,1) == ' ') {
					$linesCompacted[count($linesCompacted)-1] .= trim($line);
				} else {
					$linesCompacted[] = trim($line);
				}
			};
						
			#pass2: turn lines into formatted data
			$rawLines = array();
			foreach($linesCompacted as $line) {
				$bits = explode(":", $line, 2);
				$kbits = explode(";", $bits[0], 2);
				$value = count($bits) == 2 ? $bits[1] : '';
				// http://www.ietf.org/rfc/rfc2445.txt section 4.3.11
				$value = str_replace('\\\\', '\\', $value);
				$value = str_replace('\\N', "\n", $value);
				$value = str_replace('\\n', "\n", $value);
				$value = str_replace('\\;', ';', $value);
				$value = str_replace('\,', ',', $value);
				$rawLines[] = array(
						'KEYWORD'=>$kbits[0],
						'KEYWORDOPTIONS'=>count($kbits) == 2 ? $kbits[1] : '',
						'VALUE'=>$value,
					);
			}
			
			# pass3: finally parse lines
			$stack = array();
			foreach($rawLines as $line) {
				if ($line['KEYWORD'] == 'BEGIN') {
					$stack[] = $line['VALUE'];
					if ($line['VALUE'] == 'VEVENT') {
						$this->events[] = new ICalParserEvent($this->timezone->getTimeZone());
					}
				} else if ($line['KEYWORD'] == 'END') {
					// TODO check VALUE and last stack match
					array_pop($stack);
				} else {
					$currentlyIn = $stack[count($stack)-1];
					//print $currentlyIn." with K ".$line['KEYWORD']."\n";
					if ($currentlyIn == 'VEVENT') {
						$this->events[count($this->events)-1]->processLine($line['KEYWORD'],$line['VALUE']);
					} elseif ($currentlyIn == 'VTIMEZONE') {
						$this->timezone->processLine($line['KEYWORD'],$line['VALUE']);
					}
				}
			}
			
			//die();
			
			return true;
		}
    }

	/**
	 * This is only for debugging.
	 * This class ensures all dates returned are in UTC, whatever the input time zone was.
	 * @return type 
	 */
	public function getTimeZoneIdentifier() 
	{
		return $this->timezone->getTimeZoneIdentifier();
	}

    public function getEvents()
    {
		return $this->events;
	}

}


