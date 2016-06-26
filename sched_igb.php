<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_igb.php

$igb_results = '';

if (preg_match("/sched_igb.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

if (!isset($swordfish) || $swordfish != $adminpass)
{
	die("Script has not been called properly");
}

$exponinter = mypw($ibank_interest + 1, $multiplier);
$expoloan = mypw($ibank_loaninterest + 1, $multiplier);

$debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime, balance=balance * $exponinter, " .
							"loan=loan * $expoloan");
db_op_result($debug_query,__LINE__,__FILE__);

while (!$debug_query->EOF && $debug_query !='')
{
	$igb_results = $debug_query;
}

if($adminexecuted == 1){
	echo"<b>IGB</b><br><br>";

	if ($igb_results != '')
	{
		echo "Errors encountered: $igb_results";
	}else{
		echo "All IGB accounts updated $multiplier times successfully!";
	}
	echo "<br><br>";
}
$multiplier = 0;

?>
