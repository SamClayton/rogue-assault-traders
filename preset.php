<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: preset.php

include ("config/config.php");
include ("languages/$langdir/lang_presets.inc");

$title = "$l_pre_title";

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

	$debug_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
	db_op_result($debug_query,__LINE__,__FILE__);

	$sector_max = $debug_query->fields['sector_id'];

if ($name == "add")
{
	$smarty->assign("l_pre_set", $l_pre_set);
	$smarty->assign("presettotal", $presettotal);
	$smarty->assign("l_pre_info", $l_pre_info);
	$smarty->assign("l_pre_save", $l_pre_save);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."presetadd.tpl");
	include ("footer.php");
	die();
}

if ($name == "addcomplete")
{
	$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$preset");
	$sector_type = $sector_res->fields['sg_sector'];
	if ($preset > $sector_max or $preset < 1 or $sector_type == 1)
	{
   		$l_pre_exceed = str_replace("[preset]", $presettotal, $l_pre_exceed);
	   	$l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
		$complete_msg = $l_pre_exceed;
	}else{
		$complete_msg = "$l_pre_set $presettotal: <a href=move.php?move_method=real&engage=1&destination=".$preset.">".$preset."</a> - $l_pre_info: $presetstuff<br>";
		$debug_query = $db->Execute("INSERT INTO $dbtables[presets] (player_id,preset,info) VALUES ($playerinfo[player_id], $preset, '$presetstuff')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
	$smarty->assign("l_pre_set", $l_pre_set);
	$smarty->assign("presettotal", $presettotal);
	$smarty->assign("presetinfo", $presetinfo);
	$smarty->assign("presettext", $presettext);
	$smarty->assign("complete_msg", $complete_msg);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."presetaddcomplete.tpl");
	include ("footer.php");
	die();
}

if ($name == "set")
{
	$smarty->assign("l_pre_set", $l_pre_set);
	$smarty->assign("presettotal", $presettotal);
	$smarty->assign("presetinfo", $presetinfo);
	$smarty->assign("presettext", $presettext);
	$smarty->assign("l_pre_save", $l_pre_save);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."presetset.tpl");
	include ("footer.php");
	die();
}


if ($name == "change")
{
	$debug_query = $db->Execute("DELETE FROM $dbtables[presets] WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	for($totals = 0; $totals < $presettotal; $totals++){
		$tdel="preset$totals";
		$presetdata=${$tdel};
		$preset[$totals] = round(abs($presetdata));
		$tdel="presetstuff$totals";
		$presetjunk[$totals]=${$tdel};

		$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$preset[$totals]");
		$sector_type = $sector_res->fields['sg_sector'];
		if ($preset[$totals] > $sector_max or $preset[$totals] < 1 or $sector_type == 1)
		{
			$l_pre_exceed = str_replace("[preset]", $totals, $l_pre_exceed);
			$l_pre_exceed = str_replace("[sector_max]", $sector_max, $l_pre_exceed);
			$presetjunk[$totals] = $l_pre_exceed;
		}else{
			$debug_query = $db->Execute("INSERT INTO $dbtables[presets] (player_id,preset,info) VALUES ($playerinfo[player_id], $preset[$totals], '$presetjunk[$totals]')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		$presettype[$totals] = $sector_type;
	}
	$smarty->assign("presettype", $presettype);
	$smarty->assign("presettotal", $presettotal);
	$smarty->assign("l_pre_set", $l_pre_set);
	$smarty->assign("l_pre_info", $l_pre_info);
	$smarty->assign("preset", $preset);
	$smarty->assign("presetjunk", $presetjunk);
	$smarty->assign("sector_max", $sector_max);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."presetchange.tpl");
	include ("footer.php");
	die();
}

close_database();
?>


