<?
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: shoutbox2.php

include ("config/config.php");

include ("languages/$langdir/lang_shoutbox.inc");

$title = $l_teamm_title;

if (checklogin())
{
	include ("footer.php");
	die();
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

$result = $db->Execute("SELECT * FROM $dbtables[players] WHERE email='$username'");
$playerinfo = $result->fields;

$sbt = strip_tags($sbt);
$sbt = substr(clean_words($sbt),0,70);

$html_entities_match = array('#&(?!(\#[0-9]+;))#', '#<#', '#>#');
$html_entities_replace = array('&amp;', '&lt;', '&gt;');
$sbt = preg_replace($html_entities_match, $html_entities_replace, trim($sbt));

$itag1 = "<IMG BORDER=0 SRC='templates/".$playerinfo['template']."images/smilies/";
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

$sbt = rawurlencode(addslashes($sbt));

// Check Team shout or public
$sb_alli = (($playerinfo[team]<=0)?-1:$playerinfo[team]);
if (isset($SBPB)) $sb_alli = 0;

// Check double post!
$result = $db->Execute("SELECT * FROM $dbtables[shoutbox] ORDER BY sb_date DESC");
$lastshout = $result->fields;
if ($lastshout[sb_text] == $sbt)
	$sbt = "";

// Add Shout only if not empty !
if ($sbt != "")
	$res = $db->Execute("INSERT INTO $dbtables[shoutbox] (player_id,player_name,sb_date,sb_text,sb_alli) VALUES ($playerinfo[player_id],'$playerinfo[character_name]'," . time() . ",'$sbt',$sb_alli) ");

$smarty->assign("l_shout_saved", $l_shout_saved);
$smarty->assign("l_shout_return", $l_shout_return);
$smarty->assign("l_shout_title", $l_shout_title);
$smarty->assign("l_shout_close", $l_shout_close);
$smarty->display($templatename."shoutbox2.tpl");

include ("footer.php");
?>