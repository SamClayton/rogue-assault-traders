<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: options.php

include ("config/config.php");
include ("languages/$langdir/lang_options.inc");

$title = $l_opt_title;

if ((!isset($i)) || ($i == ''))
{
	$i = 0;
}

if (checklogin())
{
	include ("footer.php");
	die();
}

function RecurseDir($basedir, $AllDirectories=array()) { 
	#Create array for current directories contents 
	$ThisDir=array(); 
	if ($handle = @opendir($basedir)) {
    	while (false !== ($file = readdir($handle))) { 
			if ($file != "." && $file != "..") { 
				if (is_dir($basedir."/".$file)) {
					array_push($ThisDir,$file);     
				}
			}
		}
		closedir($handle); 
	}
	#Loop through each directory,  run RecurseDir function on each one 
	foreach ($ThisDir as $key=>$var) { 
		array_push($AllDirectories, $var); 
		$AllDirectories=RecurseDir($var, $AllDirectories); 
	} 
	return $AllDirectories; 
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

$lang_drop_down = '';

// Get the languages from the DB.
$lang_query = $db->Execute("SELECT name, value from $dbtables[languages]");
db_op_result($lang_query,__LINE__,__FILE__);
$i = 0;
while (!$lang_query->EOF && $lang_query)  
{
	$row = $lang_query->fields;
	$avail_lang[$i]['name'] = $row['name'];
	$avail_lang[$i]['value'] = $row['value'];
	$i++;
	$lang_query->MoveNext();
}

$maxval = count($avail_lang);
for ($i=0; $i<$maxval; $i++)
{
	if ($avail_lang[$i]['value'] == $langdir)
	{
		$selected = " selected";
	}
	else
	{
		$selected = "";
	}
	$lang_drop_down = $lang_drop_down . "<option value=\"" . $avail_lang[$i]['value'] . "\"$selected>" . $avail_lang[$i]['name'] . "</option>\n";
}

$template_drop_down = '';
$dirlist=RecurseDir($gameroot."templates"); 
chdir($gameroot);
$templatecount = 0;
$authorarray = "";
$emailarray = "";
$websitearray = "";
$descriptionarray = "";
$picturearray = "";
$picturesmallarray = "";

foreach ($dirlist as $key=>$val) { 
	$temp = str_replace($gameroot."templates/", "", $val);
	$template = explode("/", $temp);

	if(is_file($gameroot."templates/" . $template[0] . "/about_template.inc")){
		$templatedir[$templatecount] = $template[0];

		$templatedata = file("templates/" . $template[0] . "/about_template.inc");
		$variable = explode("=", $templatedata[0], 2);
		$templatefullname[$templatecount] = str_replace("\"", "", trim($variable[1]));
		$variable = explode("=", $templatedata[1], 2);
		$templateauthor[$templatecount] = str_replace("\"", "", trim($variable[1]));
		$variable = explode("=", $templatedata[2], 2);
		$templateemail[$templatecount] = str_replace("\"", "", trim($variable[1]));
		$variable = explode("=", $templatedata[3], 2);
		$templatewebsite[$templatecount] = str_replace("\"", "", trim($variable[1]));
		$variable = explode("=", $templatedata[4], 2);
		$templatedescription[$templatecount] = str_replace("\"", "", trim($variable[1]));

		$templatepicturesmall[$templatecount] = "templates/" . $template[0] . "/about_picture_small.gif";
		$templatepicture[$templatecount] = "templates/" . $template[0] . "/about_picture.gif";

		$authorarray .= "author[$templatecount] = '" . $templateauthor[$templatecount] . "'\n";
		$emailarray .= "email[$templatecount] = '" . $templateemail[$templatecount] . "'\n";
		$websitearray .= "website[$templatecount] = '" . $templatewebsite[$templatecount] . "'\n";
		$descriptionarray .= "descriptions[$templatecount] = '" . $templatedescription[$templatecount] . "'\n";
		$picturearray .= "pictures[$templatecount] = '" . $templatepicture[$templatecount] . "'\n";
		$picturesmallarray .= "picturessmall[$templatecount] = '" . $templatepicturesmall[$templatecount] . "'\n";

		if($playerinfo['template'] == $template[0]."/")
		{
			$selected = "selected";
			$template_author = $templateauthor[$templatecount];
			$template_email = $templateemail[$templatecount];
			$template_website = $templatewebsite[$templatecount];
			$template_description = $templatedescription[$templatecount];
			$template_picturesmall = $templatepicturesmall[$templatecount];
			$template_picture = $templatepicture[$templatecount];
		}
		else
		{
			$selected = "";
		}

		$template_drop_down = $template_drop_down . "<option value=\"" . $templatedir[$templatecount] . "\" $selected>" . $templatefullname[$templatecount] . "</option>\n";
		$templatecount++;
	} 
}

$smarty->assign("authorarray", $authorarray);
$smarty->assign("emailarray", $emailarray);
$smarty->assign("websitearray", $websitearray);
$smarty->assign("descriptionarray", $descriptionarray);
$smarty->assign("picturearray", $picturearray);
$smarty->assign("picturesmallarray", $picturesmallarray);


$smarty->assign("templatedir", $templatedir);
$smarty->assign("templatepicture", $templatepicture);
$smarty->assign("templatepicturesmall", $templatepicturesmall);
$smarty->assign("templatefullname", $templatefullname);
$smarty->assign("templateauthor", $templateauthor);
$smarty->assign("templateemail", $templateemail);
$smarty->assign("templatewebsite", $templatewebsite);
$smarty->assign("templatedescription", $templatedescription);
$smarty->assign("templatecount", $templatecount);

$smarty->assign("template_author", $template_author);
$smarty->assign("template_email", $template_email);
$smarty->assign("template_website", $template_website);
$smarty->assign("template_description", $template_description);
$smarty->assign("template_picture", $template_picture);
$smarty->assign("template_picturesmall", $template_picturesmall);

$showteamicon = 1;

if($playerinfo['team'] == 0){
	$showteamicon = 0;
}

$result_team = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
$teamstuff = $result_team->fields;

if($teamstuff['id'] != $playerinfo['player_id']){
	$showteamicon = 0;
}

if ((isset($playerinfo['profile_name'])) && ($playerinfo['profile_name'] != ''))
{
	$registeredprofile = 1;
}
else
{
	$registeredprofile = 0;
}

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$smarty->assign("title", $title);
$smarty->assign("enable_profilesupport", $enable_profilesupport);
$smarty->assign("l_here", $l_here);
$smarty->assign("l_opt_profiletitle", $l_opt_profiletitle);
$smarty->assign("l_opt_profile", $l_opt_profile);
$smarty->assign("l_opt_profilereg", $l_opt_profilereg);
$smarty->assign("l_opt_profilerereg", $l_opt_profilerereg);
$smarty->assign("registeredprofile", $registeredprofile);
$smarty->assign("l_opt_mapwidth", $l_opt_mapwidth);
$smarty->assign("map_width", $playerinfo['map_width']);
$smarty->assign("oldshipname", $shipinfo['name']);
$smarty->assign("l_opt_shipname", $l_opt_shipname);
$smarty->assign("allow_shipnamechange", $allow_shipnamechange);
$smarty->assign("teamicon", $teamstuff['icon']);
$smarty->assign("l_opt_teamicon", $l_opt_teamicon);
$smarty->assign("showteamicon", $showteamicon);
$smarty->assign("l_avatar", $l_avatar);
$smarty->assign("l_set", $l_set);
$smarty->assign("avatar", $playerinfo['avatar']);
$smarty->assign("l_opt_chpass", $l_opt_chpass);
$smarty->assign("l_opt_curpass", $l_opt_curpass);
$smarty->assign("l_opt_newpass", $l_opt_newpass);
$smarty->assign("l_opt_newpagain", $l_opt_newpagain);
$smarty->assign("l_opt_usenew", $l_opt_usenew);
$smarty->assign("l_opt_lang", $l_opt_lang);
$smarty->assign("l_opt_select", $l_opt_select);
$smarty->assign("l_opt_template", $l_opt_template);
$smarty->assign("lang_drop_down", $lang_drop_down);
$smarty->assign("template_drop_down", $template_drop_down);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_opt_enabled", $l_opt_enabled);
$smarty->assign("l_opt_save", $l_opt_save);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."options.tpl");

include ("footer.php");
?>
