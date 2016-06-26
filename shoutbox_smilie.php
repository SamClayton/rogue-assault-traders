<?
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: shoutbox_smilie.php

include ("config/config.php");

include ("languages/$langdir/lang_shoutbox.inc");

$title = $l_teamm_title;

if (checklogin())
{
	include ("footer.php");
	die();
}

function x_tag($sbt)
{
	global $templatename;
	
	$itag1 = "<IMG BORDER=0 SRC='templates/".$templatename."images/smilies/";
	$itag2 = "'>";
	$sbt = str_replace(":)",$itag1 . "smile.gif" . $itag2,$sbt);
	$sbt = str_replace(":(",$itag1 . "cry.gif" . $itag2,$sbt);
	$sbt = str_replace(":o",$itag1 . "redface.gif" . $itag2,$sbt);
	$sbt = str_replace(":D",$itag1 . "biggrin.gif" . $itag2,$sbt);
	$sbt = str_replace(";)",$itag1 . "wink.gif" . $itag2,$sbt);
	$sbt = str_replace(":P",$itag1 . "razz.gif" . $itag2,$sbt);
	$sbt = str_replace(":cool:",$itag1 . "cool.gif" . $itag2,$sbt);
	$sbt = str_replace(":roll:",$itag1 . "rolleyes.gif" . $itag2,$sbt);
	$sbt = str_replace(":mad:",$itag1 . "mad.gif" . $itag2,$sbt);
	$sbt = str_replace(":eek:",$itag1 . "eek.gif" . $itag2,$sbt);
	$sbt = str_replace(":confused:",$itag1 . "confused.gif" . $itag2,$sbt);
	return $sbt;
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

$a = array(':)',':(',':o',':D',';)',':P',':cool:',':roll:',':mad:',':eek:',':confused:');
$r = count($a);

for ($i=0;$i<$r;$i++)
{
	$image[$i] = x_tag($a[$i]);
}

$smarty->assign("count", $r);
$smarty->assign("l_shout_return", $l_shout_return);
$smarty->assign("l_shout_title", $l_shout_title);
$smarty->assign("l_shout_close", $l_shout_close);
$smarty->assign("smile_text", $a);
$smarty->assign("image", $image);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."shoutbox_smile.tpl");

include ("footer.php");

?>