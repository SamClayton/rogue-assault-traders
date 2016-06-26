<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: login2.php

include ("config/config.php");
include ("languages/$langdir/lang_login2.inc");
$no_gzip = 1;

// Here we set to defaults all non-defined variables. Later we will also clean them and typecast them.

if ((!isset($_POST['res'])) || ($_POST['res'] == ''))
{
	$_POST['res'] = '';
}

if ((!isset($_POST['character_name'])) || ($_POST['character_name'] == ''))
{
	$_POST['character_name'] = '';
}

// Cleans character name before we run the select below. Otherwise, someone can use
// semi-colons in the char name at login and sql inject.
// TODO! : Mark up the new user page to mention it..
// Allows A-Z, a-z, 0-9, whitespace, minus/dash, equals, backslash, explanation point, ampersand, asterix, and underscore.

$_POST['character_name'] = preg_replace ("/[^A-Za-z0-9\s\-\=\\\'\!\&\*\_]/","",$_POST['character_name']);

if ((!isset($_POST['pass'])) || ($_POST['pass'] == ''))
{
	$_POST['pass'] = '';
}

// Test to see if server is closed to logins
$playerfound = false;

$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE character_name='$_POST[character_name]' and password='$_POST[pass]'");
db_op_result($debug_query,__LINE__,__FILE__);

if ($debug_query)
{
	$playerfound = $debug_query->RecordCount();
}

$playerinfo = $debug_query->fields;

if($player_limit > 0)
{
	$debug_query = sql_time_since_login();
	$online = $debug_query->RecordCount();

	if($online >= $player_limit && $playerinfo['player_id'] > 3)
	{
		$title = $l_login_title2;
		// Skinning stuff
		if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
			$templatename = $default_template;
		}else{
			$templatename = $playerinfo['template'];
		}
		include ("templates/".$templatename."/skin_config.inc");
		include ("header.php");

			$smarty->assign("title", $title);
		$smarty->assign("templatename", $templatename);

		$smarty->assign("error_msg", "<font size=3 color=yellow>$l_login_playerlimit</font>");
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}
}

if($playerinfo['player_id'] > 3 && $playerinfo['npc'] != 0){
	$playerfound = 0;
}

