<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: spy.php

include ("config/config.php");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_bounty.inc");

$title = $l_spy_title;

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

if (!$spy_success_factor) {
	$smarty->assign("title", $title);
	$smarty->assign("error_msg", $l_spy_disabled);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."spy-die.tpl");
	include ("footer.php");
	die();
}

if (checklogin() or $tournament_setup_access == 1) {
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])) {
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

if ((!isset($command)) || ($command == '')) {
	$command = '';
}

if ((!isset($by)) || ($by == '')) {
	$by = '';
}

if ((!isset($by1)) || ($by1 == '')) {
	$by1 = '';
}

if ((!isset($by2)) || ($by2 == '')) {
	$by2 = '';
}

if ((!isset($by3)) || ($by3 == '')) {
	$by3 = '';
}

if ((!isset($planet_id)) || ($planet_id == '')) {
	$planet_id = '-1';
}

if ((!isset($spy_id)) || ($spy_id == '')) {
	$spy_id = '-1';
}

if ((!isset($dismiss)) || ($dismiss == '')) {
	$dismiss = '';
}

$smarty->assign("command", $command);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_spy_menu", $l_spy_menu);
$smarty->assign("l_clickme", $l_clickme);

$spy_cleanup_ship_turns[1] = $spy_cleanup_ship_turns1;
$spy_cleanup_ship_turns[2] = $spy_cleanup_ship_turns2;
$spy_cleanup_ship_turns[3] = $spy_cleanup_ship_turns3;

$spy_cleanup_planet_turns[1] = $spy_cleanup_planet_turns1;
$spy_cleanup_planet_turns[2] = $spy_cleanup_planet_turns2;
$spy_cleanup_planet_turns[3] = $spy_cleanup_planet_turns3;

$spy_cleanup_planet_credits[1] = $spy_cleanup_planet_credits1;
$spy_cleanup_planet_credits[2] = $spy_cleanup_planet_credits2;
$spy_cleanup_planet_credits[3] = $spy_cleanup_planet_credits3;

switch ($command)
{
	case "send":	 //SENDING spy to enemy planet
	$res3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
	$planetinfo = $res3->fields;
//AATrade
	if ($planetinfo['owner'] == 2 or $planetinfo['owner'] == 3)
	{
		$smarty->assign("error_msg", $l_spy_cantsendfeds);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}

	if ($planetinfo['team'] != 0)
	{
		if($planetinfo['owner'] != $playerinfo['player_id'])
		{
			if($planetinfo['team'] == $playerinfo['team'])
			{
				$smarty->assign("error_msg", $l_spy_cantsendteam);
				$smarty->assign("error_msg2", "");
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."spy-die.tpl");
				include ("footer.php");
				die();
			}
		}
	}

//end
	if ($playerinfo['turns'] < 1) {
		$smarty->assign("error_msg", $l_spy_noturn);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}
  
	$res2 = $db->SelectLimit("SELECT spy_id FROM $dbtables[spies] WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id]",1);// AND active = 'N'
	$result = $res2->RecordCount();
	if (!$result) {
		$smarty->assign("error_msg", $l_spy_notonboard);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	} else {
		$spyinfo = $res2->fields['spy_id'];
	}

//	$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
	$base_factor = 0;
	$planetinfo['sensors'] += $base_factor;

	$res = $db->Execute("SELECT max(sensors) as maxsensors FROM $dbtables[ships] WHERE planet_id=$planet_id AND on_planet='Y'");
	if ($planetinfo['sensors'] < $res->fields['maxsensors']) {
		$planetinfo['sensors'] = $res->fields['maxsensors'];
	}
  
	if ($shipinfo['sector_id'] != $planetinfo['sector_id']) {
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}

	if ($planetinfo['owner'] == $playerinfo['player_id']) {
		$smarty->assign("error_msg", $l_spy_ownplanet);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}
	elseif ($planetinfo['owner'] == 0)
	{
		$smarty->assign("error_msg", $l_spy_unownedplanet);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}
  
	$res5 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$planet_id AND owner_id=$playerinfo[player_id]");
	$num_spies = $res5->RecordCount();
	if ($num_spies >= $max_spies_per_planet) {
		$l_spy_planetfull = str_replace("[max]", $max_spies_per_planet, $l_spy_planetfull);
		$smarty->assign("error_msg", $l_spy_planetfull);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}

	$smarty->assign("executecommand", empty($doit));
	if (empty($doit)) {
		$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
		$ownerinfo = $result3->fields;

		$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 0);

		if($isfedbounty > 0)
		{
			$smarty->assign("bountystatus", $l_by_fedbountyspy);
		}
		else
		{
			$smarty->assign("bountystatus", $l_by_nofedbountyspy);
		}

		$l_spy_sendtitle = str_replace("[spyID]", "$spyinfo", $l_spy_sendtitle);
		$smarty->assign("l_spy_sendtitle", $l_spy_sendtitle);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_spy_type1", $l_spy_type1);
		$smarty->assign("l_spy_type2", $l_spy_type2);
		$smarty->assign("l_spy_type3", $l_spy_type3);
		$smarty->assign("l_spy_trytitle", $l_spy_trytitle);
		$smarty->assign("l_spy_try_sabot", $l_spy_try_sabot);
		$smarty->assign("l_spy_try_inter", $l_spy_try_inter);
		$smarty->assign("l_spy_try_birth", $l_spy_try_birth);
		$smarty->assign("l_spy_try_steal", $l_spy_try_steal);
		$smarty->assign("l_spy_try_torps", $l_spy_try_torps);
		$smarty->assign("l_spy_try_fits", $l_spy_try_fits);
		$smarty->assign("allow_spy_capture_planets", $allow_spy_capture_planets);
		$smarty->assign("l_spy_try_capture", $l_spy_try_capture);
		$smarty->assign("l_spy_sendbutton", $l_spy_sendbutton);
	} else {
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

		$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
		$ownerinfo = $result3->fields;

		$smarty->assign("playerbounty", "");

		$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 1);

		if($isfedbounty > 0)
		{
			echo $l_by_fedbounty2 . "<BR><BR>";
		}

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id] ");
		db_op_result($debug_query,__LINE__,__FILE__);

		$success = 10 + $shipinfo['cloak'] - $planetinfo['sensors'];
		if ($success > 0)
		{
			$success = $success * 5;
		}

		// Here we subtract 4% for every spy the planet owner has on the planet from the success score.
		$res66 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$planet_id AND owner_id=$planetinfo[owner]");
		$num_spies = $res66->RecordCount();
		$success = $success - ($num_spies * 4);
	 
		// Here we add 4% for every spy the spy owner has on the planet to the success score.
		$res77 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$planet_id AND owner_id=$playerinfo[player_id]");
		$num_own_spies = $res77->RecordCount();
		$success = $success + ($num_own_spies * 4);
	 
		if ($success<5)
		{
			$success=5;
		}

		if ($success>99)
		{
			$success=99;
		}

		$roll = mt_rand(1,100);

		if ($roll<$success) {
			$try_sabot   = isset($_POST['try_sabot'])   ? "Y" : "N";
			$try_inter   = isset($_POST['try_inter'])   ? "Y" : "N";
			$try_birth   = isset($_POST['try_birth'])   ? "Y" : "N";
			$try_steal   = isset($_POST['try_steal'])   ? "Y" : "N";
			$try_torps   = isset($_POST['try_torps'])   ? "Y" : "N";
			$try_fits	= isset($_POST['try_fits'])	? "Y" : "N";
			$try_capture = isset($_POST['try_capture']) ? "Y" : "N";
			
			if (empty($mode) || ($mode!="toship" && $mode!="toplanet" && $mode!="none")) {
				$mode = "toship";
			}

			$debug_query = $db->Execute("UPDATE $dbtables[spies] SET active='Y', planet_id='$planet_id', ship_id='0', spy_percent='0.0', job_id='0', move_type='$mode', try_sabot='$try_sabot', try_inter='$try_inter', try_birth='$try_birth', try_steal='$try_steal', try_torps='$try_torps', try_fits='$try_fits', try_capture='$try_capture', spy_cloak=$shipinfo[cloak] WHERE spy_id='$spyinfo' ");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("sendstatus", $l_spy_sendsuccessful);
		} else {
			$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE spy_id=$spyinfo ");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("sendstatus", $l_spy_sendfailed);
			if (!$planetinfo['name']) 
			{
				$planetinfo['name'] = $l_unnamed;
			}
			playerlog($planetinfo['owner'], LOG_SPY_SEND_FAIL, "$planetinfo[name]|$planetinfo[sector_id]|$playerinfo[character_name]");
		}
	}   
	$smarty->assign("planet_id", $planet_id);
	$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
	break;

