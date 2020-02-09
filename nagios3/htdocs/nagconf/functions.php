<?php

$error		= array();
$warning	= array();
$good		= array();

$admin_user="guest";
$error=array();
if (isset($_SERVER['REMOTE_USER'])) {
    $admin_user=$_SERVER['REMOTE_USER'];
}

$hgid = 1;

#####

function hackvalidate($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function normaltext($text) {
  return preg_match('/^[A-Za-z0-9\;\.\-\_\+\@\,\(\)\ ]*$/', $text);
}

function makeclean($dir) {
    # shell_exec('rm /etc/nagios3/' .$dir. '/*');
}

function populate($dir) {
    global $contacts,$hosts,$servicesbyhost;
    $config = '';
    # looping through variables
    foreach ($contacts as $name => $contact) {
        $config .= "define contact {\n\tcontact_name\t". $contact['name'] ."\n\temail\t";
        $config .= $contact['email'] ."\n\tuse\tgeneric-contact\n}\n";
    }
    file_put_contents('/etc/nagios3/' .$dir. '/contacts.cfg', $config);
    $in = print_r($contacts,true);
    file_put_contents('/etc/nagios3/' .$dir. '/printr',$in);
}

?>
