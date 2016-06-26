<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: log.php

include ("config/config.php");
include ("languages/$langdir/lang_log.inc");
include ("languages/$langdir/lang_lrscan.inc");
include ("languages/$langdir/lang_planet.inc"); 

$title = $l_log_titlet;
$md5adminpass = md5($adminpass);

if (isset($_GET['md5swordfish']))
{
	$md5swordfish = $_GET['md5swordfish'];
}

if (isset($_POST['md5swordfish']))
{
	$md5swordfish = $_POST['md5swordfish'];
}

if ((!isset($md5swordfish)) || ($md5swordfish == ''))
{
	$md5swordfish = '';
}

if (isset($_GET['player']))
{
	$player = $_GET['player'];
}

if (isset($_POST['player']))
{
	$player = $_POST['player'];
}

if ((!isset($player)) || ($player == ''))
{
	$player = '';
}

if ((!isset($nonext)) || ($nonext == ''))
{
	$nonext = '';
}
// AATrade
if ((!isset($loglist)) || ($loglist == ''))
{
$loglist='';
}
// end
if ($md5adminpass <> $md5swordfish)
{
	if (checklogin() or $tournament_setup_access == 1)
	{
		include ("footer.php");
		die();
	}
}
else
{
	$no_gzip = 1;
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}

include ("templates/".$templatename."skin_config.inc");
include ("header.php");

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}


if ($md5swordfish == $md5adminpass) //check if called by admin script
{
	$playerinfo['player_id'] = $player;

	if ($player == 0)
	{
		$playerinfo['character_name'] = 'Administrator';
	}
	else
	{
		$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$player");
		$targetname = $res->fields;
		$playerinfo['character_name'] = $targetname['character_name'];
	}

	$loglist = 8;
}

//Recognizes only some (d, j, F, M, Y, H, i) format string components!
function simple_date($frmtstr, $full_year, $month_full, $month_short, $day, $hour, $min)
{
	$retvalue="";
	for($cntr=0; $cntr<strlen($frmtstr); $cntr++)
	{
		switch (substr($frmtstr,$cntr,1))
		{
			case "d":
				if (strlen($day)==1)
				{
					$retvalue .= "0$day";
				}
				else
				{
					$retvalue .= $day;
				}
			break;
			
			case "j":
				$retvalue .= NUMBER($day);
			break;
			
			case "F":
				$retvalue .= $month_full;
			break;
			
			case "M":
				$retvalue .= $month_short;
			break;
			
			case "Y":
				$retvalue .= $full_year;
			break;

			case "H":
				if (strlen($hour)==1)
				{
					$retvalue .= "0$hour";
				}
				else
				{
					$retvalue .= $hour;
				}
			break;
			
			case "i":
				if (strlen($min)==1)
				{
					$retvalue .= "0$min";
				}
				else
				{
					$retvalue .= $min;
				}
			break;
			
			default:
				$retvalue .= substr($frmtstr,$cntr,1);
			break;
		}
	}
	return $retvalue;
}

$smarty->assign("isadmin", ($md5adminpass == $md5swordfish));
$smarty->assign("l_log_select", $l_log_select);
$smarty->assign("startdate", $startdate.$postlink);
$smarty->assign("l_log_general", $l_log_general);
$smarty->assign("l_log_dig", $l_log_dig);
$smarty->assign("l_log_spy", $l_log_spy);
$smarty->assign("l_log_disaster", $l_log_disaster);
$smarty->assign("l_log_nova", $l_log_nova);
$smarty->assign("l_log_attack", $l_log_attack);
$smarty->assign("l_log_scan", $l_log_scan);
$smarty->assign("l_log_starv", $l_log_starv);
$smarty->assign("l_log_probe", $l_log_probe);
$smarty->assign("l_log_autotrade", $l_log_autotrade);
$smarty->assign("l_log_combined", $l_log_combined);

$logline = str_replace("[player]", "$playerinfo[character_name]", $l_log_log);

$smarty->assign("templatename", $templatename);
$smarty->assign("logline", $logline);
$smarty->assign("l_log_combined", $l_log_combined);

if($loglist == ""){
	$typelist = "";
	$logtype = $l_log_combined;
}

