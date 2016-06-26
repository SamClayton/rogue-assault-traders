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

// calculate trip time and energy scooped

function traderoute_distance($type1, $type2, $start, $dest, $circuit, $sells = 'N')
{
	global $playerinfo, $shipinfo, $smarty;
	global $level_factor;
//	global $db, $dbtables;

	$retvalue['triptime'] = 0;
	$retvalue['scooped1'] = 0;
	$retvalue['scooped2'] = 0;
	$retvalue['scooped'] = 0;
/*
	if ($type1 == 'L')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$start");
	$start = $query->fields;
	}

	if ($type2 == 'L')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$dest");
	$dest = $query->fields;
	}
*/
	if ($start == $dest)
	{
	if ($circuit == '1')
		$retvalue['triptime'] = '1';
	else
		$retvalue['triptime'] = '2';
	return $retvalue;
	}

	$distance=calc_dist($start,$dest);

	$shipspeed = mypw($level_factor, $shipinfo['engines']);
	$triptime = round($distance / $shipspeed);

	if (!$triptime && $destination != $shipinfo['sector_id'])
	$triptime = 1;

	if ($shipinfo['dev_fuelscoop'] == "Y")
		$energyscooped = $distance * 100;
	else
	$energyscooped = 0;

	if ($shipinfo['dev_fuelscoop'] == "Y" && !$energyscooped && $triptime == 1)
	$energyscooped = 100;

	$free_power = NUM_ENERGY($shipinfo['power']);

	if ($free_power < $energyscooped)
	$energyscooped = $free_power;

	if ($energyscooped < 1)
	$energyscooped = 0;

	$retvalue['scooped1'] = floor($energyscooped);

	if ($circuit == '2')
	{
	if ($sells == 'Y' && $shipinfo['dev_fuelscoop'] == 'Y' && $type2 == 'P' && $dest['port_type'] != 'energy')
	{
		$energyscooped = $distance * 100;
		$free_power = NUM_ENERGY($shipinfo['power']);
		if ($free_power < $energyscooped)
		$energyscooped = $free_power;
		$retvalue['scooped2'] = floor($energyscooped);
	}
	elseif ($shipinfo['dev_fuelscoop'] == 'Y')
	{
		$energyscooped = $distance * 100;
		$free_power = NUM_ENERGY($shipinfo['power']);
		if ($free_power < $energyscooped)
		$energyscooped = $free_power;
		$retvalue['scooped2'] = floor($energyscooped);
	}
	}

	if ($circuit == '2')
	{
	$triptime*=2;
	$triptime+=2;
	}
	else
	$triptime+=1;

	$retvalue['triptime'] = $triptime;
	$retvalue['scooped'] = $retvalue['scooped1'] + $retvalue['scooped2'];

	return $retvalue;
}

$smarty->assign("l_tdr_newtdr", $l_tdr_newtdr);
$smarty->assign("l_tdr_modtdrset", $l_tdr_modtdrset);

// List all trade routes

