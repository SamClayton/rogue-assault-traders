<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_ports.php

if (preg_match("/sched_ports.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

TextFlush ( "<B>PORTS</B><BR>");
TextFlush ( "\nAdding commodities to all ports...");
$debug_query = $db->Execute("UPDATE $dbtables[universe] SET 
							port_ore=GREATEST(LEAST(port_ore+($ore_rate*$multiplier), $ore_limit), 0),
							port_organics=GREATEST(LEAST(port_organics+($organics_rate*$multiplier), $organics_limit), 0),
							port_goods=GREATEST(LEAST(port_goods+($goods_rate*$multiplier), $goods_limit), 0), 
							port_energy=GREATEST(LEAST(port_energy+($energy_rate*$multiplier), $energy_limit), 0)
							WHERE port_type <> 'upgrades' AND port_type <> 'devices' AND port_type <> 'none'");
db_op_result($debug_query,__LINE__,__FILE__);

$multiplier = 0;
TextFlush ( "<BR>\n");
?>
