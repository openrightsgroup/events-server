<?php

namespace site\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use repositories\builders\CountryRepositoryBuilder;
use models\SiteModel;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ImportURLNewForm extends AbstractType{
	
	protected $timeZoneName;
	/** @var SiteModel **/
	protected $site;
	
	function __construct(SiteModel $site, $timeZoneName) {
		$this->site = $site;
		$this->timeZoneName = $timeZoneName;
	}
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		
		$builder->add('title', 'text', array(
			'label'=>'Title',
			'required'=>false, 
			'max_length'=>VARCHAR_COLUMN_LENGTH_USED
		));
		
		$builder->add('url', 'url', array(
			'label'=>'URL',
			'required'=>true, 
			'max_length'=>VARCHAR_COLUMN_LENGTH_USED
		));
			
		$crb = new CountryRepositoryBuilder();
		$crb->setSiteIn($this->site);
		$countries = array();
		$defaultCountry = null;
		foreach($crb->fetchAll() as $country) {
			$countries[$country->getId()] = $country->getTitle();
			if ($defaultCountry == null && in_array($this->timeZoneName, $country->getTimezonesAsList())) {
				$defaultCountry = $country->getId();
			}	
		}
		// TODO if current country not in list add it now
		$builder->add('country_id', 'choice', array(
			'label'=>'Country',
			'choices' => $countries,
			'required' => true,
			'data' => $defaultCountry,
		));
		
	}
	
	public function getName() {
		return 'ImportURLNewForm';
	}
	
	public function getDefaultOptions(array $options) {
		return array(
		);
	}
	
}