case "comeback";   //GETTING your spy back from enemy planet

	if ($playerinfo['turns'] < 1) {
		$smarty->assign("error_msg", $l_spy_noturn2);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}
  
	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
	$planetinfo = $res->fields;
	if ($shipinfo['sector_id'] != $planetinfo['sector_id']) {
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
	}
  
	$res = $db->Execute("SELECT * FROM $dbtables[spies] WHERE owner_id = $playerinfo[player_id] AND spy_id = $spy_id  AND active = 'Y' AND planet_id = $planetinfo[planet_id]");
	$smarty->assign("planetspies", $res->RecordCount());
	if ($res->RecordCount()) {
		$smarty->assign("executecommand", empty($doit));
		if (empty($doit))
		{
			$spy = $res->fields;
			$l_spy_confirm = str_replace("[spyID]", "$spy[spy_id]", $l_spy_confirm);
			$smarty->assign("l_spy_codenumber", $l_spy_codenumber);
			$smarty->assign("l_spy_job", $l_spy_job);
			$smarty->assign("l_spy_percent", $l_spy_percent);
			$smarty->assign("l_spy_move", $l_spy_move);
			$smarty->assign("l_spy_action", $l_spy_action);

			if ($spy['job_id'] == 0) {
				$job = "$l_spy_jobs[0]";
			} else {
				$temp = $spy['job_id'];
				$job = "<a href=spy.php?command=change&spy_id=$spy[spy_id]&planet_id=$planet_id>$l_spy_jobs[$temp]</a>";
			}
	  
			$move = $l_spy_moves[$spy['move_type']];

			if ($spy['spy_percent'] == 0) {
				$spy['spy_percent'] = "-";
			} else {
				$spy['spy_percent'] = NUMBER(100*$spy['spy_percent'],5);
			}

			$smarty->assign("spyid", $spy['spy_id']);
			$smarty->assign("job", $job);
			$smarty->assign("spy_percent", $spy['spy_percent']);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("move", $move);
			$smarty->assign("l_yes", $l_yes);
			$smarty->assign("l_no", $l_no);
		} else {
			$debug_query = $db->Execute("UPDATE $dbtables[spies] SET planet_id='0', job_id='0', spy_percent='0.0', ship_id='$shipinfo[ship_id]', active='N', try_sabot='Y', try_inter='Y', try_birth='Y', try_steal='Y', try_torps='Y', try_fits='Y', try_capture='Y' WHERE spy_id=$spy_id ");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id] ");
			db_op_result($debug_query,__LINE__,__FILE__);

			$smarty->assign("l_spy_backonship", $l_spy_backonship);
		}
	} else {
		$smarty->assign("l_spy_backfailed", $l_spy_backfailed);
	}
	
	$smarty->assign("planet_id", $planet_id);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
break;

