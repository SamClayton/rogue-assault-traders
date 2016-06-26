<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: messageblockmanager.php

include ("config/config.php");
include ("languages/$langdir/lang_blockmanager.inc");

$title = $l_block_title;

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

if($command == "block"){
	$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE character_name='$to2'");
	$target_info = $res->fields;
	if($target_info['player_id'] > 3){
		$db->Execute("DELETE FROM $dbtables[messages] WHERE sender_id='".$target_info['player_id']."' AND recp_id='".$playerinfo['player_id']."'");
		$debug_query = $db->Execute("INSERT INTO $dbtables[message_block] (blocked_player_id, player_id) VALUES " .
					   "('" . $target_info['player_id'] . "', '" . $playerinfo['player_id'] . "')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

if($command == "unblock"){
	$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE character_name='$to'");
	$target_info = $res->fields;
	$debug_query = $db->Execute("DELETE FROM $dbtables[message_block] WHERE blocked_player_id = $target_info[player_id] and player_id = $playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
}

// Get Blocked Players
$res = $db->Execute("SELECT $dbtables[players].character_name, $dbtables[players].player_id FROM $dbtables[players], $dbtables[message_block] WHERE $dbtables[players].player_id <> $playerinfo[player_id]
					and $dbtables[message_block].player_id = $playerinfo[player_id] and $dbtables[message_block].blocked_player_id = $dbtables[players].player_id ORDER BY $dbtables[players].character_name ASC");
$blockcount = 0;
while (!$res->EOF)
{
	$row = $res->fields;
	$blockedplayers[$blockcount] = $row['character_name'];
	$blockcount++;
	$res->MoveNext();
}

// Get Unblocked Players

$res = $db->Execute("SELECT character_name, player_id FROM $dbtables[players] WHERE player_id <> $playerinfo[player_id]
					and player_id > 3 ORDER BY character_name ASC");
$unblockcount = 0;
while (!$res->EOF)
{
	$row = $res->fields;
	$res2 = $db->Execute("SELECT * FROM $dbtables[message_block] WHERE player_id = $playerinfo[player_id] and blocked_player_id = $row[player_id]");
	if($res2->RecordCount() == 0){
		$unblockedplayers[$unblockcount] = $row['character_name'];
		$unblockcount++;
	}
	$res->MoveNext();
}


$smarty->assign("blockedplayers", $blockedplayers);
$smarty->assign("blockcount", $blockcount);
$smarty->assign("unblockedplayers", $unblockedplayers);
$smarty->assign("unblockcount", $unblockcount);
$smarty->assign("l_block_receivefrom", $l_block_receivefrom);
$smarty->assign("l_block_block", $l_block_block);
$smarty->assign("l_block_blockfrom", $l_block_blockfrom);
$smarty->assign("l_block_unblock", $l_block_unblock);
$smarty->assign("l_block_empty", $l_block_empty);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."blockmanager.tpl");
include ("footer.php");

close_database();
?>
