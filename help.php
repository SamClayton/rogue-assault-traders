<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: help.php

include ("config/config.php");
include ("languages/$langdir/lang_help.inc");
$no_gzip=1;
checklogin();

$title = $l_help_title;

if ((!isset($help)) || ($help == ''))
{
	$help = '';
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if(is_file($gameroot."templates/".$templatename."help/$help")){
	$templatehelp = file ("templates/".$templatename."help/$help");
	$templatecount = count($templatehelp);
	for($i = 0; $i < $templatecount; $i++)
	{
		$templatehelp[$i] = str_replace("src=\"", "src=\"templates/" . $templatename, $templatehelp[$i]);
	}

	$smarty->assign("templatehelp", $templatehelp);
	$smarty->assign("templatecount", $templatecount);
}
else
if(is_file($gameroot."/help/$help")){
	$templatehelp = @file ("help/$help");
	$templatecount = @count($templatehelp);

	$smarty->assign("templatehelp", $templatehelp);
	$smarty->assign("templatecount", $templatecount);
}
else
{
	$lines[0] = $l_help_nohelp;
	$smarty->assign("templatehelp", $lines);
	$smarty->assign("templatecount", 1);
}


$smarty->assign("l_help_closewindow", $l_help_closewindow);
$smarty->display($templatename."help.tpl");

include ("footer.php");
?> 
