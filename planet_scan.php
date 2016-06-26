<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: report.php

include ("config/config.php");
include ("languages/$langdir/lang_scan.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_ports.inc");

if (isset($_GET['planet_id']))
{
	$planet_id = $_GET['planet_id'];
}

$title = $l_planet_scn_link . " " . $l_dig_planetname;

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

function base_string($base)
{
	global $l_planet_based;
	return ($base=='Y') ? $l_planet_based : "N";
}
//-------------------------------------------------------------------------------------------------

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
	$planetinfo=$result3->fields;

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

/* check to ensure target is in the same sector as player */
if ($planetinfo['sector_id'] != $shipinfo['sector_id'])
{
	$smarty->assign("error_msg", $l_planet_noscan);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."scandie.tpl");
	include ("footer.php");
	die();
}

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_plant_scn_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planetdie.tpl");
	include ("footer.php");
	die();
}

// determine per cent chance of success in scanning target ship - 
// Based on player's sensors and opponent's cloak

planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_SCANNED);

/* determine per cent chance of success in scanning target ship - based on player's sensors and opponent's planet's cloak */
$success = (10 - $planetinfo['cloak'] / 2 + $shipinfo['sensors']) * 5;
if ($success < 5)
{
	$success = 5;
}
if ($success > 95)
{
	$success = 95;
}

$roll = mt_rand(1, 100);
if ($roll > $success)
{
	// if scan fails - inform both player and target.
	playerlog($planetinfo['owner'], LOG_PLANET_SCAN_FAIL, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
	$smarty->assign("error_msg", $l_planet_noscan);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planetdie.tpl");
	include ("footer.php");
	die();
}

// Player will get a Federation Bounty on themselves if they attack a player who's score is less than 
// bounty_ratio of themselves. If the target has a Federation Bounty, they can attack without attracting a bounty on themselves.

$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
$ownerinfo = $result3->fields;

$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 0);

if($isfedbounty > 0)
{
	$fedcheckbounty = $l_by_fedbounty;
}
else
{
	$fedcheckbounty = $l_by_nofedbounty;
}

if (empty($planetinfo['name']))
   $planetinfo['name'] = $l_unnamed;

$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
$ownerinfo = $result3->fields;

$targetname = $planetinfo['name'];
$targetinfoname = $ownerinfo['character_name'];

