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

empty($colonists) ? $colonists = 'N' : $colonists = 'Y';
empty($fighters) ? $fighters = 'N' : $fighters = 'Y';
empty($torps) ? $torps = 'N' : $torps = 'Y';
empty($goods) ? $goods = 'N' : $goods = 'Y';
empty($organics) ? $organics = 'N' : $organics = 'Y';
empty($ore) ? $ore = 'N' : $ore = 'Y';

$debug_query = $db->Execute("UPDATE $dbtables[players] SET trade_ore='$ore', trade_goods='$goods', trade_organics='$organics', trade_colonists='$colonists', trade_fighters='$fighters', trade_torps='$torps', trade_energy='$energy' WHERE player_id=$playerinfo[player_id]");
db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("l_tdr_globalsetsaved", $l_tdr_globalsetsaved);
$smarty->assign("l_tdr_returnmenu", $l_tdr_returnmenu);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."traderoute_savesettings.tpl");
include ("footer.php");

?>
