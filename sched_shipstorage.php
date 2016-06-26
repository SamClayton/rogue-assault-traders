<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: sched_apocalypse.php

if (preg_match("/sched_shipstorage.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}
// Update ship storage fee
TextFlush ( "<b>Update storage fee</b><br>");

if($db_mysql_valid == "yes")
{
	$res2 = $db->Execute("UPDATE $dbtables[ships], $dbtables[players] SET $dbtables[ships].store_fee=$dbtables[ships].store_fee+($dbtables[ships].class*500) WHERE $dbtables[players].player_id=$dbtables[ships].player_id and $dbtables[players].currentship != $dbtables[ships].ship_id");
}
else
{
	$res2 = $db->Execute("SELECT * FROM $dbtables[ships],$dbtables[players] WHERE $dbtables[players].player_id=$dbtables[ships].player_id and $dbtables[players].currentship !=$dbtables[ships].ship_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	while(!$res2->EOF)
	{
		$row2 = $res2->fields;
		$debug_query = $db->Execute("update $dbtables[ships] set store_fee=store_fee+(class*500) where ship_id=$row2[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		TextFlush ( "Ship update: ".$row2['name']." storage fee updated.<br>");
		$res2->MoveNext();
	}
}

TextFlush ( "<br>Completed<br><br>");

?>
