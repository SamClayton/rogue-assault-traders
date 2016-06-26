<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: logout.php

include ("config/config.php");
include ("languages/$langdir/lang_logout.inc");
$no_gzip = 1;


if ((!isset($self_destruct)) && checklogin())
{
	$title = "Logout";
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
	}

	echo $l_global_needlogin;
	include ("footer.php");
	die();
}

$difftime = TIME() - 360;
$stamp = date("Y-m-d H:i:s", $difftime);
$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp' WHERE player_id = $playerinfo[player_id]");

setcookie("PHPSESSID","",0,"/");
session_destroy();

$title = "Logout";
// Skinning stuff
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

if($enable_profilesupport == 1){
	if ((isset($playerinfo['profile_name'])) && ($playerinfo['profile_name'] != ''))
	{

	    $debug_query = $db->Execute("SELECT $dbtables[players].player_id FROM $dbtables[players], $dbtables[ships] WHERE $dbtables[players].turns_used != 0 and $dbtables[players].player_id = $dbtables[ships].player_id and $dbtables[players].currentship=$dbtables[ships].ship_id and destroyed!='Y' " .
        	                "and email NOT LIKE '%@npc' AND $dbtables[players].player_id > 3 ORDER BY score DESC,character_name ASC");
	    db_op_result($debug_query,__LINE__,__FILE__);

		$num_players = $debug_query->RecordCount();
		$rank = 0;
		if ($debug_query)
		{
			while (!$debug_query->EOF)
			{
				$row = $debug_query->fields;
				$rank++;
				if($playerinfo['player_id'] == $row['player_id']){
					break;
				}
				$debug_query->MoveNext();
			}
		}

		$resavg = $db->Execute("SELECT SUM(credits) AS a1 , AVG(computer_normal) AS a4 , " .
							"AVG(sensors_normal) AS a5 , AVG(beams_normal) AS a6 , AVG(torp_launchers_normal) AS a7 , AVG(shields_normal) AS a8 , " .
							"AVG(armour_normal) AS a9 , AVG(cloak_normal) AS a10, AVG(jammer_normal) AS a11 FROM $dbtables[planets],$dbtables[players] WHERE " .
							"$dbtables[planets].owner = $dbtables[players].player_id AND $dbtables[players].player_id = $playerinfo[player_id]");
		$row = $resavg->fields;
		$dyn_avg_lvl = $row['a4'] + $row['a5'] + $row['a6'] + $row['a7'] + $row['a8'] + $row['a9'] + $row['a10'] + $row['a11'];
		$dyn_avg = $dyn_avg_lvl / 8;

		$gm_url = $_SERVER['HTTP_HOST'] . $gamepath;
		$gm_all = "planets_built=" . $playerinfo['planets_built'] .
				  "&planets_lost=" . $playerinfo['planets_lost'] .
				  "&captures=" . $playerinfo['captures'] .
				  "&deaths=" . $playerinfo['deaths'] .
				  "&kills=" . $playerinfo['kills'] .
				  "&rating=" . $playerinfo['rating'] .
				  "&turns_used=" . $playerinfo['turns_used'] .
				  "&credits=" . rawurlencode($playerinfo['credits'] + $row['a1']) .
				  "&score=" . rawurlencode($playerinfo['score']) .
				  "&max_defense=" . $dyn_avg .
				  "&rank=" . $rank .
				  "&ptotal=" . $num_players .
				  "&ship_losses=" . $playerinfo['ship_losses'] .
				  "&self_destruct=" . $self_destruct .
				  "&player_name=" . rawurlencode($playerinfo['character_name']) .
				  "&name=" . $playerinfo['profile_name'] .
				  "&password=" . $playerinfo['profile_password'] .
				  "&server_url=" . rawurlencode($gm_url) .
				  "&server_name=" . rawurlencode($game_name);

		$url = "http://profiles.aatraders.com/update_current.php?" . $gm_all;

		echo "\n\n<!--" . $url . "-->\n\n";

		$i = @file($url);
	}
}

if (isset($self_destruct))
{
	$smarty->assign("error_msg", $l_global_mlogin);
	$smarty->assign("error_msg", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genericdie.tpl");
	include ("footer.php");
	die();
}

$current_score = NUMBER(gen_score($playerinfo['player_id']));
playerlog($playerinfo['player_id'], LOG_LOGOUT, $ip);

$l_logout_text = str_replace("[name]",$playerinfo['character_name'],$l_logout_text);

$smarty->assign("title", $title);
$smarty->assign("l_logout_score", $l_logout_score);
$smarty->assign("current_score", $current_score);
$smarty->assign("l_logout_text", $l_logout_text);
$smarty->display($templatename."logout.tpl");

include ("footer.php");
?>
