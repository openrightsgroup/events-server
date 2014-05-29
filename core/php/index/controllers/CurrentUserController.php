<?php

namespace index\controllers;


use Silex\Application;
use index\forms\SignUpUserForm;
use index\forms\LogInUserForm;
use index\forms\ForgotUserForm;
use index\forms\ResetUserForm;
use index\forms\UserEmailsForm;
use index\forms\UserPrefsForm;
use Symfony\Component\HttpFoundation\Request;
use models\UserAccountModel;
use repositories\UserAccountRepository;
use repositories\UserAccountRememberMeRepository;
use repositories\builders\SiteRepositoryBuilder;
use Symfony\Component\Form\FormError;
use repositories\UserAccountResetRepository;
use index\forms\UserChangePasswordForm;
use repositories\builders\filterparams\EventFilterParams;
use repositories\UserAccountVerifyEmailRepository;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CurrentUserController {
	
	
	function logout(Application $app) {
		userLogOut();
		
		return $app['twig']->render('index/user/logout.html.twig', array(
				'currentUser'=>null,
			));
	}
	
	function verifyNeeded(Application  $app) {
		return $app['twig']->render('index/currentuser/verifyAccountNeeded.html.twig', array());
	}
	
	
	function resendVerifyEmail(Request $request, Application $app) {
		global $FLASHMESSAGES, $CONFIG;
		
		$repo = new UserAccountVerifyEmailRepository();
		
		$date = $repo->getLastSentForUserAccount(userGetCurrent());
		if ($date && $date->getTimestamp() > (\TimeSource::time() - $CONFIG->userAccountVerificationSecondsBetweenAllowedSends)) {
			$FLASHMESSAGES->addMessage("Sorry, but the email was sent to recently. Please try again soon.");
		}  else {
			
			$verifyEmail = $repo->create(userGetCurrent());
			$verifyEmail->sendEmail($app, userGetCurrent());


			$FLASHMESSAGES->addMessage("Verification email resent.");
		
		}
		
		return $app->redirect("/me/");
		
	}
	
	function changePassword(Request $request, Application $app) {
		global $FLASHMESSAGES;
		
		$form = $app['form.factory']->create(new UserChangePasswordForm());
		
		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {
				$data = $form->getData();

				if (!userGetCurrent()->checkPassword($data['oldpassword'])) {
					$form->addError(new FormError('old password wrong'));
				} else {
					userGetCurrent()->setPassword($data['password1']);
					$userAccountRepository = new UserAccountRepository();
					$userAccountRepository->editPassword(userGetCurrent());
					$FLASHMESSAGES->addMessage("Password Changed.");
					return $app['twig']->render('index/currentuser/changePasswordDone.html.twig', array());
				}
								
			}
		}
		
		
		return $app['twig']->render('index/currentuser/changePassword.html.twig', array(
			'form'=>$form->createView(),
		));
	}
	
	function emails(Request $request, Application $app) {
		global $FLASHMESSAGES;
		
		$form = $app['form.factory']->create(new UserEmailsForm(), userGetCurrent());
		
		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {
				$userRepo = new UserAccountRepository;
				$userRepo->editEmailsOptions(userGetCurrent());
				$FLASHMESSAGES->addMessage("Options Changed.");
				return $app->redirect("/me/");
			}
		}
		
		return $app['twig']->render('index/currentuser/emails.html.twig', array(
			'form'=>$form->createView(),
		));
	}
	
	
	function prefs(Request $request, Application $app) {
		global $FLASHMESSAGES;
		
		$form = $app['form.factory']->create(new UserPrefsForm(), userGetCurrent());
		
		if ('POST' == $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {
				$userRepo = new UserAccountRepository;
				$userRepo->editPreferences(userGetCurrent());
				$FLASHMESSAGES->addMessage("Options Changed.");
				return $app->redirect("/me/");
			}
		}
		
		return $app['twig']->render('index/currentuser/prefs.html.twig', array(
			'form'=>$form->createView(),
		));
	}
	
	function index(Request $request, Application $app) {
		
		return $app['twig']->render('index/currentuser/index.html.twig', array(
		));
		
	}
	
	function sites(Request $request, Application $app) {
		
		$srb = new SiteRepositoryBuilder();
		$srb->setUserInterestedIn(userGetCurrent());
		
		return $app['twig']->render('index/currentuser/sites.html.twig', array(
			'sites'=>$srb->fetchAll(),
		));
		
	}
	
	function agenda(Request $request, Application $app) {
		
		$params = new EventFilterParams();
		$params->setSpecifiedUserControls(true, userGetCurrent(), true);
		$params->set($_GET);
		$events = $params->getEventRepositoryBuilder()->fetchAll();
		
		return $app['twig']->render('index/currentuser/agenda.html.twig', array(
				'eventListFilterParams'=>$params,
				'events'=>$events,
			));
		
	}
	
	
	
	function calendarNow(Application $app) {
		$cal = new \RenderCalendar();
		$params = new EventFilterParams($cal->getEventRepositoryBuilder());
		$params->setHasDateControls(false);
		$params->setSpecifiedUserControls(true, userGetCurrent(), true);
		$params->set($_GET);
		$cal->byDate(\TimeSource::getDateTime(), 31, true);
		
		list($prevYear,$prevMonth,$nextYear,$nextMonth) = $cal->getPrevNextLinksByMonth();

		return $app['twig']->render('/index/currentuser/calendar.html.twig', array(
				'calendar'=>$cal,
				'eventListFilterParams'=>$params,
				'prevYear' => $prevYear,
				'prevMonth' => $prevMonth,
				'nextYear' => $nextYear,
				'nextMonth' => $nextMonth,
				'showCurrentUserOptions' => true,
			));
	}
	
	function calendar($year, $month, Application $app) {
		
		$cal = new \RenderCalendar();
		$params = new EventFilterParams($cal->getEventRepositoryBuilder());
		$params->setHasDateControls(false);
		$params->setSpecifiedUserControls(true, userGetCurrent(), true);
		$params->set($_GET);
		$cal->byMonth($year, $month, true);
		
		list($prevYear,$prevMonth,$nextYear,$nextMonth) = $cal->getPrevNextLinksByMonth();

		return $app['twig']->render('/index/currentuser/calendar.html.twig', array(
				'calendar'=>$cal,
				'eventListFilterParams'=>$params,
				'prevYear' => $prevYear,
				'prevMonth' => $prevMonth,
				'nextYear' => $nextYear,
				'nextMonth' => $nextMonth,
				'showCurrentUserOptions' => true,
			));
	}
}

