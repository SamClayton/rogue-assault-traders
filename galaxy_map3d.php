<?php
// 3D Galaxy Map
//
// The second line MUST be the name of the command that is to be shown in the command list.
//
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: galaxy_map3d.php

include ("config/config.php");
include("languages/$langdir/lang_galaxy3d.inc");
include ("languages/$langdir/lang_galaxy_local.inc");

if (checklogin())
{
	include ("footer.php");
	die();
}

$title = $l_g3d_title;
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

if($arm == '')
	$selected = "selected";
else $selected = "";

$armdropdown = "<option value=\"\" $selected>All</option>\n";
for($i = 0; $i < $spiral_galaxy_arms; $i++){
	if($arm == $i and $arm != '')
		$selected = "selected";
	else $selected = "";
	
	$armdropdown .= "<option value=\"". $i ."\" $selected>".  $i ."</option>\n";
}

if($turns != '')
{
	$shipspeed = mypw($level_factor, $shipinfo['engines']);
	$distance = ($turns * $shipspeed);
}

$smarty->assign("l_submit", $l_submit);
$smarty->assign("l_glxy_turns", $l_glxy_turns);
$smarty->assign("l_glxy_select", $l_glxy_select);
$smarty->assign("distance", $distance);
$smarty->assign("turns", $turns);
$smarty->assign("armdropdown", $armdropdown);
$smarty->assign("spiral_galaxy_arms", $spiral_galaxy_arms);
$smarty->assign("arm", $arm);
$smarty->assign("l_g3d_wait", $l_g3d_wait);
$smarty->assign("shipsector", $shipinfo['sector_id']);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."galaxy3d.tpl");
include ("footer.php");
?>


