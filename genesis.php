<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: genesis.php

include ("config/config.php");
include ("languages/$langdir/lang_genesis.inc");

$title = $l_gns_title;

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

$result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$shipinfo[sector_id]'");
$num_planets = $result3->RecordCount();

$res = $db->Execute("SELECT $dbtables[universe].zone_id, $dbtables[zones].allow_planet, $dbtables[zones].team_zone, " .
					"$dbtables[zones].owner FROM $dbtables[zones],$dbtables[universe] WHERE " .
					"$dbtables[zones].zone_id=$sectorinfo[zone_id] AND $dbtables[universe].sector_id = $shipinfo[sector_id]");
$query97 = $res->fields;

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$dobuild = 0;

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_gns_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genesisdie.tpl");
	include ("footer.php");
	die();
}
elseif ($shipinfo['on_planet'] == 'Y')
{
	$smarty->assign("error_msg", $l_gns_onplanet);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genesisdie.tpl");
	include ("footer.php");
	die();
}
elseif ($num_planets >= $sectorinfo['star_size'])
{
	$smarty->assign("error_msg", $l_gns_full);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genesisdie.tpl");
	include ("footer.php");
	die();
}
elseif ($shipinfo['dev_genesis'] < 1)
{
	$smarty->assign("error_msg", $l_gns_nogenesis);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genesisdie.tpl");
	include ("footer.php");
	die();
}
else
{
	if ($query97['allow_planet'] == 'N')
	{
		// foo - error occurs here, although removing this section leaves no way for the creation to occur.
		$smarty->assign("error_msg", $l_gns_forbid);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genesisdie.tpl");
		include ("footer.php");
		die();
	}
	elseif ($query97['allow_planet'] == 'L')
	{
		if ($query97['team_zone'] == 'N')
		{
			if ($playerinfo['team'] == 0 && $playerinfo['player_id'] != $query97['owner'])
			{
				$smarty->assign("error_msg", $l_gns_bforbid);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."genesisdie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$query97[owner]");
				$ownerinfo = $res->fields;
				if ($ownerinfo['team'] != $playerinfo['team'] && $playerinfo['player_id'] != $query97['owner'])
				{
					$smarty->assign("error_msg", $l_gns_bforbid);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."genesisdie.tpl");
					include ("footer.php");
					die();
				}
				else
				{
					$dobuild = 1;
				}
			}
		}
		elseif ($playerinfo['team'] != $query97['owner'])
		{
			$smarty->assign("error_msg", $l_gns_bforbid);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."genesisdie.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			$dobuild = 1;
		}
	}
	else
	{
		$dobuild = 1;
	}
}

if($dobuild)
{
	$query1 = "INSERT INTO $dbtables[planets] (sector_id, name, organics, ore, goods, 
				energy, colonists, credits, computer, sensors, beams, torp_launchers, torps, shields, 
				jammer, armour, armour_pts, cloak, fighters, owner, team, base, defeated, prod_organics, 
				prod_ore, prod_goods, prod_energy, prod_fighters, prod_torp, max_credits )
				VALUES($shipinfo[sector_id], NULL, 0, 0, 0, 0, 0, 0,0, 0, 0, 0, " .
				"0, 0, 0, 0, 0, 0, 0, $playerinfo[player_id], 0, 'N','N',  $default_prod_organics, " .
				"$default_prod_ore, $default_prod_goods, $default_prod_energy, $default_prod_fighters, " .
				"$default_prod_torp, $base_credits)";

	$debug_query = $db->Execute($query1);
	db_op_result($debug_query,__LINE__,__FILE__);

	$query2 = "UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1, planets_built=planets_built+1 WHERE " .
			  "player_id=$playerinfo[player_id]";
	$debug_query = $db->Execute($query2);
	db_op_result($debug_query,__LINE__,__FILE__);

	$query3 = "UPDATE $dbtables[ships] SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]";
	$debug_query = $db->Execute($query3);
	db_op_result($debug_query,__LINE__,__FILE__);

	$logres = $db->Execute("SELECT MAX(planet_id) AS planet_id FROM $dbtables[planets] WHERE owner = $playerinfo[player_id]");
	$newplanet_id = $logres->fields['planet_id'];
	$result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$shipinfo[sector_id]'");
	$num_planets = $result3->RecordCount();
	if ($num_planets > $sectorinfo['star_size'])
	{
		$debug_query = $db->Execute("DELETE from $dbtables[planets] where planet_id=$newplanet_id");
		db_op_result($debug_query,__LINE__,__FILE__);
		$smarty->assign("error_msg", $l_gns_full);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genesisdie.tpl");
		include ("footer.php");
		die();
	}
	else
	{
		update_player_experience($playerinfo['player_id'], $building_planet);
		planet_log($newplanet_id,$playerinfo['player_id'],$playerinfo['player_id'],PLOG_GENESIS_CREATE);
		$smarty->assign("error_msg", $l_gns_pcreate);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genesisdie.tpl");
		include ("footer.php");
		die();
	}
}

close_database();
?>
