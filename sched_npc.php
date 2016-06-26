<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_npc.php

if (preg_match("/sched_npc.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

// *********************************
// ***** Alliance TURN UPDATES *****
// *********************************
TextFlush ( "\n<B>Alliance TURNS</B><br>");

if($sched_i == 0){
	if(mt_rand(1, 10000) < 2500){
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		if ($totrecs > 0){
			$randplay=mt_rand(0,($totrecs-1));
			$sector_id = $getit[$randplay]['sector_id'];
		}	
	}else{
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[planets]");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		if ($totrecs > 0){
			$randplay=mt_rand(0,($totrecs-1));
			$sector_id = $getit[$randplay]['sector_id'];
		}	
	}

	$query = "UPDATE $dbtables[ships] SET class=99, hull=70, engines=70, power=70, computer=70,
		  sensors=70, beams=70, armour=70, cloak=0, torp_launchers=70, shields=70, ecm=70,
		  hull_normal=70, engines_normal=70, power_normal=70, computer_normal=70, ecm_normal=70,
		  sensors_normal=70, beams_normal=70, armour_normal=70, cloak_normal=0, torp_launchers_normal=70, shields_normal=70, fighters=21202551848303,
		  torps=21202551848303, armour_pts=21202551848303 , dev_emerwarp=1, dev_minedeflector=200000000000, dev_escapepod='Y',
		  dev_fuelscoop='Y', dev_nova='Y', energy=106012759241513, sector_id=$sector_id WHERE ship_id=1";
	$debug_query = $db->Execute("$query");
	db_op_result($debug_query,__LINE__,__FILE__);
	TextFlush ( "<br>Alliance Leader Ship Moved<BR>");
}
?>