case "change":   //CHANGING your spy settings on enemy planet

	$res = $db->Execute("SELECT * FROM $dbtables[spies] WHERE owner_id = '$playerinfo[player_id]' AND spy_id = '$spy_id'");
	$spy = $res->fields;

	$smarty->assign("spycount", $res->RecordCount());
	if ($res->RecordCount()) {
		$smarty->assign("executecommand", empty($doit));
		if (empty($doit)) {
		  $try_sabot   = ($spy['try_sabot'] == 'Y')   ? "CHECKED" : "";
		  $try_inter   = ($spy['try_inter'] == 'Y')   ? "CHECKED" : "";
		  $try_birth   = ($spy['try_birth'] == 'Y')   ? "CHECKED" : "";
		  $try_steal   = ($spy['try_steal'] == 'Y')   ? "CHECKED" : "";
		  $try_torps   = ($spy['try_torps'] == 'Y')   ? "CHECKED" : "";
		  $try_fits	= ($spy['try_fits'] == 'Y')	? "CHECKED" : "";
		  $try_capture = ($spy['try_capture'] == 'Y') ? "CHECKED" : "";
	  
		  if ($spy['move_type'] == 'none')	   { $set_1 = 'CHECKED';   $set_2 = '';   $set_3 = ''; }
		  elseif ($spy['move_type'] == 'toship') { $set_1 = '';   $set_2 = 'CHECKED';   $set_3 = ''; }
		  else								  { $set_1 = '';   $set_2 = '';   $set_3 = 'CHECKED'; }
	  
		  if ($spy['planet_id'] == '0') { $set_1 .= " DISABLED"; }

		  $l_spy_changetitle = str_replace("[spyID]", "$spy_id", $l_spy_changetitle);
			$smarty->assign("l_spy_changetitle", $l_spy_changetitle);
			$smarty->assign("spy_id", $spy_id);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("set_1", $set_1);
			$smarty->assign("l_spy_type1", $l_spy_type1);
			$smarty->assign("set_2", $set_2);
			$smarty->assign("l_spy_type2", $l_spy_type2);
			$smarty->assign("set_3", $set_3);
			$smarty->assign("l_spy_type3", $l_spy_type3);

			$smarty->assign("jobid", $spy['job_id']);
		  if ($spy['job_id']>0) {
				$temp = $spy['job_id'];
				$job = $l_spy_jobs[$temp];

				$temp = NUMBER(100*$spy['spy_percent'],5);
				$l_spy_occupied = str_replace("[spyID]", "$spy_id", $l_spy_occupied);
				$l_spy_occupied = str_replace("[job]", $job, $l_spy_occupied);
				$l_spy_occupied = str_replace("[percent]", $temp, $l_spy_occupied);
				$smarty->assign("l_spy_occupied", $l_spy_occupied);
				$smarty->assign("l_spy_dismiss", $l_spy_dismiss);
		  }

			$smarty->assign("l_spy_trytitle", $l_spy_trytitle);
			$smarty->assign("try_sabot", $try_sabot);
			$smarty->assign("l_spy_try_sabot", $l_spy_try_sabot);
			$smarty->assign("try_inter", $try_inter);
			$smarty->assign("l_spy_try_inter", $l_spy_try_inter);
			$smarty->assign("try_birth", $try_birth);
			$smarty->assign("l_spy_try_birth", $l_spy_try_birth);
			$smarty->assign("try_steal", $try_steal);
			$smarty->assign("l_spy_try_steal", $l_spy_try_steal);
			$smarty->assign("try_torps", $try_torps);
			$smarty->assign("l_spy_try_torps", $l_spy_try_torps);
			$smarty->assign("try_fits", $try_fits);
			$smarty->assign("l_spy_try_fits", $l_spy_try_fits);
			$smarty->assign("allow_spy_capture_planets", $allow_spy_capture_planets);
		  if ($allow_spy_capture_planets) {
			$smarty->assign("try_capture", $try_capture);
			$smarty->assign("l_spy_try_capture", $l_spy_try_capture);
		  }

			$smarty->assign("l_spy_changebutton", $l_spy_changebutton);
		  if ($planet_id == -1) { // Not called from Planet Menu
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_spy_linkback", $l_spy_linkback);
			} else {
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
			}
		} else {
		  $try_sabot   = isset($_POST['try_sabot'])   ? "Y" : "N";
		  $try_inter   = isset($_POST['try_inter'])   ? "Y" : "N";
		  $try_birth   = isset($_POST['try_birth'])   ? "Y" : "N";
		  $try_steal   = isset($_POST['try_steal'])   ? "Y" : "N";
		  $try_torps   = isset($_POST['try_torps'])   ? "Y" : "N";
		  $try_fits	= isset($_POST['try_fits'])	? "Y" : "N";
		  $try_capture = isset($_POST['try_capture']) ? "Y" : "N";

		  if ($mode!="toship" && $mode!="toplanet" && $mode!="none")
			$mode = "toship";
		  if ($spy['planet_id']=='0' && $mode == "none")
			$mode = "toship";

		  if ($dismiss) {
				$debug_query = $db->Execute("UPDATE $dbtables[spies] SET move_type='$mode', job_id='0', spy_percent='0.0', try_sabot='$try_sabot', try_inter='$try_inter', try_birth='$try_birth', try_steal='$try_steal', try_torps='$try_torps', try_fits='$try_fits', try_capture='$try_capture' WHERE spy_id=$spy_id ");
				db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("spystatus", $l_spy_changed2);
		  } else {
				$debug_query = $db->Execute("UPDATE $dbtables[spies] SET move_type='$mode', try_sabot='$try_sabot', try_inter='$try_inter', try_birth='$try_birth', try_steal='$try_steal', try_torps='$try_torps', try_fits='$try_fits', try_capture='$try_capture' WHERE spy_id=$spy_id ");
				db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("spystatus", $l_spy_changed2);
		  }
	  
			$smarty->assign("planet_id", $planet_id);
		  if ($planet_id == -1) { // Not called from Planet Menu
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_spy_linkback", $l_spy_linkback);
			} else {
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
			}
		}
  } else {
	$smarty->assign("l_spy_changefailed", $l_spy_changefailed);
  }
