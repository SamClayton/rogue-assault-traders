<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: probe_funcs.php

if (preg_match("/probe_functions.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

include ("languages/$langdir/lang_probes.inc");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");

if (!function_exists('mypw')) {
	function mypw($one,$two)
	{
		return pow($one*1,$two*1);
	}
}

if (!function_exists('calc_dist')) {
	function calc_dist($src,$dst) 
	{
		global $db, $dbtables, $enable_spiral_galaxy;
		if ($dst == '' or $src == '')
		{
			return 0;
		}

		$results = $db->Execute("SELECT x,y,z FROM ".$dbtables['universe'].
								" WHERE sector_id=$src OR sector_id=$dst");
		db_op_result($results,__LINE__,__FILE__);

		// Make sure you check for this when calling this function.
		if (!$results)
		{
			return 0;
		}

		$x = $results->fields['x'];
		$y = $results->fields['y'];
		$z = $results->fields['z'];

		$results->MoveNext();

		$x -= $results->fields['x'];
		$y -= $results->fields['y'];
		$z -= $results->fields['z'];

		if($enable_spiral_galaxy != 1){
			$x = sqrt(($x*$x) + ($y*$y) + ($z*$z));
		}else{
    		$x = sqrt(pow($x,2.0)+pow($y,2.0)+pow($z,2.0));
		}

// Make sure it's never less than 1.
		if ($x < 1) 
		{
			return 1;
		}

		return $x;
	}
}

if (!function_exists('SCAN_SUCCESS')) {
	function SCAN_SUCCESS($level_scan, $level_cloak)
	{
		return (10 + $level_scan - $level_cloak) * 5;
	}
}

if (!function_exists('SCAN_SUCCESS')) {
	function SCAN_SUCCESS($level_scan, $level_cloak)
	{
		return (10 + $level_scan - $level_cloak) * 5;
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

function probewarpmove()
{
	// *********************************
	// *** SETUP GENERAL VARIABLES  ****
	// *********************************
	global $db, $dbtables, $probeisdead, $probeinfo, $targetlink,$level_factor,$probe_type;

	// *********************************
	// ***** OBTAIN A TARGET LINK ******
	// *********************************
	$probe_type = "Warp";
	
	if ($targetlink == $probeinfo['sector_id'])
	{
		$targetlink = 0;
	}

		$rswarp=0;
	$linkres = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start='$probeinfo[sector_id]'");
	if ($linkres > 0)
	{
		while (!$linkres->EOF)
		{
			$row = $linkres->fields;
			// *** OBTAIN SECTOR INFORMATION ***
			$sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$row[link_dest]'");
			$sectrow = $sectres->fields;
			$zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
			$zonerow = $zoneres->fields;
		   
				$setlink = mt_rand(0,2);						//*** 33% CHANCE OF REPLACING DEST LINK WITH THIS ONE ***

				if ($setlink == 0 || !$targetlink > 0)		  //*** UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE ***
				{
				
				 	
					$targetlink = $row['link_dest'];
					
				}
			

			$linkres->MoveNext();
		}
	}

	// *********************************
	// ***** IF NO ACCEPTABLE LINK *****
	// *********************************
	// **** TIME TO USE A WORM HOLE ****
	// *********************************
	if ($targetlink>0)
	{
	$resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
		$i = 0;
		$total_sector_fighters = 0;
		$highsensors=0;
		if ($resultf > 0)
		{
			while (!$resultf->EOF)
			{
				$defences[$i] = $resultf->fields;
				$total_sector_fighters += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors DESC",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultf->MoveNext();
			}
		}

		$resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M'");
		$i = 0;
		$total_sector_mines = 0;
		$highsensors=0;
		if ($resultm > 0)
		{
			while (!$resultm->EOF)
			{
				$defences[$i] = $resultm->fields;
				$total_sector_mines += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors DESC",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultm->MoveNext();
			}
		}

		if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
		// ********************************
		// **** DEST LINK HAS DEFENCES ****
		// ********************************
		{

		
		TextFlush ( "sensorstuff[ ". $highsensors."  ".$probeinfo['cloak']."]<br>");
		   $success = SCAN_SUCCESS($highsensors, $probeinfo['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}
				
				$roll = mt_rand(1, 100);
				  if (($roll < $success)and ($total_sector_fighters>0)) 
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe1!");
				$roll = mt_rand(1, 100);
				if (($roll < $success)and ($total_sector_mines>0))
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe2!");
				if ($rswarp==1){
					$distance = calc_dist($probeinfo['sector_id'],$targetlink);
		   			 $shipspeed = mypw($level_factor, $probeinfo['engines']);
					$triptime = round($distance / $shipspeed);
				}else{
					$triptime = 1;
				}


				if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
				{
			   	 $triptime = 1;
				}
				$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

				db_op_result($resultcc,__LINE__,__FILE__);
				if ($resultcc > 0)
				{
				$probeturns=$resultcc->fields;
					if ($probeturns['turns'] >= $triptime)
					{
					$stamp = date("Y-m-d H:i:s");
					$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
					$move_result = $db->Execute ("$query");
					$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");

					probe_detect();
					return;
					
					}else{
						playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
						return;
					}
				}	
		}
		else
		// ********************************
		// **** Safe Move ***
		// ********************************
		{
		TextFlush ( $targetlink."<br>");
		//  Calculate number of turns for RS
		if ($rswarp==1){
		$distance = calc_dist($probeinfo['sector_id'],$targetlink);
			$shipspeed = mypw($level_factor, $probeinfo['engines']);
			$triptime = round($distance / $shipspeed);
			}else{
			$triptime = 1;
			}
			if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
			{
				$triptime = 1;
			}
		$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

db_op_result($resultcc,__LINE__,__FILE__);
			if ($resultcc > 0)
			{
			$probeturns=$resultcc->fields;
				if ($probeturns['turns'] >= $triptime)
				{
				$stamp = date("Y-m-d H:i:s");
				$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
				$move_result = $db->Execute ("$query");
				$res=$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");
db_op_result($res,__LINE__,__FILE__);
				TextFlush ( "Here");
				probe_detect();
				return;
			}else{
			playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
				return;
			}
		}
	}}
	
}


function proberealspacemove()
{
	// *********************************
	// *** SETUP GENERAL VARIABLES  ****
	// *********************************
	global $db, $dbtables, $probeisdead, $probeinfo, $targetlink,$level_factor,$probe_type;
	$probe_type = "Real Space";

	// *********************************
	// ***** OBTAIN A TARGET LINK ******
	// *********************************
	if ($targetlink == $probeinfo['sector_id'])
	{
		$targetlink = 0;
	}

	if($probeinfo['sector_id'] == $probeinfo['target_sector']){
		return;
	}

	 // *** OBTAIN SECTOR INFORMATION ***
	 $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$probeinfo[target_sector]'");
	 $sectrow = $sectres->fields;
	 $zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
	 $zonerow = $zoneres->fields;
	
		 
		  if (($sectrow['zone_id'] == 1) or ($sectrow['zone_id'] > 4))			 //*** UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE ***
		 {
			 $targetlink = $probeinfo[target_sector];
		 }
		else
		{
		playerlog($probeinfo['owner_id'], LOG_PROBE_INVALIDSECTOR, $probeinfo['target_sector']); 
		$targetlink = 0;
		}
			

	// *********************************
	// ***** IF NO ACCEPTABLE LINK *****
   
	if ($targetlink>0)
	{
	$resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
		$i = 0;
		$total_sector_fighters = 0;
		$highsensors=0;
		if ($resultf > 0)
		{
			while (!$resultf->EOF)
			{
				$defences[$i] = $resultf->fields;
				$total_sector_fighters += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultf->MoveNext();
			}
		}

		$resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M'");
		$i = 0;
		$total_sector_mines = 0;
		$highsensors=0;
		if ($resultm > 0)
		{
			while (!$resultm->EOF)
			{
				$defences[$i] = $resultm->fields;
				$total_sector_mines += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultm->MoveNext();
			}
		}

		if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
		// ********************************
		// **** DEST LINK HAS DEFENCES ****
		// ********************************
		{

		
		TextFlush ( "sensorstuff[ ". $highsensors."  ".$probeinfo['cloak']."]<br>");
		   $success = SCAN_SUCCESS($highsensors, $probeinfo['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}
				
				$roll = mt_rand(1, 100);
				  if (($roll < $success)and ($total_sector_fighters>0)) 
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe1!");
				$roll = mt_rand(1, 100);
				if (($roll < $success)and ($total_sector_mines>0))
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe2!");
				$distance = calc_dist($probeinfo['sector_id'],$targetlink);
		   		$shipspeed = mypw($level_factor, $probeinfo['engines']);
				$triptime = round($distance / $shipspeed);

				if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
				{
			   	 $triptime = 1;
				}
				$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

				db_op_result($resultcc,__LINE__,__FILE__);
				if ($resultcc > 0)
				{
				$probeturns=$resultcc->fields;
					if ($probeturns['turns'] >= $triptime)
					{
					$stamp = date("Y-m-d H:i:s");
					$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
					$move_result = $db->Execute ("$query");
					$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");

					probe_detect();
					return;
					
					}else{
						playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
						return;
					}
				}	
		}
		else
		// ********************************
		// **** Safe Move ***
		// ********************************
		{
		//  Calculate number of turns for RS
		$distance = calc_dist($probeinfo['sector_id'],$targetlink);
			$shipspeed = mypw($level_factor, $probeinfo['engines']);
			$triptime = round($distance / $shipspeed);

			if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
			{
				$triptime = 1;
			}
		$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

db_op_result($resultcc,__LINE__,__FILE__);
			if ($resultcc > 0)
			{
			$probeturns=$resultcc->fields;
				if ($probeturns['turns'] >= $triptime)
				{
				$stamp = date("Y-m-d H:i:s");
				$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
				$move_result = $db->Execute ("$query");
				$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");

				probe_detect();
				return;
			}else{
			playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
				return;
			}
		}
	}}
	
}


function probeseqrealspacemove()
{
	// *********************************
	// *** SETUP GENERAL VARIABLES  ****
	// *********************************
	global $db, $dbtables, $probeisdead, $probeinfo, $targetlink,$level_factor,$probe_type;
	$probe_type = "Real Space";

	// *********************************
	// ***** OBTAIN A TARGET LINK ******
	// *********************************
	if ($targetlink == $probeinfo['sector_id'])
	{
		$targetlink = 0;
	}

	if($probeinfo['sector_id'] == $probeinfo['target_sector']){
		return;
	}

	if ($probeinfo['sector_id'] > $probeinfo['target_sector'])
	{
		$nextsector=$probeinfo['sector_id']-1;
	}else{
		$nextsector=$probeinfo['sector_id']+1;
	}
	TextFlush ( $probeinfo['probe_id']."  ". $nextsector."<br>");
		  $row = $linkres->fields;
		  // *** OBTAIN SECTOR INFORMATION ***
		  $sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$nextsector'");
		  $sectrow = $sectres->fields;
		  $zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
		  $zonerow = $zoneres->fields;
		 
			  
			  if (($sectrow['zone_id'] == 1) or ($sectrow['zone_id'] > 4))		//*** UNLESS THERE IS NO DEST LINK, CHHOSE THIS ONE ***
			  {
				  $targetlink = $nextsector;
			  }
		else
		{
		playerlog($probeinfo['owner_id'], LOG_PROBE_INVALIDSECTOR, $probeinfo['target_sector']); 
		}
			


	// *********************************
	// ***** IF NO ACCEPTABLE LINK *****
   
	if ($targetlink>0)
	{
	$resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' ORDER BY quantity DESC");
		$i = 0;
		$total_sector_fighters = 0;
		$highsensors=0;
		if ($resultf > 0)
		{
			while (!$resultf->EOF)
			{
				$defences[$i] = $resultf->fields;
				$total_sector_fighters += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultf->MoveNext();
			}
		}

		$resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M'");
		$i = 0;
		$total_sector_mines = 0;
		$highsensors=0;
		if ($resultm > 0)
		{
			while (!$resultm->EOF)
			{
				$defences[$i] = $resultm->fields;
				$total_sector_mines += $defences[$i]['quantity'];
				$fmowners = $defences[$i]['player_id'];
				// Get Players ship sensors
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
				$ship_owner = $result3->fields;
				if ($ship_owner['sensors'] > $highsensors){
					$highsensors=$ship_owner['sensors'];
				}
				// get planet sensors
				$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
				$planets = $result4->fields;
				if ($planets['sensors'] > $highsensors){
					$highsensors=$planets['sensors'];
				}
				
				
				$i++;
				$resultm->MoveNext();
			}
		}

		if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
		// ********************************
		// **** DEST LINK HAS DEFENCES ****
		// ********************************
		{

		
		TextFlush ( "sensorstuff[ ". $highsensors."  ".$probeinfo['cloak']."]<br>");
		   $success = SCAN_SUCCESS($highsensors, $probeinfo['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}
				
				$roll = mt_rand(1, 100);
				  if (($roll < $success)and ($total_sector_fighters>0)) 
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe1!");
				$roll = mt_rand(1, 100);
				if (($roll < $success)and ($total_sector_mines>0))
				{
		   		TextFlush ( "explode");
		   
			   playerlog($probeinfo['owner_id'], LOG_PROBE_DESTROYED, $targetlink);
			 $resultdestroy = $db->Execute ("delete from $dbtables[probe] WHERE probe_id=$probeinfo[probe_id]");
				return;
				}
				TextFlush ( "Safe2!");
				$distance = calc_dist($probeinfo['sector_id'],$targetlink);
		   		$shipspeed = mypw($level_factor, $probeinfo['engines']);
				$triptime = round($distance / $shipspeed);

				if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
				{
			   	 $triptime = 1;
				}
				$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

				db_op_result($resultcc,__LINE__,__FILE__);
				if ($resultcc > 0)
				{
				$probeturns=$resultcc->fields;
					if ($probeturns['turns'] >= $triptime)
					{
					$stamp = date("Y-m-d H:i:s");
					$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
					$move_result = $db->Execute ("$query");
					$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");

					probe_detect();
					return;
					
					}else{
						playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
						return;
					}
				}	
		}
		else
		// ********************************
		// **** Safe Move ***
		// ********************************
		{
		//  Calculate number of turns for RS
		$distance = calc_dist($probeinfo['sector_id'],$targetlink);
			$shipspeed = mypw($level_factor, $probeinfo['engines']);
			$triptime = round($distance / $shipspeed);

			if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
			{
				$triptime = 1;
			}
		$resultcc = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");

db_op_result($resultcc,__LINE__,__FILE__);
			if ($resultcc > 0)
			{
			$probeturns=$resultcc->fields;
				if ($probeturns['turns'] >= $triptime)
				{
				$stamp = date("Y-m-d H:i:s");
				$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
				   "WHERE player_id=$probeinfo[owner_id]";
				$move_result = $db->Execute ("$query");
				$db->Execute("UPDATE $dbtables[probe] SET sector_id=$targetlink WHERE probe_id=$probeinfo[probe_id]");

				probe_detect();
				return;
			}else{
			playerlog($probeinfo['owner_id'], LOG_PROBE_NOTURNS, "$probeinfo[probe_id]|$targetlink"); 
				return;
			}
		}
	}}
	
}

function probe_detect()
{
	global $l_armour,$l_none,$l_planet_noscan,$l_planet_scn_report,$l_commodities, $l_power, $l_ewd;
	global$l_organics,$l_ore,$l_goods,$l_energy,$l_colonists,$l_credits, $l_hull, $l_engines, $l_escape_pod;
	global $l_defense,$l_base,$l_base, $l_torps,$l_fighters,$l_computer,$l_beams, $l_torp_launch;
	global $l_torp_launch,$l_sensors,$l_cloak,$l_shields,$l_jammer, $l_unknown, $l_ecm, $l_probe_planetname;
	global $l_armourpts,$l_planet_ison,$lssd_level_two,$lssd_level_three, $l_deflect, $l_probe2_noneowned;
	global $db;
	global $dbtables,$probeinfo,$targetlink;
	global $l_unnamed,$probe_type, $l_probe2_warplink, $l_probe2_player, $l_probe2_onboarda, $l_probe2_classship;
	global $l_probe2_traveled, $l_probe2_fedjammed, $l_probe2_lastseen, $l_probe2_portfound, $l_probe2_supports;
	global $l_probe2_planets, $l_probe2_sectord, $l_probe2_mines, $l_probe2_fighters, $l_probe2_ships;
	global $l_probe2_ships2, $l_probe2_warplinks, $lss_decay_time;

	$sensors = $probeinfo['sensors'];
	$probe_id=$probeinfo['probe_id'];
	$owner_id=$probeinfo['owner_id'];
	$sector=$probeinfo['sector_id'];
		$success = SCAN_SUCCESS($sensors, 5);
	if ($success < 5)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		// Warp Links
		 $result2 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$sector'");
		$num_links = $result2->RecordCount();
		
		$warplinks="";
		if ($num_links == 0)
		{
			$warplinks= "";
		}
		else
		{
			 $warplinks= "";
			$linknumber = 0;
			for($i = 0; $i < $num_links; $i++)
			{
			$links[$i] = $result2->fields;
			// Last Ship Seen
				 if ($links[$i] != '0')
				{
					$linknumber++;
					$warplinks.="$l_probe2_warplink $linknumber: ".$links[$i]['link_dest'];
					$destination=$links[$i]['link_dest'];
					$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
					$decaydate = date("Y-m-d H:i:s", $oldstamp);
					$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> '$owner_id' AND source = $destination and time > '$decaydate' ORDER BY time DESC",1);

					db_op_result($resx,__LINE__,__FILE__);
					
					$myrow = $resx->fields;
					$count = $resx->RecordCount;
					echo $count;
					if (!$myrow)
					{
						$warplinks.= " - $l_none<br>";
					}
					else
					{
						if($destination != 1){
							if ($sensors >= $lssd_level_three)
							{
								$warplinks.= " - $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship $l_probe2_traveled " . $myrow['destination'] . " <br>";
							}
							elseif ($sensors >= $lssd_level_two)
							{
								$warplinks.= " - $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
							}
							else
							{
								$warplinks.= " - " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
							}
						}
						else
						{
								$warplinks.= " - $l_probe2_fedjammed <br>";
						}
					}
				}
				 $result2->MoveNext();
			}
		}
		}else{
		$warplinks="";
		}
		// Last Ship Seen in sector
		$success = SCAN_SUCCESS($sensors, 10);
	if ($success < 5)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
		$decaydate = date("Y-m-d H:i:s", $oldstamp);
		$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> '$owner_id' AND source = $sector and time > '$decaydate' ORDER BY time DESC",1);
		db_op_result($resx,__LINE__,__FILE__);
		$myrow = $resx->fields;
		$count = $resx->RecordCount;
		echo $count;
		$lastship="";
		if (!$myrow)
		{
			$lastship.= "Last Ship Seen: $l_none<br>";
		}
		else
		{
			if($sector != 1){
				if ($sensors >= $lssd_level_three)
				{
					$lastship = "$l_probe2_lastseen: $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship $l_probe2_traveled " . $myrow['destination'] . " <br>";
				}
				elseif ($sensors >= $lssd_level_two)
				{
					$lastship = "$l_probe2_lastseen: $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
				}
				else
				{
					$lastship = "$l_probe2_lastseen: " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
				}
			}
			else
			{
				$lastship = "$l_probe2_fedjammed <br>";
			}
		}
	}else{
		$lastship="";
	}

	// Detect port and sun and warps
	$success = SCAN_SUCCESS($sensors, 5);
	if ($success < 5)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
		 $query96 = $result2->fields;
		
		
		 $port_type = $query96['port_type'];
		 $star_size = $query96['star_size']; 
		 $portinfo="$l_probe2_portfound: ".$port_type." - $l_probe2_supports ".$star_size." $l_probe2_planets.<br>";
		 
		 }else{
		 $portinfo="";
		 }
		 $success = SCAN_SUCCESS($sensors, 15);
	if ($success < 15)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		// Detect Sector Defence
		 $resultSDa = $db->Execute("SELECT SUM(quantity) as mines from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='M'");
		$resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='F'");
		$defM = $resultSDa->fields;
		$defF = $resultSDb->fields;
		$has_mines = NUMBER($defM['mines']);
		$has_fighters = NUMBER($defF['fighters']);
		
		$sector_def="$l_probe2_sectord: ".$has_mines." $l_probe2_mines ".$has_fighters." $l_probe2_fighters<br>";
		}else{
		$sector_def="";
		}
		// Detect ships
		  $success = SCAN_SUCCESS($sensors, 10);
	if ($success < 15)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
	$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		if ($sector != 0)
	{
		// get ships located in the scanned sector
		$result4 = $db->Execute("SELECT * FROM $dbtables[ships] " .
								"LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id " .
								"WHERE sector_id='$sector' AND on_planet='N'");
				$shipdetect="";				
		if ($result4->EOF)
		{
			$shipdetect.= "$l_probe2_ships: $l_none<br>";
		}
		else
		{
			$num_detected = 0;
			while (!$result4->EOF)
			{
				$row = $result4->fields;
				// display other ships in sector - unless they are successfully cloaked
				$success = SCAN_SUCCESS($sensors, $row['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}
				
				$roll = mt_rand(1, 100);
				if ($roll < $success)
				{
					$num_detected++;
					$shipdetect.="$l_probe2_ships2 $num_detected: ".$row['name'] . "(" . $row['character_name'] . ") - ";
					// probe detect incoming ship
					// Get type of ship
			 		$roll = mt_rand(1, 100);
					$shiptype=$l_unknown;
					if ($roll < $success)
					{
						$res2 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$row[class];");
						db_op_result($res2,__LINE__,__FILE__);
						$shiptype = $res2->fields['name'];
					}
					$res3 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$row[class];");
					db_op_result($res3,__LINE__,__FILE__);
					$shiptype = $res3->fields['name'];
					$shipdetect.="($shiptype)<br>";
					$roll = mt_rand(1, 100);
					//scan ship
					if($sensors > 1){
						if ($roll < $success)
						{
							$sc_error = SCAN_ERROR($sensors, $row['cloak']);

							$sc_hull = (mt_rand(1, 100) < $success) ? round($row['hull'] * $sc_error / 100) : "???";
							$sc_engines = (mt_rand(1, 100) < $success) ? round($row['engines'] * $sc_error / 100) : "???";
							$sc_power = (mt_rand(1, 100) < $success) ? round($row['power'] * $sc_error / 100) : "???";
							$sc_computer = (mt_rand(1, 100) < $success) ? round($row['computer'] * $sc_error / 100) : "???";
							$sc_sensors = (mt_rand(1, 100) < $success) ? round($row['sensors'] * $sc_error / 100) : "???";
							$sc_beams = (mt_rand(1, 100) < $success) ? round($row['beams'] * $sc_error / 100) : "???";
							$sc_torp_launchers = (mt_rand(1, 100) < $success) ? round($row['torp_launchers'] * $sc_error / 100) : "???";
							$sc_armour = (mt_rand(1, 100) < $success) ? round($row['armour'] * $sc_error / 100) : "???";
							$sc_shields = (mt_rand(1, 100) < $success) ? round($row['shields'] * $sc_error / 100) : "???";
							$sc_cloak = (mt_rand(1, 100) < $success) ? round($row['cloak'] * $sc_error / 100) : "???";
							$sc_ecm = (mt_rand(1, 100) < $success) ? round($row['ecm'] * $sc_error / 100) : "???";
							$sc_armour_pts = (mt_rand(1, 100) < $success) ? round($row['armour_pts'] * $sc_error / 100) : "???";
							$sc_ship_fighters = (mt_rand(1, 100) < $success) ? round($row['fighters'] * $sc_error / 100) : "???";
							$sc_torps = (mt_rand(1, 100) < $success) ? round($row['torps'] * $sc_error / 100) : "???";
							$sc_credits = (mt_rand(1, 100) < $success) ? round($row['credits'] * $sc_error / 100) : "???";
							$sc_ship_energy = (mt_rand(1, 100) < $success) ? round($row['energy'] * $sc_error / 100) : "???";
							$sc_dev_minedeflector = (mt_rand(1, 100) < $success) ? round($row['dev_minedeflector'] * $sc_error / 100) : "???";
							$sc_dev_emerwarp = (mt_rand(1, 100) < $success) ? round($row['dev_emerwarp'] * $sc_error / 100) : "???";
							$sc_dev_pod = (mt_rand(1, 100) < $success) ? round($row['dev_escapepod'] * $sc_error / 100) : "???";
							$sc_ship_colonists = (mt_rand(1, 100) < $success) ? round($row['colonists'] * $sc_error / 100) : "???";
							$sc_ship_ore = (mt_rand(1, 100) < $success) ? round($row['ore'] * $sc_error / 100) : "???";
							$sc_ship_organics = (mt_rand(1, 100) < $success) ? round($row['organics'] * $sc_error / 100) : "???";
							$sc_ship_goods = (mt_rand(1, 100) < $success) ? round($row['goods'] * $sc_error / 100) : "???";
							$sc_dev_warpedit = (mt_rand(1, 100) < $success) ? round($row['dev_warpedit'] * $sc_error / 100) : "???";
							$sc_dev_genesis = (mt_rand(1, 100) < $success) ? round($row['dev_genesis'] * $sc_error / 100) : "???";
							$sc_scoop = (mt_rand(1, 100) < $success) ? round($row['dev_fuelscoop'] * $sc_error / 100) : "???";

							$shipdetect.="&nbsp;&nbsp;&nbsp;$l_hull: ".$sc_hull." $l_engines: ".$sc_engines." $l_power: ".$sc_power." $l_computer: ".$sc_computer." $l_sensors: ".$sc_sensors."<br>&nbsp;&nbsp;&nbsp;$l_beams: ".$sc_beams." $l_torp_launch: ".$sc_torp_launchers." $l_armour: ".$sc_armour." $l_shields: ".$sc_shields." $l_cloak: ".$sc_cloak." $l_ecm: ".$sc_ecm."<br>&nbsp;&nbsp;&nbsp;$l_armourpts: ".$sc_armour_pts." $l_fighters: ".$sc_ship_fighters." $l_torps: ".$sc_torps." $l_energy: ".$sc_ship_energy." $l_credits: ".$sc_credits."<br>&nbsp;&nbsp;&nbsp;$l_deflect: ".$sc_dev_minedeflector." $l_ewd: ".$sc_dev_emerwarp." $l_escape_pod: ".$sc_dev_pod."<br>";
						}
					}
				}
				$result4->MoveNext();
			}
			if ($num_detected == 0)
			{
			   $shipdetect = "$l_probe2_ships: $l_none<br>";
			}
		}
	}	
	
	}else{
		$shipdetect="";
	}
	
	 $success = SCAN_SUCCESS($sensors, 5);
	if ($success < 5)
	{
		$success = 5;
	}
	if ($success > 95)
	{
		$success = 95;
	}
		$roll = mt_rand(1, 100);
	if ($roll < $success)
	{
		// Detect Planets
		$has_planets = 0;

		$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$sector' and owner!=$owner_id");
		$planetinfo="";
		if ($result3->RecordCount() ==0)
		  	$planetinfo="$l_probe2_noneowned<br>";

		$totalplanetsfound = 0;

		while (!$result3->EOF)
		{
			$uber = 0;
			$success = 0;
			$hiding_planet[$i] = $result3->fields;
			$powner=$hiding_planet[$i]['owner'];
			echo $powner;
			// Get Char name
			$pname = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id = $powner");
 			 db_op_result($pname,__LINE__,__FILE__);
			if ($pname)
			{
				$resn = $pname->fields;
				$playername = $resn['character_name'];
			}
			else
			{
				$playername=$l_unknown;
			}
			if ($hiding_planet[$i]['owner'] == $owner_id)
			{
				$uber = 1;
			}

			if ($hiding_planet[$i]['team'] != 0)
			{
				if ($hiding_planet[$i]['team'] == $owner_id)
				{
					$uber = 1;
				}
			}

			if ($sensors >= $hiding_planet[$i]['cloak'])
			{
				$uber = 1;
			}

			if ($uber == 0) //Not yet 'visible'
			{
				$success = SCAN_SUCCESS($sensors, $hiding_planet[$i]['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}

				$roll = mt_rand(1, 100);
				if ($roll <= $success) // If able to see the planet
				{
					$uber = 1; //confirmed working
				}

				if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
				{
					$res_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '" . $hiding_planet[$i]['planet_id'] . "' AND owner_id = '$playerinfo[player_id]'");
					if ($res_s->RecordCount())
					$uber = 1;
				}
			}

			if ($uber == 1)
			{
				$totalplanetsfound++;

				$planets[$i] = $result3->fields;
				$success = (10 - $hiding_planet[$i]['cloak'] / 2 + $sensors) * 5;
				if ($success < 5)
				{
					$success = 5;
				}
				if ($success > 95)
				{
					$success = 95;
				}
				$roll = mt_rand(1, 100);
				if ($roll > $success)
				{
					$planetinfo.= "$l_probe_planetname $totalplanetsfound: $l_planet_noscan<BR>";
				}
				else
				{
					// scramble results by scan error factor. 
					$sc_error= SCAN_ERROR($sensors, $hiding_planet[$i]['jammer']);
					$sc_error_plus=100;
					if ($sc_error < 100){
						$sc_error_plus=115;
					}
					if (empty($hiding_planet[$i]['name']))
						$hiding_planet[$i]['name'] = $l_unnamed;

					$preport = str_replace("[name]",$hiding_planet[$i]['name'] ,$l_planet_scn_report );
					$preport = str_replace("[owner]",$playername ,$preport );
					$planetinfo.= "$l_probe_planetname $totalplanetsfound: $preport<BR>";
					$planetinfo.= "&nbsp;&nbsp;&nbsp;$l_organics: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_organics=NUMBER(round($hiding_planet[$i]['organics'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_organics";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_ore: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_ore=NUMBER(round($hiding_planet[$i]['ore'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_ore";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_goods: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_goods=NUMBER(round($hiding_planet[$i]['goods'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_goods";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_energy: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_energy=NUMBER(round($hiding_planet[$i]['energy'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_energy";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_colonists: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_colonists=NUMBER(round($hiding_planet[$i]['colonists'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_colonists";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_credits: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_credits=NUMBER(round($hiding_planet[$i]['credits'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_credits";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= "<br><br>$l_defense:<br>";
					$planetinfo.= "&nbsp;&nbsp;&nbsp;$l_base: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$planetinfo.= $hiding_planet[$i]['base'];
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_torps: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_base_torp=NUMBER(round($hiding_planet[$i]['torps'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_base_torp";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_fighters: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_fighters=NUMBER(round($hiding_planet[$i]['fighters'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_fighters";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= "<br>&nbsp;&nbsp;&nbsp;$l_computer: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_planet_computer=NUMBER(round($hiding_planet[$i]['computer'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_planet_computer";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_beams: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_beams=NUMBER(round($hiding_planet[$i]['beams'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_beams";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_torp_launch: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_torp_launchers=NUMBER(round($hiding_planet[$i]['torp_launchers'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_torp_launchers";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_sensors: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_sensors=NUMBER(round($hiding_planet[$i]['sensors'] *(mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_sensors";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= "<br>&nbsp;&nbsp;&nbsp;$l_cloak: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_cloak=NUMBER(round($hiding_planet[$i]['cloak'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_cloak";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_shields: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_cloak=NUMBER(round($hiding_planet[$i]['shields'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_shields";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_jammer: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_jammer=NUMBER(round($hiding_planet[$i]['jammer'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_jammer";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_armour: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_armour=NUMBER(round($hiding_planet[$i]['armour'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_armour";
					}
					else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= " - $l_armourpts: ";

					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_armour_pts=NUMBER(round($hiding_planet[$i]['armour_pts'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						$planetinfo.= "$sc_armour_pts";
					}
						else
					{
						$planetinfo.= "???";
					}
					$planetinfo.= "<BR>";

					$planet_id=$hiding_planet[$i]['planet_id'];
					$resa = $db->Execute("SELECT $dbtables[ships].*, $dbtables[players].character_name FROM $dbtables[ships] LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE on_planet = 'Y' and planet_id = $planet_id");
					db_op_result($resa,__LINE__,__FILE__);

					while (!$resa->EOF)
					{
						$row = $resa->fields;
						$success = SCAN_SUCCESS($sensors, $row['cloak']);
						if ($success < 5)
						{
							$success = 5;
						}
						if ($success > 95)
						{
							$success = 95;
						}

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$planetinfo.= "&nbsp;&nbsp;&nbsp;<B>$row[character_name] $l_planet_ison</B><BR>";
						}
						$resa->MoveNext();
					}
				}
				$has_planets++;
			}

			$planetinfo.="<br>";
			$i++;
			$result3->MoveNext();
		}
		
		}else{
			$planetinfo="";
		}
		echo "$l_probe2_warplinks: ".$warplinks."<br>";
		echo "$l_probe2_lastseen: ".$lastship."<br>";
		echo "$l_probe2_portfound: ".$portinfo."<br>";
		echo "$l_probe2_sectord: ".$sector_def."<br>";
		echo "$l_probe2_ships: ".$shipdetect."<br>";
		echo "$l_probe_planetname: ".$planetinfo."<br>";

		$stamp = date("Y-m-d H:i:s");

		$probe_detect="";
		if ($warplinks!=""){
			$probe_detect.=$warplinks;
		}
		if ($lastship!=""){
			$probe_detect.=$lastship;
		}
		if ($portinfo!=""){
			$probe_detect.=$portinfo;
		}
		if ($sector_def!=""){
			$probe_detect.=$sector_def;
		}
		if ($shipdetect!=""){
			$probe_detect.=$shipdetect;
		}
		if ($planetinfo!=""){
			$probe_detect.=$planetinfo;
		}
		
		playerlog($probeinfo['owner_id'], LOG_PROBE_DETECTPROBE, "$probeinfo[probe_id]|$sector|$probe_detect|$probe_type"); 

		return;
	
}


?>