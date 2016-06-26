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


	global $base_ore, $base_organics, $base_goods, $base_credits;
	global $db, $dbtables, $l_clickme, $l_planet_bbuild, $l_toplanetmenu;
	global $shipinfo, $spy_success_factor, $planet_detect_success1, $playerinfo;
	global $username,$smarty,$templatename,$l_global_mmenu, $$l_planet2_notowner;

	$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id=$builds and planet_id=$buildp and owner=$playerinfo[player_id]");
	if ($result3)
	{
		$planetinfo = $result3->fields;
	}
	else
	{
		$smarty->assign("error_msg", $l_planet2_notowner);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet-report-cedie.tpl");
		include ("footer.php");
	}

	$realmove = Real_Space_Move($builds);

	if ($realmove['type'] == "GO")
	{
		if ($spy_success_factor)
		{
			spy_detect_planet($shipinfo['ship_id'], $buildp, $planet_detect_success1);
		}

		// build a base
		if ($planetinfo['ore'] >= $base_ore && $planetinfo['organics'] >= $base_organics && $planetinfo['goods'] >= $base_goods && $planetinfo['credits'] >= $base_credits)
		{
			// ** Create The Base
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET base='Y', ore=$planetinfo[ore]-$base_ore, organics=$planetinfo[organics]-$base_organics, goods=$planetinfo[goods]-$base_goods, credits=$planetinfo[credits]-$base_credits WHERE planet_id=$buildp and owner=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			// ** Update User Turns
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1 where player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			// ** Refresh Planet Info
			$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$buildp");
			$planetinfo=$result3->fields;

			// ** Calc Ownership and Notify User Of Results
			$ownership = calc_ownership($planetinfo['sector_id']);
			if (!empty($ownership))
			{
				$owned = 1;
			}
		}
		$smarty->assign("l_pr_menulink", $l_pr_menulink);
		$smarty->assign("l_pr_planetstatus", $l_pr_planetstatus);
		$smarty->assign("planet_id", $buildp);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_planet_bbuild", $l_planet_bbuild);
		$smarty->assign("owned", $owned);
		$smarty->assign("ownership", $ownership);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet-report-cebase.tpl");
		include ("footer.php");
	}else{
		$smarty->assign("error_msg", $realmove['msg1']);
		$smarty->assign("error_msg2", $realmove['msg2']);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet-report-cebasedie.tpl");
		include ("footer.php");
	}

function Real_Space_Move($destination)
{
	global $db, $dbtables;
	global $l_pr_hostile,$l_rs_invalid;
	global $l_rs_ready;
	global $l_unnamed;
	global $level_factor;
	global $playerinfo, $shipinfo;
	global $username;

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
			$l_rs_movetime=str_replace("[triptime]",NUMBER($triptime),$l_rs_movetime);
			$retval['msg1'] = $l_rs_movetime;
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
