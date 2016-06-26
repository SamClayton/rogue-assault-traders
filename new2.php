<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: new2.php

include ("config/config.php");
include ("languages/$langdir/lang_mail.inc");
include ("languages/$langdir/lang_new2.inc");
include ("languages/$langdir/lang_login2.inc");

function hexColor($color) { 
	return sprintf("%02X%02X%02X",$color[0],$color[1],$color[2]); 
} 

function newplayer($email, $char, $pass, $ship_name)
{
	global $db, $dbtables, $db_type;
	global $start_credits, $start_turns, $default_lang, $random_default_template, $default_template_alt;
	global $start_armour, $start_energy, $start_fighters, $max_turns, $default_template;

	$stamp = date("Y-m-d H:i:s");

	$query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[players]");
	db_op_result($query,__LINE__,__FILE__);
	$res = $query->fields;

	$mturns = $res['mturns'];

	if ($mturns > $max_turns)
	{
		$mturns = $max_turns;
	}

	if ($mturns < $start_turns)
	{
		$mturns = $start_turns;
	}

	$col[0] = 50 + mt_rand(0, 205);
	$col[1] = 50 + mt_rand(0, 205);
	$col[2] = 50 + mt_rand(0, 205);
	$zonecolor = "#".hexColor($col);

	$randomroll =  mt_rand(0, 100);
	if ($randomroll < 50 && $random_default_template)
	{
		$pick_template = $default_template_alt;
	}else{
		$pick_template = $default_template;
	}
	
	//Create player
	$debug_query = $db->Execute("INSERT INTO $dbtables[players] (currentship, ".
								"character_name, password, email, credits, turns, ".
								"turns_used, last_login, rating, ".
								"score, team, team_invite, ip_address, 
								trade_colonists, trade_fighters, ".
								"trade_torps, trade_energy, template, avatar) VALUES(" .
//								"''," .			 //player_id   - not needed.
								"0," .			  //currentship
								"'$char'," .		//character_name
								"'$pass'," .		//password
								"'$email'," .	   //email
//								"0,".				 //vzwnum
//								"0,".			   //icqnum
								"$start_credits," . //credits
								"$mturns," .		//turns
								"0," .			  //turns_used
								"'$stamp'," .	   //last_login
								"0," .			  //rating
								"0," .			  //score
								"0," .			  //team
								"0," .			  //team_invite
//								"''," .
							  "'". getenv("REMOTE_ADDR") ."'," .		  //ip_address

								"'Y'," .			//trade_colonists
								"'N'," .			//trade_fighters
								"'N'," .			//trade_torps
								"'Y'," .			//trade_energy
								"'$pick_template',
								'default_avatar.gif')");
	db_op_result($debug_query,__LINE__,__FILE__);

	// Get the new player's id
	$res = $db->Execute("SELECT player_id from $dbtables[players] WHERE email='$email'");
	db_op_result($res,__LINE__,__FILE__);
	$player_id = $res->fields['player_id'];

	// Create player's ship
	$debug_query = $db->Execute("INSERT INTO $dbtables[ships] (player_id, ".
								"class, name, destroyed, basehull, hull, engines, ".
								"power, computer, sensors, beams, ".
								"torp_launchers, torps, shields, armour, ".
								"armour_pts, cloak, sector_id, ore, ".
								"organics, goods, energy, colonists, ".
								"fighters, on_planet, dev_warpedit, ".
								"dev_genesis, dev_emerwarp, ".
								"dev_escapepod, dev_fuelscoop, ".
								"dev_minedeflector, planet_id, ".
								"cleared_defences,dev_nova) VALUES(" .
//								"''," .			 //ship_id	 -  not needed.
								"'$player_id'," .	 //player_id
								"'10'," .			//class
								"'$ship_name'," .   //name
								"'N'," .			//destroyed
								"10,".				//basehull
								"0," .			  //hull
								"0," .			  //engines
								"0," .			  //power
								"0," .			  //computer
								"0," .			  //sensors
								"0," .			  //beams
								"0," .			  //torp_launchers
								"0," .			  //torps
								"0," .			  //shields
								"0," .			  //armour
								"$start_armour," .  //armour_pts
								"0," .			  //cloak
								"1," .			  //sector_id
								"0," .			  //ore
								"0," .			  //organics
								"0," .			  //goods
								"$start_energy," .  //energy
								"0," .			  //colonists
								"$start_fighters," .//fighters
								"'N'," .			//on_planet
								"0," .			  //dev_warpedit
								"0," .			  //dev_genesis
								"0," .			  //dev_emerwarp
								"'Y'," .			//dev_escapepod
								"'N'," .			//dev_fuelscoop
								"0," .			  //dev_minedeflector
								"0," .			  //planet_id
								"''," .			 //cleared_defences
								 "'N'" .			//dev_nova
								")");
	db_op_result($debug_query,__LINE__,__FILE__);

	// Get the new ship's id
	$res = $db->Execute("SELECT ship_id from $dbtables[ships] WHERE player_id=$player_id");
	db_op_result($res,__LINE__,__FILE__);
	$ship_id = $res->fields['ship_id'];

	// Insert current ship in players table
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET currentship=$ship_id WHERE player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	// Create player's zone
	$zone_name = "$char" . "&#39;s Territory";
	$debug_query = $db->Execute("INSERT INTO $dbtables[zones] (zone_name, ".
								"owner, team_zone, allow_attack, ".
								"allow_planetattack, allow_warpedit, ".
								"allow_planet, allow_trade, allow_defenses, ".
								"max_hull, zone_color) VALUES(" .
//								"''," .			 //zone_id	  -  not needed
								"'$zone_name'," .   //zone_name
								"'$player_id'," .	 //owner
								"'N'," .			//team_zone
								"'Y'," .			//allow_attack
								"'Y'," .			//allow_planetattack
								"'Y'," .			//allow_warpedit
								"'Y'," .			//allow_planet
								"'Y'," .			//allow_trade
								"'Y'," .			//allow_defenses
								"0," .			   //max_hull
								"'$zonecolor'" .			   //player color
								")");
	db_op_result($debug_query,__LINE__,__FILE__);

	for($total = 0; $total < 3; $total++){
		$debug_query = $db->Execute("INSERT INTO $dbtables[presets] (player_id,preset) VALUES ('$player_id',1)");	 
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	sql_insert_identity_on($dbtables['ibank_accounts']);

	$stamp = date("Y-m-d H:i:s");	 
	$debug_query = $db->Execute("INSERT INTO $dbtables[ibank_accounts] (player_id,balance,loan,loantime) VALUES ('$player_id',0,0,'$stamp')");	 
	db_op_result($debug_query,__LINE__,__FILE__);

	sql_insert_identity_off($dbtables['ibank_accounts']);

	return $player_id;
}

function is_valid_email_eregi ($address) { 

	return (eregi( 
		'^[-!#$%&\'*+\\./0-9=?A-Z^_`{|}~]+'.	  // the user name 
		'@'.									  // the ubiquitous at-sign 
		'([-0-9A-Z]+\.)+' .					   // host, sub-, and domain names 
		'([0-9A-Z]){2,4}$',					   // top-level domain (TLD) 
		trim($address))); 
} 

if ((!isset($character)) || ($character == ''))
{
	$character = '';
}

if ((!isset($shipname)) || ($shipname == ''))
{
	$shipname = '';
}

if ((!isset($username)) || ($username == ''))
{
	$username = '';
}

$character = trim($character);
$shipname = trim($shipname);
$username = trim($username);

$makepass = '';
$title = $l_new_title2;
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

if ((!isset($hdrs)) || ($hdrs == ''))
{
	$hdrs = '';
}

if ($account_creation_closed)
{
	include ("languages/$langdir/lang_new.inc");
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_new_closed_message);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

// Limit all entries to A-Z (word characters \w), 0-9 (digits \d), whitespace (\s), and single quote. Our big concern is semicolons.
// Username has to allow @ signs and periods for email. :)
if(is_valid_email_eregi($_POST['username'])){
	$username = $_POST['username'];
}else{
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_new_invalidemail);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

$shipname = preg_replace ("/[^\w\d\s\']/","",clean_words($shipname));
$character = preg_replace ("/[^\w\d\s\']/","",clean_words($character));

// Convert any html entities. Prevents html/js exploit crap.
$username = htmlspecialchars($username,ENT_QUOTES);
$shipname = htmlspecialchars(trim($shipname),ENT_QUOTES);
$character = htmlspecialchars(trim($character),ENT_QUOTES);

// Add slashes to the funky stuff, so the DB won't choke.
if (!get_magic_quotes_gpc())
{
	$username = addslashes($username);
	$shipname = addslashes($shipname);
	$character = addslashes($character);
}

if ($username == '' || $character == '' || $shipname == '' )
{ 
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_new_blank);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

$debug_query = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask or email='$username'");
db_op_result($debug_query,__LINE__,__FILE__);

if ($debug_query->RecordCount() != 0)
{
	$title = $l_login_title2;
	$smarty->assign("title", $title);
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_login_banned);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}
elseif ($server_closed)
{
	$title = $l_login_sclosed;
	$smarty->assign("title", $title);
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_login_closed_message);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

