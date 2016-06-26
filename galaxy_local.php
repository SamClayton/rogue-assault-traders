<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: galaxy_local.php

include ("config/config.php");
include ("languages/$langdir/lang_galaxy_local.inc");

$title = $l_glxy_title;

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

if (empty($startsector) || $startsector == '')
{
	$startsector = 1;
}

if (empty($turns) || $turns == '')
{
	$turns = 0;
}

//$endsector = $startsector + 1000;
$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($max_query,__LINE__,__FILE__);

$sector_max = $max_query->fields['sector_id'];

//if($startsector == "-1"){
	$startsector = 1;
	$endsector = $sector_max;
//	$allselected = "selected";
//}

$shipspeed = mypw($level_factor, $shipinfo['engines']);
$distance = ($turns * $shipspeed);

if($enable_spiral_galaxy != 1){
	$query2 = "SELECT spiral_arm, sector_id, port_type, zone_id, x, y, z,SQRT((($sectorinfo[x]-x) * ($sectorinfo[x]-x))+(($sectorinfo[y]-y) * ($sectorinfo[y]-y))+(($sectorinfo[z]-z) * ($sectorinfo[z]-z))) as sector_distance FROM $dbtables[universe] where SQRT((($sectorinfo[x]-x) * ($sectorinfo[x]-x))+(($sectorinfo[y]-y) * ($sectorinfo[y]-y))+(($sectorinfo[z]-z) * ($sectorinfo[z]-z))) < $distance and sector_id!=$shipinfo[sector_id] and sg_sector != 1  ORDER BY sector_distance ASC,sector_id DESC";
}
else
{
	$query2 = "SELECT spiral_arm, sector_id, port_type, zone_id, x, y, z,SQRT(POW(($sectorinfo[x]-x),2)+POW(($sectorinfo[y]-y),2)+POW(($sectorinfo[z]-z),2)) as sector_distance FROM $dbtables[universe] where SQRT(POW(($sectorinfo[x]-x),2)+POW(($sectorinfo[y]-y),2)+POW(($sectorinfo[z]-z),2)) < $distance and sector_id!=$shipinfo[sector_id] and sg_sector != 1  ORDER BY sector_distance ASC,sector_id DESC";
}

$result = $db->Execute ($query2);
$result2 = $db->Execute("SELECT distinct source, zone_id, time FROM $dbtables[movement_log] WHERE player_id = $playerinfo[player_id] order by source ASC");
$result3 = $db->Execute("SELECT distinct sector_id, zone_id, time FROM $dbtables[scan_log] WHERE player_id = $playerinfo[player_id] order by sector_id ASC");
$result4 = $db->Execute ("SELECT distinct zone_id, zone_color, zone_name, team_zone FROM $dbtables[zones] ORDER BY zone_name ASC");

$text['ore']	  =$l_ore;
$text['goods']	=$l_goods;
$text['organics'] =$l_organics;
$text['energy']   =$l_energy;
$text['upgrades'] =$l_upgrade_ports;
$text['devices']  =$l_device_ports;
$text['spacedock']  =$l_spacedock;
$text['casino']  =$l_casino;
$text['none']	 =$l_none;

$pages = floor($sector_max / 1000);

while (!$result2->EOF) 
{
	$row2 = $result2->fields;
	$temp = $row2['source'];
	$movementzone[$temp] = $row2['zone_id'];
	$movementtime[$temp] = strtotime($row2['time']);
	$result2->Movenext();
}

while (!$result3->EOF) 
{
	$row3 = $result3->fields;
	$temp = $row3['sector_id'];
	$scanzone[$temp] = $row3['zone_id'];
	$scantime[$temp] = strtotime($row3['time']);
	$result3->Movenext();
}

$totalzones = 0;

while (!$result4->EOF) 
{
	$row4 = $result4->fields;
	$temp = $row4['zone_id'];
	$zoneinfo[$temp] = $row4['zone_color'];
	$zonename[$temp] = $row4['zone_name'];
	$zonenumber[$totalzones] = $temp;
	$zoneteam[$temp] = $row4['team_zone'];
	$totalzones++;
	$result4->Movenext();
}

$mapsectorcount = 1;

$ship_x = $sectorinfo['x'];
$ship_y = $sectorinfo['y'];
$ship_z = $sectorinfo['z'];

