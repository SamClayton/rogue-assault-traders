<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: shipyard.php

include ("config/config.php");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_report.inc");

$title = $l_shipyard_title;

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

if($sectorinfo['port_type'] != "upgrades")
{
	close_database();
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
	die();
}

$res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $shipinfo[player_id]");
if ($res2)
{
	$bty = $res2->fields;
	if ($bty['total_bounty'] > 0)
	{
		if ($pay <> 1)
		{
			$l_port_bounty2 = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_bounty2);
			$smarty->assign("l_port_bounty", $l_port_bounty);
			$smarty->assign("l_port_bounty2", $l_port_bounty2);
			$smarty->assign("l_by_placebounty", $l_by_placebounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyardbounty.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			if ($playerinfo['credits'] < $bty['total_bounty'])
			{
				$l_port_btynotenough = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_btynotenough);
				$smarty->assign("error_msg", $l_port_btynotenough);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."shipyarddie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$bty[total_bounty] WHERE player_id = $shipinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("DELETE from $dbtables[bounty] WHERE bounty_on = $shipinfo[player_id] AND placed_by = 0");
				db_op_result($debug_query,__LINE__,__FILE__);

				$smarty->assign("error_msg", $l_port_bountypaid);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."shipyarddie.tpl");
				include ("footer.php");
				die();
			}
		}
	}
}

if($zoneinfo['zone_id'] != 3){
	$alliancefactor = 1;
}

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE buyable = 'Y' order by type_id");
while (!$res->EOF)
{
	$ships[] = $res->fields;
	$res->MoveNext();
}

$lastship = end($ships);

$price_modifier_base = explode("|", $playerinfo['ship_losses']);

for($i = 0; $i < count($price_modifier_base); $i++){
	$items = explode("-", $price_modifier_base[$i]);
	$element = $items[0];
	$price_modifier[$element] = $items[1];
	if($price_modifier[$element] < 1)
		$price_modifier[$element] = 0;
}

$res = $db->Execute("SELECT type_id FROM $dbtables[ship_types] WHERE buyable = 'Y'");
$type_count = $res->RecordCount(); 

$new_losses = "";
for($i = 0; $i < $type_count; $i++){
	$element = $res->fields['type_id'];
	if($price_modifier[$element] < 1)
		$price_modifier[$element] = 0;
	$res->MoveNext();
}

$smarty->assign("l_ship_welcome", $l_ship_welcome);
$smarty->assign("color_line", $color_line);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_ship_class", $l_ship_class);
$smarty->assign("l_ship_properties", $l_ship_properties);

$first = 1;

$countship = 0;

