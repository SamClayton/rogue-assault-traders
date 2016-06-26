<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: port_casino.php

if (preg_match("/port_casino.php/i", $_SERVER['PHP_SELF']))  
{
	echo "You can not access this file directly!"; 
	die(); 
}

include ("languages/$langdir/lang_casino.inc");

$title = $l_casino_title;

if($sectorinfo['port_type'] != "casino")
{
	close_database();
	echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
	die();
}

$smarty->assign("l_casino_welcome", $l_casino_welcome);
$smarty->assign("color_line", $color_line);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_casino_option", $l_casino_option);
$smarty->assign("l_casino_detail", $l_casino_detail);

// Get casino links

$casinodata = file("config/casino.ini.php");
$j=0;
for($i = 0; $i < count($casinodata); $i += 6){
	$fields = "";
	$fielddata = "";
	$j++;
	for($element = 0; $element < 5; $element++){
		$variable = explode("=", $casinodata[$i + $element], 2);
		$variable[0] = trim($variable[0]);
		$variable[1] = trim($variable[1]);
		$$variable[0] = $variable[1];

		$fields .= $variable[0];
		$fielddata .= trim($variable[1],"'");
		if($element != 5){
			$fields .= ", ";
			$fielddata .= "|";
		}
		list($name_array[$j],$image_array[$j],$description_array[$j],$casino_link_array[$j],$online_status_array[$j])=explode("|",$fielddata);

		//echo "name: ".$name_array[$j]."<br>";
		//echo $variable[0] . " = " . $variable[1] . "<br>";
	}
}

$smarty->assign("item_count",$j);		
$smarty->assign("name_array",$name_array);	
$smarty->assign("image_array",$image_array);		
$smarty->assign("description_array",$description_array);		
$smarty->assign("casino_link_array",$casino_link_array);		
$smarty->assign("online_status_array",$online_status_array);			
$smarty->assign("title",$title);
$smarty->assign("gotomain", $l_global_mmenu);

$smarty->display($templatename."casino.tpl");

include ("footer.php");

?>		