<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: ngs_ls_client.php

/* 
if (preg_match("/ngs_ls_client.php/i", $_SERVER['PHP_SELF']))
{
	die();
}
*/
include_once("config/config_sched.php");

TextFlush( "\n\n<b>Updating Server List</b></br></br>\n\n");

$gm_url = $_SERVER['HTTP_HOST'] . $gamepath;
$gm_speed = $sched_ports + $sched_planets + $sched_igb;
$gm_speed_turns = 1;
$gm_speed_turns1 = $turn_rate;

$res = $db->Execute("SELECT COUNT(sector_id) AS n FROM $dbtables[universe]");
$gm_size_sc = $res->fields['n'];

$gm_size_un = $universe_size;
$gm_money_igb = $ibank_interest;
$gm_sofa_on = $sofa_on;
if ($sofa_on === false)
{
	$gm_sofa_on = 0;
}

$gm_all2 = "gm_speed=" . $sched_ticks .
		  "&gm_speed_turns=" . $gm_speed_turns1 .
		  "&gm_size_sc=" . $gm_size_sc .
		  "&gm_size_un=" . $gm_size_un .
		  "&gm_money_igb=" . $gm_money_igb .
		  "&gm_sofa_on=" . $gm_sofa_on .
		  "&gm_url=" . rawurlencode($gm_url) .
		  "&gm_name=" . rawurlencode($game_name);

if($showzeroranking == 1)
	$showzero = "";
else $showzero = "$dbtables[players].turns_used != 0 and";

$res = $db->Execute("SELECT * FROM $dbtables[players], $dbtables[ships] " .
					" WHERE ".$showzero." $dbtables[players].player_id = $dbtables[ships].player_id and destroyed!='Y' and $dbtables[players].player_id > 3 and  $dbtables[players].currentship=$dbtables[ships].ship_id " .
					"and email NOT LIKE '%@npc' ");
					
$dyn_players = $res->RecordCount();	   

$res = $db->Execute("SELECT score, character_name FROM $dbtables[ships], $dbtables[players] WHERE " .
					"$dbtables[ships].player_id = $dbtables[players].player_id AND $dbtables[ships].destroyed='N' AND " .
					"$dbtables[players].email NOT LIKE '%@npc' and $dbtables[players].player_id > 3 ORDER BY score DESC");
$row = $res->fields;
$dyn_top_score = $row['score'];
$dyn_top_player = $row['character_name'];

$res = $db->Execute("SELECT COUNT($dbtables[ships].ship_id) AS x FROM $dbtables[ships],$dbtables[players] WHERE " .
					"$dbtables[ships].player_id = $dbtables[players].player_id AND $dbtables[ships].destroyed='N' AND " .
					"$dbtables[players].email LIKE '%@npc' and $dbtables[players].player_id > 3");
$row = $res->fields;
$dyn_kabal = $row['x'];

$res = $db->Execute("SELECT AVG(hull) AS a1 , AVG(engines) AS a2 , AVG(power) AS a3 , AVG(computer) AS a4 , " .
					"AVG(sensors) AS a5 , AVG(beams) AS a6 , AVG(torp_launchers) AS a7 , AVG(shields) AS a8 , " .
					"AVG(armour) AS a9 , AVG(cloak) AS a10, AVG(ecm) AS a11 FROM $dbtables[ships],$dbtables[players] WHERE " .
					"$dbtables[ships].player_id = $dbtables[players].player_id AND destroyed='N' AND email LIKE '%@npc' and $dbtables[players].player_id > 3");
$row = $res->fields;
$dyn_kabal_lvl = $row['a1'] + $row['a2'] + $row['a3'] + $row['a4'] + $row['a5'] + $row['a6'] + $row['a7'] + $row['a8'] + $row['a9'] + $row['a10'] + $row['a11'];
$dyn_kabal_lvl = $dyn_kabal_lvl / 11;

$dyn_all = "&dyn_players=" . $dyn_players .
		   "&dyn_kabal=" . $dyn_kabal .
		   "&dyn_kabal_lvl=" . $dyn_kabal_lvl .
		   "&dyn_top_score=" . $dyn_top_score .
		   "&dyn_top_player=" . rawurlencode($dyn_top_player) .
		   "&dyn_key=" . rawurlencode($server_list_key);
	
$url2 = $aatrade_server_list_url . "aatrade_ls_server.php?" . $gm_all2 . $dyn_all;
if (isset($creating))
{
	$url2 = $url2 . "&gm_reset=1";
}
	$url2 = $url2 . "&reset_date=$reset_date";
	$url2 = $url2 . "&scheduled_reset=$scheduled_reset";
	$url2 = $url2 . "&private=$private_server";
	$url2 = $url2 . "&ver=". rawurlencode($release_version);

echo "\n\n<!--" . $url2 . "-->\n\n";

$i = @file($url2);
//@fopen($url2,'r');

?>
