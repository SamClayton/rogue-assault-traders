<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: options_save.php

include ("config/config.php");
include ("languages/$langdir/lang_option2.inc");

if (checklogin())
{
	include ("footer.php");
	die();
}

$title = "$l_opt2_title";

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

//-------------------------------------------------------------------------------------------------

if (($newpass1 == $newpass2) && ($playerinfo['password'] == $oldpass) && ($newpass1 != ''))
{
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET password='$newpass1' WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$userpass = $username."+".$newpass1;
	$_SESSION['userpass'] = $userpass;
}

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
		$newlang = $langdir;
		$l_opt2_chlang = str_replace("[lang]", $avail_lang[$i]['name'], $l_opt2_chlang);
		break;
	}
}

if(is_file($gameroot."templates/" . $newtemplate . "/about_template.inc")){
	$templatedata = file("templates/" . $newtemplate . "/about_template.inc");
	$variable = explode("=", $templatedata[0], 2);
	$templatefullname = str_replace("\"", "", trim($variable[1]));
}
else
{
	$templatefullname = $newtemplate;
}

$l_opt2_chtemplate = str_replace("[template]", $templatefullname, $l_opt2_chtemplate);

$newtemplate .= "/";

if($map_width < 10)
	$map_width = 10;

if($map_width > 100)
	$map_width = 100;

$playerinfo['map_width'] = $map_width;

$debug_query2 = $db->Execute("UPDATE $dbtables[players] SET map_width=$map_width, template='$newtemplate' WHERE email='$username'");
db_op_result($debug_query2,__LINE__,__FILE__);

if($allow_shipnamechange == 1){
	$newshipname = clean_words($newshipname);

	$result = $db->Execute ("SELECT name FROM $dbtables[ships]");

	if ($result>0)
	{
		while (!$result->EOF)
		{
			$row = $result->fields;
			if (strtolower($row['name']) == strtolower($newshipname)) 
			{ 
				$newshipname = $shipinfo['name'];
			}
			elseif (metaphone($row['name']) == metaphone($newshipname)) 
			{ 
				$newshipname = $shipinfo['name'];
			}
			$result->MoveNext();
		}
	}

	if (strtolower($newshipname) == "unknown" || strtolower($newshipname) == "unowned" || strtolower($newshipname) == "unchartered" || strtolower($newshipname) == "uncharted") 
	{ 
		$newshipname = $shipinfo['name'];
	}
	$debug_query2 = $db->Execute("UPDATE $dbtables[ships] SET name='" . addslashes($newshipname) . "' WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query2,__LINE__,__FILE__);
}

//-------------------------------------------------------------------------------------------------

$smarty->assign("title", $title);
$smarty->assign("l_opt2_mapwidth", $l_opt2_mapwidth);
$smarty->assign("map_width", $playerinfo['map_width']);
$smarty->assign("l_opt2_shipnamechanged", $l_opt2_shipnamechanged);
$smarty->assign("allow_shipnamechange", $allow_shipnamechange);
$smarty->assign("password", $playerinfo['password']);
$smarty->assign("newpass1", $newpass1);
$smarty->assign("newpass2", $newpass2);
$smarty->assign("l_opt2_passunchanged", $l_opt2_passunchanged);
$smarty->assign("l_opt2_newpassnomatch", $l_opt2_newpassnomatch);
$smarty->assign("oldpass", $oldpass);
$smarty->assign("l_opt2_srcpassfalse", $l_opt2_srcpassfalse);
$smarty->assign("debug_query", $debug_query);
$smarty->assign("l_opt2_passchanged", $l_opt2_passchanged);
$smarty->assign("l_opt2_passchangeerr", $l_opt2_passchangeerr);
$smarty->assign("l_opt2_chlang", $l_opt2_chlang);
$smarty->assign("l_opt2_chtemplate", $l_opt2_chtemplate);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."option2.tpl");

include ("footer.php");

?>
