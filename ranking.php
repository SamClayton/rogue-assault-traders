<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: ranking.php

include ("config/config.php");
include ("languages/$langdir/lang_ranking.inc");
include ("languages/$langdir/lang_teams.inc");

$title = "Player Rankings";

$noreturn = 1;
$isonline = checklogin();

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($isonline != 1){
	if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
		base_template_data();
	}
	else
	{
		$smarty->assign("title", $title);
		$smarty->assign("templatename", $templatename);
	}
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if($showzeroranking == 1)
	$showzero = "";
else $showzero = "$dbtables[players].turns_used != 0 and";

if ($hide_admin_rank == 1)
{
	$query = " AND $dbtables[players].player_id > 3 ";
}
else
{
	$query = " ";
}

// This code is now functional on all three db platforms.
$res = $db->Execute("SELECT * FROM $dbtables[players], $dbtables[ships] " .
					" WHERE ".$showzero." $dbtables[players].player_id = $dbtables[ships].player_id and destroyed!='Y' and $dbtables[players].player_id > 3 and  $dbtables[players].currentship=$dbtables[ships].ship_id " .
					"and email NOT LIKE '%@npc' ". $query);

$num_players = $res->RecordCount();

if ($res)
{
	while (!$res->EOF)
	{
		$row = $res->fields;
		if($username == $row['email']){
			gen_score($row['player_id']);
			break;
		}
		$res->MoveNext();
	}
}

if ((!isset($sort)) || ($sort == ''))
{
	$sort = ' ';
}

if ((!isset($page)) || ($page == ''))
{
	$page = 0;
}

$temp = floor($num_players / $max_rank);

if ($page == -1)
{
	$page = 0;
	$max_rank = $num_players;
}

if ($sort == "turns")
{
	$by = "turns_used DESC,character_name ASC";
}
elseif ($sort == "login")
{
	$by = "last_login DESC,character_name ASC";
}
elseif ($sort == "good")
{
	$by = "rating DESC,character_name ASC";
}
elseif ($sort == "bad")
{
	$by = "rating ASC,character_name ASC";
}
elseif ($sort == "team")
{
	$by = "$dbtables[teams].team_name ASC, character_name ASC";
}
elseif ($sort == "efficiency")
{
	$by = "efficiency DESC";
}
elseif ($sort == "kills")
{
	$by = "kills DESC";
}
elseif ($sort == "deaths")
{
	$by = "deaths DESC";
}
elseif ($sort == "captures")
{
	$by = "captures DESC";
}
elseif ($sort == "lost")
{
	$by = "planets_lost DESC";
}
elseif ($sort == "built")
{
	$by = "planets_built DESC";
}
elseif ($sort == "experience")
{
	$by = "experience DESC";
}
elseif ($sort == "name")
{
	$by = "character_name ASC";
}
else
{
	$by = "score DESC,character_name ASC";
}

if ($hide_admin_rank == 1)
{
	$query = " AND $dbtables[players].player_id > 3 ";
}
else
{
	$query = " ";
}

$res = sql_ranking();

$smarty->assign("sort", $sort);
$smarty->assign("multiplepages", $temp);
$smarty->assign("pages", $pages);
$smarty->assign("max_rank", $max_rank);
$smarty->assign("allselected", $allselected);
$smarty->assign("l_all", $l_all);
$smarty->assign("l_submit", $l_submit);
$smarty->assign("prevlink", $page - 1);
$smarty->assign("nextlink", $page + 1);
$smarty->assign("l_ranks_next", $l_ranks_next);
$smarty->assign("l_ranks_prev", $l_ranks_prev);
$smarty->assign("res", $res);
$smarty->assign("l_ranks_none", $l_ranks_none);
$smarty->assign("l_ranks_select", $l_ranks_select);
$smarty->assign("l_ranks_page", $l_ranks_page);

$i = 1;

