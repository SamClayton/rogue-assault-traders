<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet-report.php

include ("config/config.php");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_planets.inc");
include("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_teams.inc");
include ("languages/$langdir/lang_ports.inc");

$title = $l_pr_title;

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

// determine what type of report is displayed and display it's title

if ((!isset($PRepType)) || ($PRepType == ''))
{
	$PRepType='';
}

if($PRepType==3 || !isset($PRepType)) // display the defenses on the planets
{
	$query = "SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[player_id]";

	if(!empty($sort))
	{
		$query .= " ORDER BY";
	if($sort == "name")
	{
		$query .= " $sort ASC";
	}
	elseif($sort == "computer" || $sort == "sensors" || $sort == "beams" || $sort == "torp_launchers" ||
		$sort == "shields" || $sort == "cloak" || $sort == "base" || $sort == "jammer")
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
	if($res)
	{
	while(!$res->EOF)
	{
		$planet[$i] = $res->fields;
	///
		if($spy_success_factor)
			spy_detect_planet($shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);
			$i++;
			$res->MoveNext();
		}
	}

	$num_planets = $i;

	$total_base = 0;

	for($i=0; $i<$num_planets; $i++)
	{
		if(empty($planet[$i]['name']))
		{
			$planet[$i]['name'] = $l_unnamed;
		}
		$planetsector[$i] = $planet[$i]['sector_id'];
		$planetname[$i] = $planet[$i]['name'];
		$planetcomputer[$i] = NUMBER($planet[$i]['computer']);
		$planetsensors[$i] = NUMBER($planet[$i]['sensors']);
		$planetbeams[$i] = NUMBER($planet[$i]['beams']);
		$planettorps[$i] = NUMBER($planet[$i]['torp_launchers']);
		$planetshields[$i] = NUMBER($planet[$i]['shields']);
		$planetjammer[$i] = NUMBER($planet[$i]['jammer']);
		$planetcloak[$i] = NUMBER($planet[$i]['cloak']);

		$planetbase[$i] = $planet[$i]['base'];
		$planetbaseitems[$i] = ($planet[$i]['ore'] >= $base_ore && $planet[$i]['organics'] >= $base_organics && $planet[$i]['goods'] >= $base_goods && $planet[$i]['credits'] >= $base_credits);
		$planetid[$i] = $planet[$i]["planet_id"];
		$total_base += 1;
	}

	$smarty->assign("title", $title);
	$smarty->assign("l_pr_pdefense", $l_pr_pdefense);
	$smarty->assign("l_pr_menulink", $l_pr_menulink);
	$smarty->assign("l_pr_changeprods", $l_pr_changeprods);
	$smarty->assign("l_pr_baserequired", $l_pr_baserequired);
	$smarty->assign("l_pr_teamlink", $l_pr_teamlink);
	$smarty->assign("playerteam", $playerinfo['team']);
	$smarty->assign("num_planets", $num_planets);
	$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("l_pr_totals", $l_pr_totals);
	$smarty->assign("total_base", $total_base);
	$smarty->assign("l_yes", $l_yes);
	$smarty->assign("l_no", $l_no);
	$smarty->assign("l_pr_build", $l_pr_build);
	$smarty->assign("planetsector", $planetsector);
	$smarty->assign("planetname", $planetname);
	$smarty->assign("planetcomputer", $planetcomputer);
	$smarty->assign("planetsensors", $planetsensors);
	$smarty->assign("planetbeams", $planetbeams);
	$smarty->assign("planettorps", $planettorps);
	$smarty->assign("planetshields", $planetshields);
	$smarty->assign("planetjammer", $planetjammer);
	$smarty->assign("planetcloak", $planetcloak);
	$smarty->assign("planetbase", $planetbase);
	$smarty->assign("planetbaseitems", $planetbaseitems);
	$smarty->assign("planetid", $planetid);
	$smarty->assign("l_pr_sector", $l_pr_sector);
	$smarty->assign("l_name", $l_name);
	$smarty->assign("l_computer", $l_computer);
	$smarty->assign("l_sensors", $l_sensors);
	$smarty->assign("l_beams", $l_beams);
	$smarty->assign("l_torp_launch", $l_torp_launch);
	$smarty->assign("l_shields", $l_shields);
	$smarty->assign("l_jammer", $l_jammer);
	$smarty->assign("l_cloak", $l_cloak);
	$smarty->assign("l_base", $l_base);
	$smarty->assign("l_pr_noplanet", $l_pr_noplanet);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet-report-defenses.tpl");
	include ("footer.php");
	die();
}

if ($PRepType==1 || !isset($PRepType)) // display the commodities on the planets
{
	$query = "SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[player_id]";

	if (!empty($sort))
	{
		$query .= " ORDER BY";
		if ($sort == "name")
		{
			$query .= " $sort ASC";
		}
		elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" ||
			$sort == "colonists" || $sort == "credits" || $sort == "fighters")
		{
			$query .= " $sort DESC, sector_id ASC";
		}
		elseif ($sort == "torp")
		{
			$query .= " torps DESC, sector_id ASC";
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
			if ($spy_success_factor)
				spy_detect_planet($shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);

			$i++;
			$res->MoveNext();
		}
	}

	$num_planets = $i;

	$total_organics = 0;
	$total_ore = 0;
	$total_goods = 0;
	$total_energy = 0;
	$total_colonists = 0;
	$total_credits = 0;
	$total_fighters = 0;
	$total_torp = 0;
	$total_base = 0;
	$total_team = 0;
	$total_teamcash = 0;
	$color = $color_line1;
	for($i=0; $i<$num_planets; $i++)
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
		if ($planet[$i]['team'] > 0)
		{
			$total_team += 1;
		}
		if ($planet[$i]['team_cash'] == "Y")
		{
			$total_teamcash += 1;
		}
		if (empty($planet[$i]['name']))
		{
			$planet[$i]['name'] = $l_unnamed;
		}
		$planetsector[$i] = $planet[$i]['sector_id'];
		$planetname[$i] = $planet[$i]['name'];
		$planetore[$i] = NUMBER($planet[$i]['ore']);
		$planetorganics[$i] = NUMBER($planet[$i]['organics']);
		$planetgoods[$i] = NUMBER($planet[$i]['goods']);
		$planetenergy[$i] = NUMBER($planet[$i]['energy']);
		$planetcolonists[$i] = NUMBER($planet[$i]['colonists']);
		$planetcredits[$i] = NUMBER($planet[$i]['credits']);
		$planetmaxcredits[$i] = round(($planet[$i]['credits']/$planet[$i]['max_credits'])*100);
		$planetid[$i] = $planet[$i]["planet_id"];
		$planetfighters[$i] = NUMBER($planet[$i]['fighters']);
		$planettorps[$i] = NUMBER($planet[$i]['torps']);
		$planetbase[$i] = $planet[$i]['base'];
		$planetbaseitems[$i] = ($planet[$i]['ore'] >= $base_ore && $planet[$i]['organics'] >= $base_organics && $planet[$i]['goods'] >= $base_goods && $planet[$i]['credits'] >= $base_credits);
		$planetteam[$i] = $planet[$i]['team'];
		$planettcash[$i] = $planet[$i]['team_cash'];
	}

	$smarty->assign("title", $title);
	$smarty->assign("l_pr_status", $l_pr_status);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("l_pr_menulink", $l_pr_menulink);
	$smarty->assign("l_pr_changeprods", $l_pr_changeprods);
	$smarty->assign("l_pr_baserequired", $l_pr_baserequired);
	$smarty->assign("playerteam", $playerinfo['team']);
	$smarty->assign("l_pr_teamlink", $l_pr_teamlink);
	$smarty->assign("num_planets", $num_planets);
	$smarty->assign("l_pr_noplanet", $l_pr_noplanet);
	$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
	$smarty->assign("l_pr_warning", $l_pr_warning);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_pr_sector", $l_pr_sector);
	$smarty->assign("l_name", $l_name);
	$smarty->assign("l_ore", $l_ore);
	$smarty->assign("l_organics", $l_organics);
	$smarty->assign("l_goods", $l_goods);
	$smarty->assign("l_energy", $l_energy);
	$smarty->assign("l_colonists", $l_colonists);
	$smarty->assign("l_credits", $l_credits);
	$smarty->assign("l_pr_takecreds", $l_pr_takecreds);
	$smarty->assign("l_fighters", $l_fighters);
	$smarty->assign("l_torps", $l_torps);
	$smarty->assign("l_base", $l_base);
	$smarty->assign("l_team", $l_team);
	$smarty->assign("l_teamcash", $l_teamcash);
	$smarty->assign("l_pr_collectcreds", $l_pr_collectcreds);
	$smarty->assign("l_pr_selectall", $l_pr_selectall);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("total_teamcash", NUMBER($total_teamcash));
	$smarty->assign("total_team", NUMBER($total_team));
	$smarty->assign("total_base", NUMBER($total_base));
	$smarty->assign("total_torp", NUMBER($total_torp));
	$smarty->assign("total_fighters", NUMBER($total_fighters));
	$smarty->assign("total_credits", NUMBER($total_credits));
	$smarty->assign("total_colonists", NUMBER($total_colonists));
	$smarty->assign("total_energy", NUMBER($total_energy));
	$smarty->assign("total_goods", NUMBER($total_goods));
	$smarty->assign("total_organics", NUMBER($total_organics));
	$smarty->assign("total_ore", NUMBER($total_ore));
	$smarty->assign("l_pr_totals", $l_pr_totals);
	$smarty->assign("l_yes", $l_yes);
	$smarty->assign("l_pr_build", $l_pr_build);
	$smarty->assign("l_no", $l_no);
	$smarty->assign("planetbase", $planetbase);
	$smarty->assign("planetsector", $planetsector);
	$smarty->assign("planetname", $planetname);
	$smarty->assign("planetore", $planetore);
	$smarty->assign("planetorganics", $planetorganics);
	$smarty->assign("planetgoods", $planetgoods);
	$smarty->assign("planetenergy", $planetenergy);
	$smarty->assign("planetcolonists", $planetcolonists);
	$smarty->assign("planetcredits", $planetcredits);
	$smarty->assign("planetmaxcredits", $planetmaxcredits);
	$smarty->assign("planetid", $planetid);
	$smarty->assign("planetfighters", $planetfighters);
	$smarty->assign("planettorps", $planettorps);
	$smarty->assign("planetbaseitems", $planetbaseitems);
	$smarty->assign("planetteam", $planetteam);
	$smarty->assign("planettcash", $planettcash);
	$smarty->assign("l_max", $l_max);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet-report-commodities.tpl");
	include ("footer.php");
	die();
}

