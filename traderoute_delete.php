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

//-------------------------------------------------------------------------------------------------

bigtitle();

if (isset($_POST["TRDel"]))
{
	for ($i = 0; $i < count($TRDel); $i++)
	{
		$get_planetinfo = $db->Execute("delete from $dbtables[traderoutes] WHERE  traderoute_id =$TRDel[$i] and owner=$playerinfo[player_id]");
	}

	$smarty->assign("error_msg", $l_tdr_tdrdeleted);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_die.tpl");
	include ("footer.php");
}else{
	$smarty->assign("error_msg", $l_tdr_returnmenu);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."traderoute_die.tpl");
	include ("footer.php");
}
?>
