<?php

if (preg_match("/sched_apocalypse.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
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

TextFlush ( "<b>Planetary apocalypse</b><br><br>");

$doomsday = floor($colonist_limit * ($doomsday_value * 0.01));

$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE colonists > $doomsday and owner > '3'");
db_op_result($debug_query,__LINE__,__FILE__);

$chance = 9;
$reccount = $debug_query->RecordCount();

$trigger = floor($reccount / 100);

if ($trigger < 7) 
{
	$chance = $chance - $trigger; // increase chance it will happen if we have lots of planets meeting the criteria 
}else{
	$chance = 3;
}

$affliction = mt_rand(1,$chance); // the chance something bad will happen

if ($debug_query && $affliction <= 3 && $reccount > 0)
{
	$i = 1;
	while (!$debug_query->EOF)
	{
		$targetinfo = $debug_query->fields;
		$averagetechlvl = ($targetinfo['computer'] + $targetinfo['sensors'] + $targetinfo['beams'] + $targetinfo['torp_launchers'] + $targetinfo['shields'] + $targetinfo['jammer'] + $targetinfo['cloak']) / 7;
		$doomsday = floor(($colonist_limit + ($colonist_tech_add * $averagetechlvl)) * ($doomsday_value * 0.01));
		if($targetinfo['colonists'] > $doomsday){
			$targetplanet[$i] = $targetinfo['planet_id'];
			$targetsector[$i] = $targetinfo['sector_id'];
			$targetname[$i] = $targetinfo['name'];
			$targeowner[$i] = $targetinfo['owner'];
			$i++;
		}
		$debug_query->MoveNext();
	}

	if($i > 1){
		$targetnum = mt_rand(1,$i-1);

		if ($affliction == 1) // Space Plague
		{
			TextFlush ( "Space Plague triggered.<br>.");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists = ROUND(colonists-colonists*$space_plague_kills) WHERE planet_id = $targetplanet[$targetnum]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$logpercent = ROUND($space_plague_kills * 100);
			playerlog($targeowner[$targetnum],LOG_SPACE_PLAGUE,"$targetname[$targetnum]|$targetsector[$targetnum]|$logpercent"); 
		}else
		if ($affliction == 2) // Planetary Revolt
		{
			$collkillfactor=rand(20,100)/100;
			$creditkillfactor=rand(20,100)/100;
			$fighterskillfactor=rand(20,100)/100;
			$torpskillfactor=rand(20,100)/100;
			$orekillfactor=rand(20,100)/100;
			$organicskillfactor=rand(20,100)/100;
			$goodskillfactor=rand(20,100)/100;
			$energykillfactor=rand(20,100)/100;
		
			TextFlush ( "Planetary revolt triggered.<br>.");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists = ROUND(colonists-colonists*$collkillfactor),
						credits=ROUND(credits-credits*$creditkillfactor), 
						fighters=ROUND(fighters-fighters*$fighterskillfactor),
						torps=ROUND(torps-torps*$torpskillfactor),
						ore=ROUND(ore-ore*$orekillfactor),
						goods=ROUND(goods-goods*$goodskillfactor),
						organics=ROUND(organics-organics*$organicskillfactor),
						energy=ROUND(energy-energy*$energykillfactor) 
						WHERE planet_id = $targetplanet[$targetnum]");

			db_op_result($debug_query,__LINE__,__FILE__);

			$collogpercent = ROUND($collkillfactor * 100);
			$creditlogpercent = ROUND($creditkillfactor * 100);
			$fighterlogpercent = ROUND($fighterskillfactor * 100);
			$torpslogpercent = ROUND($torpskillfactor * 100);
			$orelogpercent = ROUND($orekillfactor * 100);
			$goodslogpercent = ROUND($goodskillfactor * 100);
			$organicslogpercent = ROUND($organicskillfactor * 100);
			$energylogpercent = ROUND($energykillfactor * 100);
			playerlog($targeowner[$targetnum],LOG_PLANET_REVOLT,"$targetname[$targetnum]|$targetsector[$targetnum]|$organicslogpercent|$goodslogpercent|$orelogpercent|$torpslogpercent|$collogpercent|$creditlogpercent|$fighterlogpercent|$energylogpercent"); 
		}
		else
		{
			TextFlush ( "Plasma Storm triggered.<BR>.");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET energy = 0 WHERE planet_id = $targetplanet[$targetnum]");
			db_op_result($debug_query,__LINE__,__FILE__);

			playerlog($targeowner[$targetnum],LOG_PLASMA_STORM,"$targetname[$targetnum]|$targetsector[$targetnum]");
		} 
	}
}

TextFlush ( "<br>");

?>