$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_computer = (mt_rand(1, 100) < $success) ? round($planetinfo['computer'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_sensors = (mt_rand(1, 100) < $success) ? round($planetinfo['sensors'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_beams = (mt_rand(1, 100) < $success) ? round($planetinfo['beams'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_torp_launchers = (mt_rand(1, 100) < $success) ? round($planetinfo['torp_launchers'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_armour = (mt_rand(1, 100) < $success) ? round($planetinfo['armour'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_shields = (mt_rand(1, 100) < $success) ? round($planetinfo['shields'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_cloak = (mt_rand(1, 100) < $success) ? round($planetinfo['cloak'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_jammer = (mt_rand(1, 100) < $success) ? round($planetinfo['jammer'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_armour_pts = (mt_rand(1, 100) < $success) ? round($planetinfo['armour_pts'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_fighters = (mt_rand(1, 100) < $success) ? round($planetinfo['fighters'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_torps = (mt_rand(1, 100) < $success) ? round($planetinfo['torps'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_credits = (mt_rand(1, 100) < $success) ? round($planetinfo['credits'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_energy = (mt_rand(1, 100) < $success) ? round($planetinfo['energy'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_colonists = (mt_rand(1, 100) < $success) ? round($planetinfo['colonists'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_ore = (mt_rand(1, 100) < $success) ? round($planetinfo['ore'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_organics = (mt_rand(1, 100) < $success) ? round($planetinfo['organics'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
$sc_error_plus=100;
if ($sc_error < 100){
	$sc_error_plus=115;
}
$sc_ship_goods = (mt_rand(1, 100) < $success) ? round($planetinfo['goods'] * (mt_rand($sc_error , $sc_error_plus) / 100)) : 0;
$sc_base = (mt_rand(1, 100) < $success) ? base_string($planetinfo['base']) : "N";

playerlog($planetinfo['owner'], LOG_PLANET_SCAN, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");

$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
db_op_result($debug_query,__LINE__,__FILE__);

$average_stats = (($sc_cloak + $sc_sensors + $sc_computer + $sc_armour + $sc_shields + $sc_beams + $sc_torp_launchers + $sc_jammer ) / 8 );

$computer_normal_bars = MakeBars($sc_computer, 54, "normal");
$sensors_normal_bars = MakeBars($sc_sensors, 54, "normal");
$armour_normal_bars = MakeBars($sc_armour, 54, "normal");
$shields_normal_bars = MakeBars($sc_shields, 54, "normal");
$beams_normal_bars = MakeBars($sc_beams, 54, "normal");
$torp_launchers_normal_bars = MakeBars($sc_torp_launchers, 54, "normal");
$cloak_normal_bars = MakeBars($sc_cloak, 54, "normal");
$jammer_normal_bars = MakeBars($sc_jammer, 54, "normal");
$average_bars = MakeBars($average_stats, 54, "normal");

$planetavg = $planetinfo['computer'] + $planetinfo['sensors'] + $planetinfo['beams'] + $planetinfo['torp_launchers'] + $planetinfo['shields'] + $planetinfo['cloak'] + ($planetinfo['colonists'] / ($colonist_limit / 54));
$planetavg = round($planetavg/37.8); // Divide by (54 levels * 7 categories / 4) to get 1-4.
if ($planetavg > 10)
{
	$planetavg = 10;
}

if ($planetavg < 0)
{
	$planetavg = 0;
}

$planetlevel = $planetavg;

$res = $db->Execute("SELECT $dbtables[ships].*, $dbtables[players].character_name FROM $dbtables[ships] LEFT JOIN " .
				   "$dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE " .
				   "on_planet = 'Y' and planet_id = $planetinfo[planet_id]");

$shipcount = 0;
while (!$res->EOF)
{
	$row = $res->fields;
	$success = SCAN_SUCCESS($shipinfo['sensors'], $row['cloak']);
	if ($success < 5)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);

	if ($roll < $success)
	{
		$playeronplanet[$shipcount] = $row['character_name'];
		$shipcount++;
	}
	$res->MoveNext();
}

$smarty->assign("l_planet_noone", ucfirst($l_planet_noone));
$smarty->assign("l_none", $l_none);
$smarty->assign("playeronplanet", $playeronplanet);
$smarty->assign("l_planet_ison", $l_planet_ison);
$smarty->assign("shipcount", $shipcount);
$smarty->assign("sc_base", $sc_base);
$smarty->assign("planettype", $planettypes[$planetlevel]);

$smarty->assign("scanbounty", $scanbounty);
$smarty->assign("scanfedbounty", $scanfedbounty);
$smarty->assign("fedcheckbounty", $fedcheckbounty);
$smarty->assign("l_scan_ron", $l_scan_ron);
$smarty->assign("targetinfoname", $targetinfoname);

$smarty->assign("avatar", $ownerinfo['avatar']);
$smarty->assign("planetname", $planetinfo['name']);
$smarty->assign("l_planetary_defense_levels", $l_planetary_defense_levels);

$smarty->assign("l_planetary_computer", $l_planetary_computer);
$smarty->assign("shipinfo_computer_normal", $sc_computer);
$smarty->assign("classinfo_maxcomputer", 54);
$smarty->assign("computer_normal_bars", $computer_normal_bars);

$smarty->assign("l_planetary_sensors", $l_planetary_sensors);
$smarty->assign("shipinfo_sensors_normal", $sc_sensors);
$smarty->assign("classinfo_maxsensors", 54);
$smarty->assign("sensors_normal_bars", $sensors_normal_bars);

$smarty->assign("l_avg_stats", $l_shipavg);
$smarty->assign("average_stats", NUMBER($average_stats,1));
$smarty->assign("average_bars", $average_bars);

$smarty->assign("l_planetary_armour", $l_planetary_armour);
$smarty->assign("shipinfo_armour_normal", $sc_armour);
$smarty->assign("classinfo_maxarmour", 54);
$smarty->assign("armour_normal_bars", $armour_normal_bars);

$smarty->assign("l_planetary_shields", $l_planetary_shields);
$smarty->assign("shipinfo_shields_normal", $sc_shields);
$smarty->assign("classinfo_maxshields", 54);
$smarty->assign("shields_normal_bars", $shields_normal_bars);

$smarty->assign("l_planetary_beams", $l_planetary_beams);
$smarty->assign("shipinfo_beams_normal", $sc_beams);
$smarty->assign("classinfo_maxbeams", 54);
$smarty->assign("beams_normal_bars", $beams_normal_bars);

$smarty->assign("l_planetary_torp_launch", $l_planetary_torp_launch);
$smarty->assign("shipinfo_torp_launchers_normal", $sc_torp_launchers);
$smarty->assign("classinfo_maxtorp_launchers", 54);
$smarty->assign("torp_launchers_normal_bars", $torp_launchers_normal_bars);

$smarty->assign("l_planetary_cloak", $l_planetary_cloak);
$smarty->assign("shipinfo_cloak_normal", $sc_cloak);
$smarty->assign("classinfo_maxcloak", 54);
$smarty->assign("cloak_normal_bars", $cloak_normal_bars);

$smarty->assign("l_planetary_jammer", $l_planetary_jammer);
$smarty->assign("shipinfo_jammer_normal", $sc_jammer);
$smarty->assign("classinfo_maxjammer", 54);
$smarty->assign("jammer_normal_bars", $jammer_normal_bars);

$smarty->assign("l_holds", $l_holds);
$smarty->assign("l_arm_weap", $l_arm_weap);
$smarty->assign("l_devices", $l_devices);
$smarty->assign("l_total_cargo", $l_total_cargo);
$smarty->assign("holds_used", NUMBER($holds_used));
$smarty->assign("l_ore", $l_ore);
$smarty->assign("shipinfo_ore", NUMBER($sc_ship_ore));
$smarty->assign("l_organics", $l_organics);
$smarty->assign("shipinfo_organics", NUMBER($sc_ship_organics));
$smarty->assign("l_goods", $l_goods);
$smarty->assign("shipinfo_goods", NUMBER($sc_ship_goods));
$smarty->assign("l_colonists", $l_colonists);
$smarty->assign("shipinfo_colonists", NUMBER($sc_ship_colonists));
$smarty->assign("l_energy", $l_energy);
$smarty->assign("shipinfo_energy", NUMBER($sc_ship_energy));
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("shipinfo_fighters", NUMBER($sc_ship_fighters));
$smarty->assign("l_torps", $l_torps);
$smarty->assign("shipinfo_torps", NUMBER($sc_torps));
$smarty->assign("l_armourpts", $l_armourpts);
$smarty->assign("shipinfo_armour_pts", NUMBER($sc_armour_pts));
$smarty->assign("l_credits", $l_credits);
$smarty->assign("shipinfo_credits", NUMBER($sc_credits));
$smarty->assign("title", $title);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("templatename", $templatename);

$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
$smarty->assign("allow_ibank", $allow_ibank);
$smarty->assign("l_ifyouneedplan", $l_ifyouneedplan);
$smarty->assign("planet_id", $planetinfo['planet_id']);
$smarty->assign("l_igb_term", $l_igb_term);
$smarty->assign("l_by_placebounty", $l_by_placebounty);

$smarty->display($templatename."planet_scan.tpl");

include ("footer.php");

?>

