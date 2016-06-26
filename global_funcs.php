<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: global_funcs.php

if (preg_match("/global_funcs.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if ( $create_universe != 1 && ((!isset($_SESSION['userpass'])) or ($_SESSION['userpass'] == '')))
{
	$_SESSION['userpass'] = '';
}


if ((!isset($username)) || ($username == ''))
{
	$username = '';
}

if ((!isset($character_name)) || ($character_name == ''))
{
	$character_name = '';
}

if ((!isset($password)) || ($password == ''))
{
	$password = '';
}

if ($create_universe != 1 && ($_SESSION['userpass'] != '' && $_SESSION['userpass'] != '+'))
{
	$username = substr($_SESSION['userpass'], 0, strpos($_SESSION['userpass'], "+"));
	$password = substr($_SESSION['userpass'], strpos($_SESSION['userpass'], "+")+1);
}

if (isset($newlang)) 
{ 
	$_SESSION['langdir'] = $newlang; 
}

if ((!isset($_SESSION['langdir'])) || ($_SESSION['langdir'] == ''))
{
	$_SESSION['langdir'] = $default_lang;
}

$langdir = $_SESSION['langdir'];
include ("languages/$langdir/regional_settings.php");
include ("log_definitions.php");

include ("includes/$db_type-common.php"); // This is where all mysql calls that are common should be moved.

include ("languages/$langdir/lang_common.inc");
include ("languages/$langdir/lang_main.inc");
include ("languages/$langdir/lang_spy.inc");
include ("languages/$langdir/lang_dig.inc");
include ("languages/$langdir/lang_sched_news.inc");
include ("languages/$langdir/lang_global_funcs.inc");
include ("languages/$langdir/lang_modify_defences.inc");
include ("languages/$langdir/lang_news.inc");

// Define global functions
if ($spy_success_factor)
{
	include ("spy_funcs.php");
}

if ($dig_success_factor)
{
	include ("dig_funcs.php");
}

function myrand($lower, $upper, $distribution_const = 1) // Used for spies.
{
	$max_random = mt_getrandmax();
  
	if ($distribution_const == 1)
	{
		return floor($lower + ($upper-$lower+1)*MT_RAND(0,$max_random)/($max_random+1));
	}
  
	elseif ($distribution_const > 1)
	{
		return floor($lower + ($upper-$lower+1)*POW(MT_RAND(0,$max_random)/($max_random+1),$distribution_const));
	}
  
	else
	{
		return floor($lower + ($upper-$lower+1)*POW(MT_RAND(1,$max_random)/($max_random+1),$distribution_const));  //it might be 0..$max_random, but for example, POW(0, 0.8) returns error
	}
}

function mypw($one,$two)
{
	return pow($one*1,$two*1);
}

function bigtitle()
{
	global $title;
	echo "<H1>$title</H1>\n";
}

function TEXT_GOTOMAIN()
{
	global $l_global_mmenu;
	echo $l_global_mmenu;
}

function TEXT_JAVASCRIPT_BEGIN()
{
	echo "\n<SCRIPT LANGUAGE=\"JavaScript\">\n";
	echo "<!--\n";
}

function TEXT_JAVASCRIPT_END()
{
	echo "\n// -->\n";
	echo "</SCRIPT>\n";
}

function checklogin()
{
	$flag = 0;

	global $db, $dbtables, $playerinfo, $shipinfo, $zoneinfo, $sectorinfo, $presetinfo, $presettotal, $presettext; 
	global $spy_success_factor, $classinfo, $username, $userpass, $password, $response;
	global $l_global_needlogin, $l_global_died, $l_login_died, $l_die_please;
	global $start_fighters, $start_armour, $start_energy, $noreturn, $silent, $create_universe, $sector_max, $refreshcount, $refresh_max, $idle_max;
	global $server_closed;
	global $l_login_closed_message;

	$max_query = $db->SelectLimit("SELECT sector_id from $dbtables[universe] order by sector_id DESC", 1);
	db_op_result($max_query,__LINE__,__FILE__);

	$sector_max = $max_query->fields['sector_id'];

	if ($username == '' || $password == '')
	{
		if($noreturn != 1)
			echo $l_global_needlogin;
		return 1;
	}

	$temp = $silent;
	$silent = 1;

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[players] WHERE email='$username'",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$playerinfo = $debug_query->fields;

	// Check password against database
	if ($password != $playerinfo['password'])
	{
		echo $l_global_needlogin;
		$flag = 1;
	}

	if ((TIME() - strtotime($playerinfo['last_login'])) / 60 > $idle_max || $refreshcount >= $refresh_max)
	{
		$stamp = date("Y-m-d H:i:s", (TIME() - 360));
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp' WHERE player_id = $playerinfo[player_id]");

		session_destroy();
		if($noreturn != 1)
			echo $l_global_needlogin;

		return 1;
	}

	$stamp = date("Y-m-d H:i:s");
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp' WHERE player_id = $playerinfo[player_id]");
//	db_op_result($debug_query,__LINE__,__FILE__);

	// The combined userpass login.
	$userpass = $username."+".$password;
	if ($create_universe != 1) 
	{
		$_SESSION['userpass'] = $userpass;
	}

	$silent = $temp;

	$debug_query = $db->Execute("SELECT * FROM $dbtables[presets] WHERE player_id=$playerinfo[player_id] order by info,preset");
	db_op_result($debug_query,__LINE__,__FILE__);
	$presettotal = $debug_query->RecordCount();
	$total = 0;

	while(!$debug_query->EOF){
		$presetinfo[$total] = $debug_query->fields['preset'];
		$presettext[$total] = $debug_query->fields['info'];
		$debug_query->MoveNext();
		$total++;
	}

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$shipinfo = $debug_query->fields;

	if ($shipinfo['cleared_defences'] > ' ')
	{
		header("location: $shipinfo[cleared_defences]\n");
	}

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[ship_types] WHERE type_id=$shipinfo[class]",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$classinfo = $debug_query->fields;

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$sectorinfo = $debug_query->fields;

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[zones] WHERE zone_id=$sectorinfo[zone_id]",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$zoneinfo = $debug_query->fields;

	if ($shipinfo['destroyed'] == "Y") // Check for destroyed ship
	{
		if ($shipinfo['dev_escapepod'] == "Y") // If the player has an escapepod, set the player up with a new ship.
		{
			player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $playerinfo['rating'], 0, 0);
			
			if ($spy_success_factor) // If there was a spy onboard, make sure its destroyed.
			{
				spy_ship_destroyed($shipinfo['ship_id'],0);
			}

		   if ($dig_success_factor)
		   {
			   dig_ship_destroyed($shipinfo['ship_id'],0);
		   }

			$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
			db_op_result($debug_query,__LINE__,__FILE__);

			echo $l_login_died;
			$flag = 1;
		}
		else
		{
			// if the player doesn't have an escapepod - they're dead, delete them.
			// uhhh  don't delete them to prevent self-distruct inherit
			echo $l_global_died;

			echo $l_die_please;
			$flag = 1;
		}
	}

	if ($server_closed && $flag == 0)
	{
		echo $l_login_closed_message;
		$flag = 1;
	}

	if($shipinfo['destroyed'] == "K" and $flag == 0){
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET destroyed='N' where ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$shipinfo['destroyed'] = "N";
	}

	return $flag;
}

function playerlog($sid, $log_type, $data = '')
{
	global $db, $dbtables;

	// write log_entry to the player's log - identified by player's player_id - sid.
	if ($sid != '' && !empty($log_type))
	{
		$stamp = date("Y-m-d H:i:s");
		$data = addslashes($data);
		$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES($sid, $log_type, '$stamp', '$data')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

function adminlog($log_type, $data = '')
{
	global $db, $dbtables;

	// Failures should be silent, since its the admin log.
	$silent = 1;

	// write log_entry to the admin log
	if (!empty($log_type))
	{
		$stamp = date("Y-m-d H:i:s");
		$data = addslashes($data);
		$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES(0, $log_type, '$stamp', '$data')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

function gen_score($sid)
{
	if ($sid != '')
	{
		global $dev_genesis_price, $dev_emerwarp_price, $dev_warpedit_price;
		global $dev_minedeflector_price, $dev_escapepod_price,$dev_nova_price, $dev_fuelscoop_price;
		global $fighter_price, $torpedo_price, $armour_price, $colonist_price;
		global $base_ore, $base_goods, $base_organics, $base_credits, $torpedo_price, $fighter_price;
		global $ore_price, $organics_price, $goods_price, $energy_price, $dig_price, $dev_probe;
		global $upgrade_cost, $upgrade_factor;
		global $db, $dbtables;
		global $spy_price; // Used by spies.
		$score="";
		$calc_hull = "ROUND(pow($upgrade_factor,$dbtables[ships].hull))";
		$calc_engines = "ROUND(pow($upgrade_factor,$dbtables[ships].engines))";
		$calc_power = "ROUND(pow($upgrade_factor,$dbtables[ships].power))";
		$calc_computer = "ROUND(pow($upgrade_factor,$dbtables[ships].computer))";
		$calc_sensors = "ROUND(pow($upgrade_factor,$dbtables[ships].sensors))";
		$calc_beams = "ROUND(pow($upgrade_factor,$dbtables[ships].beams))";
		$calc_torp_launchers = "ROUND(pow($upgrade_factor,$dbtables[ships].torp_launchers))";
		$calc_shields = "ROUND(pow($upgrade_factor,$dbtables[ships].shields))";
		$calc_armour = "ROUND(pow($upgrade_factor,$dbtables[ships].armour))";
		$calc_cloak = "ROUND(pow($upgrade_factor,$dbtables[ships].cloak))";
		$calc_ecm = "ROUND(pow($upgrade_factor,$dbtables[ships].ecm))";
		$calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak+$calc_ecm)*$upgrade_cost";
	
		$calc_torps = "$dbtables[ships].torps*$torpedo_price";
		$calc_armour_pts = "$dbtables[ships].armour_pts*$armour_price";
		$calc_ship_ore = "$dbtables[ships].ore*$ore_price";
		$calc_ship_organics = "$dbtables[ships].organics*$organics_price";
		$calc_ship_goods = "$dbtables[ships].goods*$goods_price";
		$calc_ship_energy = "$dbtables[ships].energy*$energy_price";
		$calc_ship_colonists = "$dbtables[ships].colonists*$colonist_price";
		$calc_ship_fighters = "$dbtables[ships].fighters*$fighter_price";
		$calc_equip = "$calc_torps+$calc_armour_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";
	
		$calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
		$calc_dev_genesis = "dev_genesis*$dev_genesis_price";
		$calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
		$calc_dev_escapepod = "if (dev_escapepod='Y', $dev_escapepod_price, 0)";
		$calc_dev_fuelscoop = "if (dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
		$calc_dev_nova = "IF(dev_nova='Y', $dev_nova_price, 0)";
		$calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
		$calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_nova+$calc_dev_minedeflector";
	
		$calc_planet_goods = "SUM($dbtables[planets].organics)*$organics_price+SUM($dbtables[planets].ore)*$ore_price+SUM($dbtables[planets].goods)*$goods_price+SUM($dbtables[planets].energy)*$energy_price";
		$calc_planet_colonists = "SUM($dbtables[planets].colonists)*$colonist_price";
		$calc_planet_defence = "SUM($dbtables[planets].fighters)*$fighter_price+if ($dbtables[planets].base='Y', $base_credits+SUM($dbtables[planets].torps)*$torpedo_price, 0)";
		$calc_planet_credits = "SUM($dbtables[planets].credits)";
	
		$calc_planet_computer = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].computer)))";
		$calc_planet_sensors = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].sensors)))";
		$calc_planet_beams = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].beams)))";
		$calc_planet_torp_launchers = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].torp_launchers)))";
		$calc_planet_shields = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].shields)))";
		$calc_planet_jammer = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].jammer)))";
		$calc_planet_cloak = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].cloak)))";
		 //Add $calc_planet_armour if necessary
		$calc_planet_def_levels = "($calc_planet_computer+$calc_planet_sensors+$calc_planet_beams+$calc_planet_torp_launchers+$calc_planet_shields+$calc_planet_jammer+$calc_planet_cloak)*$upgrade_cost";
   
		$debug_query = $db->Execute("SELECT $calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits+$calc_planet_def_levels AS score1 FROM $dbtables[players] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=$dbtables[players].player_id  WHERE $dbtables[players].player_id=$sid ");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score = $row['score1'];
		
	 // Loop through all ships for total ranking
	 
	 $res2 = $db->Execute("SELECT $calc_levels+$calc_equip+$calc_dev AS score2 FROM $dbtables[players]  LEFT JOIN $dbtables[ships] ON $dbtables[players].player_id=$dbtables[ships].player_id WHERE $dbtables[players].player_id=$sid AND destroyed='N'");
	db_op_result($res2,__LINE__,__FILE__);

	while (!$res2->EOF)
		{
		$row2 = $res2->fields;
		$score += $row2['score2'];
		$res2->MoveNext();
				
		}		
		
	// End Ship Score loop
	  $debug_query = $db->Execute("SELECT credits FROM $dbtables[players] where player_id = $sid");
		db_op_result($debug_query,__LINE__,__FILE__);
	 if ($debug_query)
		{
			$row = $debug_query->fields;
			$score += $row['credits'];
		}

	
		$debug_query = $db->Execute("SELECT balance, loan FROM $dbtables[ibank_accounts] where player_id = $sid");
		db_op_result($debug_query,__LINE__,__FILE__);
	
		if ($debug_query)
		{
			$row = $debug_query->fields;
			$score += ($row['balance'] - $row['loan']);
		}
	
		// This function checks the number of spies the player has, and adds their cost to the score calculation.
		$debug_query = $db->Execute("SELECT * FROM $dbtables[spies] where owner_id = $sid");
		db_op_result($debug_query,__LINE__,__FILE__);
	
		if ($debug_query)
		{
			$spies = $debug_query->RecordCount();
			$score += ($spies * $spy_price);
		}
	
		// This function checks the number of dignitaries the player has, and adds their cost to the score calculation.
		$debug_query = $db->Execute("SELECT * FROM $dbtables[dignitary] where owner_id = $sid");
		db_op_result($debug_query,__LINE__,__FILE__);
	
		if ($debug_query)
		{
			$digs = $debug_query->RecordCount();
			$score += ($digs * $dig_price);
		}

		// Add in Sector Defense values
		$debug_query = $db->Execute("SELECT (SUM(quantity)*$fighter_price) AS score6 FROM $dbtables[sector_defence] WHERE player_id=$sid and defence_type='F'");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score += $row['score6'];
