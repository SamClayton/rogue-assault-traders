<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_base.php

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

		/* build a base */
		if ($planetinfo['ore'] >= $base_ore && $planetinfo['organics'] >= $base_organics && $planetinfo['goods'] >= $base_goods && $planetinfo['credits'] >= $base_credits)
		{
			// Check to see if the player has less than one turn available
			// and if so return to the main menu
			if ($playerinfo['turns']<1)
			{
				$smarty->assign("error_msg", $l_planet_basenoturn);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."planet_owneddie.tpl");
				include ("footer.php");
				die();
			}

			// ** Create The Base
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET base='Y', ore=$planetinfo[ore]-$base_ore, organics=$planetinfo[organics]-$base_organics, goods=$planetinfo[goods]-$base_goods, credits=$planetinfo[credits]-$base_credits WHERE planet_id=$planet_id and owner=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			// ** Update User Turns
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1 where player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			// ** Refresh Plant Info
			$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
			$planetinfo=$result3->fields;

			// ** Notify User Of Base Results
			$result = $l_planet_bbuild;
			// ** Calc Ownership and Notify User Of Results
			$ownership = calc_ownership($shipinfo['sector_id']);
		}
		else
		{
			$result = $l_planet_baseinfo;
		}

		$smarty->assign("ownership", $ownership);
		$smarty->assign("result", $result);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_igb_term", $l_igb_term);
		$smarty->assign("allow_ibank", $allow_ibank);
		$smarty->assign("l_by_placebounty", $l_by_placebounty);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_ownedgeneric1.tpl");
		include ("footer.php");
		die();

}

close_database();
?>