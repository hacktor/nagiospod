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
    return preg_match('/^[A-Za-z0-9\!\;\.\-\_\+\@\,\(\)\ ]*$/', $text);
}

function save_htpasswd($user,$pw) {
    shell_exec("htpasswd -b /etc/nagios3/htpasswd.users $user $pw");
}

function makeclean($dir) {
    shell_exec('rm -f /etc/nagios3/' .$dir. '/*.cfg');
}

function populate($dir) {
    global $etc,$contacts,$hosts,$servicesbyhost;
    $config = '';
    # looping through variables
    foreach ($contacts as $name => $contact) {
        $config .= "define contact {\n\tcontact_name\t". $contact['name'] ."\n\temail\t";
        $config .= $contact['email'] ."\n\tuse\tgeneric-contact\n}\n";
    }
    # abuse email and pager macro's for api_key and chat_id
    if (isset($etc['api_key']) and isset($etc['chat_id']) and !empty($etc['api_key']) and !empty($etc['chat_id'])) {
        $config .= "define contact {\n\tcontact_name\ttelegram\n\tpager\t".$etc['chat_id']."\n\temail\t".$etc['api_key']."\n\tuse\ttelegram-contact\n}\n";
    }
    file_put_contents('/etc/nagios3/' .$dir. '/contacts.cfg', $config);
    $config = '';
    $ctmp = join(",", array_keys($contacts));
    $ctmp = (isset($etc['api_key']) and isset($etc['chat_id'])) ? $ctmp.',telegram' : $ctmp;

    foreach ($hosts as $name => $host) {
        $config .= "define host {\n\thost_name\t". $name ."\n\taddress\t". $host['address'];
	$config .= "\n\tmax_check_attempts\t". $etc['max_check_attempts'];
	$config .= "\n\tnormal_check_interval\t". $etc['normal_check_interval'];
	$config .= "\n\tretry_check_interval\t". $etc['retry_check_interval'];
        $config .= "\n\talias\t". $host['alias'] ."\n\tcontacts\t". $ctmp ."\n\tuse\tinternet-server\n}\n";
    }
    file_put_contents('/etc/nagios3/' .$dir. '/hosts.cfg', $config);
    $config = '';
    foreach ($servicesbyhost as $host => $srvs) {
        foreach ($srvs as $cc => $s) {
            $config .= "define service {\n\tcheck_command\t". $cc ."\n\tservice_description\t". $s['descr'];
	    $config .= "\n\tmax_check_attempts\t". $etc['max_check_attempts'];
	    $config .= "\n\tnormal_check_interval\t". $etc['normal_check_interval'];
	    $config .= "\n\tretry_check_interval\t". $etc['retry_check_interval'];
            $config .= "\n\thost_name\t". $host ."\n\tcontacts\t". $ctmp ."\n\tuse\tgeneric-service\n}\n";
        }
    }
    file_put_contents('/etc/nagios3/' .$dir. '/services.cfg', $config);
}

function fileset($file,$search,$replace) {
    $lines = file($file);
    $result = '';
    
    foreach($lines as $line) {
        if(substr($line, 0, strlen($search)) == $search) {
            $result .= "$replace\n";
        } else {
            $result .= $line;
        }
    }
    file_put_contents($file, $result);
}

?>