while (!$result->EOF)
{
	$row   = $result->fields;

	$sectorid[$mapsectorcount] = $row['sector_id'];
	$position[$mapsectorcount]= $row['x']."|".$row['y']."|".$row['z'];
	$galacticarm[$mapsectorcount]= $row['spiral_arm'];

	$port= "unknown";
	$alt = "$row[sector_id] - $l_unknown - " . round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
	$altsector[$mapsectorcount] = $row['sector_id'];
	$altport[$mapsectorcount] = $l_unknown;
	$altturns[$mapsectorcount] = round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
	$zonecolor = "#000000";

	$tempsector = $row['sector_id'];

	if($movementtime[$tempsector] >= $scantime[$tempsector]){
		if ($movementzone[$tempsector] > 0 )
		{
			$temp = $movementzone[$tempsector];
			$zonecolor = $zoneinfo[$temp];
			$port = $row['port_type'];
			$alt  = "$row[sector_id] - $text[$port] - ".$zonename[$temp] . " - " . round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
			$altsector[$mapsectorcount]  = $row['sector_id'];
			$altport[$mapsectorcount]  = $text[$port];
			$altturns[$mapsectorcount]  = round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
		}
	}else{
		if ($scanzone[$tempsector] > 0)
		{
			$temp = $scanzone[$tempsector];
			$zonecolor = $zoneinfo[$temp];
			$port = $row['port_type'];
			$alt  = "$row[sector_id] - $text[$port] - ".$zonename[$temp] . " - " . round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
			$altsector[$mapsectorcount]  = $row['sector_id'];
			$altport[$mapsectorcount]  = $text[$port];
			$altturns[$mapsectorcount]  = round(trip_time($shipinfo['sector_id'], $row['sector_id'])) . " realspace turns";
		}
	}

	$sectorzonecolor[$mapsectorcount] = $zonecolor;
	$sectorimage[$mapsectorcount] = $tile[$port];
	$sectortitle[$mapsectorcount] = $alt;

	$result_sn = $db->Execute("SELECT * FROM $dbtables[sector_notes] WHERE note_sector_id=$row[sector_id] and note_player_id=$playerinfo[player_id] ORDER BY note_date DESC");
	if(!$result_sn->EOF && $result_sn)
	{
		$notelistnote[$mapsectorcount] = addslashes(str_replace("\r", "", str_replace("\n", "<br>", rawurldecode("Personal: " . $result_sn->fields['note_data'] . "<br><br>"))));
	}
	else
	{
		$notelistnote[$mapsectorcount] = "";
	}

	if($playerinfo['team'] > 0){
		$result_sn = $db->Execute("SELECT * FROM $dbtables[sector_notes] WHERE note_sector_id=$row[sector_id] and note_team_id=$playerinfo[team] ORDER BY note_date DESC");
		if(!$result_sn->EOF && $result_sn)
		{
			$teamnotelistnote[$mapsectorcount] = addslashes(str_replace("\r", "", str_replace("\n", "<br>", rawurldecode("Team: " . $result_sn->fields['note_data']))));
		}
		else
		{
			$teamnotelistnote[$mapsectorcount] = "";
		}
	}
	else
	{
		$teamnotelistnote[$mapsectorcount] = "";
	}

	$mapsectorcount++;

	$result->Movenext();
	if($mapsectorcount == 1000)
		break;
}	


$temp = $zonenumber[0];
$count = 1;
for($i = 0; $i <= $totalzones; $i++){
	$temp = $zonenumber[$i];
	if($zoneinfo[$temp] != "#000000" && $zoneteam[$temp] == "Y"){
		$namezone[$count] = $zonename[$temp];
		$namezonecolor[$count] = $zoneinfo[$temp];
		$count++;
	}
}

$temp = $zonenumber[0];
$player_count = 1;
for($i = 0; $i <= $totalzones; $i++){
	$temp = $zonenumber[$i];
	if($zoneinfo[$temp] != "#000000" && $zoneteam[$temp] == "N"){
		$playerzone[$player_count] = $zonename[$temp];
		$playerzonecolor[$player_count] = $zoneinfo[$temp];
		$player_count++;
	}
}

$smarty->assign("l_glxy_personal", $l_glxy_personal);
$smarty->assign("player_count", $player_count);
$smarty->assign("playerzone", $playerzone);
$smarty->assign("playerzonecolor", $playerzonecolor);
$smarty->assign("l_galacticarm", $l_galacticarm);
$smarty->assign("galacticarm", $galacticarm);
$smarty->assign("nav_scan_coords", $position);

$smarty->assign("altsector", $altsector);
$smarty->assign("altport", $altport);
$smarty->assign("altturns", $altturns);
$smarty->assign("notelistnote", $notelistnote);
$smarty->assign("teamnotelistnote", $teamnotelistnote);
$smarty->assign("map_width", $playerinfo['map_width']);
$smarty->assign("l_glxy_turns", $l_glxy_turns);
$smarty->assign("turns", $turns);
$smarty->assign("startsector", $startsector);
$smarty->assign("divider", 1000);
$smarty->assign("endsector", $endsector);
$smarty->assign("allselected", $allselected);
$smarty->assign("namezone", $namezone);
$smarty->assign("namezonecolor", $namezonecolor);
$smarty->assign("zoneonename", $l_zname[1]);
$smarty->assign("count", $count);
$smarty->assign("totalzones", $totalzones);
$smarty->assign("sectorid", $sectorid);
$smarty->assign("sectorzonecolor", $sectorzonecolor);
$smarty->assign("sectorimage", $sectorimage);
$smarty->assign("sectortitle", $sectortitle);
$smarty->assign("mapsectorcount", $mapsectorcount);
$smarty->assign("t_spacedock", $tile['spacedock']);
$smarty->assign("t_casino", $tile['casino']);
$smarty->assign("t_devices", $tile['devices']);
$smarty->assign("t_upgrades", $tile['upgrades']);
$smarty->assign("t_ore", $tile['ore']);
$smarty->assign("t_organics", $tile['organics']);
$smarty->assign("t_energy", $tile['energy']);
$smarty->assign("t_goods", $tile['goods']);
$smarty->assign("t_none", $tile['none']);
$smarty->assign("t_unknown", $tile['unknown']);
$smarty->assign("l_spacedock", $l_spacedock);
$smarty->assign("l_casino", $l_casino);
$smarty->assign("l_glxy_select", $l_glxy_select);
$smarty->assign("l_all", $l_all);
$smarty->assign("l_device_ports", $l_device_ports);
$smarty->assign("l_upgrade_ports", $l_upgrade_ports);
$smarty->assign("l_ore", $l_ore);
$smarty->assign("l_organics", $l_organics);
$smarty->assign("l_energy", $l_energy);
$smarty->assign("l_goods", $l_goods);
$smarty->assign("l_none", $l_none);
$smarty->assign("l_unknown", $l_unknown);
$smarty->assign("l_glxy_nonteamed", $l_glxy_nonteamed);
$smarty->assign("l_submit", $l_submit);
$smarty->assign("l_sector", $l_sector);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."galaxy_local.tpl");
include ("footer.php");

?>

