<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_upgrade.php

include ("config/config.php");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");

if ((!isset($planet_id)) || ($planet_id == ''))
{
	$planet_id = '';
}

$title = $l_planet2_title;

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

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

//-------------------------------------------------------------------------------------------------

$result2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result2)
{
	$planetinfo = $result2->fields;
}

if ($planetinfo['sector_id'] <> $shipinfo['sector_id'])
{
	$smarty->assign("error_msg", $l_planet2_sector);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet_upgradedie.tpl");
	include ("footer.php");
	die();
}

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_planet2_noturn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet_upgradedie.tpl");
	include ("footer.php");
	die();

}

function phpTrueDelta($futurevalue,$planetvalue)
{
	$tempval = $futurevalue - $planetvalue;
	return $tempval;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo['owner'] > 0))
{
	if (!empty($planetinfo))
	{
		if ($planetinfo['base'] == "N")
		{
			$smarty->assign("error_msg", $l_planet_upgradebase);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planet_upgradedie.tpl");
			include ("footer.php");
			die();
		}

		$color_red	 = "red";
		$color_green   = "#00FF00"; //light green
		$trade_deficit = "$l_cost : ";
		$trade_benefit = "$l_profit : ";

// Computer
		$computer_upgrade_cost = 0;
		if($planetinfo['computer'] == $planetinfo['computer_normal']){
			if ($computer_upgrade > 54)
				$computer_upgrade = 54;

			if ($computer_upgrade < 0)
				$computer_upgrade = 0;

			if ($computer_upgrade > $planetinfo['computer_normal'])
			{
				$computer_upgrade_cost = phpChangeDelta($computer_upgrade, $planetinfo['computer_normal']);
			}
		}
		else
		{
			$computer_upgrade = $planetinfo['computer_normal'];
		}

// Sensors
		$sensors_upgrade_cost = 0;
		if($planetinfo['sensors'] == $planetinfo['sensors_normal']){
			if ($sensors_upgrade > 54)
				$sensors_upgrade = 54;

			if ($sensors_upgrade < 0)
				$sensors_upgrade = 0;

			if ($sensors_upgrade > $planetinfo['sensors_normal'])
			{
				$sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $planetinfo['sensors_normal']);
			}
		}
		else
		{
			$sensors_upgrade = $planetinfo['sensors_normal'];
		}

// Beams
		$beams_upgrade_cost = 0;
		if($planetinfo['beams'] == $planetinfo['beams_normal']){
			if ($beams_upgrade > 54)
				$beams_upgrade = 54;

			if ($beams_upgrade < 0)
				$beams_upgrade = 0;

			if ($beams_upgrade > $planetinfo['beams_normal'])
			{
				$beams_upgrade_cost = phpChangeDelta($beams_upgrade, $planetinfo['beams_normal']);
			}
		}
		else
		{
			$beams_upgrade = $planetinfo['beams_normal'];
		}

// Cloak
		$cloak_upgrade_cost = 0;
		if($planetinfo['cloak'] == $planetinfo['cloak_normal']){
			if ($cloak_upgrade > 54)
				$cloak_upgrade = 54;

			if ($cloak_upgrade < 0)
				$cloak_upgrade = 0;

			if ($cloak_upgrade > $planetinfo['cloak_normal'])
			{
				$cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $planetinfo['cloak_normal']);
			}
		}
		else
		{
			$cloak_upgrade = $planetinfo['cloak_normal'];
		}

// Torp_launchers
		$torp_launchers_upgrade_cost = 0;
		if($planetinfo['torp_launchers'] == $planetinfo['torp_launchers_normal']){
			if ($torp_launchers_upgrade > 54)
				$torp_launchers_upgrade = 54;

			if ($torp_launchers_upgrade < 0)
				$torp_launchers_upgrade = 0;

			if ($torp_launchers_upgrade > $planetinfo['torp_launchers_normal'])
			{
				$torp_launchers_upgrade_cost = phpChangeDelta($torp_launchers_upgrade, $planetinfo['torp_launchers_normal']);
			}
		}
		else
		{
			$torp_launchers_upgrade = $planetinfo['torp_launchers_normal'];
		}

// Shields
		$shields_upgrade_cost = 0;
		if($planetinfo['shields'] == $planetinfo['shields_normal']){
			if ($shields_upgrade > 54)
				$shields_upgrade = 54;

			if ($shields_upgrade < 0)
				$shields_upgrade = 0;

			if ($shields_upgrade > $planetinfo['shields_normal'])
			{
				$shields_upgrade_cost = phpChangeDelta($shields_upgrade, $planetinfo['shields_normal']);
			}
		}
		else
		{
			$shields_upgrade = $planetinfo['shields_normal'];
		}

