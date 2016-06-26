<?php

if (preg_match("/sched_indy_upgrade.php/i", $_SERVER['PHP_SELF'])) {
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('phpChangeDelta')) {
	function phpChangeDelta($desiredvalue,$currentvalue)
	{
		global $upgrade_cost, $upgrade_factor;

		$Delta=0; $DeltaCost=0;
		$Delta = $desiredvalue - $currentvalue;

		while ($Delta>0)
		{
			$DeltaCost=$DeltaCost + mypw($upgrade_factor,$desiredvalue-$Delta);
			$Delta=$Delta-1;
		}
		$DeltaCost=$DeltaCost * $upgrade_cost;

		return $DeltaCost;
	}
}

if (!function_exists('set_max_credits')) {
	function set_max_credits($planet_id){
		global $db, $dbtables, $planet_credit_multi, $base_credits;

		$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE planet_id = $planet_id");
		db_op_result($debug_query,__LINE__,__FILE__);

		$planetinfo = $debug_query->fields;

		$max_credits = phpChangeDelta($planetinfo['computer'], 0) + phpChangeDelta($planetinfo['sensors'], 0) + phpChangeDelta($planetinfo['beams'], 0) + phpChangeDelta($planetinfo['torp_launchers'], 0) + phpChangeDelta($planetinfo['shields'], 0) + phpChangeDelta($planetinfo['jammer'], 0) + phpChangeDelta($planetinfo['cloak'], 0);
		$max_credits = ($max_credits * $planet_credit_multi) + $base_credits;
		$debug_query = $db->Execute("UPDATE $dbtables[planets] SET max_credits=$max_credits WHERE planet_id=$planet_id");
	}
}

TextFlush ( "<b>Planetary Independance Upgrade</b><br><br>");

$base_query = $db->Execute("SELECT * from $dbtables[planets] WHERE owner='2' and base='N'");
db_op_result($base_query,__LINE__,__FILE__);

$reccount = $base_query->RecordCount();
$totalupdates = 0;

if ($base_query && $reccount > 0){
	while (!$base_query->EOF){
		$targetinfo = $base_query->fields;

		/* build a base */
		if ($targetinfo['ore'] >= $base_ore && $targetinfo['organics'] >= $base_organics && $targetinfo['goods'] >= $base_goods && $targetinfo['credits'] >= $base_credits)
		{
			// ** Create The Base
			$makebase_query = $db->Execute("UPDATE $dbtables[planets] SET base='Y', ore=ore-$base_ore, organics=organics-$base_organics, goods=goods-$base_goods, credits=credits-$base_credits WHERE planet_id=$targetinfo[planet_id]");
			db_op_result($makebase_query,__LINE__,__FILE__);

			// ** Calc Ownership and Notify User Of Results
			$ownership = calc_ownership($targetinfo['sector_id']);

			$totalupdates++;
		}

		$base_query->MoveNext();
	}
}
TextFlush ("<br>Non-Based Independent Planets Based: $totalupdates<br>");
TextFlush ( "<br>");

$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE owner='2' and base='Y'");
db_op_result($debug_query,__LINE__,__FILE__);

$reccount = $debug_query->RecordCount();
$totalupdates = 0;