break;

case "cleanup_planet":   // TRYING to find enemy spies on my planet

	$smarty->assign("l_spy_cleanupplanettitle", $l_spy_cleanupplanettitle);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id='$planet_id' ");
	$planetinfo=$res->fields;
	
	if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
  {
		$smarty->assign("error_msg", $l_spy_cleanupplanettitle);
		$smarty->assign("error_msg2", $l_planet_none);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
  }
	
	if ($playerinfo['player_id'] != $planetinfo['owner'])
  {
		$smarty->assign("error_msg", $l_spy_cleanupplanettitle);
		$smarty->assign("error_msg2", $l_spy_notyourplanet);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
  }
  
if($zoneinfo['zone_id'] != 3){
	$alliancefactor = 1;
}

  for($a=1; $a<=3; $a++)
  {
	$spy_cleanup_planet_credits[$a]=calc_planet_cleanup_cost($planetinfo['colonists'],$a) * $alliancefactor; 
	$l_spy_cleanuptext[$a]=str_replace("[creds]", NUMBER($spy_cleanup_planet_credits[$a]), $l_spy_cleanuptext[$a]);
	$l_spy_cleanuptext[$a]=str_replace("[turns]", NUMBER($spy_cleanup_planet_turns[$a]), $l_spy_cleanuptext[$a]);
  }

  if ($planetinfo['credits'] < $spy_cleanup_planet_credits[1] || $playerinfo['turns'] < $spy_cleanup_planet_turns[1])
	$set[1] = "DISABLED";
  else
	$set[1] = "CHECKED";
 
  if ($planetinfo['credits'] < $spy_cleanup_planet_credits[2] || $playerinfo['turns'] < $spy_cleanup_planet_turns[2])
	$set[2] = "DISABLED";
  elseif ($set[1] == "CHECKED")
	$set[2] = "";
  else  
	$set[2] = "CHECKED";

  if ($planetinfo['credits'] < $spy_cleanup_planet_credits[3] || $playerinfo['turns'] < $spy_cleanup_planet_turns[3])
	$set[3] = "DISABLED";
  elseif ($set[1] == "CHECKED" || $set[2] == "CHECKED")
	$set[3] = "";
  else
	$set[3] = "CHECKED";

	$smarty->assign("executecommand", empty($doit));
  if (empty($doit))
  { 
	$smarty->assign("planet_id", $planet_id);
	$smarty->assign("set1", $set[1]);
	$smarty->assign("l_spy_cleanuptext1", $l_spy_cleanuptext[1]);
	$smarty->assign("set2", $set[2]);
	$smarty->assign("l_spy_cleanuptext2", $l_spy_cleanuptext[2]);
	$smarty->assign("set3", $set[3]);
	$smarty->assign("l_spy_cleanuptext3", $l_spy_cleanuptext[3]);
	$smarty->assign("disabled", ($set[1] == "DISABLED" && $set[2] == "DISABLED" && $set[3] == "DISABLED"));
	
	if ($set[1] == "DISABLED" && $set[2] == "DISABLED" && $set[3] == "DISABLED")
	{
	  $l_spy_cannotcleanupplanet = str_replace("[credits]" , NUMBER($planetinfo['credits']), $l_spy_cannotcleanupplanet);
	  $l_spy_cannotcleanupplanet = str_replace("[turns]" , NUMBER($playerinfo['turns']), $l_spy_cannotcleanupplanet);
		$smarty->assign("cleanupstatus", $l_spy_cannotcleanupplanet);
	}
	else
	{
		$smarty->assign("cleanupstatus", $l_spy_cleanupbutton1);
	}
  }
  else
  {
	$smarty->assign("l_spy_cleanupplanettitle2", $l_spy_cleanupplanettitle2);
	if ($type != 1 && $type != 2 && $type != 3)
	  $type = 1;

	$smarty->assign("disabled", $set[$type]);
	if ($set[$type] != "DISABLED") 
	{  
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

	  $found=0;
	  $debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+$spy_cleanup_planet_turns[$type], turns=turns-$spy_cleanup_planet_turns[$type] WHERE player_id=$playerinfo[player_id] ");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits-$spy_cleanup_planet_credits[$type] WHERE planet_id=$planet_id ");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $res = $db->Execute("SELECT max(sensors) AS maxsensors FROM $dbtables[ships] WHERE planet_id=$planet_id AND on_planet='Y'");
	  if (!$res->EOF)
		if ($shipinfo['sensors'] < $res->fields['maxsensors'])
		  $shipinfo['sensors'] = $res->fields['maxsensors'];

		$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id='$planet_id' ");
		$planetinfo=$res->fields;

	  $res = $db->Execute("SELECT * FROM $dbtables[spies] WHERE active='Y' AND ship_id='0' and planet_id='$planet_id'");
		$spycount = 0;
	  while (!$res->EOF)
	  {
		$info = $res->fields;
		
		$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
		$planetinfo['sensors'] += $base_factor;

		if ($planetinfo['sensors'] < $shipinfo['sensors'])
		  $planetinfo['sensors'] = $shipinfo['sensors'];

		$spycloak = $info['spy_cloak'];

		if ($type==1)
		{
		  $success = (5 + $planetinfo['sensors'] - $spycloak) * 5;
		  if ($success<10)  $success=10;
		  if ($success>60)  $success=60;
		}
		elseif ($type==2)
		{
		  $success = (11 + $planetinfo['sensors'] - $spycloak) * 5;
		  if ($success<25)  $success=25;
		  if ($success>77)  $success=77;
		}
		else
		{
		  $success = (14 + 1.1 * $planetinfo['sensors'] - $spycloak) * 5;
		  if ($success<40)  $success=40;
		  if ($success>95)  $success=95;
		}
		$roll = mt_rand(1,100);
		if ($roll<$success)
		{
		  $found = 1;
		$res2 = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$info[owner_id]");
		db_op_result($res2,__LINE__,__FILE__);
		$character_name = $res2->fields['character_name'];
		  $l_spy_spyfoundonplanet2 = str_replace("[player]", "<B>$character_name</B>", $l_spy_spyfoundonplanet);
		  $l_spy_spyfoundonplanet2 = str_replace("[spyID]", "<B>$info[spy_id]</B>", $l_spy_spyfoundonplanet2);
		  $spyinfo[$spycount] = $l_spy_spyfoundonplanet2;
		  $spycount++;
		  if (!$planetinfo['name']) $planetinfo['name'] = $l_unnamed;
		  $res2 = $db->Execute("DELETE FROM $dbtables[spies] WHERE spy_id=$info[spy_id]");
		  playerlog($info['owner_id'], LOG_SPY_KILLED_SPYOWNER, "$info[spy_id]|$planetinfo[name]|$planetinfo[sector_id]");
		}
		$res->MoveNext();
	  }

		$smarty->assign("spycount", $spycount);
		$smarty->assign("spyinfo", $spyinfo);
		$smarty->assign("found", $found);
	  if (!$found)
	  {
		$smarty->assign("l_spy_spynotfoundonplanet", $l_spy_spynotfoundonplanet);
	  }
	}  
	else
	{
		$smarty->assign("l_spy_notenough", $l_spy_notenough);
	  }
  }
	$smarty->assign("planet_id", $planet_id);
	$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
