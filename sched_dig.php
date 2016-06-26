<?

if (preg_match("/sched_dig.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('myrand')) {
	function myrand($lower, $upper, $distribution_const = 1) // Used for spies.
	{
		$max_random = mt_getrandmax();

		if ($distribution_const == 1)
		{
			return floor($lower + ($upper-$lower+1)*MT_RAND(0,$max_random)/($max_random+1));
		}

		elseif ($distribution_const > 1)
		{
			return floor($lower + ($upper-$lower+1)*POW(MT_RAND(0,$max_random)/($max_random+1),$distribution_const));
		}

		else
		{
			return floor($lower + ($upper-$lower+1)*POW(MT_RAND(1,$max_random)/($max_random+1),$distribution_const));  //it might be 0..$max_random, but for example, POW(0, 0.8) returns error
		}
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

TextFlush ( "<b>DIGNITARIES</b><br>\n");

if ($dig_success_factor){
	$build_interest  = (int)(800 / $dig_success_factor) + 1; // Build Interest
	$production_builder	= (int)(900 / $dig_success_factor) + 1; // Production Builder
	$birth_decrease	   = (int)(1000 / $dig_success_factor) + 1; // Decrease Birthrate
	$spy_hunt = (int)(1500 / $dig_success_factor) + 1;// Spy Hunt
	$birth_increase	   = (int)(2000 / $dig_success_factor) + 1; // Increase Birthrate
	$steal_money = (int)(3000 / $dig_success_factor) + 1;//Embezel Funds
	$build_torp = (int)(4000 / $dig_success_factor) + 1;//Build Torps
	$build_fighters = (int)(5000 / $dig_success_factor) + 1;// Build Fighters
	
	$production_builder_trigger	= (int) ($production_builder / 2);
	$build_interest_trigger  = (int) ($build_interest / 2);
	$birth_decrease_trigger	   = (int) ($birth_decrease / 2);
	$spyhunt_trigger1 = (int) ($spy_hunt / 2);
	$birth_increase2	   = (int) ($birth_increase / 2);
	$steal_money_trigger = (int) ($steal_money / 2);
	$build_torp_trigger = (int) ($build_torp / 2);
	$build_fighters_trigger = (int) ($build_fighters / 2);
	
	$i = 0;
		
	// Getting all possibly needed information about the dig, the planet, the dig owner and his ship
	$dignitaries = $db->Execute("SELECT $dbtables[planets].*, $dbtables[dignitary].*, $dbtables[players].character_name FROM $dbtables[planets] INNER JOIN $dbtables[dignitary] ON $dbtables[planets].planet_id = $dbtables[dignitary].planet_id INNER JOIN $dbtables[players] ON $dbtables[dignitary].owner_id = $dbtables[players].player_id INNER JOIN $dbtables[ships] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE $dbtables[dignitary].job_id = '0' AND $dbtables[dignitary].active='Y' AND $dbtables[dignitary].ship_id = '0' and $dbtables[planets].base='Y' ");
	TextFlush ( $db->ErrorMsg());

	while(!$dignitaries->EOF){
		$dig = $dignitaries->fields;
		$flag = 1;

		if (!$dig['name']) {
			$dig['name'] = $l_unnamed;
		}

		for ($j=1; $j <= $i; $j++){
			if ($dig['planet_id'] == $changed_planets[$j]){
				$flag = 0;
			}
		}
	  
		if ($dig['job_id'] == 0){
			$stamp = date("Y-m-d H:i:s");
			$reactive_date = date("Y-m-d H:i:s", strtotime($stamp) + mt_rand(floor($dig_embezzlerdelay * 86400 / 2), $dig_embezzlerdelay * 86400));
			$success = mt_rand(0, $build_interest);
			if ($success == $build_interest_trigger && $flag){
				TextFlush ( "Build Interest<br>");

				$new_percet = (mt_rand(1, 100) / 100) * $dig_interest_max;
//echo"Percent3: ".$new_percet."<br>";
				$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='2', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);
				$temp = NUMBER($new_percet*100.0, 5);
				playerlog($dig['owner_id'], LOG_DIG_INTEREST, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
				$flag = 0;
			}

			$success = mt_rand(0, $production_builder);
			if ($success == $production_builder_trigger && $flag)
			{
			TextFlush ( "Production Build<br>");
				$new_percet = (mt_rand(1, 100) / 100) * $dig_prod_max;

				$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='1', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);
				$temp = NUMBER($new_percet*100.0, 5);
				playerlog($dig['owner_id'], LOG_DIG_PRODUCTION, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
				$flag = 0;
			}

			$success = mt_rand(0, $birth_decrease);
			if ($success == $birth_decrease_trigger && $flag)
			{
				$doom_query = $db->Execute("SELECT * from $dbtables[planets] WHERE planet_id=$dig[planet_id]");
	   			$doomcheck = $doom_query->fields;
				if($doomcheck['colonists'] > ($colonist_limit*0.5)){
					TextFlush ( "Birth Rate Decrease<br>");
					$new_percet = (mt_rand(1, 100) / 100) * $dig_birthdec_max;

					$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='3', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
					db_op_result($debug_query,__LINE__,__FILE__);
					$temp = NUMBER($new_percet*100.0, 5);
					playerlog($dig['owner_id'], LOG_DIG_BIRTHDEC, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					$flag = 0;
				}
			}

			$success = mt_rand(0, $spy_hunt);
			if ($success == $spyhunt_trigger1 && $flag)
			{
				TextFlush ( "Spy Hunter<br>");
				$new_percet = (mt_rand(1, 100) / 100) * $dig_spyhunter_max;

				$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='5', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);
				$temp = NUMBER($new_percet*100.0, 5);
				playerlog($dig['owner_id'], LOG_DIG_SPYHUNT, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
				$flag = 0;
			}
			
  			$success = mt_rand(0, $birth_increase);
			if ($success == $birth_increase2 && $flag)
			{
				$doom_query = $db->Execute("SELECT * from $dbtables[planets] WHERE planet_id=$dig[planet_id]");
	   			$doomcheck = $doom_query->fields;
				if($doomcheck['colonists'] < ($colonist_limit*0.5)){
					TextFlush ( "Birth Rate Increase<br>");
					$new_percet = (mt_rand(1, 100) / 100) * $dig_birthinc_max;

		  			$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='4', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
			   		db_op_result($debug_query,__LINE__,__FILE__);
				   	$temp = NUMBER($new_percet*100.0, 5);
					playerlog($dig['owner_id'], LOG_DIG_BIRTHINC, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
   					$flag = 0;
				}
			}

			$success = mt_rand(0, $steal_money);
 			if ($success == $steal_money_trigger && $flag)
	   		{
				TextFlush ( "Imbezzeler<br>");
			   	if ($dig['credits'] > 0)
				{
   					$roll = myrand(2400, 9000, 2.5);	//8%...30%
	   				$sum = floor($dig['credits'] * $roll / 30000);
		   			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits-$sum WHERE planet_id=$dig[planet_id] ");
			   		db_op_result($debug_query,__LINE__,__FILE__);
					$ownerdude=$dig['owner_id'];
					$findem = $db->Execute("SELECT player_id FROM $dbtables[ships]  WHERE destroyed ='N' AND player_id <> '$ownerdude'");
					TextFlush ( $db->ErrorMsg());
					$totrecs=$findem->RecordCount(); 
					$getit=$findem->GetArray();
					if ($totrecs > 0){
						$randplay=mt_rand(0,($totrecs-1));
						$playergift = $getit[$randplay]['player_id'];
//						echo $playergift;
					}	
					$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$sum WHERE player_id=$playergift");
					db_op_result($debug_query,__LINE__,__FILE__);
					$temp = NUMBER($sum);
					playerlog($playergift, LOG_DIG_MONEY, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
//echo "Dig: ".$dig[dig_id]."<br>";

					$new_percet = (mt_rand(1, 100) / 100) * $dig_imbezzler_max;

					$digtype = mt_rand(6, 10);
					$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET percent='$new_percet', job_id='" . $digtype . "', active_date='$stamp', reactive_date='$reactive_date' WHERE dig_id=$dig[dig_id] ");
   					db_op_result($debug_query,__LINE__,__FILE__);
	   				$temp = NUMBER($new_percet*100.0, 5);
					if($digtype == 6)
						playerlog($dig['owner_id'], LOG_DIG_PRODUCTION, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					if($digtype == 7)
						playerlog($dig['owner_id'], LOG_DIG_INTEREST, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					if($digtype == 8)
						playerlog($dig['owner_id'], LOG_DIG_BIRTHDEC, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					if($digtype == 9)
						playerlog($dig['owner_id'], LOG_DIG_BIRTHINC, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					if($digtype == 10)
						playerlog($dig['owner_id'], LOG_DIG_SPYHUNT, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
			   		$flag = 0;
				}
	 		}

			$success = mt_rand(0, $build_torp);
			if ($success == $build_torp_trigger && $flag)
			{
				TextFlush ( "Build Torps<br>");
				if ($dig['torps'] > 0)
				{
					$roll = myrand(2100, 7500, 3);	//7%...25%
					$blow = floor($dig['torps'] * $roll / 30000);
					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET torps=torps+$blow WHERE planet_id=$dig[planet_id] ");
					db_op_result($debug_query,__LINE__,__FILE__);
					$temp = NUMBER($blow);
					playerlog($dig['owner_id'], LOG_DIG_TORPS, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					$flag = 0; 
				}  
			}
	
			$success = mt_rand(0, $build_fighters);
			if ($success == $build_fighters_trigger && $flag)
			{
				TextFlush ( "Build Fighters<br>");
				if ($dig['fighters'] > 0)
				{
					$roll = myrand(2400, 9000, 4);	//8%...30%
					$blow = floor($dig['fighters'] * $roll / 30000);
					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET fighters=fighters+$blow WHERE planet_id=$dig[planet_id] ");
					db_op_result($debug_query,__LINE__,__FILE__);
					$temp = NUMBER($blow);
					playerlog($dig['owner_id'], LOG_DIG_FITS, "$dig[dig_id]|$dig[name]|$dig[sector_id]|$temp");
					$flag = 0; 
				}  
			}
		} //job_id==0
		$dignitaries->MoveNext();
	} //while
	TextFlush ( "dignitaries updated.<BR><BR>");
}
else
{
	TextFlush ( "dignitaries are disabled in this game.<BR><br>");
	$multiplier = 0;
}
?>

