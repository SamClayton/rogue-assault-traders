<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: config.php

if (!empty($_GET)) {
	extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
	extract($HTTP_GET_VARS);
}

if (!empty($_POST)) {
	extract($_POST);
} else if (!empty($HTTP_POST_VARS)) {
	extract($HTTP_POST_VARS);
}

if (!empty($_FILES)) {
	extract($_FILES);
} else if (!empty($HTTP_POST_FILES)) {
	extract($HTTP_POST_FILES);
}

error_reporting (E_ALL ^ E_NOTICE);

// Include the DB config file:
include ("config/config_local.php");

// Define the adodb directory:
define ('ADODB_DIR',"$ADOdbpath");

// Set the adodb session variables to global.
global $HTTP_SESSION_VARS;

// Include adodb:
include ("$ADOdbpath" . "/adodb.inc.php");

// Include adodb session handlers:
include ("$ADOdbpath" . "/session/adodb-cryptsession.php");

// Database tables variables
$dbtables['adminnews'] = "${db_prefix}adminnews";
$dbtables['autoroutes'] = "${db_prefix}autoroutes"; 
$dbtables['bounty'] = "${db_prefix}bounty";
$dbtables['config_values'] = "${db_prefix}config_values";
$dbtables['detect'] = "${db_prefix}detect"; 
$dbtables['dignitary'] = "${db_prefix}dignitary";
$dbtables['email_log'] = "${db_prefix}email_log";
$dbtables['ibank_accounts'] = "${db_prefix}ibank_accounts";
$dbtables['igb_transfers'] = "${db_prefix}igb_transfers";
$dbtables['ip_bans'] = "${db_prefix}ip_bans";
$dbtables['ip_log'] = "${db_prefix}ip_log";
$dbtables['kabal'] = "${db_prefix}kabal";
$dbtables['languages'] = "${db_prefix}languages"; 
$dbtables['links'] = "${db_prefix}links";
$dbtables['logs'] = "${db_prefix}logs";
$dbtables['messages'] = "${db_prefix}messages";
$dbtables['movement_log'] = "${db_prefix}movement_log";
$dbtables['news'] = "${db_prefix}news";
$dbtables['planet_log'] = "${db_prefix}planet_log";
$dbtables['planets'] = "${db_prefix}planets";
$dbtables['players'] = "${db_prefix}players";
$dbtables['player_team_history'] = "${db_prefix}player_team_history";
$dbtables['probe'] = "${db_prefix}probe";  
$dbtables['message_block'] = "${db_prefix}message_block";  
$dbtables['scan_log'] = "${db_prefix}scan_log";
$dbtables['scheduler'] = "${db_prefix}scheduler";
$dbtables['sector_defence'] = "${db_prefix}sector_defence";
$dbtables['sessions'] = "${db_prefix}sessions";
$dbtables['ship_types'] = "${db_prefix}ship_types";
$dbtables['ships'] = "${db_prefix}ships";
$dbtables['spies'] = "${db_prefix}spies";
$dbtables['teams'] = "${db_prefix}teams";
$dbtables['traderoutes'] = "${db_prefix}traderoutes";
$dbtables['universe'] = "${db_prefix}universe";
$dbtables['zones'] = "${db_prefix}zones";
$dbtables['shoutbox'] = "${db_prefix}shoutbox";
$dbtables['forums'] = "${db_prefix}forums";
$dbtables['fplayers'] = "${db_prefix}fplayers";
$dbtables['posts'] = "${db_prefix}posts";
$dbtables['posts_text'] = "${db_prefix}posts_text";
$dbtables['topics'] = "${db_prefix}topics";
$dbtables['autotrades'] = "${db_prefix}autotrades";
$dbtables['presets'] = "${db_prefix}presets";
$dbtables['debris'] = "${db_prefix}debris";
$dbtables['wordcensor'] = "${db_prefix}wordcensor";
$dbtables['sector_notes'] = "${db_prefix}sector_notes";

//$dbtables['ship_mounts'] = "${db_prefix}ship_mounts";
//$dbtables['planet_research'] = "${db_prefix}planet_research";
//$dbtables['planet_research_built'] = "${db_prefix}planet_research_built";
//$dbtables['research_items'] = "${db_prefix}research_items";

$dbtables['casino_forums'] = "${db_prefix}casino_forums";
$dbtables['casino_posts'] = "${db_prefix}casino_posts";
$dbtables['casino_posts_text'] = "${db_prefix}casino_posts_text";
$dbtables['casino_topics'] = "${db_prefix}casino_topics";
$dbtables['navmap'] = "${db_prefix}navmap";

// connect to database - and if we can't stop right there

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
if (!empty($dbport))
{
	$dbhost.= ":$dbport";
}

$db = ADONewConnection("$db_type");
$db->debug=0;
if ($db_persistent == 1)
{
	$result = $db->PConnect("$dbhost", "$dbuname", "$dbpass", "$dbname");
}
else
{
	$result = $db->Connect("$dbhost", "$dbuname", "$dbpass", "$dbname");
}

if (!$result)
{
	die ("Unable to connect to the database");
}

if($db_type == "mysql")
{
	$debug_query = $db->Execute("SET interactive_timeout=900");
}

// Get the config_values from the DB - silently.
$silent = 1;
$debug_query = $db->Execute("SELECT name, value FROM $dbtables[config_values]");

while (!$debug_query->EOF && $debug_query)  
{  
    $row = $debug_query->fields;  
    $$row['name'] = $row['value'];  
    $debug_query->MoveNext();  
}

?>