if ($playerfound)
{
	$debug_query = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$playerinfo[player_id] AND " .
								"ship_id=$playerinfo[currentship]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$shipinfo = $debug_query->fields;

	if($playerinfo['team'] != 0){
		$debug_query = $db->Execute("select * from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$time = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("update $dbtables[fplayers] set lastonline='$forumplayer[currenttime]', currenttime='$time' where player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	$debug_query = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask OR '$playerinfo[ip_address]' LIKE ban_mask or email='$playerinfo[email]'");
	db_op_result($debug_query,__LINE__,__FILE__);

	if ($debug_query->RecordCount() != 0)
	{
		$banned = 1;
		$title = $l_login_title2;
		// Skinning stuff
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

		$smarty->assign("error_msg", "<font size=3 color=red>$l_login_banned</font>");
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}
}

if ($server_closed)
{
	$title = $l_login_sclosed;
	// Skinning stuff
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

	$smarty->assign("error_msg", $l_login_closed_message);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."genericdie.tpl");
	include ("footer.php");
	die();
}

$title = $l_login_title2;
// Skinning stuff
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

function ip_log($player_id,$ip_address)
{
	global $db, $dbtables,$enhanced_logging;
	if ($enhanced_logging)
	{
		$stamp = date("Y-m-d H:i:s"); 
		$debug_query = $db->Execute("INSERT INTO $dbtables[ip_log] (player_id,ip_address,time) VALUES ($player_id,'$ip_address','$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__); 
	}
}

if ($playerfound)
{
	if ($playerinfo['password'] == $_POST['pass'])
	{
		// password is correct
		$userpass = $playerinfo['email']."+".$_POST['pass'];
		$_SESSION['userpass'] = $userpass;

		if ($shipinfo['destroyed'] == "N")
		{
			// player's ship has not been destroyed
			playerlog($playerinfo['player_id'], LOG_LOGIN, $ip);
			ip_log($playerinfo['player_id'],$ip);
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET forum_login='$playerinfo[last_login]', last_login='$stamp', ip_address='$ip' WHERE " .
										"player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			close_database();
			if ($playerinfo['turns_used'] == 0)
			{
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=options.php\">";
			}
			else
			{
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
			}
		}
		else
		{
			// player's ship has been destroyed
			if ($shipinfo['destroyed'] == "K")
			{
				playerlog($playerinfo['player_id'], LOG_LOGIN, $ip);
				ip_log($playerinfo['player_id'],$ip);
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET forum_login='$playerinfo[last_login]', last_login='$stamp', ip_address='$ip' WHERE " .
											"player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$smarty->assign("error_msg", $l_login_died);
				$smarty->assign("error_msg2", "");
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."genericdie.tpl");
				include ("footer.php");
				die();
			}
			else if ($shipinfo['dev_escapepod'] == "Y")
			{
				playerlog($playerinfo['player_id'], LOG_LOGIN, $ip);
				ip_log($playerinfo['player_id'],$ip);
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET forum_login='$playerinfo[last_login]', last_login='$stamp', ip_address='$ip' WHERE " .
											"player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				player_ship_destroyed($playerinfo['currentship'], $playerinfo['player_id'], $playerinfo['rating'], 0, 0);

				if ($spy_success_factor)
				{
					spy_ship_destroyed($playerinfo['currentship'],0);
				}

				if ($dig_success_factor)
				{
					dig_ship_destroyed($playerinfo['currentship'],0);
				}

				$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $playerinfo[currentship] and active='P'"); 
				db_op_result($debug_query,__LINE__,__FILE__);

				$smarty->assign("error_msg", $l_login_died);
				$smarty->assign("error_msg2", "");
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."genericdie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				// Check if $newbie_nice is set, if so, verify ship limits
				if ($newbie_nice == "YES")
				{
					$debug_query = $db->Execute("SELECT hull,engines,power,computer,sensors,armour,shields,beams,torp_launchers,cloak FROM $dbtables[ships] WHERE player_id='$playerinfo[player_id]' AND ship_id=$playerinfo[currentship] AND hull<='$newbie_hull' AND engines<='$newbie_engines' AND power<='$newbie_power' AND computer<='$newbie_computer' AND sensors<='$newbie_sensors' AND armour<='$newbie_armour' AND shields<='$newbie_shields' AND beams<='$newbie_beams' AND torp_launchers<='$newbie_torp_launchers' AND cloak<='$newbie_cloak'");
					db_op_result($debug_query,__LINE__,__FILE__);
					$num_rows = $debug_query->RecordCount();

					if ($num_rows and $playerinfo['turns_used'] < $start_turns)
					{
						player_ship_destroyed($playerinfo['currentship'], $playerinfo['player_id'], $playerinfo['rating'], 0, 0);
						$debug_query = $db->Execute("UPDATE $dbtables[ships] SET destroyed='N' WHERE player_id=$playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$stamp = date("Y-m-d H:i:s");
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET forum_login='$playerinfo[last_login]', last_login='$stamp', credits=credits+1000 WHERE player_id=$playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						if ($spy_success_factor)
						{
							spy_ship_destroyed($playerinfo['currentship'],0);
						}

						if ($dig_success_factor)
						{
							dig_ship_destroyed($playerinfo['currentship'],0);
						}

						$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $playerinfo[currentship] and active='P'"); 
						db_op_result($debug_query,__LINE__,__FILE__);

						$smarty->assign("l_here", ucfirst($l_here));
						$smarty->assign("l_login_blackbox", $l_login_blackbox);
						$smarty->assign("l_login_newbie", $l_login_newbie);
						$smarty->assign("error_msg", $l_login_diedincident);
						$smarty->assign("error_msg2", $l_login_newlife);
						$smarty->assign("l_clickme", $l_clickme);
						$smarty->assign("l_new_login", $l_new_login);
						$smarty->display($templatename."login2.tpl");
						include ("footer.php");
						die();
					}
					else
					{
						$smarty->assign("error_msg", $l_login_diedincident);
						$smarty->assign("error_msg2", $l_login_looser);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."genericdie.tpl");
						include ("footer.php");
						die();
					}

				} // End if $newbie_nice
				else
				{
					$smarty->assign("error_msg", $l_login_diedincident);
					$smarty->assign("error_msg2", $l_login_looser);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."genericdie.tpl");
					include ("footer.php");
					die();
				}
			}
		}
	}
}
else
{
	$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE character_name='$_POST[character_name]'");
	db_op_result($debug_query,__LINE__,__FILE__);

	if ($debug_query)
	{
		$playerfound = $debug_query->RecordCount();
	}

	if($playerfound)
	{
		// password is incorrect
		$playerinfo = $debug_query->fields;

		$l_new_message = "Someone attempted to login to your player account with a bad password from IP: $ip\r\n\r\n";

		$msg = "$l_new_message\r\n\r\nhttp://". $_SERVER['HTTP_HOST'] . "$gamepath\r\n";
		$msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
		$hdrs .= "From: AATRADE System Mailer <$admin_mail>\r\n";

		$e_response = mail($playerinfo['email'],"A Bad Login Attempt Detected", $msg,$hdrs);

		if ($e_response === TRUE)
		{
			$smarty->assign("emailresult", "<font color=\"lime\">Bad Login Email sent to $username</font>");
			AddELog($playerinfo['email'],1,'Y',"A Bad Login Attempt Detected",$e_response);
		}
		else
		{
			$smarty->assign("emailresult", "<font color=\"Red\">Bad Login Email failed to send to $username</font>");
			AddELog($playerinfo['email'],1,'N',"A Bad Login Attempt Detected",$e_response);
		}

		$smarty->assign("l_login_4gotpw1", $l_login_4gotpw1);
		$smarty->assign("playeremail", $playerinfo['email']);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_login_4gotpw2", $l_login_4gotpw2);
		$smarty->assign("l_login_4gotpw3", $l_login_4gotpw3);
		$smarty->assign("ip", $ip);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_new_login", $l_new_login);
		$smarty->display($templatename."login2-badpassword.tpl");
		include ("footer.php");
		playerlog($playerinfo['player_id'], LOG_BADLOGIN, $ip);
		die();
	}
	else
	{
		$smarty->assign("error_msg", $l_login_noone);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}
}

?>
