<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: readmail.php

include ("config/config.php");
include ("languages/$langdir/lang_readmail.inc");
include ("languages/$langdir/lang_mailto2.inc");

$title = $l_readm_title;

if ((!isset($action)) || ($action == ''))
{
	$action = '';
}

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

if ($action == "delete")
{
	$db->Execute("DELETE FROM $dbtables[messages] WHERE ID='".$ID."' AND recp_id='".$playerinfo['player_id']."'");
}
else if ($action == "delete_all")
{
	$db->Execute("DELETE FROM $dbtables[messages] WHERE recp_id='".$playerinfo['player_id']."'");
}

if ($action == "block" && $name > 3)
{
	$db->Execute("DELETE FROM $dbtables[messages] WHERE sender_id='".$name."' AND recp_id='".$playerinfo['player_id']."'");
	$debug_query = $db->Execute("INSERT INTO $dbtables[message_block] (blocked_player_id, player_id) VALUES " .
				   "('" . $name . "', '" . $playerinfo['player_id'] . "')");
	db_op_result($debug_query,__LINE__,__FILE__);
}

$cur_D = date("Y-m-d");
$cur_T = date("H:i:s");

$res = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo['player_id']."' ORDER BY sent DESC");

$smarty->assign("l_readm_center", $l_readm_center);
$smarty->assign("cur_D", $cur_D);
$smarty->assign("cur_T", $cur_T);

if ($res->EOF)
{
	$smarty->assign("nomessages", 1);
	$smarty->assign("l_readm_nomessage", $l_readm_nomessage);
}
else
{
	$smarty->assign("nomessages", 0);
	$line_counter = true;
	$messagecount = 0;
	while (!$res->EOF)
	{
		$msg = $res->fields;
		if($msg['sender_id'] == 0)
		{
			$sender['avatar'] = "default_avatar.gif";
			$sender['player_id'] = 0;
			$sender['character_name'] = $l_readm_alert;
			$sendership['name'] = $l_readm_alertbody;
		}
		else
		{
			$result = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id='".$msg['sender_id']."'");
			$sendership = $result->fields;
			$result2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id='".$msg['sender_id']."'");
			$sender = $result2->fields;
		}
		$avatar[$messagecount] = $sender['avatar'];
		$msgid[$messagecount] = $msg['ID'];
		$sendername[$messagecount] = $sender['character_name'];
		$senderid[$messagecount] = $sender['player_id'];
		$msgsent[$messagecount] = $msg['sent'];
		$sendname[$messagecount] = $sendership['name'];
		$subject[$messagecount] = $msg['subject'];
		$message[$messagecount] = nl2br($msg['message']);

		$messagecount++;
		$res->MoveNext();
	}
}

$smarty->assign("avatar", $avatar);
$smarty->assign("sender", $sendername);
$smarty->assign("senderid", $senderid);
$smarty->assign("msgsent", $msgsent);
$smarty->assign("msgid", $msgid);
$smarty->assign("sendname", $sendname);
$smarty->assign("subject", $subject);
$smarty->assign("message", $message);

$smarty->assign("messagecount", $messagecount);
$smarty->assign("l_readm_subject", $l_readm_subject);
$smarty->assign("l_readm_sender", $l_readm_sender);
$smarty->assign("l_readm_captn", $l_readm_captn);
$smarty->assign("l_readm_del", $l_readm_del);
$smarty->assign("l_readm_repl", $l_readm_repl);
$smarty->assign("l_readm_title2", $l_readm_title2);
$smarty->assign("l_readm_delete", $l_readm_delete);
$smarty->assign("l_readm_quote", $l_readm_quote);
$smarty->assign("l_readm_block", $l_readm_block);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."readmail.tpl");
include ("footer.php");
?>

