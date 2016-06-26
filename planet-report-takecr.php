<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet-report-ce.php

include ("config/config.php");
include ("languages/$langdir/lang_rsmove.inc");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_planets.inc");

if ((!isset($team_id)) || ($team_id == ''))
{
	$team_id = '';
}

$title = $l_pr_title;

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

if (isset($_POST["TPCreds"]))
{
	collect_credits($_POST["TPCreds"]);
}


function collect_credits($planetarray)
{
	global $db, $dbtables, $username, $l_pr_notenoughturns, $l_pr_planetstatus,$l_global_mmenu, $l_pr_menulink;
	global $spy_success_factor, $planet_detect_success1, $shipinfo,$smarty,$templatename, $playerinfo;

	// create an array of sector -> planet pairs
	for ($i = 0; $i < count($planetarray); $i++)
	{
		$get_planetinfo = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planetarray[$i] and owner=$playerinfo[player_id]");
		$s_p_pair[$i]= array($get_planetinfo->fields["sector_id"], $planetarray[$i], $get_planetinfo->fields["name"]);
//	}

	// Sort the array so that it is in order of sectors, lowest number first, not closest
//	sort($s_p_pair);
//	reset($s_p_pair);

	// run through the list of sector planet pairs realspace moving to each sector and then performing the transfer.
	// Based on the way realspace works we don't need a sub loop -- might add a subloop to clean things up later.

//	for ($i=0; $i < count($planetarray); $i++)
//	{
		$CS = real_space_move($s_p_pair[$i][0], $s_p_pair[$i][2]);

		$messagetype[$i] = $CS['type'];
		$messagea[$i] = $CS['msg1'];
		$messageb[$i] = $CS['msg2'];
		if ($CS['type'] == "HOSTILE" || $CS['type'] == "BREAK-TURNS" or $CS['type'] == "BREAK-SECTORS")
		{
			$CS['type'] = "HOSTILE";
		} 
		else if ($CS['type'] == "GO")
		{
			$gotthem = Take_Credits($s_p_pair[$i][0], $s_p_pair[$i][1]);
			$CS['type'] = $gotthem['type'];
			$messagetype[$i] = $CS['type'];
			$message_takea[$i] = $gotthem['msg1'];
			$message_takeb[$i] = $gotthem['msg2'];
			if ($spy_success_factor)
			{
				spy_detect_planet($shipinfo['ship_id'], $s_p_pair[$i][1], $planet_detect_success1);
			}
			if($gotthem['type'] == "BREAK-TURNS" or $gotthem['type'] == "BREAK-SECTORS"){
				$messagetype[$i] = $CS['type'];
				$message_takea[$i] = $gotthem['msg1'];
				$message_takeb[$i] = $gotthem['msg2'];
				$i++;
				break;
			}
		}
		else
		{
			$i++;
			break;
		}
	}

	$smarty->assign("l_pr_menulink", $l_pr_menulink);
	$smarty->assign("creditcount", $i);
	$smarty->assign("l_pr_planetstatus", $l_pr_planetstatus);
	$smarty->assign("l_pr_notenoughturns", $l_pr_notenoughturns);
	$smarty->assign("messagetype", $messagetype);
	$smarty->assign("messagea", $messagea);
	$smarty->assign("messageb", $messageb);
	$smarty->assign("message_takea", $message_takea);
	$smarty->assign("message_takeb", $message_takeb);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet-report-cecredits.tpl");
	include ("footer.php");
}

function Take_Credits($sector_id, $planet_id)
{
	global $db, $dbtables, $username, $shipinfo, $playerinfo, $l_unnamed, $playerinfo;
	global $l_pr_credstaken, $l_pr_credsonboard, $l_planet2_notowner, $l_planet2_sector, $l_pr_notturns;

	// Get basic Database information (ship, player and planet)
	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[players] WHERE email='$username'",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$playerinfo = $debug_query->fields;

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id and owner=$playerinfo[player_id]");
	$planetinfo = $res->fields;

	$debug_query = $db->SelectLimit("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]",1);
	db_op_result($debug_query,__LINE__,__FILE__);
	$shipinfo = $debug_query->fields;

	// Set the name for unamed planets to be "unnamed"
	if (empty($planetinfo['name']))
	{
		$planet['name'] = $l_unnamed;
	}

	//verify player is still in same sector as the planet
	if ($shipinfo['sector_id'] == $planetinfo['sector_id'])
	{
		if ($playerinfo['turns'] >= 1)
		{
			// verify player owns the planet to take credits from
			if ($planetinfo['owner'] == $playerinfo['player_id'])
			{
				// get number of credits from the planet and current number player has on ship
				$CreditsTaken = $planetinfo['credits'];
				$CreditsOnShip = $playerinfo['credits'];
				$NewShipCredits = $CreditsTaken + $CreditsOnShip;

				// update the planet record for credits
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=0 WHERE planet_id=$planetinfo[planet_id] and owner=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				// update the player record
				// credits & turns
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=$NewShipCredits, turns=turns-1 WHERE email='$username'");
				db_op_result($debug_query,__LINE__,__FILE__);

				$l_pr_credstaken2 = str_replace("[CreditsTaken]", NUMBER($CreditsTaken), $l_pr_credstaken);
				$l_pr_credstaken2 = str_replace("[name]", $planetinfo['name'], $l_pr_credstaken2);
		
				$l_pr_credsonboard2 = str_replace("[name]", $shipinfo['name'], $l_pr_credsonboard);
				$l_pr_credsonboard2 = str_replace("[NewShipCredits]", NUMBER($NewShipCredits), $l_pr_credsonboard2);
		
				$retval['msg1'] = $l_pr_credstaken2;
				$retval['msg2'] = $l_pr_credsonboard2;

				$retval['type'] = "GO";
			}
			else
			{
				$retval['msg1'] = $l_planet2_notowner;
				$retval['msg2'] = $planetinfo['name'];
				$retval['type'] = "GO";
			}
		}
		else
		{
			$l_pr_notturns2 = str_replace("[name]", $planetinfo[name], $l_pr_notturns);
			$l_pr_notturns2 = str_replace("[sector_id]", $planetinfo['sector_id'], $l_pr_notturns2);
			$retval['msg1'] = $l_pr_notturns2;
			$retval['type'] = "BREAK-TURNS";
		}
	}
	else
	{
		$retval['msg1'] = $l_planet2_sector;
		$retval['type'] = "BREAK-SECTORS";
	}

	return($retval);
}