//echo $row['score6']."<br>";
		$debug_query = $db->Execute("SELECT (SUM(quantity)*$torpedo_price) AS score7 FROM $dbtables[sector_defence] WHERE player_id=$sid and defence_type='M'");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score += $row['score7'];
//echo $row['score7']."<br><br>";

		// Add in Cargo Hull values
		$calc_levels = "((SUM(ROUND(pow($upgrade_factor,cargo_hull))) + SUM(ROUND(pow($upgrade_factor,cargo_power))))*$upgrade_cost)";
		$calc_cargo_ship = "SUM(IF(cargo_hull!=0, 116383500, 0))";
		$debug_query = $db->Execute("SELECT $calc_levels+$calc_cargo_ship AS score3 FROM $dbtables[planets] WHERE owner=$sid");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score += $row['score3'];

		// Add in Probe values
		$calc_levels = "((SUM(ROUND(pow($upgrade_factor,engines))) + SUM(ROUND(pow($upgrade_factor,sensors))) + SUM(ROUND(pow($upgrade_factor,cloak))))*$upgrade_cost)";
		$debug_query = $db->Execute("SELECT $calc_levels AS score4 FROM $dbtables[probe] WHERE owner_id=$sid");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score += $row['score4'];
		$debug_query = $db->Execute("SELECT probe_id FROM $dbtables[probe] WHERE owner_id=$sid");
		db_op_result($debug_query,__LINE__,__FILE__);
		$num = $debug_query->recordcount();
		$score += $num * $dev_probe;

		$debug_query = $db->Execute("SELECT SUM(amount) AS score7 FROM $dbtables[bounty] WHERE placed_by=$sid");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row = $debug_query->fields;
		$score += $row['score7'];

		$score = sign($score) * ROUND(SQRT(ABS($score)));
	
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET score=$score WHERE player_id=$sid");
		db_op_result($debug_query,__LINE__,__FILE__);

		return $score;
	}
	else
	{
		return 0;
	}
}