break;

case "cleanup_ship":   // TRYING to find enemy spies on my ship

	$smarty->assign("l_spy_cleanupshiptitle", $l_spy_cleanupshiptitle);

  if ($sectorinfo['port_type']!="devices")
  {
		$smarty->assign("error_msg", $l_spy_cleanupshiptitle);
		$smarty->assign("error_msg2", $l_spy_notinspecial);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die.tpl");
		include ("footer.php");
		die();
  }

  $level_avg = $shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['computer'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['armour'];
  $level_avg /=7;
  
if($zoneinfo['zone_id'] != 3){
	$alliancefactor = 1;
}

  for($a=1; $a<=3; $a++)
  {
	$spy_cleanup_ship_credits[$a]=calc_ship_cleanup_cost($level_avg,$a) * $alliancefactor;
	$l_spy_cleanupshiptext[$a]=str_replace("[creds]", NUMBER($spy_cleanup_ship_credits[$a]), $l_spy_cleanupshiptext[$a]);
	$l_spy_cleanupshiptext[$a]=str_replace("[turns]", NUMBER($spy_cleanup_ship_turns[$a]), $l_spy_cleanupshiptext[$a]);
  }

  if ($playerinfo['credits'] < $spy_cleanup_ship_credits[1] || $playerinfo['turns'] < $spy_cleanup_ship_turns[1])
	$set[1] = "DISABLED";
  else
	$set[1] = "CHECKED";
 
  if ($playerinfo['credits'] < $spy_cleanup_ship_credits[2] || $playerinfo['turns'] < $spy_cleanup_ship_turns[2])
	$set[2] = "DISABLED";
  elseif ($set[1] == "CHECKED")
	$set[2] = "";
  else  
	$set[2] = "CHECKED";

  if ($playerinfo['credits'] < $spy_cleanup_ship_credits[3] || $playerinfo['turns'] < $spy_cleanup_ship_turns[3])
	$set[3] = "DISABLED";
  elseif ($set[1] == "CHECKED" || $set[2] == "CHECKED")
	$set[3] = "";
  else
	$set[3] = "CHECKED";

	$smarty->assign("executecommand", empty($doit));
  if (empty($doit))
  { 
	$smarty->assign("planet_id", $planet_id);
	$smarty->assign("set1", $set[1]);
	$smarty->assign("l_spy_cleanupshiptext1", $l_spy_cleanupshiptext[1]);
	$smarty->assign("set2", $set[2]);
	$smarty->assign("l_spy_cleanupshiptext2", $l_spy_cleanupshiptext[2]);
	$smarty->assign("set3", $set[3]);
	$smarty->assign("l_spy_cleanupshiptext3", $l_spy_cleanupshiptext[3]);
	$smarty->assign("disabled", ($set[1] == "DISABLED" && $set[2] == "DISABLED" && $set[3] == "DISABLED"));

	
	if ($set[1] == "DISABLED" && $set[2] == "DISABLED" && $set[3] == "DISABLED")
	{
	  $l_spy_cannotcleanupship = str_replace("[credits]" , NUMBER($playerinfo['credits']), $l_spy_cannotcleanupship);
	  $l_spy_cannotcleanupship = str_replace("[turns]" , NUMBER($playerinfo['turns']), $l_spy_cannotcleanupship);
		$smarty->assign("cleanupstatus", $l_spy_cannotcleanupship);
	}
	else
	{
		$smarty->assign("cleanupstatus", $l_spy_cleanupbutton2);
	}
  }
  else
  {
		$smarty->assign("l_spy_cleanupshiptitle2", $l_spy_cleanupshiptitle2);
	if ($type != 1 && $type != 2 && $type != 3)
	  $type = 1;

	$smarty->assign("disabled", $set[$type]);
	if ($set[$type] != "DISABLED") 
	{  
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

	  $found=0;
	  $debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+$spy_cleanup_ship_turns[$type], turns=turns-$spy_cleanup_ship_turns[$type], credits=credits-$spy_cleanup_ship_credits[$type] WHERE player_id=$playerinfo[player_id] ");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[ships].cloak, $dbtables[players].character_name FROM $dbtables[ships] INNER JOIN $dbtables[players] ON $dbtables[ships].player_id = $dbtables[players].player_id INNER JOIN $dbtables[spies] ON $dbtables[players].player_id=$dbtables[spies].owner_id WHERE $dbtables[spies].ship_id=$shipinfo[ship_id] AND $dbtables[spies].active='Y' AND $dbtables[spies].planet_id='0'");
		$spycount = 0;
	  while (!$res->EOF)
	  {
		$info = $res->fields;
		if ($type==1)
		{
		  $success = (5 + $shipinfo['sensors'] - $info['spy_cloak']) * 5;
		  if ($success<10)  $success=10;
		  if ($success>60)  $success=60;
		}
		elseif ($type==2)
		{
		  $success = (11 + $shipinfo['sensors'] - $info['spy_cloak']) * 5;
		  if ($success<25)  $success=25;
		  if ($success>77)  $success=77;
		}
		else
		{
		  $success = (14 + 1.1 * $shipinfo['sensors'] - $info['spy_cloak']) * 5;
		  if ($success<40)  $success=40;
		  if ($success>95)  $success=95;
		}
		$roll = mt_rand(1,100);
		if ($roll<$success)
		{
		  $found = 1;
		  $l_spy_spyfoundonship2 = str_replace("[player]", "<B>$info[character_name]</B>", $l_spy_spyfoundonship);
		  $l_spy_spyfoundonship2 = str_replace("[spyID]", "<B>$info[spy_id]</B>", $l_spy_spyfoundonship2);
		  $spyinfo[$spycount] = $l_spy_spyfoundonship2;
			$spycount++;
		  $res2 = $db->Execute("DELETE FROM $dbtables[spies] WHERE spy_id=$info[spy_id]");
		  playerlog($info['owner_id'], LOG_SHIPSPY_KILLED, "$info[spy_id]|$playerinfo[character_name]|$shipinfo[name]");
		}
		$res->MoveNext();
	  }

		$smarty->assign("spycount", $spycount);
		$smarty->assign("spyinfo", $spyinfo);
		$smarty->assign("found", $found);
	  if (!$found)
	  {
		$smarty->assign("l_spy_spynotfoundonship", $l_spy_spynotfoundonship);
	  }
	}
	else
	{
		$smarty->assign("l_spy_notenough", $l_spy_notenough);
	}
  }
