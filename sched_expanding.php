<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_trade.php

if (preg_match("/sched_expanding.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

function sector_todb($array,$method,$sector_id,$be_quiet)
{
	global $db, $db_type, $dbtables, $silent;

	$silent = 1;

	$sql = "SELECT * FROM $dbtables[universe] WHERE sector_id = $sector_id"; 

	// Execute the query and get the empty recordset
	$debug_query_rs = $db->Execute($sql);
	db_op_result($debug_query_rs,__LINE__,__FILE__);

	if ($be_quiet == 1)
	{
		$silent = 1;
	}

	if ($be_quiet == 0)
	{
		TextFlush ( $method."ing sector ". $sector_id . " ");
	}

	// Adodb generates the insert statement will be for the array.
	$silent = 0;
	$debug_query_insert  = $db->GetInsertSQL($debug_query_rs, $array);
	db_op_result($debug_query_insert,__LINE__,__FILE__);
	$silent = 1;

	$debug_query = $db->Execute($debug_query_insert);
	db_op_result($debug_query,__LINE__,__FILE__);

}

function check_php_version () {
   $testSplit = explode ('.', '4.3.0');
   $currentSplit = explode ('.', phpversion());

   if ($testSplit[0] < $currentSplit[0])
       return True;
   if ($testSplit[0] == $currentSplit[0]) {
       if ($testSplit[1] < $currentSplit[1])
           return True;
       if ($testSplit[1] == $currentSplit[1]) {
           if ($testSplit[2] <= $currentSplit[2])
               return True;
       }
   }
   return False;
}


if (!check_php_version ())
{
	$enable_spiral_galaxy = 0;
}

if($allow_expanding_universe == 1){
	TextFlush ( "<b>Expanding Universe</b><br><br>\n");

	$debug_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
	db_op_result($debug_query,__LINE__,__FILE__);

	$endsector = $debug_query->fields['sector_id'];

	while (!$debug_query->EOF){
		$indexsectors = $debug_query->fields;
		$index[$indexsectors['x'].','.$indexsectors['y'].','.$indexsectors['z']]=&$indexsectors['sector_id'];
		$debug_query->MoveNext();
	}

	$collisions = 0;
	# calculate the scale to use such that 
	# the max distance between 2 points will be
	# approx $universe_size.
	$scale = $universe_size / (4.0*pi());

	# compute the angle between arms
	$angle = deg2rad(360/$spiral_galaxy_arms);
	$addedsectors = mt_rand(1, $universe_expansion_rate);
	TextFlush ("Creating ". $addedsectors ." New Sectors <br><br>");
	for ($i=0; $i<$addedsectors; $i++) 
	{
		$sector = '';

		$initbore = $ore_limit;
		$initborganics = $organics_limit ;
		$initbgoods = $goods_limit ;
		$initbenergy = $energy_limit ;

		$port_type= mt_rand(0,100);

		if ($port_type > 40){
			$port="none";
			$sector['port_organics'] = '';
			$sector['port_ore'] = '';
			$sector['port_goods'] = '';
			$sector['port_energy'] = '';
		}elseif ($port_type >15){
			$random_port = mt_rand(1,4);
			if ($random_port==1){
				$port="goods";
			}elseif($random_port==2){
				$port="ore";
			}elseif($random_port==3){
				$port="organics";
			}else{
				$port="energy";
			}

			$sector['port_organics'] = $initborganics;
			$sector['port_ore'] = $initbore;
			$sector['port_goods'] = $initsgoods;
			$sector['port_energy'] = $initbenergy;
		}else{
			$random_port = mt_rand(1,3);
			if ($random_port==1){
				$port="upgrades";
			}elseif ($random_port==2){
				$port="devices";
			}else{
				$port="spacedock";
			}
			$sector['port_organics'] = '';
			$sector['port_ore'] = '';
			$sector['port_goods'] = '';
			$sector['port_energy'] = '';
		}

		$random_star = mt_rand(0,$max_star_size);
		$sector['star_size'] = $random_star;
		$sector['sector_name'] = '';
		$sector['port_type'] = $port;
		$sector['beacon'] = '';
		$sector['zone_id'] = '1'; // Uncharted

		$collision = FALSE;
		while (TRUE) 
		{
			// Lot of shortcuts here. Basically we generate a spherical coordinate and convert it to cartesian.
			// Why? Cause random spherical coordinates tend to be denser towards the center.
			// Should really be like a spiral arm galaxy but this'll do for now.
			if($enable_spiral_galaxy != 1){
				$radius = mt_rand(100,$universe_size*100)/100;

				$temp_a = deg2rad(mt_rand(0,36000)/100-180);
				$temp_b = deg2rad(mt_rand(0,18000)/100-90);
				$temp_c = $radius*sin($temp_b);

				$sector['x'] = round(cos($temp_a)*$temp_c);
				$sector['y'] = round(sin($temp_a)*$temp_c);
				$sector['z'] = round($radius*cos($temp_b));

				// Collision check
				if (isset($index[$sector['x'].','.$sector['y'].','.$sector['z']])) 
				{
					$collisions++;
				} 
				else 
				{
					break;
				}
			}else{
				//The Spiral Galaxy Code was proviced by "Kelly Shane Harrelson" <shane@mo-ware.com> 
				# need to randomly assign this point to an arm.
				$arm = mt_rand(0,$spiral_galaxy_arms-1);
				$arm_offset = $arm * $angle;

				# generate the logical position on the spiral (0 being closer to the center).
				# the double rand puts more towards the center.
				$u = deg2rad(mt_rand(0, mt_rand(0, 360)));

				# generate the base x,y,z location in cartesian form
				$bx = $u*cos($u+$arm_offset);
				$by = $u*sin($u+$arm_offset);
				$bz = 0.0;

				# generate a max delta from the base x, y, z.
				# this will be larger closer to the center,
				# tapering off the further out you are. 
				# this will create the bulge like effect in 
				# the center.  this is just a rough function
				# and there are probably better ones out there.
				$d = ($u<0.3) ? 1.5 : (log($u,10)*-1.0)+1.0;  # log base 10

				# generate random angles and distance for offsets from base x,y,z
				$dt = deg2rad(mt_rand(0, 360)); # angle theta 0-360
				$dp = deg2rad(mt_rand(0, 360)); # angle phi   0-360
				$dd = $d*rand(1,100)/100;    # distance    0-$d

				# based on random angles and distance, generate cartesian offsets for base x,y,z
				$dx = $dd*sin($dt)*cos($dp);
				$dy = $dd*sin($dt)*sin($dp);
				$dz = $dd*cos($dt);

				# we want the arms to flatten out away from center
				$dz *= ($d/1.5);  

				# calcuate final cartesian coordinate 
				$x = $bx + $dx;
				$y = $by + $dy;
				$z = $bz + $dz;

				# now scale them to fit $universe_size
				$x *= $scale;
				$y *= $scale;
				$z *= $scale;

				$sector['x'] = $x;
				$sector['y'] = $y;
				$sector['z'] = $z;
				$sector['spiral_arm'] = $arm;

				// Collision check
				if (isset($index[$sector['x'].','.$sector['y'].','.$sector['z']])) 
				{
					$collisions++;
				} 
				else 
				{
					break;
				}
			}
		}
		$index[$sector['x'].','.$sector['y'].','.$sector['z']]=&$sector;

		sector_todb($sector,"Insert",'-1',1);
		flush();
	}

	if ($collisions) 
	{
		TextFlush ("<font color=\"yellow\">$collisions sector collisions repaired</font> ");
	} 
	else 
	{
		TextFlush ("No sector collisions detected ");
	}

	$debug_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
	db_op_result($debug_query,__LINE__,__FILE__);

	$newendsector = $debug_query->fields['sector_id'];

	$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] where sg_sector != 1 and sector_id > 3");
	$totrecs=$findem->RecordCount(); 
	$getit=$findem->GetArray();

	TextFlush ( "<br>Generating warp links <br>\n");
	for ($i=$endsector+1; $i<=$newendsector; $i++) 
	{
		$numlinks = mt_rand(0,5);
		for ($j=0; $j<$numlinks; $j++)
		{
			$randplay=mt_rand(0,($totrecs-1));
			$destination = $getit[$randplay]['sector_id'];

			$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES ($i,$destination);");
			db_op_result($debug_query,__LINE__,__FILE__);
			$link_odds = mt_rand(0,100);
			if ($link_odds < 50)
			{
				$result4 = $db->Execute(" SELECT * FROM $dbtables[links] where link_start=$destination");
				db_op_result($result4,__LINE__,__FILE__);

				$totalcount = $result4->RecordCount();
				if($totalcount <= 5){
					$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES ($destination,$i);");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}
		}
	}

	TextFlush ( "<br><font color=\"lime\">- operation completed successfully.</font><br><br>");
}

TextFlush ( "<br>\n");
$multiplier = 0; //no use to run this again

?>
