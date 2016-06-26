<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: header.php

//header("Cache-Control: no-store, no-cache, must-revalidate");

if (preg_match("/header.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

// Defines to avoid warnings
if ((!isset($no_body)) || ($no_body == ''))
{
	$no_body = '';
}

// Smarty Templates!
require_once (SMARTY_CLASS);
$smarty = new Smarty;
$smarty->template_dir = "./templates/";
$smarty->compile_dir = "./templates_c/";
$smarty->use_sub_dirs = $use_subdirectories;
if($enable_gzip)
	$smarty->load_filter('output','gzip');

// put this in your application
function extract_variables($tpl_source, &$smarty)
{
	return str_replace("{php}","{php}extract(\$this->get_template_vars());",$tpl_source);
}

// register the prefilter
$smarty->register_prefilter("extract_variables");

$banner_top = "";
$lines = @file ("config/banner_top.inc");
for($i=0; $i<count($lines); $i++){
	$banner_top .= $lines[$i];
}

$lines = file ("templates/" . $templatename . "base_template_list.inc");
for($i = 0; $i < count($lines); $i++){
	$lines[$i] = trim($lines[$i]);
	if($lines[$i] != "done"){
		$base_template[$lines[$i]] = 1;
	}
	else
	{
		break;
	}
}

$smarty->assign("banner_top", $banner_top);
$smarty->assign("templatename", $templatename);

$smarty->assign("gameroot", $gameroot);
$smarty->assign("gameroot", $gameroot);
$smarty->assign("spiral_arm", $sectorinfo['spiral_arm']);
$smarty->assign("style_sheet_file", "templates/".$templatename."style.css");
$smarty->assign("header_bg_image", $background_image);
$smarty->assign("header_bg_color", $header_bg_color);
$smarty->assign("header_text_color", $header_text_color);
$smarty->assign("header_link_color", $header_link_color);
$smarty->assign("header_alink_color", $header_alink_color);
$smarty->assign("header_vlink_color", $header_vlink_color);
$smarty->assign("Title", $title);
$smarty->assign("no_body", $no_body);
$smarty->assign("local_charset", $local_charset);
$send_now = 0;
$smarty->display($templatename."header.tpl");

?>
