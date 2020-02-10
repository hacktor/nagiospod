#!/bin/bash

chmod 775 /var/lib/nagios3 /var/lib/nagios3/rw
chmod 664 /var/lib/nagios3/rw/nagios.cmd
chown -R www-data:www-data /var/lib/nagios3
chown -R www-data:www-data /etc/nagios3
/usr/sbin/nagios3 -d /etc/nagios3/nagios.cfg
/usr/sbin/apache2ctl -DFOREGROUND

