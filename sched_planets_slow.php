<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_planets_slow.php

if (preg_match("/sched_planets_slow.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

$expoprod = mypw($colonist_reproduction_rate + 1, $multiplier);
$expoprod *= $multiplier;

$expostarvation_death_rate = 1 - mypw((1 - $starvation_death_rate ), $multiplier);  

TextFlush ( "<b>PLANETS</b><br><br>\n");

//--==** Planets without 'working' spies **==--

// If organics plus org production minus org consumption is less then zero then there is starvation
// So set organics to zero and kill off some colonists

TextFlush ( "Calculating Starvation<br>");

$debug_query = sql_log_starvation(); // See includes/dbtype-common.php 
while (!$debug_query->EOF)
{
	$info = $debug_query->fields;
	if($info['st_value']>0)
	{
		playerlog($info['owner'], LOG_STARVATION, "$info[sector_id]|" . NUMBER($info['st_value']));
	}
	$debug_query->MoveNext();
}

sql_update_starvation(); // See includes/dbtype-common.php

TextFlush ( "Calculating Ore, Goods, Organics, Energy and Credit Production<br>");

sql_production_update(); // See includes/dbtype-common.php

TextFlush ( "Calculating Fighter and Torpedo Production<br>");

sql_defense_update(); // See includes/dbtype-common.php

//--==** Planets with 'working' spies **==--
// We have to update them one by one, because production etc is different on different planets, depending on the spies activity
if ($spy_success_factor)
{
	TextFlush ( "<b>Spies Sabotaging Planet Production</b><br>");

	$line = "-1";
	$debug_query = $db->Execute("SELECT DISTINCT planet_id from $dbtables[spies] WHERE active='Y' AND job_id = '1' ");
	db_op_result($debug_query,__LINE__,__FILE__);
	while (!$debug_query->EOF)
	{
		$line .= ", " . $debug_query->fields['planet_id'];
		$debug_query->MoveNext();
	}

	$line = str_replace("-1, ", "", $line);

	$debug_query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line)");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF)
	{
		$row = $debug_query->fields;

		$sum = 0.0;

		$result_b = $db->Execute("SELECT SUM(spy_percent) as sum FROM $dbtables[spies] WHERE job_id='1' AND planet_id=$row[planet_id] AND active='Y' "); // Birth decreasers  (or however we call them)
		$sum = $result_b->fields['sum'];

		TextFlush ( "$row[planet_id] - Spy Sabotaging Production - ". $multiplier * $sum . " - organics: " . floor($row['organics'] - MAX($row['organics']-($row['organics'] * ($multiplier * $sum)), 0)) . " - ");
		TextFlush ( "ore: " . floor($row['ore'] - MAX($row['ore']-($row['ore'] * ($multiplier * $sum)), 0)) . " - ");
		TextFlush ( "goods: " . floor($row['goods'] - MAX($row['goods']-($row['goods'] * ($multiplier * $sum)), 0)) . " - ");
		TextFlush ( "energy: " . floor($row['energy'] - MAX($row['energy']-($row['energy'] * ($multiplier * $sum)), 0)) . " - ");
		TextFlush ( "torps: " . floor($row['torps'] - MAX($row['torps']-($row['torps'] * ($multiplier * $sum)), 0)) . " - ");
		TextFlush ( "fighters: " . floor($row['fighters'] - MAX($row['fighters']-($row['fighters'] * ($multiplier * $sum)), 0)) . " <br>");

		$query = $db->Execute("UPDATE $dbtables[planets] SET organics=GREATEST(organics-(organics * ($multiplier * $sum)), 0), " .
									"ore=GREATEST(ore-(ore * ($multiplier * $sum)), 0), goods=GREATEST(goods-(goods * ($multiplier * $sum)), 0), energy=GREATEST(energy-(energy * ($multiplier * $sum)), 0), " .
									"torps=GREATEST(torps-(torps * ($multiplier * $sum)), 0), " .
									"fighters=GREATEST(fighters-(fighters * ($multiplier * $sum)), 0)
									WHERE planet_id=$row[planet_id]");
		db_op_result($query,__LINE__,__FILE__);
		$debug_query->MoveNext();
	}

	TextFlush ( "<b>Spies Killing Enemy Colonists</b><br>");

	$line = "-1";
	$debug_query = $db->Execute("SELECT DISTINCT planet_id from $dbtables[spies] WHERE active='Y' AND job_id = '3' ");
	db_op_result($debug_query,__LINE__,__FILE__);
	while (!$debug_query->EOF)
	{
		$line .= ", " . $debug_query->fields['planet_id'];
		$debug_query->MoveNext();
	}

	$line = str_replace("-1, ", "", $line);

	$debug_query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line)");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF)
	{
		$row = $debug_query->fields;

		$sum = 0.0;
		$result_b = $db->Execute("SELECT SUM(spy_percent) as sum FROM $dbtables[spies] WHERE job_id='3' AND planet_id=$row[planet_id] AND active='Y' "); // Birth decreasers  (or however we call them)
		$sum = $result_b->fields['sum'];

		TextFlush ( "$row[planet_id] - Spy Killing Colonists - ". $multiplier * $sum . " - " . floor($row['colonists']-MAX($row['colonists']-($row['colonists'] * ($multiplier * $sum)), 0)) . "<br>");

		$query = $db->Execute("UPDATE $dbtables[planets] SET " .
									"colonists=GREATEST(colonists-(colonists * ($multiplier * $sum)), 0)
									WHERE planet_id=$row[planet_id]");
		db_op_result($query,__LINE__,__FILE__);
		$debug_query->MoveNext();
	}

	TextFlush ( "<b>Spies Stealing Credits</b><br>");

	$line = "-1";
	$debug_query = $db->Execute("SELECT DISTINCT planet_id from $dbtables[spies] WHERE active='Y' AND job_id = '2' ");
	db_op_result($debug_query,__LINE__,__FILE__);
	while (!$debug_query->EOF)
	{
		$line .= ", " . $debug_query->fields['planet_id'];
		$debug_query->MoveNext();
	}

	$line = str_replace("-1, ", "", $line);

	$debug_query = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line)");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF)
	{
		$row = $debug_query->fields;

		$intr = 0.0;

		$result_b = $db->Execute("SELECT SUM(spy_percent) as intr FROM $dbtables[spies] WHERE job_id='2' AND planet_id=$row[planet_id] AND active='Y' "); // Birth decreasers  (or however we call them)
		$intr = $result_b->fields['intr'];

		TextFlush ( "$row[planet_id] - Spy Stealing Credits - ". $multiplier * $intr . " - " . floor($row['credits']-MAX($row['credits']-($row['credits'] * ($multiplier * $intr)), 0)) . "<br>");

		$query = $db->Execute("UPDATE $dbtables[planets] SET " .
									"credits=GREATEST(credits-(credits * ($multiplier * $intr)), 0)
									WHERE planet_id=$row[planet_id]");
		db_op_result($query,__LINE__,__FILE__);
		$debug_query->MoveNext();
	}
}

//--==** Planets with 'working' dignitary **==--
// We have to update them one by one, because production etc is different on different planets, 
// depending on the spies activity

if($dig_success_factor)
{
	TextFlush ( "<b>Dignitaries increasing production</b><br>");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id='1' ");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	while(!$res->EOF)
	{
		$row = $res->fields;

		$averagetechlvl = ($row['computer'] + $row['sensors'] + $row['beams'] + $row['torp_launchers'] + $row['shields'] + $row['jammer'] + $row['cloak']) / 7;

		// Production Builder
		$result_b = $db->Execute("SELECT SUM(percent) as sum FROM $dbtables[dignitary] WHERE planet_id=$row[planet_id] AND job_id='1' AND active='Y' "); 
		$sum = $result_b->fields['sum'];

		if($sum != 0){
			TextFlush ( "$row[planet_id] - Production Dig - ".($sum * 100)."%, ");
			$production = min($row['colonists'], $colonist_limit + floor($colonist_tech_add * $averagetechlvl)) * (mypw(($sum + 1), $multiplier) - 1);
			$organics_production = floor($production * $organics_prate * $row['prod_organics'] / 100.0);
			$ore_production = floor($production * $ore_prate * $row['prod_ore'] / 100.0);
			$goods_production = floor($production * $goods_prate * $row['prod_goods'] / 100.0);
			$energy_production = floor($production * $energy_prate * $row['prod_energy'] / 100.0);
			TextFlush ( "organics_production = $organics_production, ");
			TextFlush ( "ore_production = $ore_production, ");
			TextFlush ( "goods_production = $goods_production, ");
			TextFlush ( "energy_production = $energy_production, ");

			if ($row['owner'])
			{
				$fighter_production = floor($production * $fighter_prate * $row['prod_fighters'] / 100.0);
				$torp_production = floor($production * $torpedo_prate * $row['prod_torp'] / 100.0);
			}
			else
			{
				$fighter_production = 0;
				$torp_production = 0;
			}

			TextFlush ( "fighter_production = $fighter_production, ");
			TextFlush ( "torp_production = $torp_production<br>");

			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET organics=organics+$organics_production, torps=torps+$torp_production, fighters=fighters+$fighter_production, " .
										"ore=ore+$ore_production, goods=goods+$goods_production, energy=energy+$energy_production " .
										"WHERE planet_id=$row[planet_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		TextFlush ("<br>");
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries increasing birthrate</b><br>");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id='4' ");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	while(!$res->EOF)
	{
		$row = $res->fields;

		$averagetechlvl = ($row['computer'] + $row['sensors'] + $row['beams'] + $row['torp_launchers'] + $row['shields'] + $row['jammer'] + $row['cloak']) / 7;

		// Birth Rate Increaser
		$sum2 = 0.0;
		$result_b = $db->Execute("SELECT SUM(percent) as sum FROM $dbtables[dignitary] WHERE planet_id=$row[planet_id] AND job_id='4' AND active='Y' "); 
		$sum2 = $result_b->fields['sum'];

		if($sum2 != 0){
			$reproduction = round(($row['colonists'] - $starvation) * (mypw($sum2 + 1,$multiplier) - 1));

			if(($row['colonists'] + $reproduction) > $colonist_limit + floor($colonist_tech_add * $averagetechlvl))
			{
				$reproduction = ($colonist_limit + floor($colonist_tech_add * $averagetechlvl)) - $row['colonists'] ;
			}

			TextFlush ( "$row[planet_id] - Birth Increaser Dig - ".($sum2 * 100)."% - ".NUMBER($reproduction)." colonists added<br>");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists=colonists+$reproduction WHERE planet_id=$row[planet_id] and base='Y'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		TextFlush ("<br>");
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries decreasing birthrate</b><br>");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id='3' ");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	while(!$res->EOF)
	{
		$row = $res->fields;

		$averagetechlvl = ($row['computer'] + $row['sensors'] + $row['beams'] + $row['torp_launchers'] + $row['shields'] + $row['jammer'] + $row['cloak']) / 7;

		// Birth Rate Decreaser
		$result_b = $db->Execute("SELECT SUM(percent) as sum FROM $dbtables[dignitary] WHERE planet_id=$row[planet_id] AND job_id='3' AND active='Y' "); 
		$sum2 = $result_b->fields['sum'];

		if($sum2 != 0){
			$sum2 = -$sum2;
			$reproduction = round(($row['colonists'] - $starvation) * (mypw($sum2 + 1,$multiplier) - 1));

			if(($row['colonists'] + $reproduction) < $colonist_lower_limit)
			{
				$reproduction = 0;
			}

			TextFlush ( "$row[planet_id] - Birth Decreaser Dig - ".abs($sum2 * 100)."% - ".NUMBER(abs($reproduction))." colonists died<br>");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists=colonists+$reproduction WHERE planet_id=$row[planet_id] and base='Y'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		TextFlush ("<br>");
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries looking for spies</b><br>");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id='5' ");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	while(!$res->EOF)
	{
		$row = $res->fields;

		// Spy hunter
		$chancetotal = 0;

		$result_b = $db->Execute("SELECT SUM(percent) as chancetotal FROM $dbtables[dignitary] WHERE planet_id=$row[planet_id] AND job_id='5' AND active='Y' "); 
		$chancetotal = $result_b->fields['chancetotal'];

		if($chancetotal > 0){
			$success = mt_rand(0, 100);
			TextFlush ( "$row[planet_id] - Spy hunter Dig - random = $success - level = ".(5+($chancetotal*10000))."<br>");
			if ($success < (5+($chancetotal*10000)))
			{
				$result_sf = $db->Execute("SELECT * FROM $dbtables[spies],$dbtables[players] WHERE $dbtables[spies].owner_id=$dbtables[players].player_id and $dbtables[spies].planet_id=$row[planet_id] AND $dbtables[spies].active='Y' "); 
				TextFlush ( $db->ErrorMsg());
				if ($result_sf->RecordCount() > 0){
					while(!$result_sf->EOF)
					{
						$spy1 = $result_sf->fields;
						$result_sf->MoveNext();
					}
				}
				if ($result_sf->RecordCount() > 0){
					$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE spy_id=$spy1[spy_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
					playerlog($dig['owner_id'], LOG_DIG_KILLED_SPY, "$row[name]|$row[sector_id]|$spy1[character_name]");
					playerlog($spy1['player_id'], LOG_SPY_KILLED_SPYOWNER, "$row[name]|$row[sector_id]|$spy1[character_name]");
					TextFlush ( "$row[planet_id] - Spy hunter Dig - found and killed enemy spy<br>");
				}
			}
		}
		TextFlush ("<br>");
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries increasing interest</b><br>");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id='2' ");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	while(!$res->EOF)
	{
		$row = $res->fields;

		// Interest Builder
		$result_b = $db->Execute("SELECT SUM(percent) as intr FROM $dbtables[dignitary] WHERE planet_id=$row[planet_id] AND job_id='2' AND active='Y' "); 
		$intr = $result_b->fields['intr'];

		if($intr != 0){
			$result_s = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$row[planet_id]");
			$planetcredits = $result_s->fields['credits'];
			TextFlush ( "$row[planet_id] - Interest Dig - ".($intr * 100)."% - ".mypw(1 + $intr, $multiplier)." - $planetcredits<br>");
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits+(((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $credits_prate * (100.0 - prod_organics - prod_ore - prod_goods - prod_energy - prod_fighters - prod_torp - prod_research - prod_build) / 100.0 * $expoprod *".mypw(1 + $intr, $multiplier).") - (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $credits_prate * (100.0 - prod_organics - prod_ore - prod_goods - prod_energy - prod_fighters - prod_torp - prod_research - prod_build) / 100.0 * $expoprod) WHERE planet_id=$row[planet_id] and base='Y'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
		TextFlush ("<br>");
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries imbezzling credits</b><br>");

	$stamp = date("Y-m-d H:i:s");

	$line2 ="-1";

	$res = $db->Execute("SELECT DISTINCT planet_id from $dbtables[dignitary] WHERE job_id > '5' and job_id < '11'");
	db_op_result($res,__LINE__,__FILE__);
	$reccount = $res->RecordCount();
	while (!$res->EOF)
	{
		$line2 .= ", " . $res->fields['planet_id'];
		$res->MoveNext();
	}

	$line2 = str_replace("-1, ", "", $line2);

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id IN ($line2) and base='Y'");
	$error = $db->ErrorMsg();
	if (!empty($error)) { TextFlush ( "$error <br>"); }

	if(!$res->EOF)
	{
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
		$totunverse=$findem->RecordCount(); 
		$getuniverse=$findem->GetArray();
	}

	while(!$res->EOF)
	{
		$row = $res->fields;

		// Imbezzeler
		$stamp = date("Y-m-d H:i:s");
		$result_i = $db->Execute("SELECT * FROM $dbtables[dignitary] WHERE  job_id > '5' and job_id < '11' AND planet_id=$row[planet_id] AND active='Y' and reactive_date<='$stamp'"); //Interest Builder
		while(!$result_i->EOF)
		{
			$dig = $result_i->fields;

			$success=mt_rand(0,100);
			if ($success <= $dig_embezzler_success and $row['credits'] > 0){
				$sum = floor((mt_rand(1, $dig_embezzler_amount) / 100) * $row['credits']);
				TextFlush ( "$row[planet_id] - Embezzler Dig - amount embezzled = ".NUMBER($sum)." - ");
				$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits-$sum WHERE planet_id=$row[planet_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);
				$ownerdude=$row['owner'];
				$findem = $db->Execute("SELECT $dbtables[players].player_id FROM $dbtables[ships],$dbtables[players] WHERE $dbtables[players].player_id=$dbtables[ships].player_id and $dbtables[players].currentship=$dbtables[ships].ship_id and $dbtables[ships].destroyed ='N' AND $dbtables[ships].player_id <> $ownerdude and $dbtables[ships].player_id > 3 and $dbtables[players].turns_used > $dig_embezzlerturns");
				$totrecs=$findem->RecordCount(); 
				$getit=$findem->GetArray();
				if ($totrecs > 0){
					$randplay=mt_rand(0,($totrecs-1));
					$playergift = $getit[$randplay]['player_id'];
					TextFlush ( "PlayerID:".$playergift);
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$sum WHERE player_id=$playergift");
					db_op_result($debug_query,__LINE__,__FILE__);
					$temp = NUMBER($sum);
					playerlog($playergift, LOG_DIG_MONEY, "$dig[dig_id]|$row[name]|$row[sector_id]|$temp");
				}
				else
				{
					$randplay=mt_rand(0,($totunverse-1));
					$targetlink = $getuniverse[$randplay]['sector_id'];
					$debug_query = $db->Execute("INSERT INTO $dbtables[debris] (debris_type, debris_data, sector_id) values (14,'$sum', $targetlink)");
					db_op_result($debug_query,__LINE__,__FILE__);
					TextFlush ( "Debris");
				}
				TextFlush ( "<br>");

				$stamp = date("Y-m-d H:i:s");
				$reactive_date = date("Y-m-d H:i:s", strtotime($stamp) + mt_rand(floor($dig_embezzlerdelay * 86400 / 2), $dig_embezzlerdelay * 86400));
				$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);

				$result_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$row[planet_id] and owner_id=$dig[owner_id]");
				$reccount = $result_s->RecordCount();
				for($spycheck = 0; $spycheck < $reccount; $spycheck++){
					$success=mt_rand(1,1000);
					if($success < ($dig_spy_embezzler * 10)){
						playerlog($dig['owner_id'], LOG_SPY_FOUND_EMBEZZLER, "$dig[dig_id]|$row[name]");
						TextFlush ( "$row[planet_id] - Embezzler Dig - found by spy<br>");
						$spycheck = $reccount;
					}
				}
			}
			$result_i->MoveNext();
		}
		$res->MoveNext();
	}
	TextFlush ("<br>");
}
/*
TextFlush ( "Changing Digs to Embezzlers<br>");
$stamp = date("Y-m-d H:i:s");
$reactive_date = date("Y-m-d H:i:s", strtotime($stamp) + mt_rand(floor($dig_embezzlerdelay * 86400 / 2), $dig_embezzlerdelay * 86400));
$result_s = $db->Execute("UPDATE $dbtables[dignitary] SET job_id='6', active_date='$stamp', reactive_date='$reactive_date' WHERE job_id!=0 and job_id!=5 and reactive_date<='$stamp' AND active='Y' AND RAND() < $dig_changetoembezzler");
db_op_result($result_s,__LINE__,__FILE__);
*/

TextFlush ( "Updating Planet Armor<br>");
// Update armor level
$planetupdate = "UPDATE $dbtables[planets] SET armour =((beams+computer+sensors+jammer+cloak+shields+torp_launchers)/7),
 ore=GREATEST(ore-(((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $ore_prate * .05 / 100.0 * $expoprod)),0),
 goods=GREATEST(goods - (((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $goods_prate * 05 / 100.0 * $expoprod)),0),
 armour_pts=armour_pts+(((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $ore_prate * 05 / 100.0 * $expoprod)/8)+(((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $goods_prate * 05 / 100.0 * $expoprod)/8)
 where  armour_pts < ((POW($level_factor ,(((beams+computer+sensors+jammer+cloak+shields+torp_launchers)/7)+5))*10)*$armor_prod_multiplier) and base='Y' and goods >=10000 and ore >= 10000 AND owner!=0";
//echo  $planetupdate;
$debug_query = $db->Execute($planetupdate);
db_op_result($debug_query,__LINE__,__FILE__);

//Deplete armor 
$planetupdate = "UPDATE $dbtables[planets] SET armour_pts=GREATEST(armour_pts-(armour_pts*.05),0) where base='Y' AND owner!=0 and (goods < 10000 or ore < 10000) ";
//echo  $planetupdate;
$debug_query = $db->Execute($planetupdate);
db_op_result($debug_query,__LINE__,__FILE__);

 //freshen armor points 
$planetupdate = "UPDATE $dbtables[planets] SET 
 armour_pts=((POW($level_factor ,(((beams+computer+sensors+jammer+cloak+shields+torp_launchers)/7)+5))*10)*$armor_prod_multiplier)
 where armour_pts > ((POW($level_factor ,(((beams+computer+sensors+jammer+cloak+shields+torp_launchers)/7)+5))*10)*$armor_prod_multiplier) ";
//echo  $planetupdate;
$debug_query = $db->Execute($planetupdate);
db_op_result($debug_query,__LINE__,__FILE__);

TextFlush ( "Safty Check so nothing goes below 0<br>");
//Make sure no planets never go below 0.
$planetupdate = "UPDATE $dbtables[planets] SET armour_normal=armour, credits=GREATEST(credits,0), organics=GREATEST(organics,0), ore=GREATEST(ore,0), goods=GREATEST(goods,0), energy=GREATEST(energy,0), colonists=GREATEST(colonists,0), torps=GREATEST(torps,0), armour_pts=GREATEST(armour_pts,0), fighters=GREATEST(fighters,0)";
$debug_query = $db->Execute($planetupdate);
db_op_result($debug_query,__LINE__,__FILE__);

//Make sure no planets never go above max_credits.
$planetupdate = "UPDATE $dbtables[planets] SET credits=LEAST(credits, max_credits)";
$debug_query = $db->Execute($planetupdate);
db_op_result($debug_query,__LINE__,__FILE__);

TextFlush ( "Planets updated ($multiplier times).<BR><BR>\n");
$multiplier = 0;

?>