if ($debug_query && $reccount > 0){
	while (!$debug_query->EOF){
		$targetinfo = $debug_query->fields;
		$upgraded = 0;

		$upgradelist = '';
		$startcredits = $targetinfo['credits'];
		$computer_upgrade_cost = phpChangeDelta($targetinfo['computer']+1, $targetinfo['computer']);
		if($computer_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 8000){
			$targetinfo['credits'] -= $computer_upgrade_cost;
			$targetinfo['computer']++;
			$upgraded = 1;
			$upgradelist .= "- Computer <font color='#ffffff'>$targetinfo[computer]</font> ";
		}

		$beams_upgrade_cost = phpChangeDelta($targetinfo['beams']+1, $targetinfo['beams']);
		if($beams_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 7000){
			$targetinfo['credits'] -= $beams_upgrade_cost;
			$targetinfo['beams']++;
			$upgraded = 1;
			$upgradelist .= "- Beams <font color='#ffffff'>$targetinfo[beams]</font> ";
		}

		$torp_launchers_upgrade_cost = phpChangeDelta($targetinfo['torp_launchers']+1, $targetinfo['torp_launchers']);
		if($torp_launchers_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 6000){
			$targetinfo['credits'] -= $torp_launchers_upgrade_cost;
			$targetinfo['torp_launchers']++;
			$upgraded = 1;
			$upgradelist .= "- Torps <font color='#ffffff'>$targetinfo[torp_launchers]</font> ";
		}

		$shields_upgrade_cost = phpChangeDelta($targetinfo['shields']+1, $targetinfo['shields']);
		if($shields_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 5000){
			$targetinfo['credits'] -= $shields_upgrade_cost;
			$targetinfo['shields']++;
			$upgraded = 1;
			$upgradelist .= "- Shields <font color='#ffffff'>$targetinfo[shields]</font> ";
		}

		$jammer_upgrade_cost = phpChangeDelta($targetinfo['jammer']+1, $targetinfo['jammer']);
		if($jammer_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 4000){
			$targetinfo['credits'] -= $jammer_upgrade_cost;
			$targetinfo['jammer']++;
			$upgraded = 1;
			$upgradelist .= "- Jammer <font color='#ffffff'>$targetinfo[jammer]</font> ";
		}

		$cloak_upgrade_cost = phpChangeDelta($targetinfo['cloak']+1, $targetinfo['cloak']);
		if($cloak_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 3000){
			$targetinfo['credits'] -= $cloak_upgrade_cost;
			$targetinfo['cloak']++;
			$upgraded = 1;
			$upgradelist .= "- Cloak <font color='#ffffff'>$targetinfo[cloak]</font> ";
		}

		$sensors_upgrade_cost = phpChangeDelta($targetinfo['sensors']+1, $targetinfo['sensors']);
		if($sensors_upgrade_cost < $targetinfo['credits'] and mt_rand(1, 10000) < 2000){
			$targetinfo['credits'] -= $sensors_upgrade_cost;
			$targetinfo['sensors']++;
			$upgraded = 1;
			$upgradelist .= "- Sensors <font color='#ffffff'>$targetinfo[sensors]</font> ";
		}

		// Upgrade Sector Defenses

		$res = $db->Execute("SELECT allow_defenses, $dbtables[universe].zone_id, owner FROM $dbtables[zones],$dbtables[universe] " .
							"WHERE sector_id=$targetinfo[sector_id] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
		$query97 = $res->fields;

		if ($query97['allow_defenses'] != 'N')
		{
			//Put the defence information into the array "defenceinfo"
			$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$targetinfo[sector_id] ");
			$defenseinfo = $result3->fields;
			$randomcheck = 100;
			$i = 0;
			$total_sector_fighters = 0;
			$total_sector_mines = 0;
			$owns_all = true;
			$fighter_id = 0;
			$mine_id = 0;
			if ($result3 > 0)
			{
				while (!$result3->EOF)
				{
					$defences[$i] = $result3->fields;
					if ($defences[$i]['defence_type'] == 'F')
					{
						$total_sector_fighters += $defences[$i]['quantity'];
					}
					else
					{
						$total_sector_mines += $defences[$i]['quantity'];
					}

					if ($defences[$i]['player_id'] != 2)
					{
						$owns_all = false;
					}
					else
					{
						if ($defences[$i]['defence_type'] == 'F')
						{
							$fighter_id = $defences[$i]['defence_id'];
						}
						else
						{
							$mine_id = $defences[$i]['defence_id'];
						}
					}
					$i++;
					$result3->MoveNext();
				}
			}

			if ($i > 0)
			{
				if (!$owns_all)
				{
					$defence_owner = $defences[0]['player_id'];
					$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$defence_owner");
					$fighters_owner = $result2->fields;

					if ($fighters_owner['team'] != 2)
					{
						$randomcheck = 0;
					}
				}
			}

			if ($query97['allow_defenses'] == 'L' and $randomcheck != 0)
			{
				$zone_owner = $query97['owner'];
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$zone_owner");
				$zoneowner_info = $result2->fields;

				if ($zone_owner <> 2)
				{
					 if ($zoneowner_info['team'] != 2)
					 {
						$randomcheck = 0;
					 }
				}
			}

			if(mt_rand(1, 10000) < $randomcheck){
				$res = $db->Execute("SELECT * from $dbtables[sector_defence] where defence_type = 'F' and player_id=2 and sector_id=$targetinfo[sector_id]");
				$row = $res->fields;
				$energy_required = ROUND($row['quantity'] * $energy_per_fighter);
				$res4 = $db->Execute("SELECT IFNULL(SUM(energy),0) as energy_available from $dbtables[planets] where owner = 2 and sector_id = $targetinfo[sector_id]"); 
				$planet_energy = $res4->fields;
				$energy_available = $planet_energy['energy_available'];
				$maxfighters = floor(($energy_available - $energy_required) * $energy_per_fighter);
				$addfighters = floor($maxfighters * 0.015);
				$fightercost = $addfighters * $fighter_price;
				if($fightercost > $targetinfo['credits']){
					$addfighters = floor($targetinfo['credits'] / $fighter_price);
					$fightercost = $addfighters * $fighter_price;
				}
				$addfighters = floor($addfighters * (rand(10, 75) * 0.01));
				$fightercost = $addfighters * $fighter_price;
				$targetinfo['credits'] -= $fightercost;
//			echo "available $energy_available, required $energy_required, max add fighters $maxfighters, fighters to add $addfighters, fighter cost $fightercost, credits left $targetinfo[credits].<br>\n";

				if ($addfighters > 0)
				{
					if ($row['defence_id'] != 0)
					{
						$fighter_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $addfighters " .
													"where defence_id = $row[defence_id]");
						db_op_result($fighter_query,__LINE__,__FILE__);
					}
					else
					{
						$fighter_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
													"(player_id,sector_id,defence_type,quantity) values " .
													"(2,$targetinfo[sector_id],'F',$addfighters)");
						db_op_result($fighter_query,__LINE__,__FILE__);
					}
					$upgraded = 1;
					$upgradelist .= "- Sector Fighters <font color='#ffffff'>$addfighters</font> ";
				}
			}

			if(mt_rand(1, 10000) < $randomcheck){
				$minecost = floor($targetinfo['credits'] * 0.02);
				$addmines = floor($minecost / $torpedo_price);
				$addmines = floor($addmines * (rand(10, 75) * 0.01));
				$minecost = $addmines * $torpedo_price;
				$targetinfo['credits'] -= $minecost;
//			echo "mines to add $addmines, mine cost $minecost, credits left $targetinfo[credits].<br>\n";

				$res = $db->Execute("SELECT * from $dbtables[sector_defence] where defence_type = 'M' and player_id=2 and sector_id=$targetinfo[sector_id]");
				$row = $res->fields;
				if ($addmines > 0)
				{
					if ($row['defence_id'] != 0)
					{
						$mines_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $addmines " .
													"where defence_id = $row[defence_id]");
						db_op_result($mines_query,__LINE__,__FILE__);
					}
					else
					{
						$mines_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
													"(player_id,sector_id,defence_type,quantity) values " .
													"(2,$targetinfo[sector_id],'M',$addmines)");
						db_op_result($mines_query,__LINE__,__FILE__);
					}
					$upgraded = 1;
					$upgradelist .= "- Sector Mines <font color='#ffffff'>$addmines</font> ";
				}
			}
		}

		if($upgraded == 1){
			$update_query = $db->Execute("UPDATE $dbtables[planets] SET credits=$targetinfo[credits], computer=$targetinfo[computer], sensors=$targetinfo[sensors], beams=$targetinfo[beams], torp_launchers=$targetinfo[torp_launchers], shields=$targetinfo[shields], jammer=$targetinfo[jammer], cloak=$targetinfo[cloak], computer_normal=$targetinfo[computer], sensors_normal=$targetinfo[sensors], beams_normal=$targetinfo[beams], torp_launchers_normal=$targetinfo[torp_launchers], shields_normal=$targetinfo[shields], jammer_normal=$targetinfo[jammer], cloak_normal=$targetinfo[cloak] WHERE planet_id = $targetinfo[planet_id]");
			db_op_result($update_query,__LINE__,__FILE__);
			TextFlush ( "<font color='#00ff00'>Upgraded planet <font color='#ffffff'>$targetinfo[name]</font> in sector <font color='#ffff00'>$targetinfo[sector_id]</font>, credits spent = <font color='#ff0000'>".NUMBER($startcredits - $targetinfo[credits])."</font>, credits left = <font color='#ffffff'>".NUMBER($targetinfo[credits])."</font>:  <font color='#00ffff'>$upgradelist-</font></font><br>");
			$totalupdates++;
		}
		set_max_credits($targetinfo['planet_id']);
		$debug_query->MoveNext();
	}
}

TextFlush ("<br>Based: $totalupdates - Upgraded Levels<br>");
TextFlush ( "<br>");

$new_ticks = 300 + mt_rand(0, 300);
TextFlush ("Next Indy Update: $new_ticks minutes<br><br>");
$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET ticks_full=$new_ticks WHERE sched_file='sched_indy_upgrade.php'");
db_op_result($debug_query,__LINE__,__FILE__);
?>
