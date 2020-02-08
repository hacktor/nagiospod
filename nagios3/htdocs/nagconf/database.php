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
        $contacts[$row['id']] = $row;
}

function updateaddress($db,$addres,$zip,$city,$country,$admin_contact,$admin_tel,$admin_email) {
    global $error;
    if (! normaltext($addres)) {
        $error[] = "Address contains illegal characters: ".$addres;
    } elseif (! normaltext($zip)) {
        $error[] = "ZIP code contains illegal characters: ".$zip;
    } elseif (! normaltext($city)) {
        $error[] = "City contains illegal characters: ".$city;
    } elseif (! normaltext($country)) {
        $error[] = "Coutry contains illegal characters: ".$country;
    } elseif (! normaltext($admin_contact)) {
        $error[] = "First Contact contains illegal characters: ".$admin_contact;
    } elseif (! normaltext($admin_tel)) {
        $error[] = "Phone number contains illegal characters: ".$admin_tel;
    } elseif (! normaltext($admin_email)) {
        $error[] = "Email Address contains illegal characters: ".$admin_email;
    } else {
        $q = $db->prepare("update customers set address = ?,zipcode = ?,city = ?,country = ?, admin_contact = ?,admin_tel = ?,admin_email = ?, hgid = 1");
        $q->execute(array($addres,$zip,$city,$country,$admin_contact,$admin_tel,$admin_email));
    }
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
    if (! preg_match('/^[A-Za-z0-9\-\.]+$/', $name)) {
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

function rmcontact($db,$id) {
    if (! is_numeric($id)) {
        $error[] = "contact id not accepted";
    } else {
        $q = $db->prepare('delete from contacts where id = ":id"');
        $q->bindValue(':id',$id);
        $q->execute();
    }
    unset($contacts[$id]);
}

function addhost($db,$name,$alias,$address) {
    global $error,$hosts;
    if ($alias == '') { $alias = $name; }
    if (! preg_match('/^[A-Za-z0-9\-\.]+$/', $name)) {
        $error[] = "hostname not accepted";
    } elseif (! preg_match('/^[A-Za-z0-9\-\.\,\; \/]+$/', $alias)) {
        $error[] = "alias not accepted";
    } elseif (! preg_match('/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/', $address)) {
        $error[] = "Not a valid IP address";
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
        }
    }
}

function addservice2host($db,$hostname,$srvname,$descr,$argnr,$arg1,$arg2,$arg3,$ccid) {
    global $error,$servicesbyhost;
    if (! normaltext($hostname)) {
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
            $servicesbyhost[$hostname][$ccommand] = [ 'check_commands' => $ccommand, 'hostname' => $name, 'descr' => $descr, 'argnr' => $argnr,
                                                      'arg1' => $arg1, 'arg2' => $arg2, 'arg3' => $arg3, 'ccid' => $ccid ];
        }
    }
}

function addcontact($db,$name,$email,$telephone) {
    global $error;
    if (! preg_match('/^[A-Za-z0-9 ]+$/', $name)) {
        $error[] = "Name/description not accepted: ".$name." (only alphanumerics and spaces allowed)";
    } elseif (! preg_match('/^[A-Za-z0-9\.\-]+\@[A-Za-z0-9]+\.[A-Za-z]+$/', $email)) {
        $error[] = "Email Address not accepted: ".$email;
    } elseif (! preg_match('/^[0-9\+][0-9]+$/', $telephone)) {
        $error[] = "Telephone not accepted: ".$telephone." (only numbers and a starting '+' allowed)";
    } else {
        $q = $db->prepare("insert into contacts (name,hgid,email,telephone) values (:name,1,:email,:tel,:time)");
        $q->bindValue(':name', $name);
        $q->bindValue(':email', $email);
        $q->bindValue(':tel', $telephone);
        $q->execute();
    }
}

?>
