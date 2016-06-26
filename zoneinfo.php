<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: zoneinfo.php

include ("config/config.php");
include ("languages/$langdir/lang_zoneinfo.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_attack.inc");

$title = $l_zi_title;

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

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if ($zoneinfo['zone_id'] < 5)
{
	$zoneinfo['zone_name'] = $l_zname[$zoneinfo['zone_id']];
}

if ($zoneinfo['zone_id'] == '1')
{
	$ownername = $l_zi_nobody;
}
elseif ($zoneinfo['zone_id'] == '2')
{
	$ownername = $l_zi_feds;
}
elseif ($zoneinfo['zone_id'] == '3')
{
	$ownername = $l_zi_traders;
}
elseif ($zoneinfo['zone_id'] == '4')
{
	$ownername = $l_zi_war;
}
else
{
	if ($zoneinfo['team_zone'] == 'N')
	{
		$result = $db->Execute("SELECT player_id, character_name FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
		$ownerinfo = $result->fields;
		$ownername = $ownerinfo['character_name'];
	}
	else
	{
		$result = $db->Execute("SELECT team_name, creator, id FROM $dbtables[teams] WHERE id=$zoneinfo[owner]");
		$ownerinfo = $result->fields;
		$ownername = $ownerinfo['team_name'];
	}
}

if ($zoneinfo['allow_beacon'] == 'Y'){
	$beacon = $l_zi_allow;
}else{
	if ($zoneinfo['allow_beacon'] == 'N'){
		$beacon = $l_zi_notallow;
	}else{
		$beacon = $l_zi_limit;
	}
}

if ($zoneinfo['allow_attack'] == 'Y')
{	
	$attack = $l_zi_allow;
}
else
{
	$attack = $l_zi_notallow;
}

if ($zoneinfo['allow_defenses'] == 'Y')
{
	$defense = $l_zi_allow;
}
elseif ($zoneinfo['allow_defenses'] == 'N')
{
	$defense = $l_zi_notallow;
}
else
{
	$defense = $l_zi_limit;
}

if ($zoneinfo['allow_warpedit'] == 'Y')
{
	$warpedit = $l_zi_allow;
}
elseif ($zoneinfo['allow_warpedit'] == 'N')
{
	$warpedit = $l_zi_notallow;
}
else
{
	$warpedit = $l_zi_limit;
}

if ($zoneinfo['allow_planet'] == 'Y')
{
	$planet = $l_zi_allow;
}
elseif ($zoneinfo['allow_planet'] == 'N')
{
	$planet = $l_zi_notallow;
}
else
{
	$planet = $l_zi_limit;
}

if ($zoneinfo['allow_trade'] == 'Y')
{
	$trade = $l_zi_allow;
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
	$trade = $l_zi_notallow;
}
else
{
	$trade = $l_zi_limit;
}

if ($zoneinfo['max_hull'] == 0)
{
	$hull = $l_zi_ul;
}
else
{
	$hull = $zoneinfo['max_hull'];
}

if (($zoneinfo['team_zone'] == 'N' && $zoneinfo['owner'] == $playerinfo['player_id']) || ($zoneinfo['team_zone'] == 'Y' && $zoneinfo[owner] == $playerinfo[team] && $playerinfo[player_id] == $ownerinfo[creator]))
{
	$zoneowner = $zoneinfo['zone_id'];
}else{
	$zoneowner = -1;
}

$smarty->assign("title", $title);
$smarty->assign("l_zi_control", $l_zi_control);
$smarty->assign("zoneowner", $zoneowner);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("l_zi_tochange", $l_zi_tochange);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("zone_name", $zoneinfo['zone_name']);
$smarty->assign("l_zi_owner", $l_zi_owner);
$smarty->assign("ownername", $ownername);
$smarty->assign("l_beacons", $l_beacons);
$smarty->assign("beacon", $beacon);
$smarty->assign("l_att_att", $l_att_att);
$smarty->assign("attack", $attack);
$smarty->assign("l_md_title", $l_md_title);
$smarty->assign("defense", $defense);
$smarty->assign("l_warpedit", $l_warpedit);
$smarty->assign("warpedit", $warpedit);
$smarty->assign("l_planets", $l_planets);
$smarty->assign("planet", $planet);
$smarty->assign("l_title_port", $l_title_port);
$smarty->assign("trade", $trade);
$smarty->assign("l_zi_maxhull", $l_zi_maxhull);
$smarty->assign("hull", $hull);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."zoneinfo.tpl");

include ("footer.php");

?>
