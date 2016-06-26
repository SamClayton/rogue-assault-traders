<?php

// File: footer.php

global $db ,$dbtables, $smarty;

$silent = 1;
$timeleft = '';

if (isset($smarty))
{
	$smarty->display("faq/faqfooter.tpl");
}
unset ($smarty);

exit; // To prevent pop-up windows ;)
?>