if($loglist == 1){
	$typelist = " and ((type>0 and type<3) or (type>91 and type<108) or type=89 or type=114) ";
	$logtype = $l_log_dig;
}

if($loglist == 2){
	$typelist = " and ((type>0 and type<3) or type>59 and type<77 or type=109) ";
	$logtype = $l_log_spy;
}

if($loglist == 3){
	$typelist = " and ((type>0 and type<3) or (type>76 and type<85) or (type>49 and type<52)) ";
	$logtype = $l_log_disaster;
}

if($loglist == 4){
	$typelist = " and ((type>0 and type<3) or (type>89 and type<92)) ";
	$logtype = $l_log_nova;
}

if($loglist == 5){
	$typelist = " and ((type>0 and type<3) or (type>2 and type<15) or (type>15 and type<18) or (type>27 and type<30) or type=46 or type=53 or type=56) ";
	$logtype = $l_log_attack;
}

if($loglist == 6){
	$typelist = " and ((type>0 and type<3) or (type>19 and type<25)) ";
	$logtype = $l_log_scan;
}

if($loglist == 7){
	$typelist = " and ((type>0 and type<3) or type=26) ";
	$logtype = $l_log_starv;
}
if($loglist == 8){
	$typelist = " and ((type>0 and type<3) or type=15 or (type>17 and type<20) or type=27 or (type>29 and type<46) or (type>46 and type<50) or type=52 or type=54 or type=55 or type=57 or type=58 or type=59) ";
	$logtype = $l_log_general;

}

if($loglist == 9){
	$typelist = " and ((type>0 and type<3) or (type>110 and type<114) or (type>299 and type<304)) ";
	$logtype = $l_log_probe;
}

if($loglist == 10){
	$typelist = " and ((type>0 and type<3) or (type=200 or type=201)) ";
	$logtype = $l_log_autotrade;
}

if (empty($startdate))
  $startdate = date("Y-m-d");

$entry = simple_date($local_logdate_med_format, substr($startdate, 0, 4), $l_log_months[substr($startdate, 5, 2) - 1], $l_log_months_short[substr($startdate, 5, 2) - 1], substr($startdate, 8, 2), 0, 0 ) ;

$smarty->assign("l_log_start", $l_log_start);
$smarty->assign("entry", $entry);
$smarty->assign("logtype", $logtype);

$res = $db->Execute("SELECT * FROM $dbtables[logs] WHERE player_id=$playerinfo[player_id] AND time LIKE '$startdate%' ".$typelist."ORDER BY time DESC, log_id DESC");
$logcount = 0;
while (!$res->EOF)
{
	$event = log_parse($res->fields);
	$time = simple_date($local_logdate_full_format, substr($res->fields['time'], 0, 4), $l_log_months[substr($res->fields['time'], 5, 2) - 1], $l_log_months_short[substr($res->fields['time'], 5, 2) - 1], substr($res->fields['time'], 8, 2), substr($res->fields['time'], 11, 2), substr($res->fields['time'], 14, 2) );

	$logtitle[$logcount] = $event['title'];
	$logbody[$logcount] = $event['text'];
	$logtime[$logcount] = $time;
	$logcount++;
	$res->MoveNext();
}

$smarty->assign("logtitle", $logtitle);
$smarty->assign("logbody", $logbody);
$smarty->assign("logtime", $logtime);
$smarty->assign("logcount", $logcount);

$smarty->assign("l_log_end", $l_log_end);
$smarty->assign("endentry", $entry);

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 1;
$year = substr($startdate, 0, 4);

$yesterday = mktime (0,0,0,$month,$day,$year);
$yesterday = date("Y-m-d", $yesterday);

$day = substr($startdate, 8, 2) - 2;

$yesterday2 = mktime (0,0,0,$month,$day,$year);
$yesterday2 = date("Y-m-d", $yesterday2);

