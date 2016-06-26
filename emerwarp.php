<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: emerwarp.php

include ("config/config.php");
include ("languages/$langdir/lang_emerwarp.inc");

$title = $l_ewd_title;

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

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] where sg_sector != 1 and sector_id > 3");
$totrecs=$findem->RecordCount(); 
$getit=$findem->GetArray();

if ($shipinfo['dev_emerwarp'] > 0)
{
	$source_sector = $shipinfo['sector_id'];
	$randplay=mt_rand(0,($totrecs-1));
	$dest_sector = $getit[$randplay]['sector_id'];
	$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1 WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$source_sector");
	db_op_result($zone_query,__LINE__,__FILE__);
	$zones = $zone_query->fields;

	log_move($playerinfo['player_id'],$shipinfo['ship_id'],$source_sector,$dest_sector,$shipinfo['class'],$shipinfo['cloak'],$zones['zone_id']);
	$l_ewd_used=str_replace("[sector]",$dest_sector,$l_ewd_used);
	$ewd_echo = $l_ewd_used;
} 
else 
{
	$ewd_echo = $l_ewd_none;
}

$smarty->assign("title", $title);
$smarty->assign("ewd_echo", $ewd_echo);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."emerwarp.tpl");

include ("footer.php");

?>
