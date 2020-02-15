<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>
Hacking Services
</title>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/common.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/notifications.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/extinfo.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/status.css'>
<LINK REL='stylesheet' TYPE='text/css' HREF='/nagios/stylesheets/summary.css'>
</head>
<body CLASS='status'>

<?php
include 'database.php';
include_once 'functions.php';

# Add a host
if (isset($_POST['addhost']) and isset($_POST['hostname']) and isset($_POST['hostalias']) and isset($_POST['hostaddress']) and isset($_POST['ccid']) and !isset($_POST['justsrv'])) {
    if (isset($checkcommands[$_POST['ccid']])) {
        $cc = $checkcommands[$_POST['ccid']];
        if (isset($_POST['descr'])) {
            $a1 = (isset($_POST['arg1']) ? $_POST['arg1'] : '');
            $a2 = (isset($_POST['arg2']) ? $_POST['arg2'] : '');
            $a3 = (isset($_POST['arg3']) ? $_POST['arg3'] : '');
            addhost($db,$_POST['hostname'],$_POST['hostalias'],$_POST['hostaddress']);
            addservice2host($db,$_POST['hostname'],$cc['name'],$_POST['descr'],$cc['argnr'],$a1,$a2,$a3,$cc['id']);
        } else {
            $ccargs = $cc;
            $hostargs = $_POST['hostname'];
	    $hostalias = $_POST['hostalias'];
	    $hostaddress = $_POST['hostaddress'];
        }
    }
}

# Remove a host
if (isset($_POST['rmhost'])) {
    rmhost($db,$_POST['rmhost']);
}

# Add a service to a host
if (isset($_POST['addsrvtohost'])) {
    foreach ($_POST['addsrvtohost'] as $host => $ccid) break;
    if (isset($hosts[$host]) and isset($checkcommands[$ccid])) {
        # need to get description and parameters
        $ccargs = $checkcommands[$ccid];
        $hostargs = $host;
	$hostalias = $hosts[$host]['alias'];
	$hostaddress = $hosts[$host]['address'];
        $justsrv = true;
    }
} elseif (isset($_POST['justsrv']) and isset($_POST['hostname']) and isset($_POST['ccid']) and isset($_POST['descr'])) {
    # we have all the info; add the service
    $cc = $checkcommands[$_POST['ccid']];
    $a1 = (isset($_POST['arg1']) ? $_POST['arg1'] : '');
    $a2 = (isset($_POST['arg2']) ? $_POST['arg2'] : '');
    $a3 = (isset($_POST['arg3']) ? $_POST['arg3'] : '');
    addservice2host($db,$_POST['hostname'],$cc['name'],$_POST['descr'],$cc['argnr'],$a1,$a2,$a3,$cc['id']);
}

# Remove a service from a host
if (isset($_POST['removesrv'])) {
    $tmp=explode(';',$_POST['removesrv']);
    if (count($servicesbyhost[$tmp[0]]) > 1) {
        rmservicefromhost($db,$tmp[0],$tmp[1]);
    } else {
        $error[] = "You tried to remove a service from a host with only one service. This is not allowed. You should remove the host instead";
    }
}

foreach ($error as $ln) { echo "<font color=red>" .$ln. "</font><br>\n"; }

$tbody='';
$allcheckselect='';

foreach ($hosts as $host) {
    $tdhost="<TR><FORM method=POST><TD CLASS=statusODD>".$host['name']."</TD><TD CLASS=statusODD>".$host['address']."</TD><TD CLASS=statusODD>".$host['alias'].'</TD>';

    foreach ($servicesbyhost[$host['name']] as $ccommand => $service) {

        $tbody.=$tdhost . "<TD CLASS=statusODD>".$service['descr']."</TD>";
        $tbody.="<TD CLASS=statusODD><BUTTON type='submit' name=removesrv value='" . $host['name'].';'.$ccommand . "' alt=Remove><IMG SRC='/nagios/images/disabled.gif'></BUTTON></TD></FORM></TR>";
	    $tdhost='<TR><FORM method=POST><TD></TD><TD></TD><TD></TD>';
    }

    $tbody.="<TR><FORM method=POST><TD></TD><TD></TD><TD></TD>";
    $tbody.="<TD CLASS=statusODD><SELECT name=addsrvtohost[".$host['name']."]>";

    foreach ($checkcommands as $cc) {
        if (isset($servicesbyhost[$host['name']][$cc['name']]) and ($cc['argnr'] == 0)) {
            continue;
        } else {
            $tbody.="<OPTION value=".$cc['id'].">".$cc['descr']."</OPTION>";
        }
    }

    $tbody.="</SELECT></TD><TD CLASS=statusODD><INPUT type=submit name=addsrv[".$host['name']."] value='Add'></TD></FORM></TR>";
}

foreach ($checkcommands as $checkcommand) {
    $allcheckselect.="<OPTION value=".$checkcommand['id'].">".$checkcommand['descr']."</OPTION>";
}
?>