$date1 = simple_date($local_logdate_short_format, 0, $l_log_months[substr($startdate, 5, 2) - 1], $l_log_months_short[substr($startdate, 5, 2) - 1], substr($startdate, 8, 2), 0, 0);
$date2 = simple_date($local_logdate_short_format, 0, $l_log_months[substr($yesterday, 5, 2) - 1], $l_log_months_short[substr($yesterday, 5, 2) - 1], substr($yesterday, 8, 2), 0, 0);
$date3 = simple_date($local_logdate_short_format, 0, $l_log_months[substr($yesterday2, 5, 2) - 1], $l_log_months_short[substr($yesterday2, 5, 2) - 1], substr($yesterday2, 8, 2), 0, 0);

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2) - 3;
$year = substr($startdate, 0, 4);

$backlink = mktime (0,0,0,$month,$day,$year);
$backlink = date("Y-m-d", $backlink);

$day = substr($startdate, 8, 2) + 3;

$nextlink = mktime (0,0,0,$month,$day,$year);
if ($nextlink > time())
  $nextlink = time();
$nextlink = date("Y-m-d", $nextlink);

if ($startdate == date("Y-m-d"))
  $nonext = 1;

if ($md5swordfish == $md5adminpass) //fix for admin log view
  $postlink =  "&player=$player&md5swordfish=" . urlencode($md5swordfish);
else
  $postlink = "";

$smarty->assign("loglist", $loglist);
$smarty->assign("backlink", $backlink.$postlink);
$smarty->assign("yesterday2", $yesterday2.$postlink);
$smarty->assign("yesterday", $yesterday.$postlink);
$smarty->assign("newstartdate", $startdate.$postlink);
$smarty->assign("nextlink", $nextlink.$postlink);
$smarty->assign("nonext", $nonext);
$smarty->assign("date3", $date3);
$smarty->assign("date2", $date2);
$smarty->assign("date1", $date1);
$smarty->assign("md5swordfish", $md5swordfish);
$smarty->assign("l_log_click", $l_log_click);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."log.tpl");
include ('footer.php');

