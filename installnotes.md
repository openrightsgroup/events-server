# Installing on Debian Stable

## Assumptions, and Live vs Test

This assumes you have a fresh Debian Stable box and are installing the live system.

If you want to install a test enviroment, boot up Debian Stable in VirtualBox or whatever you prefer. Follow the instructions below, with one exception:

When you create /orgevents-latest/config.live.private.php and /orgevents-latest/config.live.private.php add the following lines:

````
$CONFIG->webIndexDomain = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webSiteDomain = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webSysAdminDomain = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webIndexDomainSSL = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webSiteDomainSSL = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webSysAdminDomainSSL = "cal-dev-1.default.orgtech.uk0.bigv.io";
$CONFIG->webCommonSessionDomain = "cal-dev-1.default.orgtech.uk0.bigv.io";
````
And change them all to whatever domain your test server will have.


If you plan to be doing coding, you should also set in those 2 files:

````
$CONFIG->isDebug = true;
````

This will give you better error messages. Also it won't send any emails, but instead write the contents to /tmp for you to inspect.

## Let's get started

````
sudo apt-get install apache2 postgresql php5-gd php5 php-apc php5-pgsql git php5-intl php5-curl curl
cd /
sudo git clone https://github.com/openrightsgroup/events-server.git /orgevents-latest
sudo git clone https://github.com/openrightsgroup/events-server.git /orgevents-web
sudo chown www-data:www-data /orgevents-web/cache/templates.web/
sudo mkdir /var/log/orgevents/
sudo chown www-data:www-data /var/log/orgevents/ 
sudo mkdir /orgevents-php
cd /orgevents-php/
sudo sh -c "curl -sS https://getcomposer.org/installer | php"
cd /orgevents-latest/
sudo /orgevents-php/composer.phar install
cd /orgevents-web/
sudo /orgevents-php/composer.phar install
````

## Email server

````
sudo apt-get install postfix
````

Select "Internet Site"

## Web Server

Let's get some SSL certs into /etc/apache2/sslcert.crt and /etc/apache2/sslcert.key 

Ideally some real ones but you can also follow steps 1-4 of http://www.akadia.com/services/ssh_test_certificate.html then 

````
sudo cp server.crt /etc/apache2/sslcert.crt
sudo cp server.key /etc/apache2/sslcert.key
````

Now edit /etc/apache2/sites-enabled/000-default and save:

````
<VirtualHost *:80>
        ServerAdmin james@jarofgreen.co.uk

        DocumentRoot /orgevents-web/webSingleSite/
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /orgevents-web/webSingleSite/ >
                AllowOverride all
                Order allow,deny
                allow from all
        </Directory>


        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>


<VirtualHost *:443>
        SSLEngine on
        SSLCertificateFile /etc/apache2/sslcert.crt
        SSLCertificateKeyFile /etc/apache2/sslcert.key

        SetEnvIf User-Agent ".*MSIE.*" nokeepalive ssl-unclean-shutdown

        ServerAdmin james@jarofgreen.co.uk

        DocumentRoot /orgevents-web/webSingleSite/
        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>
        <Directory /orgevents-web/webSingleSite/ >
                AllowOverride all
                Order allow,deny
                allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

````

Now run:

    sudo a2enmod

And select
 
    rewrite expires ssl


Now run:

    sudo /etc/init.d/apache2 restart

## DB

````
 sudo su postgres
 psql
````

Insert SQL commands. Set your own random password from something like http://randpass.com/

````
CREATE USER orgevents WITH PASSWORD 'random_password_goes_here';
CREATE DATABASE orgevents WITH OWNER orgevents ENCODING 'UTF8'  LC_COLLATE='en_GB.UTF-8' LC_CTYPE='en_GB.UTF-8' ;
\q
````

Type exit to return to your normal shell.

If you have a backup, restore it now. If this is a fresh install just continue.

## App Config

Create 2 text files, /orgevents-latest/config.live.private.php and /orgevents-latest/config.live.private.php both with the same contents:

````
$CONFIG->databasePassword = 'random_password_goes_here';
$CONFIG->sysAdminExtraPassword = "another_random_password_goes_here";
````

For those of you with sharp eyes, yes there is no PHP opening tag.

## App Update


Edit the file /orgevents-latest/update

