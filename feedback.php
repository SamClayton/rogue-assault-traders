<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: feedback.php

include ("config/config.php");
include ("languages/$langdir/lang_feedback.inc");

$title = $l_feedback_title;

if ((!isset($msg)) || ($msg == ''))
{
	$msg = '';
}

if ((!isset($hdrs)) || ($hdrs == ''))
{
	$hdrs = '';
}

if ((!isset($subject)) || ($subject == ''))
{
	$subject = '';
}

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

if (empty($_POST['content']))
{

	$smarty->assign("l_feedback_info", $l_feedback_info);
	$smarty->assign("l_feedback_to", $l_feedback_to);
	$smarty->assign("l_feedback_from", $l_feedback_from);
	$smarty->assign("l_feedback_topi", $l_feedback_topi);
	$smarty->assign("l_feedback_message", $l_feedback_message);
	$smarty->assign("l_submit", $l_submit);
	$smarty->assign("playername", $playerinfo['character_name']);
	$smarty->assign("playeremail", $playerinfo['email']);
	$smarty->assign("l_feedback_feedback", $l_feedback_feedback);
	$smarty->assign("l_reset", $l_reset);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."feedbackenter.tpl");
	include ("footer.php");
	die();
} 
else 
{
	// New lines to prevent SQL injection. Bad stuff.
	$content = htmlspecialchars($_POST['content']);
	$subject = htmlspecialchars($_POST['subject']);

	$msg .= "IP address - " . getenv("REMOTE_ADDR") . "\r\nGame Name - $playerinfo[character_name]\r\n\r\n$content\n\nhttp://". $_SERVER['HTTP_HOST'] . "$gamepath\r\n";
	$msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
	$hdrs .= "From: $playerinfo[character_name] <$playerinfo[email]>\r\n";

	$e_response = mail($admin_mail,$l_feedback_subj,$msg,$hdrs);
	if ($e_response===TRUE)
	{
		$error_msg = "<font color=\"lime\">Message Sent</font><br>";
		AddELog($admin_mail,2,'Y',$l_feedback_subj,$e_response);
	}
	else
	{
		$error_msg = "<font color=\"red\">Message failed to send!</font><br>\n";
		AddELog($admin_mail,2,'N',$l_feedback_subj,$e_response);
	}

	$smarty->assign("error_msg", $error_msg);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."feedbacksend.tpl");
	include ("footer.php");
	die();
}

close_database();
?>