function Real_Space_Move($destination, $planetname)
{
	global $db, $dbtables;
	global $l_pr_hostile,$l_rs_invalid;
	global $l_rs_ready;
	global $l_unnamed;
	global $level_factor;
	global $playerinfo, $shipinfo;
	global $username;
	global $l_rs_movetime, $l_rs_noturns, $l_sector;

	$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$destination");
	$sector_type = $sector_res->fields['sg_sector'];

	if ($sector_type != 1){
		$distance = calc_dist($shipinfo['sector_id'],$destination);
		$shipspeed = mypw($level_factor, $shipinfo['engines']);
		$triptime = round($distance / $shipspeed);

		if ($triptime == 0 && $destination != $shipinfo['sector_id'])
		{
			$triptime = 1;
		}

		if ($shipinfo['dev_fuelscoop'] == "Y")
		{
			$energyscooped = $distance * 100;
		}
		else
		{
			$energyscooped = 0;
		}

		if ($shipinfo['dev_fuelscoop'] == "Y" && $energyscooped == 0 && $triptime == 1)
		{
			$energyscooped = 100;
		}

		$free_power = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];

		// amount of energy that can be stored is less than amount scooped amount scooped is set to what can be stored
		if ($free_power < $energyscooped)
		{
			$energyscooped = $free_power;
		}

		// make sure energyscooped is not null
		if (!isset($energyscooped))
		{
			$energyscooped = "0";
		}

		// make sure energyscooped not negative, or decimal
		if ($energyscooped < 1)
		{
			$energyscooped = 0;
		}

		// check to see if already in that sector
		if ($destination == $shipinfo['sector_id'])
		{
			$triptime = 0;
			$energyscooped = 0;
		}

		if ($triptime > $playerinfo['turns'])
		{
			$l_rs_movetime2=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
			$retval['msg1'] = $planetname . " - " . $l_sector . " " . $destination . ": " . $l_rs_movetime2;
			$retval['msg2'] = $l_rs_noturns;
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET cleared_defences=' ' WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$retval['type'] = "BREAK-TURNS";
		}
		else
		{
			// ********************************
			// ***** Sector Defense Check *****
			// ********************************

			$hostile = 0;
			$result98 = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE sector_id = $destination AND player_id <> $playerinfo[player_id]");
			if (!$result98->EOF)
			{
				$fighters_owner = $result98->fields;
				$nsresult = $db->Execute("SELECT * from $dbtables[players] where player_id=$fighters_owner[player_id]");
				$nsfighters = $nsresult->fields;
				if ($nsfighters[team] != $playerinfo[team] || $playerinfo[team]==0)
				{
					$hostile = 1;
				}
			}

			if ($hostile > 0)
			{
				$retval['type'] = "HOSTILE";
				$l_pr_hostile2 = str_replace("[destination]", $destination, $l_pr_hostile);
				$retval['msg1'] = $l_pr_hostile2;
			}
			else
			{
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-$triptime,turns_used=turns_used+$triptime WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$destination,energy=energy+$energyscooped WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				$l_rs_ready2=str_replace("[sector]",$destination,$l_rs_ready);
				$l_rs_ready2=str_replace("[triptime]",NUMBER($triptime),$l_rs_ready2);
				$l_rs_ready2=str_replace("[energy]",NUMBER($energyscooped),$l_rs_ready2);
				$retval['msg1'] = $l_rs_ready2;
				$retval['type'] = "GO";
			}
		}
	}else{
		$retval['type'] = "HOSTILE";
		$l_pr_hostile2 = str_replace("[destination]", $destination, $l_pr_hostile);
		$retval['msg1'] = $l_rs_invalid;
	}
	return($retval);
}

close_database();
?>
