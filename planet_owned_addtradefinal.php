<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_addtradefinal.php

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

		if($planetinfo['owner'] == $playerinfo['player_id'])
		{
			//dbase sanity check for source
			$debug_query = $db->Execute("SELECT * FROM $dbtables[autotrades] WHERE planet_id=$planet_id");
			db_op_result($debug_query,__LINE__,__FILE__);

			if ($debug_query->RecordCount() != 0)
			{
				$smarty->assign("error_msg", $l_tdr_errhaveauto);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."planet_owneddie.tpl");
				include ("footer.php");
				die();
			}

			$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
			db_op_result($max_query,__LINE__,__FILE__);

			$sector_max = $max_query->fields['sector_id'];

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$destination");
			$sector_type = $sector_res->fields['sg_sector'];

			$query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id and sector_id <= $sector_max");
			if (!$query || $query->EOF || $sector_type == 1)
			{
				$smarty->assign("error_msg", $l_tdr_errnosrc);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."planet_owneddie.tpl");
				include ("footer.php");
				die();
			}

			$source=$query->fields;

			if($port_id_goods > 0){
				//dbase sanity check for dest
				$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
				db_op_result($max_query,__LINE__,__FILE__);

				$sector_max = $max_query->fields['sector_id'];

				$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id_goods and sector_id <= $sector_max and sg_sector != 1");
				if (!$query || $query->EOF)
				{
					$l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $port_id_goods, $l_tdr_errnotvaliddestport);
					$smarty->assign("error_msg", $l_tdr_errnotvaliddestport);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'none')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_goods, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_goods, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				// ensure that they have been in the sector for the second port, but only if its a valid port type.
				$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND source = $port_id_goods");
				if (!$res1 || $res1->EOF)
				{
					$res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id_goods");
					if (!$res11 || $res11->EOF)
					{
						$smarty->assign("error_msg", $l_tdr_explorefirst);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."planet_owneddie.tpl");
						include ("footer.php");
						die();
					}
				}
			}

			if($port_id_ore > 0)
			{
				//dbase sanity check for dest
				$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
				db_op_result($max_query,__LINE__,__FILE__);

				$sector_max = $max_query->fields['sector_id'];

				$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id_ore and sector_id <= $sector_max and sg_sector != 1");
				if (!$query || $query->EOF)
				{
					$l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $port_id_ore, $l_tdr_errnotvaliddestport);
					$smarty->assign("error_msg", $l_tdr_errnotvaliddestport);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'none')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_ore, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_ore, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				// ensure that they have been in the sector for the second port, but only if its a valid port type.
				$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND source = $port_id_ore");
				if (!$res1 || $res1->EOF)
				{
					$res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id_ore");
					if (!$res11 || $res11->EOF)
					{
						$smarty->assign("error_msg", $l_tdr_explorefirst);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."planet_owneddie.tpl");
						include ("footer.php");
						die();
					}
				}
			}

			if($port_id_organics > 0)
			{
				//dbase sanity check for dest
				$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
				db_op_result($max_query,__LINE__,__FILE__);

				$sector_max = $max_query->fields['sector_id'];

				$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id_organics and sector_id <= $sector_max and sg_sector != 1");
				if (!$query || $query->EOF)
				{
					$l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $port_id_organics, $l_tdr_errnotvaliddestport);
					$smarty->assign("error_msg", $l_tdr_errnotvaliddestport);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'none')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_organics, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_organics, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				// ensure that they have been in the sector for the second port, but only if its a valid port type.
				$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND source = $port_id_organics");
				if (!$res1 || $res1->EOF)
				{
					$res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id_organics");
					if (!$res11 || $res11->EOF)
					{
						$smarty->assign("error_msg", $l_tdr_explorefirst);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."planet_owneddie.tpl");
						include ("footer.php");
						die();
					}
				}
			}

			if($port_id_energy > 0)
			{
				//dbase sanity check for dest
				$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
				db_op_result($max_query,__LINE__,__FILE__);

				$sector_max = $max_query->fields['sector_id'];

				$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id_energy and sector_id <= $sector_max and sg_sector != 1");
				if (!$query || $query->EOF)
				{
					$l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $port_id_energy, $l_tdr_errnotvaliddestport);
					$smarty->assign("error_msg", $l_tdr_errnotvaliddestport);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'none')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_energy, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				$destination=$query->fields;
				if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
				{
					$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id_energy, $l_tdr_errnoport2);
					$smarty->assign("error_msg", $l_tdr_errnoport2);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planet_owneddie.tpl");
					include ("footer.php");
					die();
				}

				// ensure that they have been in the sector for the second port, but only if its a valid port type.
				$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND source = $port_id_energy");
				if (!$res1 || $res1->EOF)
				{
					$res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id_energy");
					if (!$res11 || $res11->EOF)
					{
						$smarty->assign("error_msg", $l_tdr_explorefirst);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."planet_owneddie.tpl");
						include ("footer.php");
						die();
					}
				}
			}

			$src_id = $planet_id;

			$debug_query = $db->Execute("INSERT INTO $dbtables[autotrades] (planet_id, port_id_goods, port_id_ore, port_id_organics, port_id_energy, owner ) VALUES($src_id, $port_id_goods, $port_id_ore, $port_id_organics, $port_id_energy, $playerinfo[player_id])");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("error_msg", $l_planet_tradeadded);
		}

		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_owneddie.tpl");
		include ("footer.php");
		die();

}

close_database();
?>