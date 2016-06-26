<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: footer.php

global $db ,$dbtables, $sched_ticks, $langdir, $create_universe, $default_template, $playerinfo, $smarty, $send_now, $no_gzip;

$silent = 1;
$timeleft = '';

include ("languages/$langdir/lang_footer.inc");

// Players online
$debug_query = sql_time_since_login();
$online = $debug_query->RecordCount();

// Time left til next update
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$debug_query = $db->Execute("SELECT last_run FROM $dbtables[scheduler] WHERE sched_file='sched_turns.php'");
db_op_result($debug_query,__LINE__,__FILE__);
$row = $debug_query->fields['last_run'];

$debug_query2 = $db->UnixTimeStamp($row);
db_op_result($debug_query2,__LINE__,__FILE__);
$timeleft = $debug_query2;

$mySEC = ($sched_ticks * 60) - (TIME()-$timeleft);
if ($mySEC <= 0)
{
	$mySEC = 1;
}

if ($online == 0)
{
	$players_online = $l_footer_no_players_on;
}
else
{
	$players_online = $l_footer_players_on_2 . $online;
}

if($player_limit > 0)
{
	$players_online .= $l_footer_open_slots . $player_limit;
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}

if (isset($smarty))
{
	if(basename($_SERVER['PHP_SELF']) == "help.php")
		$currentprogram = "";
	else $currentprogram = basename($_SERVER['PHP_SELF']);

	$currentprogram = str_replace(".php", ".inc", $currentprogram);

	if($playerinfo['player_id'] != '' and isset($playerinfo['player_id'])){
		$result = $db->Execute("SELECT * FROM $dbtables[messages] WHERE recp_id='".$playerinfo['player_id']."' AND notified='N'");
		$smarty->assign("instantmessagecount", $result->RecordCount());
		$smarty->assign("l_youhave", $l_youhave);
		$smarty->assign("l_messages_wait", $l_messages_wait);
		if ($result->RecordCount() > 0)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[messages] SET notified='Y' WHERE recp_id='".$playerinfo['player_id']."'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
	$smarty->assign("currentprogram", $currentprogram);
	$smarty->assign("seconds_until_update", $mySEC);
	$smarty->assign("footer_players_online", $players_online);
	$smarty->assign("footer_until_update", $l_footer_until_update);
	$smarty->assign("footer_type", $footer_type);
	$smarty->assign("scheduler_ticks", $sched_ticks);
	$smarty->assign("l_footer_news", $l_footer_news);
	$smarty->assign("l_footer_title", $l_footer_title);
	$smarty->assign("l_here", $l_here);
	$smarty->assign("l_footer_help", $l_footer_help);
	$smarty->assign("l_footer_click", $l_footer_click);
	$lines = @file ("config/banner_bottom.inc");
	$banner_bottom = "";
	for($i=0; $i<count($lines); $i++)
		$banner_bottom .= $lines[$i];

	$smarty->assign("banner_bottom", $banner_bottom);
	$send_now = 1;
	$smarty->display($templatename."footer.tpl");
}

unset($_SESSION['currentprogram'], $currentprogram);
unset ($smarty);

close_database();
die();
exit; // To prevent pop-up windows ;)
?>