<DIV CLASS='infoBoxTitle'>Hosts and Services to Monitor</DIV>
<DIV CLASS='infoBox'>
Here is the place to add and remove hosts to your monitoring configuration and to connect them to services.<br />
Since every Host you want to monitor needs at least one service check associated with it, you should allways select a check command you want to use. <br />
If a certain check command needs 1 or more arguments, e.g. a port number, you will be asked for it after you click "Add"<br /><br />
Check updates to your configuration with the Dry-Run button before you hit the Update button.
</DIV>
<br /><br />

<?php
if (isset($ccargs)) {
    echo "<DIV ALIGN=CENTER CLASS='statusTitle'>Please specify parameters for ". $ccargs['descr'] ." on ". $hostargs ."</DIV>\n";
    echo "<FORM method=POST name=args><TABLE border=0 width=100% CLASS=status><TR>";
    echo "<TH CLASS=status>Description of service</TH>";
    if ($ccargs['argnr'] > 0) {
        echo "<TH CLASS=status>". $ccargs['arg1_descr'] ."</TH>";
    }
    if ($ccargs['argnr'] > 1) {
        echo "<TH CLASS=status>". $ccargs['arg2_descr'] ."</TH>";
    }
    if ($ccargs['argnr'] > 2) {
        echo "<TH CLASS=status>". $ccargs['arg3_descr'] ."</TH>";
    }
    echo "<TH CLASS=status></TH></TR><TR><TD class=statusODD><INPUT type=text name=descr></TD>";
    if ($ccargs['argnr'] > 0) {
        echo "<TD class=statusODD><INPUT type=text id=arg1 name=arg1></TD>";
    }
    if ($ccargs['argnr'] > 1) {
        echo "</TR><TR><TD class=statusODD><INPUT type=text id=arg2 name=arg2></TD>";
    }
    if ($ccargs['argnr'] > 2) {
        echo "</TR><TR><TD class=statusODD><INPUT type=text id=arg3 name=arg3></TD>";
    }
    echo "<TD class=statusODD><INPUT type=submit name=args value=Submit></TD</TR></TABLE><INPUT type=hidden name=hostname value='". $hostargs;
    echo "'><INPUT type=hidden name=ccid value=". $ccargs['id'] ."><INPUT type=hidden name=hostalias value='". $hostalias;
    echo "'><INPUT type=hidden name=hostaddress value=". $hostaddress ."><INPUT type=hidden name=addhost value=Add>";
    if (isset($justsrv)) {
        echo "<INPUT type=hidden name=justsrv value=true>";
    }
    echo "</FORM>";
}
?>

<br />
<DIV ALIGN=CENTER CLASS='statusTitle'>Service Checks For All Hosts</DIV>
<TABLE border=0 width=100% CLASS=status>
<TR><TH CLASS=status width=22.5%>Host Name</TH>
<TH CLASS=status width=22.5%>Host Address</TH>
<TH CLASS=status width=22.5%>Host Alias</TH>
<TH CLASS=status width=22.5%>Description</TH>
<TH CLASS=status width=10%>Add/Remove</TH></TR>

<?php echo $tbody ?>

</TABLE>

<DIV ALIGN=CENTER CLASS='statusTitle'>Add a Host</DIV>
<FORM name=addhost method=POST>
<TABLE border=0 width=100% CLASS=status>
<TR><TH CLASS=status width=22.5%>Host Name</TH>
<TH CLASS=status width=22.5%>Host Address</TH>
<TH CLASS=status width=22.5%>Host Alias</TH>
<TH CLASS=status width=22.5%>Service to Check</TH>
<TH CLASS=status width=10%>Add</TH></TR>
<TR><TD CLASS=statusODD><INPUT type=text name=hostname></TD>
<TD CLASS=statusODD><INPUT type=text name=hostaddress></TD>
<TD CLASS=statusODD><INPUT type=text name=hostalias></TD>
<TD CLASS=statusODD><SELECT name=ccid><?php echo $allcheckselect ?></SELECT></TD>
<TD CLASS=statusODD><INPUT type=submit name=addhost value=Add></TD></TR>
</TABLE></FORM>

<DIV ALIGN=CENTER CLASS='statusTitle'>Remove a Host</DIV>
<FORM name=removehost method=post>
<TABLE border=0 CLASS=status align=CENTER>
<TR><TH CLASS=status>Host Name</TH>
<TH CLASS=status>Delete</TH></TR>
<TR><TD CLASS=statusODD><SELECT name=rmhost>

<?php foreach ($hosts as $host) { echo "<OPTION value=".$host['name'].">".$host['name']."</OPTION>"; } ?>

</SELECT></TD>
<TD CLASS=statusODD><BUTTON type='submit' name=removehost value=rmhost alt=Remove><IMG SRC='/nagios/images/disabled.gif'></BUTTON></TD></TR>
</TABLE></FORM>

</BODY></HTML>

<?php $db->close(); ?>
