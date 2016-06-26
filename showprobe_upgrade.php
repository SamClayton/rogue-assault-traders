<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet2.php

include ("config/config.php");
include ("languages/$langdir/lang_probes.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");

if ((!isset($probe_id)) || ($probe_id == ''))
{
	$probe_id = '';
}

if ((!isset($probeinfo)) || ($probeinfo == ''))
{
	$probeinfo = '';
}

$title = $l_probe2_title;

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

$result2 = $db->Execute("SELECT * FROM $dbtables[probe] WHERE probe_id=$probe_id");
if ($result2)
{
	$probeinfo = $result2->fields;
}

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if ($probeinfo['sector_id'] <> $shipinfo['sector_id'])
{
	$smarty->assign("error_msg", $l_probe2_sector);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genericdie.tpl");
	include ("footer.php");
	die();
}

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_probe2_noturn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genericdie.tpl");
	include ("footer.php");
	die();
}
else
{
	$smarty->assign("isprobe", (!empty($probeinfo) && !empty($probeupgrade)));

	if (!empty($probeinfo) && !empty($probeupgrade))
	{
		$engines_upgrade_cost = 0;
		if ($engines_upgrade > 54)
		  $engines_upgrade = 54;

		if ($engines_upgrade < 0)
		  $engines_upgrade = 0;

		if ($engines_upgrade > $probeinfo['engines'])
		{
			$engines_upgrade_cost = phpChangeDelta($engines_upgrade, $probeinfo['engines']);
		}

		$sensors_upgrade_cost = 0;
		if ($sensors_upgrade > 54)
			$sensors_upgrade = 54;

		if ($sensors_upgrade < 0)
			$sensors_upgrade = 0;

		if ($sensors_upgrade > $probeinfo['sensors'])
		{
			$sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $probeinfo['sensors']);
		}

		$cloak_upgrade_cost = 0;
		if ($cloak_upgrade > 54)
			$cloak_upgrade = 54;

		if ($cloak_upgrade < 0)
			$cloak_upgrade = 0;

		if ($cloak_upgrade > $probeinfo['cloak'])
		{
			$cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $probeinfo['cloak']);
		}

		$total_cost = $engines_upgrade_cost + $sensors_upgrade_cost + $cloak_upgrade_cost;

		$smarty->assign("total_cost", $total_cost);
		$smarty->assign("playercredits", $playerinfo['credits']);
		if ($total_cost > $playerinfo['credits'])
		{
			$smarty->assign("l_probe2_nocredits1", $l_probe2_nocredits1);
			$smarty->assign("total_cost_n", NUMBER($total_cost));
			$smarty->assign("l_probe2_nocredits2", $l_probe2_nocredits2);
			$smarty->assign("playercredits_n", NUMBER($playerinfo[credits]));
			$smarty->assign("l_credits", $l_credits);
		}
		else
		{
			$trade_credits = NUMBER(abs($total_cost));
			$smarty->assign("trade_credits", $trade_credits);
			$smarty->assign("l_trade_result", $l_trade_result);
			$smarty->assign("l_cost", $l_cost);

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$total_cost,turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$query = "UPDATE $dbtables[probe] SET probe_id=$probe_id ";

			$smarty->assign("l_trade_upgraded", $l_trade_upgraded);
			$smarty->assign("isengineupgrade", ($engines_upgrade > $probeinfo['engines']));
			if ($engines_upgrade > $probeinfo['engines'])
			{
				$query = $query . ", engines=$engines_upgrade";
				$smarty->assign("l_engines", $l_engines);
				$smarty->assign("engines_upgrade", $engines_upgrade);
			}

			$smarty->assign("issensorupgrade", ($sensors_upgrade > $probeinfo['sensors']));
			if ($sensors_upgrade > $probeinfo['sensors'])
			{
				$query = $query . ", sensors=$sensors_upgrade";
				$smarty->assign("l_sensors", $l_sensors);
				$smarty->assign("sensors_upgrade", $sensors_upgrade);
			}

			$smarty->assign("iscloakupgrade", ($cloak_upgrade > $probeinfo['cloak']));
			if ($cloak_upgrade > $probeinfo['cloak'])
			{
				$query = $query . ", cloak=$cloak_upgrade";
				$smarty->assign("l_cloak", $l_cloak);
				$smarty->assign("cloak_upgrade", $cloak_upgrade);
			}

			$query = $query . " WHERE probe_id=$probe_id";
			$debug_query = $db->Execute("$query");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

$smarty->assign("probe_id", $probe_id);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("l_toprobemenu", $l_toprobemenu);

$smarty->assign("error_msg", $l_probe2_noturn);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."showprobe_upgrade.tpl");

include ("footer.php");

?>
