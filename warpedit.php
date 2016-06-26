<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: warpedit.php

include ("config/config.php");
include ("languages/$langdir/lang_warpedit1.inc");
include ("languages/$langdir/lang_report.inc");

$title = $l_warp_title;

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

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_warp_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpeditdie.tpl");
	include ("footer.php");
	die();
}

if ($shipinfo['dev_warpedit'] < 1)
{
	$smarty->assign("error_msg", $l_warp_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpeditdie.tpl");
	include ("footer.php");
	die();
}

if ($zoneinfo['allow_warpedit'] == 'N')
{
	$smarty->assign("error_msg", $l_warp_forbid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpeditdie.tpl");
	include ("footer.php");
	die();
}

if ($zoneinfo['allow_warpedit'] == 'L')
{
	$result5 = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id='$zoneinfo[owner]'");
	$zoneteam = $result5->fields;

	if ($zoneinfo[owner] != $playerinfo[player_id])
	{
		if (($zoneteam[team] != $playerinfo[team]) || ($playerinfo[team] == 0))
		{
			$smarty->assign("error_msg", $l_warp_forbid);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."warpeditdie.tpl");
			include ("footer.php");
			die();
		}
	}
}


if(!isset($confirm)){
	$linkcount = 0;

	$result2 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start=$shipinfo[sector_id] ORDER BY link_dest ASC");
	if ($result2 < 1)
	{
		$smarty->assign("linkmessage", $l_warp_nolink);
	}
	else
	{
		$smarty->assign("linkmessage", $l_warp_linkto);
		$linkcount = 0;
		while (!$result2->EOF)
		{
			$linklist[$linkcount] = $result2->fields['link_dest'];
			$linkcount++;
			$result2->MoveNext();
		}
	}

	$smarty->assign("linkcount", $linkcount);
	$smarty->assign("linklist", $linklist);

	$smarty->assign("l_warp_query", $l_warp_query);
	$smarty->assign("l_warp_oneway", $l_warp_oneway);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("l_warp_dest", $l_warp_dest);
	$smarty->assign("l_warp_destquery", $l_warp_destquery);
	$smarty->assign("l_warp_bothway", $l_warp_bothway);
	$smarty->assign("l_submit", $l_submit);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit.tpl");

	include ("footer.php");
}else{
	$distance = floor(calc_dist($shipinfo['sector_id'],$target_sector));
	$cost = $distance * $warplink_build_cost;
	$energycost = $distance * $warplink_build_energy;

	$smarty->assign("l_player", $l_player);
	$smarty->assign("l_ship", $l_ship);
	$smarty->assign("l_energy", $l_energy);
	$smarty->assign("l_credits", $l_credits);
	$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
	$smarty->assign("shipenergy", NUMBER($shipinfo['energy']));
	$smarty->assign("l_warp_costenergy", $l_warp_costenergy);
	$smarty->assign("l_warp_costdelete", $l_warp_costdelete);
	$smarty->assign("l_warp_costcreate", $l_warp_costcreate);
	$smarty->assign("l_warp_delete", $l_warp_delete);
	$smarty->assign("l_warp_lightyears", $l_warp_lightyears);
	$smarty->assign("l_warp_distance", $l_warp_distance);
	$smarty->assign("l_warp_andsector", $l_warp_andsector);
	$smarty->assign("l_warp_create", $l_warp_create);
	$smarty->assign("startsector", $shipinfo['sector_id']);
	$smarty->assign("cost", NUMBER($cost));
	$smarty->assign("energycost", NUMBER($energycost));
	$smarty->assign("distance", $distance);
	$smarty->assign("l_yes", $l_yes);
	$smarty->assign("l_no", $l_no);
	$smarty->assign("confirm", $confirm);
	$smarty->assign("target_sector", $target_sector);
	$smarty->assign("bothway", $bothway);
	$smarty->assign("oneway", $oneway);
	$smarty->assign("l_warp_query", $l_warp_query);
	$smarty->assign("l_warp_oneway", $l_warp_oneway);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("l_warp_dest", $l_warp_dest);
	$smarty->assign("l_warp_destquery", $l_warp_destquery);
	$smarty->assign("l_warp_bothway", $l_warp_bothway);
	$smarty->assign("l_submit", $l_submit);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_confirm.tpl");

	include ("footer.php");
}

close_database();
?>
