<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: self-destruct.php

include ("config/config.php");
include ("languages/$langdir/lang_self_destruct.inc");

$title = $l_die_title;

if (checklogin())
{
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if ((!isset($sure)) || ($sure == ''))
{
	$sure = 0;
}

if ($sure == 2)
{
	$logout_link="<a href=logout.php?self_destruct=true>$l_die_please1</a>";
	$l_die_please2=str_replace("[logout_link]",$logout_link,$l_die_please2);

	db_kill_player($playerinfo['player_id'], 0, 0);
	cancel_bounty($playerinfo['player_id']);
	adminlog(LOG_ADMIN_HARAKIRI, "$playerinfo[character_name]|$ip");
	playerlog($playerinfo['player_id'], LOG_HARAKIRI, "$ip");
}

if ($sure != 2)
{
	$smarty->assign("gotomain", $l_global_mmenu);
}

$smarty->assign("sure", $sure);
$smarty->assign("l_die_rusure", $l_die_rusure);
$smarty->assign("l_die_nonono", $l_die_nonono);
$smarty->assign("l_yes", $l_yes);
$smarty->assign("l_die_goodbye", $l_die_goodbye);
$smarty->assign("l_die_check", $l_die_check);
$smarty->assign("l_die_what", $l_die_what);
$smarty->assign("l_die_count", $l_die_count);
$smarty->assign("l_die_vapor", $l_die_vapor);
$smarty->assign("l_die_please1", $l_die_please1);
$smarty->assign("l_die_please2", $l_die_please2);
$smarty->display($templatename."self_destruct.tpl");
include ("footer.php");

?>
