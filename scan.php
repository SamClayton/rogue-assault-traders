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

if ((!isset($player_id)) || ($player_id == ''))
{
	$player_id = '';
}

if ((!isset($ship_id)) || ($ship_id == ''))
{
	$ship_id = '';
}

$title = $l_scan_title;

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

$debug_query = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id='$player_id'");
db_op_result($debug_query,__LINE__,__FILE__);
$targetinfo = $debug_query->fields;

$debug_query = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
db_op_result($debug_query,__LINE__,__FILE__);
$targetshipinfo = $debug_query->fields;

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

/* check to ensure target is in the same sector as player */
if ($targetshipinfo['sector_id'] != $shipinfo['sector_id'])
{
	$smarty->assign("error_msg", $l_planet_noscan);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."scandie.tpl");
	include ("footer.php");
	die();
}

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_scan_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."scandie.tpl");
	include ("footer.php");
	die();
}


// determine per cent chance of success in scanning target ship - 
// Based on player's sensors and opponent's cloak

$success = SCAN_SUCCESS($shipinfo['sensors'], $targetshipinfo['cloak']);
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
	playerlog($targetinfo['player_id'], LOG_SHIP_SCAN_FAIL, "$playerinfo[character_name]");
	$smarty->assign("error_msg", $l_planet_noscan);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."scandie.tpl");
	include ("footer.php");
	die();
}

// if scan succeeds, show results and inform target.
// cramble results by scan error factor.
// Get total bounty on this player, if any
$btyamount = 0;
$debug_query = $db->Execute("SELECT SUM(amount) AS btytotal FROM $dbtables[bounty] WHERE bounty_on = $targetinfo[player_id]");
db_op_result($debug_query,__LINE__,__FILE__);

$scanbounty = 1;
$scanfedbounty = 1;

$isfedbounty = ship_bounty_check($playerinfo, $shipinfo['sector_id'], $targetinfo, 0);

if($isfedbounty > 0)
{
	$fedcheckbounty = $l_by_fedbounty;
	$btyamount = NUMBER($isfedbounty);
	$l_scan_bounty = str_replace("[amount]",$btyamount,$l_scan_bounty);
	$scanbounty = $l_scan_bounty;
}
else
{
	$fedcheckbounty = $l_by_nofedbounty;
}

$targetname = $targetshipinfo['name'];
$targetinfoname = $targetinfo['character_name'];

