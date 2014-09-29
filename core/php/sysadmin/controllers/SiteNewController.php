<?php

namespace sysadmin\controllers;

use Silex\Application;
use site\forms\NewEventForm;
use Symfony\Component\HttpFoundation\Request;
use models\SiteModel;
use models\EventModel;
use repositories\SiteRepository;
use repositories\UserAccountRepository;
use repositories\CountryRepository;
use repositories\SiteQuotaRepository;
use repositories\builders\SiteRepositoryBuilder;
use sysadmin\forms\NewSiteForm;
use Symfony\Component\Form\FormError;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class SiteNewController {
	
	
	function index(Request $request, Application $app) {

		$form = $app['form.factory']->create(new NewSiteForm());

		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			$data = $form->getData();
			$siteRepository = new SiteRepository();

			$site = $siteRepository->loadBySlug($data['slug']);
			if ($site) {
				$form->addError(new FormError('That address is already taken'));
			}

			if ($form->isValid()) {
				
				$userRepo = new UserAccountRepository();
				$user = $userRepo->loadByEmail($data['email']);
				if ($user) {

					$data = $form->getData();
					$site = new SiteModel();
					$site->setSlug($data['slug']);
					$site->setTitle($data['title']);
					if ($data['read'] == 'public') {
						$site->setIsListedInIndex(true);
						$site->setIsWebRobotsAllowed(true);
					} else {
						$site->setIsListedInIndex(false);
						$site->setIsWebRobotsAllowed(false);
					}
					if ($data['write'] == 'public') {
						$site->setIsAllUsersEditors(true);
						$site->setIsRequestAccessAllowed(false);
					} else {
						$site->setIsAllUsersEditors(false);
						$site->setIsRequestAccessAllowed(true);
					}
					$site->setIsFeatureCuratedList($app['config']->newSiteHasFeatureCuratedList);
					$site->setIsFeatureImporter($app['config']->newSiteHasFeatureImporter);
					$site->setIsFeatureMap($app['config']->newSiteHasFeatureMap);
					$site->setIsFeatureVirtualEvents($app['config']->newSiteHasFeatureVirtualEvents);
					$site->setIsFeaturePhysicalEvents($app['config']->newSiteHasFeaturePhysicalEvents);
					$site->setIsFeatureGroup($app['config']->newSiteHasFeatureGroup);
					$site->setPromptEmailsDaysInAdvance($app['config']->newSitePromptEmailsDaysInAdvance);
					$site->setIsFeatureTag($app['config']->newSiteHasFeatureTag);

					$countryRepository = new CountryRepository();
					$siteQuotaRepository = new SiteQuotaRepository();

					$siteRepository->create(
						$site,
						$user,
						array( $countryRepository->loadByTwoCharCode("GB") ),
						$siteQuotaRepository->loadByCode($app['config']->newSiteHasQuotaCode)
					);
					return $app->redirect("/sysadmin/site/".$site->getId());
				} else {
					$app['flashmessages']->addError('Existing user not found!');
				}

			}
		}



		return $app['twig']->render('sysadmin/sitenew/index.html.twig', array(
				'form'=>$form->createView(),
			));
		
	}
	
	
}


