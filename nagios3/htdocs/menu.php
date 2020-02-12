<?php
include_once(dirname(__FILE__).'/includes/utils.inc.php');
$isadmin = ( $cfg['authorized_for_system_information'] === $_SERVER['REMOTE_USER'] );
$link_target="main";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Web Interface Monitoring</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="robots" content="noindex, nofollow" />
	<link rel="stylesheet" type="text/css" href="stylesheets/interface/menu.css" media="screen, projection" />
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
	<script type="text/javascript" src="js/mootools.js"></script>
	<script type="text/javascript" src="js/menu.js"></script>
</head>
<body>
	<div id="menu">
		<h2>Monitoring</h2>
		<ul>
			<li class="menuli_style1"><a href="/nagios/cgi-bin/tac.cgi" target="main">Tactical Overview</a></li>
			<li class="menuli_style2"><a href="/nagios/cgi-bin/status.cgi?hostgroup=all&amp;style=hostdetail" target="main">Host Detail</a></li>
			<li class="menuli_style1"><a href="/nagios/cgi-bin/status.cgi?host=all" target="main">Service Detail</a></li>
			<li class="menuli_style2"><a href="/nagios/cgi-bin/status.cgi?host=all&amp;servicestatustypes=28" target="main">Service Problems</a></li>
			<li class="menuli_style1"><a href="/nagios/cgi-bin/status.cgi?hostgroup=all&amp;style=hostdetail&amp;hoststatustypes=12" target="main">Host Problems</a></li>
		</ul>
		<h2>Reporting</h2>
		<ul>
			<li class="menuli_style2"><a href="/nagios/cgi-bin/avail.cgi" target="main">Availability</a></li>
			<li class="menuli_style2"><a href="/nagios/cgi-bin/history.cgi?host=all" target="main">Alert History</a></li>
			<li class="menuli_style1"><a href="/nagios/cgi-bin/summary.cgi" target="main">Alert Summary</a></li>
			<li class="menuli_style2"><a href="/nagios/cgi-bin/notifications.cgi?contact=all" target="main">Notifications</a></li>
			<li class="menuli_style1"><a href="/nagios/cgi-bin/showlog.cgi" target="main">Event Log</a></li>
		</ul>
		<h2>Configuration</h2>
		<ul>
			<li class="menuli_style1"><a href="/nagconf/hackhosts.php" target="main">Hosts and Services</a></li>
			<li class="menuli_style1"><a href="/nagconf/hackcontacts.php" target="main">Contacts</a></li>
			<li class="menuli_style1"><a href="/nagconf/hackread.php" target="main">Help</a></li>
		</ul>
        <h2>Danger Zone</h2>
        <ul>
			<li class="menuli_style1"><a href="/nagconf/checker.php" target="main">Dry Run</a></li>
			<li class="menuli_style1"><a href="/nagconf/activate.php" target="main">Acivate Configuration</a></li>
			<li class="menuli_style1"><a href="/nagconf/resources.php" target="main">Miscellaneous Options</a></li>
        </ul>
	</div>
</body>
</html>
