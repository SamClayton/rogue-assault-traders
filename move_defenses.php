<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: check_defenses.php

include ("config/config.php");
include ("languages/$langdir/lang_move.inc");
include ("languages/$langdir/lang_rsmove.inc");
include ("languages/$langdir/lang_check_fighters.inc");
include ("languages/$langdir/lang_check_mines.inc");
include ("combat_functions.php");
$no_gzip = 1;

$title = $l_move_title;

// Check to see if the user is logged in
if (checklogin() or $tournament_setup_access == 1)
{
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template']))
	$templatename = $default_template;
else
	$templatename = $playerinfo['template'];

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

if (!isset($move_method) || $move_method == '' || !isset($destination) || $destination == '') {
	$smarty->assign("error_msg", $l_rs_invalid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."movedie.tpl");
	include ("footer.php");
	die();
}
if ((!isset($response)) || ($response == ''))
{
	$response = '';
}

// Without '===' destination sector 0 is not printed. This is a departure from the normal '==', although we don't have a
// good reason why.
if ((!isset($destination)) || ($destination === ''))
{
	$destination = '';
}

if ($move_method == '' || ($destination == '' && $sector == '')) {
	$smarty->assign("error_msg", $l_rs_invalid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."movedie.tpl");
	include ("footer.php");
	die();
}

switch ($move_method) {
	case "real":
		$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$destination and sg_sector=0");
		if($result) {
			if($result->RecordCount() == 0 || $sectorinfo['sg_sector'] != 0) {
				$move_failed = 1;
				$smarty->assign("error_msg", $l_rs_invalid);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."movedie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$distance = calc_dist($shipinfo['sector_id'],$destination);
				$shipspeed = mypw($level_factor, $shipinfo['engines']);
				$turns_required = round($distance / $shipspeed);
				if ($turns_required == 0)
				{
					$turns_required = 1;
				}
				$energy_collected = $distance * 100;
				$linkback = true;
			}
		}
		else
		{
			$move_failed = 1;
			$smarty->assign("error_msg", "DB Failure");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."movedie.tpl");
			include ("footer.php");
			die();
		}

		break;

	case "warp":
		$destination = $sector;
		$result = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$shipinfo[sector_id]' AND link_dest='$destination'");
		if($result) {
			if($result->RecordCount() == 0) {
				$move_failed = 1;
				$smarty->assign("error_msg", $l_rs_invalid);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."movedie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$turns_required = 1;
				$energy_collected = 0;

				$linkback = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start='$destination' AND link_dest'$shipinfo[sector_id]'");
				if($linkback) {
					if($linkback->recordCount() > 0)
						$linkback = true;
					else
						$linkback = false;
				}
				else
					$linkback = false;
			}
		}
		else
		{
			$move_failed = 1;
			$smarty->assign("error_msg", "DB Failure");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."movedie.tpl");
			include ("footer.php");
			die();
		}

		break;

	default:
		$move_failed = 1;
		$smarty->assign("error_msg", $l_rs_invalid);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."movedie.tpl");
		include ("footer.php");
		die();
		break;
}

// Check to see if the player has less than one turn available
// and if so return to the main menu
if($turns_required > $playerinfo['turns']) {
	$l_rs_movetime=str_replace("[triptime]",NUMBER($turns_required),$l_rs_movetime);
	$smarty->assign("error_msg", $l_rs_movetime);
	$smarty->assign("error_msg2", $l_rs_noturns);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."move.tpl");
	include ("footer.php");
	die();
}

if($shipinfo['dev_fuelscoop'] != "Y")
	$energy_collected = 0;
else {
	if((NUM_ENERGY($shipinfo['power']) - $shipinfo['energy']) < $energy_collected)
		$energy_collected = max(NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'],0);
}

if(isset($explore)){
	$move_failed = 1;
	$l_rs_movetime=str_replace("[triptime]",NUMBER($turns_required),$l_rs_movetime);
	$l_rs_energy=str_replace("[energy]",NUMBER($energy_collected),$l_rs_energy);
	$l_rs_engage_link= "<A HREF=move.php?move_method=real&engage=1&destination=$destination>" . $l_rs_engage_link . "</A>";
	$l_rs_engage=str_replace("[turns]",NUMBER($playerinfo['turns']),$l_rs_engage);
	$l_rs_engage=str_replace("[engage]",$l_rs_engage_link,$l_rs_engage);
	$smarty->assign("error_msg", "$l_rs_movetime $l_rs_energy");
	$smarty->assign("error_msg2", $l_rs_engage);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."move.tpl");
	include ("footer.php");
	die();
}
//Put the defence information into the array "defenceinfo"
$num_defences = 0;
$total_sector_fighters = 0;
$total_sector_mines = 0;
$move_failed = 0;

