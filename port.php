<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: port.php

include ("config/config.php");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_device.inc");
include ("languages/$langdir/lang_bounty.inc");
$no_gzip = 1;

$title = $l_title_port;

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

$smarty->assign("templatename", $templatename);

if ((!isset($pay)) || ($pay == ''))
{
	$pay = '';
}
//-------------------------------------------------------------------------------------------------

if ($zoneinfo['zone_id'] == 4)
{
	$title=$l_sector_war;

		$smarty->assign("error_msg", $l_war_info);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

	die();
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
	$title="Trade forbidden";

		$smarty->assign("error_msg", $l_no_trade_info);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

	die();
}

elseif ($zoneinfo['allow_trade'] == 'L')
{
	if ($zoneinfo[team_zone] == 'N')
	{
	$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
	$ownerinfo = $res->fields;

	if ($playerinfo[player_id] != $zoneinfo[owner] && $playerinfo[team] == 0 || $playerinfo[team] != $ownerinfo[team])
	{
		$title=$l_ports_tradeforbidden;
		$smarty->assign("error_msg", $l_ports_nooutsiders);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

		die();
	}
	}
	else
	{
	if ($playerinfo[team] != $zoneinfo[owner])
	{
		$title=$l_no_trade;
		$smarty->assign("error_msg", $l_no_trade_out);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

		die();
	}
	}
}

include ("port_" . $sectorinfo['port_type'] . ".php");

?>
