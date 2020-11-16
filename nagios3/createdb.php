<?php

$db = new SQLite3('/etc/nagios3/sqlite.db');

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS hosts (
  name text primary key,
  hgid integer not null default 1,
  address text not null,
  alias text
);
CREATE TABLE IF NOT EXISTS checkcommands (
  id integer primary key,
  name text not null,
  command_line text not null,
  argnr integer not null,
  arg1_descr text,
  arg2_descr text,
  arg3_descr text,
  descr
);
CREATE TABLE IF NOT EXISTS services (
  check_command text,
  hostname text,
  hgid integer not null default 1,
  descr text not null,
  argnr integer not null default 0,
  arg1 text,
  arg2 text,
  arg3 text,
  ccid interger not null,
  FOREIGN KEY (ccid) REFERENCES checkcommands(id),
  PRIMARY KEY (check_command,hostname)
);
CREATE TABLE IF NOT EXISTS contacts (
  name text primary key,
  email text not null,
  telephone text not null default '1-WHITEHOUSE'
);
CREATE TABLE IF NOT EXISTS etc (
  key text primary key,
  value text not null
);
SQL;

echo "Creating database\n";
$db->exec($sql);

$sql =<<<'SQL'
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_tcp_port', '$USER1$/check_tcp -H $HOSTADDRESS$ -p $ARG1$', 1, 'port to check', NULL, NULL, 'Check TCP port');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_udp_port', '$USER1$/check_udp -H $HOSTADDRESS$ -p $ARG1$ -s $ARG2$ -e $ARG3$', 3, 'port to check', 'string to send', 'string to expect', 'Check UDP port');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_http', '$USER1$/check_http -H $HOSTADDRESS$ -A "check_http monitoring.hacktor.com"', 0, NULL, NULL, NULL, 'Check HTTP');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_http_port', '$USER1$/check_http -H $HOSTADDRESS$ -p $ARG1$ -A "check_http monitoring.hacktor.com"', 1, 'port number to check, eg 8080', NULL, NULL, 'Check HTTP port');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_https', '$USER1$/check_http -H $HOSTADDRESS$ -S -A "check_http monitoring.hacktor.com"', 0, NULL, NULL, NULL, 'Check HTTPS');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_https_port', '$USER1$/check_http -H $HOSTADDRESS$ -p $ARG1$ -S -A "check_http monitoring.hacktor.com"', 1, 'port number to check, eg 8443', NULL, NULL, 'Check HTTPS port');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_https_auth', '$USER1$/check_http -H $HOSTADDRESS$ -S -A "check_http monitoring.hacktor.com" -a $ARG1$:$ARG2$', 2, 'Username', 'Password', '', 'Check HTTPS with Authentication');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_certificate_https', '$USER1$/check_ssl_certificate -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check Certificate HTTPS');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_dns', '$USER1$/check_dns -H localhost -s $HOSTADDRESS$ -w 5 -c 15', 0, NULL, NULL, NULL, 'Check DNS');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_imap', '$USER1$/check_imap -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check IMAP');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_imaps', '$USER1$/check_imap -H $HOSTADDRESS$ -S -p 993', 0, NULL, NULL, NULL, 'Check IMAPS');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_certificate_imaps', '$USER1$/check_ssl_certificate -H $HOSTADDRESS$ -p 993', 0, NULL, NULL, NULL, 'Check Certificate IMAPS');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_ping', '$USER1$/check_ping -H $HOSTADDRESS$ -w 100.0,20% -c 500.0,60%', 0, NULL, NULL, NULL, 'Check PING');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_pop', '$USER1$/check_pop -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check POP3');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_smtp', '$USER1$/check_smtp -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check SMTP');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_ssh', '$USER1$/check_ssh -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check SSH');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_ssh_port', '$USER1$/check_ssh -H $HOSTADDRESS$ -p $ARG1$', 1, 'port to check', NULL, NULL, 'Check SSH port');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_ftp', '$USER1$/check_ftp -H $HOSTADDRESS$', 0, NULL, NULL, NULL, 'Check FTP');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_nt_uptime', '$USER1$/check_nt -H $HOSTADDRESS$ -p 12489 -v UPTIME', 0, NULL, NULL, NULL, 'Check Windows agent UPTIME');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_nt_cpu', '$USER1$/check_nt -H $HOSTADDRESS$ -p 12489 -v CPULOAD -l 5,80,90', 0, NULL, NULL, NULL, 'Check Windows agent CPULOAD');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_nt_mem', '$USER1$/check_nt -H $HOSTADDRESS$ -p 12489 -v MEMUSE -w 80 -c 90', 0, NULL, NULL, NULL, 'Check Windows agent MEMUSE');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_nt_disk', '$USER1$/check_nt -H $HOSTADDRESS$ -p 12489 -v USEDDISKSPACE -w 80 -c 90', 0, NULL, NULL, NULL, 'Check Windows agent USEDDISKSPACE');
INSERT INTO checkcommands (name, command_line, argnr, arg1_descr, arg2_descr, arg3_descr, descr) VALUES ('check_nt', '$USER1$/check_nt -H $HOSTADDRESS$ -p 12489 -v "$ARG1$"', 1, "Name of nsclient service to check", NULL, NULL, 'Check by Windows agent');
SQL;

echo "Populating checkcommands table\n";
$db->exec($sql);

$sql =<<<'SQL'
INSERT INTO etc (key,value) VALUES ('api_key','');
INSERT INTO etc (key,value) VALUES ('chat_id','');
INSERT INTO etc (key,value) VALUES ('max_check_attempts','4');
INSERT INTO etc (key,value) VALUES ('check_interval','2');
INSERT INTO etc (key,value) VALUES ('retry_interval','1');
INSERT INTO etc (key,value) VALUES ('notification_interval','1440');
INSERT INTO etc (key,value) VALUES ('$USER3$','smtp://10.88.0.1:25');
INSERT INTO etc (key,value) VALUES ('$USER4$','monitor@hacktor.com');
INSERT INTO contacts (name,email,telephone) VALUES ('nagiosadmin','devnull@hacktor.com','1-WHITEHOUSE');
SQL;

echo "Setting default values\n";
$db->exec($sql);
include_once('/etc/nagios3/htdocs/nagconf/database.php');
include_once('/etc/nagios3/htdocs/nagconf/functions.php');
populate('objects');
?>
