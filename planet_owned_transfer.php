<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_transfer.php

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

		/* transfer menu */
		$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
		$free_power = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];

		$l_planet_cinfo=str_replace("[cargo]",NUMBER($free_holds),$l_planet_cinfo);
		$l_planet_cinfo=str_replace("[energy]",NUMBER($free_power),$l_planet_cinfo);

		if ($spy_success_factor and ($playerinfo['player_id'] == $planetinfo['owner']))
		{
			$res = $db->execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
			$n_pl = $res->RecordCount();
			$res = $db->execute("SELECT * FROM $dbtables[spies] WHERE ship_id = '$shipinfo[ship_id]' AND owner_id = '$playerinfo[player_id]' ");
			$n_sh = $res->RecordCount();
			$spytransfer = 1;
		}
		if ($dig_success_factor and ($playerinfo['player_id'] == $planetinfo['owner']))
		{
			$res = $db->execute("SELECT * FROM $dbtables[dignitary] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
			$n_pld = $res->RecordCount();
			$res = $db->execute("SELECT * FROM $dbtables[dignitary] WHERE ship_id = '$shipinfo[ship_id]' AND owner_id = '$playerinfo[player_id]' ");
			$n_shd = $res->RecordCount();
			$digtransfer = 1;
		}

		$smarty->assign("l_planet", $l_planet);
		$smarty->assign("spytransfer", $spytransfer);
		$smarty->assign("digtransfer", $digtransfer);
		$smarty->assign("l_planet_transfer_link", $l_planet_transfer_link);
		$smarty->assign("l_reset", $l_reset);
		$smarty->assign("l_planet_cinfo", $l_planet_cinfo);
		$smarty->assign("l_commodity", $l_commodity);
		$smarty->assign("l_ore", $l_ore);
		$smarty->assign("l_organics", $l_organics);
		$smarty->assign("l_goods", $l_goods);
		$smarty->assign("l_energy", $l_energy);
		$smarty->assign("l_colonists", $l_colonists);
		$smarty->assign("l_fighters", $l_fighters);
		$smarty->assign("l_torps", $l_torps);
		$smarty->assign("l_credits", $l_credits);
		$smarty->assign("l_max", $l_max);
		$smarty->assign("planetmaxcredit", NUMBER($planetinfo['max_credits']));
		$smarty->assign("planetore", NUMBER($planetinfo['ore']));
		$smarty->assign("planetorganics", NUMBER($planetinfo['organics']));
		$smarty->assign("planetgoods", NUMBER($planetinfo['goods']));
		$smarty->assign("planetenergy", NUMBER($planetinfo['energy']));
		$smarty->assign("planetcolonists", NUMBER($planetinfo['colonists']));
		$smarty->assign("planetfighters", NUMBER($planetinfo['fighters']));
		$smarty->assign("planettorps", NUMBER($planetinfo['torps']));
		$smarty->assign("planetcredits", NUMBER($planetinfo['credits']));
		$smarty->assign("shipore", NUMBER($shipinfo['ore']));
		$smarty->assign("shiporganics", NUMBER($shipinfo['organics']));
		$smarty->assign("shipgoods", NUMBER($shipinfo['goods']));
		$smarty->assign("shipenergy", NUMBER($shipinfo['energy']));
		$smarty->assign("shipcolonists", NUMBER($shipinfo['colonists']));
		$smarty->assign("shipfighters", NUMBER($shipinfo['fighters']));
		$smarty->assign("shiptorps", NUMBER($shipinfo['torps']));
		$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
		$smarty->assign("color_header", $color_header);
		$smarty->assign("color_line1", $color_line1);
		$smarty->assign("color_line2", $color_line2);
		$smarty->assign("l_ship", $l_ship);
		$smarty->assign("l_planet_transfer_link", $l_planet_transfer_link);
		$smarty->assign("l_planet_toplanet", $l_planet_toplanet);
		$smarty->assign("l_all", $l_all);
		$smarty->assign("l_spy", $l_spy);
		$smarty->assign("n_pl", NUMBER($n_pl));
		$smarty->assign("n_sh", NUMBER($n_sh));
		$smarty->assign("l_dig", $l_dig);
		$smarty->assign("n_pld", NUMBER($n_pld));
		$smarty->assign("n_shd", NUMBER($n_shd));
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_igb_term", $l_igb_term);
		$smarty->assign("allow_ibank", $allow_ibank);
		$smarty->assign("l_by_placebounty", $l_by_placebounty);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_ownedtransfer.tpl");
		include ("footer.php");
		die();

}

close_database();
?>