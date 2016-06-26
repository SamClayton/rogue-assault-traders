<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: team.php

include ("config/config.php");
include ("languages/$langdir/lang_team.inc");

$title = $l_teamm_title;

if ((!isset($planet_id)) || ($planet_id == ''))
{
	$planet_id = '';
}

if (checklogin())
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

$result2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id and owner=$playerinfo[player_id]");
if ($result2)
{
	$planetinfo=$result2->fields;

	if ($planetinfo['owner'] == $playerinfo['player_id'] && $playerinfo['team'] > 0)
	{
		if ($action == "planetteam")
		{
			$smarty->assign("error_msg", $l_teamm_toteam);
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team='$playerinfo[team]' WHERE planet_id=$planet_id");
			db_op_result($debug_query,__LINE__,__FILE__);
			$ownership = calc_ownership($shipinfo['sector_id']);
		}
		if ($action == "planetpersonal" && $planetinfo['team'] == $playerinfo['team'])
		{
			$smarty->assign("error_msg", $l_teamm_topersonal);
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team='0' WHERE planet_id=$planet_id");
			db_op_result($debug_query,__LINE__,__FILE__);

			$ownership = calc_ownership($shipinfo['sector_id']);

			// Kick other players off the planet
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE on_planet='Y' AND planet_id = $planet_id AND player_id <> $playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		$smarty->assign("error_msg2", $ownership);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."team.tpl");
		include ("footer.php");
		die();
	}else{
		$smarty->assign("error_msg", $l_teamm_exploit);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."team.tpl");
		include ("footer.php");
		die();
	}
}else{
	$smarty->assign("error_msg", $l_teamm_exploit);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team.tpl");
	include ("footer.php");
	die();
}

close_database();
?>
