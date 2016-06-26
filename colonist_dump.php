<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: colonist_dump.php

include ("config/config.php");
include ("languages/$langdir/lang_dump.inc");

$title = $l_dump_title;

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

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("l_dump_turn", $l_dump_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($default_template."dump.tpl");
	include ("footer.php");
	die();
}

if ($shipinfo['colonists'] == 0)
{
	$dump_echo = $l_dump_nocol;
} 
elseif ($sectorinfo['port_type'] == "upgrades") 
{
	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET colonists=0 WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$dump_echo = $l_dump_dumped;
} 
else 
{
	$dump_echo = $l_dump_nono;
}

$smarty->assign("dump_echo", $dump_echo);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($default_template."dump.tpl");

include ("footer.php");

?>
