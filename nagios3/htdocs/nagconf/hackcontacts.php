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
require_once 'htpasswd.inc';

$pass_array=load_htpasswd();

# Update password
if (isset($_POST['oldpw'])) {
  if ($_POST['oldpw'] == $_SERVER['PHP_AUTH_PW']) {
    if ($_POST['newpw1'] == $_POST['newpw2']) {
      if (strlen($_POST['newpw1']) > 8) {
        $good[]="Changing password";
        if (isset($_SERVER['PHP_AUTH_USER']) && test_htpasswd( $pass_array,  $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] )) {
	  $pass_array[$admin_user] = rand_salt_crypt($_POST['newpw1']);
	  save_htpasswd($pass_array);
        }
      } else {
        $error[]="Password must be more than 8 characters";
      }
    } else {
      $error[]="Passwords do not match";
    }
  } else {
    $error[] = "Old Password Incorrect";
  }
}

# Remove Contact
isset($_POST['removecontact']) && rmcontact($db,$_POST['removecontact']);

# Add Telegram bot
if (isset($_POST['api_key']) and isset($_POST['chat_id'])) {
    addetc($db, ['api_key' => $_POST['api_key'], 'chat_id' => $_POST['chat_id']]);
}

# Add Contact
if (isset($_POST['contactadd']) and ($_POST['contactadd'] == 'Add')) {
  $name		= isset($_POST['contact_name_add']) ? hackvalidate($_POST['contact_name_add']) : ''; 
  $email	= isset($_POST['contact_email_add']) ? hackvalidate($_POST['contact_email_add']) : ''; 
  $telephone	= isset($_POST['contact_telephone_add']) ? hackvalidate($_POST['contact_telephone_add']) : ''; 
  addcontact($db,$name,$email,$telephone);
}

foreach ($error as $ln) { echo "<font color=red>" .$ln. "</font><br>\n"; }
foreach ($warning as $ln) { echo "<font color=orange>" .$ln. "</font><br>\n"; }
foreach ($good as $ln) { echo "<font color=green>" .$ln. "</font><br>\n"; }

?>
<BR /><BR />
<DIV CLASS='infoBoxTitle'>Hacking Contact Information</DIV>
<DIV CLASS='infoBox'>
Here is the place to add and remove email addresses to your monitoring configuration to send notifications to.<br />
You may also configure a telegram bot to send messages to.
</DIV>
<br />

<DIV ALIGN=CENTER CLASS='statusTitle'>Logged in as <?php echo $admin_user ?></DIV>
<TABLE border=0 width=100%>
<TR><TD valign=top width=50%>
<FORM method=POST name=telegram>
<TABLE border=0 CLASS=status width=100%>
<TR><TD colspan=2 CLASS=statusTitle>Telegram Bot Information</TD</TR>
<TR><TH width=35% CLASS=status>API KEY</TH><TD class=statusODD><input type=text name=api_key value=<?php echo $etc['api_key']; ?>></TD></TR>
<TR><TH width=35% CLASS=status>CHAT ID</TH><TD class=statusODD><input type=text name=chat_id value=<?php echo $etc['chat_id']; ?>></TD></TR>
<TR><TD cellspan=2><INPUT type=submit name=UpdateTEL value="Update Telegram" /></TD></TR>
</TABLE></FORM>

</TD><TD valign=top width=50%>
<FORM method=POST name=chpass value=chpass>
<TABLE border=0 CLASS=status width=100%>
<TR><TD colspan=2 CLASS=statusTitle>Login Information</TD</TR>
<TR><TH width=35% CLASS=status>Login:</TH><TD class=statusODD><?php echo $admin_user ?></TD></TR>
<TR><TH width=35% CLASS=status>Old Password:</TH><TD class=statusODD><input type=password name=oldpw></TD></TR>
<TR><TH width=35% CLASS=status>New Password:</TH><TD class=statusODD><input type=password name=newpw1></TD></TR>
<TR><TH width=35% CLASS=status>Retype New Password:</TH><TD class=statusODD><input type=password name=newpw2></TD></TR>
<TR><TD cellspan=2><INPUT type=submit name=UpdatePW value="Update Password" /></TD></TR>
</TABLE></FORM>
</TD></TR></TABLE>

<DIV ALIGN=CENTER CLASS='statusTitle'>Overview of Contacts for Notifications and Alerts</DIV>
<TABLE border=0 width=100% CLASS=status>
<TR><TH CLASS=status width=30%>Name/description</TH>
<TH CLASS=status width=25%>Email Address</TH>
<TH CLASS=status width=15%>Telephone</TH>
<TH CLASS=status width=5%>Remove</TH></TR>

<?php
foreach($contacts as $contact) {
    echo "<TR><FORM method=post name=removecontact>\n";
    echo "<TD CLASS=queueEVEN>" . $contact['name'] . "</TD>\n";
    echo "<TD CLASS=queueEVEN>" . $contact['email'] . "</TD>\n";
    echo "<TD CLASS=queueEVEN>" . $contact['telephone'] . "</TD>\n";
    echo "<TD CLASS=queueEVEN><BUTTON type='submit' name=removecontact value=" . $contact['name'] . " ALT=Remove>\n";
    echo "<IMG SRC='/nagios/images/disabled.gif'></BUTTON></TD></FORM></TR>\n";
}
?>

<FORM method=POST name=contactaddform value=contactadd>
<TR>
<TD CLASS='notificationsEVEN'><INPUT type=text maxlength=64 name=contact_name_add></TD>
<TD CLASS='notificationsEVEN'><INPUT type=text maxlength=64 name=contact_email_add></TD>
<TD CLASS='notificationsEVEN'><INPUT type=text maxlength=64 name=contact_telephone_add></TD>
<TD CLASS='notificationsEVEN'><INPUT type=submit name=contactadd value='Add'></TD>
</TR>
</FORM>
</TABLE></BODY></HTML>

<?php $db->close(); ?>
