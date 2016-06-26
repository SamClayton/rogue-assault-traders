<?php
// Automatically created configuration file. Do not change!


$pos = strpos($_SERVER['PHP_SELF'], "/config_local.php");
if ($pos !== false)
{
  echo "You can not access this file directly!";
  die();
}
$db_persistent = "1";
$db_type = "mysql";
$dbname = "test";
$db_mysql_type = "default";
$dbuname = "root";
$dbpass = "";
$dbhost = "localhost";
$dbport = "";
$db_prefix = "aatrade_";
$gameurl = "192.168.0.3";
$gamepath = "/aatrade";
$gameroot = "c:/unzipped/apache/htdocs/aatrade/";
$ADOdbpath = "c:/unzipped/apache/htdocs/aatrade/backends/adodb";
$link_forums = "http://aatraders.com/phpBB2/";
$admin_mail = "gvar_00000@yahoo.com";
$adminpass = "77017701";
$sched_ticks = "5";
$db_mysql_valid = "no";
$ADODB_SESSION_DRIVER  = $db_type;
$ADODB_SESSION_CONNECT = $dbhost;
$ADODB_SESSION_USER	= $dbuname;
$ADODB_SESSION_PWD	 = $dbpass;
$ADODB_SESSION_DB	  = $dbname;
$ADODB_SESSION_TBL	 = "aatrade_sessions";

$game_installed = "1";
$default_lang = "english"; // This is needed for the codepage stuff. 
?>
