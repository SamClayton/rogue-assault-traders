<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_turns.php

$turns_results1 = '';
$turns_results2 = '';

if (preg_match("/sched_turns.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

if (!isset($swordfish) || $swordfish != $adminpass)
{
	die("Script has not been called properly");
}

$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=LEAST(turns+round($turn_rate*$multiplier), $max_turns)");
db_op_result($debug_query,__LINE__,__FILE__);

while (!$debug_query->EOF && $debug_query !='')
{
	$turns_results1 = $debug_query;
}

if($adminexecuted == 1){
	echo"<b>TURNS</b><br><br>";

	if ($turns_results1 != '')
	{
		echo "Errors encountered: $turns_results1";
	}else{
		echo "Completed successfully!";
	}
	echo "<br><br>";
}

$multiplier = 0;

?>
