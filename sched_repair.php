<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_repair.php

if (preg_match("/sched_repair.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if($db_type == "mysql" and $db_mysql_type == "default"){

	if($deoperror != 1)
		TextFlush ("<b>Starting Database Repair Check</b><br><br>");

	$badtables = 0;
	foreach($dbtables as $tablename){

		$debug_query = $db->Execute("CHECK TABLE $tablename");
		db_op_result($debug_query,__LINE__,__FILE__);
		$isok = $debug_query->fields['Msg_text'];

		if($isok != "OK" and $isok != "Table is already up to date"){
			if($deoperror != 1)
				TextFlush ("<b>Corrupted table: $tablename</b><br>");

			$debug_query = $db->Execute("REPAIR TABLE $tablename QUICK");
			db_op_result($debug_query,__LINE__,__FILE__);

			if($deoperror != 1)
				TextFlush ("<b>Repaired table: $tablename</b><br><br>");

			$badtables++;
		}
		$debug_query = $db->Execute("OPTIMIZE TABLE $tablename");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	if($deoperror != 1){
		if($badtables == 0)
			TextFlush ("Nothing to repair.<br>");
		TextFlush ("<br><b>Database Repair Check Complete</b><br><br>");
	}
}

$multiplier = 0;
?>

