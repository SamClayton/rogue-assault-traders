<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: team-planets.php

include ("config/config.php");
include ("languages/$langdir/lang_team_planets.inc");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_ports.inc");

$title = $l_teamplanet_title;

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

if ($playerinfo['team'] == 0)
{
	$smarty->assign("error_msg", $l_teamplanet_notally);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team-planetsdie.tpl");
	include ("footer.php");
	die();
}

$query = "SELECT * FROM $dbtables[planets] WHERE team=$playerinfo[team]";
if (!empty($sort))
{
	$query .= " ORDER BY";
	if ($sort == "name")
	{
		$query .= " $sort ASC";
	}
	elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || $sort == "colonists" || $sort == "credits" || $sort == "fighters")
	{
		$query .= " $sort DESC";
	}
	elseif ($sort == "torp")
	{
		$query .= " torps DESC";
	}
	elseif ($sort == "max_credits")
	{
		$query .= " max_credits DESC, sector_id ASC";
	}
	else
	{
		$query .= " sector_id ASC";
	}
}

$res = $db->Execute($query);

$i = 0;
if ($res)
{
	while (!$res->EOF)
	{
		$planet[$i] = $res->fields;
		///
		if ($spy_success_factor)
		{
			spy_detect_planet($shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);
		}

		$i++;
		$res->Movenext();
	}
}

$num_planets = $i;
if ($num_planets < 1)
{
	$smarty->assign("error_msg", $l_teamplanet_noplanet);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team-planetsdie.tpl");
	include ("footer.php");
	die();
}

$total_organics = 0;
$total_ore = 0;
$total_goods = 0;
$total_energy = 0;
$total_colonists = 0;
$total_credits = 0;
$total_fighters = 0;
$total_torp = 0;
$total_base = 0;

for ($i=0; $i<$num_planets; $i++)
{
	$total_organics += $planet[$i]['organics'];
	$total_ore += $planet[$i]['ore'];
	$total_goods += $planet[$i]['goods'];
	$total_energy += $planet[$i]['energy'];
	$total_colonists += $planet[$i]['colonists'];
	$total_credits += $planet[$i]['credits'];
	$total_fighters += $planet[$i]['fighters'];
	$total_torp += $planet[$i]['torps'];
	if ($planet[$i]['base'] == "Y")
	{
		$total_base += 1;
	}

	if (empty($planet[$i]['name']))
	{
		$planet[$i]['name'] = "$l_unnamed";
	}

	$owner = $planet[$i]['owner'];
	$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$owner");
	$player = $res->fields['character_name'];

	$planetsector[$i] = $planet[$i]['sector_id'];
	$planetname[$i] = $planet[$i]['name'];
	$planetore[$i] = NUMBER($planet[$i]['ore']);
	$planetorganics[$i] = NUMBER($planet[$i]['organics']);
	$planetgoods[$i] = NUMBER($planet[$i]['goods']);
	$planetenergy[$i] = NUMBER($planet[$i]['energy']);
	$planetcolonists[$i] = NUMBER($planet[$i]['colonists']);
	$planetcredits[$i] = NUMBER($planet[$i]['credits']);
	$planetmaxcredits[$i] = round(($planet[$i]['credits']/$planet[$i]['max_credits'])*100);
	$planetfighters[$i] = NUMBER($planet[$i]['fighters']);
	$planettorps[$i] = NUMBER($planet[$i]['torps']);
	$planetbase[$i] = ($planet[$i]['base'] == 'Y' ? "$l_yes" : "$l_no");
	$planetplayer[$i] = $player;
}

$smarty->assign("l_pr_menulink", $l_pr_menulink);
$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
$smarty->assign("color_header", $color_header);
$smarty->assign("l_sector", $l_sector);
$smarty->assign("l_name", $l_name);
$smarty->assign("l_ore", $l_ore);
$smarty->assign("l_organics", $l_organics);
$smarty->assign("l_goods", $l_goods);
$smarty->assign("l_energy", $l_energy);
$smarty->assign("l_colonists", $l_colonists);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("l_torps", $l_torps);
$smarty->assign("l_base", $l_base);
$smarty->assign("l_player", $l_player);
$smarty->assign("num_planets", $num_planets);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("total_ore", NUMBER($total_ore));
$smarty->assign("total_organics", NUMBER($total_organics));
$smarty->assign("total_goods", NUMBER($total_goods));
$smarty->assign("total_energy", NUMBER($total_energy));
$smarty->assign("total_colonists", NUMBER($total_colonists));
$smarty->assign("total_credits", NUMBER($total_credits));
$smarty->assign("total_fighters", NUMBER($total_fighters));
$smarty->assign("total_torp", NUMBER($total_torp));
$smarty->assign("total_base", NUMBER($total_base));
$smarty->assign("planetsector", $planetsector);
$smarty->assign("planetname", $planetname);
$smarty->assign("planetore", $planetore);
$smarty->assign("planetorganics", $planetorganics);
$smarty->assign("planetgoods", $planetgoods);
$smarty->assign("planetenergy", $planetenergy);
$smarty->assign("planetcolonists", $planetcolonists);
$smarty->assign("planetcredits", $planetcredits);
$smarty->assign("planetmaxcredits", $planetmaxcredits);
$smarty->assign("planetfighters", $planetfighters);
$smarty->assign("planettorps", $planettorps);
$smarty->assign("planetbase", $planetbase);
$smarty->assign("planetplayer", $planetplayer);
$smarty->assign("l_max", $l_max);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."team-planets.tpl");
include ("footer.php");
?>

