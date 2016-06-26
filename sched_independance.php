<?php

if (preg_match("/sched_independance.php/i", $_SERVER['PHP_SELF'])) {
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('insert_news')) {
	function insert_news($data, $user_id, $news_type)
	{
		global $db, $dbtables;
		$total = 1;

		$result2 = $db->Execute("SELECT * from $dbtables[news] order by news_id DESC");
		$newsinfo = $result2->fields;
//print "$newsinfo[data] - $data<br>$newsinfo[news_type]<br>$newsinfo[user_id]<br>";
		if($newsinfo['data'] == $data and $newsinfo['news_type'] == $news_type and $newsinfo['user_id'] == $user_id){
			$total = $newsinfo['total'] + 1;
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("UPDATE $dbtables[news] set total='$total', date='$stamp' where news_id=$newsinfo[news_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("INSERT INTO $dbtables[news] (data, total, user_id, date, news_type) VALUES ('$data', '$total', '$user_id', '$stamp', '$news_type')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

include_once ("languages/$langdir/lang_global_funcs.inc");

if (!function_exists('calc_ownership')) {
	function calc_ownership($sector)
	{
		global $min_bases_to_own;
		global $l_global_warzone, $l_global_nzone, $l_global_team, $l_global_player;
		global $l_global_nochange;
		global $db, $dbtables;

		$res = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$sector");
		db_op_result($res,__LINE__,__FILE__);
		$zone = $res->fields;

		if($zone['zone_id'] == 3 or $zone['zone_id'] == 2)
				return $l_global_nzone;

		$res = $db->Execute("SELECT owner, team FROM $dbtables[planets] WHERE sector_id=$sector AND base='Y'");
		db_op_result($res,__LINE__,__FILE__);

		$res2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id=$sector");
		db_op_result($res2,__LINE__,__FILE__);

		if ($res > 0)
		{
			$num_bases = $res->RecordCount();
		}
		else
		{
			$num_bases = 0;
		}

		$i = 0;
		if ($num_bases > 0)
		{
			while (!$res->EOF)
			{
				$bases[$i] = $res->fields;
				$i++;
				$res->MoveNext();
			}
		}
		else
		{
//			$result = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
//			$sectorinfo = $result->fields;
//			if ($sectorinfo['zone_id'] > 2) // 1 is unowned, so we dont need to redo it. 2 is fed space, and protected.
//			{
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector AND zone_id > '2' ");
				db_op_result($debug_query,__LINE__,__FILE__);
//			}
				return $l_global_nzone;
		}

		$owner_num = 0;
		foreach ($bases as $curbase)
		{
			$curteam = -1;
			$curship = -1;
			$loop = 0;
			while ($loop < $owner_num)
			{
				if ($curbase['team'] != 0)
				{
					if ($owners[$loop]['type'] == 'C')
					{
						if ($owners[$loop]['id'] == $curbase[team])
						{
							$curteam = $loop;
							$owners[$loop]['num']++;
						}
					}
				}

				if ($owners[$loop]['type'] == 'S')
				{
					if ($owners[$loop]['id'] == $curbase['owner'])
					{
						$curship=$loop;
						$owners[$loop]['num']++;
					}
				}
				$loop++;
			}

			if ($curteam == -1)
			{
				if ($curbase['team'] != 0)
				{
					$curteam = $owner_num;
					$owner_num++;
					$owners[$curteam]['type'] = 'C';
					$owners[$curteam]['num'] = 1;
					$owners[$curteam]['id'] = $curbase['team'];
				}
			}

			if ($curship == -1)
			{
				if ($curbase['owner'] != 0)
				{
					$curship = $owner_num;
					$owner_num++;
					$owners[$curship]['type'] = 'S';
					$owners[$curship]['num'] = 1;
					$owners[$curship]['id'] = $curbase['owner'];
				}
			}
		}

		// We've got all the contenders with their bases.
		// Time to test for conflict

		$loop = 0;
		$nbteams = 0;
		$nbships = 0;

		while ($loop < $owner_num)
		{
			if ($owners[$loop]['type'] == 'C')
			{
				$nbteams++;
			}
			else
			{
				$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=" . $owners[$loop]['id']);
				db_op_result($res,__LINE__,__FILE__);		 
				if ($res && $res->RecordCount() != 0)
				{
					$curship = $res->fields;
					$ships[$nbships]=$owners[$loop]['id'];
					$steams[$nbships]=$curship['team'];
					$nbships++;
				}
			}
			$loop++;
		}

		// More than one team, war
		if ($nbteams > 1)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);

			return $l_global_warzone;
		}

		// More than one unallied ship, war
		$numunallied = 0;
		foreach ($steams as $team)
		{
			if ($team == 0)
			{
				$numunallied++;
			}
		}

		if ($numunallied > 1)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return $l_global_warzone;
		}

		// Unallied ship, another team present, war
		if ($numunallied > 0 && $nbteams > 0)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return $l_global_warzone;
		}

		// Unallied ship, another ship in a team, war
		if ($numunallied > 0)
		{
			$query = "SELECT team FROM $dbtables[players] WHERE (";
			$i = 0;
			foreach ($ships as $ship)
			{
				$query = $query . "player_id=$ship";
				$i++;

				if ($i != $nbships)
				{
					$query = $query . " OR ";
				}
				else
				{
					$query = $query . ")";
				}
			}

			$query = $query . " AND team!=0";
			$res = $db->Execute($query);
			db_op_result($res,__LINE__,__FILE__);

			if ($res->RecordCount() != 0)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=4 WHERE sector_id=$sector");
				db_op_result($debug_query,__LINE__,__FILE__);
				return $l_global_warzone;
			}
		}

		// Ok, all bases are allied at this point. Let's make a winner.
		$winner = 0;
		$i = 1;
		while ($i < $owner_num)
		{
			if ($owners[$i]['num'] > $owners[$winner]['num'])
			{
				$winner = $i;
			}
			elseif ($owners[$i]['num'] == $owners[$winner]['num'])
			{
				if ($owners[$i]['type'] == 'C')
				{
					$winner = $i;
				}
			}
			$i++;
		}

		$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$sector'");
		db_op_result($res,__LINE__,__FILE__);
		$num_planets = $res->RecordCount();

		$min_bases_to_own = round (($num_planets+1)/2);

		if ($owners[$winner]['num'] < $min_bases_to_own)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return $l_global_nzone;
		}

		if ($owners[$winner]['type'] == 'C')
		{
			$res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE team_zone='Y' && owner=" . $owners[$winner][id]);
			db_op_result($res,__LINE__,__FILE__);
			$zone = $res->fields;

			$res = $db->Execute("SELECT team_name FROM $dbtables[teams] WHERE id=" . $owners[$winner][id]);
			db_op_result($res,__LINE__,__FILE__);
			$team = $res->fields;

			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
			db_op_result($debug_query,__LINE__,__FILE__);
			return "$l_global_team $team[team_name]!";
		}
		else
		{
			$onpar = 0;
			foreach ($owners as $curowner)
			{
				if ($curowner['type'] == 'S' && $curowner['id'] != $owners[$winner]['id'] && $curowner['num'] == $owners[winner]['num'])
				$onpar = 1;
				break;
			}

			// Two allies have the same number of bases
			if ($onpar == 1)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=1 WHERE sector_id=$sector");
				db_op_result($debug_query,__LINE__,__FILE__);
				return $l_global_nzone;
			}
			else
			{
				$res = $db->Execute("SELECT zone_id FROM $dbtables[zones] WHERE team_zone='N' && owner=" . $owners[$winner]['id']);
				db_op_result($res,__LINE__,__FILE__);
				$zone = $res->fields;

				$res = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id=" . $owners[$winner]['id']);
				db_op_result($res,__LINE__,__FILE__);
				$ship = $res->fields;

				if($zone['zone_id'] == '' or $zone['zone_id'] == 0)
					$zone['zone_id'] = 1;

				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET zone_id=$zone[zone_id] WHERE sector_id=$sector");
				db_op_result($debug_query,__LINE__,__FILE__);
				return "$l_global_player $ship[character_name]!";
			}
		}
	}
}

