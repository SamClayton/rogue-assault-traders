<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: device.php

include ("config/config.php");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_device.inc");

$title = $l_device_title;

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

$debug_query = $db->Execute("SELECT * from $dbtables[probe] WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id] and active='P'");
db_op_result($debug_query,__LINE__,__FILE__);

$ship_probe = $debug_query->RecordCount();

$smarty->assign("ship_probe", $ship_probe);
$smarty->assign("l_probe", $l_probe);

$smarty->assign("l_device_expl", $l_device_expl);
$smarty->assign("l_device", $l_device);
$smarty->assign("l_qty", $l_qty);
$smarty->assign("l_usage", $l_usage);
$smarty->assign("l_beacons", $l_beacons);
$smarty->assign("dev_beacon", NUMBER($shipinfo['dev_beacon']));
$smarty->assign("l_warpedit", $l_warpedit);
$smarty->assign("dev_warpedit", NUMBER($shipinfo['dev_warpedit']));
$smarty->assign("l_sectorgenesis", $l_sectorgenesis);
$smarty->assign("dev_sectorgenesis", NUMBER($shipinfo['dev_sectorgenesis']));
$smarty->assign("l_genesis", $l_genesis);
$smarty->assign("dev_genesis", NUMBER($shipinfo['dev_genesis']));
$smarty->assign("l_deflect", $l_deflect);
$smarty->assign("dev_minedeflector", NUMBER($shipinfo['dev_minedeflector']));
$smarty->assign("l_mines", $l_mines);
$smarty->assign("dev_torps", NUMBER($shipinfo['torps']));
$smarty->assign("l_fighters", $l_fighters);
$smarty->assign("dev_fighters", NUMBER($shipinfo['fighters']));
$smarty->assign("l_ewd", $l_ewd);
$smarty->assign("dev_emerwarp", NUMBER($shipinfo['dev_emerwarp']));
$smarty->assign("l_escape_pod", $l_escape_pod);
$smarty->assign("dev_escapepod", $shipinfo['dev_escapepod']);
$smarty->assign("l_fuel_scoop", $l_fuel_scoop);
$smarty->assign("dev_fuelscoop", $shipinfo['dev_fuelscoop']);
$smarty->assign("l_nova", $l_nova);
$smarty->assign("dev_nova", $shipinfo['dev_nova']);
$smarty->assign("l_yes", $l_yes);
$smarty->assign("l_no", $l_no);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_manual", $l_manual);
$smarty->assign("l_automatic", $l_automatic);
$smarty->assign("title", $title);
$smarty->assign("gotomain", $l_global_mmenu);

$smarty->display($templatename."device.tpl");

include ("footer.php");

?>