$result = $db->Execute ("SELECT email, character_name FROM $dbtables[players]");

if ($result>0)
{
	while (!$result->EOF)
	{
		$row = $result->fields;
		if (strtolower($row['email']) == strtolower($username)) 
		{ 
			$l_new_inuse = str_replace("[username]", "\"$username\"", $l_new_inuse);
			$smarty->assign("l_new_login", $l_new_login);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("error_msg", $l_new_inuse);
			$smarty->assign("error_msg2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."new2-die.tpl");
			include ("footer.php");
			die();
		}

		if (strtolower($row['character_name']) == strtolower($character)) 
		{ 
			$l_new_inusechar = str_replace("[character]", "\"$character\"", $l_new_inusechar);
			$smarty->assign("l_new_login", $l_new_login);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("error_msg", $l_new_inusechar);
			$smarty->assign("error_msg2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."new2-die.tpl");
			include ("footer.php");
			die();
		}
		elseif (metaphone($row['character_name']) == metaphone($character)) 
		{ 
			$l_new_similar_inusechar = str_replace("[character]", "\"$row[character_name]\"", $l_new_similar_inusechar);
			$smarty->assign("l_new_login", $l_new_login);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("error_msg", $l_new_similar_inusechar);
			$smarty->assign("error_msg2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."new2-die.tpl");
			include ("footer.php");
			die();
		}
		$result->MoveNext();
	}
}

if (strtolower($character) == "unknown" || strtolower($character) == "unowned" || strtolower($character) == "unchartered" || strtolower($character) == "uncharted") 
{ 
	$l_new_inusechar = str_replace("[character]", "\"$character\"", $l_new_inusechar);
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_new_inusechar);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

$result = $db->Execute ("SELECT name FROM $dbtables[ships]");

if ($result>0)
{
	while (!$result->EOF)
	{
		$row = $result->fields;
		if (strtolower($row['name']) == strtolower($shipname)) 
		{ 
			$l_new_inuseship = str_replace("[shipname]", "\"$shipname\"", $l_new_inuseship);
			$smarty->assign("l_new_login", $l_new_login);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("error_msg", $l_new_inuseship);
			$smarty->assign("error_msg2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."new2-die.tpl");
			include ("footer.php");
			die();
		}
		elseif (metaphone($row['name']) == metaphone($shipname)) 
		{ 
			$l_new_similar_inuseship = str_replace("[shipname]", "\"$row[name]\"", $l_new_similar_inuseship);
			$smarty->assign("l_new_login", $l_new_login);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("error_msg", $l_new_similar_inuseship);
			$smarty->assign("error_msg2", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."new2-die.tpl");
			include ("footer.php");
			die();
		}
		$result->MoveNext();
	}
}

if (strtolower($shipname) == "unknown" || strtolower($shipname) == "unowned" || strtolower($shipname) == "unchartered" || strtolower($shipname) == "uncharted") 
{ 
	$l_new_inuseship = str_replace("[shipname]", "\"$shipname\"", $l_new_inuseship);
	$smarty->assign("l_new_login", $l_new_login);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("error_msg", $l_new_inuseship);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."new2-die.tpl");
	include ("footer.php");
	die();
}

/* insert code to add player to database */
$syllables = "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
$syllable_array = explode(",", $syllables);
mt_srand((double)microtime()*1000000);
for ($count=1;$count<=4;$count++) 
{
	if (mt_rand()%10 == 1) 
	{
		$makepass .= sprintf("%0.0f",(mt_rand()%50)+1);
	} 
	else 
	{
		$makepass .= sprintf("%s",$syllable_array[mt_rand()%62]);
	}
}

$shipid = newplayer($username, $character, $makepass, $shipname);
$l_new_message = str_replace("[pass]", $makepass, $l_new_message);

$msg = "$l_new_message\r\n\r\nhttp://". $_SERVER['HTTP_HOST'] . "$gamepath\r\n";
$msg = ereg_replace("\r\n.\r\n","\r\n. \r\n",$msg);
$hdrs .= "From: AATRADE System Mailer <$admin_mail>\r\n";

$e_response = mail($username,$l_new_topic, $msg,$hdrs);

if ($e_response === TRUE)
{
	$smarty->assign("emailresult", "<font color=\"lime\">Email sent to $username</font>");
	AddELog($username,1,'Y',$l_new_topic,$e_response);
}	   
else
{
	$smarty->assign("emailresult", "<font color=\"Red\">Email failed to send to $username</font>");
	AddELog($username,1,'N',$l_new_topic,$e_response);
}

$smarty->assign("enable_profilesupport", $enable_profilesupport);
$smarty->assign("l_here", $l_here);
$smarty->assign("l_new_profile", $l_new_profile);
$smarty->assign("l_new_tutorial", $l_new_tutorial);
$smarty->assign("display_password", $display_password);
$smarty->assign("l_new_pwis", $l_new_pwis);
$smarty->assign("l_new_charis", $l_new_charis);
$smarty->assign("makepass", $makepass);
$smarty->assign("character", $character);
$smarty->assign("username", $username);
$smarty->assign("l_new_login", $l_new_login);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("l_new_err", $l_new_err);
$smarty->display($default_template."new2.tpl");

include ("footer.php");
?>

