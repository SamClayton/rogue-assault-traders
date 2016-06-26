<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_upgradecagofinal.php

include ("config/config.php");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_traderoute.inc");

$planet_id = '';

if (isset($_GET['planet_id']))
{
	$planet_id = $_GET['planet_id'];
}

function phpTrueDelta($futurevalue,$shipvalue)
{
	$tempval = $futurevalue - $shipvalue;
	return $tempval;
}

$title = $l_planet_title;

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

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
  $planetinfo=$result3->fields;

if ((!isset($command)) || ($command == ''))
{
	$command = '';
}

if ((!isset($destroy)) || ($destroy == ''))
{
	$destroy = '';
}

// No planet

if (empty($planetinfo))
{
	$smarty->assign("error_msg", $l_planet_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet_owneddie.tpl");
	include ("footer.php");
	die();
}

if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
	if ($shipinfo['on_planet'] == 'Y')
	{
	  $debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$shipinfo[ship_id]");
	  db_op_result($debug_query,__LINE__,__FILE__);
	}
	$smarty->assign("error_msg", $l_planet_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet_owneddie.tpl");
	include ("footer.php");
	die();
}

if (($planetinfo['owner'] == 0  || $planetinfo['defeated'] == 'Y') && $command != "capture")
{
	if ($planetinfo['owner'] == 0) echo "$l_planet_unowned.<BR><BR>";
	$capture_link="<a href='planet_unowned_capture.php?planet_id=$planet_id'>$l_planet_capture1</a>";
	$l_planet_capture2=str_replace("[capture]",$capture_link,$l_planet_capture2);
	$smarty->assign("error_msg", $l_planet_capture2);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet_owneddie.tpl");
	include ("footer.php");
	die();
}

if ($planetinfo['owner'] != 0)
{
	if ($spy_success_factor)
	{
	  spy_detect_planet($shipinfo['ship_id'], $planetinfo['planet_id'],$planet_detect_success1);
	}
	$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
	$ownerinfo = $result3->fields;

	$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$planetinfo[owner] AND ship_id=$ownerinfo[currentship]");
	$ownershipinfo = $res->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
{

		if($planetinfo['owner'] == $playerinfo['player_id'])
		{
			$isowner = 1;
			$cargoshipcost = 0;

			if($cargoshiphull > 29)
				$cargoshiphull = $planetinfo['cargo_hull'];

			if ($cargoshiphull > $planetinfo['cargo_hull'])
			{
			  $cargoshipcost = phpChangeDelta($cargoshiphull, $planetinfo['cargo_hull']);
			}
			else
			{
				$cargoshiphull = $planetinfo['cargo_hull'];
			}

			if($cargoshippower > 29)
				$cargoshippower = $planetinfo['cargo_power'];

			if ($cargoshippower > $planetinfo['cargo_power'])
			{
			  $cargoshipcost += phpChangeDelta($cargoshippower, $planetinfo['cargo_power']);
			}
			else
			{
				$cargoshippower = $planetinfo['cargo_power'];
			}

			if ($cargoshipcost > $playerinfo['credits'])
			{
				$nomoney = 1;
			}
			else
			{
				$trade_credits = NUMBER(abs($cargoshipcost));
				$tempvar = 0;
				if ($cargoshiphull > $planetinfo['cargo_hull'])
				{
					$tempvar=phpTrueDelta($cargoshiphull, $planetinfo['cargo_hull']);
				}
				$tempvar2 = 0;
				if ($cargoshippower > $planetinfo['cargo_power'])
				{
					$tempvar2=phpTrueDelta($cargoshippower, $planetinfo['cargo_power']);
				}

				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$cargoshipcost,turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull=$cargoshiphull, cargo_power=$cargoshippower WHERE planet_id=$planetinfo[planet_id] and owner=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}

		$smarty->assign("l_planet_nocredits1", $l_planet_nocredits1);
		$smarty->assign("l_planet_nocredits2", $l_planet_nocredits2);
		$smarty->assign("cargoshipcost", NUMBER($cargoshipcost));
		$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
		$smarty->assign("l_trade_result", $l_trade_result);
		$smarty->assign("l_cost", $l_cost);
		$smarty->assign("l_credits", $l_credits);
		$smarty->assign("trade_credits", $trade_credits);
		$smarty->assign("l_hull", $l_hull);
		$smarty->assign("l_trade_upgraded", $l_trade_upgraded);
		$smarty->assign("cargoshiphull", $cargoshiphull);
		$smarty->assign("l_power", $l_power);
		$smarty->assign("l_trade_upgraded", $l_trade_upgraded);
		$smarty->assign("cargoshippower", $cargoshippower);
		$smarty->assign("tempvar2", $tempvar2);
		$smarty->assign("tempvar", $tempvar);
		$smarty->assign("nomoney", $nomoney);
		$smarty->assign("isowner", $isowner);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_igb_term", $l_igb_term);
		$smarty->assign("allow_ibank", $allow_ibank);
		$smarty->assign("l_by_placebounty", $l_by_placebounty);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_ownedupgradecargofinal.tpl");
		include ("footer.php");
		die();

}

close_database();
?>