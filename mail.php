<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: mail.php

include ("config/config.php");
include ("languages/$langdir/lang_mail.inc");

if ((!isset($mail)) || ($mail == ''))
{
	$mail = '';
}

$title = $l_mail_title;
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

$result = $db->Execute ("select email, password from $dbtables[players] where email='$mail'");
if (!$result->EOF) 
{
	$mailplayer_info = $result->fields;
	$l_mail_message = str_replace("[pass]",$mailplayer_info['password'],$l_mail_message);
	$msg = $l_mail_message;
	$msg .="\r\n\r\nhttp://$SERVER_NAME$gamepath\r\n";
	$msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
	$hdrs = "From: Alien Assault Tradewars Mailer <$admin_mail>\r\n"; 
	$e_response = mail($mail,$l_mail_topic,$msg,$hdrs); 

	if ($e_response === TRUE) 
	{ 
		$smarty->assign("mailresult", "<font color=\"lime\">$l_mail_sent $mail.</font>");
		AddELog($mail,3,'Y',$l_mail_topic,$e_response); 
	} 
	else 
	{ 
		$smarty->assign("mailresult", "<font color=\"red\">$l_mail_failed $mail.</font>");
		AddELog($mail,3,'N',$l_mail_topic,$e_response); 
	} 
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("l_new_login", $l_new_login);
} 
else 
{ 
	$smarty->assign("mailresult", $l_mail_noplayer);
	$smarty->assign("l_clickme", "");
	$smarty->assign("l_new_login", "");
}

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."mail.tpl");
include ("footer.php");
?>

