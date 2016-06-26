<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: spy_funcs.php

if (preg_match("/spy_funcs.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}
if (!function_exists("buy_them")){
function buy_them($player_id, $how_many = 1)
{
	global $db;
	global $dbtables;
	global $shipinfo;

	for ($i=1; $i<=$how_many; $i++)
	{
		$debug_query = $db->Execute("INSERT INTO $dbtables[spies] (spy_id, active, owner_id, planet_id, ship_id, job_id, spy_percent, move_type) values ('','N',$player_id,'0','$shipinfo[ship_id]','0','0.0','toship')");
		db_op_result($debug_query,__LINE__,__FILE__);
	}  
}
}
function transfer_to_planet($player_id, $planet_id, $spy_cloak, $how_many = 1)
{
	global $db;
	global $dbtables;
	global $max_spies_per_planet;
	global $shipinfo;
	$res = $db->Execute("SELECT COUNT(spy_id) AS n FROM $dbtables[spies] WHERE owner_id = $player_id AND ship_id = '0' AND planet_id = $planet_id");
	$on_planet = $res->fields['n'];
	$can_transfer = min(($max_spies_per_planet - $on_planet), $how_many);
	if ($can_transfer < 0)
	{
		$can_transfer = 0;
	}

	$res = $db->SelectLimit("SELECT spy_id FROM $dbtables[spies] WHERE owner_id = $player_id AND ship_id = $shipinfo[ship_id]",$can_transfer);
	$how_many2 = $res->RecordCount();
  
	if (!$how_many2)
	{
		return 0;
	}
	else  
	{
		while (!$res->EOF)
		{
			$spy = $res->fields['spy_id'];
			$debug_query = $db->Execute("UPDATE $dbtables[spies] SET planet_id = '$planet_id', ship_id = '0', active = 'N', job_id = '0', spy_percent = '0.0', spy_cloak=$spy_cloak WHERE spy_id = $spy");
			db_op_result($debug_query,__LINE__,__FILE__);

			$res->MoveNext();
		}
	return $how_many2;
	}   
}

function transfer_to_ship($player_id, $planet_id, $spy_cloak, $how_many = 1)
{
	global $db;
	global $dbtables;
	global $shipinfo;

	$res = $db->SelectLimit("SELECT spy_id FROM $dbtables[spies] WHERE owner_id = $player_id AND planet_id = $planet_id",$how_many);// AND active = 'N'
	$how_many2 = $res->RecordCount();
  
	if (!$how_many2)
	{
		return 0;
	}
	else  
	{
		while (!$res->EOF)
		{
			$spy = $res->fields['spy_id'];
			$debug_query = $db->Execute("UPDATE $dbtables[spies] SET planet_id = 0, ship_id = $shipinfo[ship_id], active = 'N', job_id = '0', spy_percent = '0.0', spy_cloak=$spy_cloak WHERE spy_id = $spy");
			db_op_result($debug_query,__LINE__,__FILE__);

			$res->MoveNext();
		}
	return $how_many2;
	}   
}

function change_planet_ownership($planet_id, $old_owner, $new_owner = 0)
{
	global $db;
	global $dbtables;

	if ($new_owner)
	{
		$result2 = $db->Execute("SELECT cloak FROM $dbtables[planets] WHERE planet_id=$planet_id");
		db_op_result($result2,__LINE__,__FILE__);
		$planetinfo = $result2->fields;

		$debug_query = $db->Execute("UPDATE $dbtables[spies] SET active='N', job_id='0', spy_percent='0.0', spy_cloak=$planetinfo[cloak] WHERE planet_id = $planet_id AND owner_id = $new_owner");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[spies] SET active='Y' WHERE planet_id = $planet_id AND owner_id <> $new_owner");
		db_op_result($debug_query,__LINE__,__FILE__);

		gen_score($new_owner);
	}  
	else
	{
		$debug_query = $db->Execute("UPDATE $dbtables[spies] SET active='N', job_id='0', spy_percent='0.0' WHERE planet_id = $planet_id");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	if ($old_owner) 
	{
		gen_score($old_owner);
	}
}

function spy_detect_planet($shipowner_ship_id, $planet_id, $succ)
{
	global $db;
	global $dbtables;
	global $l_unnamed;

	mt_srand((double)microtime()*1000000);
	$res0 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE ship_id='$shipowner_ship_id' AND active='Y'"); //// AND owner_id <> ship_id ///MITTE kasutada!
	while (!$res0->EOF)
	{
		$spyowners = $res0->fields;

		$i = mt_rand(1,100);
		if ($i <= $succ)
		{
			$res = $db->Execute("SELECT * FROM $dbtables[detect] WHERE unique_value = '$planet_id' AND owner_id=$spyowners[owner_id] AND det_type = '0'");
			if (!$res->RecordCount())
			{
				$res = $db->Execute("SELECT $dbtables[planets].planet_id, $dbtables[planets].sector_id, $dbtables[planets].name, $dbtables[players].character_name FROM $dbtables[planets] LEFT JOIN $dbtables[players] ON $dbtables[planets].owner=$dbtables[players].player_id WHERE $dbtables[planets].planet_id = '$planet_id' AND $dbtables[planets].owner <> $spyowners[owner_id]");
				if ($res->RecordCount())
				{
					$planet = $res->fields;
					if (!$planet['name']) 
					{ 
						$planet['name'] = $l_unnamed; 
					}

					$stamp = date("Y-m-d H:i:s");
					$planet['name'] = addslashes($planet['name']);;
					$debug_query = $db->Execute("INSERT INTO $dbtables[detect] values('', '$spyowners[owner_id]', '0', '$stamp','$planet[sector_id]|$planet[character_name]|$planet[name]', '$planet[planet_id]' ) ");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}
		}
	$res0->MoveNext();
	}
}

function spy_planet_destroyed($planet_id)
{
	global $db;
	global $dbtables;
  
	$res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[planets].name, $dbtables[planets].sector_id FROM $dbtables[spies] INNER JOIN $dbtables[planets] ON $dbtables[spies].planet_id = $dbtables[planets].planet_id WHERE $dbtables[spies].planet_id = '$planet_id' ");
	while (!$res->EOF)
	{
		$owners = $res->fields;
		playerlog($owners[owner_id], LOG_SPY_CATACLYSM, "$owners[spy_id]|$owners[name]|$owners[sector_id]");
		$res->MoveNext();
	}
  
	$db->Execute("DELETE FROM $dbtables[spies] WHERE planet_id = '$planet_id' ");
}

function spy_ship_destroyed($ship_id, $attacker_player_id = 0)
{
	global $db;
	global $dbtables;
	global $shipinfo;
  
	if ($attacker_player_id)
	{	
		$debug_query = $db->Execute("UPDATE $dbtables[spies] SET active ='N', job_id = '0', spy_percent = '0.0', ship_id = $shipinfo[ship_id], planet_id='0' WHERE ship_id = $ship_id AND owner_id = $attacker_player_id"); 
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	$res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[players].character_name, $dbtables[ships].name AS ship_name FROM $dbtables[ships] INNER JOIN $dbtables[players] ON $dbtables[ships].player_id = $dbtables[players].player_id INNER JOIN $dbtables[spies] ON $dbtables[spies].ship_id = $dbtables[ships].ship_id  WHERE $dbtables[spies].ship_id = $ship_id "); 
	while (!$res->EOF)
	{
		$owners = $res->fields;
		playerlog($owners['owner_id'], LOG_SHIPSPY_CATACLYSM, "$owners[spy_id]|$owners[character_name]|$owners[ship_name]");
		$res->MoveNext();
	}
  
	$debug_query = $db->Execute("DELETE FROM $dbtables[spies] WHERE ship_id = $ship_id "); 
	db_op_result($debug_query,__LINE__,__FILE__);
}

function spy_sneak_to_ship($planet_id, $ship_id)
{
	global $db;
	global $dbtables;
	global $sneak_toship_success;

	mt_srand((double)microtime()*1000000);  
	$i=0;
	$res = $db->Execute("SELECT $dbtables[spies].*, $dbtables[planets].name, $dbtables[planets].sector_id, $dbtables[players].character_name, $dbtables[ships].name AS ship_name FROM $dbtables[players] INNER JOIN $dbtables[ships] ON $dbtables[players].player_id = $dbtables[ships].player_id INNER JOIN $dbtables[planets] ON $dbtables[players].player_id = $dbtables[planets].owner INNER JOIN $dbtables[spies] ON $dbtables[planets].planet_id = $dbtables[spies].planet_id WHERE $dbtables[spies].planet_id = $planet_id AND $dbtables[spies].active = 'Y' AND $dbtables[spies].job_id = '0' AND $dbtables[spies].move_type <> 'none' "); 
	while (!$res->EOF)
	{
		$spy = $res->fields;
		$flag=1;
		for($j=1; $j<=$i; $j++)
		{
			if ($spy['owner_id'] == $changers[$j])
			{
				$flag = 0;
			}
		}
	
		if ($flag)
		{
			$k = mt_rand(1,100);
			if ($k <= $sneak_toship_success)
			{
				$res2 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE ship_id = '$ship_id' AND active = 'Y' AND owner_id = $spy[owner_id] "); 
				if ($res2->EOF) //No spies on ship
				{
					$debug_query = $db->Execute("UPDATE $dbtables[spies] SET planet_id = '0', ship_id = '$ship_id', job_id = '0', spy_percent = '0' WHERE spy_id = $spy[spy_id] "); 
					db_op_result($debug_query,__LINE__,__FILE__);

					playerlog($spy['owner_id'], LOG_SPY_TOSHIP, "$spy[spy_id]|$spy[name]|$spy[sector_id]|$spy[character_name]|$spy[ship_name]");
					$i++;
					$changers[$i] = $spy['owner_id'];
				}
			}
		}
	$res->MoveNext();
	}
}


function spy_sneak_to_planet($planet_id, $ship_id)
{
	global $db;
	global $dbtables;
	global $max_spies_per_planet;
	global $sneak_toplanet_success;

	mt_srand((double)microtime()*1000000);
	$res = $db->Execute("SELECT * FROM $dbtables[spies] WHERE ship_id = '$ship_id' AND active = 'Y' AND move_type = 'toplanet' "); 
	while (!$res->EOF)
	{
		$spy = $res->fields;

		$i = mt_rand(1,100);
		if ($i <= $sneak_toplanet_success)
		{
			$res2 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '$planet_id' AND owner_id = '$spy[owner_id]' "); 
			if ($res2->RecordCount() < $max_spies_per_planet)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[spies] SET planet_id = '$planet_id', ship_id = '0', job_id = '0', spy_percent = '0', move_type = 'none' WHERE spy_id = '$spy[spy_id]' "); 
				db_op_result($debug_query,__LINE__,__FILE__);
  
				$debug_query = $db->Execute("SELECT $dbtables[spies].*, $dbtables[planets].name, $dbtables[planets].sector_id, $dbtables[players].character_name, $dbtables[ships].name as ship_name FROM $dbtables[spies] INNER JOIN $dbtables[planets] ON $dbtables[spies].planet_id = $dbtables[planets].planet_id INNER JOIN $dbtables[players] ON $dbtables[planets].owner = $dbtables[players].player_id  INNER JOIN $dbtables[ships] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE $dbtables[spies].spy_id = $spy[spy_id] ");
				db_op_result($debug_query,__LINE__,__FILE__);
		
				$info = $debug_query->fields;
				playerlog($info['owner_id'], LOG_SPY_TOPLANET, "$info[spy_id]|$info[name]|$info[sector_id]|$info[character_name]|$info[ship_name]");
			}
		}
	$res->MoveNext();
	}
}


function calc_planet_cleanup_cost($colo = 0, $type = 1)
{
	global $db, $dbtables, $planet_id, $planetinfo;
	global $colonist_limit, $spy_cleanup_planet_credits1, $spy_cleanup_planet_credits2, $spy_cleanup_planet_credits3, $max_spies_per_planet;

	$spy_cleanup_planet_credits[1] = $spy_cleanup_planet_credits1;
	$spy_cleanup_planet_credits[2] = $spy_cleanup_planet_credits2;
	$spy_cleanup_planet_credits[3] = $spy_cleanup_planet_credits3;
	$col_lim = $colonist_limit / 1000000;
	$cred_lim = $spy_cleanup_planet_credits[$type] / 1000000;
	$colonists = $colo / 1000000;
  
	//// Constansts to create the S-curve function
	$c1 = 0.75 * $cred_lim;
	$c2 = 0.5 * $col_lim;
	$c3 = $c1 / mypw($c2, 2);
	$c4 = mypw($cred_lim - $c1, 2) / ($col_lim - $c2);
	$c5 = 0.1 * $col_lim;
	$c6 = 1/30 * $cred_lim;

	if ($colonists <= $c5)
	{
		$cl_cost = $c6;
	}
	elseif ($colonists > $c5 && $colonists <= $c2)
	{
		$cl_cost = $c3 * mypw($colonists, 2);
	}
	else
	{
		$cl_cost = SQRT($c4 * ($colonists - $c2)) + $c1;
	}

	$cl_cost = ($cl_cost * 1000000);

	// Here we reduce the costs of scans by 9.9% per spy the owner has on the planet.
	$res66 = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id=$planet_id AND owner_id=$planetinfo[owner]");
	$spies_on_planet = $res66->RecordCount();
  
	$cl_cost = ($cl_cost - ($cl_cost * $spies_on_planet / $max_spies_per_planet * 99/100) );  
	
	// You must check for upper boundary. Otherwise the typecast can cause it to flip to negative amounts.
	if ($cl_cost < 0)
	{
		$cl_cost = 2000000000;
	}

	$cl_cost = floor( $cl_cost);  
	return $cl_cost;
}

function calc_ship_cleanup_cost($level_avg = 0, $type = 1)
{
	global $level_factor, $upgrade_cost;
  
	if ($type==1)
	{
		$c=1;
	}
	elseif ($type==2)
	{
		$c=2;
	}
	else
	{
		$c=4;
	}

	// You must check for upper boundary. Otherwise the typecast can cause it to flip to negative amounts.
	$cl_cost = (mypw($level_factor, ($level_avg * 1.1)) * 70 * $upgrade_cost * $c);

	if ($cl_cost < 0)
	{
		$cl_cost = 2000000000;
	}
  
	$cl_cost = floor( $cl_cost);  
	return $cl_cost;
}

function spy_buy_new_ship($old_ship_id, $new_ship_id)
{
	global $db;
	global $dbtables;
  
	$debug_query = $db->Execute("UPDATE $dbtables[spies] SET ship_id = $new_ship_id WHERE ship_id = $old_ship_id");
	db_op_result($debug_query,__LINE__,__FILE__);
}

?>