if (!function_exists('adminlog')) {
	function adminlog($log_type, $data = '')
	{
		global $db, $dbtables;

		// Failures should be silent, since its the admin log.
		$silent = 1;

		// write log_entry to the admin log
		if (!empty($log_type))
		{
			$stamp = date("Y-m-d H:i:s");
			$data = addslashes($data);
			$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES(0, $log_type, '$stamp', '$data')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

TextFlush ( "<b>Planetary Independance</b><br><br>");

$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE owner != '0' and owner != '2' and owner != '3'");
db_op_result($debug_query,__LINE__,__FILE__);

$reccount = $debug_query->RecordCount();
$totalupdates = 0;

if ($debug_query && $reccount > 0){

	while (!$debug_query->EOF){
		$targetinfo = $debug_query->fields;
		$averagetechlvl = ($targetinfo['computer'] + $targetinfo['sensors'] + $targetinfo['beams'] + $targetinfo['torp_launchers'] + $targetinfo['shields'] + $targetinfo['jammer'] + $targetinfo['cloak']) / 7;

		if ($targetinfo['base'] == "N" or $averagetechlvl < mt_rand(1, 40)){
			if($targetinfo['base'] == "N"){
				$randomlevel = 20;
				$averagetechlvl = "Non-Based - Avg Tech = ".$averagetechlvl." in sector ".$targetinfo['sector_id'];
			}else{
				$randomlevel = 1;
				if($averagetechlvl < 30)
					$randomlevel = 2;
				if($averagetechlvl < 20)
					$randomlevel = 3;
				if($averagetechlvl < 10)
					$randomlevel = 4;
				$basedav = ($baseav + $averagetechlvl)/2;
				$averagetechlvl = "Based - Avg Tech = ".$averagetechlvl." in sector ".$targetinfo['sector_id'];
			}
			
			$randpick = mt_rand(0, 100);
//			echo "Rec: $totalrec, Picked: $randpick, RandLvl: $randomlevel, Other: $averagetechlvl<br>";
			if($randpick < $randomlevel){
			
				TextFlush ( "Planet $targetinfo[planet_id] ($targetinfo[name]) became independant.<BR>");
				if($targetinfo['base'] == "N"){
					$totalnon++;
				}else{
					$totalbased++;
					TextFlush ( "----- $averagetechlvl<br><br>");
				}

				$indiplanet[$totalupdates] = $targetinfo['planet_id'];
				$indiowner[$totalupdates] = $targetinfo['owner'];
				$indisector[$totalupdates] = $targetinfo['sector_id'];
				$indiadmin[$totalupdates] = "$targetinfo[name]|$targetinfo[planet_id]|$averagetechlvl";
				$indycomp[$totalupdates] = floor($targetinfo['computer'] * (rand(75, 100) * 0.01));
				$indysens[$totalupdates] = floor($targetinfo['sensors'] * (rand(75, 100) * 0.01));
				$indybeam[$totalupdates] = floor($targetinfo['beams'] * (rand(75, 100) * 0.01));
				$indytorp[$totalupdates] = floor($targetinfo['torp_launchers'] * (rand(75, 100) * 0.01));
				$indyshield[$totalupdates] = floor($targetinfo['shields'] * (rand(75, 100) * 0.01));
				$indyjammer[$totalupdates] = floor($targetinfo['jammer'] * (rand(75, 100) * 0.01));
				$indycloak[$totalupdates] = floor($targetinfo['cloak'] * (rand(75, 100) * 0.01));
				$totalupdates++;
			}else{
				if($targetinfo['base'] == "N"){
					$totalnon2++;
				}else{
					$totalbased2++;
				}
			}
		}
		$debug_query->MoveNext();
		$totalrec++;
	}
	
	if($totalupdates != 0){
		for($indi = 0; $indi < $totalupdates; $indi++){
			$debug_query = $db->Execute("SELECT * from $dbtables[ships] WHERE planet_id=$indiplanet[$indi] and on_planet='Y'");
			db_op_result($debug_query,__LINE__,__FILE__);
			if($debug_query->RecordCount() == 0){
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET planets_lost=planets_lost+1 WHERE player_id=$indiowner[$indi]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("DELETE from $dbtables[dignitary] WHERE planet_id = $indiplanet[$indi]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, owner = 2, team=0, computer=$indycomp[$indi], sensors=$indysens[$indi], beams=$indybeam[$indi] ,torp_launchers=$indytorp[$indi] ,shields=$indyshield[$indi] ,jammer=$indyjammer[$indi] ,cloak=$indycloak[$indi] WHERE planet_id = $indiplanet[$indi]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("delete from $dbtables[autotrades] WHERE planet_id=$indiplanet[$indi]");
				db_op_result($debug_query,__LINE__,__FILE__);
				calc_ownership($indisector[$indi]);
				adminlog(LOG_ADMIN_PLANETIND, $indiadmin[$indi]);
			}
		}
	}
}

TextFlush ("<br>Based: $totalbased - $basedav<br>Non-Based: $totalnon");
TextFlush ("<br>Based bypassed: $totalbased2<br>Non-Based bypassed: $totalnon2");
TextFlush ( "<br>");

$totalindi = $totalnon + $totalnon2;

if($totalindi > 0)
	insert_news($totalindi, 1, "indi");

$new_ticks = 1440 + (mt_rand(0, 288) * 5);
TextFlush ("Next Tick: $new_ticks<br><br>");
$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET ticks_full=$new_ticks WHERE sched_file='sched_independance.php'");
db_op_result($debug_query,__LINE__,__FILE__);
?>