if ($res)
{

	$rankto = ($page * $max_rank + $res->recordcount());

	$smarty->assign("l_ranks_pnum", $l_ranks_pnum);
	$smarty->assign("l_ranks_show", $l_ranks_show);
	$smarty->assign("l_ranks_dships", $l_ranks_dships);
	$smarty->assign("l_ranks_to", $l_ranks_to);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_ranks_standing", $l_ranks_standing);
	$smarty->assign("l_score", $l_score);
	$smarty->assign("page", $page);
	$smarty->assign("l_ranks_rank", $l_ranks_rank);
	$smarty->assign("l_player", $l_player);
	$smarty->assign("l_turns_used", $l_turns_used);
	$smarty->assign("l_ranks_lastlog", $l_ranks_lastlog);
	$smarty->assign("l_ranks_good", $l_ranks_good);
	$smarty->assign("l_team", $l_team);
	$smarty->assign("l_ranks_online", $l_ranks_online);
	$smarty->assign("l_ranks_rating", $l_ranks_rating);
	$smarty->assign("l_ranks_evil", $l_ranks_evil);
	$smarty->assign("num_players", NUMBER($num_players));
	$smarty->assign("rankfrom", ($page * $max_rank + 1));
	$smarty->assign("username", $username);
	$smarty->assign("rankto", $rankto);
	$smarty->assign("l_ranks_kills", $l_ranks_kills);
	$smarty->assign("l_ranks_deaths", $l_ranks_deaths);
	$smarty->assign("l_ranks_captures", $l_ranks_captures);
	$smarty->assign("l_ranks_lost", $l_ranks_lost);
	$smarty->assign("l_ranks_built", $l_ranks_built);
	$smarty->assign("color", $color_line1);
	$smarty->assign("l_ranks_experience", $l_ranks_experience);

	$rankcount = 0;

	while (!$res->EOF)
	{
		$row = $res->fields;
		$rating = sign($row['rating']) * round(sqrt( abs($row['rating']) ));

		$curtime = TIME();
		$time = $row['online'];
		$difftime = ($curtime - $time) / 60;
		$temp_turns = $row['turns_used'];
		if ($temp_turns <= 0)
		{
			$temp_turns = 1;
		}

		$online = "";
		if ($difftime <= 5) 
		{
			$online = "Online";
		}else{
			$online = "";
		}

		$lastlogin = date($local_date_full_format, $row['last_login']);

		$email[$rankcount] = $row['email'];

		$rankprofileid[$rankcount] =  $row['profile_id'];
		$ranknumber[$rankcount] =  NUMBER($i + ($page * $max_rank));
		$rankscore[$rankcount] =  NUMBER($row['score']);
		$rankimage[$rankcount] =  player_insignia_name($row['email']);
		$publicavatar[$rankcount] = "avatars/".$row['avatar'];
		$rankname[$rankcount] = $row['character_name'];
		$rankturns[$rankcount] = NUMBER($row['turns_used']);
		$ranklastlogin[$rankcount] = $lastlogin;
		$rankrating[$rankcount] = NUMBER($rating);
		$rankteam[$rankcount] = $row['team_name'];
		$rankonline[$rankcount] = $online;
		$rankkills[$rankcount] = NUMBER($row['kills']);
		$rankdeaths[$rankcount] = NUMBER($row['deaths']);
		$rankcaptures[$rankcount] = NUMBER($row['captures']);
		$ranklost[$rankcount] = NUMBER($row['planets_lost']);
		$rankbuilt[$rankcount] = NUMBER($row['planets_built']);
		$rankexperience[$rankcount] = NUMBER(floor($row['experience']));

		if ($row['turns_used'] >= 150)
		{
			$efficient = "SELECT ROUND($dbtables[players].score/$dbtables[players].turns_used, 0) ".				
						 "AS efficiency FROM $dbtables[players] WHERE email = '". $row['email']. "'";
			$res2= $db->Execute($efficient);

			db_op_result($debug_query,__LINE__,__FILE__);

			$row2 = $res2->fields;

			$rankeff[$rankcount] = NUMBER($row2['efficiency']);
		}
		else
		{
			$rankeff[$rankcount] = 0;
		}

		$rankcount++;
		$i++;
		$res->MoveNext();
	}
}

if (empty($username))
{
	$smarty->assign("gotomain", $l_global_mlogin);
}
else
{
	$smarty->assign("gotomain", $l_global_mmenu);
}

$smarty->assign("rankexperience", $rankexperience);
$smarty->assign("rankprofileid", $rankprofileid);
$smarty->assign("ranklost", $ranklost);
$smarty->assign("rankbuilt", $rankbuilt);
$smarty->assign("rankkills", $rankkills);
$smarty->assign("rankdeaths", $rankdeaths);
$smarty->assign("rankcaptures", $rankcaptures);
$smarty->assign("rankeff", $rankeff);
$smarty->assign("rankonline", $rankonline);
$smarty->assign("rankteam", $rankteam);
$smarty->assign("rankrating", $rankrating);
$smarty->assign("ranklastlogin", $ranklastlogin);
$smarty->assign("rankturns", $rankturns);
$smarty->assign("rankname", $rankname);
$smarty->assign("publicavatar", $publicavatar);
$smarty->assign("rankimage", $rankimage);
$smarty->assign("rankscore", $rankscore);
$smarty->assign("ranknumber", $ranknumber);
$smarty->assign("email", $email);
$smarty->assign("rankcount", $rankcount);

$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->display($templatename."ranking.tpl");
include ("footer.php");
?>
