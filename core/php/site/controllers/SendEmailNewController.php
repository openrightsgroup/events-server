<?php

namespace site\controllers;

use Silex\Application;
use site\forms\SendEmailNewForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use models\SendEmailModel;
use repositories\SendEmailRepository;
use repositories\UserAccountRepository;
use repositories\builders\EventRepositoryBuilder;
use repositories\builders\VenueRepositoryBuilder;
use repositories\UserWatchesSiteRepository;
use repositories\UserWatchesSiteStopRepository;
use repositories\builders\SendEmailRepositoryBuilder;
use Symfony\Component\Form\FormError;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class SendEmailNewController {
	
	function index(Request $request, Application $app) {
		
		$sendemail = new SendEmailModel();
		$sendemail->setSiteId($app['currentSite']->getId());
		$sendemail->setTimezone($app['currentTimeZone']);
		
		$form = $app['form.factory']->create(new SendEmailNewForm(), $sendemail);
		
		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			$sendemail->setSendTo($_POST['send_to'] == 'other' ? $_POST['send_to_other'] : $_POST['send_to']);
			
			if (!filter_var($sendemail->getSendTo(), FILTER_VALIDATE_EMAIL)) {
				$form->addError(new FormError('Please enter an email address'));
			}
			
			if ($form->isValid()) {
				
				$sendemail->buildEvents($app);
		
				$repository = new SendEmailRepository();
				$repository->create($sendemail, $app['currentSite'], userGetCurrent());
				
				return $app->redirect("/admin/sendemail/".$sendemail->getSlug());
				
			}
		}
		
		$emails = array(  userGetCurrent()->getEmail()  );
		
		$rb = new SendEmailRepositoryBuilder();
		$rb->setSite($app['currentSite']);
		$rb->setUserCreatedBy(userGetCurrent());
		foreach($rb->fetchAll() as $sendemail) {
			if (!in_array($sendemail->getSendTo(), $emails)) {
				$emails[] = $sendemail->getSendTo();
			}
		}
		
		return $app['twig']->render('site/sendemailnew/index.html.twig', array(
				'form'=>$form->createView(),
				'emails'=>$emails,
			));
			
		
	}
	
}