break;

case "detect":   //DETECTED data

  if ($by=="time")	  $by2="det_type asc, det_time desc";
  elseif ($by=="time")  $by2="data asc, det_time desc";
  else				 $by2="det_time desc";
  
  $res = $db->Execute("SELECT * FROM $dbtables[detect] WHERE $dbtables[detect].owner_id=$playerinfo[player_id] ORDER BY $by2");
  if (!$res->RecordCount()) {
		$smarty->assign("error_msg", $l_spy_noinfo);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_spy_linkback", $l_spy_linkback);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."spy-die2.tpl");
		include ("footer.php");
		die();
  }

	$smarty->assign("isinfoid", empty($info_id));
  if (!empty($info_id)) {
		$res2 = $db->Execute("SELECT * FROM $dbtables[detect] WHERE $dbtables[detect].owner_id=$playerinfo[player_id] AND det_id='$info_id'");
		$smarty->assign("infocount", $res->RecordCount());
		if ($res->RecordCount()) {
			$smarty->assign("l_spy_infodeleted", $l_spy_infodeleted);
		  $res2 = $db->Execute("DELETE FROM $dbtables[detect] WHERE det_id='$info_id'");
		} else {
			$smarty->assign("l_spy_infonotyours", $l_spy_infonotyours);
		}
  }

	$smarty->assign("isinfoidall", empty($info_id_all));
  if (!empty($info_id_all)) {
		$res2 = $db->Execute("DELETE FROM $dbtables[detect] WHERE $dbtables[detect].owner_id=$playerinfo[player_id]");
		$smarty->assign("l_spy_messagesdeleted", $l_spy_messagesdeleted);
  }

	$smarty->assign("l_spy_infotitle", $l_spy_infotitle);
	$smarty->assign("l_spy_time", $l_spy_time);
	$smarty->assign("l_spy_type", $l_spy_type);
	$smarty->assign("l_spy_info", $l_spy_info);
	$smarty->assign("l_spy_deleteall", $l_spy_deleteall);

	$detectcount = 0;
  while (!$res->EOF) {
		$info = $res->fields;

		switch($info['det_type']) {
		  case 0:
				list($sector, $owner, $planet)= split ("\|", $info['data']);
				$planet = stripslashes($planet);
				$l_spy_datatextF = str_replace("[sector]", "<a href=move.php?move_method=real&engage=1&destination=$sector>$sector</a>", $l_spy_datatext[1]);
				$l_spy_datatextF = str_replace("[player]", "<font color=white><b>$owner</b></font>", $l_spy_datatextF);
				$l_spy_datatextF = str_replace("[planet]", "<font color=white><b>$planet</b></font>", $l_spy_datatextF);
				$data=$l_spy_datatextF;
				$data_type=$l_spy_datatype[1];
			  break;
	  
			case 1:
				list($inf, $sender, $receiver,$type)= split ("\>", $info['data']);	// I use that symbol, because a letter may include '|' symbols, but cannot include '>' symbols
				if ($type == 'alliance') {
				  $l_spy_datatextF = str_replace("[sender]", "<font color=white><b>$sender</b></font>", $l_spy_datatext[2]);
				  $l_spy_datatextF = str_replace("[receiver]", "<font color=white><b>$receiver</b></font>", $l_spy_datatextF);
				  $l_spy_datatextF = str_replace("[letter]", "<font color=white><b>$inf</b></font>", $l_spy_datatextF);
				  $data=$l_spy_datatextF;
				} else {
				  $l_spy_datatextF = str_replace("[sender]", "<font color=white><b>$sender</b></font>", $l_spy_datatext[3]);
				  $l_spy_datatextF = str_replace("[receiver]", "<font color=white><b>$receiver</b></font>", $l_spy_datatextF);
				  $l_spy_datatextF = str_replace("[letter]", "<font color=white><b>$inf</b></font>", $l_spy_datatextF);
				  $data=$l_spy_datatextF;
				}
		
				$data_type=$l_spy_datatype[2];
			  break;
		}

		$det_time[$detectcount] = $info['det_time'];
		$datatype[$detectcount] = $data_type;
		$datainfo[$detectcount] = $data;
		$det_id[$detectcount] = $info['det_id'];
		$detectcount++;
		$res->MoveNext();
  }

	$smarty->assign("detectcount", $detectcount);
	$smarty->assign("l_spy_delete", $l_spy_delete);
	$smarty->assign("by", $by);
	$smarty->assign("det_time", $det_time);
	$smarty->assign("datatype", $datatype);
	$smarty->assign("datainfo", $datainfo);
	$smarty->assign("det_id", $det_id);
