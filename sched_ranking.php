<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_ranking.php

$cleanup_results = '';

if (preg_match("/sched_ranking.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

if (!isset($swordfish) || $swordfish != $adminpass)
{
	die("Script has not been called properly");
}

if (!function_exists('gen_score')) {
	function gen_score($sid)
	{
		if ($sid != '')
		{
			global $dev_genesis_price, $dev_emerwarp_price, $dev_warpedit_price;
			global $dev_minedeflector_price, $dev_escapepod_price,$dev_nova_price, $dev_fuelscoop_price;
			global $fighter_price, $torpedo_price, $armour_price, $colonist_price;
			global $base_ore, $base_goods, $base_organics, $base_credits, $torpedo_price, $fighter_price;
			global $ore_price, $organics_price, $goods_price, $energy_price, $dig_price, $dev_probe;
			global $upgrade_cost, $upgrade_factor;
			global $db, $dbtables;
			global $spy_price; // Used by spies.
			$score="";
			$calc_hull = "ROUND(pow($upgrade_factor,$dbtables[ships].hull))";
			$calc_engines = "ROUND(pow($upgrade_factor,$dbtables[ships].engines))";
			$calc_power = "ROUND(pow($upgrade_factor,$dbtables[ships].power))";
			$calc_computer = "ROUND(pow($upgrade_factor,$dbtables[ships].computer))";
			$calc_sensors = "ROUND(pow($upgrade_factor,$dbtables[ships].sensors))";
			$calc_beams = "ROUND(pow($upgrade_factor,$dbtables[ships].beams))";
			$calc_torp_launchers = "ROUND(pow($upgrade_factor,$dbtables[ships].torp_launchers))";
			$calc_shields = "ROUND(pow($upgrade_factor,$dbtables[ships].shields))";
			$calc_armour = "ROUND(pow($upgrade_factor,$dbtables[ships].armour))";
			$calc_cloak = "ROUND(pow($upgrade_factor,$dbtables[ships].cloak))";
			$calc_levels = "($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak)*$upgrade_cost";

			$calc_torps = "$dbtables[ships].torps*$torpedo_price";
			$calc_armour_pts = "$dbtables[ships].armour_pts*$armour_price";
			$calc_ship_ore = "$dbtables[ships].ore*$ore_price";
			$calc_ship_organics = "$dbtables[ships].organics*$organics_price";
			$calc_ship_goods = "$dbtables[ships].goods*$goods_price";
			$calc_ship_energy = "$dbtables[ships].energy*$energy_price";
			$calc_ship_colonists = "$dbtables[ships].colonists*$colonist_price";
			$calc_ship_fighters = "$dbtables[ships].fighters*$fighter_price";
			$calc_equip = "$calc_torps+$calc_armour_pts+$calc_ship_ore+$calc_ship_organics+$calc_ship_goods+$calc_ship_energy+$calc_ship_colonists+$calc_ship_fighters";

			$calc_dev_warpedit = "dev_warpedit*$dev_warpedit_price";
			$calc_dev_genesis = "dev_genesis*$dev_genesis_price";
			$calc_dev_emerwarp = "dev_emerwarp*$dev_emerwarp_price";
			$calc_dev_escapepod = "if (dev_escapepod='Y', $dev_escapepod_price, 0)";
			$calc_dev_fuelscoop = "if (dev_fuelscoop='Y', $dev_fuelscoop_price, 0)";
			$calc_dev_nova = "IF(dev_nova='Y', $dev_nova_price, 0)";
			$calc_dev_minedeflector = "dev_minedeflector*$dev_minedeflector_price";
			$calc_dev = "$calc_dev_warpedit+$calc_dev_genesis+$calc_dev_emerwarp+$calc_dev_escapepod+$calc_dev_fuelscoop+$calc_dev_nova+$calc_dev_minedeflector";

			$calc_planet_goods = "SUM($dbtables[planets].organics)*$organics_price+SUM($dbtables[planets].ore)*$ore_price+SUM($dbtables[planets].goods)*$goods_price+SUM($dbtables[planets].energy)*$energy_price";
			$calc_planet_colonists = "SUM($dbtables[planets].colonists)*$colonist_price";
			$calc_planet_defence = "SUM($dbtables[planets].fighters)*$fighter_price+if ($dbtables[planets].base='Y', $base_credits+SUM($dbtables[planets].torps)*$torpedo_price, 0)";
			$calc_planet_credits = "SUM($dbtables[planets].credits)";

			$calc_planet_computer = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].computer)))";
			$calc_planet_sensors = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].sensors)))";
			$calc_planet_beams = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].beams)))";
			$calc_planet_torp_launchers = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].torp_launchers)))";
			$calc_planet_shields = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].shields)))";
			$calc_planet_jammer = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].jammer)))";
			$calc_planet_cloak = "SUM(ROUND(pow($upgrade_factor,$dbtables[planets].cloak)))";
			 //Add $calc_planet_armour if necessary
			$calc_planet_def_levels = "($calc_planet_computer+$calc_planet_sensors+$calc_planet_beams+$calc_planet_torp_launchers+$calc_planet_shields+$calc_planet_jammer+$calc_planet_cloak)*$upgrade_cost";

			$debug_query = $db->Execute("SELECT $calc_planet_goods+$calc_planet_colonists+$calc_planet_defence+$calc_planet_credits+$calc_planet_def_levels AS score1 FROM $dbtables[players] LEFT JOIN $dbtables[planets] ON $dbtables[planets].owner=$dbtables[players].player_id  WHERE $dbtables[players].player_id=$sid ");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score = $row['score1'];

			// Loop through all ships for total ranking

			$res2 = $db->Execute("SELECT $calc_levels+$calc_equip+$calc_dev AS score2 FROM $dbtables[players]  LEFT JOIN $dbtables[ships] ON $dbtables[players].player_id=$dbtables[ships].player_id WHERE $dbtables[players].player_id=$sid AND destroyed='N'");
			db_op_result($res2,__LINE__,__FILE__);

			while (!$res2->EOF)
			{
				$row2 = $res2->fields;
				$score += $row2['score2'];
				$res2->MoveNext();
			}

			// End Ship Score loop
			$debug_query = $db->Execute("SELECT credits FROM $dbtables[players] where player_id = $sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			if ($debug_query)
			{
				$row = $debug_query->fields;
				$score += $row['credits'];
			}

			$debug_query = $db->Execute("SELECT balance, loan FROM $dbtables[ibank_accounts] where player_id = $sid");
			db_op_result($debug_query,__LINE__,__FILE__);

			if ($debug_query)
			{
				$row = $debug_query->fields;
				$score += ($row['balance'] - $row['loan']);
			}

			// This function checks the number of spies the player has, and adds their cost to the score calculation.
			$debug_query = $db->Execute("SELECT * FROM $dbtables[spies] where owner_id = $sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			if ($debug_query)
			{
				$spies = $debug_query->RecordCount();
				$score += ($spies * $spy_price);
			}

			// This function checks the number of dignitaries the player has, and adds their cost to the score calculation.
			$debug_query = $db->Execute("SELECT * FROM $dbtables[dignitary] where owner_id = $sid");
			db_op_result($debug_query,__LINE__,__FILE__);

			if ($debug_query)
			{
				$digs = $debug_query->RecordCount();
				$score += ($digs * $dig_price);
			}

			// Add in Sector Defense values
			$debug_query = $db->Execute("SELECT (SUM(quantity)*$fighter_price) AS score6 FROM $dbtables[sector_defence] WHERE player_id=$sid and defence_type='F'");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score += $row['score6'];
//echo $row['score6']."<br>";
			$debug_query = $db->Execute("SELECT (SUM(quantity)*$torpedo_price) AS score7 FROM $dbtables[sector_defence] WHERE player_id=$sid and defence_type='M'");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score += $row['score7'];
//echo $row['score7']."<br><br>";

			// Add in Cargo Hull values
			$calc_levels = "((SUM(ROUND(pow($upgrade_factor,cargo_hull))) + SUM(ROUND(pow($upgrade_factor,cargo_power))))*$upgrade_cost)";
			$calc_cargo_ship = "SUM(IF(cargo_hull!=0, 116383500, 0))";
			$debug_query = $db->Execute("SELECT $calc_levels+$calc_cargo_ship AS score3 FROM $dbtables[planets] WHERE owner=$sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score += $row['score3'];

			// Add in Probe values
			$calc_levels = "((SUM(ROUND(pow($upgrade_factor,engines))) + SUM(ROUND(pow($upgrade_factor,sensors))) + SUM(ROUND(pow($upgrade_factor,cloak))))*$upgrade_cost)";
			$debug_query = $db->Execute("SELECT $calc_levels AS score4 FROM $dbtables[probe] WHERE owner_id=$sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score += $row['score4'];
			$debug_query = $db->Execute("SELECT probe_id FROM $dbtables[probe] WHERE owner_id=$sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			$num = $debug_query->recordcount();
			$score += $num * $dev_probe;

			$debug_query = $db->Execute("SELECT SUM(amount) AS score7 FROM $dbtables[bounty] WHERE placed_by=$sid");
			db_op_result($debug_query,__LINE__,__FILE__);
			$row = $debug_query->fields;
			$score += $row['score7'];

			$score = sign($score) * ROUND(SQRT(ABS($score)));

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET score=$score WHERE player_id=$sid");
			db_op_result($debug_query,__LINE__,__FILE__);

			return $score;
		}
		else
		{
			return 0;
		}
	}
}

if (!function_exists('sign')) {
	function sign( $data )
	{
		if ($data > 0)
		{
			return 1;
		}
		elseif ($data < 0)
		{
			return -1;
		}
		else
		{
			return 0;
		}
	}
}

$debug_query = $db->Execute("SELECT $dbtables[players].player_id FROM $dbtables[players] LEFT JOIN " .
							"$dbtables[ships] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE destroyed='N'");
while (!$debug_query->EOF)
{
	gen_score($debug_query->fields['player_id']);
	$debug_query->MoveNext();
}

while (!$debug_query->EOF && $debug_query !='')
{
	$cleanup_results = $debug_query;
}

$multiplier = 0; //no use to run this again
if($adminexecuted == 1){
	echo"<b>RANKING</b><br><br>";

	if ($cleanup_results != '')
	{
		echo "Errors encountered: $cleanup_results";
	}else{
		echo "All ranking updates completed successfully!";
	}
	echo "<br><br>";
}
?>
