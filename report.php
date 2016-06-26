<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: report.php

include ("config/config.php");
include ("languages/$langdir/lang_report.inc");

$title = $l_report_title;

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

if (isset($_GET['sid']))  //Called fron the Spy menu
{
	$sid=$_GET['sid'];
	$debug_query = $db->Execute("SELECT * FROM $dbtables[spies] WHERE owner_id=$playerinfo[player_id] and ship_id='$sid'");
	db_op_result($debug_query,__LINE__,__FILE__);
	$ok = $debug_query->RecordCount();

	if ($ok)  // Player has a spy on the target ship. Let's change the ****info-s.
	{
        $debug_query = $db->SelectLimit("SELECT * FROM $dbtables[ships] WHERE ship_id='$sid'",1);
        db_op_result($debug_query,__LINE__,__FILE__);
        $shipinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM $dbtables[players] WHERE player_id='$shipinfo[player_id]'",1);
        db_op_result($debug_query,__LINE__,__FILE__);
        $playerinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class]",1);
        db_op_result($debug_query,__LINE__,__FILE__);
        $classinfo = $debug_query->fields;

        $debug_query = $db->SelectLimit("SELECT * FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]",1);
        db_op_result($debug_query,__LINE__,__FILE__);
        $sectorinfo = $debug_query->fields;
	}
	else
	{
		$smarty->assign("error_msg", $l_report_cheater);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}

}

$holds_used = $shipinfo['ore'] + $shipinfo['organics'] + $shipinfo['goods'] + $shipinfo['colonists'];
$holds_max = NUM_HOLDS($shipinfo['hull']);

$armour_pts_max = NUM_ARMOUR($shipinfo['armour']);
$ship_fighters_max = NUM_FIGHTERS($shipinfo['computer']);
$torps_max = NUM_TORPEDOES($shipinfo['torp_launchers']);
$energy_max = NUM_ENERGY($shipinfo['power']);

$average_stats = (($shipinfo['hull_normal'] + $shipinfo['cloak_normal'] + $shipinfo['sensors_normal'] + $shipinfo['power_normal'] + $shipinfo['engines_normal'] + $shipinfo['computer_normal'] + $shipinfo['armour_normal'] + $shipinfo['shields_normal'] + $shipinfo['beams_normal'] + $shipinfo['torp_launchers_normal'] + $shipinfo['ecm_normal'] ) / 11 );
$average_stats_max = (($classinfo['maxhull'] + $classinfo['maxcloak'] + $classinfo['maxsensors'] + $classinfo['maxpower'] + $classinfo['maxengines'] + $classinfo['maxcomputer'] + $classinfo['maxarmour'] + $classinfo['maxshields'] + $classinfo['maxbeams'] + $classinfo['maxtorp_launchers'] + $classinfo['maxecm'] ) / 11 );

$hull_bars = MakeBars($shipinfo['hull'], $classinfo['maxhull'], "damage");
$engines_bars = MakeBars($shipinfo['engines'], $classinfo['maxengines'], "damage");
$power_bars = MakeBars($shipinfo['power'], $classinfo['maxpower'], "damage");
$computer_bars = MakeBars($shipinfo['computer'], $classinfo['maxcomputer'], "damage");
$sensors_bars = MakeBars($shipinfo['sensors'], $classinfo['maxsensors'], "damage");
$armour_bars = MakeBars($shipinfo['armour'], $classinfo['maxarmour'], "damage");
$shields_bars = MakeBars($shipinfo['shields'], $classinfo['maxshields'], "damage");
$beams_bars = MakeBars($shipinfo['beams'], $classinfo['maxbeams'], "damage");
$torp_launchers_bars = MakeBars($shipinfo['torp_launchers'], $classinfo['maxtorp_launchers'], "damage");
$cloak_bars = MakeBars($shipinfo['cloak'], $classinfo['maxcloak'], "damage");
$ecm_bars = MakeBars($shipinfo['ecm'], $classinfo['maxecm'], "damage");
$average_bars = MakeBars($average_stats, $average_stats_max, "damage");

