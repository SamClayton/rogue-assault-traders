<?php
//$title = "Administration";
//include ("header.php");

//bigtitle();

// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_prune.php

if (preg_match("/sched_prune.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

include ("languages/$langdir/lang_mail.inc");

if (!function_exists('cancel_fed_bounty')) {
	function cancel_fed_bounty($bounty_on)
	{
		global $db, $dbtables;
		$res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id");
		db_op_result($res,__LINE__,__FILE__);
		if ($res)
		{
			while (!$res->EOF)
			{
				$bountydetails = $res->fields;
				if ($bountydetails['placed_by'] == 0)
				{
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $bountydetails[placed_by]");
					db_op_result($debug_query,__LINE__,__FILE__);
					playerlog($bountydetails['placed_by'],LOG_BOUNTY_CANCELLED,"$bountydetails[amount]|$bountydetails[character_name]");
					$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
				$res->MoveNext();
			}
		}
	}
}

if (!function_exists('playerlog')) {
	function playerlog($sid, $log_type, $data = '')
	{
		global $db, $dbtables;

		// write log_entry to the player's log - identified by player's player_id - sid.
		if ($sid != '' && !empty($log_type))
		{
			$stamp = date("Y-m-d H:i:s");
			$data = addslashes($data);
			$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES($sid, $log_type, '$stamp', '$data')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

if (!function_exists('cancel_bounty')) {
	function cancel_bounty($bounty_on)
	{
		global $db, $dbtables;
		$res = $db->Execute("SELECT * FROM $dbtables[bounty],$dbtables[players] WHERE bounty_on = $bounty_on AND bounty_on = player_id");
		db_op_result($res,__LINE__,__FILE__);
		if ($res)
		{
			while (!$res->EOF)
			{
				$bountydetails = $res->fields;
				if ($bountydetails['placed_by'] != 0)
				{
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits = credits + $bountydetails[amount] WHERE player_id = $bountydetails[placed_by]");
					db_op_result($debug_query,__LINE__,__FILE__);
					playerlog($bountydetails['placed_by'],LOG_BOUNTY_CANCELLED,"$bountydetails[amount]|$bountydetails[character_name]");
					$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bountydetails[bounty_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
				$res->MoveNext();
			}
		}
	}
}

function delete_player($player_id){

	global $dbtables, $db;
	TextFlush (" Deleting player: $player_id<br>");

	TextFlush ("DELETE FROM $dbtables[zones] WHERE owner = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[zones] WHERE owner = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[zones] WHERE owner=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[traderoutes] WHERE owner = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[traderoutes] WHERE owner = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE owner = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[shoutbox] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[shoutbox] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[sector_defence] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[scan_log] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[scan_log] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[scan_log] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[message_block] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[message_block] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[message_block] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[probe] WHERE owner_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE owner_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[probe] WHERE owner_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[planet_log] WHERE player_id = $player_id or owner_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[planet_log] WHERE player_id = $player_id or owner_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[planet_log] WHERE player_id = $player_id or owner_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[movement_log] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[movement_log] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[movement_log] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[players] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[players] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[ships] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[ships] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[spies] WHERE owner_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE owner_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[spies] WHERE owner_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[dignitary] WHERE owner_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[dignitary] WHERE owner_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[dignitary] WHERE owner_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[autotrades] WHERE owner = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[autotrades] WHERE owner = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[autotrades] WHERE owner = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[detect] WHERE owner_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[detect] WHERE owner_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[detect] WHERE owner_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[igb_transfers] WHERE source_id = $player_id or dest_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[igb_transfers] WHERE source_id = $player_id or dest_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[igb_transfers] WHERE source_id = $player_id or dest_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[logs] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[logs] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[logs] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[ibank_accounts] WHERE player_id = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[ibank_accounts] WHERE player_id = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[ibank_accounts] WHERE player_id = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("DELETE FROM $dbtables[bounty] WHERE placed_by = $player_id<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[bounty] WHERE placed_by = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[bounty] WHERE placed_by = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("UPDATE $dbtables[planets] set owner=2, team=0, cargo_hull=0, cargo_power=0 WHERE owner = $player_id<br>");
	$debug_query = $db->Execute("UPDATE $dbtables[planets] set owner=2, team=0, cargo_hull=0, cargo_power=0 WHERE owner = $player_id");
//	$debug_query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE owner = $player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
//	echo $debug_query->recordcount()."<br>";

	TextFlush ("SELECT * FROM $dbtables[teams] WHERE creator = $player_id<br>");
	$debug_query2 = $db->Execute("SELECT * FROM $dbtables[teams] WHERE creator = $player_id");
	db_op_result($debug_query2,__LINE__,__FILE__);
	TextFlush ( "Created Team: ".$debug_query2->recordcount()."<br>");

	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_topics] WHERE post_player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_posts] WHERE post_player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	$debug_query = $db->Execute("DELETE FROM $dbtables[casino_posts_text] WHERE post_player_id=$player_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	while(!$debug_query2->EOF){

		TextFlush ("UPDATE $dbtables[players] set team=0 WHERE team = $player_id<br>");
		$query2 = $db->Execute("UPDATE $dbtables[players] set team=0 WHERE team = $player_id");
//		$query2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE team = $player_id");
		db_op_result($query2,__LINE__,__FILE__);
//		echo $query2->recordcount()."<br>";

		TextFlush ("UPDATE $dbtables[players] set team_invite=0 WHERE team_invite = $player_id<br>");
		$query2 = $db->Execute("UPDATE $dbtables[players] set team_invite=0 WHERE team_invite = $player_id");
//		$query2 = $db->Execute("SELECT * FROM $dbtables[players] WHERE team_invite = $player_id");
		db_op_result($query2,__LINE__,__FILE__);
//		echo $query2->recordcount()."<br>";

		TextFlush ("UPDATE $dbtables[planets] set team=0 WHERE team = $player_id<br>");
		$query2 = $db->Execute("UPDATE $dbtables[planets] set team=0 WHERE team = $player_id");
//		$query2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE team = $player_id");
		db_op_result($query2,__LINE__,__FILE__);
//		echo $query2->recordcount()."<br>";

		$debug_query2->MoveNext();
	}
	TextFlush ("<br>");

	TextFlush ("delete FROM $dbtables[teams] WHERE creator = $player_id<br>");
	$debug_query2 = $db->Execute("delete FROM $dbtables[teams] WHERE creator = $player_id");
	db_op_result($debug_query2,__LINE__,__FILE__);

	TextFlush ("delete FROM $dbtables[zones] WHERE owner = $player_id<br>");
	$debug_query2 = $db->Execute("delete FROM $dbtables[zones] WHERE owner = $player_id");
	db_op_result($debug_query2,__LINE__,__FILE__);
}

if($disable_pruning != 1){
	TextFlush ( "<br>Current: ".date("Y-m-d H:i:s"));
	$stamp = strtotime(date("Y-m-d H:i:s")) - ($retaindataduration * 86400);
	$prune_date = date("Y-m-d H:i:s", $stamp);
	TextFlush ( "<br>Prune News: $prune_date<br>");

	$stamp = strtotime(date("Y-m-d H:i:s")) - ($retainnonplayers * 86400);
	$nonplayer_date = date("Y-m-d H:i:s", $stamp);
	TextFlush ( "Delete Non Player Date: $nonplayer_date<br><br>");

//$stamp = strtotime(date("Y-m-d H:i:s")) - (($retainnonplayers - 3) * 86400);
//$warning_date = date("Y-m-d H:i:s", $stamp);
//echo "Warning Date: $warning_date<br>";

	$stamp = strtotime(date("Y-m-d H:i:s")) - ($retaindestroyedplayers * 86400);
	$delay_date = date("Y-m-d H:i:s", $stamp);
	TextFlush ( "Destroy Delay Date: $delay_date<br><br>");

	$stamp = strtotime(date("Y-m-d H:i:s")) - ($retainlogsduration * 86400);
	$log_date = date("Y-m-d H:i:s", $stamp);
	TextFlush ( "Delete Log Date: $log_date<br><br>");

// Prune all news past the data retention period
	TextFlush ("DELETE FROM $dbtables[news] WHERE date < '$prune_date'<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[news] WHERE date < '$prune_date'");
//$debug_query = $db->Execute("SELECT * FROM $dbtables[news] WHERE date < '$prune_date'");
	db_op_result($debug_query,__LINE__,__FILE__);
//echo $debug_query->recordcount()."<br><br>";

// Prune all logs past the data retention period
	TextFlush ("DELETE FROM $dbtables[logs] WHERE time < '$log_date'<br>");
	$debug_query = $db->Execute("DELETE FROM $dbtables[logs] WHERE time < '$log_date'");
//$debug_query = $db->Execute("SELECT * FROM $dbtables[logs] WHERE time < '$prune_date'");
	db_op_result($debug_query,__LINE__,__FILE__);
//echo $debug_query->recordcount()."<br><br>";

// Prune all players who haven't played in the data retention period
	TextFlush ("SELECT * FROM $dbtables[players] WHERE last_login < '$nonplayer_date'<br>");
	$debug_query = $db->Execute("SELECT * FROM $dbtables[players] WHERE last_login < '$nonplayer_date' and player_id > 3 order by player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	TextFlush ( "Total Players Date Pruned: ".$debug_query->recordcount()."<br><br>");

	while(!$debug_query->EOF){

		if($enable_profilesupport == 1){
			if ((isset($debug_query->fields['profile_name'])) && ($debug_query->fields['profile_name'] != ''))
			{
				$gm_url = $_SERVER['HTTP_HOST'] . $gamepath;
				$gm_all = "&name=" . $debug_query->fields['profile_name'] .
					  "&password=" . $debug_query->fields['profile_password'] .
					  "&server_url=" . rawurlencode($gm_url);

				$url = "http://profiles.aatraders.com/update_current.php?" . $gm_all;

				TextFlush ( "\n\n<!--" . $url . "-->\n\n");

				$i = @file($url);
			}
		}
		cancel_bounty($debug_query->fields['player_id']);
		cancel_fed_bounty($debug_query->fields['player_id']);
		delete_player($debug_query->fields['player_id']);

		$junkold = explode("/", $debug_query->fields['avatar']);
		$galleryold = $junkold[0];
		$pictureold = $junkold[1];
		if($galleryold == "uploads"){
			@unlink($gameroot."images/avatars/uploads/$pictureold");
		}

		$debug_query->MoveNext();
	}

// Prune all players who have died completely
	TextFlush ("SELECT DISTINCT $dbtables[ships].player_id, $dbtables[ships].avatar FROM $dbtables[ships], $dbtables[players] WHERE $dbtables[ships].player_id=$dbtables[players].player_id and $dbtables[ships].destroyed='Y' and $dbtables[players].last_login < '$delay_date' and $dbtables[players].player_id > 3 order by $dbtables[ships].player_id<br>");
	$debug_query = $db->Execute("SELECT DISTINCT $dbtables[ships].player_id FROM $dbtables[ships], $dbtables[players] WHERE $dbtables[ships].player_id=$dbtables[players].player_id and $dbtables[ships].destroyed='Y' and $dbtables[players].last_login < '$delay_date' and $dbtables[players].player_id > 3 order by $dbtables[ships].player_id");
	db_op_result($debug_query,__LINE__,__FILE__);
	TextFlush ( "Total Players Destroyed Pruned: ".$debug_query->recordcount()."<br><br>");

	while(!$debug_query->EOF){

		if($enable_profilesupport == 1){
			if ((isset($debug_query->fields['profile_name'])) && ($debug_query->fields['profile_name'] != ''))
			{
				$gm_url = $_SERVER['HTTP_HOST'] . $gamepath;
				$gm_all = "&name=" . $debug_query->fields['profile_name'] .
					  "&password=" . $debug_query->fields['profile_password'] .
					  "&server_url=" . rawurlencode($gm_url);

				$url = "http://profiles.aatraders.com/update_current.php?" . $gm_all;

				TextFlush ( "\n\n<!--" . $url . "-->\n\n");

				$i = @file($url);
			}
		}
		cancel_bounty($debug_query->fields['player_id']);
		cancel_fed_bounty($debug_query->fields['player_id']);
		delete_player($debug_query->fields['player_id']);

		$junkold = explode("/", $debug_query->fields['avatar']);
		$galleryold = $junkold[0];
		$pictureold = $junkold[1];
		if($galleryold == "uploads"){
			@unlink($gameroot."images/avatars/uploads/$pictureold");
		}

		$debug_query->MoveNext();
	}
}else{
	TextFlush ( "Auto Pruning Disabled.");
}

$multiplier = 0; //no use to run this again

//include ("footer.php");
?>