````
git pull
sudo /orgevents-php/composer.phar install
php core/cli/upgradeDatabase.php
cat config.live.public.php > config.new.php
cat config.live.private.php >> config.new.php
diff config.new.php config.php
````

Edit the file /orgevents-web/update

````
git pull
cp -R /orgevents-latest/vendor/* /orgevents-web/vendor/
rm /orgevents-web/cache/templates.web/*/*/*.php
chown -R www-data:www-data /orgevents-web/cache/templates.web/
rm /orgevents-web/cache/templates.cli/*/*/*.php
cat config.live.public.php > config.new.php
cat config.live.private.php >> config.new.php
diff config.new.php config.php
````

Make both those files executable, and run some commands:

````
sudo chown root:root /orgevents-latest/update
sudo chown root:root /orgevents-web/update
sudo chmod u+x /orgevents-latest/update
sudo chmod u+x /orgevents-web/update
sudo sh -c "/bin/cat /orgevents-latest/config.live.public.php > /orgevents-latest/config.php"
sudo sh -c "/bin/cat /orgevents-latest/config.live.private.php >> /orgevents-latest/config.php"
sudo sh -c "/bin/cat /orgevents-web/config.live.public.php > /orgevents-web/config.php"
sudo sh -c "/bin/cat /orgevents-web/config.live.private.php >> /orgevents-web/config.php"
cd /orgevents-latest
sudo ./update
````

## If it's a new install only

(If your have restored a DB skip this step)

Run, replacing USERNAME, EMAIL & PASSWORD with sensible stuff. EMAIL must be the same both times.

````
cd /orgevents-latest
php core/cli/loadStaticData.php
php core/cli/createUser.php USERNAME EMAIL PASSWORD sysadmin
php core/cli/createSite.php orgevents EMAIL
````

There is a delibrate pause in those scripts, it probably hasn't crashed.

## Cron

    sudo crontab -e

Add:

```
## Send emails
10 3 * * * php /orgevents-web/core/cli/sendUpcomingEventsForUser.php yes >> /var/log/orgevents/sendUpcomingEventsForUser.log 2>&1
45 * * * * php /orgevents-web/core/cli/sendUserWatchesGroupNotifyEmails.php yes >> /var/log/orgevents/sendUserWatchesGroupNotifyEmails.log 2>&1
0 6 * * * php /orgevents-web/core/cli/sendUserWatchesGroupPromptEmails.php yes >> /var/log/orgevents/sendUserWatchesGroupPromptEmails.log 2>&1

## Import
10 * * * * php /orgevents-web/core/cli/runImportURL.php yes >> /var/log/orgevents/runImportURL.log 2>&1


## Caches
30 * * * * php /orgevents-web/core/cli/updateAreaFutureEventsCache.php >> /var/log/orgevents/updateAreaFutureEventsCache.log 2>&1
40 * * * * php /orgevents-web/core/cli/updateAreaBoundsCache.php >> /var/log/orgevents/updateAreaBoundsCache.log 2>&1
50 * * * * php /orgevents-web/core/cli/updateAreaParentCache.php >> /var/log/orgevents/updateAreaParentCache.log 2>&1

## History
5 * * * * php /orgevents-web/core/cli/updateHistoryChangedFlags.php >> /var/log/orgevents/updateHistoryChangedFlags.log 2>&1
````


## UK Postcodes

Some extra data needs to be installed.

Follow the instructions on http://docs.openacalendar.org/en/master/serveradministrators/extension.AddressCodeGBOpenCodePoint/installation.html 
in both folders /orgevents-web/ and  /orgevents-latest/

## Backups

Create the script /root/.pgpass with the contents:

    localhost:5432:orgevents:orgevents:random_password_goes_here

Create the script /root/backup with the contents:

```
#!/bin/sh
echo "Running Nightly Backup"
CURRENT=$(date +%d)
pg_dump  -U orgevents orgevents  -h localhost -f /var/backups/orgevents/orgevents-$CURRENT.sql -w
````

Run the commands

````
sudo chmod 0600  /root/.pgpass
sudo mkdir /var/backups/orgevents
sudo chmod u+x /root/backup
sudo /root/backup
sudo crontab -e
````

Add:

    0 3 * * * /root/backup