$hull_normal_bars = MakeBars($shipinfo['hull_normal'], $classinfo['maxhull'], "normal");
$engines_normal_bars = MakeBars($shipinfo['engines_normal'], $classinfo['maxengines'], "normal");
$power_normal_bars = MakeBars($shipinfo['power_normal'], $classinfo['maxpower'], "normal");
$computer_normal_bars = MakeBars($shipinfo['computer_normal'], $classinfo['maxcomputer'], "normal");
$sensors_normal_bars = MakeBars($shipinfo['sensors_normal'], $classinfo['maxsensors'], "normal");
$armour_normal_bars = MakeBars($shipinfo['armour_normal'], $classinfo['maxarmour'], "normal");
$shields_normal_bars = MakeBars($shipinfo['shields_normal'], $classinfo['maxshields'], "normal");
$beams_normal_bars = MakeBars($shipinfo['beams_normal'], $classinfo['maxbeams'], "normal");
$torp_launchers_normal_bars = MakeBars($shipinfo['torp_launchers_normal'], $classinfo['maxtorp_launchers'], "normal");
$cloak_normal_bars = MakeBars($shipinfo['cloak_normal'], $classinfo['maxcloak'], "normal");
$ecm_normal_bars = MakeBars($shipinfo['ecm_normal'], $classinfo['maxecm'], "normal");

