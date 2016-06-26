<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: profile.php

include ("config/config.php");
include ("languages/$langdir/lang_profile.inc");

$title = $l_profile_title;

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

if($enable_profilesupport == 1){
	if ($command == "Register"){
		$url = "http://profiles.aatraders.com/validate_profile.php?name=" . rawurlencode($profilename) . "&password=" . rawurlencode($profilepassword) . "&url=$url&game=$game";

//		echo "\n\n<!--" . $url . "-->\n\n";

		$lines = @file($url);
		$result = trim($lines[0]);
		$name = trim($lines[1]);
		$password = trim($lines[2]);
		$profile_id = trim($lines[3]);
		if($result == "ok"){
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET profile_name='$name', profile_password='$password', profile_id=$profile_id WHERE player_id = $playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("message", $l_profile_registered);
			$smarty->assign("message2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."profile.tpl");
			include ("footer.php");
			die();
		}
		elseif($result == "bad")
		{
			$smarty->assign("l_profile_nomatch", $l_profile_nomatch);
			$smarty->assign("l_profile_tryagain", $l_profile_tryagain);
			$smarty->assign("l_here", $l_here);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."profile-bad.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			if($name == "server"){
				$smarty->assign("message", $l_profile_bannedserver);
				$smarty->assign("message2", "");
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."profile.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$smarty->assign("message", $l_profile_bannedplayer);
				$smarty->assign("message2", "");
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."profile.tpl");
				include ("footer.php");
				die();
			}
		}
	}
	else
	{
		$smarty->assign("l_profile_name", $l_profile_name);
		$smarty->assign("l_profile_password", $l_profile_password);
		$smarty->assign("url", rawurlencode($_SERVER['HTTP_HOST'] . $gamepath));
		$smarty->assign("game", rawurlencode($game_name));
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."profile-register.tpl");
		include ("footer.php");
		die();
	}
}
else
{
	$smarty->assign("message", $l_profile_notenabled);
	$smarty->assign("message2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."profile.tpl");
	include ("footer.php");
	die();
}
?>