if ($num_traderoutes == 0)
{
	$smarty->assign("l_tdr_noactive", $l_tdr_noactive);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_none.tpl");
	include ("footer.php");
	die();
}else{
	$smarty->assign("color_header", $color_header);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("l_tdr_curtdr", $l_tdr_curtdr);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("l_tdr_src", $l_tdr_src);
	$smarty->assign("l_tdr_srctype", $l_tdr_srctype);
	$smarty->assign("l_tdr_dest", $l_tdr_dest);
	$smarty->assign("l_tdr_desttype", $l_tdr_desttype);
	$smarty->assign("l_tdr_move", $l_tdr_move);
	$smarty->assign("l_tdr_circuit", $l_tdr_circuit);
	$smarty->assign("l_tdr_change", $l_tdr_change);
	$smarty->assign("l_tdr_del", $l_tdr_del);
	$smarty->assign("num_traderoutes", $num_traderoutes);

	$smarty->assign("l_tdr_portin", $l_tdr_portin);
	$smarty->assign("l_tdr_planet", $l_tdr_planet);
	$smarty->assign("l_tdr_within", $l_tdr_within);
	$smarty->assign("l_tdr_nonexistance", $l_tdr_nonexistance);
	$smarty->assign("l_tdr_na", $l_tdr_na);
	$smarty->assign("l_tdr_cargo", $l_tdr_cargo);
	$smarty->assign("l_tdr_none", $l_tdr_none);
	$smarty->assign("l_tdr_colonists", $l_tdr_colonists);
	$smarty->assign("l_tdr_torps", $l_tdr_torps);
	$smarty->assign("l_tdr_fighters", $l_tdr_fighters);
	$smarty->assign("l_tdr_warp", $l_tdr_warp);
	$smarty->assign("l_tdr_turns", $l_tdr_turns);
	$smarty->assign("l_tdr_way", $l_tdr_way);
	$smarty->assign("l_tdr_ways", $l_tdr_ways);
	$smarty->assign("l_tdr_edit", $l_tdr_edit);

	$i = 0;
	while ($i < $num_traderoutes)
	{
		$tradesource_type[$i] = $traderoutes[$i]['source_type'];
		$tradesource_id[$i] = $traderoutes[$i]['source_id'];
		$tradedest_type[$i] = $traderoutes[$i]['dest_type'];
		$tradedest_id[$i] = $traderoutes[$i]['dest_id'];
		$tradecircuit[$i] = $traderoutes[$i]['circuit'];
		$trademove_type[$i] = $traderoutes[$i]['move_type'];
		$traderoute_id[$i] = $traderoutes[$i]['traderoute_id'];

		if ($traderoutes[$i]['source_type'] == 'P')
		{
			$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=" . $traderoutes[$i]['source_id']);
			$port1 = $result->fields;
			$tradesource_port[$i] = t_port($port1['port_type']);
		}else{
			$result = $db->Execute("SELECT name, sector_id FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i]['source_id']);
			if ($result)
			{
				$planet1 = $result->fields;
				$tradesource_planet[$i] = $planet1['sector_id'];
				$tradesource_planetname[$i] = $planet1['name'];
			}else{
				$tradesource_planet[$i] = 0;
			}
		}

		if ($traderoutes[$i]['dest_type'] == 'P')
		{
			$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=" . $traderoutes[$i]['dest_id']);
			$port2 = $result->fields;
			$tradedest_port[$i] = t_port($port2['port_type']);
		}else{
			$result = $db->Execute("SELECT name, sector_id FROM $dbtables[planets] WHERE planet_id=" . $traderoutes[$i]['dest_id']);

			if ($result)
			{
				$planet2 = $result->fields;
				$tradedest_planet[$i] = $planet2['sector_id'];
				$tradedest_planetname[$i] = $planet2['name'];
				$tradedest_planetcolonist[$i] = $playerinfo['trade_colonists'];
				$tradedest_planetfighters[$i] = $playerinfo['trade_fighters'];
				$tradedest_planettorps[$i] = $playerinfo['trade_torps'];
			}else{
				$tradedest_planet[$i] = 0;
			}
		}

		if ($traderoutes[$i]['move_type'] == 'R')
		{
			if ($traderoutes[$i]['source_type'] == 'P')
			{
				$src = $port1;
			}else{
				$src = $planet1;
			}
			if ($traderoutes[$i]['dest_type'] == 'P')
			{
				$dst = $port2;
			}else{
				$dst = $planet2;
			}
			$dist = traderoute_distance($traderoutes[$i]['source_type'], $traderoutes[$i]['dest_type'], $src['sector_id'], $dst['sector_id'], $traderoutes[$i]['circuit']);
			$l_tdr_escooped2 = $l_tdr_escooped;
			$l_tdr_escooped2 = str_replace("[tdr_dist_triptime]", $dist['triptime'], $l_tdr_escooped2);
			$l_tdr_escooped2 = str_replace("[tdr_dist_scooped]", $dist['scooped'], $l_tdr_escooped2);
			$tradedest_move[$i] = $l_tdr_escooped2;
		}else{
			if ($traderoutes[$i]['circuit'] == '1')
			{
				$tradedest_move[$i] = 2;
			}else{
				$tradedest_move[$i] = 4;
			}
		}
		$i++;
	}

	$smarty->assign("tradesource_type", $tradesource_type);
	$smarty->assign("tradesource_id", $tradesource_id);
	$smarty->assign("tradedest_type", $tradedest_type);
	$smarty->assign("tradedest_id", $tradedest_id);
	$smarty->assign("tradecircuit", $tradecircuit);
	$smarty->assign("trademove_type", $trademove_type);
	$smarty->assign("traderoute_id", $traderoute_id);
	$smarty->assign("tradesource_port", $tradesource_port);
	$smarty->assign("tradesource_planet", $tradesource_planet);
	$smarty->assign("tradesource_planetname", $tradesource_planetname);
	$smarty->assign("tradedest_port", $tradedest_port);
	$smarty->assign("tradedest_planet", $tradedest_planet);
	$smarty->assign("tradedest_planetname", $tradedest_planetname);
	$smarty->assign("tradedest_planetcolonist", $tradedest_planetcolonist);
	$smarty->assign("tradedest_planetfighters", $tradedest_planetfighters);
	$smarty->assign("tradedest_planettorps", $tradedest_planettorps);
	$smarty->assign("tradedest_move", $tradedest_move);

	$smarty->assign("l_delete", $l_tdr_del);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_listroutes.tpl");
	include ("footer.php");
}

?>
