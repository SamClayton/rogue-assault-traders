<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_degrade.php

if (preg_match("/sched_degrade.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

TextFlush ( "<b>DEGRADE</b><br>\n");
TextFlush ( "Degrading Sector Fighters<br>");
$res = $db->Execute("SELECT * from $dbtables[sector_defence] where defence_type = 'F'");

while (!$res->EOF)
{
	$row = $res->fields;
	$res3 = $db->Execute("SELECT team from $dbtables[players] where player_id = $row[player_id]");
	$sched_playerinfo = $res3->fields;

	$res2 = $db->Execute("SELECT owner, planet_id from $dbtables[planets] where (owner = $row[player_id] or (team = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id] and energy > 0"); 
	if ($res2->EOF)
	{	
		if ($row['defence_id']!=""){
			sql_sched_degrade_defences($row['defence_id']);
			$degrade_rate = $defence_degrade_rate * 100;
			playerlog($row['player_id'], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");
		}
	}
	else
	{
		$energy_required = ROUND($row['quantity'] * $energy_per_fighter);
		$res4 = $db->Execute("SELECT IFNULL(SUM(energy),0) as energy_available from $dbtables[planets] where (owner = $row[player_id] or (team = $sched_playerinfo[team] AND $sched_playerinfo[team] <> 0)) and sector_id = $row[sector_id]"); 
		$planet_energy = $res4->fields;
		$energy_available = $planet_energy['energy_available'];
		TextFlush ( "available $energy_available, required $energy_required.<br>\n");

		if ($energy_available > $energy_required)
		{
			while (!$res2->EOF)
			{
				$degrade_row = $res2->fields;
				sql_sched_degrade_energy($degrade_row['planet_id']);
				$res2->MoveNext();
			}
		}
		else
		{
			sql_sched_degrade_defences($row['defence_id']);
			$degrade_rate = $defence_degrade_rate * 100;
			playerlog($row['player_id'], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");  
		}
		
	}
	$res->MoveNext();
}
$db->Execute("DELETE from $dbtables[sector_defence] where quantity <= 0");

TextFlush ( "Sector defense degradation completed<br>\n");
TextFlush ( "<br>");
?>
