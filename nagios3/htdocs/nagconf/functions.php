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

?>