if ($spy_success_factor)
{
	$debug_query = $db->Execute("SELECT * from $dbtables[spies] WHERE owner_id = $playerinfo[player_id] AND " .
								"ship_id = $shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$ship_spies = $debug_query->RecordCount();
  
	$smarty->assign("ship_spies", $ship_spies);
	$smarty->assign("l_spy", $l_spy);
}

if ($dig_success_factor)
{
	$debug_query = $db->Execute("SELECT * from $dbtables[dignitary] WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$ship_dig = $debug_query->RecordCount();
  
	$smarty->assign("ship_dig", $ship_dig);
	$smarty->assign("l_dig", $l_dig);
}

 $debug_query = $db->Execute("SELECT * from $dbtables[probe] WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id] and active='P'");
	db_op_result($debug_query,__LINE__,__FILE__);

	$ship_probe = $debug_query->RecordCount();
  
	$smarty->assign("ship_probe", $ship_probe);
	$smarty->assign("l_probe", $l_probe);

	$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
	$teamstuff = $result_team->fields;

$smarty->assign("teamicon", $teamstuff['icon']);
$smarty->assign("avatar", $playerinfo['avatar']);
$smarty->assign("spy_success_factor", $spy_success_factor);
$smarty->assign("dig_success_factor", $dig_success_factor);
$smarty->assign("shipname", $shipinfo['name']);
$smarty->assign("classname", $classinfo['name']);
$smarty->assign("classdescription", $classinfo['description']);
$smarty->assign("classimage", "templates/".$templatename."images/".$classinfo['image']);
$smarty->assign("l_ship_levels", $l_ship_levels);
$smarty->assign("l_damaged", $l_damaged);

$smarty->assign("l_hull", $l_hull);
$smarty->assign("l_hull_normal", $l_hull_normal);
$smarty->assign("shipinfo_hull", $shipinfo['hull']);
$smarty->assign("shipinfo_hull_normal", $shipinfo['hull_normal']);
$smarty->assign("classinfo_maxhull", $classinfo['maxhull']);
$smarty->assign("hull_bars", $hull_bars);
$smarty->assign("hull_normal_bars", $hull_normal_bars);

$smarty->assign("l_engines", $l_engines);
$smarty->assign("l_engines_normal", $l_engines_normal);
$smarty->assign("shipinfo_engines", $shipinfo['engines']);
$smarty->assign("shipinfo_engines_normal", $shipinfo['engines_normal']);
$smarty->assign("classinfo_maxengines", $classinfo['maxengines']);
$smarty->assign("engines_bars", $engines_bars);
$smarty->assign("engines_normal_bars", $engines_normal_bars);

$smarty->assign("l_power", $l_power);
$smarty->assign("l_power_normal", $l_power_normal);
$smarty->assign("shipinfo_power", $shipinfo['power']);
$smarty->assign("shipinfo_power_normal", $shipinfo['power_normal']);
$smarty->assign("classinfo_maxpower", $classinfo['maxpower']);
$smarty->assign("power_bars", $power_bars);
$smarty->assign("power_normal_bars", $power_normal_bars);

$smarty->assign("l_computer", $l_computer);
$smarty->assign("l_computer_normal", $l_computer_normal);
$smarty->assign("shipinfo_computer", $shipinfo['computer']);
$smarty->assign("shipinfo_computer_normal", $shipinfo['computer_normal']);
$smarty->assign("classinfo_maxcomputer", $classinfo['maxcomputer']);
$smarty->assign("computer_bars", $computer_bars);
$smarty->assign("computer_normal_bars", $computer_normal_bars);
$smarty->assign("shipinfo_computer_class", substr($shipinfo['computer_class'], 0, strpos($shipinfo['computer_class'], "_")));

$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("l_sensors_normal", $l_sensors_normal);
$smarty->assign("shipinfo_sensors", $shipinfo['sensors']);
$smarty->assign("shipinfo_sensors_normal", $shipinfo['sensors_normal']);
$smarty->assign("classinfo_maxsensors", $classinfo['maxsensors']);
$smarty->assign("sensors_bars", $sensors_bars);
$smarty->assign("sensors_normal_bars", $sensors_normal_bars);

$smarty->assign("l_avg_stats", $l_shipavg);
$smarty->assign("average_stats", NUMBER($average_stats,1));
$smarty->assign("average_stats_max", NUMBER($average_stats_max,1));
$smarty->assign("average_bars", $average_bars);

$smarty->assign("l_armour", $l_armour);
$smarty->assign("l_armour_normal", $l_armour_normal);
$smarty->assign("shipinfo_armour", $shipinfo['armour']);
$smarty->assign("shipinfo_armour_normal", $shipinfo['armour_normal']);
$smarty->assign("classinfo_maxarmour", $classinfo['maxarmour']);
$smarty->assign("armour_bars", $armour_bars);
$smarty->assign("armour_normal_bars", $armour_normal_bars);
$smarty->assign("shipinfo_armour_class", substr($shipinfo['armor_class'], 0, strpos($shipinfo['armor_class'], "_")));

$smarty->assign("l_shields", $l_shields);
$smarty->assign("l_shields_normal", $l_shields_normal);
$smarty->assign("shipinfo_shields", $shipinfo['shields']);
$smarty->assign("shipinfo_shields_normal", $shipinfo['shields_normal']);
$smarty->assign("classinfo_maxshields", $classinfo['maxshields']);
$smarty->assign("shields_bars", $shields_bars);
$smarty->assign("shields_normal_bars", $shields_normal_bars);
$smarty->assign("shipinfo_shields_class", substr($shipinfo['shield_class'], 0, strpos($shipinfo['shield_class'], "_")));

$smarty->assign("l_beams", $l_beams);
$smarty->assign("l_beams_normal", $l_beams_normal);
$smarty->assign("shipinfo_beams", $shipinfo['beams']);
$smarty->assign("shipinfo_beams_normal", $shipinfo['beams_normal']);
$smarty->assign("classinfo_maxbeams", $classinfo['maxbeams']);
$smarty->assign("beams_bars", $beams_bars);
$smarty->assign("beams_normal_bars", $beams_normal_bars);
$smarty->assign("shipinfo_beams_class", substr($shipinfo['beam_class'], 0, strpos($shipinfo['beam_class'], "_")));

$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("l_torp_launch_normal", $l_torp_launch_normal);
$smarty->assign("shipinfo_torp_launchers", $shipinfo['torp_launchers']);
$smarty->assign("shipinfo_torp_launchers_normal", $shipinfo['torp_launchers_normal']);
$smarty->assign("classinfo_maxtorp_launchers", $classinfo['maxtorp_launchers']);
$smarty->assign("torp_launchers_bars", $torp_launchers_bars);
$smarty->assign("torp_launchers_normal_bars", $torp_launchers_normal_bars);
$smarty->assign("shipinfo_torp_launchers_class", substr($shipinfo['torp_class'], 0, strpos($shipinfo['torp_class'], "_")));

$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("l_cloak_normal", $l_cloak_normal);
$smarty->assign("shipinfo_cloak", $shipinfo['cloak']);
$smarty->assign("shipinfo_cloak_normal", $shipinfo['cloak_normal']);
$smarty->assign("classinfo_maxcloak", $classinfo['maxcloak']);
$smarty->assign("cloak_bars", $cloak_bars);
$smarty->assign("cloak_normal_bars", $cloak_normal_bars);

$smarty->assign("l_ecm", $l_ecm);
$smarty->assign("l_ecm_normal", $l_ecm_normal);
$smarty->assign("shipinfo_ecm", $shipinfo['ecm']);
$smarty->assign("shipinfo_ecm_normal", $shipinfo['ecm_normal']);
$smarty->assign("classinfo_maxecm", $classinfo['maxecm']);
$smarty->assign("ecm_bars", $ecm_bars);
$smarty->assign("ecm_normal_bars", $ecm_normal_bars);

$smarty->assign("l_class", $l_class);
$smarty->assign("l_holds", $l_holds);
$smarty->assign("l_arm_weap", $l_arm_weap);
$smarty->assign("l_devices", $l_devices);
$smarty->assign("l_total_cargo", $l_total_cargo);
$smarty->assign("holds_used", NUMBER($holds_used));
$smarty->assign("holds_max", NUMBER($holds_max));
$smarty->assign("l_ore", $l_ore);
$smarty->assign("shipinfo_ore", NUMBER($shipinfo['ore']));
$smarty->assign("l_organics", $l_organics);
$smarty->assign("shipinfo_organics", NUMBER($shipinfo['organics']));
$smarty->assign("l_goods", $l_goods);
$smarty->assign("shipinfo_goods", NUMBER($shipinfo['goods']));
$smarty->assign("l_colonists", $l_colonists);
$smarty->assign("shipinfo_colonists", NUMBER($shipinfo['colonists']));
$smarty->assign("l_energy", $l_energy);
$smarty->assign("shipinfo_energy", NUMBER($shipinfo['energy']));
$smarty->assign("energy_max", NUMBER($energy_max));
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("shipinfo_fighters", NUMBER($shipinfo['fighters']));
$smarty->assign("ship_fighters_max", NUMBER($ship_fighters_max));
$smarty->assign("l_torps", $l_torps);
$smarty->assign("shipinfo_torps", NUMBER($shipinfo['torps']));
$smarty->assign("torps_max", NUMBER($torps_max));
$smarty->assign("l_armourpts", $l_armourpts);
$smarty->assign("shipinfo_armour_pts", NUMBER($shipinfo['armour_pts']));
$smarty->assign("armour_pts_max", NUMBER($armour_pts_max));
$smarty->assign("l_beacons", $l_beacons);
$smarty->assign("shipinfo_dev_beacon", $shipinfo['dev_beacon']);
$smarty->assign("l_warpedit", $l_warpedit);
$smarty->assign("shipinfo_dev_warpedit", NUMBER($shipinfo['dev_warpedit']));
$smarty->assign("l_genesis", $l_genesis);
$smarty->assign("shipinfo_dev_genesis", NUMBER($shipinfo['dev_genesis']));
$smarty->assign("l_sectorgenesis", $l_sectorgenesis);
$smarty->assign("shipinfo_dev_sectorgenesis", NUMBER($shipinfo['dev_sectorgenesis']));
$smarty->assign("l_deflect", $l_deflect);
$smarty->assign("shipinfo_dev_minedeflector", NUMBER($shipinfo['dev_minedeflector']));
$smarty->assign("l_escape_pod", $l_escape_pod);
$smarty->assign("shipinfo_dev_escapepod", $shipinfo['dev_escapepod']);
$smarty->assign("l_installed", $l_installed);
$smarty->assign("l_not_installed", $l_not_installed);
$smarty->assign("l_fuel_scoop", $l_fuel_scoop);
$smarty->assign("shipinfo_dev_fuelscoop", $shipinfo['dev_fuelscoop']);
$smarty->assign("l_ewd", $l_ewd);
$smarty->assign("shipinfo_dev_emerwarp", $shipinfo['dev_emerwarp']);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("shipinfo_credits", NUMBER($playerinfo['credits']));
$smarty->assign("l_nova", $l_nova);
$smarty->assign("shipinfo_dev_nova", $shipinfo['dev_nova']);
$smarty->assign("title", $title);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("templatename", $templatename);
$smarty->assign("l_spy_linkback", $l_spy_linkback);
$smarty->assign("spycheck", isset($_GET['sid']));

$smarty->display($templatename."report.tpl");

include ("footer.php");

?>

