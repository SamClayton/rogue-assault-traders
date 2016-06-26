<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File){ showdebris.php

include ("config/config.php");

include ("languages/$langdir/lang_debris.inc");
include ("languages/$langdir/lang_report.inc");

$debris_id = '';
if (isset($_GET['debris_id']))
{
	$debris_id = $_GET['debris_id'];
}

if (checklogin() or $tournament_setup_access == 1)
{
	include ("footer.php");
	die();
}

$title = $l_debris_title;
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

$result4 = $db->Execute(" SELECT * FROM $dbtables[debris] where debris_id=$debris_id and sector_id=$shipinfo[sector_id]");
db_op_result($result4,__LINE__,__FILE__);

if ($result4->recordcount())
{
	$row = $result4->fields;

	if($row['debris_type'] == 0){
			$debrismessage =  $l_debris_nothing;
	}

	if($row['debris_type'] == 1){	// Add/Remove Turns
		$amount = mt_rand(50, 50 + $max_debris_turns);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addturns);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns+$amount WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if(($playerinfo['turns'] - $amount) < 0){
				$amount = $playerinfo['turns'];
			}
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removeturns);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-$amount WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 2){	// Add/Remove Torps
		$amount = mt_rand(50, 50 + $max_debris_torps);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addtorps);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET torps=torps+$amount WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if(($shipinfo['torps'] - $amount) < 0){
				$amount = $shipinfo['torps'];
			}
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removetorps);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET torps=GREATEST(torps-$amount, 0) WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 3){	// Add/Remove Fighters
		$amount = mt_rand(50, 50 + $max_debris_fighters);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addfighters);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET fighters=fighters+$amount WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if(($shipinfo['fighters'] - $amount) < 0){
				$amount = $shipinfo['fighters'];
			}
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removefighters);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET fighters=GREATEST(fighters-$amount, 0) WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 4){	// Add/Remove Armor
		$amount = mt_rand(50, 50 + $max_debris_armor);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addarmor);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=armour_pts+$amount WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if(($shipinfo['armour_pts'] - $amount) < 0){
				$amount = $shipinfo['armour_pts'];
			}
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removearmor);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=GREATEST(armour_pts-$amount, 0) WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 5){	// Add/Remove Energy
		$amount = mt_rand(50, 50 + $max_debris_energy);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addenergy);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=energy+$amount WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if(($shipinfo['energy'] - $amount) < 0){
				$amount = $shipinfo['energy'];
			}
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removeenergy);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST(energy-$amount, 0) WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 6){	// Add/Remove Credits
		$amount = mt_rand(50, 50 + $max_debris_credits);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_addcredits);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$amount WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			$amount = round($playerinfo['credits'] * ($piratestealpercent / 100));

			$debrismessage = str_replace("[amount]", NUMBER($amount), $l_debris_removecredits);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=GREATEST(credits-$amount, 0) WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
			$totrecs=$findem->RecordCount(); 
			$getit=$findem->GetArray();
			$randplay=mt_rand(0,($totrecs-1));
			$targetlink = $getit[$randplay]['sector_id'];
			$debug_query = $db->Execute("INSERT INTO $dbtables[debris] (debris_type, debris_data, sector_id) values (14,'$amount', $targetlink)");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 7){	// Random Enemy Spy placed on ship
		$debrismessage = $l_debris_nothing;

		$findem = $db->Execute("SELECT player_id FROM $dbtables[players] where player_id != $playerinfo[player_id] and npc=0");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		$randplay=mt_rand(0,($totrecs-1));
		$fromplayer = $getit[$randplay]['player_id'];

		$debug_query = $db->Execute("INSERT INTO $dbtables[spies] (active, owner_id, planet_id, ship_id, job_id, spy_percent, move_type) values ('Y',$fromplayer,'0','$shipinfo[ship_id]','0','0.0','toship')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	if($row['debris_type'] == 8){	// Random Wormhole Warp
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		$randplay=mt_rand(0,($totrecs-1));
		$targetlink = $getit[$randplay]['sector_id'];
		$debrismessage = str_replace("[sector]", $targetlink, $l_debris_wormhole);

		$query = "UPDATE $dbtables[ships] SET sector_id=$targetlink WHERE ship_id=$shipinfo[ship_id]";
		$debug_query = $db->Execute("$query");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	if($row['debris_type'] == 9){	// Add/Remove tech level from random tech selection 1-5
		$amount = mt_rand(1, 5);
		$tech = mt_rand(1, 11);
		if($row['debris_data'] == 1)
		{
			if($tech == 1){
				$techname = $l_hull;
				$query = "hull_normal=hull_normal + $amount, hull=hull + $amount";
			}
			if($tech == 2){
				$techname = $l_engines;
				$query = "engines_normal=engines_normal + $amount, engines=engines + $amount";
			}
			if($tech == 3){
				$techname = $l_power;
				$query = "power_normal=power_normal + $amount, power=power + $amount";
			}
			if($tech == 4){
				$techname = $l_computer;
				$query = "computer_normal=computer_normal + $amount, computer=computer + $amount";
			}
			if($tech == 5){
				$techname = $l_sensors;
				$query = "sensors_normal=sensors_normal + $amount, sensors=sensors + $amount";
			}
			if($tech == 6){
				$techname = $l_beams;
				$query = "beams_normal=beams_normal + $amount, beams=beams + $amount";
			}
			if($tech == 7){
				$techname = $l_torp_launch;
				$query = "torp_launchers_normal=torp_launchers_normal + $amount, torp_launchers=torp_launchers + $amount";
			}
			if($tech == 8){
				$techname = $l_shields;
				$query = "shields_normal=shields_normal + $amount, shields=shields + $amount";
			}
			if($tech == 9){
				$techname = $l_armour;
				$query = "armour_normal=armour_normal + $amount, armour=armour + $amount";
			}
			if($tech == 10){
				$techname = $l_cloak;
				$query = "cloak_normal=cloak_normal + $amount, cloak=cloak + $amount";
			}
			if($tech == 11){
				$techname = $l_ecm;
				$query = "ecm_normal=ecm_normal + $amount, ecm=ecm + $amount";
			}
			$debrismessage = str_replace("[tech]", $techname, $l_debris_upgradelevel);
			$debrismessage = str_replace("[levels]", NUMBER($amount), $debrismessage);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET $query WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if($tech == 1){
				$techname = $l_hull;
				$query = "hull_normal=GREATEST(hull_normal - $amount, 0), hull=GREATEST(hull - $amount, 0)";
			}
			if($tech == 2){
				$techname = $l_engines;
				$query = "engines_normal=GREATEST(engines_normal - $amount, 0), engines=GREATEST(engines - $amount, 0)";
			}
			if($tech == 3){
				$techname = $l_power;
				$query = "power_normal=GREATEST(power_normal - $amount, 0), power=GREATEST(power - $amount, 0)";
			}
			if($tech == 4){
				$techname = $l_computer;
				$query = "computer_normal=GREATEST(computer_normal - $amount, 0), computer=GREATEST(computer - $amount, 0)";
			}
			if($tech == 5){
				$techname = $l_sensors;
				$query = "sensors_normal=GREATEST(sensors_normal - $amount, 0), sensors=GREATEST(sensors - $amount, 0)";
			}
			if($tech == 6){
				$techname = $l_beams;
				$query = "beams_normal=GREATEST(beams_normal - $amount, 0), beams=GREATEST(beams - $amount, 0)";
			}
			if($tech == 7){
				$techname = $l_torp_launch;
				$query = "torp_launchers_normal=GREATEST(torp_launchers_normal - $amount, 0), torp_launchers=GREATEST(torp_launchers - $amount, 0)";
			}
			if($tech == 8){
				$techname = $l_shields;
				$query = "shields_normal=GREATEST(shields_normal - $amount, 0), shields=GREATEST(shields - $amount, 0)";
			}
			if($tech == 9){
				$techname = $l_armour;
				$query = "armour_normal=GREATEST(armour_normal - $amount, 0), armour=GREATEST(armour - $amount, 0)";
			}
			if($tech == 10){
				$techname = $l_cloak;
				$query = "cloak_normal=GREATEST(cloak_normal - $amount, 0), cloak=GREATEST(cloak - $amount, 0)";
			}
			if($tech == 11){
				$techname = $l_ecm;
				$query = "ecm_normal=GREATEST(ecm_normal - $amount, 0), ecm=GREATEST(ecm - $amount, 0)";
			}

			$debrismessage = str_replace("[tech]", $techname, $l_debris_degradelevel);
			$debrismessage = str_replace("[levels]", NUMBER($amount), $debrismessage);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET $query WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 10){	// Add/Remove 1-2 on all tech levels
		$amount = mt_rand(1, 5);
		if($row['debris_data'] == 1)
		{
			$debrismessage = str_replace("[level]", NUMBER($amount), $l_debris_upgradeall);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET hull_normal=hull_normal+$amount, engines_normal=engines_normal+$amount, power_normal=power_normal+$amount, computer_normal=computer_normal+$amount, sensors_normal=sensors_normal+$amount, beams_normal=beams_normal+$amount, torp_launchers_normal=torp_launchers_normal+$amount, shields_normal=shields_normal+$amount, armour_normal=armour_normal+$amount, cloak_normal=cloak_normal+$amount, ecm_normal=ecm_normal+$amount, hull=hull+$amount, engines=engines+$amount, power=power+$amount, computer=computer+$amount, sensors=sensors+$amount, beams=beams+$amount, torp_launchers=torp_launchers+$amount, shields=shields+$amount, armour=armour+$amount, cloak=cloak+$amount, ecm=ecm+$amount WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			$debrismessage = str_replace("[level]", NUMBER($amount), $l_debris_degradeall);
			$query = "hull_normal=GREATEST(hull_normal - $amount, 0), hull=GREATEST(hull - $amount, 0),";
			$query .= "engines_normal=GREATEST(engines_normal - $amount, 0), engines=GREATEST(engines - $amount, 0),";
			$query .= "power_normal=GREATEST(power_normal - $amount, 0), power=GREATEST(power - $amount, 0),";
			$query .= "computer_normal=GREATEST(computer_normal - $amount, 0), computer=GREATEST(computer - $amount, 0),";
			$query .= "sensors_normal=GREATEST(sensors_normal - $amount, 0), sensors=GREATEST(sensors - $amount, 0),";
			$query .= "beams_normal=GREATEST(beams_normal - $amount, 0), beams=GREATEST(beams - $amount, 0),";
			$query .= "torp_launchers_normal=GREATEST(torp_launchers_normal - $amount, 0), torp_launchers=GREATEST(torp_launchers - $amount, 0),";
			$query .= "shields_normal=GREATEST(shields_normal - $amount, 0), shields=GREATEST(shields - $amount, 0),";
			$query .= "armour_normal=GREATEST(armour_normal - $amount, 0), armour=GREATEST(armour - $amount, 0),";
			$query .= "cloak_normal=GREATEST(cloak_normal - $amount, 0), cloak=GREATEST(cloak - $amount, 0),";
			$query .= "ecm_normal=GREATEST(ecm_normal - $amount, 0), ecm=GREATEST(ecm - $amount, 0)";

			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET $query WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}

	if($row['debris_type'] == 11){	// Add/Remove Sector Genesis Device
		if($row['debris_data'] == 1)
		{
			$debrismessage = $l_debris_addsg;
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_sectorgenesis=dev_sectorgenesis+1 WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if($shipinfo['dev_sectorgenesis'] == 0){
				if(($shipinfo['dev_sectorgenesis'] - 1) < 0){
					$amount = $shipinfo['dev_sectorgenesis'];
				}
				$debrismessage = $l_debris_removesg;
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_sectorgenesis=GREATEST(dev_sectorgenesis-1, 0) WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$debrismessage = $l_debris_nothing;
			}
		}
	}

	if($row['debris_type'] == 12){	// Add/Remove Nova Bomb
		if($row['debris_data'] == 1)
		{
			if($shipinfo['dev_nova'] == "N" && $dev_nova_shiplimit <= $shipinfo['class']){
				$debrismessage = $l_debris_addnova;
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_nova='Y' WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$debrismessage = $l_debris_nothing;
			}
		}else{
			if($shipinfo['dev_nova'] == "Y"){
				$debrismessage = $l_debris_removenova;
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_nova='N' WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$debrismessage = $l_debris_nothing;
			}
		}
	}

	if($row['debris_type'] == 13){	// Add/Remove escapepod
		if($row['debris_data'] == 1)
		{
			if($shipinfo['dev_escapepod']){
				$debrismessage = $l_debris_nothing;
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_escapepod='Y' WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$debrismessage = $l_debris_nothing;
			}
		}else{
			if($shipinfo['dev_escapepod'] == "Y"){
				$debrismessage = $l_debris_nothing;
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_escapepod='N' WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$debrismessage = $l_debris_nothing;
			}
		}
	}

	// Destroy Ship, changed to add / remove escape pod
	

	if($row['debris_type'] == 14){	// Credits that were stolen in remove credits
		$debrismessage = str_replace("[amount]", NUMBER($row['debris_data']), $l_debris_addcredits);
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$row[debris_data] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	$debug_query = $db->Execute("DELETE FROM $dbtables[debris] WHERE debris_id=$debris_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$smarty->assign("error_msg", $debrismessage);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."debris.tpl");
	include ("footer.php");
}else{
	$smarty->assign("error_msg", $l_debris_cantfind);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."debris.tpl");
	include ("footer.php");
}

close_database();
?>
