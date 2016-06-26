<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet-report-ce.php

include ("config/config.php");
include ("languages/$langdir/lang_rsmove.inc");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_planets.inc");

if ((!isset($team_id)) || ($team_id == ''))
{
	$team_id = '';
}

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

while (list($commod_type, $valarray) = each($_POST))
{
	$totalplanets = 0;
	while (list($planet_id, $prodpercent) = each($valarray))
	{
		if($prodpercent < 0)
			$prodpercent = 0;

		if ($commod_type == "planetsector")
		{
			$planetsector[$totalplanets] = $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "planetname")
		{
			$planetname[$totalplanets] = $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_ore")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] = "prod_ore=$prodpercent, ";
			$percentage[$totalplanets] = $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_organics")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_organics=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_goods")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_goods=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_energy")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_energy=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_fighters")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_fighters=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_torp")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_torp=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_research")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_research=$prodpercent, ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "prod_build")
		{
			$prodpercent = stripnum($prodpercent);
			$planetquery[$totalplanets] .= "prod_build=$prodpercent ";
			$percentage[$totalplanets] += $prodpercent;
			$totalplanets++;
		}

		if ($commod_type == "team")
		{
			if($prodpercent == 1)
				$prodpercent = $playerinfo['team'];
			else $prodpercent = 0;
			$planetquery[$totalplanets] .= ", team=$prodpercent, ";
			$totalplanets++;
		}

		if ($commod_type == "team_cash")
		{
			if($prodpercent == 1)
				$prodpercent = "Y";
			else $prodpercent = "N";
			$planetquery[$totalplanets] .= "team_cash='$prodpercent' ";
			$totalplanets++;
		}

		if ($commod_type == "prod_done")
		{
			$planetquery[$totalplanets] .= "where planet_id=$planet_id and owner=$playerinfo[player_id]";
			$totalplanets++;
		}
	}
}

$exceeded = 0;
for($i = 0; $i<$totalplanets; $i++){
	if ($percentage[$i] > 100)
	{
		$l_pr_prexeeds2 = str_replace("[name]", $planetname[$i], $l_pr_prexeeds);
		$l_pr_prexeeds2 = str_replace("[sector_id]", $planetsector[$i], $l_pr_prexeeds2);

		$planetexceeded[$exceeded] =  $l_pr_prexeeds2;
		$exceeded++;
	}
	else
	{
		$debug_query = $db->Execute("UPDATE $dbtables[planets] SET " . $planetquery[$i]);
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

$smarty->assign("l_pr_menulink", $l_pr_menulink);
$smarty->assign("l_pr_changeprods", $l_pr_changeprods);
$smarty->assign("l_pr_ppupdated", $l_pr_ppupdated);
$smarty->assign("l_pr_prexeedcheck", $l_pr_prexeedcheck);
$smarty->assign("exceeded", $exceeded);
$smarty->assign("planetexceeded", $planetexceeded);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."planet-report-ceprod.tpl");
include ("footer.php");

?>