foreach ($ships as $curship)
{
	if (!isset($_GET['stype']))
	{
		$_GET['stype'] = $curship['type_id'];
	}
	$currentshipid[$countship] = $curship['type_id'];
	$currentshipimage[$countship] = "templates/".$templatename."images/$curship[image]";
	$currentshipname[$countship] = $curship['name'];

	if ($curship['type_id'] == $shipinfo['class'])
	{
		$currentship[$countship] = $l_ship_current;
	}else{
		$currentship[$countship] = "";
	}
	$countship++;
	if ($first == 1)
	{
		$first = 0;

		if (isset($_GET['stype']))
		{
			//get info for selected ship class
			foreach ($ships as $testship)
			{
				if ($testship['type_id'] == $_GET['stype'])
				{
					$sship = $testship;
					break;
				}
			}

			$ship_price = ($price_modifier[$_GET['stype']] * ($sship['cost_tobuild'] * ($ship_cost_increase / 100))) + $sship['cost_tobuild'];

			$storageflag=0;
			$res2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE class = ". $_GET['stype']." and ship_id!=".$shipinfo['ship_id']."  and player_id=".$shipinfo['player_id']." and destroyed='N'");
			$totships = $res2->RecordCount(); 
			while (!$res2->EOF)
			{
				$ships2 = $res2->fields;
				$res2->MoveNext();
				$storageflag=1;
			}

			if ($storageflag!=1){	
				$hull_bars = MakeBars($sship['maxhull'], 54, "normal");
				$engines_bars = MakeBars($sship['maxengines'], 54, "normal");
				$power_bars = MakeBars($sship['maxpower'], 54, "normal");
				$computer_bars = MakeBars($sship['maxcomputer'], 54, "normal");
				$sensors_bars = MakeBars($sship['maxsensors'], 54, "normal");
				$armour_bars = MakeBars($sship['maxarmour'], 54, "normal");
				$shields_bars = MakeBars($sship['maxshields'], 54, "normal");
				$beams_bars = MakeBars($sship['maxbeams'], 54, "normal");
				$torp_launchers_bars = MakeBars($sship['maxtorp_launchers'], 54, "normal");
				$cloak_bars = MakeBars($sship['maxcloak'], 54, "normal");
				$ecm_bars = MakeBars($sship['maxecm'], 54, "normal");

				$calc_nhull = round(mypw($upgrade_factor,$sship['minhull']));
				$calc_nengines = round(mypw($upgrade_factor,$sship['minengines']));
				$calc_npower = round(mypw($upgrade_factor,$sship['minpower']));
				$calc_ncomputer = round(mypw($upgrade_factor,$sship['mincomputer']));
				$calc_nsensors = round(mypw($upgrade_factor,$sship['minsensors']));
				$calc_nbeams = round(mypw($upgrade_factor,$sship['minbeams']));
				$calc_ntorp_launchers = round(mypw($upgrade_factor,$sship['mintorp_launchers']));
				$calc_nshields = round(mypw($upgrade_factor,$sship['minshields']));
				$calc_narmour = round(mypw($upgrade_factor,$sship['minarmour']));
				$calc_ncloak = round(mypw($upgrade_factor,$sship['mincloak']));
				$calc_necm = round(mypw($upgrade_factor,$sship['minecm']));
				//$newshipvalue = ($calc_nhull+$calc_nengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmour+$calc_ncloak+$calc_necm) * $upgrade_cost;
				$smarty->assign("sship_img", "templates/".$templatename."images/$sship[image]");
				$smarty->assign("name", $sship['name']);
				$smarty->assign("description", $sship['description']);
				$smarty->assign("currentstorage", "1");

			}else{
				$hull_bars = MakeBars($ships2['hull'], $sship['maxhull'], "normal");
				$engines_bars = MakeBars($ships2['engines'], $sship['maxengines'], "normal");
				$power_bars = MakeBars($ships2['power'], $sship['maxpower'], "normal");
				$computer_bars = MakeBars($ships2['computer'], $sship['maxcomputer'], "normal");
				$sensors_bars = MakeBars($ships2['sensors'], $sship['maxsensors'], "normal");
				$armour_bars = MakeBars($ships2['armour'], $sship['maxarmour'], "normal");
				$shields_bars = MakeBars($ships2['shields'], $sship['maxshields'], "normal");
				$beams_bars = MakeBars($ships2['beams'], $sship['maxbeams'], "normal");
				$torp_launchers_bars = MakeBars($ships2['torp_launchers'], $sship['maxtorp_launchers'], "normal");
				$cloak_bars = MakeBars($ships2['cloak'], $sship['maxcloak'], "normal");
				$ecm_bars = MakeBars($ships2['ecm'], $sship['maxecm'], "normal");
				$calc_nhull = round(mypw($upgrade_factor,$sship['minhull']));
				$calc_nengines = round(mypw($upgrade_factor,$sship['minengines']));
				$calc_npower = round(mypw($upgrade_factor,$sship['minpower']));
				$calc_ncomputer = round(mypw($upgrade_factor,$sship['mincomputer']));
				$calc_nsensors = round(mypw($upgrade_factor,$sship['minsensors']));
				$calc_nbeams = round(mypw($upgrade_factor,$sship['minbeams']));
				$calc_ntorp_launchers = round(mypw($upgrade_factor,$sship['mintorp_launchers']));
				$calc_nshields = round(mypw($upgrade_factor,$sship['minshields']));
				$calc_narmour = round(mypw($upgrade_factor,$sship['minarmour']));
				$calc_ncloak = round(mypw($upgrade_factor,$sship['mincloak']));
				$calc_necm = round(mypw($upgrade_factor,$sship['minecm']));
				//$newshipvalue = ($calc_nhull+$calc_nengines+$calc_npower+$calc_ncomputer+$calc_nsensors+$calc_nbeams+$calc_ntorp_launchers+$calc_nshields+$calc_narmour+$calc_ncloak+$calc_necm) * $upgrade_cost;
				$smarty->assign("sship_img", "templates/".$templatename."images/$sship[image]");
				$smarty->assign("name", $sship['name']);
				$smarty->assign("description", $sship['description']);
				$smarty->assign("currentstorage", "0");

				if (($_GET['stype'] != $shipinfo['class']) or ($ships2['ship_id']!=$shipinfo['ship_id']))
				{   
					$ships2fee = NUMBER($ships2['store_fee']);
				}
			}
		}
	}
}

