<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the		 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: traderoute_create.php

include ("config/config.php");
include ("languages/$langdir/lang_traderoute.inc");
include ("languages/$langdir/lang_teams.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_ports.inc");
$no_gzip = 1;
$total_experience = 0;

$title = $l_tdr_title;

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

$smarty->assign("trade_ore", $playerinfo['trade_ore']);
$smarty->assign("l_tdr_ore", $l_tdr_ore);
$smarty->assign("trade_organics", $playerinfo['trade_organics']);
$smarty->assign("l_tdr_organics", $l_tdr_organics);
$smarty->assign("trade_goods", $playerinfo['trade_goods']);
$smarty->assign("l_tdr_goods", $l_tdr_goods);
$smarty->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
$smarty->assign("l_tdr_save", $l_tdr_save);
$smarty->assign("trade_energy", $playerinfo['trade_energy']);
$smarty->assign("l_tdr_keep", $l_tdr_keep);
$smarty->assign("l_tdr_trade", $l_tdr_trade);
$smarty->assign("l_tdr_tdrescooped", $l_tdr_tdrescooped);
$smarty->assign("trade_torps", $playerinfo['trade_torps']);
$smarty->assign("l_tdr_torps", $l_tdr_torps);
$smarty->assign("trade_fighters", $playerinfo['trade_fighters']);
$smarty->assign("l_tdr_fighters", $l_tdr_fighters);
$smarty->assign("trade_colonists", $playerinfo['trade_colonists']);
$smarty->assign("l_tdr_colonists", $l_tdr_colonists);
$smarty->assign("l_tdr_tdrsportsrc", $l_tdr_tdrsportsrc);
$smarty->assign("l_tdr_tdrsportsrc", $l_tdr_tdrsportsrc);
$smarty->assign("l_tdr_globalset", $l_tdr_globalset);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."traderoute_settings.tpl");
include ("footer.php");
?>
