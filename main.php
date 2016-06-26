<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: main.php

include ("config/config.php");

$title = $l_main_title;

if (checklogin())
{
	include ("footer.php");
	die();
}

// Skinning stuff
if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($tournament_setup_access == 1){

	$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
	$playerinfo = $result->fields;

	$startdate = date("Y/m/d");
	$shoutcount = 0;
	$res = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE sb_alli = 0 ORDER BY sb_date desc  LIMIT 0,5");

	if($res->EOF)
	{
		$shoutmessage[$shoutcount] = $l_news_none;
		$shoutposter[$shoutcount] = "";
		$shoutcount++;
	}
	else
	{
		while (!$res->EOF) 
		{
			$row = $res->fields;
			$newsdata = stripslashes(rawurldecode($row['sb_text']));
			$shoutmessage[$shoutcount] = $newsdata;
			$shoutposter[$shoutcount] = $row['player_name'];
			$shoutcount++;
			$res->MoveNext();
		}
	}

	$smarty->assign("shoutcount", $shoutcount);
	$smarty->assign("shoutmessage", $shoutmessage);
	$smarty->assign("shoutposter", $shoutposter);

	$newposts = 0;
	if($playerinfo['team'] != 0){
		$debug_query = $db->Execute("select lastonline from $dbtables[fplayers] WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumplayer = $debug_query->fields;

		$debug_query = $db->Execute("select forum_id from $dbtables[forums] where teams=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$forumdata = $debug_query->fields;

		$query2 = $db->Execute("select * from $dbtables[posts] where forum_id=$forumdata[forum_id] and post_time>='$forumplayer[lastonline]' order by post_time");
		db_op_result($query2,__LINE__,__FILE__);
		$newposts = $query2->RecordCount();
	}

	$smarty->assign("title", $title);

	$smarty->assign("insignia", player_insignia_name($username));
	$smarty->assign("avatar", $playerinfo['avatar']);
	$smarty->assign("teamicon", $teamicon);
	$smarty->assign("player_name", $playerinfo['character_name']);
	$smarty->assign("team_id", $playerinfo['team']);
	$smarty->assign("shipname", $shipinfo['name']);

	$smarty->assign("general_text_color", $general_text_color);
	$smarty->assign("basefontsize", $basefontsize);
	$smarty->assign("general_highlight_color", $general_highlight_color);
	$smarty->assign("main_table_heading", $main_table_heading);
	$smarty->assign("l_abord", $l_abord);
	$smarty->assign("l_commands", $l_commands);
	$smarty->assign("shoutboxtitle", "Shout Box");

	$smarty->assign("commandreadmail", "&nbsp;<a class=mnu href=\"readmail.php\">$l_read_msg</A>&nbsp;");
	$smarty->assign("l_read_msg", $l_read_msg);

	$smarty->assign("commandsendmail", "&nbsp;<a class=mnu href=\"mailto2.php\">$l_send_msg</a>&nbsp;");
	$smarty->assign("l_send_msg", $l_send_msg);

	$smarty->assign("commandblockmail", "&nbsp;<a class=mnu href=\"messageblockmanager.php\">$l_block_msg</a>&nbsp;");
	$smarty->assign("l_block_msg", $l_block_msg);

	$smarty->assign("commandranking", "&nbsp;<a class=mnu href=\"ranking.php\">$l_rankings</a>&nbsp;");
	$smarty->assign("l_rankings", $l_rankings);

	$smarty->assign("commandteams", "&nbsp;<a class=mnu href=\"teams.php\">$l_teams</a>&nbsp;");
	$smarty->assign("l_teams", $l_teams);

	$smarty->assign("commandteamforum", "&nbsp;<a class=mnu href=\"team-forum.php?command=showtopics\">$l_teamforum<font size=\"1\"> - New: $newposts</font></a>&nbsp;");
	$smarty->assign("l_teamforum", $l_teamforum);

	$smarty->assign("commandteamship", "&nbsp;<a class=mnu href=\"team-report.php\">$l_teamships</a>&nbsp;");
	$smarty->assign("l_teamships", $l_teamships);

	$smarty->assign("commanddestruct", "&nbsp;<a class=mnu href=\"self-destruct.php\">$l_ohno</a>&nbsp;");
	$smarty->assign("l_ohno", $l_ohno);

	$smarty->assign("commandoptions", "&nbsp;<a class=mnu href=\"options.php\">$l_options</a>&nbsp;");
	$smarty->assign("l_options", $l_options);

	$smarty->assign("commandfeedback", "&nbsp;<a class=mnu href=\"feedback.php\">$l_feedback</a>&nbsp;");
	$smarty->assign("l_feedback", $l_feedback);

	$smarty->assign("commandlogout", "&nbsp;<a class=mnu href=\"logout.php\">$l_logout</a>&nbsp;");
	$smarty->assign("l_logout", $l_logout);

	$smarty->assign("avatar", $playerinfo['avatar']);

	if($playerinfo['team'] != 0){
		$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
		$teamicon = $result->fields['icon'];
	}else{
		$teamicon="default_icon.gif";
	}
	$smarty->assign("teamicon", $teamicon);

	$smarty->assign("classname", $classinfo['name']);
	$smarty->assign("templatename", $templatename);

	$smarty->display($templatename."tourney.tpl");
	include ("footer.php");
	die();
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

if ($shipinfo['on_planet'] == "Y")
{
	$res2 = $db->Execute("SELECT planet_id, owner FROM $dbtables[planets] WHERE planet_id=$shipinfo[planet_id]");
	if ($res2->RecordCount() != 0)
	{

		close_database();
		echo "<A HREF=planet.php?planet_id=$shipinfo[planet_id]>$l_clickme</A> $l_toplanetmenu	<BR>";
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=planet.php?planet_id=$shipinfo[planet_id]&id=".$playerinfo['player_id']."\">";
		die();
	}
	else
	{
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE player_id=$playerinfo[player_id] AND ship_id=$playerinfo[currentship]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
}

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$shipyard = "";

if ($sectorinfo['port_type'] != "none")
{
	$portname = t_port($sectorinfo['port_type']);
	$basename = $sectorinfo['port_type'];
	$portgraphic = $porttype[$basename];

	if ($sectorinfo['port_type'] == 'upgrades')
	{
		$shipyard = $l_main_shipyard1;
	}
}
else
{
	$portname = ucfirst($l_none);
	$portgraphic = $porttype['none'];
}

$smarty->assign("port_type", $sectorinfo['port_type']);
$smarty->assign("portname", ucfirst($portname));
$smarty->assign("portgraphic", $portgraphic);
$smarty->assign("shipyard", $shipyard);
$smarty->assign("shipyardgraphic", $porttype['shipyard']);

$countplanet = 0;

function scanlevel($techlevel){
	global $playerinfo, $shipinfo, $techjammer, $techowner;

	$sc_error= SCAN_ERROR($shipinfo['sensors'], $techjammer);
	$sc_error_plus=100;
	if ($sc_error < 100){
		$sc_error_plus=115;
	}
	if($playerinfo['player_id'] == $techowner or $techowner == 3)
		return $techlevel;

	return round($techlevel * (mt_rand($sc_error , $sc_error_plus) / 100));
}

function display_this_planet($this_planet) 
{
	global $planettypes, $basefontsize, $l_unowned, $l_unnamed, $basefontsize, $dbtables, $db, $colonist_limit, $general_highlight_color;
	global $planetid, $planetimg, $planetname, $planetowner, $countplanet, $shipinfo, $playerinfo, $techowner, $techjammer;

	$totalcount = 0;
	$curcount = 0;
	$i = 0;
	$planetlevel = 0;
	if ($this_planet['owner'] != 0)
	{
		$result5 = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=". $this_planet['owner'] . "");
		$planet_owner = $result5->fields;

		$techowner = $this_planet['owner'];
		$techjammer = $this_planet['jammer'];
		$planetavg = scanlevel($this_planet['computer']) + scanlevel($this_planet['sensors']) + scanlevel($this_planet['beams']) + scanlevel($this_planet['torp_launchers']) + scanlevel($this_planet['shields']) + scanlevel($this_planet['cloak']) + ($this_planet['colonists'] / ($colonist_limit / 54));
		$planetavg = round($planetavg/37.8); // Divide by (54 levels * 7 categories / 4) to get 1-4.
		if ($planetavg > 10)
		{
			$planetavg = 10;
		}

		if ($planetavg < 0)
		{
			$planetavg = 0;
		}

		$planetlevel = $planetavg;
	}
	$planetid[$countplanet] = $this_planet['planet_id'];
	$planetimg[$countplanet] = $planettypes[$planetlevel];
	if (empty($this_planet['name']))
	{
		$planetname[$countplanet] = $l_unnamed;
	}
	else
	{
		$planetname[$countplanet] = $this_planet['name'];
	}

	if (@$this_planet['owner'] == 0)
	{
		$planetowner[$countplanet] = "$l_unowned";
	}
	else
	{
		$planetowner[$countplanet] = "$planet_owner[character_name]";
	}
	$countplanet++;
	return;
}

$res = '';
$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$shipinfo[sector_id]'");

$i = 0;
$successful_display = 0;

while (!$res->EOF)
{
	$uber = 0; 
	$success = 0;
	$hiding_planet[$i] = $res->fields;

	if ($hiding_planet[$i]['owner'] == $playerinfo['player_id']) 
	{
		$uber = 1;
	}

	if ($hiding_planet[$i]['team'] != 0) 
	{
		if ($hiding_planet[$i]['team'] == $playerinfo['team']) 
		{
			$uber = 1;
		}
	}
	
	if ($shipinfo['sensors'] >= $hiding_planet[$i]['cloak'])
	{
		$uber = 1;
	}

	if ($uber == 0) //Not yet 'visible'
	{
		$success = SCAN_SUCCESS($shipinfo['sensors'], $hiding_planet[$i]['cloak']);
		if ($success < 5)
		{
			$success = 5;
		}
	
		if ($success > 95)
		{
			$success = 95;
		}
	
		$roll = mt_rand(1, 100);
		if ($roll <= $success) // If able to see the planet
		{
			$uber = 1; //confirmed working
		}
	
		if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
		{
			$res_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '" . $hiding_planet[$i]['planet_id'] . "' AND owner_id = '$playerinfo[player_id]'");
			if ($res_s->RecordCount())
			{
				 $uber = 1;
			}
		}
	}
	
	if ($uber == 1)
	{
		$planets[$i] = $res->fields;
		display_this_planet($planets[$i]);
		$successful_display++;
	}
	$i++;
	$res->MoveNext();
}

if (($i > 0) && ($successful_display < 1))
{
	$countplanet = 0;
}

$smarty->assign("countplanet", $countplanet);
$smarty->assign("planetid", $planetid);
$smarty->assign("planetimg", $planetimg);
$smarty->assign("planetname", $planetname);
$smarty->assign("planetowner", $planetowner);

$playercount = 0;

if ($shipinfo['sector_id'] != 1)
{
	$result4 = $db->Execute(" SELECT DISTINCT
							  $dbtables[ships].*,
							  $dbtables[players].*,
							  $dbtables[teams].team_name,
							  $dbtables[teams].id
							  FROM $dbtables[ships]
							  LEFT JOIN $dbtables[players] ON $dbtables[ships].player_id=$dbtables[players].player_id
							  LEFT JOIN $dbtables[teams]
							  ON $dbtables[players].team = $dbtables[teams].id
							  WHERE $dbtables[ships].player_id<>$playerinfo[player_id]
							  AND $dbtables[ships].sector_id=$shipinfo[sector_id]
							  AND $dbtables[ships].on_planet='N' AND  $dbtables[players].currentship=$dbtables[ships].ship_id");
	$totalcount = 0;

	if ($result4 > 0)
	{
		$curcount = 0;
		while (!$result4->EOF)
		{
			$row = $result4->fields;
			$success = SCAN_SUCCESS($shipinfo['sensors'], $row['cloak']);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);

			if ($roll < $success)
			{
				$shipavg = $row['hull'] + $row['engines'] + $row['computer'] + $row['beams'] + $row['torp_launchers'] + $row['shields'] + $row['armour'];
				$shipavg /= 7;
				$shipclass=$row['class'];
				$player_id[$playercount] = $row['player_id'];
				$ship_id[$playercount] = $row['ship_id'];
				$getshipimage = $db->Execute("SELECT image FROM $dbtables[ship_types] WHERE type_id = $shipclass");
				$image = $getshipimage->fields['image'];

				$shipimage[$playercount] = "templates/".$templatename."images/$image";
				$shipnames[$playercount] = $row['name'];
				$playername[$playercount] = $row['character_name'];
				$shipprobe[$playercount] = "ship";

				if ($row['team_name']) 
				{
					$teamname[$playercount] = $row['team_name'];
				}
				else
				{
					$teamname[$playercount] = "";
				}
				$totalcount++;
				$playercount++;
			}
			$result4->MoveNext();
		}
	}

	
$result4 = $db->Execute(" SELECT DISTINCT
							  $dbtables[probe].*,
							  $dbtables[players].*,
							  $dbtables[teams].team_name,
							  $dbtables[teams].id
							  FROM $dbtables[probe]
							  LEFT JOIN $dbtables[players] ON $dbtables[probe].owner_id=$dbtables[players].player_id
							  LEFT JOIN $dbtables[teams]
							  ON $dbtables[players].team = $dbtables[teams].id
							  WHERE  $dbtables[probe].sector_id=$shipinfo[sector_id] and $dbtables[probe].active='Y'
							  ");
	$totalcount = 0;

	if ($result4 > 0)
	{
		$curcount = 0;
		while (!$result4->EOF)
		{
			$row = $result4->fields;
			$success = SCAN_SUCCESS($shipinfo['sensors'], $row['cloak']);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);

			if (($roll < $success) or ($shipinfo['player_id']==$row['owner_id']))
			{
				
				if ($shipinfo['player_id']==$row['owner_id']) 
				{
					$player_id[$playercount] = $row['probe_id'];
					$ship_id[$playercount] = "";
					$shipimage[$playercount] = $probetype;
					$shipnames[$playercount] = $row['name'];
					$playername[$playercount] = $row['character_name'];
					if($row['team_name'] != ''){
						$teamname[$playercount] = $row['team_name'];
					}else{
						$teamname[$playercount]= "";
					}
				}
				else
				{
					$player_id[$playercount] = $row['probe_id'];
					$ship_id[$playercount] = "";
					$shipimage[$playercount] = $probetype;
					$shipnames[$playercount] = "";
					$playername[$playercount] = $row['character_name'];
					if($row['team_name'] != ''){
						$teamname[$playercount] = $row['team_name'];
					}else{
						$teamname[$playercount] = "";
					}
				}
				$shipprobe[$playercount] = "probe";
				$playercount++;
				$totalcount++;
			}
			$result4->MoveNext();
		}
	}

$result4 = $db->Execute(" SELECT debris_id FROM $dbtables[debris] WHERE sector_id=$shipinfo[sector_id]");
	$totalcount = 0;

	if ($result4 > 0)
	{
		$curcount = 0;
		while (!$result4->EOF)
		{
			$row = $result4->fields;

			$player_id[$playercount] = $row['debris_id'];
			$shipimage[$playercount] = $debris;
			$playername[$playercount] = $l_debris;

			$shipprobe[$playercount] = "debris";
			$playercount++;
			$totalcount++;

			$result4->MoveNext();
		}
	}

}
else
{
	$insector0 = "sector0";
}

$smarty->assign("insector0", $insector0);
$smarty->assign("playercount", $playercount);
$smarty->assign("player_id", $player_id);
$smarty->assign("ship_id", $ship_id);
$smarty->assign("shipimage", $shipimage);
$smarty->assign("shipnames", $shipnames);
$smarty->assign("playername", $playername);
$smarty->assign("teamname", $teamname);
$smarty->assign("shipprobe", $shipprobe);
$smarty->assign("l_sector_0", $l_sector_0);


$lss_info = "";
if ($shipinfo['sector_id'] != '1')
{
	$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
	$decaydate = date("Y-m-d H:i:s", $oldstamp);
	$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> $playerinfo[player_id] AND source = $shipinfo[sector_id] and time > '$decaydate' ORDER BY time DESC",1); 
	db_op_result($resx,__LINE__,__FILE__);
	$myrow = $resx->fields;
	if (!$myrow)
	{
		$lss_info .= "$l_none";
		$smarty->assign("lss_playername", $l_none);
		$smarty->assign("lss_shipclass", "");
		$smarty->assign("lss_destination", "");
		$smarty->assign("lss_sensorlevel", 0);
	}
	else
	{
		if ($shipinfo['sensors'] >= $lssd_level_three)
		{
			$lss_info .= "$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship $l_traveled " . $myrow['destination'];
			$smarty->assign("lss_playername", get_player($myrow['player_id']));
			$smarty->assign("lss_shipclass", get_shipclassname($myrow['ship_class']));
			$smarty->assign("lss_destination", $myrow['destination']);
			$smarty->assign("lss_sensorlevel", 3);
		}
		elseif ($shipinfo['sensors'] >= $lssd_level_two)
		{
			$lss_info .= "$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship.";
			$smarty->assign("lss_playername", get_player($myrow['player_id']));
			$smarty->assign("lss_shipclass", get_shipclassname($myrow['ship_class']));
			$smarty->assign("lss_destination", "");
			$smarty->assign("lss_sensorlevel", 2);
		}
		else
		{
			$lss_info .= "$l_unknown " . get_shipclassname($myrow['ship_class']) . " $l_classship.";
			$smarty->assign("lss_playername", $l_unknown);
			$smarty->assign("lss_shipclass", get_shipclassname($myrow['ship_class']));
			$smarty->assign("lss_destination", "");
			$smarty->assign("lss_sensorlevel", 1);
		}
	}
}
else
{
	$lss_info .= "$l_sector_0";
	$smarty->assign("lss_playername", $l_sector_0);
	$smarty->assign("lss_shipclass", "");
	$smarty->assign("lss_destination", "");
	$smarty->assign("lss_sensorlevel", 0);
}

$smarty->assign("l_player", $l_player);
$smarty->assign("l_onboard", $l_onboard);
$smarty->assign("l_classship", $l_classship);
$smarty->assign("l_traveled", $l_traveled);

$res = $db->Execute("SELECT * FROM $dbtables[sector_defence],$dbtables[players] WHERE $dbtables[sector_defence].sector_id='$shipinfo[sector_id]'
													AND $dbtables[players].player_id = $dbtables[sector_defence].player_id order by $dbtables[players].character_name");
$i = 0;
if ($res > 0)
{
	while (!$res->EOF)
	{
		$defences[$i] = $res->fields;
		$i++;
		$res->MoveNext();
	}
}
$num_defences = $i;

$defensecount = 0;
$totalcount = 0;
$curcount = 0;
$fightercount = 0;
$minecount = 0;

if ($num_defences > 0) 
{
	$i = 0;
	while ($i < $num_defences)
	{
		$defence_id = $defences[$i]['defence_id'];
		if ($defences[$i]['defence_type'] == 'F')
		{
			$defenseid[$defensecount] = $defence_id;
			$defenseimage[$defensecount] = $fightertype;
			$def_type = $l_fighters;
			$fightercount++;
		}
		elseif ($defences[$i]['defence_type'] == 'M')
		{
			$defenseid[$defensecount] = $defence_id;
			$defenseimage[$defensecount] = $minetype;
			$def_type = $l_mines;
			$minecount++;
		}

		$defensetype[$defensecount] = $defences[$i]['defence_type'];
		$defensemode[$defensecount] = $def_type;
		$defplayername[$defensecount] = $defences[$i]['character_name'];
		$defenseqty[$defensecount] = NUMBER($defences[$i]['quantity']);
		$totalcount++;
		$i++;
		$defensecount++;
	}
}

$smarty->assign("fightercount", $fightercount);
$smarty->assign("minecount", $minecount);
$smarty->assign("defensetype", $defensetype);
$smarty->assign("defensecount", $defensecount);
$smarty->assign("defenseid", $defenseid);
$smarty->assign("defenseimage", $defenseimage);
$smarty->assign("defensemode", $defensemode);
$smarty->assign("defplayername", $defplayername);
$smarty->assign("defenseqty", $defenseqty);

$smarty->assign("l_sector_def", $l_sector_def);

// Start of center

$starsize = $startypes[$sectorinfo['star_size']];

$smarty->assign("l_tradingport", $l_tradingport);
$smarty->assign("l_planet_in_sec", $l_planet_in_sec);
$smarty->assign("l_ships_in_sec", $l_ships_in_sec);
$smarty->assign("l_sector_def", $l_sector_def);
$smarty->assign("starsize", $starsize);
$smarty->assign("max_planets", $sectorinfo['star_size']);
$smarty->assign("l_max_planets", $l_max_planets);
$smarty->assign("general_highlight_color", $general_highlight_color);
$smarty->assign("basefontsize", $basefontsize + 2);
$smarty->assign("l_lss", $l_lss);
$smarty->assign("lss_info", $lss_info);
$smarty->assign("sectorzero", $shipinfo['sector_id']);
$smarty->assign("sg_sector", $sectorinfo['sg_sector']);

//-------------------------------------------------------------------------------------------------
// end of center

$smarty->assign("classname", $classinfo['name']);

$smarty->display($templatename."main.tpl");

include ("footer.php");
 ?>