$smarty->assign("countship", $countship);
$smarty->assign("currentshipid", $currentshipid);
$smarty->assign("currentshipimage", $currentshipimage);
$smarty->assign("currentshipname", $currentshipname);
$smarty->assign("currentship", $currentship);

$smarty->assign("sship_minhull", $sship['minhull']);
$smarty->assign("sship_maxhull", $sship['maxhull']);
$smarty->assign("l_hull", $l_hull);
$smarty->assign("hull_bars", $hull_bars);
$smarty->assign("sship_minengines", $sship['minengines']);
$smarty->assign("sship_maxengines", $sship['maxengines']);
$smarty->assign("l_engines", $l_engines);
$smarty->assign("engines_bars", $engines_bars);
$smarty->assign("sship_minpower", $sship['minpower']);
$smarty->assign("sship_maxpower", $sship['maxpower']);
$smarty->assign("l_power", $l_power);
$smarty->assign("power_bars", $power_bars);
$smarty->assign("sship_mincomputer", $sship['mincomputer']);
$smarty->assign("sship_maxcomputer", $sship['maxcomputer']);
$smarty->assign("l_computer", $l_computer);
$smarty->assign("computer_bars", $computer_bars);
$smarty->assign("sship_minsensors", $sship['minsensors']);
$smarty->assign("sship_maxsensors", $sship['maxsensors']);
$smarty->assign("l_sensors", $l_sensors);
$smarty->assign("sensors_bars", $sensors_bars);
$smarty->assign("sship_minarmour", $sship['minarmour']);
$smarty->assign("sship_maxarmour", $sship['maxarmour']);
$smarty->assign("l_armour", $l_armour);
$smarty->assign("armour_bars", $armour_bars);
$smarty->assign("sship_minshields", $sship['minshields']);
$smarty->assign("sship_maxshields", $sship['maxshields']);
$smarty->assign("l_shields", $l_shields);
$smarty->assign("shields_bars", $shields_bars);
$smarty->assign("sship_minbeams", $sship['minbeams']);
$smarty->assign("sship_maxbeams", $sship['maxbeams']);
$smarty->assign("l_beams", $l_beams);
$smarty->assign("beams_bars", $beams_bars);
$smarty->assign("sship_mintorp_launchers", $sship['mintorp_launchers']);
$smarty->assign("sship_maxtorp_launchers", $sship['maxtorp_launchers']);
$smarty->assign("l_torp_launch", $l_torp_launch);
$smarty->assign("torp_launchers_bars", $torp_launchers_bars);
$smarty->assign("sship_mincloak", $sship['mincloak']);
$smarty->assign("sship_maxcloak", $sship['maxcloak']);
$smarty->assign("l_cloak", $l_cloak);
$smarty->assign("cloak_bars", $cloak_bars);
$smarty->assign("sship_minecm", $sship['minecm']);
$smarty->assign("sship_maxecm", $sship['maxecm']);
$smarty->assign("l_ecm", $l_ecm);
$smarty->assign("ecm_bars", $ecm_bars);
$smarty->assign("newshipvalue", NUMBER($ship_price * $alliancefactor));
$smarty->assign("sship_turnstobuild", NUMBER($sship['turnstobuild']));
$smarty->assign("color_line2", $color_line2);

$smarty->assign("stype",$_GET['stype']);
$smarty->assign("shipinfo_class", $shipinfo['class']);
$smarty->assign("ships2id",$ships2['ship_id']);
$smarty->assign("shipsid",$shipinfo['ship_id']);
$smarty->assign("ships2fee",$ships2fee);

$smarty->assign("l_ship_outstorage",$l_ship_outstorage);
$smarty->assign("l_ship_storagecost",$l_ship_storagecost);
$smarty->assign("l_ship_storagewarn",$l_ship_storagewarn);
$smarty->assign("l_ship_levels",$l_ship_levels);
$smarty->assign("l_ship_min",$l_ship_min);
$smarty->assign("l_ship_max",$l_ship_max);
$smarty->assign("l_ship_price",$l_ship_price);
$smarty->assign("l_ship_turns",$l_ship_turns);
$smarty->assign("l_ship_purchase",$l_ship_purchase);
$smarty->assign("title",$title);
$smarty->assign("gotomain", $l_global_mmenu);

$smarty->display($templatename."shipyard.tpl");

include ("footer.php");

?>
