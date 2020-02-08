#!/bin/bash

debconf-set-selections -v /root/debconf
export DEBIAN_FRONTEND=noninteractive
export DEBCONF_NONINTERACTIVE_SEEN=true

apt update
apt install -y vim apache2 apache2-utils php-sqlite3 nagios3 monitoring-plugins-basic nagios-nrpe-plugin libapache2-mod-php
apt clean
rm -Rf /var/lib/apt/lists/*

export DOC_ROOT="DocumentRoot /etc/nagios3/htdocs/"
sed -i "s,DocumentRoot.*,$DOC_ROOT," /etc/apache2/sites-enabled/000-default.conf
sed -i "s,</VirtualHost>,\n\tScriptAlias /cgi-bin/ /usr/lib/cgi-bin/nagios3/\n\n</VirtualHost>," /etc/apache2/sites-enabled/000-default.conf

rm -rf /etc/rsyslog.d /etc/rsyslog.conf
[ -d /tmp/nagios3 ] && rm -rf /etc/nagios3 && mv /tmp/nagios3 /etc/nagios3
mkdir -p /var/log/nagios3/archives
chown -R nagios:www-data /var/log/nagios3 /var/lib/nagios3
chown -R www-data:nagios /etc/nagios3

a2enmod session session_cookie session_crypto auth_form request cgi auth_digest