$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$destination' and player_id != '$playerinfo[player_id]' ORDER BY quantity DESC");
if ($result3 > 0)
{
	while (!$result3->EOF)
	{
		$row = $result3->fields;
		$defences[$num_defences] = $row;
		if ($defences[$num_defences]['defence_type'] == 'F')
		{
			$total_sector_fighters += $defences[$num_defences]['quantity'];
		}
		elseif ($defences[$num_defences]['defence_type'] == 'M')
		{
			$total_sector_mines += $defences[$num_defences]['quantity'];
		}

		$num_defences++;
		$result3->MoveNext();
	}
}

if ($num_defences > 0 && $total_sector_fighters > 0)
{
	// Are the fighter owner and player are on the same team?
	// All sector defences must be owned by members of the same team.
	$fm_owner = $defences[0]['player_id'];
	$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
	$fighters_owner = $result2->fields;

	if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
	{
		switch ($response) 
		{
			case "retreat":
				if ($move_method == 'real')
				{
					$shipspeed = mypw($level_factor, $shipinfo['engines']);
					$turns_back = 2 * round($distance / $shipspeed);
					if ($turns_back == 0)
					{
						$turns_back = 2;
					}
				}
				else
				{
					$turns_back = 2; //Warp
				}

				$stamp = date("Y-m-d H:i:s");

				$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-$turns_back, turns_used=turns_used+$turns_back WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$shipinfo[sector_id] WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				$smarty->assign("error_msg", $l_chf_youretreatback);
				$smarty->assign("error_msg2", '');
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."move.tpl");
				include ("footer.php");
				die();
				break;

			case "fight":
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns+($turns_required * 2),turns_used=turns_used-($turns_required * 2) WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				include ("combat_sector_fighters.php");
				break;

			case "run":
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns+($turns_required * 2),turns_used=turns_used-($turns_required * 2) WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				include ("combat_sector_run.php");
				TEXT_GOTOMAIN();
				include ("footer.php");
				die();
				break;

			case "sneak":
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns+($turns_required * 2),turns_used=turns_used-($turns_required * 2) WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$highsensors=0;
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
				db_op_result($result2,__LINE__,__FILE__);
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				db_op_result($result3,__LINE__,__FILE__);
				$ship_owner = $result3->fields;

				// get planet sensors
				$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$destination' order by sensors DESC");
				db_op_result($result4,__LINE__,__FILE__);
				$planets = $result4->fields;

				if ($highsensors < $planets['sensors']){
					$highsensors=$planets['sensors'];
				}
				$success = max(min(SCAN_SUCCESS($highsensors, $shipinfo['cloak']), 95), 5);

				if (mt_rand(1, 100) < $success)
				{
					// sector defences detect incoming ship
					include ("combat_sector_fighters.php");
					break;
				}
				else
				{
					// sector defences don't detect incoming ship
					$move_failed = 0;
				}
				break;
		}
	}
}

if ($num_defences > 0 && $total_sector_mines > 0 && $playerinfo['player_id'] != 3)
{
	$fm_owner = $defences[0]['player_id'];
	$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
	$mine_owner = $result2->fields;

	if ($mine_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) // Are the mine owner and player are on the same team?
	{
		include("combat_sector_mines.php");
	}
}

