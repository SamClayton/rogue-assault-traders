<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_autotrade.php

if (preg_match("/sched_autotrade.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('NUM_HOLDS')) {
	function NUM_HOLDS($level_hull)
	{
		global $level_factor;
		return round(mypw($level_factor, $level_hull) * 10);
	}
}

if (!function_exists('NUM_ENERGY')) {
	function NUM_ENERGY($level_power)
	{
		global $level_factor;
		return round(mypw($level_factor, $level_power) * 50);
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

// ********************************
// ***** Sector Defense Check *****
// ********************************

function checksectordefence($playerid, $sectorid){

	global $db, $dbtables;

	$hostile = 0;
	$result98 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$playerid");
	$playerinfo = $result98->fields;

	$result98 = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE sector_id = $sectorid AND player_id <> $playerid");
	if (!$result98->EOF)
	{
		 $fighters_owner = $result98->fields;
		 $nsresult = $db->Execute("SELECT * from $dbtables[players] where player_id=$fighters_owner[player_id]");
		 $nsfighters = $nsresult->fields;
		 if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
			$hostile = 1;
	}

	return $hostile;
}

function TRADE($price, $delta, $max, $limit, $factor, $port_type, $origin, $sectorprice)
{
	global $trade_color, $trade_deficit, $trade_result, $trade_benefit, $sectorinfo, $color_green, $color_red, $price_array;
	global $energy_reducerate, $organics_reducerate, $goods_reducerate, $ore_reducerate, $neworeprice, $newgoodsprice, $neworganicsprice, $newenergyprice;

	if ($sectorinfo['port_type'] ==  $port_type )
	{
		$price_array[$port_type] = 0;
	}else{
		$price_array[$port_type] = $sectorprice + $price + $delta * $limit / $limit * $factor;
		$origin				  = -$origin;
		if($price_array[$port_type] <= 0)
			$price_array[$port_type] = 0.01;

		if($port_type == "ore" and $origin < 1){
			$neworeprice = $sectorprice + ($ore_reducerate * $origin / 10000);
		}

		if($port_type == "goods" and $origin < 1){
			$newgoodsprice = $sectorprice + ($goods_reducerate * $origin / 10000);
		}

		if($port_type == "organics" and $origin < 1){
			$neworganicsprice = $sectorprice + ($organics_reducerate * $origin / 10000);
		}

		if($port_type == "energy" and $origin < 1){
			$newenergyprice = $sectorprice + ($energy_reducerate * $origin / 10000);
		}
	}
	return $origin;
}

TextFlush ( "<b>AUTO PLANETARY TRADING</b><br>\n");

TextFlush ( "\nTrading with ports...<br>");

$res = $db->Execute("SELECT * FROM $dbtables[autotrades]");
$tradecount = $res->recordcount();

TextFlush ( "\nRunning ".$tradecount." trade routes.<br>");

$trade_date = date("Y-m-d H:i:s");

while(!$res->EOF){

	$tradeinfo = $res->fields;

	$res1 = $db->Execute("SELECT * FROM $dbtables[planets] where planet_id=$tradeinfo[planet_id]");
	$planetinfo = $res1->fields;

	if($planetinfo['planet_id'] != '' and $planetinfo['planet_id'] != 0){
		TextFlush ( "Starting trade for planet ".$planetinfo['name']." - ".$planetinfo['planet_id']."<BR>");

		$price_array = array();

		$total_cost = 0;
		$trade_goods = 0;
		$trade_ore = 0;
		$trade_organics = 0;
		$trade_energy = 0;

		$goods_price2 = 0;
		$ore_price2 = 0;
		$organics_price2 = 0;
		$energy_price2 = 0;

		if($tradeinfo['port_id_goods'] != 0){
			if(checksectordefence($tradeinfo['owner'], $tradeinfo['port_id_goods']) != 1){
				$res2 = $db->Execute("SELECT * FROM $dbtables[universe] where sector_id=$tradeinfo[port_id_goods] and port_type!='goods'");

				if($res2->recordcount() != 0){
					$sectorinfo = $res2->fields;
					$trade_goods = NUM_HOLDS($planetinfo['cargo_hull']);

					if (($planetinfo['goods'] - 10000) - $trade_goods < 0)
					{
						$trade_goods = $planetinfo['goods'] - 10000;
					}

					if ($planetinfo['goods'] < 0 or $trade_goods < 0)
					{
						$trade_goods = 0;
					}

					$newgoodsprice = $sectorinfo['goods_price'];
					$trade_goods = TRADE($goods_price, $goods_delta, $sectorinfo['port_goods'], $goods_limit, $inventory_factor, "goods", $trade_goods, $sectorinfo['goods_price']);
					$goods_price2 = $price_array['goods'];
					$total_cost += $trade_goods * $goods_price2;
					$trade_goods = round(abs($trade_goods));
//echo "UPDATE $dbtables[universe] SET goods_price = $newgoodsprice, trade_date='$trade_date', port_goods=port_goods-$trade_goods where sector_id=$sectorinfo[sector_id]<br>";
					$debug_query = $db->Execute("UPDATE $dbtables[universe] SET goods_price = $newgoodsprice, trade_date='$trade_date', port_goods=port_goods-$trade_goods where sector_id=$sectorinfo[sector_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}else{
				TextFlush ( "Trade Aborted due to enemy sector defence in sector $tradeinfo[port_id_goods].");
				playerlog($tradeinfo['owner'], LOG_AUTOTRADE_ABORTED, "$planetinfo[name]|$planetinfo[sector_id]|$tradeinfo[port_id_goods]");
			}
		}

		if($tradeinfo['port_id_ore'] != 0){
			if(checksectordefence($tradeinfo['owner'], $tradeinfo['port_id_ore']) != 1){
				$res3 = $db->Execute("SELECT * FROM $dbtables[universe] where sector_id=$tradeinfo[port_id_ore] and port_type!='ore'");

				if($res3->recordcount() != 0){
					$sectorinfo = $res3->fields;
					$trade_ore = NUM_HOLDS($planetinfo['cargo_hull']);

					if (($planetinfo['ore'] - 10000) - $trade_ore < 0)
					{
						$trade_ore = $planetinfo['ore'] - 10000;
					}

					if ($planetinfo['ore'] < 0 or $trade_ore < 0)
					{
						$trade_ore = 0;
					}

					$neworeprice = $sectorinfo['ore_price'];
					$trade_ore = TRADE($ore_price, $ore_delta, $sectorinfo['port_ore'], $ore_limit, $inventory_factor, "ore", $trade_ore, $sectorinfo['ore_price']);
					$ore_price2 = $price_array['ore'];
					$total_cost += $trade_ore * $ore_price2;
					$trade_ore = round(abs($trade_ore));
//echo"UPDATE $dbtables[universe] SET ore_price = $neworeprice, trade_date='$trade_date', port_ore=port_ore-$trade_ore where sector_id=$sectorinfo[sector_id]<br>";
					$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, trade_date='$trade_date', port_ore=port_ore-$trade_ore where sector_id=$sectorinfo[sector_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}else{
				TextFlush ( "Trade Aborted due to enemy sector defence in sector $tradeinfo[port_id_ore].");
				playerlog($tradeinfo['owner'], LOG_AUTOTRADE_ABORTED, "$planetinfo[name]|$planetinfo[sector_id]|$tradeinfo[port_id_ore]");
			}
		}

		if($tradeinfo['port_id_organics'] != 0){
			if(checksectordefence($tradeinfo['owner'], $tradeinfo['port_id_organics']) != 1){
				$res4 = $db->Execute("SELECT * FROM $dbtables[universe] where sector_id=$tradeinfo[port_id_organics] and port_type!='organics'");

				if($res4->recordcount() != 0){
					$sectorinfo = $res4->fields;
					$trade_organics = NUM_HOLDS($planetinfo['cargo_hull']);

					if (($planetinfo['organics'] - 10000) - $trade_organics < 0)
					{
						$trade_organics = $planetinfo['organics'] - 10000;
					}

					if ($planetinfo['organics'] < 0 or $trade_organics < 0)
					{
						$trade_organics = 0;
					}

					$neworganicsprice = $sectorinfo['organics_price'];
					$trade_organics = TRADE($organics_price, $organics_delta, $sectorinfo['port_organics'], $organics_limit, $inventory_factor, "organics", $trade_organics, $sectorinfo['organics_price']);
					$organics_price2 = $price_array['organics'];
					$total_cost += $trade_organics * $organics_price2;
					$trade_organics = round(abs($trade_organics));
//echo"UPDATE $dbtables[universe] SET organics_price = $neworganicsprice, trade_date='$trade_date', port_organics=port_organics-$trade_organics where sector_id=$sectorinfo[sector_id]<br>";
					$debug_query = $db->Execute("UPDATE $dbtables[universe] SET organics_price = $neworganicsprice, trade_date='$trade_date', port_organics=port_organics-$trade_organics where sector_id=$sectorinfo[sector_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}else{
				TextFlush ( "Trade Aborted due to enemy sector defence in sector $tradeinfo[port_id_organics].");
				playerlog($tradeinfo['owner'], LOG_AUTOTRADE_ABORTED, "$planetinfo[name]|$planetinfo[sector_id]|$tradeinfo[port_id_organics]");
			}
		}

		if($tradeinfo['port_id_energy'] != 0){
			if(checksectordefence($tradeinfo['owner'], $tradeinfo['port_id_energy']) != 1){
				$res5 = $db->Execute("SELECT * FROM $dbtables[universe] where sector_id=$tradeinfo[port_id_energy] and port_type!='energy'");

				if($res5->recordcount() != 0){
					$sectorinfo = $res5->fields;
					$trade_energy = NUM_ENERGY($planetinfo['cargo_power']);

					if (($planetinfo['energy'] - 10000) - $trade_energy < 0)
					{
						$trade_energy = $planetinfo['energy'] - 10000;
					}

					if ($planetinfo['energy'] < 0 or $trade_energy < 0)
					{
						$trade_energy = 0;
					}

					$newenergyprice = $sectorinfo['energy_price'];
					$trade_energy = TRADE($energy_price, $energy_delta, $sectorinfo['port_energy'], $energy_limit, $inventory_factor, "energy", $trade_energy, $sectorinfo['energy_price']);
					$energy_price2 = $price_array['energy'];
					$total_cost += $trade_energy * $energy_price2;
					$trade_energy = round(abs($trade_energy));
//echo "UPDATE $dbtables[universe] SET energy_price = $newenergyprice, trade_date='$trade_date', port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]<br>";
					$debug_query = $db->Execute("UPDATE $dbtables[universe] SET energy_price = $newenergyprice, trade_date='$trade_date', port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}else{
				TextFlush ( "Trade Aborted due to enemy sector defence in sector $tradeinfo[port_id_energy].");
				playerlog($tradeinfo['owner'], LOG_AUTOTRADE_ABORTED, "$planetinfo[name]|$planetinfo[sector_id]|$tradeinfo[port_id_energy]");
			}
		}

		$total_cost = floor(abs($total_cost));
//echo"UPDATE $dbtables[planets] SET goods=goods-$trade_goods, ore=ore-$trade_ore, organics=organics-$trade_organics, energy=energy-$trade_energy, credits=credits+$total_cost WHERE planet_id=$planetinfo[planet_id]<br>";
		$debug_query = $db->Execute("UPDATE $dbtables[planets] SET goods=goods-$trade_goods, ore=ore-$trade_ore, organics=organics-$trade_organics, energy=energy-$trade_energy, credits=credits+$total_cost WHERE planet_id=$planetinfo[planet_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$goods_price2 = floor($goods_price2);
		$ore_price2 = floor($ore_price2);
		$organics_price2 = floor($organics_price2);
		$energy_price2 = floor($energy_price2);

//echo"UPDATE $dbtables[autotrades] SET goods_price=$goods_price2, ore_price=$ore_price2, organics_price=$organics_price2, energy_price=$energy_price2, current_trade=$total_cost WHERE traderoute_id=$tradeinfo[traderoute_id]<br>";
		$debug_query = $db->Execute("UPDATE $dbtables[autotrades] SET goods_price=$goods_price2, ore_price=$ore_price2, organics_price=$organics_price2, energy_price=$energy_price2, current_trade=$total_cost WHERE traderoute_id=$tradeinfo[traderoute_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		if($planetinfo['name'] == '')
			$planetinfo['name'] = "Unnamed";

		TextFlush ( "$tradeinfo[owner], LOG_AUTOTRADE, $planetinfo[name]|$planetinfo[sector_id]|$total_cost|$trade_goods|$trade_ore|$trade_organics|$trade_energy|$goods_price2|$ore_price2|$organics_price2|$energy_price2<br>");

		playerlog($tradeinfo['owner'], LOG_AUTOTRADE, "$planetinfo[name]|$planetinfo[sector_id]|$total_cost|$trade_goods|$trade_ore|$trade_organics|$trade_energy|$goods_price2|$ore_price2|$organics_price2|$energy_price2");

		TextFlush ( "Trade Complete for planet ".$planetinfo['name']." - ".$planetinfo['planet_id']." - Total Amount of Trade: ".floor($total_cost)."<BR><br>");
	}else{
		$debug_query = $db->Execute("DELETE FROM $dbtables[autotrades] WHERE traderoute_id=$tradeinfo[traderoute_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
	$res->MoveNext();
}

// end of trading

TextFlush ( "<br>\n");
$multiplier = 0; //no use to run this again

?>
