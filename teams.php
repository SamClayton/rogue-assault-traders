<?php
// This program is free software; you can redistribute it and/or modify it	
// under the terms of the GNU General Public License as published by the	  
// Free Software Foundation; either version 2 of the License, or (at your	 
// option) any later version.																
// 
// File: teams.php

include ("config/config.php");
include ("languages/$langdir/lang_teams.inc");

$title = $l_team_title;

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

// Avoiding warnings from undeclareds

if ((!isset($command)) || ($command == ''))
{
	 $command = '';
}

if ((!isset($team_id)) || ($team_id == ''))
{
	 $team_id = '';
}

/* Get user info */
$debug_query = $db->Execute("SELECT $dbtables[players].*, $dbtables[teams].team_name, $dbtables[teams].description, $dbtables[teams].creator, $dbtables[teams].id
							FROM $dbtables[players]
							LEFT JOIN $dbtables[teams] ON $dbtables[players].team = $dbtables[teams].id
							WHERE $dbtables[players].email='$username'");
db_op_result($debug_query,__LINE__,__FILE__);
$thisplayer_info = $debug_query->fields;

/*
	We do not want to query the database
	if it is not necessary.
*/
if ($thisplayer_info['team_invite'] != "") {
	/* Get invite info */
	$debug_query = $db->Execute(" SELECT $dbtables[players].player_id, $dbtables[players].team_invite, $dbtables[teams].team_name,$dbtables[teams].id
								 FROM $dbtables[players]
								 LEFT JOIN $dbtables[teams] ON $dbtables[players].team_invite = $dbtables[teams].id
								 WHERE $dbtables[players].email='$username'");
	db_op_result($debug_query,__LINE__,__FILE__);
	$invite_info = $debug_query->fields;
}

/*
	Get Team Info
*/
$team_id = stripnum($team_id);
if ($team_id)
{
	$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$team_id");
	$team = $result_team->fields;
} else {
	$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$thisplayer_info[team]");
	$team = $result_team->fields;
}

function kick_off_planet($player_id,$team_id)
{
	global $db, $dbtables;

	$result1 = $db->Execute("SELECT * from $dbtables[planets] where owner = '$player_id' ");
	db_op_result($result1,__LINE__,__FILE__);

	if ($result1 > 0)
	{
		while (!$result1->EOF)
		{
			$row = $result1->fields;
			$result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and planet_id = '$row[planet_id]' and player_id <> '$player_id' ");
			db_op_result($result2,__LINE__,__FILE__);
			if ($result2 > 0)
			{
				while (!$result2->EOF )
				{
					$cur = $result2->fields;
					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
					db_op_result($debug_query,__LINE__,__FILE__);

					playerlog($cur[player_id], LOG_PLANET_EJECT, "$cur[sector]|$row[character_name]");
					$result2->MoveNext();
				}
			}
			$result1->MoveNext();
		}
	}
}

function defence_vs_defence($player_id)
{
	global $db, $dbtables;

	$result1 = $db->Execute("SELECT * from $dbtables[sector_defence] where player_id = $player_id");
	db_op_result($result1,__LINE__,__FILE__);

	if ($result1 > 0)
	{
		while (!$result1->EOF)
		{
			$row = $result1->fields;
			$deftype = $row[defence_type] == 'F' ? 'Fighters' : 'Mines';
			$qty = $row['quantity'];
			$result2 = $db->Execute("SELECT * from $dbtables[sector_defence] where sector_id = $row[sector_id] and player_id <> $player_id ORDER BY quantity DESC");
			db_op_result($result2,__LINE__,__FILE__);
			if ($result2 > 0)
			{
				while (!$result2->EOF && $qty > 0)
				{
					$cur = $result2->fields;
					$targetdeftype = $cur[defence_type] == 'F' ? $l_fighters : $l_mines;
					if ($qty > $cur['quantity'])
					{
						$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $cur[defence_id]");
						$qty -= $cur['quantity'];
						db_op_result($debug_query,__LINE__,__FILE__);

						$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] SET quantity = $qty where defence_id = $row[defence_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						playerlog($cur[player_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$targetdeftype|$row[sector_id]");
						playerlog($row[player_id], LOG_DEFS_DESTROYED, "$cur[quantity]|$deftype|$row[sector_id]");
					}else{
						$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=quantity - $qty WHERE defence_id = $cur[defence_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						playerlog($cur[player_id], LOG_DEFS_DESTROYED, "$qty|$targetdeftype|$row[sector_id]");
						playerlog($row[player_id], LOG_DEFS_DESTROYED, "$qty|$deftype|$row[sector_id]");
						$qty = 0;
					}
					$result2->MoveNext();
				}
			}
			$result1->MoveNext();
		}
		$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE quantity <= 0");
		db_op_result($debug_query,__LINE__,__FILE__);	  
	}
}

function showinfo($team_id,$isowner)
{
	global $thisplayer_info, $invite_info, $l_team_cancelinv, $team, $l_team_coord, $l_team_member, $l_options, $l_team_ed, $l_team_inv, $l_team_leave, $l_team_members, $l_score, $l_team_noinvites, $l_team_pending;
	global $l_team_noinvite, $l_team_ifyouwant, $l_team_tocreate, $l_clickme, $l_team_injoin, $l_team_tojoin, $l_team_reject, $l_team_or;
	global $db, $dbtables, $l_team_eject;
	global $color_line1, $color_line2, $smarty;

	$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$team[id]");
	$teamstuff = $result_team->fields;
	/* Heading */
	$smarty->assign("teamicon", $teamstuff['icon']);
	$smarty->assign("teamname", $team['team_name']);
	$smarty->assign("teamdescription", $team['description']);
	$smarty->assign("playerteammatch", $thisplayer_info['team'] == $team['id']);
	if ($thisplayer_info['team'] == $team['id'])
	{
		$smarty->assign("isplayercreator", $thisplayer_info['player_id'] == $team['creator']);
		$smarty->assign("playerteamid", $thisplayer_info['team']);
		$smarty->assign("l_team_ed", $l_team_ed);
		$smarty->assign("l_team_inv", $l_team_inv);
		$smarty->assign("l_team_cancelinv", $l_team_cancelinv);
		$smarty->assign("l_team_leave", $l_team_leave);
	}
	$smarty->assign("teaminvite", $thisplayer_info['team_invite']);
	$smarty->assign("l_team_noinvite", $l_team_noinvite);
	$smarty->assign("l_team_ifyouwant", $l_team_ifyouwant);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("l_team_tocreate", $l_team_tocreate);
	$smarty->assign("l_team_injoin", $l_team_injoin);
	$smarty->assign("inviteinfo", $invite_info['team_name']);
	$smarty->assign("l_team_tojoin", $l_team_tojoin);
	$smarty->assign("l_team_or", $l_team_or);
	$smarty->assign("l_team_reject", $l_team_reject);

	/* Main table */
	$result_zone = $db->Execute("SELECT * FROM $dbtables[zones] WHERE owner=$team_id and team_zone='Y'");
	$zone = $result_zone->fields;

	if($zone['zone_color'] == "#000000")
		$zonecolor = "#400040";
	else $zonecolor = $zone['zone_color'];
	
	$smarty->assign("zonecolor", $zonecolor);
	$smarty->assign("l_team_members", $l_team_members);
	$smarty->assign("color_line2", $color_line2);

	$count = 0;
	$result  = $db->Execute("SELECT * FROM $dbtables[players] WHERE team=$team_id");
	while (!$result->EOF) {
		$member = $result->fields;
		$teammember[$count] = $member['character_name'];
		$memberscore[$count] = NUMBER($member['score']);
		$memberowner[$count] = ($isowner && ($member['player_id'] != $thisplayer_info['player_id']));
		if ($isowner && ($member['player_id'] != $thisplayer_info['player_id'])) {
			$memberid[$count] = $member['player_id'];
		} else {
			$iscreator[$count] = $member['player_id'] == $team['creator'];
		}
		$count++;
		$result->MoveNext();
	}

	$smarty->assign("teammember", $teammember);
	$smarty->assign("memberscore", $memberscore);
	$smarty->assign("memberowner", $memberowner);
	$smarty->assign("memberid", $memberid);
	$smarty->assign("iscreator", $iscreator);
	$smarty->assign("teamcount", $count);
	$smarty->assign("l_score", $l_score);
	$smarty->assign("l_team_coord", $l_team_coord);
	$smarty->assign("l_team_eject", $l_team_eject);
	$smarty->assign("l_team_pending", $l_team_pending);

	/* Displays for members name */
	$count = 0;
	$res = $db->Execute("SELECT player_id,character_name FROM $dbtables[players] WHERE team_invite=$team_id");
	if ($res->RecordCount() > 0) {
		while (!$res->EOF) {
			$who = $res->fields;
			$membername[$count] = $who['character_name'];
			$count++;
			$res->MoveNext();
		}
	}
	$smarty->assign("membercount", $count);
	$smarty->assign("membername", $membername);
	$smarty->assign("l_team_noinvites", $l_team_noinvites);
}

switch ($command) {
	 case 1:	 // INFO on sigle team
		showinfo($team_id, 0);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-info.tpl");
		break;

	 case 2:	 // LEAVE
		if (!isset($confirmleave) || ($confirmleave == '') || (!$confirmleave)){
			$smarty->assign("confirmleave", 0);
			$smarty->assign("l_team_confirmleave", $l_team_confirmleave);
			$smarty->assign("teamname", $team['team_name']);
			$smarty->assign("command", $command);
			$smarty->assign("team_id", $team_id);
			$smarty->assign("l_yes", $l_yes);
			$smarty->assign("l_no", $l_no);
		}else{
			$smarty->assign("confirmleave", $confirmleave);
			if ($confirmleave == 1) {
				$res = $db->Execute("SELECT COUNT(*) as number_of_members
							FROM $dbtables[players]
							WHERE team = $team_id");
				db_op_result($res,__LINE__,__FILE__);
				$smarty->assign("number_of_members", $res->fields['number_of_members']);
				if ($res->fields['number_of_members'] == 1) {
// AATrade delete from forums
					$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$thisplayer_info[player_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
// AATrade get forum id
		 			$debug_query = $db->Execute("SELECT forum_id FROM $dbtables[forums] WHERE teams=$thisplayer_info[player_id]");
			  		db_op_result($debug_query,__LINE__,__FILE__);
				 	$forum_id = $debug_query->fields['forum_id'];
// AATrade delete topics and posts
					$debug_query = $db->Execute("DELETE FROM $dbtables[topics] WHERE forum_id=$forum_id");
					db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("DELETE FROM $dbtables[posts] WHERE forum_id=$forum_id");
					db_op_result($debug_query,__LINE__,__FILE__);
		 			$debug_query = $db->Execute("DELETE FROM $dbtables[posts_text] WHERE forum_id=$forum_id");
			  		db_op_result($debug_query,__LINE__,__FILE__);
// AATrade delete forum
					$debug_query = $db->Execute("DELETE FROM $dbtables[forums] WHERE teams=$thisplayer_info[player_id]");
					db_op_result($debug_query,__LINE__,__FILE__);

		 			$debug_query = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=$team_id");
			  		db_op_result($debug_query,__LINE__,__FILE__);
				 	$team_name = $debug_query->fields['team_name'];

					$debug_query = $db->Execute("DELETE FROM $dbtables[teams] WHERE id=$team_id");
					db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("DELETE FROM $dbtables[zones] WHERE owner=$team_id and team_zone='Y'");
					db_op_result($debug_query,__LINE__,__FILE__);
					$time = date("Y-m-d H:i:s");
					$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $thisplayer_info[player_id], 'last player', '$time', '$team_name')");
					db_op_result($debug_query,__LINE__,__FILE__);

		  			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0' WHERE player_id='$thisplayer_info[player_id]'");
			  		db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE team_invite=$team_id");
					db_op_result($debug_query,__LINE__,__FILE__);
					$res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$thisplayer_info[player_id] AND base='Y'");
					$i=0;
					if($res){
						while(!$res->EOF){
							$row = $res->fields;
				 			$sectors[$i] = $row['sector_id'];
					  		$i++;
							$res->MoveNext();
						}
					}

					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=0 WHERE owner=$thisplayer_info[player_id]");
					db_op_result($debug_query,__LINE__,__FILE__);

					if(!empty($sectors)){
		  				foreach($sectors as $sector){
							calc_ownership($sector);
						}
					}
			 		defence_vs_defence($playerinfo['player_id']);
				  	kick_off_planet($playerinfo['player_id'],$team_id);

					$l_team_onlymember = str_replace("[team_name]", "<b>$team[team_name]</b>", $l_team_onlymember);
					$smarty->assign("l_team_onlymember", $l_team_onlymember);
					playerlog($playerinfo['player_id'], LOG_TEAM_LEAVE, "$team[team_name]");
				} else {
					$smarty->assign("iscreator", ($team['creator'] == $playerinfo['player_id']));
					if ($team['creator'] == $playerinfo['player_id']) {
						$smarty->assign("l_team_youarecoord", $l_team_youarecoord);
						$smarty->assign("teamname", $team['team_name']);
						$smarty->assign("l_team_relinq", $l_team_relinq);
						$smarty->assign("team_id", $team_id);
						$smarty->assign("command", $command);
						$smarty->assign("l_team_newc", $l_team_newc);
						$smarty->assign("l_team_onlymember", $l_team_onlymember);
						$count = 0;
					 	$res = $db->Execute("SELECT character_name,player_id FROM $dbtables[players] WHERE team=$team_id ORDER BY character_name ASC");
						while(!$res->EOF) {
							$row = $res->fields;
							if ($row['player_id'] != $team['creator']){
								$playerid[$count] = $row['player_id'];
								$playername[$count] = $row['character_name'];
								$count++;
							}
							$res->MoveNext();
	 					}
						$smarty->assign("count", $count);
						$smarty->assign("playerid", $playerid);
						$smarty->assign("playername", $playername);
						$smarty->assign("l_submit", $l_submit);
					} else {
//AATrade delete from forums
						$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$thisplayer_info[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

			 			$debug_query = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=$team_id");
				  		db_op_result($debug_query,__LINE__,__FILE__);
					 	$team_name = $debug_query->fields['team_name'];

						$stamp = date("Y-m-d H:i:s");
						$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $team_id, 'left team', '$stamp', '$team_name')");
						db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0', last_team=$team_id, left_team_time='$stamp' WHERE player_id='$thisplayer_info[player_id]'");
	  					db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE team_invite=$team_id");
						db_op_result($debug_query,__LINE__,__FILE__);
			 			$res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$thisplayer_info[player_id] AND base='Y' AND team!=0");
				  		$i=0;
						while(!$res->EOF){
							$sectors[$i] = $res->fields['sector_id'];
							$i++;
							$res->MoveNext();
						}

						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=0 WHERE owner=$thisplayer_info[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						if(!empty($sectors)){
							foreach($sectors as $sector){
								calc_ownership($sector);
							}
						}

						$smarty->assign("l_team_youveleft", $l_team_youveleft);
						$smarty->assign("teamname", $team['team_name']);
						defence_vs_defence($playerinfo['player_id']);
						kick_off_planet($playerinfo['player_id'],$team_id);
						playerlog($playerinfo['player_id'], LOG_TEAM_LEAVE, "$team[team_name]");
		 				playerlog($team['creator'], LOG_TEAM_NOT_LEAVE, "$thisplayer_info[character_name]");
					}
				}
			}

			if ($confirmleave == 2) { // owner of a team is leaving and set a new owner
				$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=$newcreator");
				$newcreatorname = $res->fields;
				$smarty->assign("l_team_youveleft", $l_team_youveleft);
				$smarty->assign("teamname", $team['team_name']);
				$smarty->assign("l_team_relto", $l_team_relto);
				$smarty->assign("newcreator", $newcreatorname['character_name']);
		  		$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$thisplayer_info[player_id]");
			 	db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[forums] SET teams=$newcreator WHERE teams=$thisplayer_info[player_id]");
			 	db_op_result($debug_query,__LINE__,__FILE__);

	 			$debug_query = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=$team_id");
		  		db_op_result($debug_query,__LINE__,__FILE__);
			 	$team_name = $debug_query->fields['team_name'];

				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $team_id, 'left team', '$stamp', '$team_name')");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0', last_team=$team_id, left_team_time='$stamp' WHERE player_id='$thisplayer_info[player_id]'");
				db_op_result($debug_query,__LINE__,__FILE__);
	 			$debug_query = $db->Execute("UPDATE $dbtables[players] SET team=$newcreator WHERE team=$thisplayer_info[player_id]");
		 		db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE team_invite=$thisplayer_info[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[planets] set team=$newcreator WHERE team = $thisplayer_info[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[teams] SET creator=$newcreator, id=$newcreator WHERE id=$thisplayer_info[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[zones] SET owner=$newcreator WHERE team_zone='Y' AND owner=$thisplayer_info[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$res = $db->Execute("SELECT DISTINCT sector_id FROM $dbtables[planets] WHERE owner=$thisplayer_info[player_id] AND base='Y' AND team!=0");
				$i=0;
				while(!$res->EOF){
					$sectors[$i] = $res->fields['sector_id'];
					$i++;
					$res->MoveNext();
				}
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team=0 WHERE owner=$thisplayer_info[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				if(!empty($sectors)){
					foreach($sectors as $sector){
						calc_ownership($sector);
					}
				}

				playerlog($playerinfo[player_id], LOG_TEAM_NEWLEAD, "$team[team_name]|$newcreatorname[character_name]");
				playerlog($newcreator, LOG_TEAM_LEAD,"$team[team_name]");
			}
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-leave.tpl");
		break;

	 case 3: // JOIN
		$smarty->assign("team_join_count", $thisplayer_info['team_join_count']);
		$smarty->assign("max_team_changes", $max_team_changes);
		if($thisplayer_info['team_join_count'] < $max_team_changes){
			$smarty->assign("playerteam", $thisplayer_info['team']);
			if ($thisplayer_info['team'] <> 0)
			{
				$smarty->assign("l_team_leavefirst", $l_team_leavefirst);
			}
			else
			{
				$smarty->assign("isplayerteaminvite", ($thisplayer_info['team_invite'] == $team_id));
				if ($thisplayer_info['team_invite'] == $team_id)
				{
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET team=$team_id,team_invite=0, team_join_count=team_join_count+1 WHERE player_id=$thisplayer_info[player_id]");
					db_op_result($debug_query,__LINE__,__FILE__);

					$smarty->assign("l_team_welcome", $l_team_welcome);
					$smarty->assign("teamname", $team['team_name']);
					playerlog($thisplayer_info['player_id'], LOG_TEAM_JOIN, "$team[team_name]");
					playerlog($team['creator'], LOG_TEAM_NEWMEMBER, "$team[team_name]|$thisplayer_info[character_name]");

// AATrade add player to forums
					$time = date("Y-m-d H:i:s");
					$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $team_id, 'joined team', '$time', '$team[team_name]')");
					db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("INSERT INTO $dbtables[fplayers] (player_id, playername, signupdate, currenttime) values ($thisplayer_info[player_id], '$thisplayer_info[character_name]', '$time', '$time')");
					db_op_result($debug_query,__LINE__,__FILE__);
				}else{
					$smarty->assign("l_team_noinviteto", $l_team_noinviteto);
				}
			}
		}
		else
		{
			$smarty->assign("l_team_cantjoin", $l_team_cantjoin);
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-join.tpl");
		break;

	 case 4:
		 // Cancel Invitation

		$res = $db->Execute("SELECT player_id,character_name FROM $dbtables[players] WHERE team_invite=$team_id ORDER BY character_name ASC");
		$total = $res->RecordCount();
		$smarty->assign("canceled", (!isset($canceled) || ($canceled == '') || !($canceled)));
		if (!isset($canceled) || ($canceled == '') || !($canceled))
		{
			$smarty->assign("command", $command);
			$smarty->assign("team_id", $team_id);
			$smarty->assign("l_team_cancelplayer", $l_team_cancelplayer);
			$count = 0;
			while(!$res->EOF) {
				$row = $res->fields;
				$playerid[$count] = $row['player_id'];
				$playername[$count] = $row['character_name'];
				$count++;
				$res->MoveNext();
			}
			$smarty->assign("count", $count);
			$smarty->assign("l_submit", $l_submit);
			$smarty->assign("playerid", $playerid);
			$smarty->assign("playername", $playername);
		} else {
			$smarty->assign("isplayerteam", ($thisplayer_info['team'] == $team_id));
			if($thisplayer_info['team'] == $team_id)
			{
				$res = $db->Execute("SELECT character_name,team_invite FROM $dbtables[players] WHERE player_id=$who");
				$newpl = $res->fields;
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE player_id=$who");
				db_op_result($debug_query,__LINE__,__FILE__);
				$smarty->assign("l_team_cancelinvites", $l_team_cancelinvites);
				$smarty->assign("playername", $newpl['character_name']);
				playerlog($who,LOG_TEAM_CANCEL, "$team[team_name]");
			}else{
				$smarty->assign("l_team_notyours", $l_team_notyours);
			}
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-cancelinvite.tpl");
		break;

	 case 5: // Eject member
		$smarty->assign("isplayerteam", ($thisplayer_info[team] == $team[id]));
		if ($thisplayer_info[team] == $team[id])
		{
			$who = stripnum($who);	
			$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$who");
			$whotoexpel = $result->fields;
			$smarty->assign("confirmed", $confirmed);
			if (!$confirmed) {
				$smarty->assign("l_team_ejectsure", $l_team_ejectsure);
				$smarty->assign("playername", $whotoexpel['character_name']);
				$smarty->assign("command", $command);
				$smarty->assign("who", $who);
				$smarty->assign("l_yes", $l_yes);
				$smarty->assign("l_no", $l_no);
			} else {
				/*
				check whether the player we are ejecting might have already left in the meantime
				should go here	 if ($whotoexpel[team] ==
				*/
	 			$debug_query = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=$thisplayer_info[team]");
		  		db_op_result($debug_query,__LINE__,__FILE__);
			 	$team_name = $debug_query->fields['team_name'];

				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team='0' WHERE owner='$who'");
				db_op_result($debug_query,__LINE__,__FILE__);

				$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($who, $whotoexpel[team], 'kicked', '$stamp', '$team_name')");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='0', last_team=$whotoexpel[team], left_team_time='$stamp'  WHERE player_id='$who'");
				db_op_result($debug_query,__LINE__,__FILE__);

				$result2 = $db->Execute("SELECT * from $dbtables[ships] where on_planet = 'Y' and ship_id = $whotoexpel[currentship]");
				db_op_result($result2,__LINE__,__FILE__);

				if ($result2 > 0)
				{
					$cur = $result2->fields;
					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet = 'N',planet_id = '0' WHERE ship_id='$cur[ship_id]'");
					db_op_result($debug_query,__LINE__,__FILE__);

					playerlog($cur['player_id'], LOG_PLANET_EJECT, "$cur[sector]|$whotoexpel[character_name]");
				}

				// AATrade delete from forums
				$debug_query = $db->Execute("DELETE FROM $dbtables[fplayers] WHERE player_id=$who");
				db_op_result($debug_query,__LINE__,__FILE__);
				playerlog($who, LOG_TEAM_KICK, "$team[team_name]");
				$smarty->assign("l_team_ejected", $l_team_ejected);
				$smarty->assign("playername", $whotoexpel['character_name']);
			}
		}else{
			adminlog(LOG_CHEAT_TEAM, "$thisplayer_info[character_name]|$ip");
			$smarty->assign("l_team_cheater", $l_team_cheater);
			$smarty->assign("l_team_punishment", $l_team_punishment);
			$smarty->assign("l_die_vapor", $l_die_vapor);
			$smarty->assign("l_die_please", $l_die_please);
			db_kill_player($thisplayer_info['player_id'], 0, 0);
			cancel_bounty($thisplayer_info['player_id']);
			adminlog(LOG_ADMIN_HARAKIRI, "$thisplayer_info[character_name]|$ip");
			playerlog($thisplayer_info[player_id], LOG_HARAKIRI, "$ip");
		} 
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-ejectmember.tpl");
		break;

	 case 6: // Create Team
		$smarty->assign("team_join_count", $thisplayer_info['team_join_count']);
		$smarty->assign("max_team_changes", $max_team_changes);
		if($thisplayer_info['team_join_count'] < $max_team_changes){
			$smarty->assign("playerteam", $thisplayer_info['team']);
			if($thisplayer_info['team'] == 0){
				$smarty->assign("isteamname", (!isset($teamname) || ($teamname =='')));
				if (!isset($teamname) || ($teamname ==''))
	 			{
					$smarty->assign("l_team_entername", $l_team_entername);
					$smarty->assign("command", $command);
					$smarty->assign("l_team_enterdesc", $l_team_enterdesc);
					$smarty->assign("l_submit", $l_submit);
					$smarty->assign("l_reset", $l_reset);
	 			} else {
		  			$teamname = htmlspecialchars(trim($teamname),ENT_QUOTES);
					$teamdesc = htmlspecialchars(trim($teamdesc),ENT_QUOTES);
					$res = $db->Execute("SELECT * FROM $dbtables[teams] WHERE LOWER(team_name)=LOWER('$teamname')");
					$smarty->assign("count", $res->RecordCount());
					if($res->RecordCount() == 0){
						$debug_query = $db->SelectLimit("SELECT zone_color FROM $dbtables[zones] WHERE owner=$thisplayer_info[player_id] and team_zone='N'");
						db_op_result($debug_query,__LINE__,__FILE__);
						$zonecolor = $debug_query->fields['zone_color'];
						$debug_query = $db->Execute("INSERT INTO $dbtables[teams] (id,creator,team_name,description, icon) VALUES ('$thisplayer_info[player_id]','$thisplayer_info[player_id]','$teamname','$teamdesc', 'default_icon.gif')");
			 			db_op_result($debug_query,__LINE__,__FILE__);

						$debug_query = $db->Execute("INSERT INTO $dbtables[zones] (zone_name,owner,team_zone,allow_attack,allow_planetattack,allow_warpedit,allow_planet,allow_trade,allow_defenses,max_hull,zone_color) VALUES('$teamname\'s Empire', $thisplayer_info[player_id], 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0, '$zonecolor')");
		 				db_op_result($debug_query,__LINE__,__FILE__);

						$time = date("Y-m-d H:i:s");
						$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $thisplayer_info[player_id], 'created team', '$time', '$teamname')");
						db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET team='$thisplayer_info[player_id]', team_join_count=team_join_count+1 WHERE player_id='$thisplayer_info[player_id]'");
		 				db_op_result($debug_query,__LINE__,__FILE__);

// AATrade create team forum
						$debug_query = $db->Execute("INSERT INTO $dbtables[forums] (forum_name, forum_desc, private, teams) values ('$teamname', '$teamdesc', 1, $thisplayer_info[player_id])");
			 			db_op_result($debug_query,__LINE__,__FILE__);

// AATrade add player to forums
						$time = date("Y-m-d H:i:s");
			 			$debug_query = $db->Execute("INSERT INTO $dbtables[fplayers] (player_id, playername, signupdate, currenttime, admin) values ($thisplayer_info[player_id], '$thisplayer_info[character_name]', '$time', '$time', 1)");
				  		db_op_result($debug_query,__LINE__,__FILE__);

						$smarty->assign("l_team", $l_team);
						$smarty->assign("teamname", $teamname);
						$smarty->assign("l_team_hcreated", $l_team_hcreated);
		 				playerlog($thisplayer_info['player_id'], LOG_TEAM_CREATE, "$teamname");
					}
					else
					{
						$smarty->assign("l_team_nocreatesamename", $l_team_nocreatesamename);
					}
				}
			}else{
				$smarty->assign("l_team_leavefirst", $l_team_leavefirst);
			}
		}
		else
		{
			$smarty->assign("l_team_cantcreate", $l_team_cantcreate);
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-createteam.tpl");
		break;

	 case 7: // INVITE player
		$result  = $db->Execute("SELECT * FROM $dbtables[players] WHERE team=$team_id");
		$total = $result->RecordCount();

		$res = $db->Execute("SELECT player_id,character_name FROM $dbtables[players] WHERE team_invite=$team_id");
		$total = $total + $res->RecordCount();
		$smarty->assign("notteamlimit", ($total < $team_limit));
		if($total < $team_limit){
			$smarty->assign("invited", (!isset($invited) || ($invited == '') || !($invited)));
			if (!isset($invited) || ($invited == '') || !($invited))
			{
				$smarty->assign("command", $command);
				$smarty->assign("team_id", $team_id);
				$smarty->assign("l_team_selectp", $l_team_selectp);
				$smarty->assign("command", $command);
				$count = 0;
				$res = $db->Execute("SELECT character_name,player_id FROM $dbtables[players] WHERE team<>$team_id and player_id > 3 ORDER BY character_name ASC");
				while(!$res->EOF) {
					$row = $res->fields;
					if ($row[player_id] != $team['creator']){
						$playerid[$count] = $row['player_id'];
						$playername[$count] = $row['character_name'];
					}
					$count++;
					$res->MoveNext();
				}
				$smarty->assign("count", $count);
				$smarty->assign("l_submit", $l_submit);
				$smarty->assign("playerid", $playerid);
				$smarty->assign("playername", $playername);
			} else {
				$smarty->assign("team_join_count", $thisplayer_info['team_join_count']);
				$smarty->assign("max_team_changes", $max_team_changes);
				if($thisplayer_info['team_join_count'] < $max_team_changes){
					$smarty->assign("issameteam", ($thisplayer_info['team'] == $team_id));
					if($thisplayer_info['team'] == $team_id)
					{
						$res = $db->Execute("SELECT character_name,team_invite FROM $dbtables[players] WHERE player_id=$who");
						$newpl = $res->fields;
						$smarty->assign("team_invite", $newpl['team_invite']);
						if ($newpl['team_invite']) 
						{
							$l_team_isorry = str_replace("[name]", $newpl['character_name'], $l_team_isorry);
							$smarty->assign("l_team_isorry", $l_team_isorry);
						}else{
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=$team_id WHERE player_id=$who");
							db_op_result($debug_query,__LINE__,__FILE__);
							$smarty->assign("l_team_plinvted", $l_team_plinvted);
							playerlog($who,LOG_TEAM_INVITE, "$team[team_name]");
						}
					}else{
						$smarty->assign("l_team_notyours", $l_team_notyours);
					}
				}
				else
				{
					$smarty->assign("l_team_cantinvite", $l_team_cantinvite);
				}
			}
		}else{
			$smarty->assign("l_team_full", $l_team_full);
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-invite.tpl");
		break;

	 case 8: // REFUSE invitation
		$smarty->assign("l_team_refuse", $l_team_refuse);
		$smarty->assign("inviteteam_name", $invite_info['team_name']);
		$debug_query = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=$thisplayer_info[team_invite]");
  		db_op_result($debug_query,__LINE__,__FILE__);
	 	$team_name = $debug_query->fields['team_name'];

		$time = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($thisplayer_info[player_id], $thisplayer_info[team_invite], 'refused invite', '$time', '$team_name')");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET team_invite=0 WHERE player_id=$thisplayer_info[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		playerlog($team['creator'], LOG_TEAM_REJECT, "$thisplayer_info[character_name]|$invite_info[team_name]");
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-refuse.tpl");
		break;

	 case 9: // Edit Team
		$smarty->assign("teammatch", ($thisplayer_info['team'] == $team_id));
		if ($thisplayer_info['team'] == $team_id) {
			$smarty->assign("update", (!isset($update) || ($update == '')));
			if (!isset($update) || ($update == ''))
			{
				$smarty->assign("l_team_edname", $l_team_edname);
				$smarty->assign("swordfish", $swordfish);
				$smarty->assign("command", $command);
				$smarty->assign("team_id", $team_id);
				$smarty->assign("team_name", $team['team_name']);
				$smarty->assign("l_team_eddesc", $l_team_eddesc);
				$smarty->assign("description", $team['description']);
				$smarty->assign("l_submit", $l_submit);
				$smarty->assign("l_reset", $l_reset);
			} else {
				$teamname = htmlspecialchars(trim($teamname));
				$teamdesc = htmlspecialchars(trim($teamdesc));
				$res = $db->Execute("SELECT * FROM $dbtables[teams] WHERE LOWER(team_name)=LOWER('$teamname') and id!=$team_id");
				$smarty->assign("count", $res->RecordCount());
				if($res->RecordCount() == 0){
					$debug_query = $db->Execute("UPDATE $dbtables[teams] SET team_name='$teamname', description='$teamdesc' WHERE id=$team_id");
					db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("UPDATE $dbtables[zones] SET zone_name='$teamname\'s Empire' WHERE owner=$team_id and team_zone='Y'");
					db_op_result($debug_query,__LINE__,__FILE__);
					$smarty->assign("l_team", $l_team);
					$smarty->assign("teamname", $teamname);
					$smarty->assign("l_team_hasbeenr", $l_team_hasbeenr);
					/*
						Adding a log entry to all members of the renamed team
					*/
					$time = date("Y-m-d H:i:s");
					$debug_query = $db->Execute("INSERT INTO $dbtables[player_team_history] (player_id, history_team_id, info, left_team, history_team_name) values ($team_id, $team_id, 'edit team', '$time', '$teamname')");
					db_op_result($debug_query,__LINE__,__FILE__);

					$result_team_name = $db->Execute("SELECT player_id FROM $dbtables[players] WHERE team=$team_id AND player_id<>$thisplayer_info[player_id]");
					playerlog($thisplayer_info['player_id'], LOG_TEAM_RENAME, "$teamname");
					while (!$result_team_name->EOF) {
						$teamname_array = $result_team_name->fields;
						playerlog($teamname_array['player_id'], LOG_TEAM_M_RENAME, "$teamname");
						$result_team_name->MoveNext();
					}
				}
				else
				{
					$smarty->assign("l_team_noupdatesamename", $l_team_noupdatesamename);
				}
			}
		}else{
			$smarty->assign("l_team_error", $l_team_error);
		}
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-edit.tpl");
		break;

	 default:
		if (!$thisplayer_info['team']) {
			$smarty->assign("l_team_notmember", $l_team_notmember);
			$smarty->assign("l_team_noinvite", $l_team_noinvite);
			$smarty->assign("playerteamid", $thisplayer_info['team']);
			$smarty->assign("l_team_ifyouwant", $l_team_ifyouwant);
			$smarty->assign("l_team_tocreate", $l_team_tocreate);
			$smarty->assign("l_team_injoin", $l_team_injoin);
			$smarty->assign("l_team_tojoin", $l_team_tojoin);
			$smarty->assign("l_team_tocreate", $l_team_tocreate);
			$smarty->assign("l_team_reject", $l_team_reject);
			$smarty->assign("teaminvite", $thisplayer_info['team_invite']);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("inviteinfo", $invite_info['team_name']);
		} else {
			if ($thisplayer_info['team'] < 0) {
				$thisplayer_info['team'] = -$thisplayer_info['team'];
				$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$thisplayer_info[team]");
				$team_id = $result->fields;
				$smarty->assign("teamname", $team_id['team_name']);
				$smarty->assign("l_team_urejected", $l_team_urejected);
				$smarty->assign("l_clickme", $l_clickme);
				$smarty->assign("l_team_menu", $l_team_menu);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."teams-reject.tpl");
				break;
			}
			$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$thisplayer_info[team]");
			$team_id = $result->fields;
			if ($thisplayer_info['team_invite']) {
				$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$thisplayer_info[team_invite]");
				$whichinvitingteam = $result->fields;
			}
			$isowner = ($thisplayer_info['player_id'] == $team_id['creator']);
			showinfo($thisplayer_info['team'],$isowner);
		}

		$res = $db->Execute("SELECT * FROM $dbtables[teams]");
		$teams_count = $res->RecordCount();
		$smarty->assign("teams_count", $teams_count);
		if ($teams_count > 0) 
		{
			$smarty->assign("l_team_galax", $l_team_galax);
			$smarty->assign("color_header", $color_header);

			if ($type == "d") {
				$type = "a";
				$by = "ASC";
			} else {
				$type = "d";
				$by = "DESC";
			}

			$smarty->assign("type", $type);
			$smarty->assign("l_name", $l_name);
			$smarty->assign("l_team_members", $l_team_members);
			$smarty->assign("l_team_coord", $l_team_coord);
			$smarty->assign("l_score", $l_score);

			$sql_query = "SELECT $dbtables[players].character_name,
						COUNT(*) as number_of_members,
						SUM($dbtables[players].score) as total_score,
						$dbtables[teams].id,
						$dbtables[teams].team_name,
						$dbtables[teams].icon,
						$dbtables[teams].creator
						FROM $dbtables[players]
						LEFT JOIN $dbtables[teams] ON $dbtables[players].team = $dbtables[teams].id
						WHERE $dbtables[players].team = $dbtables[teams].id
						GROUP BY $dbtables[teams].team_name";
			/*
				Setting if the order is Ascending or descending, if any.
				Default is ordered by teams.team_name
			*/
			if ($order)
			{
				$sql_query = $sql_query ." ORDER BY " . $order . " $by";
			}
			$res = $db->Execute($sql_query);
			$count = 0;
			while (!$res->EOF) {
				$row = $res->fields;
				$teamlisticon[$count] = $row['icon'];
				$teamlistid[$count] = $row['id'];
				$teamlistname[$count] = $row['team_name'];
				$teamlistnumber[$count] = $row['number_of_members'];

				$sql_query_2 = "SELECT character_name FROM $dbtables[players] WHERE player_id = $row[id]";
				$res2 = $db->Execute($sql_query_2);
				$teamlistcname[$count] = $res2->fields['character_name'];

				$teamlistscore[$count] = NUMBER($row['total_score']);

				$count++;
				$res->MoveNext();
			}
			$smarty->assign("teamlistcname", $teamlistcname);
			$smarty->assign("teamlistscore", $teamlistscore);
			$smarty->assign("teamlisticon", $teamlisticon);
			$smarty->assign("teamlistid", $teamlistid);
			$smarty->assign("teamlistname", $teamlistname);
			$smarty->assign("teamlistnumber", $teamlistnumber);
			$smarty->assign("totalteamcount", $count);
			$smarty->assign("color", $color);
		}else{
			$smarty->assign("l_team_noteams", $l_team_noteams);
		}

		$smarty->assign("color_line1", $color_line1);
		$smarty->assign("color_line2", $color_line2);
		$smarty->assign("max_team_changes", $max_team_changes);
		$smarty->assign("team_join_count", $thisplayer_info['team_join_count']);
		$smarty->assign("l_team_allowedfront", $l_team_allowedfront);
		$smarty->assign("l_team_reachedlimit", $l_team_reachedlimit);
		$smarty->assign("l_team_allowedback", $l_team_allowedback);
		$smarty->assign("l_team_noteams", $l_team_noteams);

		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_team_menu", $l_team_menu);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."teams-default.tpl");
		break;
} // switch ($command)

include ("footer.php");
?>

