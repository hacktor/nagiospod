<?php
include_once 'functions.php';

$db = new SQLite3('/etc/nagios3/sqlite.db');
#$db = new SQLite3('/root/sqlite.db');

$res = $db->query('select * from hosts');
$hosts = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $hosts[$row['name']] = $row;
}

$res = $db->query('select * from services');
$servicesbyhost = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        if (! isset($servicesbyhost[$row['hostname']])) {
                $servicesbyhost[$row['hostname']] = array();
        }
        $servicesbyhost[$row['hostname']][$row['check_command']] = $row;
}

$res = $db->query('select * from checkcommands');
$checkcommands = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $checkcommands[$row['id']] = $row;
}

$res = $db->query('select * from contacts');
$contacts = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $contacts[$row['name']] = $row;
}

$res = $db->query('select * from etc');
$etc = array();
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $etc[$row['key']] = $row['value'];
}

function rmservicefromhost($db,$host,$ccommand) {
    global $error, $servicesbyhost;
    if (! normaltext($host)) {
        $error[] = "Hostname contains illegal characters";
    } elseif (! normaltext($ccommand)) {
        $error[] = "Servicename contains illegal characters";
    } elseif (count($servicesbyhost[$host]) > 1) {
        $q = $db->prepare('delete from services where hostname = :host and check_command = :service');
        $q->bindValue(':host',$host);
        $q->bindValue(':service',$ccommand);
        $q->execute();
        unset($servicesbyhost[$host][$ccommand]);
        $error[] = "removed";
    } else {
        $error[] = "Last service of a host can not be deleted. Delete host instead";
    }
}

function rmhost($db,$name) {
    global $error,$hosts,$servicesbyhost;
    if (! ishost($name)) {
        $error[] = "hostname not accepted";
    } else {
        $q = $db->prepare('delete from hosts where name = :name');
        $q->bindValue(':name',$name);
        $q->execute();
        $q = $db->prepare('delete from services where hostname = :name');
        $q->bindValue(':name',$name);
        $res = $q->execute();
        if (! $res) {
            $error[] = "Delete of $name from hosts table failed";
        } else {
            unset($hosts[$name]);
            unset($servicesbyhost[$name]);
            $error[] = "Host $name deleted";
        }
    }
}

function rmcontact($db,$name) {
    global $error,$contacts;
    if (! normaltext($name)) {
        $error[] = "contact name not accepted";
    } elseif ($name === "nagiosadmin") {
        $error[] = "Admin account can not be deleted. You can overwrite it with another email address";
    } else {
        $q = $db->prepare('delete from contacts where name = :name');
        $q->bindValue(':name',$name);
        $q->execute();
        unset($contacts[$name]);
    }
}

function addhost($db,$name,$alias,$address) {
    global $error,$hosts;
    if ($alias == '') { $alias = $name; }
    if (! ishost($name)) {
        $error[] = "hostname not accepted";
    } elseif (! preg_match('/^[A-Za-z0-9\-\.\,\; \/]+$/', $alias)) {
        $error[] = "alias not accepted";
    } elseif (! preg_match('/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/', $address)) {
        $error[] = "Not a valid IP address: $address";
    } else {
        $q = $db->prepare("insert into hosts (name,hgid,address,alias) values (:name,1,:addr,:alias)");
        $q->bindValue(':name', $name);
        $q->bindValue(':addr', $address);
        $q->bindValue(':alias', $alias);
        $res = $q->execute();
        if (! $res) {
            $error[] = "insert of $name into hosts table failed";
        } else {
            $hosts[$name] = [ 'name' => $name, 'address' => $address, 'alias' => $alias ];
            return true;
        }
    }
}

function addservice2host($db,$hostname,$srvname,$descr,$argnr,$arg1,$arg2,$arg3,$ccid) {
    global $error,$servicesbyhost;
    if (! ishost($hostname)) {
        $error[] = "Hostname not accepted";
    } elseif (! normaltext($srvname)) {
        $error[] = "Service Name not accepted";
    } elseif (! normaltext($descr)) {
        $error[] = "Service Description not accepted";
    } elseif (! is_numeric($argnr)) {
        $error[] = "Fishy input on addservice";
    } elseif (! normaltext($arg1)) {
        $error[] = "Argument 1 not accepted";
    } elseif (! normaltext($arg2)) {
        $error[] = "Argument 2 not accepted";
    } elseif (! normaltext($arg3)) {
        $error[] = "Argument 3 not accepted";
    } elseif (! is_numeric($ccid)) {
        $error[] = "Fishy input on addservice";
    } else {
        $ccommand = $srvname.($arg1 !== '' ? "!".$arg1 : '');
        $ccommand = $ccommand.($arg2 !== '' ? "!".$arg2 : '');
        $ccommand = $ccommand.($arg3 !== '' ? "!".$arg3 : '');
        $q = $db->prepare("insert into services (hostname,check_command,hgid,descr,argnr,arg1,arg2,arg3,ccid) values (:name,:srvname,1,:descr,:nr,:a1,:a2,:a3,:ccid)");
        $q->bindValue(':name', $hostname);
        $q->bindValue(':srvname', $ccommand);
        $q->bindValue(':descr', $descr);
        $q->bindValue(':nr', $argnr);
        $q->bindValue(':a1', $arg1);
        $q->bindValue(':a2', $arg2);
        $q->bindValue(':a3', $arg3);
        $q->bindValue(':ccid', $ccid);
        $res = $q->execute();
        if (! $res) {
            $error[] = "insert of $ccommand into services table failed";
        } else {
            $servicesbyhost[$hostname][$ccommand] = [ 'check_commands' => $ccommand, 'hostname' => $hostname, 'descr' => $descr, 'argnr' => $argnr,
                                                      'arg1' => $arg1, 'arg2' => $arg2, 'arg3' => $arg3, 'ccid' => $ccid ];
        }
    }
}

function addcontact($db,$name,$email,$telephone) {
    global $error,$contacts;
    if (! preg_match('/^[A-Za-z0-9 ]+$/', $name)) {
        $error[] = "Name/description not accepted: ".$name." (only alphanumerics and spaces allowed)";
    } elseif (! preg_match('/^[A-Za-z0-9\.\-]+\@[A-Za-z0-9\.]+\.[A-Za-z]+$/', $email)) {
        $error[] = "Email Address not accepted: ".$email;
    } elseif (! normaltext($telephone)) {
        $error[] = "Telephone not accepted: ".$telephone." (only numbers and a starting '+' allowed)";
    } else {
        $q = $db->prepare("replace into contacts (name,email,telephone) values (:name,:email,:tel)");
        $q->bindValue(':name', $name);
        $q->bindValue(':email', $email);
        $q->bindValue(':tel', $telephone);
        $q->execute();
        $contacts[$name] = [ 'name' => $name, 'email' => $email, 'telephone' => $telephone ];
    }
}

function addetc($db,$array) {
    global $etc;
    foreach ($array as $key => $value) {
        $q = $db->prepare("replace into etc (key,value) values (:key,:value)");
        $q->bindValue(':key', $key);
        $q->bindValue(':value', $value);
        $q->execute();
        $etc[$key] = $value;
    }
}

function updateetc($db,$key,$value) {
    global $etc;
    $q = $db->prepare("replace into etc (key,value) values (:key,:value)");
    $q->bindValue(':key', $key);
    $q->bindValue(':value', $value);
    $q->execute();
    $etc[$key] = $value;
}

?>
