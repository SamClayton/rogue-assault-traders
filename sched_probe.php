<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_probe.php

if (preg_match("/sched_probe.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

// *********************************
// ***** Probe TURN UPDATES *****
// *********************************
TextFlush ( "\n<B>Probe TURNS</B>");

// *********************************
// ******* INCLUDE FUNCTIONS *******
// *********************************
include_once ("probe_functions.php");

global $targetlink;


// *********************************
// **** MAKE Probe SELECTION ****
// *********************************

$res = $db->Execute("SELECT * FROM $dbtables[probe] WHERE active='Y' AND type > 1 and sector_id != target_sector ORDER BY sector_id " );

//echo "SELECT * FROM $dbtables[probe] WHERE active='Y' AND type >= 2 and sector_id != target_sector  ORDER BY sector_id";
db_op_result($res,__LINE__,__FILE__);
while (!$res->EOF)
{
		
	   $probeinfo = $res->fields;

		// *********************************
		// ****** RUN THROUGH ORDERS *******
		// *********************************
		$probecount++;
		if (mt_rand(1,5) > 1)								 // ****** 20% CHANCE OF NOT MOVING AT ALL ******

		{
		
			// *********************************
			// ****** ORDERS = 2 WARP ******
			// *********************************
		   if ($probeinfo['type'] == 2)
		   {
		   	TextFlush ( "Probe $probeinfo[probe_id]: I am warping.<br>");
		   		probewarpmove();
		   }
		   // *********************************
		   // ******** ORDERS = 3 RealSpace ***
		   // *********************************
		elseif ($probeinfo['type'] == 3)
		{
		TextFlush ( "Probe $probeinfo[probe_id]: I am realspacing.<br>");
		proberealspacemove();
		}
		// *********************************
		// *** ORDERS = 4 Real SPace seq ***
		// *********************************
		elseif ($probeinfo['type'] == 4)
		{
		TextFlush ( "Probe $probeinfo[probe_id]: I am sequential realspacing.<br>");
		probeseqrealspacemove();
		}

	   }
	$res->MoveNext();
	
	
	// *********************************
	// ***** END OF Probe TURNS *****
	// *********************************
	}
TextFlush ( "<br><br>\n<B>Probe TURNS Completed</B></br><br>");

?>