function player_ship_destroyed($ship_id, $target_id, $target_rating, $killer_id, $killer_rating){

	global $db, $dbtables, $start_armour, $start_energy, $start_fighters, $deathlostpercent;

	$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE currentship=$ship_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$player_id = $debug_query->fields['player_id'];
	$credits = $debug_query->fields['credits'];
	$shiplosses = $debug_query->fields['ship_losses'];

	$debug_query2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
	db_op_result($debug_query2,__LINE__,__FILE__);
	$class = $debug_query2->fields['class'];

	$price_modifier_base = explode("|", $shiplosses);
	for($i = 0; $i < count($price_modifier_base); $i++){
		$items = explode("-", $price_modifier_base[$i]);
		$element = $items[0];
		$price_modifier[$element] = $items[1];
		if($price_modifier[$element] < 1)
			$price_modifier[$element] = 0;
	}

	$res = $db->Execute("SELECT type_id FROM $dbtables[ship_types] WHERE buyable = 'Y'");
	$type_count = $res->RecordCount(); 

	$new_losses = "";
	for($i = 0; $i < $type_count; $i++){
		$element = $res->fields['type_id'];
		if($price_modifier[$element] < 1)
			$price_modifier[$element] = 0;
		if($element == $class)
			$price_modifier[$element]++;
		$new_losses .= $element . "-" . ($price_modifier[$element]);
		if($i != ($type_count - 1))
			$new_losses .= "|";
		$res->MoveNext();
	}

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET destroyed='K', class=10, basehull=10, hull=0,engines=0,power=0,sensors=0,computer=0,beams=0,torp_launchers=0,torps=0,armour=0,armour_pts=$start_armour,cloak=0,shields=0,ecm=0,sector_id=1,organics=0,ore=0,goods=0,energy=$start_energy,colonists=0,fighters=$start_fighters,dev_warpedit=0,dev_genesis=0,dev_beacon=0,dev_emerwarp=0,dev_escapepod='N',dev_fuelscoop='N',dev_nova='N',dev_minedeflector=0,on_planet='N', hull_normal=0,engines_normal=0,power_normal=0,sensors_normal=0,computer_normal=0,beams_normal=0,torp_launchers_normal=0,armour_normal=0,cloak_normal=0,shields_normal=0, ecm_normal=0 WHERE ship_id=$ship_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET ship_losses='$new_losses', rating='$target_rating', deaths=deaths+1 WHERE player_id=$target_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	if($killer_id != 0){
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET rating='$killer_rating', kills=kills+1 WHERE player_id=$killer_id");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	$torps = 90;
	$fighters = 100;
	$armor = 150;
	$energy = 200;
	$spy = 300;
	$wormhole = 350;
	$sg = 500;
	$nova = 550;

	$torps_trigger = floor($torps / 2);
	$fighters_trigger = floor($fighters / 2);
	$armor_trigger = floor($armor / 2);
	$energy_trigger = floor($energy / 2);
	$spy_trigger = floor($spy / 2);
	$wormhole_trigger = floor($wormhole / 2);
	$sg_trigger = floor($sg_torp / 2);
	$nova_trigger = floor($nova / 2);

	$flag = 1;

	$amount = round($credits * (mt_rand(($deathlostpercent / 2), $deathlostpercent) / 100));

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$amount WHERE player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debris_type = 14;
	$debris_data = $amount;

	$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
	$totrecs=$findem->RecordCount(); 
	$getit=$findem->GetArray();

	$randplay=mt_rand(0,($totrecs-1));
	$targetlink = $getit[$randplay]['sector_id'];
	$debug_query = $db->Execute("INSERT INTO $dbtables[debris] (debris_type, debris_data, sector_id) values ($debris_type,'$debris_data', $targetlink)");
	db_op_result($debug_query,__LINE__,__FILE__);

	$success = mt_rand(0, $torps);
	if ($success == $torps_trigger && $flag)
	{
		$debris_type = 2;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $fighters);
	if ($success == $fighters_trigger && $flag)
	{
		$debris_type = 3;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $armor);
	if ($success == $armor_trigger && $flag)
	{
		$debris_type = 4;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $energy);
	if ($success == $energy_trigger && $flag)
	{
		$debris_type = 5;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $spy);
	if ($success == $spy_trigger && $flag)
	{
		$debris_type = 7;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $wormhole);
	if ($success == $wormhole_trigger && $flag)
	{
		$debris_type = 8;
		$debris_data = 1;
		$flag = 0;
	}

	$success = mt_rand(0, $sg);
	if ($success == $sg_trigger && $flag)
	{
		$debris_type = 11;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	$success = mt_rand(0, $nova);
	if ($success == $nova_trigger && $flag)
	{
		$debris_type = 12;
		$debris_data = 1;
		if(mt_rand(0, 1) == 1)
			$debris_data = -1 * $debris_data;
		$flag = 0;
	}

	if ($flag)
	{
		$debris_type = 0;
		$debris_data = 0;
	}

	$randplay=mt_rand(0,($totrecs-1));
	$targetlink = $getit[$randplay]['sector_id'];
	$debug_query = $db->Execute("INSERT INTO $dbtables[debris] (debris_type, debris_data, sector_id) values ($debris_type,'$debris_data', $targetlink)");
	db_op_result($debug_query,__LINE__,__FILE__);
}



function db_kill_player($player_id, $killer_id, $killer_rating)
{
	global $default_prod_ore, $default_prod_organics, $default_prod_goods, $default_prod_energy;
	global $default_prod_fighters, $default_prod_torp;
	global $db, $dbtables;
	global $l_killheadline, $l_news_killed, $langdir;
	global $spy_success_factor; // Used for spies

	include ("languages/$langdir/lang_common.inc");
	include ("languages/$langdir/lang_sched_news.inc");

	if($killer_id != 0){
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET rating='$killer_rating', kills=kills+1 WHERE player_id=$killer_id");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
	$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id='$player_id'");
	db_op_result($debug_query,__LINE__,__FILE__);
	$killedplayer = $debug_query->fields;

	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_topics] WHERE post_player_id=$killedplayer[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_posts] WHERE post_player_id=$killedplayer[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_posts_text] WHERE post_player_id=$killedplayer[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	if($killedplayer['player_id'] == $killedplayer['team']){
		$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$killedplayer[team]");
		$team = $result_team->fields;
		$res = $db->Execute("SELECT COUNT(*) as number_of_members
					FROM $dbtables[players]
					WHERE team = $team[creator]");
		db_op_result($res,__LINE__,__FILE__);

		if ($res->fields['number_of_members'] == 1) {
			$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("SELECT forum_id FROM $dbtables[forums] WHERE teams=$killedplayer[player_id]");
  			db_op_result($debug_query,__LINE__,__FILE__);
	 		$forum_id = $debug_query->fields['forum_id'];
			$debug_query = $db->Execute("DELETE FROM $dbtables[topics] WHERE forum_id=$forum_id");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("DELETE FROM $dbtables[posts] WHERE forum_id=$forum_id");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("DELETE FROM $dbtables[posts_text] WHERE forum_id=$forum_id");
  			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("DELETE FROM $dbtables[forums] WHERE teams=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("DELETE FROM $dbtables[teams] WHERE id=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("DELETE FROM $dbtables[zones] WHERE owner=$killedplayer[player_id] and team_zone='Y'");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0' WHERE player_id='$killedplayer[player_id]'");
  			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE team_invite=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=0 WHERE owner=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		} else {
		 	$res = $db->Execute("SELECT player_id FROM $dbtables[players] WHERE team=$killedplayer[player_id] and player_id != $killedplayer[player_id]");
			$newcreator = $res->fields['player_id'];

			$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$newcreator");
			$newcreatorname = $res->fields;
	  		$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$killedplayer[player_id]");
		 	db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[forums] SET teams=$newcreator WHERE teams=$killedplayer[player_id]");
		 	db_op_result($debug_query,__LINE__,__FILE__);
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0', last_team=$killedplayer[player_id], left_team_time='$stamp' WHERE player_id='$killedplayer[player_id]'");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE team_invite=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
 			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team=$newcreator WHERE team=$killedplayer[player_id]");
	 		db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[teams] SET creator=$newcreator,id=$newcreator WHERE id=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
 			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=$newcreator WHERE team=$killedplayer[player_id]");
	 		db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=0 WHERE owner=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("UPDATE $dbtables[zones] SET owner=$newcreator WHERE team_zone='Y' AND owner=$killedplayer[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			playerlog($newcreator, LOG_TEAM_LEAD,"$team[team_name]");
		}
	}

	$result1 = $db->Execute("SELECT * from $dbtables[planets] where owner = '$player_id' ");
	db_op_result($result1,__LINE__,__FILE__);

	if ($result1 > 0)
	{
		while (!$result1->EOF)
		{
			$row = $result1->fields;
			$result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and player_id <> '$player_id' ");
			db_op_result($result2,__LINE__,__FILE__);
			if ($result2 > 0)
			{
				while (!$result2->EOF )
				{
					$cur = $result2->fields;
					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
					db_op_result($debug_query,__LINE__,__FILE__);

					playerlog($cur[player_id], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
					$result2->MoveNext();
				}
			}
			$result1->MoveNext();
		}
	}

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET destroyed='Y',on_planet='N',sector_id=1,cleared_defences=' ' WHERE player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("DELETE from $dbtables[bounty] WHERE placed_by = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	// If I lost my ship, all spies on it are killed and the spy owners will get log messages about it.
	if ($spy_success_factor)
	{
		$debug_query = $db->Execute("SELECT ship_id FROM $dbtables[ships] WHERE player_id = $player_id");
		db_op_result($debug_query,__LINE__,__FILE__);
		$ship_id = $debug_query->fields['ship_id'];
		spy_ship_destroyed($ship_id,0);

		$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE owner_id = $player_id");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	if ($dig_success_factor)
	{
		$debug_query = $db->Execute("DELETE FROM $dbtables[dignitary] WHERE owner_id = $player_id");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	$debug_query = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner='$player_id' AND base='Y'");
	db_op_result($debug_query,__LINE__,__FILE__);
	$i = 0;

	while (!$debug_query->EOF && $debug_query)
	{
		$sectors[$i] = $debug_query->fields['sector_id'];
		$i++;
		$debug_query->MoveNext();
	}

	$debug_query = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE owner='$player_id'");
	while (!$debug_query->EOF && $debug_query)
	{
		planet_log($debug_query->fields['planet_id'],$player_id,$player_id,PLOG_OWNER_DEAD);
		$debug_query->MoveNext();
	}

	$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull=0,cargo_power=0,owner=2,team=0, base='N' WHERE owner=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	$debug_query = $db->Execute("DELETE FROM $dbtables[autotrades] WHERE owner = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	if (!empty($sectors))
	{
		foreach($sectors as $sector)
		{
			calc_ownership($sector);
		}
	}
	$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] where player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE team_zone='N' AND owner=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	$zone = $debug_query->fields;

	$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE zone_id=$zone[zone_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id='$player_id'");
	db_op_result($debug_query,__LINE__,__FILE__);
	$name = $debug_query->fields;

	$headline = $name['character_name'] . $l_killheadline;

	$newstext=str_replace("[name]",$name['character_name'],$l_news_killed);

	insert_news("$name[character_name]", $player_id, "killed");
}

function NUMBER($number, $decimals = 0)
{
	global $local_number_dec_point;
	global $local_number_thousands_sep;
	return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}

function NUM_HOLDS($level_hull)
{
	global $level_factor;
	return round(mypw($level_factor, $level_hull) * 10);
}

function NUM_ENERGY($level_power)
{
	global $level_factor;
	return round(mypw($level_factor, $level_power) * 50);
}

function NUM_FIGHTERS($level_computer)
{
	global $level_factor;
	return round(mypw($level_factor, $level_computer) * 10);
}

function NUM_TORPEDOES($level_torp_launchers)
{
	global $level_factor;
	return round(mypw($level_factor, $level_torp_launchers) * 10);
}

function NUM_ARMOUR($level_armour)
{
	global $level_factor;
	return round(mypw($level_factor, $level_armour) * 10);
}

function NUM_SENSORS($level_sensors)
{
	global $level_factor;
	return round(mypw($level_factor, $level_sensors) * 10);
}

function NUM_BEAMS($level_beams)
{
	global $level_factor;
	return round(mypw($level_factor, $level_beams) * 10);
}

function NUM_SHIELDS($level_shields)
{
	global $level_factor;
	return round(mypw($level_factor, $level_shields) * 10);
}

function SCAN_SUCCESS($level_scan, $level_cloak)
{
	return (10 + $level_scan - $level_cloak) * 5;
}

function SCAN_ERROR($level_scan, $level_cloak)
{
	global $scan_error_factor;

	$sc_error = (4 + $level_scan / 2 - $level_cloak / 2) * mt_rand($scan_error_factor - 2, $scan_error_factor + 2);

	if ($sc_error < 1)
	{
		$sc_error=1;
	}

	if ($sc_error > 99)
	{
		$sc_error=100;
	}

	return $sc_error;
}

function explode_mines($sector, $num_mines)
{
	global $db, $dbtables;

	$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='M' order by quantity ASC");
	db_op_result($result3,__LINE__,__FILE__);

	//Put the defence information into the array "defenceinfo"
	if ($result3 > 0)
	{
		while (!$result3->EOF && $num_mines > 0)
		{
			$row = $result3->fields;
			if ($row['quantity'] > $num_mines)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_mines where defence_id = $row[defence_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$num_mines = 0;
			}
			else
			{
				$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$num_mines -= $row['quantity'];
			}

			$result3->MoveNext();
		}
	}
}

function message_defence_owner($sector, $message)
{
	global $db, $dbtables;
	$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' ");
	db_op_result($result3,__LINE__,__FILE__);

	//Put the defence information into the array "defenceinfo"
	if ($result3 > 0)
	{
		while (!$result3->EOF)
		{
			playerlog($result3->fields['player_id'],LOG_RAW, $message);
			$result3->MoveNext();
		}
	}
}

function calc_ownership($sector)
{
	global $min_bases_to_own;
	global $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
	global $l_global_nochange;
	global $db, $dbtables;

	$res = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$sector");
	db_op_result($res,__LINE__,__FILE__);
	$zone = $res->fields;

	if($zone['zone_id'] == 3 or $zone['zone_id'] == 2)
			return $l_global_nzone;

	$res = $db->Execute("SELECT owner, team FROM $dbtables[planets] WHERE sector_id=$sector AND base='Y'");
	db_op_result($res,__LINE__,__FILE__);

	$res2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id=$sector");
	db_op_result($res2,__LINE__,__FILE__);

	if ($res > 0)
	{
		$num_bases = $res->RecordCount();
	}
	else
	{
		$num_bases = 0;
	}

	$i = 0;
	if ($num_bases > 0)
	{
		while (!$res->EOF)
		{
			$bases[$i] = $res->fields;
			$i++;
			$res->MoveNext();
		}
	}
	else
	{
//		$result = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
//		$sectorinfo = $result->fields;
//		if ($sectorinfo['zone_id'] > 2) // 1 is unowned, so we dont need to redo it. 2 is fed space, and protected.
//		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector AND zone_id > '2' ");
			db_op_result($debug_query,__LINE__,__FILE__);
//		}
			return $l_global_nzone;
	}

	$owner_num = 0;
	foreach ($bases as $curbase)
	{
		$curteam = -1;
		$curship = -1;
		$loop = 0;
		while ($loop < $owner_num)
		{
			if ($curbase['team'] != 0)
			{
				if ($owners[$loop]['type'] == 'C')
				{
					if ($owners[$loop]['id'] == $curbase[team])
					{
						$curteam = $loop;
						$owners[$loop]['num']++;
					}
				}
			}

			if ($owners[$loop]['type'] == 'S')
			{
				if ($owners[$loop]['id'] == $curbase['owner'])
				{
					$curship=$loop;
					$owners[$loop]['num']++;
				}
			}
			$loop++;
		}

		if ($curteam == -1)
		{
			if ($curbase['team'] != 0)
			{
				$curteam = $owner_num;
				$owner_num++;
				$owners[$curteam]['type'] = 'C';
				$owners[$curteam]['num'] = 1;
				$owners[$curteam]['id'] = $curbase['team'];
			}
		}

		if ($curship == -1)
		{
			if ($curbase['owner'] != 0)
			{
				$curship = $owner_num;
				$owner_num++;
				$owners[$curship]['type'] = 'S';
				$owners[$curship]['num'] = 1;
				$owners[$curship]['id'] = $curbase['owner'];
			}
		}
	}

	// We've got all the contenders with their bases.
	// Time to test for conflict

	$loop = 0;
	$nbteams = 0;
	$nbships = 0;

	while ($loop < $owner_num)
	{
		if ($owners[$loop]['type'] == 'C')
		{
			$nbteams++;
		}
		else
		{
			$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=" . $owners[$loop]['id']);
			db_op_result($res,__LINE__,__FILE__);		 
			if ($res && $res->RecordCount() != 0)
			{
				$curship = $res->fields;
				$ships[$nbships]=$owners[$loop]['id'];
				$steams[$nbships]=$curship['team'];
				$nbships++;
			}
		}
		$loop++;
	}

	// More than one team, war
	if ($nbteams > 1)
	{
		$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
		db_op_result($debug_query,__LINE__,__FILE__);

		return $l_global_warzone;
	}

	// More than one unallied ship, war
	$numunallied = 0;
	foreach ($steams as $team)
	{
		if ($team == 0)
		{
			$numunallied++;
		}
	}

	if ($numunallied > 1)
	{
		$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
		db_op_result($debug_query,__LINE__,__FILE__);
		return $l_global_warzone;
	}

	// Unallied ship, another team present, war
	if ($numunallied > 0 && $nbteams > 0)
	{
		$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
		db_op_result($debug_query,__LINE__,__FILE__);
		return $l_global_warzone;
	}

	// Unallied ship, another ship in a team, war
	if ($numunallied > 0)
	{
		$query = "SELECT team FROM $dbtables[players] WHERE (";
		$i = 0;
		foreach ($ships as $ship)
		{
			$query = $query . "player_id=$ship";
			$i++;

			if ($i != $nbships)
			{
				$query = $query . " OR ";
			}
			else
			{
				$query = $query . ")";
			}
		}

		$query = $query . " AND team!=0";
		$res = $db->Execute($query);
		db_op_result($res,__LINE__,__FILE__);

		if ($res->RecordCount() != 0)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return $l_global_warzone;
		}
	}

	// Ok, all bases are allied at this point. Let's make a winner.
	$winner = 0;
	$i = 1;
	while ($i < $owner_num)
	{
		if ($owners[$i]['num'] > $owners[$winner]['num'])
		{
			$winner = $i;
		}
		elseif ($owners[$i]['num'] == $owners[$winner]['num'])
		{
			if ($owners[$i]['type'] == 'C')
			{
				$winner = $i;
			}
		}
		$i++;
	}

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$sector'");
	db_op_result($res,__LINE__,__FILE__);
	$num_planets = $res->RecordCount();

	$min_bases_to_own = round (($num_planets+1)/2);

	if ($owners[$winner]['num'] < $min_bases_to_own)
	{
		$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
		db_op_result($debug_query,__LINE__,__FILE__);
		return $l_global_nzone;
	}

	if ($owners[$winner]['type'] == 'C')
	{
		$res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE team_zone='Y' && owner=" . $owners[$winner][id]);
		db_op_result($res,__LINE__,__FILE__);
		$zone = $res->fields;

		$res = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=" . $owners[$winner][id]);
		db_op_result($res,__LINE__,__FILE__);
		$team = $res->fields;

		$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
		db_op_result($debug_query,__LINE__,__FILE__);
		return "$l_global_team $team[team_name]!";
	}
	else
	{
		$onpar = 0;
		foreach ($owners as $curowner)
		{
			if ($curowner['type'] == 'S' && $curowner['id'] != $owners[$winner]['id'] && $curowner['num'] == $owners[winner]['num'])
			$onpar = 1;
			break;
		}

		// Two allies have the same number of bases
		if ($onpar == 1)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return $l_global_nzone;
		}
		else
		{
			$res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE team_zone='N' && owner=" . $owners[$winner]['id']);
			db_op_result($res,__LINE__,__FILE__);
			$zone = $res->fields;

			$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=" . $owners[$winner]['id']);
			db_op_result($res,__LINE__,__FILE__);
			$ship = $res->fields;

			if($zone['zone_id'] == '' or $zone['zone_id'] == 0)
				$zone['zone_id'] = 1;

			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return "$l_global_player $ship[character_name]!";
		}
	}
}

function player_insignia_name($a_username) 
{
	global $db, $dbtables, $username, $player_insignia;
	global $l_insignia, $langdir;

	$res = $db->Execute("SELECT score FROM $dbtables[players] WHERE email='$a_username'");
	db_op_result($res,__LINE__,__FILE__);
	include ("languages/$langdir/lang_global_funcs.inc");

	$player_score = $res->fields;

	$i = round( (log($player_score['score'] / 4000)) / log(2.5));
	if ($i > 19)
	{
		$i = 19;
	}

	if ($i < 0 || $player_score['score'] < 0)
	{
		$i = 0;
	}

	$player_insignia = $l_insignia[$i];

	if (!isset($player_insignia))
	{
		$player_insignia = end($l_insignia);
	}

	return $player_insignia;
}

function t_port($ptype) 
{
	global $l_ore, $l_none, $l_energy, $l_organics, $l_casino;
	global $l_goods, $l_upgrade_ports, $l_device_ports, $l_spacedock;

	switch ($ptype) 
	{
		case "ore":
			$ret = $l_ore;
			break;
		case "none":
			$ret = $l_none;
			break;
		case "energy":
			$ret = $l_energy;
			break;
		case "organics":
			$ret = $l_organics;
			break;
		case "goods":
			$ret = $l_goods;
			break;
		case "upgrades":
			$ret = $l_upgrade_ports;
			break;
		case "devices":
			$ret = $l_device_ports;
			break;
		case "spacedock":
			$ret = $l_spacedock;
			break;
		case "casino":
			$ret = $l_casino;
			break;
	}
	return $ret;
}

function stripnum($str)
{
	$str = (string)$str;
	$output = ereg_replace("[^0-9.]","",$str);
	return (float)$output;
}

function collect_bounty($attacker,$bounty_on)
{
	global $db, $dbtables;
	global $l_by_thefeds, $fed_bounty_count;
	$res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id ");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		while (!$res->EOF)
		{
			$bountydetails = $res->fields;

			if ($bountydetails['placed_by'] == 0)
   			{
				if ($bountydetails['fed_bounty_count'] < $fed_bounty_count)
				{
					$placed = $l_by_thefeds;
					if ($bountydetails['rating']<0){
						$diff=(100-abs($bountydetails['rating'])/100);
						if ($diff > 80){
							$mintax=80;
						}else{
							$mintax=NUMBER($diff);
						}
						$maxtax=90;
					}else{
						$mintax=0;
						$maxtax=40;
					}
					$taxpercent=mt_rand($maxtax , $maxtax)/100;
					$tax=$bountydetails['amount']*$taxpercent;
					$fedtot=$bountydetails['amount']-round($tax);
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $fedtot WHERE player_id = $attacker");
					db_op_result($debug_query,__LINE__,__FILE__);
					playerlog($attacker, LOG_BOUNTY_CLAIMED, "$fedtot|$bountydetails[character_name]|$placed");
					playerlog($attacker,LOG_BOUNTY_TAX_PAID,"$tax|$bountydetails[character_name]");
					$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
					playerlog($bountydetails['placed_by'],LOG_BOUNTY_PAID,"$bountydetails[amount]|$bountydetails[character_name]");
				}
			}else{
  				$res2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id = $bountydetails[placed_by]");
	   			db_op_result($res2,__LINE__,__FILE__);
		   		$placed = $res2->fields['character_name'];
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $attacker");
				db_op_result($debug_query,__LINE__,__FILE__);
				playerlog($attacker, LOG_BOUNTY_CLAIMED, "$bountydetails[amount]|$bountydetails[character_name]|$placed");
				$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				playerlog($bountydetails[placed_by],LOG_BOUNTY_PAID,"$bountydetails[amount]|$bountydetails[character_name]");
	   		}
			$res->MoveNext();
		}
	}
}

function cancel_bounty($bounty_on)
{
	global $db, $dbtables;
	$res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		while (!$res->EOF)
		{
			$bountydetails = $res->fields;
			if ($bountydetails['placed_by'] != 0)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $bountydetails[placed_by]");
				db_op_result($debug_query,__LINE__,__FILE__);
				playerlog($bountydetails['placed_by'],LOG_BOUNTY_CANCELLED,"$bountydetails[amount]|$bountydetails[character_name]");
				$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			$res->MoveNext();
		}
	}
}

function get_player($player_id)
{
	global $db, $dbtables;
	$res = $db->Execute("SELECT character_name from $dbtables[players] where player_id = $player_id");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		$row = $res->fields;
		$character_name = $row['character_name'];
		return $character_name;
	}
	else
	{
		return "Unknown";
	}
}

function get_player_from_ship($ship_id)
{
	global $db, $dbtables;
	$res = $db->Execute("SELECT character_name from $dbtables[ships] LEFT JOIN $dbtables[players] " .
						"ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE ship_id = $ship_id");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		$row = $res->fields;
		$character_name = $row['character_name'];
		return $character_name;
	}
	else
	{
		return "Unknown";
	}
}

function get_shipname_from_ship($ship_id)
{
	global $db, $dbtables;
	$res = $db->Execute("SELECT name from $dbtables[ships] LEFT JOIN $dbtables[players] " .
						"ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE ship_id = $ship_id");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		$row = $res->fields;
		$ship_name = $row['name'];
		return $ship_name;
	}
	else
	{
		return "Unknown";
	}
}

function get_shipclassname($ship_class_id)
{
	global $db, $dbtables;
	$res = $db->Execute("SELECT name from $dbtables[ship_types] WHERE type_id = $ship_class_id");
	db_op_result($res,__LINE__,__FILE__);
	if ($res)
	{
		$row = $res->fields;
		$ship_name = $row['name'];
		return $ship_name;
	}
	else
	{
		return "Unknown";
	}
}

function log_move($player_id,$ship_id,$source,$destination,$class,$error,$zone_id)
{
	global $db, $dbtables;

	$debug_query = $db->Execute("DELETE from $dbtables[movement_log] WHERE player_id = $player_id and source = $source");
	db_op_result($debug_query,__LINE__,__FILE__);

	$stamp = date("Y-m-d H:i:s");
	$debug_query = $db->Execute("INSERT INTO $dbtables[movement_log] (player_id,ship_id,source,time,destination,ship_class,error_factor,zone_id) VALUES ($player_id,$ship_id,$source,'$stamp',$destination,$class,$error,$zone_id)");
	db_op_result($debug_query,__LINE__,__FILE__);
}

function log_scan($player_id,$sector_id,$zone_id)
{
	global $db, $dbtables;

	// Check if the player has already scanned that sector - no need to double the db record
	$debug_query = $db->Execute("SELECT * FROM $dbtables[scan_log] WHERE player_id = '$player_id' and sector_id = '$sector_id' ");
	db_op_result($debug_query,__LINE__,__FILE__);
	$row = $debug_query->fields;

	$stamp = date("Y-m-d H:i:s");

	if ($debug_query->EOF)
	{
		$debug_query = $db->Execute("INSERT INTO $dbtables[scan_log] (player_id,sector_id,zone_id,time) VALUES ($player_id,$sector_id,$zone_id,'$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}else{
		$debug_query = $db->Execute("UPDATE $dbtables[scan_log] SET zone_id=$zone_id, time='$stamp' WHERE event_id=$row[event_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

function planet_log($planet,$owner,$player_id,$action)
{
	global $db, $dbtables;
	global $enhanced_logging;
	if ($enhanced_logging)
	{
		$res = $db->Execute("SELECT ip_address from $dbtables[players] WHERE player_id=$player_id");
		db_op_result($res,__LINE__,__FILE__);
		$ip_address = $res->fields['ip_address'];
		$stamp = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("INSERT INTO $dbtables[planet_log] (planet_id,player_id,owner_id,ip_address,action,time) VALUES ($planet,$player_id,$owner,'$ip_address',$action,'$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

function cleanjs($dontkill)
{
	global $smarty, $cleanjs;
	$cleanjs = "<script language=\"javascript\" type=\"text/javascript\">\n".
		 "function clean_js()\n".
		 "{\n".
		 "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n".
		 "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n".
		 "var form = document.forms[0];\n".
		 "var i = form.elements.length;\n".
		 "while (i > 0)\n".
		 " {\n".
		 " if ((form.elements[i-1].type == 'text') && (form.elements[i-1].name != '$dontkill'))\n".
		 "  {\n".
		 "  var tmpval = form.elements[i-1].value.replace(/\D+/g, \"\");\n".
		 "  if (tmpval != form.elements[i-1].value)\n".
		 "   {\n".
		 "   form.elements[i-1].value = form.elements[i-1].value.replace(/\D+/g, \"\");\n".
		 "   }\n".
		 "  }\n".
		 " if (form.elements[i-1].value == '')\n".
		 "  {\n".
		 "  form.elements[i-1].value ='0';\n".
		 "  }\n".
		 " i--;\n".
		 " }\n".
		 "}\n".
		 "</script>\n";
	$smarty->assign("clean_javascript", $cleanjs);
}

// Calculate the distance between two sectors.
// We even run the queries ourselves.
function calc_dist($src,$dst) 
{
	global $db, $dbtables, $enable_spiral_galaxy;
	if ($dst == '' or $src == '')
	{
		return 0;
	}

	$results = $db->Execute("SELECT x,y,z FROM ".$dbtables['universe'].
							" WHERE sector_id=$src OR sector_id=$dst");
	db_op_result($results,__LINE__,__FILE__);

	// Make sure you check for this when calling this function.
	if (!$results)
	{
		return 0;
	}

	$x = $results->fields['x'];
	$y = $results->fields['y'];
	$z = $results->fields['z'];

	$results->MoveNext();

	$x -= $results->fields['x'];
	$y -= $results->fields['y'];
	$z -= $results->fields['z'];

	if($enable_spiral_galaxy != 1){
		$x = sqrt(($x*$x) + ($y*$y) + ($z*$z));
	}else{
    	$x = sqrt(pow($x,2.0)+pow($y,2.0)+pow($z,2.0));
	}

// Make sure it's never less than 1.
	if ($x < 1) 
	{
		return 1;
	}

	return $x;
}

function MakeBars($level, $max, $dialtype)
{
	global $l_n_a, $templatename;

	if ($max == 0)
	{
		$max = 1;
	}
	$heath = ($level / $max);
	$heath_bars = round($heath * 10);

	$img = '';

	for ($i=0; $i<$heath_bars; $i++)
	{
		if($i < 10)
		{
			$img .= "<img src=templates/".$templatename."images/". $dialtype . "on$i.png>";
		}
		else
		{
			$img .= "<img src=templates/".$templatename."images/". $dialtype . "on9.png>";
		}
	}

	for ($i=0; $i<(10-$heath_bars); $i++)
	{
		$img .= "<img src=templates/".$templatename."images/". $dialtype . "off.png>";
	}

	if ($img == '')
	{
		$img = "<font size=2><b>$l_n_a</b></font>";
	}

	return $img;
}

function db_op_result($query,$served_line,$served_page)
{
	global $db, $dbtables, $silent, $_SERVER, $cumulative, $db_type;

	if (!(!$query->EOF && $query == ''))
	{
		if (!$silent)
		{
			echo "<font color=\"lime\">- operation completed successfully.</font><br>\n";
		}
	}
	else
	{
		$temp_error = $db->ErrorMsg();
		$dberror = "A Database error occurred in " . $served_page . " on line " . ($served_line-1) . " (called from: $_SERVER[PHP_SELF]): " . $temp_error . "";
		$dberror = ereg_replace("'","&#039;",$dberror); // Allows the use of apostrophes.
		adminlog(LOG_RAW, $dberror);
		$cumulative = 1; // For areas with multiple actions needing status - 0 is all good so far, 1 is at least one bad.

		if(strstr(strtolower($temp_error), "can't open file") and strstr(strtolower($temp_error), ".myi") and strstr($temp_error, "145")){
			$deoperror = 1;
			adminlog(LOG_RAW,"Running sched_repair.php to repair table.");
			include ("sched_repair.php");
		}

		if (!$silent)
		{
			echo "<font color=\"red\">- failed to complete database operation in $served_page on line " .($served_line-1). ". Error code follows:\n";
			echo "<hr>\n";
			echo $temp_error;
			echo "<hr>\n";
			echo "</font><br>\n";
		}
	}
}

function sign( $data )
{
	if ($data > 0)
	{
		return 1;
	}
	elseif ($data < 0)
	{
		return -1;
	}
	else
	{
		return 0;
	}
}

function AddELog($d_user,$e_type,$e_status,$e_subject,$e_response)
{
	global $username, $ip, $dbtables, $db;

	$result = $db->Execute("SELECT * from $dbtables[players] LEFT JOIN $dbtables[ships] " .
						   "ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE email='$username'");
	$playerinfo = $result->fields;

	$result2 = $db->Execute("SELECT * from $dbtables[players] LEFT JOIN $dbtables[ships] " .
							"ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE email='$d_user'");
	$targetinfo = $result2->fields;

	if ($e_type == 0) // For Normal Email, For Future Use.
	{
		$sp_id = $playerinfo['ship_id'];
		$sp_name = $playerinfo['name'];
		$sp_IP = $playerinfo['ip_address'];
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $targetinfo['name'];
	}
	elseif ($e_type == 1) // For when users Register.
	{
		$sp_id = -1;
		$sp_name = "Not Logged In";
		$sp_IP = $ip;
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $targetinfo['email'];
	}
	elseif ($e_type == 2) // For when users Send Feedback.
	{
		$sp_id = $playerinfo['ship_id'];
		$sp_name = $playerinfo['character_name'];
		$sp_IP = $playerinfo['ip_address'];
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $targetinfo['character_name'];
	}
	elseif ($e_type == 3) // For when users Request Password.
	{
		$sp_id = -1;
		$sp_name = "Not Logged In";
		$sp_IP = $ip;
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $targetinfo['email'];
	}
	elseif ($e_type == 4) // For when Debugging (Not Used yet).
	{
		$sp_id = -1;
		$sp_name = "GameAdmin";
		$sp_IP = $ip;
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $d_user;
	}
	elseif ($e_type == 5) // For sending Global Email to all registered players
	{
		$sp_id = -1;
		$sp_name = "GameAdmin";
		$sp_IP = $ip;
		$dp_id = $targetinfo['ship_id'];
		$dp_name = $d_user;
	}

	if ($e_response == '1') 
	{
		$e_response = "Sent OK";
	}

	if ($e_status == 'Y')
	{
		$attempt = '';
	}
	else
	{
		$attempt = " attempt";
	}

	$e_stamp = date("Y-m-d H:i:s");
	$dp_name = htmlspecialchars($dp_name,ENT_QUOTES);
	$sp_name = htmlspecialchars($sp_name,ENT_QUOTES);
	$e_subject = htmlspecialchars($e_subject,ENT_QUOTES);

	$debug_query = $db->Execute("INSERT INTO $dbtables[email_log] VALUES('','$sp_name','$sp_IP','$dp_name','$e_subject','$e_status','$e_type','$e_stamp','$e_response')");
	db_op_result($debug_query,__LINE__,__FILE__);
}

function insert_news($data, $user_id, $news_type)
{
	global $db, $dbtables;
	$total = 1;
	
	$result2 = $db->Execute("SELECT * from $dbtables[news] order by news_id DESC");
	$newsinfo = $result2->fields;
//print "$newsinfo[data] - $data<br>$newsinfo[news_type]<br>$newsinfo[user_id]<br>";
	if($newsinfo['data'] == $data and $newsinfo['news_type'] == $news_type and $newsinfo['user_id'] == $user_id){
		$total = $newsinfo['total'] + 1;
		$stamp = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("UPDATE $dbtables[news] set total='$total', date='$stamp' where news_id=$newsinfo[news_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}else{
		$stamp = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("INSERT INTO $dbtables[news] (data, total, user_id, date, news_type) VALUES ('$data', '$total', '$user_id', '$stamp', '$news_type')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

// Check the sector to see who is in control ignoring War Zone Status
// If a team or player controls the sector they will not get a bounty attacking
// lower ranked players or planets in the sector.
// Function returns the team number of the team owning the most planets.
// When checking if the player doesn't have a team use their player id instead.
// If the sector can have 5 planets you must own 3 based planets to control it.
// If the sector can have 4 planets you must own 3 based planets to control it.
// If the sector can have 3 planets you must own 2 based planets to control it.
// If the sector can have 2 planets you must own 2 based planets to control it.
// If the sector can have 1 planets you must own 1 based planets to control it.
// If the player is the owner of the sector or the player is the fenderation
// return a 1 for true.  If the player doesn't own the sector return a 0 for false.

function get_sector_bounty_control($sector){

	global $db, $dbtables, $playerinfo;

	if($playerinfo['player_id'] == 3) // if Fed ship let the feds do anything
		return 1;

	$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
	db_op_result($res,__LINE__,__FILE__);
	$max_planets = $res->fields['star_size'];

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$sector' and base='Y'");
	db_op_result($res,__LINE__,__FILE__);
	$num_planets = $res->RecordCount();

	if($num_planets == 0 or $max_planets == 0)
		return 0;

	$min_bases_to_own =  round(($max_planets+1)/2);
	$totalowners = 0;

	while(!$res->EOF){

		$owners = $res->fields;

		$res2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$owners[owner]");
		db_op_result($res,__LINE__,__FILE__);
		$playerteam = $res2->fields['team'];

		if($playerteam == 0){
			$ownernumber = $owners['owner'];
		}else{
			$ownernumber = $playerteam;
		}

		if($ownertotal[$ownernumber] == 0){
			$ownerlist[$totalowners] = $ownernumber;
			$totalowners++;
		}
		$ownertotal[$ownernumber]++;
		$res->MoveNext();
	}

	$selected = 0;
	$selectedcount = 0;
	for($i = 0; $i < $totalowners; $i++){
		$ownernumber = $ownerlist[$i];
		$count = $ownertotal[$ownernumber];
		if($selectedcount < $count){
			$selectedcount = $count;
			if ($selectedcount >= $min_bases_to_own){
				$selected = $ownernumber;
			}
		}
	}

	if($selected == $playerinfo['player_id'] or ($playerinfo['team'] != 0 and $selected == $playerinfo['team']))
		return 1;
	else return 0;
}

function get_dirlist($dirPath)
{
	if ($handle = opendir($dirPath)) 
	{
		while (false !== ($file = readdir($handle))) 
			if ($file != "." && $file != "..") 
				$filesArr[] = trim($file);
			closedir($handle);
	}
	return $filesArr; 
}

function trip_time($start, $end){
	global $shipinfo, $level_factor;
	$distance = calc_dist($start,$end);
	$shipspeed = mypw($level_factor, $shipinfo['engines']);
	$triptime = round($distance / $shipspeed);

	if ($triptime == 0)
	{
		$triptime = 1;
	}
	return $triptime;
}

function base_template_data(){

	global $db, $dbtables, $playerinfo, $planet, $sectorinfo, $enable_spiral_galaxy, $templatename;
	global $shipinfo, $smarty, $username, $l_unnamed, $l_unknown, $sector_max, $universe_size;
	global $ksm_allowed, $general_text_color, $basefontsize;
	global $general_highlight_color, $main_table_heading, $l_turns_have;
	global $l_turns_used, $l_score, $l_abord, $l_port;
	global $l_scan, $presetinfo, $presettotal, $presettext, $l_set;
	global $l_add, $forumdata, $forumplayer, $level_factor;
	global $l_planets, $l_device_ports, $l_sector_def;
	global $l_navcomp, $l_map, $l_log;
	global $l_read_msg, $l_send_msg, $l_rankings;
	global $l_teams, $l_name, $l_teamships;
	global $l_ohno, $l_options, $l_autotrade;
	global $l_probe;
	global $l_feedback, $spy_success_factor, $l_spy;
	global $dig_success_factor, $l_dig, $link_forums;
	global $l_forums, $l_logout, $l_main_warpto;
	global $l_no_warplink, $links, $l_fullscan;
	global $l_main_autoroute, $autototal, $autolist;
	global $l_realspace, $preset_display;
	global $l_commands, $l_traderoutes, $traderoute_display;
	global $traderoute_links, $num_traderoutes, $l_trade_control;
	global $l_none, $quickshout, $l_colonists;
	global $l_credits, $l_cargo, $l_goods;
	global $l_energy, $l_ore, $l_fighters, $num_defences;
	global $l_torps, $l_armourpts, $l_organics;
	global $l_logout, $langdir, $gamepath, $title;
	global $zoneinfo, $l_zname, $sectorinfo, $l_sector, $l_teamforum, $l_news_none;

	global $l_news_planets, $l_news_cols, $l_news_p_headline, $l_killheadline;
	global $l_news_killed;
	global $l_news_p_text5, $l_news_p_text10, $l_news_p_text25, $l_news_p_text50;
	global $l_news_c_text25, $l_news_c_text100, $l_news_c_text500, $l_news_c_text1000;
	global $l_created_universe, $l_created_universe_full;

	global $l_news_indi, $l_news_indi_short;
	global $l_news_nova_short, $l_news_nova;
	global $l_news_nova_m_short, $l_news_nova_m;
	global $l_news_bounty, $l_localmap;
	global $l_news_attackerpod_p_short, $l_news_attackerpod_P;
	global $l_news_attackerdie_p_short, $l_news_attackerdie_p;
	global $l_news_destroyed_p_short, $l_news_destroyed_p;
	global $l_news_defeated_p_short, $l_news_defeated_p;
	global $l_news_notdefeated_p_short, $l_news_notdefeated_p;
	global $l_news_targetepod_short, $l_news_targetepod;
	global $l_news_targetdies_short, $l_news_targetdies;

	global $l_youhave, $l_messages_wait, $l_block_msg, $l_armor;
	global $l_news_targetdies_short, $l_news_targetdies;
	global $gameroot, $langdir, $l_arm_weap, $l_3dmap, $enable_spiral_galaxy;

	global $l_spacebeacon, $l_spaceprobes, $l_warpeditors, $l_genesistorps, $l_sgtorps;
	global $l_minesfighters, $l_ewarp, $l_reports, $l_shipinfo, $l_sectornotes, $l_maps;
	global $l_messages, $l_shiptype, $l_help, $l_ship, $l_rank,$l_teamplanets,$l_teamdefences;
	global $l_planetstatus, $l_planetdefences, $l_changeproduction,$tile, $enable_spiral_galaxy;
	global $l_upgrade_ports, $l_device_ports, $l_spacedock, $l_casino, $l_none, $l_galacticarm;

$smarty->assign("l_planetstatus", $l_planetstatus);	
$smarty->assign("l_planetdefences", $l_planetdefences);	
$smarty->assign("l_changeproduction", $l_changeproduction);	
$smarty->assign("l_teamplanets", $l_teamplanets);
$smarty->assign("l_teamdefences", $l_teamdefences);
$smarty->assign("l_spacebeacon", $l_spacebeacon);
$smarty->assign("l_spaceprobes", $l_spaceprobes);
$smarty->assign("l_warpeditors", $l_warpeditors);
$smarty->assign("l_genesistorps", $l_genesistorps);
$smarty->assign("l_sgtorps", $l_sgtorps);
$smarty->assign("l_minesfighters", $l_minesfighters);
$smarty->assign("l_ewarp", $l_ewarp);
$smarty->assign("l_reports", $l_reports);
$smarty->assign("l_shipinfo", $l_shipinfo);
$smarty->assign("l_sectornotes", $l_sectornotes);
$smarty->assign("l_maps", $l_maps);
$smarty->assign("l_messages", $l_messages);
$smarty->assign("l_shiptype", $l_shiptype);
$smarty->assign("l_help", $l_help);
$smarty->assign("l_name", $l_name);
$smarty->assign("l_ship", $l_ship);
$smarty->assign("l_rank", $l_rank);

$res = $db->Execute("SELECT link_dest FROM $dbtables[links] WHERE link_start='$shipinfo[sector_id]' ORDER BY link_dest ASC");

$i = 0;
if ($res > 0)
{
	calc_ownership($shipinfo['sector_id']);
	while (!$res->EOF)
	{
		$links[$i] = $res->fields['link_dest'];
		$i++;
		$res->MoveNext();
	}
}

$num_links = $i;

if($playerinfo['team'] != 0){
	$result = $db->Execute("SELECT icon FROM $dbtables[teams] WHERE id=$playerinfo[team]");
	$teamicon = $result->fields['icon'];
}else{
	$teamicon="default_icon.gif";
}

$startdate = date("Y/m/d");
$shoutcount = 0;
$res = $db->Execute("SELECT sb_text, player_name FROM $dbtables[shoutbox] WHERE sb_alli = 0 ORDER BY sb_date desc  LIMIT 0,5");

if($res->EOF)
{
	$shoutmessage[$shoutcount] = $l_news_none;
	$shoutposter[$shoutcount] = "";
	$shoutcount++;
}
else
{
	while (!$res->EOF) 
	{
		$row = $res->fields;
		$newsdata = stripslashes(rawurldecode($row['sb_text']));
		$shoutmessage[$shoutcount] = $newsdata;
		$shoutposter[$shoutcount] = $row['player_name'];
		$shoutcount++;
		$res->MoveNext();
	}
}

$smarty->assign("shoutcount", $shoutcount);
$smarty->assign("shoutmessage", $shoutmessage);
$smarty->assign("shoutposter", $shoutposter);

$res = $db->Execute("SELECT autoroute_id, start_sector, destination FROM $dbtables[autoroutes] WHERE player_id=$playerinfo[player_id] ");
$autototal = $res->recordcount();

$counterlist = 0;
while(!$res->EOF)
{
	$autoroute = $res->fields;
	$autolist[$counterlist] = $autoroute['autoroute_id'];
	$autostart[$counterlist] = $autoroute['start_sector'];
	$autoend[$counterlist] = $autoroute['destination'];
	$counterlist++;
	$res->MoveNext();
}

$newposts = 0;
if($playerinfo['team'] != 0){
	$debug_query = $db->Execute("select lastonline from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$forumplayer = $debug_query->fields;

	$debug_query = $db->Execute("select forum_id from $dbtables[forums] where teams=$playerinfo[team]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$forumdata = $debug_query->fields;

	$query2 = $db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id] and post_time>='$forumplayer[lastonline]' order by post_time");
	db_op_result($query2,__LINE__,__FILE__);
	$newposts = $query2->RecordCount();
}

$i = 0;
$num_traderoutes = 0;

/********* Port query ************************************ begin *********/
$query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE source_type='P' AND source_id=$shipinfo[sector_id] AND owner=$playerinfo[player_id] ORDER BY dest_id ASC");
while (!$query->EOF)
{
	$traderoutes[$i] = $query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}
/********* Port query ************************************ end **********/

/********* Personal and team planet traderoute type query ********** begin ********/
$query = $db->Execute("SELECT * FROM $dbtables[planets], $dbtables[traderoutes] WHERE (source_type='L' OR source_type='C') AND source_id=$dbtables[planets].planet_id AND $dbtables[planets].sector_id=$shipinfo[sector_id] AND $dbtables[traderoutes].owner=$playerinfo[player_id]");
while (!$query->EOF)
{
	$traderoutes[$i] = $query->fields;
	$i++;
	$num_traderoutes++;
	$query->MoveNext();
}
/********* Personal planet traderoute type query ********* end **********/

$counterroutes = 0;

if ($num_traderoutes != 0)
{
	$i = 0;
	while ($i < $num_traderoutes)
	{
		$traderoute_links[$counterroutes] = $traderoutes[$i]['traderoute_id'];
		if ($traderoutes[$i]['source_type'] == 'P')
		{
			$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . "$l_port&nbsp;";
		}
		else
		{
			$query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i]['source_id']);
			if (!$query || $query->RecordCount() == 0)
			{
				$traderoute_display[$counterroutes]  = $traderoute_display . $l_unknown;
			}
			else
			{
				$planet = $query->fields;
				if ($planet['name'] == "")
				{
					$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . "$l_unnamed ";
				}
				else
				{
					$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . "$planet[name] ";
				}
			}
		}

		if ($traderoutes[$i]['circuit'] == '1')
		{
			$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . "=&gt;&nbsp;";
		}
		else
		{
			$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . "&lt;=&gt;&nbsp;";
		}

		if ($traderoutes[$i]['dest_type'] == 'P')
		{
			$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . $traderoutes[$i]['dest_id'];
		}
		else
		{
			$query = $db->Execute("SELECT name FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i]['dest_id']);
			if (!$query || $query->RecordCount() == 0)
			{
				$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . $l_unknown;
			}
			else
			{
				$planet = $query->fields;
				if ($planet['name'] == "")
				{
					$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . $l_unnamed;
				}
				else
				{
					$traderoute_display[$counterroutes]  = $traderoute_display[$counterroutes]  . $planet['name'];
				}
			}
		}
		$counterroutes++;
		$i++;
	}
}

$quickshout = "";
$res2 = $db->Execute("SELECT sb_text FROM $dbtables[shoutbox] WHERE sb_alli = " . (($playerinfo['team']<=0)?-1:$playerinfo['team']) . " ORDER BY sb_date DESC LIMIT 0,5");
$countflag=0;
   if (!$res2 || $res2->RecordCount() != 0){
   $countflag++;
   } 
   if ($countflag > 0){
	for ( $i = 0 ; $i < 5 ; $i++ )
	{
	if (!$res2->EOF)
			{
				$row2 = $res2->fields;
				 $quickshout .= strip_tags(stripslashes(rawurldecode($row2['sb_text'])))."\n";
  				if (!$res2->EOF)
					{
					$quickshout .= "---------------------------------\n";
					$res2->MoveNext();
					}
			}		
		}	
	}

include("languages/$langdir/lang_news.inc");
include_once ("includes/newsservices.php");

$startdate = date("Y-m-d");

$newscount = 0;

$res = $db->Execute("SELECT * from $dbtables[news] where LEFT(date,10) = '$startdate' order by news_id desc");
if($res->EOF)
{
	$newsmessage[$newscount] = $l_news_none;
	$newscount++;
}
else
{
	while (!$res->EOF) 
	{
		$row = $res->fields;
		$newsdata = translate_news($row);
		$newsmessage[$newscount] = $newsdata['headline'];
		$newscount++;
		$res->MoveNext();
	}
}

$smarty->assign("newscount", $newscount);
$smarty->assign("newsmessage", $newsmessage);

/*
	Add in 5 X 5 map element.
*/

if($sectorinfo['sg_sector'] != 1){
	$text['ore']	  =$l_ore;
	$text['goods']	=$l_goods;
	$text['organics'] =$l_organics;
	$text['energy']   =$l_energy;
	$text['upgrades'] =$l_upgrade_ports;
	$text['devices']  =$l_device_ports;
	$text['spacedock']  =$l_spacedock;
	$text['casino']  =$l_casino;
	$text['none']	 =$l_none;

	$isnewnav = 0;
	$query2 = "SELECT spiral_arm, dest_sector, port_type, zone_id, x, y, z, distance FROM $dbtables[navmap], $dbtables[universe] where $dbtables[navmap].start_sector=$shipinfo[sector_id] and $dbtables[universe].sector_id = $dbtables[navmap].dest_sector ORDER BY $dbtables[navmap].distance, $dbtables[navmap].dest_sector ASC";
	$result = $db->Execute ($query2);
	db_op_result($result,__LINE__,__FILE__);

	if($result->RecordCount() == 0){
		$distance = $universe_size;
		if($enable_spiral_galaxy != 1){
			$query2 = "SELECT sector_id as dest_sector, port_type, zone_id, x, y, z,SQRT((($sectorinfo[x]-x) * ($sectorinfo[x]-x))+(($sectorinfo[y]-y) * ($sectorinfo[y]-y))+(($sectorinfo[z]-z) * ($sectorinfo[z]-z))) as distance FROM $dbtables[universe] where SQRT((($sectorinfo[x]-x) * ($sectorinfo[x]-x))+(($sectorinfo[y]-y) * ($sectorinfo[y]-y))+(($sectorinfo[z]-z) * ($sectorinfo[z]-z))) < $distance and sector_id!=$shipinfo[sector_id]  and sg_sector != 1  ORDER BY distance ASC,sector_id DESC limit 0,24";
		}
		else
		{
			$query2 = "SELECT spiral_arm, sector_id as dest_sector, port_type, zone_id, x, y, z,SQRT(POW(($sectorinfo[x]-x),2)+POW(($sectorinfo[y]-y),2)+POW(($sectorinfo[z]-z),2)) as distance FROM $dbtables[universe] where SQRT(POW(($sectorinfo[x]-x),2)+POW(($sectorinfo[y]-y),2)+POW(($sectorinfo[z]-z),2)) < $distance and sector_id!=$shipinfo[sector_id]  and sg_sector != 1  ORDER BY distance ASC,sector_id DESC limit 0,24";
		}

		$result = $db->Execute ($query2);
		db_op_result($result,__LINE__,__FILE__);
		$isnewnav = 1;
	}
	db_op_result($result,__LINE__,__FILE__);

	$mapsectorcount2 = $result->RecordCount();
	$movement_where = "(";
	$scan_where = "(";
	$row = $result->GetArray();
	for ($nav = 0; $nav < $mapsectorcount2; $nav++)
	{
		$movement_where .= "source = " . $row[$nav]['dest_sector'] . " or ";
		$scan_where .= "sector_id = " . $row[$nav]['dest_sector'] . " or ";
	}

	$movement_where .= "source = -1)";
	$scan_where .= "sector_id = -1)";

	$result2 = $db->Execute("SELECT distinct source, zone_id, time FROM $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] and $movement_where order by source ASC");
	db_op_result($result2,__LINE__,__FILE__);
	$zone_where = "";
	while (!$result2->EOF) 
	{
		$row2 = $result2->fields;
		$temp = $row2['source'];
		$movementzone[$temp] = $row2['zone_id'];
		$movementtime[$temp] = strtotime($row2['time']);
		$zone_where .= "zone_id = " . $row2['zone_id'] . " or ";
		$result2->Movenext();
	}

	$result3 = $db->Execute("SELECT distinct sector_id, zone_id, time FROM $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] and $scan_where order by sector_id ASC");
	while (!$result3->EOF) 
	{
		$row3 = $result3->fields;
		$temp = $row3['sector_id'];
		$scanzone[$temp] = $row3['zone_id'];
		$scantime[$temp] = strtotime($row3['time']);
		$zone_where .= "zone_id = " . $row3['zone_id'] . " or ";
		$result3->Movenext();
	}
	$zone_where .= "zone_id = -1";

	$result4 = $db->Execute ("SELECT distinct zone_id, zone_color, zone_name FROM $dbtables[zones] WHERE $zone_where ORDER BY zone_name ASC");
	$totalzones = 0;

	while (!$result4->EOF) 
	{
		$row4 = $result4->fields;
		$temp = $row4['zone_id'];
		$zoneinfo[$temp] = $row4['zone_color'];
		$zonename[$temp] = $row4['zone_name'];
		$zonenumber[$totalzones] = $temp;
		$totalzones++;
		$result4->Movenext();
	}

	$mapsectorcount = 1;
	for ($nav = 0; $nav < $mapsectorcount2; $nav++)
	{
		$sectorid[$mapsectorcount] = $row[$nav]['dest_sector'];
		$position[$mapsectorcount]= $row[$nav]['x']."|".$row[$nav]['y']."|".$row[$nav]['z'];
		$galacticarm[$mapsectorcount]= $row[$nav]['spiral_arm'];

		$port= "unknown";

		$shipspeed = mypw($level_factor, $shipinfo['engines']);
		$triptime = round($row[$nav]['distance'] / $shipspeed);

		if ($triptime == 0)
		{
			$triptime = 1;
		}

		$alt = "$row[dest_sector] - $l_unknown - " . round($triptime) . " realspace turns";
		$altsector[$mapsectorcount] = $row[$nav]['dest_sector'];
		$altport[$mapsectorcount] = $l_unknown;
		$altturns[$mapsectorcount] = round($triptime) . " realspace turns";
		$zonecolor = "#000000";

		$tempsector = $row[$nav]['dest_sector'];

		if($movementtime[$tempsector] >= $scantime[$tempsector]){
			if ($movementzone[$tempsector] > 0 )
			{
				$temp = $movementzone[$tempsector];
				$zonecolor = $zoneinfo[$temp];
				$port = $row[$nav]['port_type'];
				$alt  = "$row[$nav][dest_sector] - $text[$port] - ". addslashes($zonename[$temp]) . " - " . round($triptime) . " realspace turns";
				$altsector[$mapsectorcount]  = $row[$nav]['dest_sector'];
				$altport[$mapsectorcount]  = $text[$port];
				$altturns[$mapsectorcount]  = round($triptime) . " realspace turns";
			}
		}else{
			if ($scanzone[$tempsector] > 0)
			{
				$temp = $scanzone[$tempsector];
				$zonecolor = $zoneinfo[$temp];
				$port = $row[$nav]['port_type'];
				$alt  = "$row[$nav][dest_sector] - $text[$port] - ". addslashes($zonename[$temp]) . " - " . round($triptime) . " realspace turns";
				$altsector[$mapsectorcount]  = $row[$nav]['dest_sector'];
				$altport[$mapsectorcount]  = $text[$port];
				$altturns[$mapsectorcount]  = round($triptime) . " realspace turns";
			}
		}

		$sectorzonecolor[$mapsectorcount] = $zonecolor;
		$sectorimage[$mapsectorcount] = $tile[$port];
		$sectortitle[$mapsectorcount] = $alt;
		$mapsectorcount++;

		if($isnewnav == 1)
		{
			$triptime = round(calc_dist($shipinfo['sector_id'], $row[$nav]['dest_sector']));
			$debug_query3 = $db->Execute("INSERT INTO $dbtables[navmap] (start_sector, dest_sector, distance) VALUES ($shipinfo[sector_id], $row[$nav][dest_sector], $triptime)");
		}
	}
}
else
{
	$mapsectorcount = 1;
}

$smarty->assign("l_galacticarm", $l_galacticarm);
$smarty->assign("galacticarm", $galacticarm);
$smarty->assign("nav_scan_coords", $position);
$smarty->assign("ship_coordinates", $sectorinfo['x']."|".$sectorinfo['y']."|".$sectorinfo['z']);
$smarty->assign("ship_galacticarm", $sectorinfo['spiral_arm']);
$smarty->assign("altsector", $altsector);
$smarty->assign("altport", $altport);
$smarty->assign("altturns", $altturns);
$smarty->assign("sectorzonecolor", $sectorzonecolor);
$smarty->assign("sectorimage", $sectorimage);
$smarty->assign("sectortitle", $sectortitle);

// End Map element

if ($zoneinfo['zone_id'] < 5)
{
	$zoneinfo['zone_name'] = $l_zname[$zoneinfo['zone_id']];
}

$smarty->assign("sector", $shipinfo['sector_id']);
$smarty->assign("l_sector", $l_sector);
$smarty->assign("zoneid", $zoneinfo['zone_id']);
$smarty->assign("zonename", stripslashes($zoneinfo['zone_name']));
$smarty->assign("beacon", stripslashes($sectorinfo['beacon']));

$smarty->assign("title", $title);

$smarty->assign("insignia", player_insignia_name($username));
$smarty->assign("avatar", $playerinfo['avatar']);
$smarty->assign("teamicon", $teamicon);
$smarty->assign("player_name", $playerinfo['character_name']);
$smarty->assign("team_id", $playerinfo['team']);
$smarty->assign("shipname", $shipinfo['name']);
$smarty->assign("score", NUMBER($playerinfo['score']));
$smarty->assign("turns", NUMBER($playerinfo['turns']));
$smarty->assign("turnsused", NUMBER($playerinfo['turns_used']));
$smarty->assign("main", "Main");
$smarty->assign("ksm_allowed", $ksm_allowed);

$smarty->assign("general_text_color", $general_text_color);
$smarty->assign("basefontsize", $basefontsize);
$smarty->assign("general_highlight_color", $general_highlight_color);
$smarty->assign("main_table_heading", $main_table_heading);
$smarty->assign("l_turns_have", $l_turns_have);
$smarty->assign("l_turns_used", $l_turns_used);
$smarty->assign("l_score", $l_score);
$smarty->assign("l_abord", $l_abord);

$armour_pts_max = NUM_ARMOUR($shipinfo['armour']);
$ship_fighters_max = NUM_FIGHTERS($shipinfo['computer']);
$torps_max = NUM_TORPEDOES($shipinfo['torp_launchers']);
$smarty->assign("shipinfo_fighters", NUMBER($shipinfo['fighters']));
$smarty->assign("ship_fighters_max", NUMBER($ship_fighters_max));
$smarty->assign("shipinfo_torps", NUMBER($shipinfo['torps']));
$smarty->assign("torps_max", NUMBER($torps_max));
$smarty->assign("shipinfo_armour_pts", NUMBER($shipinfo['armour_pts']));
$smarty->assign("armour_pts_max", NUMBER($armour_pts_max));

$smarty->assign("commandplanetreport", "&nbsp;<a class=mnu href=\"planet-report.php\">$l_planets</a>&nbsp;");
$smarty->assign("planets", $l_planets);

$smarty->assign("commanddevices", "&nbsp;<a class=mnu href=\"device.php\">$l_device_ports</a>&nbsp;");
$smarty->assign("l_device_ports", $l_device_ports);

$smarty->assign("commanddefensereport", "&nbsp;<a class=mnu href=\"defence-report.php\">$l_sector_def</a>&nbsp;");
$smarty->assign("commandteamdefensereport", "&nbsp;<a class=mnu href=\"team-defence-report.php\">$l_teams $l_sector_def</a>&nbsp;");
$smarty->assign("l_sector_def", $l_sector_def);

$smarty->assign("commandnav", "&nbsp;<a class=mnu href=\"navcomp.php\">$l_navcomp</a>&nbsp;");
$smarty->assign("l_navcomp", $l_navcomp);

$smarty->assign("commandmap", "&nbsp;<a class=mnu href=\"galaxy_map.php\">$l_map</a>&nbsp;");
$smarty->assign("l_map", $l_map);

$smarty->assign("commandlocalmap", "&nbsp;<a class=mnu href=\"galaxy_local.php\">$l_localmap</a>&nbsp;");
$smarty->assign("l_localmap", $l_localmap);

$smarty->assign("command3dmap", "&nbsp;<a class=mnu href=\"galaxy_map3d.php\">$l_3dmap</a>&nbsp;");
$smarty->assign("l_3dmap", $l_3dmap);
$smarty->assign("gd_enabled", extension_loaded("gd"));
$smarty->assign("enable_spiral_galaxy", $enable_spiral_galaxy);

$smarty->assign("commandlog", "&nbsp;<a class=mnu href=\"log.php\">$l_log</a>&nbsp;");
$smarty->assign("l_log", $l_log);

$smarty->assign("commandreadmail", "&nbsp;<a class=mnu href=\"readmail.php\">$l_read_msg</A>&nbsp;");
$smarty->assign("l_read_msg", $l_read_msg);

$smarty->assign("commandsendmail", "&nbsp;<a class=mnu href=\"mailto2.php\">$l_send_msg</a>&nbsp;");
$smarty->assign("l_send_msg", $l_send_msg);

$smarty->assign("commandblockmail", "&nbsp;<a class=mnu href=\"messageblockmanager.php\">$l_block_msg</a>&nbsp;");
$smarty->assign("l_block_msg", $l_block_msg);

$smarty->assign("commandranking", "&nbsp;<a class=mnu href=\"ranking.php\">$l_rankings</a>&nbsp;");
$smarty->assign("l_rankings", $l_rankings);

$smarty->assign("commandteams", "&nbsp;<a class=mnu href=\"teams.php\">$l_teams</a>&nbsp;");
$smarty->assign("l_teams", $l_teams);

$smarty->assign("commandteamforum", "&nbsp;<a class=mnu href=\"team-forum.php?command=showtopics\">$l_teamforum<font size=\"1\"> - New: $newposts</font></a>&nbsp;");
$smarty->assign("l_teamforum", $l_teamforum);
$smarty->assign("newposts", $newposts);

$smarty->assign("commandteamship", "&nbsp;<a class=mnu href=\"team-report.php\">$l_teamships</a>&nbsp;");
$smarty->assign("l_teamships", $l_teamships);

$smarty->assign("commanddestruct", "&nbsp;<a class=mnu href=\"self-destruct.php\">$l_ohno</a>&nbsp;");
$smarty->assign("l_ohno", $l_ohno);

$smarty->assign("commandoptions", "&nbsp;<a class=mnu href=\"options.php\">$l_options</a>&nbsp;");
$smarty->assign("l_options", $l_options);

$smarty->assign("commandautotrade", "&nbsp;<a class=mnu href=\"autotrades.php\">$l_autotrade</a>&nbsp;");
$smarty->assign("l_autotrade", $l_autotrade);

$smarty->assign("commandprobe", "&nbsp;<a class=mnu href=\"probemenu.php\">$l_probe</a>&nbsp;");
$smarty->assign("l_probe", $l_probe);

$smarty->assign("commandfeedback", "&nbsp;<a class=mnu href=\"feedback.php\">$l_feedback</a>&nbsp;");
$smarty->assign("l_feedback", $l_feedback);

if ($spy_success_factor)
{
	$smarty->assign("commandspy", "&nbsp;<a class=mnu href=\"spy.php\">$l_spy</a>&nbsp;");
	$smarty->assign("l_spy", $l_spy);
	$smarty->assign("spy_success_factor", $spy_success_factor);
}

if ($dig_success_factor)
{
	$smarty->assign("commanddig", "&nbsp;<a class=mnu href=\"dig.php\">$l_dig</a>&nbsp;");
	$smarty->assign("l_dig", $l_dig);
	$smarty->assign("dig_success_factor", $dig_success_factor);
}

if (!empty($link_forums))
{
	$smarty->assign("commandforums", "&nbsp;<a class=\"mnu\" href=\"$link_forums\" TARGET=\"_blank\">$l_forums</a>&nbsp;");
	$smarty->assign("l_forums", $l_forums);
	$smarty->assign("link_forums", 1);
	$smarty->assign("forum_link", $link_forums);
}

$smarty->assign("commandlogout", "&nbsp;<a class=mnu href=\"logout.php\">$l_logout</a>&nbsp;");
$smarty->assign("l_logout", $l_logout);

$smarty->assign("l_set", $l_set);
$smarty->assign("l_add", $l_add);

$smarty->assign("l_main_warpto", $l_main_warpto);
$smarty->assign("linklist", $l_no_warplink);
$smarty->assign("links", $links);
$smarty->assign("l_fullscan", $l_fullscan);
$smarty->assign("l_main_autoroute", $l_main_autoroute);
$smarty->assign("autototal", $autototal);
$smarty->assign("autolist", $autolist);
$smarty->assign("l_realspace", $l_realspace);

for($i = 0; $i < $presettotal; $i++){
	$presetdist[$i] = trip_time($shipinfo['sector_id'], $presetinfo[$i]);
}

$smarty->assign("preset_display", $presetinfo);
$smarty->assign("preset_info", $presettext);
$smarty->assign("preset_dist", $presetdist);

$smarty->assign("shipinfo_sector_id", $shipinfo['sector_id']);
$smarty->assign("sector_max", $sector_max);
$smarty->assign("rslink_sector_back_dist", trip_time($shipinfo['sector_id'], ($shipinfo['sector_id'] - 1)));
$smarty->assign("rslink_sector_back", ($shipinfo['sector_id'] - 1));
$smarty->assign("rslink_sector_forward_dist", trip_time($shipinfo['sector_id'], ($shipinfo['sector_id'] + 1)));
$smarty->assign("rslink_sector_forward", ($shipinfo['sector_id'] + 1));
$smarty->assign("l_commands", $l_commands);
$smarty->assign("l_traderoutes", $l_traderoutes);
$smarty->assign("traderoute_display", $traderoute_display);
$smarty->assign("traderoute_links", $traderoute_links);
$smarty->assign("num_traderoutes", $num_traderoutes);
$smarty->assign("l_trade_control", $l_trade_control);
$smarty->assign("l_none", $l_none);
$smarty->assign("quickshout", $quickshout);
$smarty->assign("shoutboxtitle", "Shout Box");

$smarty->assign("shipinfo_hull", NUMBER($shipinfo['hull']));
$smarty->assign("shipinfo_engines", NUMBER($shipinfo['engines']));
$smarty->assign("shipinfo_power", NUMBER($shipinfo['power']));
$smarty->assign("shipinfo_computer", NUMBER($shipinfo['computer']));
$smarty->assign("shipinfo_sensors", NUMBER($shipinfo['sensors']));
$smarty->assign("shipinfo_beams", NUMBER($shipinfo['beams']));
$smarty->assign("shipinfo_torp_launchers", NUMBER($shipinfo['torp_launchers']));
$smarty->assign("shipinfo_armour", NUMBER($shipinfo['armour']));
$smarty->assign("shipinfo_shields", NUMBER($shipinfo['shields']));
$smarty->assign("shipinfo_cloak", NUMBER($shipinfo['cloak']));
$smarty->assign("shipinfo_ecm", NUMBER($shipinfo['ecm']));
$smarty->assign("shipinfo_dev_minedeflector", NUMBER($shipinfo['dev_minedeflector']));
$smarty->assign("shipinfo_emerwarp", NUMBER($shipinfo['emerwarp']));

$smarty->assign("shipinfo_goods", NUMBER($shipinfo['goods']));
$smarty->assign("shipinfo_ore", NUMBER($shipinfo['ore']));
$smarty->assign("shipinfo_organics", NUMBER($shipinfo['organics']));
$smarty->assign("shipinfo_energy", NUMBER($shipinfo['energy']));
$smarty->assign("shipinfo_colonists", NUMBER($shipinfo['colonists']));
$smarty->assign("shipinfo_fighters", NUMBER($shipinfo['fighters']));
$smarty->assign("shipinfo_armor", NUMBER($shipinfo['armour_pts']));
$smarty->assign("shipinfo_torps", NUMBER($shipinfo['torps']));
$smarty->assign("playerinfo_credits", NUMBER($playerinfo['credits']));
$smarty->assign("l_colonists", $l_colonists);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("l_cargo", $l_cargo);
$smarty->assign("l_goods", $l_goods);
$smarty->assign("l_energy", $l_energy);
$smarty->assign("l_ore", $l_ore);
$smarty->assign("weapons_armor", $l_arm_weap);
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("l_torps", $l_torps);
$smarty->assign("l_armourpts", $l_armourpts);
$smarty->assign("l_armor", $l_armor);
$smarty->assign("l_organics", $l_organics);
$smarty->assign("l_logout", $l_logout);

$smarty->assign("l_scan", $l_scan);
$smarty->assign("autostart", $autostart);
$smarty->assign("autoend", $autoend);

$sectorcount = 0;
$query2 = "SELECT source FROM $dbtables[movement_log] where player_id=$playerinfo[player_id] order by time DESC limit 0,5";
$result = $db->Execute ($query2);
db_op_result($res,__LINE__,__FILE__);		 

while(!$result->EOF)
{
	$lastsectors[$sectorcount] = $result->fields['source'];
	$lastsectorsdist[$sectorcount] = trip_time($shipinfo['sector_id'], $lastsectors[$sectorcount]);
	$sectorcount++;
	$result->Movenext();
}
$smarty->assign("lastsectors", $lastsectors);
$smarty->assign("lastsectorsdist", $lastsectorsdist);

$smarty->assign("spiral_arm", $sectorinfo['spiral_arm']);
$smarty->assign("enable_spiral_galaxy", $enable_spiral_galaxy);
$smarty->assign("templatename", $templatename);

	$filelist = get_dirlist($gameroot);
	$newcommands = 0;
	for ($c=0; $c<count($filelist); $c++) { 
		$filenameroot =  str_replace(".php", "", $filelist[$c]); 
		if(strstr($filelist[$c], "command_")){
			$fs = fopen($gameroot.$filelist[$c], "r");
			$items = fgets($fs);
			$items = fgets($fs);
			fclose($fs);
			$name = substr(trim($items), 3);
			$newcommandfull[$newcommands] = "&nbsp;<a class=mnu href=\"$filenameroot.php\">$name</a>&nbsp;";
			$newcommandname[$newcommands] = $name;
			$newcommandlink[$newcommands] = "modules_command/$filenameroot.php";
			$newcommands++;
		}
	}
	$smarty->assign("newcommandfull", $newcommandfull);
	$smarty->assign("newcommandname", $newcommandname);
	$smarty->assign("newcommandlink", $newcommandlink);
	$smarty->assign("newcommands", $newcommands);
	$smarty->assign("language", $langdir);
}

function clean_words($phrase)
{
	global $db, $dbtables, $enable_wordcensor, $enable_wordcensor_metaphone;

	if($enable_wordcensor == 0)
		return $phrase;

	$newphrase = "";
	$phrase = eregi_replace(" +", " ", $phrase);
	$words = explode(" ", $phrase);

	for($i = 0; $i<count($words); $i++){
		$fixedword = eregi_replace("-+", "-", $words[$i]);
		$fixedword = eregi_replace("=+", "=", $fixedword);
		$fixedword = eregi_replace("\++", "+", $fixedword);
		$line = "'".addslashes(strtolower($fixedword)) . "','";
		$newword = "";
		for($count = 0; $count<strlen($fixedword); $count += 2){
			$newword .= substr(strtolower($fixedword), $count, 1);
		}
		$line .= addslashes($newword) . "','";

		$newword = "";
		for($count = 1; $count<strlen($fixedword); $count += 2){
			$newword .= substr(strtolower($fixedword), $count, 1);
		}
		$line .= addslashes($newword) . "'";

		$result3 = $db->Execute ("SELECT * FROM $dbtables[wordcensor] WHERE name IN ($line)");
		db_op_result($result3,__LINE__,__FILE__);
		if (!$result3->EOF)
		{
			$word = $result3->fields['value'];
		}else{
			$word = $words[$i];
			$wordnew = "";
			$res2 = $db->Execute ("SELECT * FROM $dbtables[wordcensor]");
			db_op_result($res2,__LINE__,__FILE__);

			while(!$res2->EOF)
			{
				$name = $res2->fields['name'];
				$value = $res2->fields['value'];
				$wordnew = str_replace($name, $value, strtolower($fixedword));
				if($wordnew != strtolower($fixedword)){
					$word = $wordnew;
					break;
				}

				if (metaphone($fixedword) == metaphone($name) && $enable_wordcensor_metaphone) 
				{
					$word = $value;
					break;
				}
				$res2->MoveNext();
			}
		}

		$newphrase = $newphrase . $word . " ";
	}

	return ucfirst($newphrase);
}

function close_database(){
	global $db;

	$db->close();
}

function phpChangeDelta($desiredvalue,$currentvalue)
{
	global $upgrade_cost, $upgrade_factor;

	$Delta=0; $DeltaCost=0;
	$Delta = $desiredvalue - $currentvalue;

	while ($Delta>0)
	{
		$DeltaCost=$DeltaCost + mypw($upgrade_factor,$desiredvalue-$Delta);
		$Delta=$Delta-1;
	}
	$DeltaCost=$DeltaCost * $upgrade_cost;

	return $DeltaCost;
}

function set_max_credits($planet_id){
	global $db, $dbtables, $planet_credit_multi, $base_credits;

	$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE planet_id = $planet_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$planetinfo = $debug_query->fields;

	$max_credits = phpChangeDelta($planetinfo['computer'], 0) + phpChangeDelta($planetinfo['sensors'], 0) + phpChangeDelta($planetinfo['beams'], 0) + phpChangeDelta($planetinfo['torp_launchers'], 0) + phpChangeDelta($planetinfo['shields'], 0) + phpChangeDelta($planetinfo['jammer'], 0) + phpChangeDelta($planetinfo['cloak'], 0);
	$max_credits = ($max_credits * $planet_credit_multi) + $base_credits;
	$debug_query = $db->Execute("UPDATE $dbtables[planets] SET max_credits=$max_credits WHERE planet_id=$planet_id");
}

function update_player_experience($player_id, $experience){
	global $db, $dbtables;

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET experience=GREATEST(experience + $experience, 0) WHERE player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	adminlog(LOG_RAW,"UPDATE $dbtables[players] SET experience=GREATEST(experience + $experience, 0) WHERE player_id=$player_id");
}

function smarty_display($templatename, $templatefile)
{
	global $smarty, $default_template;

	if(is_file($gameroot.$templatename.$templatefile)){
		$smarty->display($templatename.$templatefile);
	}
	else
	{
		$smarty->display($default_template.$templatefile);
	}
}

function send_system_im($target_id, $subject, $content, $targetonline)
{
	global $db, $dbtables;

	$difftime = (TIME() - strtotime($targetonline)) / 60;

	if ($difftime <= 5) 
	{
		$result2 = $db->Execute("SELECT * from $dbtables[messages] where recp_id = $target_id order by ID DESC");
		$iminfo = $result2->fields;

		$timestamp = date("Y-m-d H:i:s");
		if($iminfo['subject'] != $subject and $iminfo['message'] != $content){
			$debug_query = $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES " .
										"('0', '" . $target_id . "', '" . $timestamp . "', " .
										"'" . $subject . "', '" . $content . "')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

// $apply = 0 - Check for bounty
// $apply = 1 - Check for bounty and if attacker gets bounty apply it to the player and announce it
// If the return value is 0 then no bounty,  If it is anything but 0 then that is the bounty amount.
function ship_bounty_check($attacker, $ship_sector, $target, $apply)
{
	global $db, $dbtables, $bounty_ratio, $bounty_minturns, $bounty_maxvalue;

	if($target['team'] != 0)
	{
		$debug_query = $db->Execute("SELECT player_id, turns_used, email FROM $dbtables[players] WHERE team=$target[team] order by score DESC");
		db_op_result($debug_query,__LINE__,__FILE__);
		$target = $debug_query->fields;
	}

	$playerscore = gen_score($attacker['player_id']);
	$targetscore = gen_score($target['player_id']);

	$playerscore = $playerscore * $playerscore;
	$targetscore = $targetscore * $targetscore;

	if ($playerscore == 0) 
	{
		$playerscore = 1;
	}

	if ($target['player_id'] > 3 && $bounty_ratio != 0 && (($targetscore / $playerscore) < $bounty_ratio || $target['turns_used'] < $bounty_minturns) && !("@npc" == substr($targetinfo['email'], -4)))
	{
		// Check to see if there is Mornoc Alliance bounty on the player. If there is, people can attack regardless.
		$btyamount = 0;
		$hasbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM $dbtables[bounty] WHERE bounty_on = $target[player_id] AND placed_by = 0");
		if ($hasbounty)
		{
			$resx = $hasbounty->fields;
			$btyamount = $resx['btytotal'];
		}

		$is_owner = get_sector_bounty_control($ship_sector);
		if($is_owner)
		{
			$btyamount = 1;
		}

		if ($btyamount == 0)
		{
			$bounty = ROUND($playerscore * $bounty_maxvalue);

			if($apply == 1)
			{
				$debug_query = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($attacker[player_id], 0 ,$bounty)");
				db_op_result($debug_query,__LINE__,__FILE__);					

				$debug_query = $db->Execute("UPDATE $dbtables[players] SET fed_bounty_count=fed_bounty_count+1, fed_attack_date='0000-00-00 00:00:00' WHERE player_id=$attacker[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				playerlog($attacker['player_id'],LOG_BOUNTY_FEDBOUNTY,"$bounty");
				insert_news("Mornoc Alliance|".$bounty."|".$attacker['character_name'], 1, "bounty");
			}
			return $bounty;
		}
	}
	return 0;
}

// $apply = 0 - Check for bounty
// $apply = 1 - Check for bounty and if attacker gets bounty apply it to the player and announce it
// If the return value is 0 then no bounty,  If it is anything but 0 then that is the bounty amount.
function planet_bounty_check($attacker, $ship_sector, $target, $apply, $modifier = 1)
{
	global $db, $dbtables, $planet_bounty_ratio, $bounty_minturns, $bounty_maxvalue;

	if($target['team'] != 0)
	{
		$debug_query = $db->Execute("SELECT player_id, turns_used, email FROM $dbtables[players] WHERE team=$target[team] order by score DESC");
		db_op_result($debug_query,__LINE__,__FILE__);
		$target = $debug_query->fields;
	}

	$playerscore = gen_score($attacker['player_id']);
	$targetscore = gen_score($target['player_id']);

	$playerscore = $playerscore * $playerscore;
	$targetscore = $targetscore * $targetscore;

	if ($playerscore == 0) 
	{
		$playerscore = 1;
	}

	if ($target['player_id'] > 3 && $planet_bounty_ratio != 0 && (($targetscore / $playerscore) < $planet_bounty_ratio || $target['turns_used'] < $bounty_minturns) && !("@npc" == substr($targetinfo['email'], -4)))
	{
		// Check to see if there is Mornoc Alliance bounty on the player. If there is, people can attack regardless.
		$btyamount = 0;
		$hasbounty = $db->Execute("SELECT SUM(amount) AS btytotal FROM $dbtables[bounty] WHERE bounty_on = $target[player_id] AND placed_by = 0");
		if ($hasbounty)
		{
			$resx = $hasbounty->fields;
			$btyamount = $resx['btytotal'];
		}

		$is_owner = get_sector_bounty_control($ship_sector);
		if($is_owner)
		{
			$btyamount = 1;
		}

		if ($btyamount == 0)
		{
			$bounty = ROUND($playerscore * $bounty_maxvalue * $modifier);

			if($apply == 1)
			{
				$debug_query = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($attacker[player_id], 0 ,$bounty)");
				db_op_result($debug_query,__LINE__,__FILE__);					

				$debug_query = $db->Execute("UPDATE $dbtables[players] SET fed_bounty_count=fed_bounty_count+1, fed_attack_date='0000-00-00 00:00:00' WHERE player_id=$attacker[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				playerlog($attacker['player_id'],LOG_BOUNTY_FEDBOUNTY,"$bounty");
				insert_news("Mornoc Alliance|".$bounty."|".$attacker['character_name'], 1, "bounty");
			}
			return $bounty;
		}
	}
	return 0;
}

function StripNonNum($str)
{
  $str=(string)$str;
  $output = ereg_replace("[^0-9]","",$str);
  return $output;
}

?>
