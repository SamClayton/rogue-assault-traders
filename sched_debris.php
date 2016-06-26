<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_federation.php

if (preg_match("/sched_debris.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

TextFlush ( "<B>Create and Move New Debris Around</B><BR>");

TextFlush ( "<br>Moving Debris around the Universe.<br>");

$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] order by sector_id DESC");
$totrecs=$findem->RecordCount(); 
$getit=$findem->GetArray();
$sector_max = $getit[0]['sector_id'];

$res=$db->Execute("UPDATE $dbtables[debris] SET sector_id = FLOOR(RAND() * $sector_max)");
db_op_result($res,__LINE__,__FILE__);

TextFlush ( "<br>Finished Moving Debris around the Universe.<br>");

$result4 = $db->Execute(" SELECT sector_id FROM $dbtables[debris]");
db_op_result($result4,__LINE__,__FILE__);

$totalcount = $result4->RecordCount();

if($totalcount < $debris_max){
	TextFlush ( "<br>Adding new Debris to the Universe.<br>");
	for($i = $totalcount; $i < $debris_max; $i++){
		$none = 5;
		$turns = 80;
		$torps = 90;
		$fighters = 100;
		$armor = 150;
		$energy = 200;
		$credits = 250;
		$spy = 300;
		$wormhole = 350;
		$level = 400;
		$level_all = 450;
		$sg = 500;
		$nova = 550;
		$destroy = 1600;

		$none_trigger = floor($none / 2);
		$turns_trigger = floor($turns / 2);
		$torps_trigger = floor($torps / 2);
		$fighters_trigger = floor($fighters / 2);
		$armor_trigger = floor($armor / 2);
		$energy_trigger = floor($energy / 2);
		$credits_trigger = floor($credits / 2);
		$spy_trigger = floor($spy / 2);
		$wormhole_trigger = floor($wormhole / 2);
		$level_trigger = floor($level / 2);
		$level_all_trigger = floor($level_all / 2);
		$sg_trigger = floor($sg_torp / 2);
		$nova_trigger = floor($nova / 2);
		$destroy_trigger = floor($destroy / 2);

		$flag = 1;

		$success = mt_rand(0, $none);
		if ($success == $none_trigger && $flag)
		{
			$debris_type = 0;
			$debris_data = 0;
			$flag = 0;
		}

		$success = mt_rand(0, $turns);
		if ($success == $turns_trigger && $flag && $enable_debris_turns != 0)
		{
			$debris_type = 1;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $torps);
		if ($success == $torps_trigger && $flag && $enable_debris_torps != 0)
		{
			$debris_type = 2;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $fighters);
		if ($success == $fighters_trigger && $flag && $enable_debris_fighters != 0)
		{
			$debris_type = 3;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $armor);
		if ($success == $armor_trigger && $flag && $enable_debris_armor != 0)
		{
			$debris_type = 4;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $energy);
		if ($success == $energy_trigger && $flag && $enable_debris_energy != 0)
		{
			$debris_type = 5;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $credits);
		if ($success == $credits_trigger && $flag && $enable_debris_credits != 0)
		{
			$debris_type = 6;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $spy);
		if ($success == $spy_trigger && $flag && $enable_debris_spy != 0)
		{
			$debris_type = 7;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $wormhole);
		if ($success == $wormhole_trigger && $flag && $enable_debris_wormhole != 0)
		{
			$debris_type = 8;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $level);
		if ($success == $level_trigger && $flag && $enable_debris_level != 0)
		{
			$debris_type = 9;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $level_all);
		if ($success == $level_all_trigger && $flag && $enable_debris_levelall != 0)
		{
			$debris_type = 10;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $sg);
		if ($success == $sg_trigger && $flag && $enable_debris_sg != 0)
		{
			$debris_type = 11;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $nova);
		if ($success == $nova_trigger && $flag && $enable_debris_nova != 0)
		{
			$debris_type = 12;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		$success = mt_rand(0, $destroy);
		if ($success == $destroy_trigger && $flag && $enable_debris_destroy != 0)
		{
			$debris_type = 13;
			$debris_data = 1;
			if(mt_rand(0, 1) == 1)
				$debris_data = -1 * $debris_data;
			$flag = 0;
		}

		if($flag == 0){
			if ($totrecs > 0){
				$randplay=mt_rand(0,($totrecs-1));
				$targetlink = $getit[$randplay]['sector_id'];
				TextFlush ( "ADDED debris_type: $debris_type, debris_data: $debris_data, debris_sector: $targetlink<br>");
				$debug_query = $db->Execute("INSERT INTO $dbtables[debris] (debris_type, debris_data, sector_id) values ($debris_type,'$debris_data', $targetlink)");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
	}
}

$multiplier = 0;
TextFlush ( "<br><B>Create and Move New Debris Finished</B><BR>");
TextFlush ( "<BR>\n");
?>
