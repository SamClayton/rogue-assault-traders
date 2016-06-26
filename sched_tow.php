<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_tow.php

if (preg_match("/sched_tow.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('log_move')) {
	function log_move($player_id,$ship_id,$source,$destination,$class,$error,$zone_id)
	{
		global $db, $dbtables;

		$debug_query = $db->Execute("DELETE from $dbtables[movement_log] WHERE player_id = $player_id and source = $source");
		db_op_result($debug_query,__LINE__,__FILE__);

		$stamp = date("Y-m-d H:i:s");
		$debug_query = $db->Execute("INSERT INTO $dbtables[movement_log] (player_id,ship_id,source,time,destination,ship_class,error_factor,zone_id) VALUES ($player_id,$ship_id,$source,'$stamp',$destination,$class,$error,$zone_id)");
		db_op_result($debug_query,__LINE__,__FILE__);
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

TextFlush ( "<b>TOWING</b><br>\n");
TextFlush ( "Towing bigger players out of restricted zones...<BR>");

$debug_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($debug_query,__LINE__,__FILE__);

$sector_max = $debug_query->fields['sector_id'];

$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] where sg_sector != 1");
$totrecs=$findem->RecordCount(); 
$getit=$findem->GetArray();

$num_to_tow = 0;
do
{
	$debug_query = $db->Execute("SELECT ship_id, $dbtables[players].player_id, character_name, hull, cloak, " .
								"$dbtables[ships].class, $dbtables[ships].sector_id, $dbtables[universe].zone_id, max_hull " .
								"FROM $dbtables[ships], $dbtables[universe],$dbtables[zones] LEFT JOIN " .
								"$dbtables[players] ON $dbtables[ships].player_id=$dbtables[players].player_id WHERE " .
								"$dbtables[ships].sector_id=$dbtables[universe].sector_id AND " .
								"$dbtables[universe].zone_id=$dbtables[zones].zone_id AND max_hull<>0 " .
								"AND ROUND((($dbtables[ships].hull_normal + $dbtables[ships].engines_normal + $dbtables[ships].computer_normal +" .
								" $dbtables[ships].beams_normal + $dbtables[ships].torp_launchers_normal + $dbtables[ships].shields_normal +" .
								" $dbtables[ships].ecm_normal + $dbtables[ships].sensors_normal + $dbtables[ships].cloak_normal + $dbtables[ships].power_normal " .
								"+ $dbtables[ships].armour_normal)/11)) >max_hull AND destroyed='N' and $dbtables[ships].ship_id=$dbtables[players].currentship");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query2 = $db->Execute("SELECT ship_id, $dbtables[players].player_id, character_name, hull, cloak, " .
								"$dbtables[ships].class, $dbtables[ships].sector_id, $dbtables[universe].zone_id, max_hull " .
								"FROM $dbtables[ships], $dbtables[universe],$dbtables[zones] LEFT JOIN " .
								"$dbtables[players] ON $dbtables[ships].player_id=$dbtables[players].player_id WHERE " .
								"$dbtables[ships].sector_id=$dbtables[universe].sector_id AND " .
								"$dbtables[universe].zone_id=$dbtables[zones].zone_id AND max_hull<>0 " .
								"AND ROUND((($dbtables[ships].hull_normal + $dbtables[ships].engines_normal + $dbtables[ships].computer_normal +" .
								" $dbtables[ships].beams_normal + $dbtables[ships].torp_launchers_normal + $dbtables[ships].shields_normal +" .
								" $dbtables[ships].ecm_normal + $dbtables[ships].sensors_normal + $dbtables[ships].cloak_normal + $dbtables[ships].power_normal " .
								"+ $dbtables[ships].armour_normal)/11)) <=max_hull AND destroyed='N' and $dbtables[ships].ship_id=$dbtables[players].currentship");
	db_op_result($debug_query2,__LINE__,__FILE__);

	if ($debug_query or $debug_query2)
	{
		if ($debug_query)
		{
			$num_to_tow = $debug_query->RecordCount();
			$count = 0;
			TextFlush ( "$num_to_tow players to tow:<br>");
			while (!$debug_query->EOF)
			{
				$row = $debug_query->fields;
				TextFlush ( "...towing $row[character_name] out of $row[sector_id] ...");
				$randplay=mt_rand(0,($totrecs-1));
				$newsector = $getit[$randplay]['sector_id'];
				TextFlush ( " to sector $newsector.<br>");
				$debug_query1 = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
				db_op_result($debug_query1,__LINE__,__FILE__);

				$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$row[sector_id]");
				db_op_result($zone_query,__LINE__,__FILE__);
				$zones = $zone_query->fields;

				playerlog($row['player_id'], LOG_TOW, "$row[sector_id]|$newsector|$row[max_hull]");
				log_move($row['player_id'],$row['ship_id'],$row['sector_id'],$newsector,$row['class'],$row['cloak'],$zones['zone_id']);
				$count++;
				$debug_query->MoveNext();
			}
			TextFlush ( "$count players towed<br><br>");
		}
		if ($debug_query2)
		{
			$num_to_tow2 = $debug_query2->RecordCount();
			$count = 0;
			TextFlush ( "Possible $num_to_tow2 players to tow with stored ships:<br>");
			while (!$debug_query2->EOF)
			{
				$row = $debug_query2->fields;
				$r1 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$row[player_id]");
				$totalships = $r1->RecordCount();
				if($totalships > 1){
					TextFlush ( "...towing $row[character_name] out of $row[sector_id] ...");
					$randplay=mt_rand(0,($totrecs-1));
					$newsector = $getit[$randplay]['sector_id'];
					TextFlush ( " to sector $newsector.<br>");
					$debug_query1 = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$newsector,cleared_defences=' ' where ship_id=$row[ship_id]");
					db_op_result($debug_query1,__LINE__,__FILE__);

					$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$row[sector_id]");
					db_op_result($zone_query,__LINE__,__FILE__);
					$zones = $zone_query->fields;

					playerlog($row['player_id'], LOG_TOW, "$row[sector_id]|$newsector|$row[max_hull]");
					log_move($row['player_id'],$row['ship_id'],$row['sector_id'],$newsector,$row['class'],$row['cloak'],$zones['zone_id']);
					$count++;
				}
				$debug_query2->MoveNext();
			}
			TextFlush ( "$count players towed<br><br>");
		}
	}
	else
	{
		TextFlush ( "<br>No players to tow.<br>");
	}
} 
while ($num_to_tow);

TextFlush ( "<br>\n");
$multiplier = 0; //no use to run this again

?>