break;

default:	// SHOWING a summary table of all spies
	$smarty->assign("l_spy_messages", $l_spy_messages);
  
  if ($by1 == 'character_name')  $by11 = "character_name asc";
  elseif ($by1 == 'ship_name')   $by11 = "ship_name asc";
  elseif ($by1 == 'ship_type')   $by11 = "c_name asc";
  elseif ($by1 == 'move_type')   $by11 = "move_type asc, spy_id asc";
  else						  $by11 = "spy_id asc";

  if ($by2 == 'planet')		  $by22 = "$dbtables[planets].name asc, $dbtables[planets].sector_id asc, spy_id asc";
  elseif ($by2 == 'id')			$by22 = "spy_id asc";
  elseif ($by2 == 'job_id')	  $by22 = "job_id desc, spy_percent desc, spy_id asc";
  elseif ($by2 == 'percent')	 $by22 = "spy_percent desc, $dbtables[planets].sector_id asc, $dbtables[planets].name asc, spy_id asc";
  elseif ($by2 == 'move_type')   $by22 = "move_type asc, $dbtables[planets].sector_id asc, $dbtables[planets].name asc, spy_id asc";
  elseif ($by2 == 'owner')	   $by22 = "$dbtables[players].character_name asc, $dbtables[planets].sector_id asc, $dbtables[planets].name asc, spy_id asc";
  else						  $by22 = "$dbtables[planets].sector_id asc, $dbtables[planets].name asc, spy_id asc";

  if ($by3 == 'spycnt')			  $by33 = "spy_id_cnt ASC, $dbtables[planets].sector_id ASC";
  elseif ($by3 == 'plnname')	  $by33 = "$dbtables[planets].name ASC";
  else             $by33 = "$dbtables[planets].sector_id ASC";

  $res = $db->Execute("SELECT * FROM $dbtables[spies] WHERE $dbtables[spies].owner_id=$playerinfo[player_id] ");
	$smarty->assign("spycount", $res->RecordCount());
  if ($res->RecordCount()) {

		/* 4 */
		/* show, how many spies on own ship */
		$res = $db->Execute("SELECT COUNT(spy_id) AS as_spy_id FROM $dbtables[spies] WHERE active='N' AND owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] AND planet_id='0'");
		$smarty->assign("shipspycount", $res->RecordCount());
		if ($res->RecordCount()) {
			$spy = $res->fields;
			$smarty->assign("l_spy_defaulttitle4", $l_spy_defaulttitle4);
			$smarty->assign("shipspytotal", $spy['as_spy_id']);
		} else { 
			$smarty->assign("l_spy_no4", $l_spy_no4);
		}

			/* 1 */
			/* show, spies on other ships */
			$res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[players].character_name, $dbtables[ships].name AS ship_name, $dbtables[ships].sector_id, $dbtables[ship_types].name AS c_name, UNIX_TIMESTAMP($dbtables[players].last_login) AS online FROM $dbtables[spies] INNER JOIN $dbtables[ships] ON $dbtables[spies].ship_id=$dbtables[ships].ship_id INNER JOIN $dbtables[ship_types] ON $dbtables[ships].class=$dbtables[ship_types].type_id INNER JOIN $dbtables[players] ON $dbtables[players].player_id=$dbtables[ships].player_id WHERE $dbtables[spies].active='Y' AND $dbtables[spies].owner_id=$playerinfo[player_id] ORDER BY $by11 ");
			$smarty->assign("enemyshipspycount", $res->RecordCount());
			if ($res->RecordCount()) {
				$smarty->assign("l_spy_defaulttitle1", $l_spy_defaulttitle1);
				$smarty->assign("by2", $by2);
				$smarty->assign("by3", $by3);
				$smarty->assign("l_spy_codenumber", $l_spy_codenumber);
				$smarty->assign("l_spy_shipowner", $l_spy_shipowner);
				$smarty->assign("l_spy_shipname", $l_spy_shipname);
				$smarty->assign("l_spy_shiptype", $l_spy_shiptype);
				$smarty->assign("l_spy_shiplocation", $l_spy_shiplocation);
				$smarty->assign("l_spy_move", $l_spy_move);
				$enemyshipcount = 0;
			while (!$res->EOF) {
				$spy = $res->fields;
				if ((time() - $spy['online'])/60 > 5) {
					$spy['sector_id'] = $l_spy_notknown;
				} else {
				  $spy['sector_id'] = "<a href=move.php?move_method=real&engage=1&destination=$spy[sector_id]>$spy[sector_id]</a>";
				}
				$move = $l_spy_moves[$spy['move_type']];

				$spy_id[$enemyshipcount] = $spy['spy_id'];
				$playername[$enemyshipcount] = $spy['character_name'];
				$shipid[$enemyshipcount] = $spy['ship_id'];
				$shipname[$enemyshipcount] = $spy['ship_name'];
				$shipclass[$enemyshipcount] = $spy['c_name'];
				$spysector[$enemyshipcount] = $spy['sector_id'];
				$movetype[$enemyshipcount] = $move;

				$enemyshipcount++;
				$res->MoveNext();
			}
			$smarty->assign("enemyshipcount", $enemyshipcount);
			$smarty->assign("spy_id", $spy_id);
			$smarty->assign("playername", $playername);
			$smarty->assign("shipid", $shipid);
			$smarty->assign("shipname", $shipname);
			$smarty->assign("shipclass", $shipclass);
			$smarty->assign("spysector", $spysector);
			$smarty->assign("movetype", $movetype);
		} else {
			$smarty->assign("l_spy_no1", $l_spy_no1);
		}

		/* 2 */
		/* show spies on enemy planets */
		$res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[planets].name, $dbtables[planets].sector_id, $dbtables[players].character_name FROM $dbtables[spies] INNER JOIN $dbtables[planets] ON $dbtables[spies].planet_id=$dbtables[planets].planet_id LEFT JOIN $dbtables[players] ON $dbtables[players].player_id=$dbtables[planets].owner WHERE $dbtables[spies].owner_id=$playerinfo[player_id] AND $dbtables[spies].owner_id<>$dbtables[planets].owner ORDER BY $by22 ");
		$smarty->assign("planetspycount", $res->RecordCount());
		if ($res->RecordCount()) {
			$smarty->assign("l_spy_defaulttitle2", $l_spy_defaulttitle2);
			$smarty->assign("by1", $by1);
			$smarty->assign("by3", $by3);
			$smarty->assign("l_spy_codenumber", $l_spy_codenumber);
			$smarty->assign("l_spy_planetowner", $l_spy_planetowner);
			$smarty->assign("l_spy_planetname", $l_spy_planetname);
			$smarty->assign("l_spy_sector", $l_spy_sector);
			$smarty->assign("l_spy_job", $l_spy_job);
			$smarty->assign("l_spy_percent", $l_spy_percent);
			$smarty->assign("l_spy_move", $l_spy_move);

			$enemyplanetcount = 0;
		  while (!$res->EOF) {
				$spy = $res->fields;

				if ($spy['job_id']==0) { $job="$l_spy_jobs[0]";
				} else {
				  $temp = $spy['job_id'];
				  $job = "<a href=spy.php?command=change&spy_id=$spy[spy_id]>$l_spy_jobs[$temp]</a>";
				}

				$temp = $spy['move_type'];
				$move = $l_spy_moves[$temp];

				if ($spy['spy_percent'] == 0) { $spy['spy_percent'] = "-";
				} else { $spy['spy_percent'] = NUMBER(100*$spy['spy_percent'],5); }

				if (empty($spy['name'])) { $spy['name'] = $l_unnamed; }
		 		if (empty($spy['character_name'])) { $spy['character_name'] = $l_unowned; }

				$pspy_id[$enemyplanetcount] = $spy['spy_id'];
				$pplayername[$enemyplanetcount] = $spy['character_name'];
				$pname[$enemyplanetcount] = $spy['name'];
				$psector[$enemyplanetcount] = $spy['sector_id'];
				$pjob[$enemyplanetcount] = $job;
				$ppercent[$enemyplanetcount] = $spy['spy_percent'];
				$pmovetype[$enemyplanetcount] = $move;
				
				$enemyplanetcount++;
				$res->MoveNext();
		  }
			$smarty->assign("enemyplanetcount", $enemyplanetcount);
			$smarty->assign("pspy_id", $pspy_id);
			$smarty->assign("pplayername", $pplayername);
			$smarty->assign("pname", $pname);
			$smarty->assign("psector", $psector);
			$smarty->assign("pjob", $pjob);
			$smarty->assign("ppercent", $ppercent);
			$smarty->assign("pmovetype", $pmovetype);
		} else {
			$smarty->assign("l_spy_no2", $l_spy_no2);
		}

		/* 3 */
		/* show spies on own planets */
		$line_color = $color_line2;
		$res = $db->Execute("SELECT COUNT($dbtables[spies].spy_id) AS spy_id_cnt, $dbtables[planets].name, $dbtables[planets].sector_id FROM $dbtables[spies] INNER JOIN $dbtables[planets] ON $dbtables[spies].planet_id=$dbtables[planets].planet_id WHERE $dbtables[spies].active='N' AND $dbtables[planets].owner=$playerinfo[player_id] AND $dbtables[spies].owner_id=$playerinfo[player_id] GROUP BY $dbtables[planets].planet_id ORDER BY $by33 ");
		$smarty->assign("myplanetspycount", $res->RecordCount());
		if ($res->RecordCount()) {
			$smarty->assign("l_spy_defaulttitle3", $l_spy_defaulttitle3);
			$smarty->assign("l_spy_sector", $l_spy_sector);
			$smarty->assign("l_spy_planetname", $l_spy_planetname);
			$smarty->assign("l_spy_onplanet", $l_spy_onplanet);
			$ownplanetspycount = 0;
		  while (!$res->EOF) {
				$spy = $res->fields;
		
				if (empty($spy['name'])) { $spy['name'] = $l_unnamed; }

				$mpsector[$ownplanetspycount] = $spy['sector_id'];
				$mpname[$ownplanetspycount] = $spy['name'];
				$mpcount[$ownplanetspycount] = $spy['spy_id_cnt'];

				$ownplanetspycount++;
				$res->MoveNext();
		  }
			$smarty->assign("ownplanetspycount", $ownplanetspycount);
			$smarty->assign("mpsector", $mpsector);
			$smarty->assign("mpname", $mpname);
			$smarty->assign("mpcount", $mpcount);
		} else {
			$smarty->assign("l_spy_no3", $l_spy_no3);
		}
	/* show message - player not own spies */
	} else {
		$smarty->assign("l_spy_nospiesatall", $l_spy_nospiesatall);
	}
break;
}   //swich

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."spy.tpl");

include ("footer.php");
?>
