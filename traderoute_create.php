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

// create new trade route

if ($num_traderoutes >= $max_traderoutes_player)
{
	$smarty->assign("error_msg", $l_tdr_maxtdr);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_die.tpl");
	include ("footer.php");
	die();
}

$smarty->assign("l_tdr_createnew", $l_tdr_createnew);
$smarty->assign("l_tdr_traderoute", $l_tdr_traderoute);

//---------------------------------------------------
//---- Get Planet info team and Personal (BEGIN) ----

$result = $db->Execute("SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[player_id] ORDER BY sector_id");

$num_planets = $result->RecordCount();
$i=0;
while (!$result->EOF)
{
	$planets[$i] = $result->fields;
	if ($planets[$i]['name'] == "")
		$planets[$i]['name'] = $l_tdr_unnamed;
	$i++;
	$result->MoveNext();
}

$result = $db->Execute("SELECT * FROM $dbtables[planets] WHERE team=$playerinfo[team] AND team!=0 AND owner<>$playerinfo[player_id] ORDER BY sector_id");
$num_team_planets = $result->RecordCount();
$i=0;
while (!$result->EOF)
{
	$planets_team[$i] = $result->fields;
	if ($planets_team[$i]['name'] == "")
		$planets_team[$i]['name'] = $l_tdr_unnamed;
	$i++;
	$result->MoveNext();
}
//---- Get Planet info team and Personal (END) ------
//---------------------------------------------------
// Display Current Sector
$smarty->assign("l_tdr_cursector", $l_tdr_cursector);
$smarty->assign("shipsector", $shipinfo['sector_id']);

$smarty->assign("l_tdr_selspoint", $l_tdr_selspoint);
$smarty->assign("l_tdr_port", $l_tdr_port);

//-------------------- Personal Planet
$smarty->assign("l_tdr_planet", $l_tdr_planet);
$smarty->assign("num_planets", $num_planets);
$smarty->assign("l_tdr_none", $l_tdr_none);

if ($num_planets != 0)
{
	$planetcount=0;
	while ($planetcount < $num_planets)
	{
		if ($planets[$planetcount]['planet_id'] == $editroute['source_id']){
			$planetselected[$planetcount] = "selected ";
		}else{
			$planetselected[$planetcount] = " ";
		}
		$planetid[$planetcount] = $planets[$planetcount]['planet_id'];
		$planetname[$planetcount] = $planets[$planetcount]['name'];
		$planetsectorid[$planetcount] = $planets[$planetcount]['sector_id'];
		$planetcount++;
	}
}
$smarty->assign("planetcount", $planetcount);
$smarty->assign("planetselected", $planetselected);
$smarty->assign("l_tdr_insector", $l_tdr_insector);
$smarty->assign("planetid", $planetid);
$smarty->assign("planetname", $planetname);
$smarty->assign("planetsectorid", $planetsectorid);

//----------------------- team Planet

$smarty->assign("l_team", $l_team);
$smarty->assign("l_tdr_planet", $l_tdr_planet);

$smarty->assign("num_team_planets", $num_team_planets);

if ($num_team_planets != 0)
{
	$planetcountteam=0;
	while ($planetcountteam < $num_team_planets)
	{
		if ($planets_team[$planetcountteam]['planet_id'] == $editroute['source_id']){
			$planetselectedteam[$planetcountteam] = "selected ";
		}else{
			$planetselectedteam[$planetcountteam] = " ";
		}
		$planetidteam[$planetcountteam] = $planets_team[$planetcountteam]['planet_id'];
		$planetnameteam[$planetcountteam] = $planets_team[$planetcountteam]['name'];
		$planetsectoridteam[$planetcountteam] = $planets_team[$planetcountteam]['sector_id'];
		$planetcountteam++;
	}
}

$smarty->assign("planetcountteam", $planetcountteam);
$smarty->assign("planetselectedteam", $planetselectedteam);
$smarty->assign("planetidteam", $planetidteam);
$smarty->assign("planetnameteam", $planetnameteam);
$smarty->assign("planetsectoridteam", $planetsectoridteam);

//----------------------- End Start point selection
//----------------------- Begin Ending point selection

$smarty->assign("l_tdr_selendpoint", $l_tdr_selendpoint);
$smarty->assign("l_tdr_planet", $l_tdr_planet);
$smarty->assign("planetsectoridteam", $planetsectoridteam);

//----------------------- End finishing point selection

$smarty->assign("l_tdr_selmovetype", $l_tdr_selmovetype);
$smarty->assign("l_tdr_realspace", $l_tdr_realspace);
$smarty->assign("l_tdr_warp", $l_tdr_warp);
$smarty->assign("l_tdr_selcircuit", $l_tdr_selcircuit);
$smarty->assign("l_tdr_oneway", $l_tdr_oneway);
$smarty->assign("l_tdr_bothways", $l_tdr_bothways);
$smarty->assign("l_tdr_create", $l_tdr_create);
$smarty->assign("l_tdr_returnmenu", $l_tdr_returnmenu);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."traderoute_create.tpl");
include ("footer.php");

?>
