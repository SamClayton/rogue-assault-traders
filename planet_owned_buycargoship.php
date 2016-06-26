<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_owned_buycargoship.php

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

function phpTrueDelta($futurevalue,$shipvalue)
{
	$tempval = $futurevalue - $shipvalue;
	return $tempval;
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
			$isowner = 1;

			if($planetinfo['cargo_hull'] == 0 or $planetinfo['cargo_hull'] == '')
			{
				$ownhull = 1;
			}
		}

		$smarty->assign("upgrade_cost", $upgrade_cost);
		$smarty->assign("java_credits", $playerinfo['credits']);
		$smarty->assign("java_hull", $planetinfo['cargo_hull']);
		$smarty->assign("java_power", $planetinfo['cargo_power']);
		$smarty->assign("l_no_credits", $l_no_credits);
		$smarty->assign("upgrade_factor", $upgrade_factor);
		$smarty->assign("cargoshiphull", dropdown("cargoshiphull",0, 29));
		$smarty->assign("cargoshippower", dropdown("cargoshippower",1, 29));
		$smarty->assign("allow_ibank", $allow_ibank);
		$smarty->assign("l_by_placebounty", $l_by_placebounty);
		$smarty->assign("l_ifyouneedplan", $l_ifyouneedplan);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_igb_term", $l_igb_term);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("isowner", $isowner);
		$smarty->assign("ownhull", $ownhull);
		$smarty->assign("l_planet_cargoshipbuy", $l_planet_cargoshipbuy);
		$smarty->assign("l_planet_cargonoown", $l_planet_cargonoown);
		$smarty->assign("l_planet_cargoowned", $l_planet_cargoowned);
		$smarty->assign("l_planet_cargoshipbuyinfo", $l_planet_cargoshipbuyinfo);
		$smarty->assign("l_credits", $l_credits);
		$smarty->assign("playercredits", number($playerinfo['credits']));
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_hull", $l_hull);
		$smarty->assign("l_power", $l_power);
		$smarty->assign("l_ship_price", $l_ship_price);
		$smarty->assign("l_planet_cargoshipbuildinfo", $l_planet_cargoshipbuildinfo);
		$smarty->assign("l_buy", $l_buy);
		$smarty->assign("onclick", $onclick);
		$smarty->assign("l_totalcost", $l_totalcost);
		$smarty->assign("templatename", $templatename);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_ownedbuycargo.tpl");
		include ("footer.php");
		die();

}

close_database();
?>