<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: index.php

include ("config/config_local.php");
include ("languages/$default_lang/regional_settings.php");
if (!$game_installed)
{
?>
   <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
   <html>
	<head>
	 <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $local_charset; ?>">
	 <meta http-equiv="Pragma" content="no-cache">
	 <link rel="stylesheet" href="templates/default/style.css" type="text/css">
	 <title>Main Login for Rogue Assault Traders.</title>
	 <body bgcolor="#000000" text="#ff0000" link="#00ff00">
	</head>
<?php
	echo "Click <a href=\"install.php\">here</a>.";
	echo "<meta http-equiv=\"refresh\" content=\"0;url=install.php\">";
	die();
}

include ("config/config.php");

if (empty($langdir))
{
	$langdir = $default_lang;
}

$no_body = 1;
$title = $game_name . " - " . $game_version;

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("header.php");
include ("templates/".$templatename."/skin_config.inc");
include ("languages/$langdir/lang_login.inc");
include ("languages/$langdir/lang_new.inc");

$smarty->assign("templatename", $templatename);

$login_drop_down = '';

// Get the languages from the DB.
$lang_query = $db->Execute("SELECT name, value from $dbtables[languages]");
db_op_result($lang_query,__LINE__,__FILE__);
$i = 0;
while (!$lang_query->EOF && $lang_query)  
{
	$row = $lang_query->fields;
	$avail_lang[$i]['name'] = $row['name'];
	$avail_lang[$i]['value'] = $row['value'];
	$i++;
	$lang_query->MoveNext();
}

$maxval = count($avail_lang);

for ($i=0; $i<$maxval; $i++)
{
	if ($avail_lang[$i]['value'] == $langdir)
	{
		$selected = " selected";
	}
	else
	{
		$selected = "";
	}

	$login_drop_down = $login_drop_down . "<option value=" . $avail_lang[$i]['value'] . "$selected>" . $avail_lang[$i]['name'] . "</option>\n";
}

if(strstr($scheduled_reset, "0000-00-00"))
	$reset = $l_login_reset1;
else $reset = $l_login_reset2 . date($local_date_short_format, strtotime($scheduled_reset));

$smarty->assign("scheduled_reset", $reset);
$smarty->assign("version", $game_version);
$smarty->assign("game_name", $game_name);
$smarty->assign("background_image", $background_image);
$smarty->assign("main_site", $main_site);
$smarty->assign("login_drop_down",$login_drop_down);
$smarty->assign("l_new_pname", $l_new_pname);
$smarty->assign("l_login_pw", $l_login_pw);
$smarty->assign("character_name", $character_name);
$smarty->assign("password", $password);
$smarty->assign("l_login_forgot_pw", $l_login_forgot_pw);
$smarty->assign("l_login_chooseres", $l_login_chooseres);
$smarty->assign("l_login_emailus", $l_login_emailus);
$smarty->assign("admin_mail", $admin_mail);
$smarty->assign("l_login_prbs", $l_login_prbs);
$smarty->assign("l_login_newp", $l_login_newp);
$smarty->assign("l_login_title", $l_login_title);
$smarty->assign("link_forums", $link_forums);
$smarty->assign("l_faq", $l_faq);
$smarty->assign("l_forums", $l_forums);
$smarty->assign("l_rankings", $l_rankings);
$smarty->assign("l_login_settings", $l_login_settings);
$smarty->assign("avail_lang", $avail_lang);
$smarty->assign("login_language_change", $l_login_change);
$smarty->assign("maxlen_password", $maxlen_password);
$smarty->assign("serverlist", $aatrade_server_list_url);

// Admin News
//$debug_query = $db->Execute("SELECT * FROM $dbtables[adminnews] ORDER BY an_id DESC");
//db_op_result($debug_query,__LINE__,__FILE__);
//$row = $debug_query->fields;
//$adminnews = $row['an_text'];

//$smarty->assign("adminnews", $adminnews);

if ((!isset($serverlisturlcheck)) || ($serverlisturlcheck == ''))
{
	$serverlisturlcheck = '';
}else{
	$urlcheck = "url=".rawurlencode($serverlisturlcheck);
}

if ((!isset($serverlistnamecheck)) || ($serverlistnamecheck == ''))
{
	$serverlistnamecheck = '';
}else{
	$namecheck = "name=".rawurlencode($serverlistnamecheck);
}

if($serverlistnamecheck != '' or $serverlisturlcheck != ''){
	$where = "?";
	if($serverlistnamecheck == ''){
		$where .= $urlcheck;
	}else{
		$where .= $namecheck;
		if($serverlisturlcheck != '')
			$where .= "&". $urlcheck;
	}
}else{
	$where = "";
}

if($showserverlist == 1){
	$fp=@fopen("http://aatraders.com/get_server_list.php".$where, "r");
	if($fp){
		$servercount = 0;
		while(!@feof($fp)){
			$lines = @fgets($fp, 4096);
			$servers = explode("|", $lines);
			if($servers[0] != "" and $servers[1] != $_SERVER['HTTP_HOST'] . $gamepath){
				if(strstr($servers[6], "0000-00-00 00:00:00"))
					$reset = $l_login_reset1;
				else $reset = $l_login_reset3 . date($local_date_short_format, strtotime($servers[6]));

				$serverurl[$servercount] = $servers[1];
				$servername[$servercount] = $servers[0];
				$serversectors[$servercount] = $servers[2];
				$serverplayers[$servercount] = $servers[3];
				$servertop[$servercount] = $servers[4] . ": " . NUMBER($servers[5]);
				$serverreset[$servercount] = $reset;
				$servercount++;
			}
		}
		@fclose($fp);
	}
}

$smarty->assign("servercount", $servercount);
$smarty->assign("serverurl", $serverurl);
$smarty->assign("servername", $servername);
$smarty->assign("serversectors", $serversectors);
$smarty->assign("serverplayers", $serverplayers);
$smarty->assign("servertop", $servertop);
$smarty->assign("serverreset", $serverreset);
$smarty->assign("showserverlist", $showserverlist);

$newscount = 0;
$res = $db->Execute("SELECT * FROM $dbtables[adminnews] ORDER BY an_id desc");

if($res->EOF)
{
	$smarty->assign("newscount", 0);
}
else
{
	while (!$res->EOF) 
	{
		$row = $res->fields;
		$newsdata[$newscount] = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", str_replace("\r", "", str_replace("\n", "", $row[an_text])));
		$newscount++;
		$res->MoveNext();
	}
	$smarty->assign("newscount", $newscount);
	$smarty->assign("newsdata", $newsdata);
	$smarty->assign("l_login_notice", $l_login_notice);
}

$smarty->display($default_template."index.tpl");

include ("footer.php");

?>