if ($PRepType==2)	// display the production values of your planets and allow changing
{
	$query = "SELECT * FROM $dbtables[planets] WHERE owner=$playerinfo[player_id] AND base='Y'";

	if (!empty($sort))
	{
		$query .= " ORDER BY";
		if ($sort == "name")
		{
			$query .= " $sort ASC";
		}
		elseif ($sort == "organics" || $sort == "ore" || $sort == "goods" || $sort == "energy" || $sort == "fighters")
		{
			$query .= " prod_$sort DESC, sector_id ASC";
		}
		elseif ($sort == "colonists" || $sort == "credits")
		{
			$query .= " $sort DESC, sector_id ASC";
		}
		elseif ($sort == "torp")
		{
			$query .= " prod_torp DESC, sector_id ASC";
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
			if ($spy_success_factor)
				spy_detect_planet($shipinfo['ship_id'], $planet[$i]['planet_id'], $planet_detect_success2);

			$i++;
			$res->MoveNext();
		}
	}

	$num_planets = $i;

	$total_colonists = 0;
	$total_credits = 0;
	$total_team = 0;

	$temp_var = 0;

	for($i=0; $i<$num_planets; $i++)
	{
		$total_colonists += $planet[$i]['colonists'];
		$total_credits += $planet[$i]['credits'];
		if (empty($planet[$i]['name']))
		{
			$planet[$i]['name'] = $l_unnamed;
		}
		$planetsector[$i] = $planet[$i]['sector_id'];
		$planetname[$i] = $planet[$i]['name'];
		$planetid[$i] = $planet[$i]["planet_id"];
		$planetore[$i] = $planet[$i]["prod_ore"];
		$planetorganics[$i] = $planet[$i]["prod_organics"];
		$planetgoods[$i] = $planet[$i]["prod_goods"];
		$planetenergy[$i] = $planet[$i]["prod_energy"];
		$planetcolonists[$i] = NUMBER($planet[$i]['colonists']);
		$planetcredits[$i] = NUMBER($planet[$i]['credits']);
		$planetfighters[$i] = $planet[$i]["prod_fighters"];
		$planettorps[$i] = $planet[$i]["prod_torp"];
		$planetresearch[$i] = $planet[$i]["prod_research"];
		$planetbuild[$i] = $planet[$i]["prod_build"];
		if ($playerinfo['team'] > 0){
			$planetteam[$i] = $planet[$i]['team'];
		}
		$planettcash[$i] = $planet[$i]['team_cash'];
	}

	$smarty->assign("title", $title);
	$smarty->assign("l_pr_build", $l_pr_build);
	$smarty->assign("l_pr_research", $l_pr_research);
	$smarty->assign("l_pr_production", $l_pr_production);
	$smarty->assign("l_pr_menulink", $l_pr_menulink);
	$smarty->assign("l_pr_planetstatus", $l_pr_planetstatus);
	$smarty->assign("playerteam", $playerinfo['team']);
	$smarty->assign("l_pr_teamlink", $l_pr_teamlink);
	$smarty->assign("num_planets", $num_planets);
	$smarty->assign("l_pr_noplanet", $l_pr_noplanet);
	$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_pr_sector", $l_pr_sector);
	$smarty->assign("l_name", $l_name);
	$smarty->assign("l_ore", $l_ore);
	$smarty->assign("l_organics", $l_organics);
	$smarty->assign("l_goods", $l_goods);
	$smarty->assign("l_energy", $l_energy);
	$smarty->assign("l_colonists", $l_colonists);
	$smarty->assign("l_credits", $l_credits);
	$smarty->assign("l_fighters", $l_fighters);
	$smarty->assign("l_torps", $l_torps);
	$smarty->assign("l_team", $l_team);
	$smarty->assign("l_teamcash", $l_teamcash);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("l_pr_totals", $l_pr_totals);
	$smarty->assign("total_colonists", NUMBER($total_colonists));
	$smarty->assign("total_credits", NUMBER($total_credits));
	$smarty->assign("player_id", $playerinfo['player_id']);
	$smarty->assign("l_submit", $l_submit);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("planetsector", $planetsector);
	$smarty->assign("planetname", $planetname);
	$smarty->assign("planetid", $planetid);
	$smarty->assign("planetore", $planetore);
	$smarty->assign("planetorganics", $planetorganics);
	$smarty->assign("planetgoods", $planetgoods);
	$smarty->assign("planetenergy", $planetenergy);
	$smarty->assign("planetcolonists", $planetcolonists);
	$smarty->assign("planetcredits", $planetcredits);
	$smarty->assign("planetfighters", $planetfighters);
	$smarty->assign("planettorps", $planettorps);
	$smarty->assign("planetresearch", $planetresearch);
	$smarty->assign("planetbuild", $planetbuild);
	$smarty->assign("planetteam", $planetteam);
	$smarty->assign("planettcash", $planettcash);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet-report-production.tpl");
	include ("footer.php");
	die();
}

if ($PRepType==0)					// For typing in manually to get a report menu
{
	$smarty->assign("title", $title);
	$smarty->assign("l_pr_menu", $l_pr_menu);
	$smarty->assign("l_pr_planetstatus", $l_pr_planetstatus);
	$smarty->assign("l_pr_comm_disp", $l_pr_comm_disp);
	$smarty->assign("l_pr_pdefense", $l_pr_pdefense);
	$smarty->assign("l_pr_display", $l_pr_display);
	$smarty->assign("l_pr_changeprods", $l_pr_changeprods);
	$smarty->assign("l_pr_baserequired", $l_pr_baserequired);
	$smarty->assign("l_pr_prod_disp", $l_pr_prod_disp);
	$smarty->assign("l_pr_teamlink", $l_pr_teamlink);
	$smarty->assign("l_pr_team_disp", $l_pr_team_disp);
	$smarty->assign("l_pr_showtd", $l_pr_showtd);
	$smarty->assign("l_pr_showd", $l_pr_showd);
	$smarty->assign("playerteam", $playerinfo['team']);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planet-report-menu.tpl");
	include ("footer.php");
	die();
}

close_database();
?>
