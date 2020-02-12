<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>
Activate Configuration
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
<DIV ALIGN=CENTER CLASS='infoBoxTitle'>Activate New Configuration</DIV>
<DIV ALIGN=CENTER CLASS='infoBox'>
<FORM method=POST name=dryrun>
Here you can activate your configuration. Hitting the "Generate" Button will overwrite the current hosts and services configuration; make sure you did a "Dry Run" first!
<p><BUTTON type='submit' name='Generate' value=Generate>Generate</BUTTON></p>
If you're confident of the results, you may proceed with the "Activate" Button.
<p><BUTTON type='submit' name='Activate' value=Activate>Activate</BUTTON></p>
</FORM>
</DIV>

<?php
if (isset($_POST['Generate'])) {
    # populate clean objects directory
    makeclean('objects');
    populate('objects');
    $out = shell_exec('/usr/sbin/nagios3 -v /etc/nagios3/nagios.cfg');
    echo "<PRE>\n";
    echo $out;
    echo "</PRE>\n";
} elseif (isset($_POST['Activate'])) {
    shell_exec('pkill nagios3');
    $out = shell_exec('/usr/sbin/nagios3 -d /etc/nagios3/nagios.cfg');
    if (!$out) {
        echo "<DIV ALIGN=CENTER CLASS='infoBoxTitle'>Looks like that worked fine :-D</DIV>";
        echo "<DIV ALIGN=CENTER><IMG src=/images/pirate-ok.gif></DIV>";
    } else {
        echo "<DIV ALIGN=CENTER CLASS='infoBoxTitle'>Looks like something went terribly wrong :-(</DIV>";
        echo "<DIV ALIGN=CENTER><IMG src=/images/pirate-hmm.gif></DIV>";
        echo "<PRE>\n";
        echo $out;
        echo "</PRE>\n";
    }
}
?>
</BODY></HTML>

<?php $db->close(); ?>
