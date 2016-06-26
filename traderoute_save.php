<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the		 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: traderoute_create.php

include ("config/config.php");
include ("languages/$langdir/lang_traderoute.inc");
include ("languages/$langdir/lang_teams.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_ports.inc");
$no_gzip = 1;
$total_experience = 0;

$title = $l_tdr_title;

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

//-------------------------------------------------------------------------------------------------

bigtitle();

$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($max_query,__LINE__,__FILE__);

$sector_max = $max_query->fields['sector_id'];

$result = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE owner=$playerinfo[player_id]");
$num_traderoutes = $result->RecordCount();

$i = 0;
while (!$result->EOF)
{
	$traderoutes[$i] = $result->fields;
	$i++;
	$result->MoveNext();
}

// Error in trade route

function traderoute_die($error_msg)
{
	global $templatename, $playerinfo, $l_global_mmenu, $total_experience, $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks, $smarty;

	$smarty->assign("error_msg", $error_msg);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_die.tpl");
	include ("footer.php");
	die();
}


// check if valid trade route

function traderoute_check_compatible($type1, $type2, $move, $circuit, $src, $dest)
{
	global $playerinfo, $smarty;
	global $l_tdr_nowlink1, $l_tdr_nowlink2, $l_tdr_sportissrc, $l_tdr_notownplanet, $l_tdr_planetisdest;
	global $l_tdr_samecom, $l_tdr_sportcom, $l_tdr_invalidspoint;
	global $db, $dbtables, $sector_max;

	if ($move != 'warp')
	{
		$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$src[sector_id]");
		$src_type = $sector_res->fields['sg_sector'];
		$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$dest[sector_id]");
		$dst_type = $sector_res->fields['sg_sector'];
		if(($src_type == 1) or ($dst_type == 1) or ($src['sector_id'] > $sector_max) or ($dest['sector_id'] > $sector_max))
		{
			if($src['sector_id'] != $dest['sector_id']){
				$l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $src['sector_id'], $l_tdr_nowlink1);
				$l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
				traderoute_die($l_tdr_nowlink1);
			}
		}
	}

	//check warp links compatibility
	if ($move == 'warp')
	{
	$query = $db->Execute("SELECT link_id FROM $dbtables[links] WHERE link_start=$src[sector_id] AND link_dest=$dest[sector_id]");
	if ($query->EOF)
	{
		$l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $src['sector_id'], $l_tdr_nowlink1);
		$l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
		traderoute_die($l_tdr_nowlink1);
	}
	if ($circuit == '2')
	{
		$query = $db->Execute("SELECT link_id FROM $dbtables[links] WHERE link_start=$dest[sector_id] AND link_dest=$src[sector_id]");
		if ($query->EOF)
		{
		$l_tdr_nowlink2 = str_replace("[tdr_src_sector_id]", $src['sector_id'], $l_tdr_nowlink2);
		$l_tdr_nowlink2 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink2);
		traderoute_die($l_tdr_nowlink2);
		}
	}
	}

	//check ports compatibility
	if ($type1 == 'port')
	{
	if ($src['port_type'] == 'upgrades')
	{
		if (($type2 != 'planet') && ($type2 != 'team_planet'))
		traderoute_die($l_tdr_sportissrc);
		if ($dest['owner'] != $playerinfo['player_id'] && ($dest['team'] == 0 || ($dest['team'] != $playerinfo['team'])))
		traderoute_die($l_tdr_notownplanet);
	}
	else
	{

		if ($type2 != 'planet')
		{
			if ($src['port_type'] == $dest['port_type'])
			{
				traderoute_die($l_tdr_samecom);
			}
		}
	}
	if ($src['port_type'] == 'devices')
	{
		traderoute_die($l_tdr_invalidspoint);
	}
	}
	else
	{
	if ($dest['port_type'] == 'upgrades')
		traderoute_die($l_tdr_sportcom);
	}
}


	if ($num_traderoutes >= $max_traderoutes_player && empty($editing))
	traderoute_die($l_tdr_maxtdr);

	//dbase sanity check for source
	if ($ptype1 == 'port')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id1	");
	if (!$query || $query->EOF)
	{
		$l_tdr_errnotvalidport = str_replace("[tdr_port_id]", $port_id1, $l_tdr_errnotvalidport);
		traderoute_die($l_tdr_errnotvalidport);
	}

	$source=$query->fields;
	if ($source['port_type'] == 'none')
	{
		$l_tdr_errnoport = str_replace("[tdr_port_id]", $port_id1, $l_tdr_errnoport);
		traderoute_die($l_tdr_errnoport);
	}

	// ensure that they have been in the sector for the first port, but only if its a valid port type.
	$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND (source = $port_id1 or destination = $port_id1)");
	if (!$res1 || $res1->EOF)
	{
		 $res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id1");
		 if (!$res11 || $res11->EOF)
		 {
			traderoute_die($l_tdr_explorefirst);
		 }
	}

	}
	else
	{
		if ($ptype1 == "planet") {
			$query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id1");
			if (!$query || $query->EOF) { traderoute_die($l_tdr_errnosrc); }
			$source=$query->fields;
			//hum, that thing was tricky
			if ($source['owner'] != $playerinfo['player_id']) 	{
				if ($playerinfo['team'] == 0 || $playerinfo['team'] != $source['team']) {
					$l_tdr_errnotownnotsell = str_replace("[tdr_source_name]", $source['name'], $l_tdr_errnotownnotsell);
					traderoute_die($l_tdr_errnotownnotsell);
				}
			}
		} else {
			$query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$team_planet_id1");
			if (!$query || $query->EOF) { traderoute_die($l_tdr_errnosrc); }
			$source=$query->fields;
			//hum, that thing was tricky
			if ($playerinfo['team'] == 0 || $playerinfo['team'] != $source['team']) {
				$l_tdr_errnotownnotsell = str_replace("[tdr_source_name]", $source['name'], $l_tdr_errnotownnotsell);
				traderoute_die($l_tdr_errnotownnotsell);
			}
		}
	}

	//dbase sanity check for dest
	if ($ptype2 == 'port')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$port_id2 ");
	if (!$query || $query->EOF)
	{
		$l_tdr_errnotvaliddestport = str_replace("[tdr_port_id]", $port_id2, $l_tdr_errnotvaliddestport);
		traderoute_die($l_tdr_errnotvaliddestport);
	}

	$destination=$query->fields;
	if ($destination['port_type'] == 'none') {
		$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id2, $l_tdr_errnoport2);
		traderoute_die($l_tdr_errnoport2);
	}

	$destination=$query->fields;
	if ($destination['port_type'] == 'devices' || $destination['port_type'] == 'upgrades')
	{
		$l_tdr_errnoport2 = str_replace("[tdr_port_id]", $port_id2, $l_tdr_errnoport2);
		traderoute_die($l_tdr_errnoport2);
	}

	// ensure that they have been in the sector for the second port, but only if its a valid port type.
	$res1 = $db->Execute("SELECT * from $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] AND (source = $port_id2 or destination = $port_id2)");
	if (!$res1 || $res1->EOF)
	{
		 $res11 = $db->Execute("SELECT * from $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] AND sector_id = $port_id2");
		 if (!$res11 || $res11->EOF)
		 {
			traderoute_die($l_tdr_explorefirst);
		 }
	}

	}
	else
	{
		if ($ptype2 == "planet") {
			$query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id2");
			if (!$query || $query->EOF) {
				traderoute_die($l_tdr_errnodestplanet);
			}
			$destination=$query->fields;
	
			if ($destination['owner'] != $playerinfo['player_id']) {
				$l_tdr_errnotownnotsell2 = str_replace("[tdr_dest_name]", $destination['name'], $l_tdr_errnotownnotsell2);
				traderoute_die($l_tdr_errnotownnotsell2);
			}
		} else {
			$query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$team_planet_id2");
			if (!$query || $query->EOF) {
				traderoute_die($l_tdr_errnodestplanet);
			}
			$destination=$query->fields;
			if ($playerinfo['team'] == 0 || $destination['team'] != $playerinfo['team']) {
				$l_tdr_errnotownnotsell2 = str_replace("[tdr_dest_name]", $destination['name'], $l_tdr_errnotownnotsell2);
				traderoute_die($l_tdr_errnotownnotsell2);
			}
		}
	}

	//check traderoute for src => dest
	traderoute_check_compatible($ptype1, $ptype2, $move_type, $circuit_type, $source , $destination);

	if ($ptype1 == 'port')
	$src_id = $port_id1;
	elseif ($ptype1 == 'planet')
	$src_id = $planet_id1;
	elseif ($ptype1 == 'team_planet')
	$src_id = $team_planet_id1;


	if ($ptype2 == 'port')
	$dest_id = $port_id2;
	elseif ($ptype2 == 'planet')
	$dest_id = $planet_id2;
	elseif ($ptype2 == 'team_planet')
	$dest_id = $team_planet_id2;


	if ($ptype1 == 'port')
	$src_type = 'P';
	elseif ($ptype1 == 'planet')
	$src_type = 'L';
	elseif ($ptype1 == 'team_planet')
	$src_type = 'C';


	if ($ptype2 == 'port')
	$dest_type = 'P';
	elseif ($ptype2 == 'planet')
	$dest_type = 'L';
	elseif ($ptype2 == 'team_planet')
	$dest_type = 'C';

	if ($move_type == 'realspace')
	$mtype = 'R';
	else
	$mtype = 'W';

	if (empty($editing))
	{
		$debug_query = $db->Execute("INSERT INTO $dbtables[traderoutes] VALUES('', $src_id, $dest_id, '$src_type', '$dest_type', '$mtype', $playerinfo[player_id], '$circuit_type')");
		db_op_result($debug_query,__LINE__,__FILE__);
		$smarty->assign("l_changed", $l_tdr_newtdrcreated);
	}
	else
	{
		$debug_query = $db->Execute("UPDATE $dbtables[traderoutes] SET source_id=$src_id, dest_id=$dest_id, source_type='$src_type', dest_type='$dest_type', move_type='$mtype', owner=$playerinfo[player_id], circuit='$circuit_type' WHERE traderoute_id=$editing");
		db_op_result($debug_query,__LINE__,__FILE__);
		$smarty->assign("l_changed", $l_tdr_tdrmodified);
	}

	$smarty->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_save.tpl");
	include ("footer.php");

?>
