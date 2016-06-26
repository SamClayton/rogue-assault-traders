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

TextFlush ( "<b>DEGRADE FAST</b><br>\n");
TextFlush ( "Degrading Sector Fighters<br>");

$degrade_rate = $defence_degrade_rate * 100;

$res = $db->Execute("SELECT sd.*, pl.team from $dbtables[sector_defence] as sd, $dbtables[players] as pl where sd.defence_type = 'F' and pl.player_id=sd.player_id");

while (!$res->EOF)
{
	$row = $res->fields;

	$energy_required = ROUND($row['quantity'] * $energy_per_fighter);
	$res4 = $db->Execute("SELECT SUM(energy) as energy_available from $dbtables[planets] where (owner = $row[player_id] or (team = $row[team] AND $row[team] <> 0)) and sector_id = $row[sector_id] and energy > 0"); 
	$energy_available = $res4->fields['energy_available'];
	TextFlush ( "Sector: $row[sector_id] - available $energy_available, required $energy_required");

	if ($energy_available > $energy_required)
	{
		$where = "";
		$res2 = $db->Execute("SELECT planet_id from $dbtables[planets] where (owner = $row[player_id] or (team = $row[team] AND $row[team] <> 0)) and sector_id = $row[sector_id] and energy > 0"); 
		while (!$res2->EOF)
		{
			$where .= "planet_id=" . $res2->fields['planet_id'] . " or ";
			$res2->MoveNext();
		}
		$where .= "planet_id=-1";
	    $debug_query = $db->Execute("UPDATE $dbtables[planets] set energy = energy - " .
	                 "GREATEST(ROUND($energy_required * (energy / $energy_available)),1)  where $where");

	    db_op_result($debug_query,__LINE__,__FILE__);
	}
	else
	{
		TextFlush(" - degrading");
		sql_sched_degrade_defences($row['defence_id']);
		playerlog($row['player_id'], LOG_DEFENCE_DEGRADE, "$row[sector_id]|$degrade_rate");  
	}

	TextFlush( "<br>\n");
	$res->MoveNext();
}
$db->Execute("DELETE from $dbtables[sector_defence] where quantity <= 0");

TextFlush ( "Sector defense degradation completed<br>\n");
TextFlush ( "<br>");
?>
