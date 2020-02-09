<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>
Hacking Contacts
</TITLE>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/common.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/notifications.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/extinfo.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/status.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/summary.css'>
</HEAD>
<BODY CLASS='status'>

<?php
include_once 'database.php';
include_once 'functions.php';
?>
<BR /><BR />
<DIV ALIGN=CENTER CLASS='infoBoxTitle'>Dry Run New Configuration</DIV>
<DIV ALIGN=CENTER CLASS='infoBox'>
Here you can test your configuration. Hit the "Dry Run" Button and you will see the output of a configuration check. If you're confident of the results, you may proceed to the activate the configuration.
</DIV>
<br />
<FORM method=POST name=dryrun>
<TABLE ALIGN=CENTER border=0 CLASS=status>
<TR><TD colspan=2 CLASS=statusTitle>
<BUTTON type='submit' name='dry-run' value=DRY-RUN>DRY-RUN</BUTTON>
</TD</TR>
</TABLE></FORM>

<?php
if (isset($_POST['dry-run'])) {
    # populate clean test directory
    makeclean('test');
    populate('test');
    $out = shell_exec('/usr/sbin/nagios3 -v /etc/nagios3/test.cfg');
    echo "<PRE>\n";
    echo $out;
    echo "</PRE>\n";
}
?>
</BODY></HTML>

<?php $db->close(); ?>