// Jammer
		$jammer_upgrade_cost = 0;
		if($planetinfo['jammer'] == $planetinfo['jammer_normal']){
			if ($jammer_upgrade > 54)
				$jammer_upgrade = 54;

			if ($jammer_upgrade < 0)
				$jammer_upgrade = 0;

			if ($jammer_upgrade > $planetinfo['jammer'])
			{
				$jammer_upgrade_cost = phpChangeDelta($jammer_upgrade, $planetinfo['jammer']);
			}
		}
		else
		{
			$jammer_upgrade = $planetinfo['jammer_normal'];
		}

		$total_cost = $computer_upgrade_cost +
		$sensors_upgrade_cost + $beams_upgrade_cost + $cloak_upgrade_cost +
		$torp_launchers_upgrade_cost + $shields_upgrade_cost+ $jammer_upgrade_cost;

		if ($total_cost > $playerinfo['credits'])
		{
			$l_planet_nomoney = str_replace("[cost]", NUMBER($total_cost), $l_planet_nomoney);
			$l_planet_nomoney = str_replace("[credits]", NUMBER($playerinfo['credits']), $l_planet_nomoney);
			$smarty->assign("error_msg", $l_planet_nomoney);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planet_upgradedie.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			$trade_credits = NUMBER(abs($total_cost));

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$total_cost,turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$max_credits = 0;
			$query = "UPDATE $dbtables[planets] SET planet_id=$planet_id ";
// Computer
			if ($computer_upgrade > $planetinfo['computer_normal'])
			{
				$query = $query . ", computer=$computer_upgrade, computer_normal=$computer_upgrade";
				$upgradecomputer = 1;
				$max_credits += phpChangeDelta($computer_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['computer_normal'], 0);
			}
// Sensors
			if ($sensors_upgrade > $planetinfo['sensors_normal'])
			{
				$query = $query . ", sensors=$sensors_upgrade, sensors_normal=$sensors_upgrade";
				$upgradesensors = 1;
				$max_credits += phpChangeDelta($sensors_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['sensors_normal'], 0);
			}
// Beam Weapons
			if ($beams_upgrade > $planetinfo['beams_normal'])
			{
				$query = $query . ", beams=$beams_upgrade, beams_normal=$beams_upgrade";
				$upgradebeams = 1;
				$max_credits += phpChangeDelta($beams_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['beams_normal'], 0);
			}
// Torpedo Launchers
			if ($torp_launchers_upgrade > $planetinfo['torp_launchers_normal'])
			{
				$query = $query . ", torp_launchers=$torp_launchers_upgrade, torp_launchers_normal=$torp_launchers_upgrade";
				$upgradetorps = 1;
				$max_credits += phpChangeDelta($torp_launchers_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['torp_launchers_normal'], 0);
			}
// Shields
			if ($shields_upgrade > $planetinfo['shields_normal'])
			{
				$query = $query . ", shields=$shields_upgrade, shields_normal=$shields_upgrade";
				$upgradeshields = 1;
				$max_credits += phpChangeDelta($shields_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['shields_normal'], 0);
			}
// Jammer
			if ($jammer_upgrade > $planetinfo['jammer_normal'])
			{
				$query = $query . ", jammer=$jammer_upgrade, jammer_normal=$jammer_upgrade";
				$upgradejammer = 1;
				$max_credits += phpChangeDelta($jammer_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['jammer_normal'], 0);
			}
// Cloak
			if ($cloak_upgrade > $planetinfo['cloak_normal'])
			{
				$query = $query . ", cloak=$cloak_upgrade, cloak_normal=$cloak_upgrade";
				$upgradecloak = 1;
				$debug_query = $db->Execute("UPDATE $dbtables[spies] SET spy_cloak=$cloak_upgrade WHERE planet_id = '$planetinfo[planet_id]' and ship_id = '0' and active = 'N'");
				db_op_result($debug_query,__LINE__,__FILE__);
				$max_credits += phpChangeDelta($cloak_upgrade, 0);
   			}
			else
			{
				$max_credits += phpChangeDelta($planetinfo['cloak_normal'], 0);
			}

			$max_credits = ($max_credits * $planet_credit_multi) + $base_credits;
			$query = $query . ", max_credits=$max_credits WHERE planet_id=$planet_id";
			$debug_query = $db->Execute("$query");
			db_op_result($debug_query,__LINE__,__FILE__);

			$smarty->assign("l_trade_result", $l_trade_result);
			$smarty->assign("l_cost", $l_cost);
			$smarty->assign("trade_credits", $trade_credits);
			$smarty->assign("l_credits", $l_credits);
			$smarty->assign("l_trade_upgraded", $l_trade_upgraded);
			$smarty->assign("l_computer", $l_computer);
			$smarty->assign("computer_upgrade", $computer_upgrade);
			$smarty->assign("l_sensors", $l_sensors);
			$smarty->assign("sensors_upgrade", $sensors_upgrade);
			$smarty->assign("l_beams", $l_beams);
			$smarty->assign("beams_upgrade", $beams_upgrade);
			$smarty->assign("l_torp_launch", $l_torp_launch);
			$smarty->assign("torp_launchers_upgrade", $torp_launchers_upgrade);
			$smarty->assign("l_shields", $l_shields);
			$smarty->assign("shields_upgrade", $shields_upgrade);
			$smarty->assign("l_jammer", $l_jammer);
			$smarty->assign("jammer_upgrade", $jammer_upgrade);
			$smarty->assign("l_cloak", $l_cloak);
			$smarty->assign("cloak_upgrade", $cloak_upgrade);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("upgradecomputer", $upgradecomputer);
			$smarty->assign("upgradesensors", $upgradesensors);
			$smarty->assign("upgradebeams", $upgradebeams);
			$smarty->assign("upgradetorps", $upgradetorps);
			$smarty->assign("upgradeshields", $upgradeshields);
			$smarty->assign("upgradejammer", $upgradejammer);
			$smarty->assign("upgradecloak", $upgradecloak);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planet_upgrade.tpl");
			include ("footer.php");
			die();
		}
	}
}
close_database();
?>
