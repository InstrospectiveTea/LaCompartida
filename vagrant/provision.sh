#!/bin/bash
MYSQL_ROOT_PASS=admin.asdwsx

export DEBIAN_FRONTEND=noninteractive

echo "mysql-server-5.6 mysql-server/root_password password $MYSQL_ROOT_PASS" | sudo debconf-set-selections
echo "mysql-server-5.6 mysql-server/root_password_again password $MYSQL_ROOT_PASS" | sudo debconf-set-selections

apt-get -y --quiet update
apt-get -y --quiet install lamp-server^
apt-get -y --quiet install php5-curl curl vim php-pear

#if [ ! -f /etc/phpmyadmin/config.inc.php ];
#then
	echo "phpmyadmin phpmyadmin/dbconfig-install boolean false" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/app-password-confirm password $MYSQL_ROOT_PASS" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/mysql/admin-pass password $MYSQL_ROOT_PASS" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/password-confirm password $MYSQL_ROOT_PASS" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/setup-password password $MYSQL_ROOT_PASS" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/database-type select mysql" | debconf-set-selections
	echo "phpmyadmin phpmyadmin/mysql/app-pass password $MYSQL_ROOT_PASS" | debconf-set-selections
	apt-get -y install phpmyadmin
#fi
apt-get -y --quiet autoremove purge
apt-get -y --quiet clean

pear install Numbers_Words-0.16.4
pear install Spreadsheet_Excel_Writer-beta
pear install OLE-0.5

# Apache
echo "AddDefaultCharset ISO-8859-1" >> /etc/apache2/conf.d/charset
echo "AddCharset ISO-8859-1 .iso8859-1 .latin1" >> /etc/apache2/conf.d/charset
echo "ServerName localhost" >> /etc/apache2/conf.d/name

sed -i "s/\/var\/www/\/var\/www\/vagrant/" /etc/apache2/sites-available/default
ln -s /vagrant /var/www/vagrant

# PHP
sed -i "s/short_open_tag = Off/short_open_tag = On/g" /etc/php5/apache2/php.ini
sed -i 's/;default_charset = "iso-8859-1"/default_charset = "iso-8859-1"/g' /etc/php5/apache2/php.ini
sed -i "s/error_reporting = E_ALL & ~E_DEPRECATED/error_reporting = E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR/g" /etc/php5/apache2/php.ini
sed -i "s/register_globals = Off/register_globals = On/g" /etc/php5/apache2/php.ini

# MySQL
echo "[mysqld]" >> /etc/mysql/conf.d/character.cnf
echo "character-set-server = latin1" >> /etc/mysql/conf.d/character.cnf
echo "character-set-client = latin1" >> /etc/mysql/conf.d/character.cnf

mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -u root -p$MYSQL_ROOT_PASS mysql
mysql -u root -p$MYSQL_ROOT_PASS mysql -e "CREATE USER 'admin'@'%' IDENTIFIED BY 'admin1awdx'"
mysql -u root -p$MYSQL_ROOT_PASS mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'%'"

# Restart
service apache2 restart
service mysql restart

