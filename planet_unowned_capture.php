<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_unowned_capture.php

include ("config/config.php");
include ("languages/$langdir/lang_attack.inc");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_traderoute.inc");
include ("combat_functions.php");
$no_gzip = 1;

if (isset($_GET['planet_id']))
{
	$planet_id = $_GET['planet_id'];
}

$title = $l_planet_title;

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

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
	$planetinfo=$result3->fields;

bigtitle();

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

// No planet

if (empty($planetinfo))
{
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

	die();
}

if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
	if ($shipinfo['on_planet'] == 'Y')
	{
	  $debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$shipinfo[ship_id]");
	  db_op_result($debug_query,__LINE__,__FILE__);
	}
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}

if ($planetinfo['owner'] != 0)
{
	if ($spy_success_factor)
	{
	  spy_detect_planet($shipinfo['ship_id'], $planetinfo['planet_id'],$planet_detect_success1);
	}
	$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
	$ownerinfo = $result3->fields;

	$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$planetinfo[owner] AND ship_id=$ownerinfo[currentship]");
	$ownershipinfo = $res->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
{
	if ($command != "")
	{
		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";
	}

	if ($allow_ibank)
	{
		echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
	}

	echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";

	TEXT_GOTOMAIN();
	include ("footer.php");
	die();
}
else
{
//
//
// Capture Unowned or Defeated planet
//
//

	if ($planetinfo['owner'] == 0 || $planetinfo['defeated'] == 'Y')
	{
		echo "$l_planet_captured<BR>";
		if ($spy_success_factor)
		{
			change_planet_ownership($planet_id, 0, $playerinfo['player_id']);
		}

		if($planetinfo['owner'] != 0 && $planetinfo['owner'] != 2 && $planetinfo['owner'] != 3)
			update_player_experience($playerinfo['player_id'], $defeating_planet);

		if($planetinfo['owner'] != 0 and ($planetinfo['team'] != $playerinfo['team'] and $playerinfo['team'] != 0)){
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET captures=captures+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, team=null, owner=$playerinfo[player_id], base='N', defeated='N' WHERE planet_id=$planet_id");
		db_op_result($debug_query,__LINE__,__FILE__);
		$debug_query = $db->Execute("DELETE from $dbtables[autotrades] WHERE planet_id = $planet_id");
		db_op_result($debug_query,__LINE__,__FILE__);

		$ownership = calc_ownership($shipinfo['sector_id']);

		if (!empty($ownership))
			echo "$ownership<p>";

		if ($planetinfo['owner'] != 0)
		{
			gen_score($planetinfo['owner']);
		}

		if ($planetinfo['owner'] != 0)
		{
			$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
			$query = $res->fields;
			$planetowner=$query['character_name'];
			playerlog($planetinfo['owner'],LOG_PLANET_YOUR_CAPTURED,"$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
		}
		else
		{
			$planetowner="$l_planet_noone";
		}  

		$res2=$db->Execute("SELECT UNIX_TIMESTAMP(MAX(time)) as lasttime FROM $dbtables[planet_log] WHERE planet_id=".$planetinfo['planet_id']." AND (action=".PLOG_CAPTURE." OR action=".PLOG_GENESIS_CREATE.")");
		$lasttime=$res2->fields['lasttime'];
		if ($lasttime)
		{
			$curtime = TIME();
			$difftime = ($curtime - $lasttime) / 60;
			if ($difftime <= 10)
			{
				adminlog(LOG_RAW,"<font color=yellow><B>Rapid planet recapture:</B></font><BR>planet_id=<B>".$planetinfo['planet_id']."</B>, sector=<B>".$shipinfo['sector_id']."</B>, attacker: <B>".get_player($playerinfo['player_id'])."</B>, owner: <B>".get_player($planetinfo['owner'])."</B>. Time difference=<B>".NUMBER($difftime,1)."</B> minutes. Money: <B>".$planetinfo['credits']."</B>, colonists: <B>".$planetinfo['colonists']."</B>.");
			}
		}
		planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_CAPTURE);
		playerlog($playerinfo['player_id'], LOG_PLANET_CAPTURED, "$planetinfo[colonists]|$planetinfo[credits]|$planetowner");

		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";

		if ($allow_ibank)
		{
			echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
		}

		echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}
else
	{
		echo "$l_planet_notdef<BR>";

		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";

		if ($allow_ibank)
		{
			echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
		}

		echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}

}

close_database();
?>