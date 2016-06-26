<?php
if (preg_match("/port_none.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

	echo "$l_noport!\n";

echo "\n";
echo "<BR><BR>\n";
TEXT_GOTOMAIN();
echo "\n";

include ("footer.php");

?>
