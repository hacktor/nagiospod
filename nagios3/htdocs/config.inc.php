<?php
//


$cfg['cgi_config_file']='/opt/vhosts/monitor.hacktor.com/etc/cgi.cfg';  // location of the CGI config file

$cfg['cgi_base_url']='/nagios/cgi-bin';

$cfg['authorized_for_system_information']='nagiosadmin';

// FILE LOCATION DEFAULTS
$cfg['main_config_file']='/opt/vhosts/monitor.hacktor.com/etc/nagios.cfg';  // default location of the main Nagios config file
$cfg['status_file']='/opt/vhosts/monitor.hacktor.com/var/status.dat'; // default location of Nagios status file
$cfg['state_retention_file']='/opt/vhosts/monitor.hacktor.com/var/retention.dat'; // default location of Nagios retention file



// utilities
require_once(dirname(__FILE__).'/includes/utils.inc.php');

?>
