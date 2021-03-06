<?php


$CONFIG->isDebug = false;

$CONFIG->databaseType= 'pgsql';
$CONFIG->databaseName = 'orgevents';
$CONFIG->databaseHost = 'localhost';
$CONFIG->databaseUser = 'orgevents';



$CONFIG->assetsVersion = 2;

$CONFIG->isSingleSiteMode = true;
$CONFIG->singleSiteID = 1;

$CONFIG->webIndexDomain = "campaignevents.co.uk";
$CONFIG->webSiteDomain = "campaignevents.co.uk";
$CONFIG->webSysAdminDomain = "campaignevents.co.uk";

$CONFIG->hasSSL = true;
$CONFIG->webIndexDomainSSL = "campaignevents.co.uk";
$CONFIG->webSiteDomainSSL = "campaignevents.co.uk";
$CONFIG->webSysAdminDomainSSL = "campaignevents.co.uk";

$CONFIG->webCommonSessionDomain = "campaignevents.co.uk";

$CONFIG->bcryptRounds = 5;


$CONFIG->siteTitle = "Campaign Events";

$CONFIG->emailFrom = "noreply@campaignevents.co.uk";
$CONFIG->emailFromName = "Campaign Events";

$CONFIG->copyrightNoticeText = "Copyright Open Rights Group and JMB Technology ltd 2013-2014";
$CONFIG->copyrightNoticeHTML = '&copy; <a href="https://www.openrightsgroup.org/">Open Rights Group</a> and <a href="http://jmbtechnology.co.uk/" target="_blank">JMB Technology ltd</a> 2013-2014';

$CONFIG->cacheFeedsInSeconds = 3600;

$CONFIG->cacheSiteLogoInSeconds = 604800; // 1 week

$CONFIG->siteReadOnly = false;
$CONFIG->siteReadOnlyReason = null;

$CONFIG->contactTwitter = "openrightsgroup";
$CONFIG->contactEmail = "james@jarofgreen.co.uk";
$CONFIG->contactEmailHTML = "james at jarofgreen dot co dot uk";
//$CONFIG->facebookPage="https://www.facebook.com/OpenTechCalendar";
//$CONFIG->googlePlusPage="https://plus.google.com/103293012309251213262";
$CONFIG->ourBlog= "https://www.openrightsgroup.org/";

/** "12hr" or "24hr" **/
$CONFIG->clockDisplayDefault = "12hr";

$CONFIG->eventsCantBeMoreThanYearsInFuture = 2; 
$CONFIG->eventsCantBeMoreThanYearsInPast = 1; 	
$CONFIG->calendarEarliestYearAllowed = 2012;

$CONFIG->sysAdminLogInTimeOutSeconds = 900;  // 15 mins


$CONFIG->newUsersAreEditors = true;
$CONFIG->allowNewUsersToRegister = true;


$CONFIG->userAccountVerificationSecondsBetweenAllowedSends = 900;  // 15 mins



// $CONFIG->fileStoreLocation= '/fileStore';
// $CONFIG->tmpFileCacheLocation = '/tmp/OpenTechCalendar3Cache/';
// $CONFIG->tmpFileCacheCreationPermissions = 0733;

$CONFIG->logFile = '/var/log/orgevents/orgevents.log';
$CONFIG->logFileParseDateTimeRange = '/var/log/orgevents/orgevents.log';

$CONFIG->sysAdminTimeZone = "Europe/London";

$CONFIG->widgetsJSPrefix = "ICanHasACalendarWidget";
$CONFIG->widgetsCSSPrefix = "ICanHasACalendarWidget";

$CONFIG->sessionLastsInSeconds = 14400; // 4 hours, 4 * 60 * 60

$CONFIG->resetEmailsGapBetweenInSeconds = 600; // 10 mins

$CONFIG->userWatchesGroupPromptEmailShowEvents = 3;
$CONFIG->userWatchesSitePromptEmailShowEvents = 3;
$CONFIG->userWatchesSiteGroupPromptEmailShowEvents = 3;
$CONFIG->userWatchesPromptEmailSafeGapDays = 30;

$CONFIG->newUserRegisterAntiSpam = true;
$CONFIG->contactFormAntiSpam = true;

$CONFIG->importURLExpireSecondsAfterLastEdit = 7776000; // 90 days
$CONFIG->importURLSecondsBetweenImports = 43200; // 12 hours
$CONFIG->importURLAllowEventsSecondsIntoFuture = 7776000; // 90 days

$CONFIG->upcomingEventsForUserEmailTextListsEvents = 20;
$CONFIG->upcomingEventsForUserEmailHTMLListsEvents = 5;

$CONFIG->siteSeenCookieStoreForDays = 30;

$CONFIG->extensions = array('AddressCodeGBOpenCodePoint','Facebook','Meetup');

$CONFIG->mediaNormalSize = 500;
$CONFIG->mediaThumbnailSize = 100;
$CONFIG->mediaQualityJpeg = 90;
$CONFIG->mediaQualityPng = 2;
$CONFIG->mediaBrowserCacheExpiresInseconds = 7776000; // 90 days
	
$CONFIG->apiExtraHeader1Html = null;
$CONFIG->apiExtraHeader1Text = null;
        




