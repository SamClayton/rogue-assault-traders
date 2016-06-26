<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_planets_fast.php

if (preg_match("/sched_planets_fast.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

$expoprod = mypw($colonist_reproduction_rate + 1, $multiplier);
$expoprod *= $multiplier;

$expostarvation_death_rate = 1 - mypw((1 - $starvation_death_rate ), $multiplier);  

TextFlush ( "<b>PLANETS FAST</b><br><br>\n");

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

	$query = $db->Execute("UPDATE $dbtables[planets] as p, $dbtables[spies] as s SET p.organics=GREATEST(p.organics-(p.organics * ($multiplier * s.spy_percent)), 0), " .
								"p.ore=GREATEST(p.ore-(p.ore * ($multiplier * s.spy_percent)), 0), 
								 p.goods=GREATEST(p.goods-(p.goods * ($multiplier * s.spy_percent)), 0), 
								 p.energy=GREATEST(p.energy-(p.energy * ($multiplier * s.spy_percent)), 0), " .
								"p.torps=GREATEST(p.torps-(p.torps * ($multiplier * s.spy_percent)), 0), " .
								"p.fighters=GREATEST(p.fighters-(p.fighters * ($multiplier * s.spy_percent)), 0)
								WHERE s.job_id='1' AND s.active='Y' and p.planet_id=s.planet_id");
	db_op_result($query,__LINE__,__FILE__);

	TextFlush ( "<b>Spies Killing Enemy Colonists</b><br>");

	$query = $db->Execute("UPDATE $dbtables[planets] as p, $dbtables[spies] as s SET " .
								"p.colonists=GREATEST(p.colonists-(p.colonists * ($multiplier * s.spy_percent)), 0)
								WHERE s.job_id='3' AND s.active='Y' and p.planet_id=s.planet_id");
	db_op_result($query,__LINE__,__FILE__);

	TextFlush ( "<b>Spies Stealing Credits</b><br>");

	$query = $db->Execute("UPDATE $dbtables[planets] as p, $dbtables[spies] as s SET " .
								"p.credits=GREATEST(p.credits-(p.credits * ($multiplier * s.spy_percent)), 0)
								WHERE s.job_id='2' AND s.active='Y' and p.planet_id=s.planet_id");
	db_op_result($query,__LINE__,__FILE__);
}

//--==** Planets with 'working' dignitary **==--
// We have to update them one by one, because production etc is different on different planets, 
// depending on the spies activity

if($dig_success_factor)
{
	TextFlush ( "<b>Dignitaries increasing production</b><br>");

    $planetupdate = "UPDATE $dbtables[planets] as p, $dbtables[dignitary] as d SET 
	p.organics=GREATEST(p.organics + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_prate * prod_organics / 100.0 * $expoprod) - (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_consumption * $expoprod), 0), 
    p.ore=p.ore + ((LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)) * $ore_prate * p.prod_ore / 100.0 * $expoprod), 
	p.goods=p.goods + ((LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)) * $goods_prate * p.prod_goods / 100.0 * $expoprod), 
	p.energy=p.energy + ((LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)) * $energy_prate * p.prod_energy / 100.0 * $expoprod), 
	p.fighters=p.fighters + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)) * $fighter_prate * p.prod_fighters / 100.0 * $expoprod, 
	p.torps=p.torps + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)) * $torpedo_prate * p.prod_torp / 100.0 * $expoprod" .
					"WHERE " .
                    "(p.organics + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_prate * " .
                    "p.prod_organics / 100.0 * $expoprod) - (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * " .
                    "$organics_consumption * $expoprod) >= 0) AND d.job_id='1' AND d.active='Y' and p.planet_id=d.planet_id";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);


	TextFlush ( "<b>Dignitaries increasing birthrate</b><br>");

    $planetupdate = "UPDATE $dbtables[planets] as p, $dbtables[dignitary] as d SET 
    p.colonists= LEAST((p.colonists + (p.colonists * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $expoprod)), $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) " .
					"WHERE " .
                     "(p.organics + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_prate * " .
                    "p.prod_organics / 100.0 * $expoprod) - (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * " .
                    "$organics_consumption * $expoprod) >= 0) AND d.job_id='4' AND d.active='Y' and p.planet_id=d.planet_id";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);


	TextFlush ( "<b>Dignitaries decreasing birthrate</b><br>");

    $planetupdate = "UPDATE $dbtables[planets] as p, $dbtables[dignitary] as d SET 
    p.colonists= p.colonists - IF((p.colonists - (p.colonists * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $expoprod)) < $colonist_lower_limit, 0, (p.colonists * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $expoprod)) " .
					"WHERE " .
                     "(p.organics + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_prate * " .
                    "p.prod_organics / 100.0 * $expoprod) - (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * " .
                    "$organics_consumption * $expoprod) >= 0) AND d.job_id='3' AND d.active='Y' and p.planet_id=d.planet_id";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);

	TextFlush ( "<b>Dignitaries increasing interest</b><br>");

    $planetupdate = "UPDATE $dbtables[planets] as p, $dbtables[dignitary] as d SET 
	p.credits = p.credits + (((LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * $colonist_production_rate) * $credits_prate * ((100.0 - p.prod_organics - p.prod_ore - p.prod_goods - p.prod_energy - p.prod_fighters - p.prod_torp - p.prod_research - p.prod_build) / 100.0) * $expoprod * (pow((d.percent + 1) * 1, $multiplier * 1) - 1)))" .
					"WHERE " .
                    "(p.organics + (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * $organics_prate * " .
                    "p.prod_organics / 100.0 * $expoprod) - (LEAST(p.colonists, $colonist_limit + ($colonist_tech_add * ((p.computer + p.sensors + p.beams + p.torp_launchers + p.shields + p.jammer + p.cloak) / 7))) * (pow((d.percent + 1) * 1, $multiplier * 1) - 1) * " .
                    "$organics_consumption * $expoprod) >= 0) AND d.job_id='2' AND d.active='Y' and p.planet_id=d.planet_id";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);

	TextFlush ( "<b>Dignitaries looking for spies</b><br>");


	$res = $db->Execute("SELECT SUM(d.percent) as chancetotal, d.*, p.* FROM $dbtables[dignitary] as d, $dbtables[planets] as p WHERE d.job_id='5' AND d.active='Y' and p.planet_id=d.planet_id group by d.planet_id");
	db_op_result($res,__LINE__,__FILE__);
	while (!$res->EOF)
	{
		$row = $res->fields;

		if (mt_rand(1, 100) < (5+($row['chancetotal']*10000)))
		{
			$result_sf = $db->Execute("SELECT * FROM $dbtables[spies],$dbtables[players] WHERE $dbtables[spies].owner_id=$dbtables[players].player_id and $dbtables[spies].planet_id=$row[planet_id] AND $dbtables[spies].active='Y' "); 
			db_op_result($result_sf,__LINE__,__FILE__);
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
				playerlog($row['owner_id'], LOG_DIG_KILLED_SPY, "$row[name]|$row[sector_id]|$spy1[character_name]");
				playerlog($spy1['player_id'], LOG_SPY_KILLED_SPYOWNER, "$row[name]|$row[sector_id]|$spy1[character_name]");
				TextFlush ( "$row[planet_id] - Spy hunter Dig - found and killed enemy spy<br>");
			}
		}
		$res->MoveNext();
	}

	TextFlush ( "<b>Dignitaries Embezzling credits</b><br>");

	$stamp = date("Y-m-d H:i:s");

	$res = $db->Execute("SELECT d.*, p.* FROM $dbtables[dignitary] as d, $dbtables[planets] as p WHERE d.job_id > '5' and d.job_id < '11' AND d.active='Y' and p.planet_id=d.planet_id and d.reactive_date<='$stamp'");
	db_op_result($res,__LINE__,__FILE__);

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
		if (mt_rand(0,100) <= $dig_embezzler_success and $row['credits'] > 0){
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
				playerlog($playergift, LOG_DIG_MONEY, "$row[dig_id]|$row[name]|$row[sector_id]|$temp");
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
			$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$row[dig_id] ");
			db_op_result($debug_query,__LINE__,__FILE__);

			$result_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$row[planet_id] and owner_id=$row[owner_id]");
			$reccount = $result_s->RecordCount();
			for($spycheck = 0; $spycheck < $reccount; $spycheck++){
				$success=mt_rand(1,1000);
				if($success < ($dig_spy_embezzler * 10)){
					playerlog($row['owner_id'], LOG_SPY_FOUND_EMBEZZLER, "$row[dig_id]|$row[name]");
					TextFlush ( "$row[planet_id] - Embezzler Dig - found by spy<br>");
					$spycheck = $reccount;
				}
			}
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
