<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: mailto2.php

include ("config/config.php");
include ("languages/$langdir/lang_mailto2.inc");

$title = $l_sendm_title;

if ((!isset($senderid)) || ($senderid == ''))
{
	$senderid = 0;
}

if ((!isset($to)) || ($to == ''))
{
	$to = '';
}

if ((!isset($content)) || ($content == ''))
{
	$content = '';
}

if ((!isset($msgid)) || ($msgid == ''))
{
	$msgid = 0;
}

if ((!isset($quote)) || ($quote == ''))
{
	$quote = 0;
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

if($msgid != 0){
	$res = $db->Execute("SELECT * FROM $dbtables[messages] WHERE ID=".$msgid." and recp_id=$playerinfo[player_id]");
	$msg = $res->fields;
	$subject = $msg['subject'];
	$sender_id = nl2br($msg['sender_id']);
	$message = nl2br($msg['message']);
	$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$sender_id");
	$sendername = $res->fields['character_name'];
}

if (empty($content))
{
	$res = $db->Execute("SELECT character_name,player_id FROM $dbtables[players] WHERE player_id <> $playerinfo[player_id] ORDER BY " .
						"character_name ASC");
	$count = 0;
	while (!$res->EOF)
	{
		$row = $res->fields;
		if ($row['player_id'] == $name)
		{
			$sendname[$count] = $row['character_name'];
			$sendid[$count] = $row['player_id'];
			$selected[$count] = "selected";
		}
		else
		{
			$sendname[$count] = $row['character_name'];
			$sendid[$count] = $row['player_id'];
			$selected[$count] = "";
		}
		$count++;
		$res->MoveNext();
	}

	$res2 = $db->Execute("SELECT team_name,id FROM $dbtables[teams] ORDER BY team_name ASC");
	while (!$res2->EOF)
	{
		$row2 = $res2->fields;
		$sendname[$count] = "$l_sendm_ally $row2[team_name]";
		$sendid[$count] = -$row2['id'];
		$selected[$count] = "";
		$count++;
		$res2->MoveNext();
	}

	if (isset($subject))
	{
		if (substr_count($subject,"RE: ")==0){
	  		$subject = "RE: " . $subject;
	  	}
		
	}

	if ($quote == 1)
	{
  		$message = "Quote from: $sendername\n". str_repeat("-=", 45) . "\n" . $message. "\n". str_repeat("-=", 45) . "\n\n";
	}else{
		$message = "";
	}

	if ((!isset($subject)) || ($subject == ''))
	{
		$subject = '';
	}

	$smarty->assign("count", $count);
	$smarty->assign("sendname", $sendname);
	$smarty->assign("sendid", $sendid);
	$smarty->assign("selected", $selected);
	$smarty->assign("l_mt_to", $l_sendm_to);
	$smarty->assign("playername", $playerinfo['character_name']);
	$smarty->assign("l_mt_from", $l_sendm_from);
	$smarty->assign("l_mt_subject", $l_sendm_subj);
	$smarty->assign("l_mt_message", $l_sendm_mess);
	$smarty->assign("l_mt_send", $l_sendm_send);
	$smarty->assign("subject", $subject);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("message", $message);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."mailto.tpl");
	include ("footer.php");
}
else
{
	$finalmessage = $l_sendm_sent;
	if ($to >= 0)
	{
		$timestamp = date("Y-m-d H:i:s");

		$res = $db->Execute("SELECT * FROM $dbtables[message_block] WHERE blocked_player_id='$playerinfo[player_id]' and player_id='$to'");
		if($res->RecordCount() == 0){
			$content = htmlspecialchars(clean_words($content));
			$subject = htmlspecialchars(clean_words($subject));
			$debug_query = $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES " .
						   "('".$playerinfo['player_id']."', '" . $to . "', '".$timestamp."', '" .
						   "".$subject."', '".$content."')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		else
		{
			$finalmessage = $target_info['character_name'] . $l_sendm_blocked;
		}
	}
	else
	{
		$finalmessage = "";
		$timestamp = date("Y-m-d H:i:s");
		$to = abs($to);
		$res2 = $db->Execute("SELECT * FROM $dbtables[players] where team='$to'");

		// New lines to prevent SQL injection. Bad stuff.
		$content = htmlspecialchars(clean_words($content));
		$subject = htmlspecialchars(clean_words($subject));

		while (!$res2->EOF)
		{
			$row2 = $res2->fields;
			$res9 = $db->Execute("SELECT * FROM $dbtables[message_block] WHERE blocked_player_id='$playerinfo[player_id]' and player_id='$row2[player_id]'");
			if($res9->RecordCount() == 0){
				$finalmessage .= $row2['character_name'] . " " . $l_sendm_sent . "<br>";
				$debug_query = $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES " .
											"('".$playerinfo['player_id']."', '".$row2['player_id']."', '".$timestamp."', " .
											"'".$subject."', '".$content."')");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				$finalmessage .= $row2['character_name'] . "$l_sendm_blocked<br>";
			}
			$res2->MoveNext();
		}
	}
	$smarty->assign("error_msg", $finalmessage);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."mailtosent.tpl");
	include ("footer.php");
}

close_database();
?>
