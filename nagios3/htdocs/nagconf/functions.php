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
    global $etc,$contacts,$hosts,$servicesbyhost;
    $config = '';
    # looping through variables
    foreach ($contacts as $name => $contact) {
        $config .= "define contact {\n\tcontact_name\t". $contact['name'] ."\n\temail\t";
        $config .= $contact['email'] ."\n\tuse\tgeneric-contact\n}\n";
    }
    if (isset($etc['api_key']) and isset($etc['chat_id'])) {
        $config .= "define contact {\n\tcontact_name\ttelegram\n\tpager\t";
        $config .= $etc['chat_id'] ."|". $etc['api_key'] ."\n\tuse\ttelegram-contact\n}\n";
    }
    file_put_contents('/etc/nagios3/' .$dir. '/contacts.cfg', $config);
    $config = '';
    $ctmp = join(",", array_keys($contacts));
    $ctmp = (isset($etc['api_key']) and isset($etc['chat_id'])) ? $ctmp.',telegram' : $ctmp;

    foreach ($hosts as $name => $host) {
        $config .= "define host {\n\thost_name\t". $name ."\n\taddress\t". $host['address'];
        $config .= "\n\talias\t". $host['alias'] ."\n\tcontacts\t". $ctmp ."\n\tuse\tinternet-server\n}\n";
    }
    file_put_contents('/etc/nagios3/' .$dir. '/hosts.cfg', $config);
    $config = '';
    foreach ($servicesbyhost as $host => $srvs) {
        foreach ($srvs as $cc => $s) {
            $config .= "define service {\n\tcheck_command\t". $cc ."\n\tservice_description\t". $s['descr'];
            $config .= "\n\thost_name\t". $host ."\n\tcontacts\t". $ctmp ."\n\tuse\tgeneric-service\n}\n";
        }
    }
    file_put_contents('/etc/nagios3/' .$dir. '/services.cfg', $config);
}

?>
