<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_spies.php

if (preg_match("/sched_spies.php/i", $_SERVER['PHP_SELF']))
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

TextFlush ( "<b>SPIES</b><br>\n");

if ($spy_success_factor)
{
	$sabotage	= (int)(900 / $spy_success_factor) + 1;
	$steal_intr  = (int)(800 / $spy_success_factor) + 1;
	$birth	   = (int)(1000 / $spy_success_factor) + 1;
	$steal_money = (int)(2000 / $spy_success_factor) + 1;
	$blowup_torp = (int)(3000 / $spy_success_factor) + 1;
	$blowup_fits = (int)(5000 / $spy_success_factor) + 1;
	$capture	 = (int)(20000 / $spy_success_factor) + 1;
	$kill		= (int)(4000 / $spy_kill_factor) + 1;

	$sabotage_trigger	= (int) ($sabotage / 2);
	$steal_intr_trigger  = (int) ($steal_intr / 2);
	$birth_trigger	   = (int) ($birth / 2);
	$steal_money_trigger = (int) ($steal_money / 2);
	$blowup_torp_trigger = (int) ($blowup_torp / 2);
	$blowup_fits_trigger = (int) ($blowup_fits / 2);
	$capture_trigger	 = (int) ($capture / 2);
	$kill_trigger		= (int) (50 / $spy_kill_factor);  //Don't write '$kill / 2' -- may cause a bug

	$lower = -$kill + 100 / $spy_kill_factor;
	$i = 0;

	// Getting all possibly needed information about the spy, the planet, the spy owner and his ship
	$spies = $db->Execute("SELECT $dbtables[planets].*, $dbtables[spies].*, $dbtables[players].character_name, $dbtables[ships].cloak AS spy_cloak FROM $dbtables[planets] INNER JOIN $dbtables[spies] ON $dbtables[planets].planet_id = $dbtables[spies].planet_id INNER JOIN $dbtables[players] ON $dbtables[spies].owner_id = $dbtables[players].player_id INNER JOIN $dbtables[ships] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE $dbtables[spies].planet_id <> '0' AND $dbtables[spies].active='Y' AND $dbtables[spies].ship_id = '0' ");

	while (!$spies->EOF)
	{
		$spy = $spies->fields;
		$flag = 1;

		if (!$spy['name'])
		{
			$spy['name'] = $l_unnamed;
		}

		for ($j=1; $j <= $i; $j++)
		{
			if ($spy['planet_id'] == $changed_planets[$j])
			{
				$flag = 0;
			}
		}

		if ($spy['job_id'] == 0) // Not yet 'occupied' - ready to do something bad...
		{
			if ($spy['try_sabot'] == 'Y')
			{
				$success = mt_rand(0, $sabotage);
				if ($success == $sabotage_trigger && $flag)
				{
					$r1 = $db->Execute("SELECT SUM(spy_percent) as s_total FROM $dbtables[spies] WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='1' ");
					$total = $r1->fields['s_total'];
					$total = floor(($colonist_production_rate - $total) * 30000);
					$new_percet = myrand(floor($total * 0.1), floor($total * 0.3), 1.3);	//10%...30%
					$new_percet /= 30000.0;
					if ($new_percet)
					{
						$debug_query = $db->Execute("UPDATE $dbtables[spies] SET spy_percent='$new_percet', job_id='1' WHERE spy_id=$spy[spy_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);

						$temp = NUMBER($new_percet*100.0, 5);
						playerlog($spy['owner_id'], LOG_SPY_SABOTAGE, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
					}
				}
			}

			if ($spy['try_inter'] == 'Y')
			{
				$success = mt_rand(0, $steal_intr);
				if ($success == $steal_intr_trigger && $flag)
				{
					$r1 = $db->Execute("SELECT SUM(spy_percent) as i_total FROM $dbtables[spies] WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='2' ");
					$total = $r1->fields['i_total'];
					$total = floor(("1.0003" - $total - 1) * 30000);
					$new_percet = myrand(floor($total * 0.15), floor($total * 0.35), 1.3);	//15%...35%
					$new_percet /= 30000.0;
					if ($new_percet)
					{
						$debug_query = $db->Execute("UPDATE $dbtables[spies] SET spy_percent='$new_percet', job_id='2' WHERE spy_id=$spy[spy_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);
						$temp = NUMBER($new_percet*100.0, 5);
						playerlog($spy['owner_id'], LOG_SPY_INTEREST, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
					}
				}
			}

			if ($spy['try_birth'] == 'Y')
			{
				$success = mt_rand(0, $birth);
				if ($success == $birth_trigger && $flag)
				{
					$r1 = $db->Execute("SELECT SUM(spy_percent) as b_total FROM $dbtables[spies] WHERE active='Y' AND planet_id=$spy[planet_id] AND job_id='3' ");
					$total = $r1->fields['b_total'];
					$total = floor(($colonist_reproduction_rate - $total) * 500000);
					$new_percet = myrand(floor($total * 0.1), floor($total * 0.3), 1.3);	//10%...30%
					$new_percet /= 500000.0;
					if ($new_percet)
					{
						$debug_query = $db->Execute("UPDATE $dbtables[spies] SET spy_percent='$new_percet', job_id='3' WHERE spy_id=$spy[spy_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);
						$temp = NUMBER($new_percet*100.0, 5);
						playerlog($spy['owner_id'], LOG_SPY_BIRTH, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
					}
				}
			}

			if ($spy['try_steal'] == 'Y')
			{
				$success = mt_rand(0, $steal_money);
				if ($success == $steal_money_trigger && $flag)
				{
					if ($spy['credits'] > 0)
					{
						$roll = myrand(2400, 9000, 2.5);	//8%...30%
						$sum = floor($spy['credits'] * $roll / 30000);
						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits-$sum WHERE planet_id=$spy[planet_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);

						if ($allow_ibank)
						{
							$debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime,balance=balance+$sum WHERE player_id=$spy[owner_id]");
							db_op_result($debug_query,__LINE__,__FILE__);
						}
						else
						{
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$sum WHERE player_id=$spy[owner_id]");
							db_op_result($debug_query,__LINE__,__FILE__);
						}

						$temp = NUMBER($sum);
						playerlog($spy['owner_id'], LOG_SPY_MONEY, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
						// don't change spy's job_id and don't inform the planet owner!
					}
				}
			}

			if ($spy['try_torps'] == 'Y')
			{
				$success = mt_rand(0, $blowup_torp);
				if ($success == $blowup_torp_trigger && $flag)
				{
					if ($spy['torps'] > 0)
					{
						$roll = myrand(2100, 7500, 3);	//7%...25%
						$blow = floor($spy['torps'] * $roll / 30000);
						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET torps=torps-$blow WHERE planet_id=$spy[planet_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);
						$temp = NUMBER($blow);
						playerlog($spy['owner_id'], LOG_SPY_TORPS, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
						// don't change spy's job_id and don't inform the planet owner!
					}
				}
			}

			if ($spy['try_fits'] == 'Y')
			{
				$success = mt_rand(0, $blowup_fits);
				if ($success == $blowup_fits_trigger && $flag)
				{
					if ($spy['fighters'] > 0)
					{
						$roll = myrand(2400, 9000, 4);	//8%...30%
						$blow = floor($spy['fighters'] * $roll / 30000);
						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET fighters=fighters-$blow WHERE planet_id=$spy[planet_id] ");
						db_op_result($debug_query,__LINE__,__FILE__);
						$temp = NUMBER($blow);
						playerlog($spy['owner_id'], LOG_SPY_FITS, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$temp");
						$flag = 0;
						// don't change spy's job_id and don't inform the planet owner!
					}
				}
			}

			if ($allow_spy_capture_planets && $spy['try_capture'] == 'Y')
			{
				$success = mt_rand(0, $capture);
				if ($success == $capture_trigger && $flag)
				{
					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET team = 0,cargo_hull = 0, cargo_power = 0, owner=$spy[owner_id] WHERE planet_id=$spy[planet_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
					//echo "ID - $spy[planet_id], OLD - $spy[owner], NEW - $spy[owner_id]<BR>";

					change_planet_ownership($spy['planet_id'], $spy['owner'], $spy['owner_id']);
					calc_ownership($spy['sector_id']);
					playerlog($spy['owner_id'], LOG_SPY_CPTURE, "$spy[spy_id]|$spy[name]|$spy[sector_id]");
					playerlog($spy['owner'], LOG_SPY_CPTURE_OWNER, "$spy[name]|$spy[sector_id]|$spy[character_name]");

					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE planet_id=$spy[planet_id]");
					db_op_result($debug_query,__LINE__,__FILE__);

					$flag = 0;
					$i++;
					$changed_planets[$i] = $spy['planet_id'];
					// don't change spy's job_id!
				}
			}
		} //job_id==0

		$base_factor = ($spy['base'] == 'Y') ? $basedefense : 0;
		$spy['sensors'] += $base_factor;

		$res = $db->Execute("SELECT max(sensors) AS maxsensors FROM $dbtables[ships] WHERE planet_id=$spy[planet_id] AND on_planet='Y'");
		if (!$res->EOF)
		{
			if ($spy['sensors'] < $res->fields['maxsensors'])
			{
				$spy['sensors'] = $res->fields['maxsensors'];
			}
		}

		$kill2 = ($spy['spy_cloak'] - $spy['sensors']) * $kill * 0.1;
		if ($kill2 > $kill)
		{
			$kill2 = $kill;
		}

		if ($kill2 < $lower)
		{
			$kill2 = $lower;
		}

		$kill2 = floor($kill2 + $kill) + 1;
		$success = mt_rand(0, $kill2);
		//echo "$spy[spy_cloak] -- $spy[sensors] -- $kill -- $kill2 -- $success -- $kill_trigger<BR>";
		if ($success == $kill_trigger && $flag)
		{
			$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE spy_id=$spy[spy_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			playerlog($spy['owner_id'], LOG_SPY_KILLED_SPYOWNER, "$spy[spy_id]|$spy[name]|$spy[sector_id]");
			playerlog($spy['owner'], LOG_SPY_KILLED, "$spy[name]|$spy[sector_id]|$spy[character_name]");
		}

		$spies->MoveNext();
	} //while
	TextFlush ( "Spies updated.<BR><BR>");
}
else
{
	TextFlush ( "Spies are disabled in this game.<BR><br>");
	$multiplier = 0;
}

?>