function log_parse($entry)
{
  global $l_log_title;
  global $l_log_text;
  global $l_log_pod;
  global $l_log_nopod;
  global $space_plague_kills;

  $entry['data'] = stripslashes($entry['data']);

  switch($entry['type'])
  {
   case LOG_LOGIN: //data args are : [ip]
	case LOG_LOGOUT:
	case LOG_BADLOGIN:
	case LOG_HARAKIRI:
	$retvalue['text'] = str_replace("[ip]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_ATTACK_OUTMAN: //data args are : [player]
	case LOG_ATTACK_OUTSCAN:
	case LOG_ATTACK_EWD:
	case LOG_ATTACK_EWDFAIL:
	case LOG_SHIP_SCAN:
	case LOG_SHIP_SCAN_FAIL:
	case LOG_KABAL_ATTACK:
	case LOG_TEAM_NOT_LEAVE:
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_ATTACK_LOSE: //data args are : [player] [pod]
	list($name, $pod)= split ("\|", $entry['data']);

	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	if ($pod == 'Y')
	  $retvalue['text'] = $retvalue['text'] . $l_log_pod;
	else
	  $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
	break;

	case LOG_ATTACKED_WIN: //data args are : [player] [armor] [fighters]
	list($name, $armor, $fighters)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[armor]", "<font color=white><b>$armor</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_HIT_MINES: //data args are : [mines] [sector]
	list($mines, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[mines]", "<font color=white><b>$mines</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_SHIP_DESTROYED_MINES: //data args are : [sector] [pod]
	case LOG_DEFS_KABOOM:
	list($sector, $pod)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	if ($pod == 'Y')
	  $retvalue['text'] = $retvalue['text'] . $l_log_pod;
	else
	  $retvalue['text'] = $retvalue['text'] . $l_log_nopod;
	break;

	case LOG_PLANET_DEFEATED_D: //data args are :[planet_name] [sector] [name]
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// AATrade
	case LOG_PLANET_novaED_D: //data args are :[planet_name] [sector] [name]
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_SHIP_novaED_D: //data args are :[planet_name] [sector] [name]
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// end
	case LOG_PLANET_DEFEATED:
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_PLANET_SCAN:
	case LOG_PLANET_SCAN_FAIL:
	case LOG_PLANET_YOUR_CAPTURED:
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_SPY_SEND_FAIL:///
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_SPY_CPTURE_OWNER:
	case LOG_SPY_KILLED:
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// AATrade
	case LOG_DIG_KILLED_SPY:
	list($planet_name, $sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_SPY_FOUND_EMBEZZLER: //data args are : [DIG] [PLANET]
	list($dig, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[DIG]", "<font color=#00FFFF><b>$dig</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[PLANET]", "<font color=#00FF00><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

//end

	case LOG_PLANET_NOT_DEFEATED: //data args are : [planet_name] [sector] [name] [ore] [organics] [goods] [salvage] [credits]
	list($planet_name, $sector, $name, $ore, $organics, $goods, $salvage, $credits)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[ore]", "<font color=white><b>$ore</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[goods]", "<font color=white><b>$goods</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[organics]", "<font color=white><b>$organics</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[salvage]", "<font color=white><b>$salvage</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_RAW: //data is stored as a message
	$retvalue['title'] = $l_log_title[$entry['type']];
	$retvalue['text'] = $entry['data'];
	break;

	case LOG_DEFS_DESTROYED: //data args are : [quantity] [type] [sector]
	list($quantity, $type, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	
	case LOG_PLANET_EJECT: //data args are : [sector] [player]
	list($sector, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_STARVATION: //data args are : [sector] [starvation]
	list($sector, $starvation)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[starvation]", "<font color=white><b>$starvation</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_TOW: //data args are : [sector] [newsector] [hull]
	list($sector, $newsector, $hull)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[newsector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$newsector>$newsector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[hull]", "<font color=white><b>$hull</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_DEFS_DESTROYED_F: //data args are : [fighters] [sector]
	list($fighters, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$fighters</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_TEAM_REJECT: //data args are : [player] [teamname]
	list($player, $teamname)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[teamname]", "<font color=white><b>$teamname</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_TEAM_RENAME: //data args are : [team]
	case LOG_TEAM_M_RENAME:
	case LOG_TEAM_KICK:
	case LOG_TEAM_CREATE:
	case LOG_TEAM_LEAVE:
	case LOG_TEAM_LEAD:
	case LOG_TEAM_JOIN:
	case LOG_TEAM_INVITE:
	$retvalue['text'] = str_replace("[team]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_TEAM_CANCEL:
	$retvalue['text'] = str_replace("[team]", "<font color=white><b>$entry[data]</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_TEAM_NEWLEAD: //data args are : [team] [name]
	case LOG_TEAM_NEWMEMBER:
	list($team, $name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[team]", "<font color=white><b>$team</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_ADMIN_HARAKIRI: //data args are : [player] [ip]
	list($player, $ip)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[ip]", "<font color=white><b>$ip</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_ADMIN_ILLEGVALUE: //data args are : [player] [quantity] [type] [holds]
	list($player, $quantity, $type, $holds)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$player</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[quantity]", "<font color=white><b>$quantity</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[type]", "<font color=white><b>$type</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[holds]", "<font color=white><b>$holds</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_ADMIN_PLANETDEL: //data args are : [attacker] [defender] [sector]
	list($attacker, $defender, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[attacker]", "<font color=white><b>$attacker</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[defender]", "<font color=white><b>$defender</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
//AATrade
	case LOG_ADMIN_PLANETIND: //data args are : [attacker] [defender] [sector]
	list($attacker, $defender, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[attacker]", "<font color=#00FFFF><b>$attacker</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[defender]", "<font color=#FF0000><b>$defender</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=#00FF00><b>$sector</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// end
	case LOG_DEFENCE_DEGRADE: //data args are : [sector] [degrade]
	list($sector, $degrade)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[degrade]", "<font color=white><b>$degrade</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_PLANET_CAPTURED: //data args are : [cols] [credits] [owner]
	list($cols, $credits, $owner)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[cols]", "<font color=white><b>$cols</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[credits]", "<font color=white><b>$credits</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[owner]", "<font color=white><b>$owner</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
	case LOG_BOUNTY_CLAIMED:
	list($amount,$bounty_on,$placed_by) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[amount]", "<font color=white><b>".number($amount)."</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[placed_by]", "<font color=white><b>$placed_by</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
 case LOG_BOUNTY_TAX_PAID:
 case LOG_BOUNTY_PAID:
	list($amount,$bounty_on) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[amount]", "<font color=white><b>".number($amount)."</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
 case LOG_BOUNTY_CANCELLED:
	list($amount,$bounty_on) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[amount]", "<font color=white><b>".number($amount)."</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[bounty_on]", "<font color=white><b>$bounty_on</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
case LOG_BOUNTY_FEDBOUNTY:
	$retvalue['text'] = str_replace("[amount]", "<font color=white><b>".number($entry[data])."</b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
 case LOG_SPACE_PLAGUE:
	list($name,$sector) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$percentage = $space_plague_kills * 100;
	$retvalue['text'] = str_replace("[percentage]", "$percentage", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
 case LOG_PLASMA_STORM:
	list($name,$sector,$percentage) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[percentage]", "<font color=white><b>$percentage</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// AATrade
 case LOG_PLANET_REVOLT:
	list($name,$sector,$organics,$goods,$ore,$torps,$col,$credit,$fighter,$energy) = split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[credits]", "<font color=white><b>$credit</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[colonists]", "<font color=white><b>$col</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[organics]", "<font color=white><b>$organics</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[goods]", "<font color=white><b>$goods</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[ore]", "<font color=white><b>$ore</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[torps]", "<font color=white><b>$torps</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$fighter</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[energy]", "<font color=white><b>$energy</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;	
//end
 case LOG_PLANET_BOMBED:
	list($planet_name, $sector, $name, $beams, $torps, $figs)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[beams]", "<font color=white><b>$beams</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[torps]", "<font color=white><b>$torps</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[figs]", "<font color=white><b>$figs</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
 case LOG_CHEAT_TEAM: //data args are : [player] [ip]
	list($name, $ip)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[player]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[ip]", "<font color=white><b>$ip</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
///
// AATrade Dig Stuff
	case LOG_DIG_MONEY:
	case LOG_DIG_PRODUCTION: //data args are :[id] [planet_name] [sector] [data]
	case LOG_DIG_BIRTHDEC:
	case LOG_DIG_BIRTHINC:
	case LOG_DIG_SPYHUNT:
	case LOG_DIG_INTEREST:
	case LOG_DIG_TORPS:
	case LOG_DIG_FITS:
	list($id, $planet_name, $sector, $data)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[data]", "<font color=white><b>$data</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
// end
// aatrade probe stuff


case LOG_PROBE_DETECTED_SHIP:

 list($id,$sector,$ship_name)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[ship_name]", "<font color=white><b>$ship_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;
case LOG_PROBE_SCAN_SHIP:

 list($id,$sector,$ship_name,$sc_hull,$sc_engines,$sc_power,$sc_computer,$sc_sensors,$sc_beams,$sc_torp_launchers,$sc_armour,$sc_shields,$sc_cloak,$sc_armour_pts,$sc_ship_fighters,$sc_torps,$sc_credits,$sc_ship_energy,$sc_dev_minedeflector,$sc_dev_emerwarp,$sc_dev_pod)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[ship_name]", "<font color=white><b>$ship_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[hull]", "<font color=white><b>$sc_hull</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[engines]", "<font color=white><b>$sc_engines</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[power]", "<font color=white><b>$sc_power</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[computer]", "<font color=white><b>$sc_computer</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[sensors]", "<font color=white><b>$sc_sensors</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[beams]", "<font color=white><b>$sc_beams</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[torps]", "<font color=white><b>$sc_torp_launchers</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[armor]", "<font color=white><b>$sc_armour</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[shields]", "<font color=white><b>$sc_shields</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[cloak]", "<font color=white><b>$sc_cloak</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[armor_pts]", "<font color=white><b>$sc_armour_pts</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[fighters]", "<font color=white><b>$sc_ship_fighters</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[avail_torps]", "<font color=white><b>$sc_torps</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[credits]", "<font color=white><b>$sc_credits</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[energy]", "<font color=white><b>$sc_ship_energy</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[deflectors]", "<font color=white><b>$sc_dev_minedeflector</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[ewd]", "<font color=white><b>$sc_dev_emerwarp</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[pod]", "<font color=white><b>$sc_dev_pod</b></font>", $retvalue['text']);
 $retvalue['text'] = str_replace("[ecm]", "<font color=white><b>$sc_ecm</b></font>", $retvalue['text']);

	$retvalue['title'] = $l_log_title[$entry['type']];
	break;	
//end
	case LOG_SPY_SABOTAGE: //data args are :[id] [planet_name] [sector] [data]
	case LOG_SPY_BIRTH:
	case LOG_SPY_INTEREST:
	case LOG_SPY_MONEY:
// AATrade
	//case LOG_DIG_KILLED_SPY:
// end
	case LOG_SPY_TORPS:
	case LOG_SPY_FITS:
	list($id, $planet_name, $sector, $data)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[data]", "<font color=white><b>$data</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_SPY_CPTURE: //data args are :[id] [planet_name] [sector]
	case LOG_SPY_KILLED_SPYOWNER:
	case LOG_SPY_CATACLYSM:
	list($id, $planet_name, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_SHIPSPY_KILLED: //data args are :[id] [name] [shipname]
	case LOG_SHIPSPY_CATACLYSM:
	case LOG_SPY_NEWSHIP:
	list($id, $name, $shipname)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[shipname]", "<font color=white><b>$shipname</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_SPY_TOSHIP: //data args are :[id] [planet_name] [sector] [playername] [shipname]
	case LOG_SPY_TOPLANET:
	list($id, $planet_name, $sector, $playername, $shipname)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planet_name]", "<font color=white><b>$planet_name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[id]", "<font color=white><b>$id</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[playername]", "<font color=white><b>$playername</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[shipname]", "<font color=white><b>$shipname</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_IGB_TRANSFER1: //data args are : [name] [sum]
	case LOG_IGB_TRANSFER2:
	list($name, $sum)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[name]", "<font color=white><b>$name</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sum]", "<font color=white><b>$sum</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_AUTOTRADE:
	list($planetname,$sector,$totalcost,$goodsamount,$oreamount,$organicsamount,$energyamount,$goodsprice,$oreprice,$organicsprice,$energyprice)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planetname]", "<font color=white><b>$planetname</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[totalcost]", "<font color=white><b>".number($totalcost)."</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[goodsamount]", "<font color=white><b>$goodsamount</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[oreamount]", "<font color=white><b>$oreamount</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[organicsamount]", "<font color=white><b>$organicsamount</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[energyamount]", "<font color=white><b>$energyamount</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[goodsprice]", "<font color=white><b>$goodsprice</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[oreprice]", "<font color=white><b>$oreprice</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[organicsprice]", "<font color=white><b>$organicsprice</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[energyprice]", "<font color=white><b>$energyprice</b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_AUTOTRADE_ABORTED:
	list($planetname, $sector, $destsector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[planetname]", "<font color=white><b>$planetname</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b>$sector</b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[destsector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$destsector>$destsector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_PROBE_DESTROYED:
	list($sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_PROBE_NOTURNS:
	list($probe_id, $sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[probe_id]", "<font color=white><b>$probe_id</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case  LOG_PROBE_INVALIDSECTOR:
	list($target_sector)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[target_sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$target_sector>$target_sector</a></b></font>", $l_log_text[$entry['type']]);
	$retvalue['title'] = $l_log_title[$entry['type']];
	break;

	case LOG_PROBE_DETECTPROBE:
	list($probe_id, $sector, $probe_detect,$probe_type)= split ("\|", $entry['data']);
	$retvalue['text'] = str_replace("[probe_id]", "<font color=white><b>$probe_id</b></font>", $l_log_text[$entry['type']]);
	$retvalue['text'] = str_replace("[sector]", "<font color=white><b><a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a></b></font>", $retvalue['text']);
	$retvalue['text'] = str_replace("[probe_detect]", "<br><font color=white><b>$probe_detect</b></font>", $retvalue['text']);
	$probe_type2 = "";
	if($probe_type == "Warp")
		$probe_type2 = "<br><font color='#87d8ec'><b>$probe_type</b></font>";
	
	if($probe_type == "Real Space")
		$probe_type2 = "<br><font color='#79f487'><b>$probe_type</b></font>";
	
	$retvalue['title'] = str_replace("[probe_type]", $probe_type2, $l_log_title[$entry['type']]);
	break;

  }
  return $retvalue;
}

close_database();
?>

