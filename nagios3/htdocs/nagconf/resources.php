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
<DIV ALIGN=CENTER CLASS='infoBoxTitle'>Miscellaneous Configurations</DIV>
<DIV ALIGN=CENTER CLASS='infoBox'>
Some expert options here that effect nagios' inner workings. Advise is to not touch these if you're not sure what you're doing...

<FORM method=POST name=expert>
<TABLE border=0 CLASS=status width=70%>
<TR><TD colspan=2 CLASS=statusTitle>Notification Options</TD</TR>
<TR><TH width=35% CLASS=status>max_check_attempts:</TH><TD class=statusODD><input type=text name=max_check_attempts value="<?php echo $etc['max_check_attempts']; ?>">Number of failed checks before notifications are send</TD></TR>
<TR><TH width=35% CLASS=status>normal_check_interval:</TH><TD class=statusODD><input type=text name=normal_check_interval value="<?php echo $etc['normal_check_interval']; ?>">Minutes between checks</TD></TR>
<TR><TH width=35% CLASS=status>retry_check_interval:</TH><TD class=statusODD><input type=text name=retry_check_interval value="<?php echo $etc['retry_check_interval']; ?>">Minutes between retries when a service or host check is failed</TD></TR>
</TABLE>
<p><BUTTON type='submit' name='NOTIFY' value=Reconfig>Reconfig</BUTTON></p>
</FORM>

<FORM method=POST name=expert>
<TABLE border=0 CLASS=status width=70%>
<TR><TD colspan=2 CLASS=statusTitle>Mail Options</TD</TR>
<TR><TH width=35% CLASS=status>SMTP server:</TH><TD class=statusODD><input type=text name=MTA value="<?php echo $etc['$USER3$']; ?>">This is an internal ip address; probable better leave it as is</TD></TR>
<TR><TH width=35% CLASS=status>Notification From address:</TH><TD class=statusODD><input type=text name=FROM value="<?php echo $etc['$USER4$']; ?>">From address in outgoing emails</TD></TR>
</TABLE>
<p><BUTTON type='submit' name='MAILCFG' value=Reconfig>Reconfig</BUTTON></p>
</FORM>

</DIV>

<?php
if (isset($_POST['NOTIFY'])) {
    if (isset($_POST['max_check_attempts']) and ($etc['max_check_attempts'] !== $_POST['max_check_attempts'])) updateetc($db,'max_check_attempts', $_POST['max_check_attempts']);
    if (isset($_POST['normal_check_interval']) and ($etc['normal_check_interval'] !== $_POST['normal_check_interval'])) updateetc($db,'normal_check_interval', $_POST['normal_check_interval']);
    if (isset($_POST['retry_check_interval']) and ($etc['retry_check_interval'] !== $_POST['retry_check_interval'])) updateetc($db,'retry_check_interval', $_POST['retry_check_interval']);
} elseif (isset($_POST['MAILCFG'])) {
    if (isset($_POST['$USER3$']) and ($etc['$USER3$'] !== $_POST['$USER3$'])) updateetc($db,'$USER3$', $_POST['$USER3$']);
    if (isset($_POST['$USER4$']) and ($etc['$USER4$'] !== $_POST['$USER4$'])) updateetc($db,'$USER4$', $_POST['$USER4$']);
}
?>
</BODY></HTML>

<?php $db->close(); ?>
