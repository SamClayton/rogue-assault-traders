<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: ship.php

include ("config/config.php");
include ("languages/$langdir/lang_ship.inc");
include ("languages/$langdir/lang_planets.inc");

$title = $l_ship_title;

if ((!isset($ship_id)) || ($ship_id == ''))
{
	$ship_id = '';
}

if (checklogin() or $tournament_setup_access == 1)
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

$debug_query = $db->Execute("SELECT name, character_name, sector_id FROM $dbtables[ships] " .
							"LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id " .
							"WHERE ship_id=$ship_id");
db_op_result($debug_query,__LINE__,__FILE__);

$otherplayer = $debug_query->fields;

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$smarty->assign("title", $title);
$smarty->assign("ship_id", $ship_id);
$smarty->assign("gamepath", $gamepath);
$smarty->assign("player_id", $player_id);
$smarty->assign("l_planet_att_link", $l_planet_att_link);
$smarty->assign("l_planet_scn_link", $l_planet_scn_link);
$smarty->assign("l_ship_perform", $l_ship_perform);
$smarty->assign("l_ship_owned", $l_ship_owned);
$smarty->assign("l_send_msg", $l_send_msg);
$smarty->assign("l_ship_youc", $l_ship_youc);
$smarty->assign("l_ship_the", $l_ship_the);
$smarty->assign("l_ship_nolonger", $l_ship_nolonger);
$smarty->assign("otherplayer_character_name", $otherplayer['character_name']);
$smarty->assign("otherplayer_name", $otherplayer['name']);
$smarty->assign("otherplayer_sector_id", $otherplayer['sector_id']);
$smarty->assign("shipinfo_sector_id", $shipinfo['sector_id']);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."ship.tpl");

include ("footer.php");

?>