$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_hull = (mt_rand(1, 100) < $success) ? round($targetshipinfo['hull'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_engines = (mt_rand(1, 100) < $success) ? round($targetshipinfo['engines'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_power = (mt_rand(1, 100) < $success) ? round($targetshipinfo['power'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_computer = (mt_rand(1, 100) < $success) ? round($targetshipinfo['computer'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_sensors = (mt_rand(1, 100) < $success) ? round($targetshipinfo['sensors'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_beams = (mt_rand(1, 100) < $success) ? round($targetshipinfo['beams'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_torp_launchers = (mt_rand(1, 100) < $success) ? round($targetshipinfo['torp_launchers'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_armour = (mt_rand(1, 100) < $success) ? round($targetshipinfo['armour'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_shields = (mt_rand(1, 100) < $success) ? round($targetshipinfo['shields'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_cloak = (mt_rand(1, 100) < $success) ? round($targetshipinfo['cloak'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ecm = (mt_rand(1, 100) < $success) ? round($targetshipinfo['ecm'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_armour_pts = (mt_rand(1, 100) < $success) ? round($targetshipinfo['armour_pts'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_fighters = (mt_rand(1, 100) < $success) ? round($targetshipinfo['fighters'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_torps = (mt_rand(1, 100) < $success) ? round($targetshipinfo['torps'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_credits = (mt_rand(1, 100) < $success) ? round($targetshipinfo['credits'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_energy = (mt_rand(1, 100) < $success) ? round($targetshipinfo['energy'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_dev_minedeflector = (mt_rand(1, 100) < $success) ? round($targetshipinfo['dev_minedeflector'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_dev_emerwarp = (mt_rand(1, 100) < $success) ? round($targetshipinfo['dev_emerwarp'] * $sc_error / 100) : 0;
$sc_escape = (mt_rand(1, 100) < $success) ? $targetshipinfo['dev_escapepod'] : "N";
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_colonists = (mt_rand(1, 100) < $success) ? round($targetshipinfo['colonists'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_ore = (mt_rand(1, 100) < $success) ? round($targetshipinfo['ore'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_organics = (mt_rand(1, 100) < $success) ? round($targetshipinfo['organics'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_ship_goods = (mt_rand(1, 100) < $success) ? round($targetshipinfo['goods'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_dev_warpedit = (mt_rand(1, 100) < $success) ? round($targetshipinfo['dev_warpedit'] * $sc_error / 100) : 0;
$sc_error = SCAN_ERROR($shipinfo['sensors'], $targetshipinfo['cloak']);
$sc_dev_genesis = (mt_rand(1, 100) < $success) ? round($targetshipinfo['dev_genesis'] * $sc_error / 100) : 0;
$sc_scoop = (mt_rand(1, 100) < $success) ? $targetshipinfo['dev_fuelscoop'] : "N";

playerlog($targetinfo['player_id'], LOG_SHIP_SCAN, "$playerinfo[character_name]");

$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
db_op_result($debug_query,__LINE__,__FILE__);

$holds_used = $sc_ship_ore + $sc_ship_organics + $sc_ship_goods + $sc_ship_colonists;
$holds_max = NUM_HOLDS($sc_hull);
if($holds_max < $holds_used)
	$holds_used = $holds_max;

$armour_pts_max = NUM_ARMOUR($sc_armour);

$ship_fighters_max = NUM_FIGHTERS($sc_computer);

$torps_max = NUM_TORPEDOES($sc_torp_launchers);

$energy_max = NUM_ENERGY($sc_power);

$average_stats = (($sc_hull + $sc_cloak + $sc_sensors + $sc_power + $sc_engines + $sc_computer + $sc_armour + $sc_shields + $sc_beams + $sc_torp_launchers + $sc_ecm ) / 11 );

$hull_normal_bars = MakeBars($sc_hull, 40, "normal");
$engines_normal_bars = MakeBars($sc_engines, 40, "normal");
$power_normal_bars = MakeBars($sc_power, 40, "normal");
$computer_normal_bars = MakeBars($sc_computer, 40, "normal");
$sensors_normal_bars = MakeBars($sc_sensors, 40, "normal");
$armour_normal_bars = MakeBars($sc_armour, 40, "normal");
$shields_normal_bars = MakeBars($sc_shields, 40, "normal");
$beams_normal_bars = MakeBars($sc_beams, 40, "normal");
$torp_launchers_normal_bars = MakeBars($sc_torp_launchers, 40, "normal");
$cloak_normal_bars = MakeBars($sc_cloak, 40, "normal");
$ecm_normal_bars = MakeBars($sc_ecm, 40, "normal");
$average_bars = MakeBars($average_stats, 40, "normal");

$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$targetinfo[team]");
$teamstuff = $result_team->fields;
$smarty->assign("scanbounty", $scanbounty);
$smarty->assign("scanfedbounty", $scanfedbounty);
$smarty->assign("fedcheckbounty", $fedcheckbounty);
$smarty->assign("l_scan_ron", $l_scan_ron);
$smarty->assign("l_scan_capt", $l_scan_capt);
$smarty->assign("targetinfoname", $targetinfoname);

$smarty->assign("teamicon", $teamstuff['icon']);
$smarty->assign("avatar", $targetinfo['avatar']);
$smarty->assign("shipname", $targetshipinfo['name']);
$smarty->assign("l_ship_levels", $l_ship_levels);

$smarty->assign("l_hull", $l_hull);
$smarty->assign("l_hull_normal", $l_hull_normal);
$smarty->assign("shipinfo_hull_normal", $sc_hull);
$smarty->assign("classinfo_maxhull", 40);
$smarty->assign("hull_normal_bars", $hull_normal_bars);

$smarty->assign("l_engines", $l_engines);
$smarty->assign("l_engines_normal", $l_engines_normal);
$smarty->assign("shipinfo_engines_normal", $sc_engines);
$smarty->assign("classinfo_maxengines", 40);
$smarty->assign("engines_normal_bars", $engines_normal_bars);

$smarty->assign("l_power", $l_power);
$smarty->assign("l_power_normal", $l_power_normal);
$smarty->assign("shipinfo_power_normal", $sc_power);
$smarty->assign("classinfo_maxpower", 40);
$smarty->assign("power_normal_bars", $power_normal_bars);

$smarty->assign("l_computer", $l_computer);
$smarty->assign("l_computer_normal", $l_computer_normal);
$smarty->assign("shipinfo_computer_normal", $sc_computer);
$smarty->assign("classinfo_maxcomputer", 40);
$smarty->assign("computer_normal_bars", $computer_normal_bars);

$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("l_sensors_normal", $l_sensors_normal);
$smarty->assign("shipinfo_sensors_normal", $sc_sensors);
$smarty->assign("classinfo_maxsensors", 40);
$smarty->assign("sensors_normal_bars", $sensors_normal_bars);

$smarty->assign("l_avg_stats", $l_shipavg);
$smarty->assign("average_stats", NUMBER($average_stats,1));
$smarty->assign("average_bars", $average_bars);

$smarty->assign("l_armour", $l_armour);
$smarty->assign("l_armour_normal", $l_armour_normal);
$smarty->assign("shipinfo_armour_normal", $sc_armour);
$smarty->assign("classinfo_maxarmour", 40);
$smarty->assign("armour_normal_bars", $armour_normal_bars);

$smarty->assign("l_shields", $l_shields);
$smarty->assign("l_shields_normal", $l_shields_normal);
$smarty->assign("shipinfo_shields_normal", $sc_shields);
$smarty->assign("classinfo_maxshields", 40);
$smarty->assign("shields_normal_bars", $shields_normal_bars);

$smarty->assign("l_beams", $l_beams);
$smarty->assign("l_beams_normal", $l_beams_normal);
$smarty->assign("shipinfo_beams_normal", $sc_beams);
$smarty->assign("classinfo_maxbeams", 40);
$smarty->assign("beams_normal_bars", $beams_normal_bars);

$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("l_torp_launch_normal", $l_torp_launch_normal);
$smarty->assign("shipinfo_torp_launchers_normal", $sc_torp_launchers);
$smarty->assign("classinfo_maxtorp_launchers", 40);
$smarty->assign("torp_launchers_normal_bars", $torp_launchers_normal_bars);

$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("l_cloak_normal", $l_cloak_normal);
$smarty->assign("shipinfo_cloak_normal", $sc_cloak);
$smarty->assign("classinfo_maxcloak", 40);
$smarty->assign("cloak_normal_bars", $cloak_normal_bars);

$smarty->assign("l_ecm", $l_ecm);
$smarty->assign("l_ecm_normal", $l_ecm_normal);
$smarty->assign("shipinfo_ecm_normal", $sc_ecm);
$smarty->assign("classinfo_maxecm", 40);
$smarty->assign("ecm_normal_bars", $ecm_normal_bars);

$smarty->assign("l_holds", $l_holds);
$smarty->assign("l_arm_weap", $l_arm_weap);
$smarty->assign("l_devices", $l_devices);
$smarty->assign("l_total_cargo", $l_total_cargo);
$smarty->assign("holds_used", NUMBER($holds_used));
$smarty->assign("holds_max", NUMBER($holds_max));
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
$smarty->assign("energy_max", NUMBER($energy_max));
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("shipinfo_fighters", NUMBER($sc_ship_fighters));
$smarty->assign("ship_fighters_max", NUMBER($ship_fighters_max));
$smarty->assign("l_torps", $l_torps);
$smarty->assign("shipinfo_torps", NUMBER($sc_torps));
$smarty->assign("torps_max", NUMBER($torps_max));
$smarty->assign("l_armourpts", $l_armourpts);
$smarty->assign("shipinfo_armour_pts", NUMBER($sc_armour_pts));
$smarty->assign("armour_pts_max", NUMBER($armour_pts_max));
$smarty->assign("l_warpedit", $l_warpedit);
$smarty->assign("shipinfo_dev_warpedit", NUMBER($sc_dev_warpedit));
$smarty->assign("l_genesis", $l_genesis);
$smarty->assign("shipinfo_dev_genesis", NUMBER($sc_dev_genesis));
$smarty->assign("l_deflect", $l_deflect);
$smarty->assign("shipinfo_dev_minedeflector", NUMBER($sc_dev_minedeflector));
$smarty->assign("l_escape_pod", $l_escape_pod);
$smarty->assign("shipinfo_dev_escapepod", $sc_escape);
$smarty->assign("l_installed", $l_installed);
$smarty->assign("l_not_installed", $l_not_installed);
$smarty->assign("l_fuel_scoop", $l_fuel_scoop);
$smarty->assign("shipinfo_dev_fuelscoop", $sc_scoop);
$smarty->assign("l_ewd", $l_ewd);
$smarty->assign("shipinfo_dev_emerwarp", $sc_dev_emerwarp);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("shipinfo_credits", NUMBER($sc_credits));
$smarty->assign("title", $title);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("templatename", $templatename);

$smarty->display($templatename."scan.tpl");

include ("footer.php");

?>

