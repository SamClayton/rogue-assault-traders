<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: team-defenses.php

include ("config/config.php");
include ("languages/$langdir/lang_team_planets.inc");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_ports.inc");
include("languages/$langdir/lang_report.inc");

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
	$smarty->display($templatename."team-defensesdie.tpl");
	include ("footer.php");
	die();
}

$query = "SELECT * FROM $dbtables[planets] WHERE team=$playerinfo[team]";

if(!empty($sort))
{
	$query .= " ORDER BY";
	if($sort == "name")
	{
	  $query .= " $sort ASC";
	}
	elseif($sort == "computer" || $sort == "sensors" || $sort == "beams" || $sort == "torp_launchers" ||
	  $sort == "shields" || $sort == "cloak" || $sort == "owner" || $sort == "base" || $sort == "jammer")
	{
	  $query .= " $sort DESC, sector_id ASC";
	}
	else
	{
	  $query .= " sector_id ASC";
	}

}
else
{
	 $query .= " ORDER BY sector_id ASC";
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
if($num_planets < 1)
{
	$smarty->assign("l_pr_noplanet", $l_pr_noplanet);
	$smarty->assign("l_pr_menulink", $l_pr_menulink);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team-defensesnone.tpl");
	include ("footer.php");
	die();
}

$total_base = 0;

$color = $color_line1;
for($i=0; $i<$num_planets; $i++)
{
	if(empty($planet[$i]['name']))
	{
		$planet[$i]['name'] = $l_unnamed;
	}

	$owner = $planet[$i]['owner'];
	$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$owner");
	$player = $res->fields['character_name'];

	$teamsector[$i] = $planet[$i]['sector_id'];
	$planetname[$i] = $planet[$i]['name'];
	$planetcomputer[$i] = NUMBER($planet[$i]['computer']);
	$planetsensors[$i] = NUMBER($planet[$i]['sensors']);
	$planetbeams[$i] = NUMBER($planet[$i]['beams']);
	$planettorps[$i] = NUMBER($planet[$i]['torp_launchers']);
	$planetshields[$i] = NUMBER($planet[$i]['shields']);
	$planetjammer[$i] = NUMBER($planet[$i]['jammer']);
	$planetcloak[$i] = NUMBER($planet[$i]['cloak']);
	$planetbase[$i] = ($planet[$i]['base'] == 'Y' ? "$l_yes" : "$l_no");
	$playername[$i] = $player;

	if($planet[$i]['base'] == 'Y')
		$total_base++;
}


$smarty->assign("l_pr_menulink", $l_pr_menulink);
$smarty->assign("teamsector", $teamsector);
$smarty->assign("planetname", $planetname);
$smarty->assign("planetcomputer", $planetcomputer);
$smarty->assign("planetsensors", $planetsensors);
$smarty->assign("planetbeams", $planetbeams);
$smarty->assign("planettorps", $planettorps);
$smarty->assign("planetshields", $planetshields);
$smarty->assign("planetjammer", $planetjammer);
$smarty->assign("planetcloak", $planetcloak);
$smarty->assign("planetbase", $planetbase);
$smarty->assign("playername", $playername);

$smarty->assign("num_planets", $num_planets);
$smarty->assign("l_pr_totals", $l_pr_totals);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("total_base", $total_base);
$smarty->assign("l_teamplanet_owner", $l_teamplanet_owner);
$smarty->assign("l_base", $l_base);
$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
$smarty->assign("color_header", $color_header);
$smarty->assign("l_sector", $l_sector);
$smarty->assign("l_name", $l_name);
$smarty->assign("l_computer", $l_computer);
$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("l_beams", $l_beams);
$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("l_shields", $l_shields);
$smarty->assign("l_jammer", $l_jammer);
$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("l_teamplanet_personal", $l_teamplanet_personal);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."team-defenses.tpl");
include ("footer.php");
?>