if(!$move_failed) {

	if($move_method == "real"){
		$distance = calc_dist($shipinfo['sector_id'],$destination);
		$shipspeed = mypw($level_factor, $shipinfo['engines']);
		$turns_required = ceil($distance / $shipspeed);
		$energy_collected = $distance * 100;
	}
	else
	{
		$turns_required = 1;
		$energy_collected = 0;
	}

	if($shipinfo['dev_fuelscoop'] != "Y")
		$energy_collected = 0;
	else {
		if((NUM_ENERGY($shipinfo['power']) - $shipinfo['energy']) < $energy_collected)
			$energy_collected = max(NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'],0);
	}

	$stamp = date("Y-m-d H:i:s");
	$source_sector = $shipinfo['sector_id'];
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns-$turns_required,turns_used=turns_used+$turns_required WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$destination, energy=energy+$energy_collected WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$l_rs_ready = str_replace("[sector]",$destination,$l_rs_ready);
	$l_rs_ready = str_replace("[triptime]",NUMBER($turns_required),$l_rs_ready);
	$l_rs_ready = str_replace("[energy]",NUMBER($energy_collected),$l_rs_ready);

	log_move($playerinfo['player_id'],$shipinfo['ship_id'],$shipinfo['sector_id'],$destination,$shipinfo['class'],$shipinfo['cloak'], $zoneinfo['zone_id']);

	$probe_query = $db->Execute("SELECT * FROM $dbtables[probe] WHERE sector_id=$destination and owner_id != $shipinfo[player_id] and active='Y';");

	db_op_result($probe_query,__LINE__,__FILE__);

	while (!$probe_query->EOF)
	{
		$sensors = $probe_query->fields['sensors'];
		$probe_id=$probe_query->fields['probe_id'];
		$sector_id=$probe_query->fields['sector_id'];
		$type=$probe_query->fields['type'];
		$owner_id=$probe_query->fields['owner_id'];

		$success = max(min(SCAN_SUCCESS($sensors, $shipinfo['cloak']), 95), 5);

		$roll = mt_rand(1, 100);
		if ($roll < $success)
		{
 			$roll = mt_rand(1, 100);
			$shiptype="unknown";
			if ($roll < $success)
			{
				$res2 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class];");
				db_op_result($res2,__LINE__,__FILE__);
				$shiptype = $res2->fields['name'];
			}

			$res3 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class];");
			db_op_result($res3,__LINE__,__FILE__);
			$shiptype = $res3->fields['name'];
			if (($type==1)and ($sensors>=20)){
				$resteam = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$owner_id;");
				db_op_result($resteam,__LINE__,__FILE__);
				$sb_alli = $resteam->fields['team'];
				if ($sb_alli==0)
				{
				  	$sb_alli=-1;
				}
				$sbt="Probe detected $playerinfo[character_name] in sector $sector_id";
			  	$res = $db->Execute("INSERT INTO $dbtables[shoutbox] (player_id,player_name,sb_date,sb_text,sb_alli) VALUES ($owner_id,'probe - $probe_id'," . time() . ",'$sbt',$sb_alli) ");
			}

			playerlog($owner_id, LOG_PROBE_DETECTED_SHIP, "$probe_id|$sector_id|$playerinfo[character_name]($shiptype)");

			if($sensors > 15){
				if (mt_rand(1, 100) < $success)
				{
					$sc_error = SCAN_ERROR($sensors, $shipinfo['cloak']);

					$sc_hull = (mt_rand(1, 100) < $success) ? round($shipinfo['hull'] * $sc_error / 100) : "???";
					$sc_engines = (mt_rand(1, 100) < $success) ? round($shipinfo['engines'] * $sc_error / 100) : "???";
					$sc_power = (mt_rand(1, 100) < $success) ? round($shipinfo['power'] * $sc_error / 100) : "???";
					$sc_computer = (mt_rand(1, 100) < $success) ? round($shipinfo['computer'] * $sc_error / 100) : "???";
					$sc_sensors = (mt_rand(1, 100) < $success) ? round($shipinfo['sensors'] * $sc_error / 100) : "???";
					$sc_beams = (mt_rand(1, 100) < $success) ? round($shipinfo['beams'] * $sc_error / 100) : "???";
					$sc_torp_launchers = (mt_rand(1, 100) < $success) ? round($shipinfo['torp_launchers'] * $sc_error / 100) : "???";
					$sc_armour = (mt_rand(1, 100) < $success) ? round($shipinfo['armour'] * $sc_error / 100) : "???";
					$sc_shields = (mt_rand(1, 100) < $success) ? round($shipinfo['shields'] * $sc_error / 100) : "???";
					$sc_cloak = (mt_rand(1, 100) < $success) ? round($shipinfo['cloak'] * $sc_error / 100) : "???";
					$sc_ecm = (mt_rand(1, 100) < $success) ? round($shipinfo['ecm'] * $sc_error / 100) : "???";
					$sc_armour_pts = (mt_rand(1, 100) < $success) ? round($shipinfo['armour_pts'] * $sc_error / 100) : "???";
					$sc_ship_fighters = (mt_rand(1, 100) < $success) ? round($shipinfo['fighters'] * $sc_error / 100) : "???";
					$sc_torps = (mt_rand(1, 100) < $success) ? round($shipinfo['torps'] * $sc_error / 100) : "???";
					$sc_credits = (mt_rand(1, 100) < $success) ? round($shipinfo['credits'] * $sc_error / 100) : "???";
					$sc_ship_energy = (mt_rand(1, 100) < $success) ? round($shipinfo['energy'] * $sc_error / 100) : "???";
					$sc_dev_minedeflector = (mt_rand(1, 100) < $success) ? round($shipinfo['dev_minedeflector'] * $sc_error / 100) : "???";
					$sc_dev_emerwarp = (mt_rand(1, 100) < $success) ? round($shipinfo['dev_emerwarp'] * $sc_error / 100) : "???";
					$sc_dev_pod = (mt_rand(1, 100) < $success) ? round($shipinfo['dev_escapepod'] * $sc_error / 100) : "???";

					playerlog($owner_id, LOG_PROBE_SCAN_SHIP, "$probe_id|$sector_id|$playerinfo[character_name]($shiptype)|$sc_hull|$sc_engines|$sc_power|$sc_computer|$sc_sensors|$sc_beams|$sc_torp_launchers|$sc_armour|$sc_shields|$sc_cloak|$sc_armour_pts|$sc_ship_fighters|$sc_torps|$sc_credits|$sc_ship_energy|$sc_dev_minedeflector|$sc_dev_emerwarp|$sc_dev_pod|$sc_ecm");
				}
			}
		}
		$probe_query->MoveNext();
	}

	$smarty->assign("title", '');
	$smarty->assign("error_msg", $l_rs_ready);
	$smarty->assign("error_msg2", '');
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."move.tpl");
	include ("footer.php");
}
else
{
	$smarty->assign("error_msg", '');
	$smarty->assign("error_msg2", '');
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."move.tpl");
	include ("footer.php");
}
?>
