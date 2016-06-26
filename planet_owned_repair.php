<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_repair.php

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

// Create dropdowns when called
function dropdown($element_name,$current_value, $max_value)
{
	global $onchange;
	$i = $current_value;
	$dropdownvar = "<select size='1' name='$element_name'";
	$dropdownvar = "$dropdownvar ONCHANGE=\"countTotal()\">\n";
	while ($i <= $max_value)
	{
		if ($current_value == $i)
		{
			$dropdownvar = "$dropdownvar		<option value='$i' selected>$i</option>\n";
		}
		else
		{
			$dropdownvar = "$dropdownvar		<option value='$i'>$i</option>\n";
		}
		$i++;
	}
	$dropdownvar = "$dropdownvar	   </select>\n";
	return $dropdownvar;
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

		if ($planetinfo['base'] == "N")
		{
			$smarty->assign("result", $l_planet_nobase);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
			$smarty->assign("l_igb_term", $l_igb_term);
			$smarty->assign("allow_ibank", $allow_ibank);
			$smarty->assign("l_by_placebounty", $l_by_placebounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planet_ownedgeneric2.tpl");
			include ("footer.php");
			die();
		}
		else
		{

			$l_creds_to_spend=str_replace("[credits]",NUMBER($playerinfo['credits']),$l_creds_to_spend);

			if ($allow_ibank)
			{
				$igblink = "<A HREF=igb.php>$l_igb_term</a>";
				$l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);
			}

			$planet_ratio=round(($planetinfo['credits']/$planetinfo['max_credits'])*100);
			$smarty->assign("java_cloak_normal", $planetinfo['cloak_normal']);
			$smarty->assign("java_jammer_normal", $planetinfo['jammer_normal']);
			$smarty->assign("java_shields_normal", $planetinfo['shields_normal']);
			$smarty->assign("java_torps_normal", $planetinfo['torp_launchers_normal']);
			$smarty->assign("java_beams_normal", $planetinfo['beams_normal']);
			$smarty->assign("java_sensors_normal", $planetinfo['sensors_normal']);
			$smarty->assign("java_computer_normal", $planetinfo['computer_normal']);
			$smarty->assign("planet_credits", $planetinfo['credits']);
			$smarty->assign("planet_creditsout", NUMBER($planetinfo['credits']));
			$smarty->assign("planet_ratio", $planet_ratio);
			$smarty->assign("planet_max_credits", $planetinfo['max_credits']);
			$smarty->assign("planet_max_creditsout", NUMBER($planetinfo['max_credits']));
			$smarty->assign("planet_credit_multi", $planet_credit_multi);
			$smarty->assign("base_credits", $base_credits);			
			$smarty->assign("l_overall", $l_overall);
			$smarty->assign("l_planetary_credits", $l_planetary_credits);			
			$smarty->assign("planet_overall", dropdown("overall_upgrade",0, 54));
			$smarty->assign("l_max_credits", $l_max_credits);

			$smarty->assign("java_cloak", $planetinfo['cloak']);
			$smarty->assign("java_jammer", $planetinfo['jammer']);
			$smarty->assign("java_shields", $planetinfo['shields']);
			$smarty->assign("java_torps", $planetinfo['torp_launchers']);
			$smarty->assign("java_beams", $planetinfo['beams']);
			$smarty->assign("java_sensors", $planetinfo['sensors']);
			$smarty->assign("java_computer", $planetinfo['computer']);
			$smarty->assign("java_credits", $playerinfo['credits']);
			$smarty->assign("l_no_credits", $l_no_credits);
			$smarty->assign("upgrade_cost", $upgrade_cost);
			$smarty->assign("upgrade_factor", $upgrade_factor);
			$smarty->assign("l_creds_to_spend", $l_creds_to_spend);
			$smarty->assign("l_ifyouneedmore", $l_ifyouneedmore);
			$smarty->assign("color_header", $color_header);
			$smarty->assign("color_line1", $color_line1);
			$smarty->assign("color_line2", $color_line2);
			$smarty->assign("l_planetary_defense_levels", $l_planetary_defense_levels);
			$smarty->assign("l_cost", $l_cost);
			$smarty->assign("l_current_level", $l_current_level);
			$smarty->assign("l_upgrade", $l_upgrade);
			$smarty->assign("l_computer", $l_computer);
			$smarty->assign("l_sensors", $l_sensors);
			$smarty->assign("l_beams", $l_beams);
			$smarty->assign("l_torp_launch", $l_torp_launch);
			$smarty->assign("l_shields", $l_shields);
			$smarty->assign("l_jammer", $l_jammer);
			$smarty->assign("l_cloak", $l_cloak);
			$smarty->assign("onclick", $onclick);
			$smarty->assign("l_buy", $l_buy);
			$smarty->assign("l_totalcost", $l_totalcost);
			$smarty->assign("repairmodifier", ($repair_modifier / 100));
			$smarty->assign("planetcloak", $planetinfo['cloak']);
			$smarty->assign("planet_cloak", dropdown("cloak_upgrade",$planetinfo['cloak'], $planetinfo['cloak_normal']));
			$smarty->assign("planetjammer", $planetinfo['jammer']);
			$smarty->assign("planet_jammer", dropdown("jammer_upgrade",$planetinfo['jammer'], $planetinfo['jammer_normal']));
			$smarty->assign("planetshields", $planetinfo['shields']);
			$smarty->assign("planet_shields", dropdown("shields_upgrade",$planetinfo['shields'], $planetinfo['shields_normal']));
			$smarty->assign("planettorps", $planetinfo['torp_launchers']);
			$smarty->assign("planet_torps", dropdown("torp_launchers_upgrade",$planetinfo['torp_launchers'], $planetinfo['torp_launchers_normal']));
			$smarty->assign("planetbeams", $planetinfo['beams']);
			$smarty->assign("planet_beams", dropdown("beams_upgrade",$planetinfo['beams'], $planetinfo['beams_normal']));
			$smarty->assign("planetsensors", $planetinfo['sensors']);
			$smarty->assign("planet_sensors", dropdown("sensors_upgrade",$planetinfo['sensors'], $planetinfo['sensors_normal']));
			$smarty->assign("planetcomputer", $planetinfo['computer']);
			$smarty->assign("planet_computer", dropdown("computer_upgrade",$planetinfo['computer'], $planetinfo['computer_normal']));

			$smarty->assign("planetcloak_normal", $planetinfo['cloak_normal']);
			$smarty->assign("planetjammer_normal", $planetinfo['jammer_normal']);
			$smarty->assign("planetshields_normal", $planetinfo['shields_normal']);
			$smarty->assign("planettorps_normal", $planetinfo['torp_launchers_normal']);
			$smarty->assign("planetbeams_normal", $planetinfo['beams_normal']);
			$smarty->assign("planetsensors_normal", $planetinfo['sensors_normal']);
			$smarty->assign("planetcomputer_normal", $planetinfo['computer_normal']);
			$smarty->assign("l_planet_upgrade", $l_planet_upgrade);

			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
			$smarty->assign("l_igb_term", $l_igb_term);
			$smarty->assign("allow_ibank", $allow_ibank);
			$smarty->assign("l_by_placebounty", $l_by_placebounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planet_ownedrepair.tpl");
			include ("footer.php");
			die();
		}

}

close_database();
?>