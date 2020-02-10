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
Here you can activate your configuration. Hit the "Generate" Button and you will see the output of a configuration check. If you're confident of the results, you may proceed with the "Activate" Button.
</DIV>
<br />
<FORM method=POST name=dryrun>
<TABLE ALIGN=CENTER border=0 CLASS=status>
<TR><TD colspan=2 CLASS=statusTitle>
<BUTTON type='submit' name='Generate' value=Generate>Generate</BUTTON>
</TD</TR>
<TR><TD colspan=2 CLASS=statusTitle>
<BUTTON type='submit' name='Activate' value=Activate>Activate</BUTTON>
</TD</TR>
</TABLE></FORM>

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
    } else {
        echo "<PRE>\n";
        echo $out;
        echo "</PRE>\n";
    }
}
?>
</BODY></HTML>

<?php $db->close(); ?>
