<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_trade.php

if (preg_match("/sched_trade.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

TextFlush ( "<b>PLANETARY TRADING</b><br>\n");

TextFlush ( "\nAdding price increases to all ports...<br>");

//echo "<br>Current Stamp: ".strtotime(date("Y-m-d H:i:s"));
$stamp = strtotime(date("Y-m-d H:i:s")) - $notradeperiod;
//echo "<br>Stamp: $stamp";
$trade_date = date("Y-m-d H:i:s", $stamp);
TextFlush ( "<br>Date range to prevent port price upgrades<br><br> Start: $trade_date<br> End:   ".date("Y-m-d H:i:s")."<br>");

TextFlush ( "\nUpdating Non-Fixed Price Ports...<br>");
$debug_query = $db->Execute("UPDATE $dbtables[universe] SET organics_price=organics_price+((RAND()*$organics_increaserate)*$organics_price),
							ore_price=ore_price+(RAND()*($ore_increaserate*$ore_price)),
							goods_price=goods_price+(RAND()*($goods_increaserate*$goods_price)), " .
							"energy_price=energy_price+(RAND()*($energy_increaserate*$energy_price)) 
							WHERE port_type <> 'upgrades' AND " .
							"port_type <> 'devices' AND port_type <> 'none' and trade_date <= '$trade_date' and fixed_price=0");
db_op_result($debug_query,__LINE__,__FILE__);

TextFlush ( "\nUpdating Fixed Price Ports...<br>");

$debug_query = $db->Execute("UPDATE $dbtables[universe] SET 
							organics_price=LEAST(organics_price+(RAND()*($organics_increaserate*$organics_price)), fixed_organics_price),
							ore_price=LEAST(ore_price+(RAND()*($ore_increaserate*$ore_price)), fixed_ore_price),
							goods_price=LEAST(goods_price+(RAND()*($goods_increaserate*$goods_price)), fixed_goods_price), 
							energy_price=LEAST(energy_price+(RAND()*($energy_increaserate*$energy_price)), fixed_energy_price) 
							WHERE port_type <> 'upgrades' AND " .
							"port_type <> 'devices' AND port_type <> 'none' and trade_date <= '$trade_date' and fixed_price=1");
db_op_result($debug_query,__LINE__,__FILE__);

TextFlush ( "<br>\n");
$multiplier = 0; //no use to run this again

?>
