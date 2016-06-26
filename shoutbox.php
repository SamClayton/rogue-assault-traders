<?
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: shoutbox.php

include ("config/config.php");

include ("languages/$langdir/lang_shoutbox.inc");

$title = $l_shout_title;

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

$res1 = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE sb_alli = 0");
$totalshouts = $res1->RecordCount();

$res1 = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE sb_alli = 0 ORDER BY sb_date desc LIMIT 0,20");
$res2 = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE sb_alli = " . (($playerinfo[team]<=0)?-1:$playerinfo[team]) . " ORDER BY sb_date DESC LIMIT 0,20");

$countflag2 = 0;

if (!$res2 || $res2->RecordCount() != 0){
	$countflag2 = 1;
	$smarty->assign("countflag2", 1);
}else{
	$smarty->assign("countflag2", 0);
}

$countflag=0;
if (!$res1 || $res1->RecordCount() != 0 ){
	$countflag++;
}

if (!$res2 || $res2->RecordCount() != 0){
	$countflag++;
}

$smarty->assign("countflag", $countflag);

if ($countflag > 0){
	for ( $i = 0 ; $i < 20 ; $i++ )
	{
		if (!$res1->EOF)
		{
			$row1 = $res1->fields;
			$result = $db->Execute("SELECT avatar FROM $dbtables[players] WHERE player_id=$row1[player_id]");
			$avatar = $result->fields;
			$publicavatar[$i] = "avatars/".$avatar['avatar'];
			$playernamea[$i] = $row1['player_name'];
			$datea[$i] = date("m/d/Y G:i",$row1['sb_date']);
			$messagea[$i] = stripslashes(stripslashes(rawurldecode($row1['sb_text'])));
			$res1->MoveNext();
		} else {
			$publicavatar[$i] = "spacer.gif";
			$playernamea[$i] = "&nbsp;";
			$datea[$i] = "&nbsp;";
			$messagea[$i] = "&nbsp;";
		}
		if (!$res2->EOF)
		{
			$row2 = $res2->fields;
			$result = $db->Execute("SELECT avatar FROM $dbtables[players] WHERE player_id=$row2[player_id]");
			$avatar = $result->fields;
			$privateavatar[$i] = "avatars/".$avatar['avatar'];
			$playernameb[$i] = $row2['player_name'];
			$dateb[$i] = date("m/d/Y G:i",$row2['sb_date']);
			$messageb[$i] = stripslashes(stripslashes(rawurldecode($row2['sb_text'])));
			$res2->MoveNext();
		} else { 
			$privateavatar[$i] = "spacer.gif";
			$playernameb[$i] = "&nbsp;";
			$dateb[$i] = "&nbsp;";
			$messageb[$i] = "&nbsp;";
		}
	}
}


$smarty->assign("publicavatar", $publicavatar);
$smarty->assign("playernamea", $playernamea);
$smarty->assign("datea", $datea);
$smarty->assign("messagea", $messagea);
$smarty->assign("privateavatar", $privateavatar);
$smarty->assign("playernameb", $playernameb);
$smarty->assign("dateb", $dateb);
$smarty->assign("messageb", $messageb);
$smarty->assign("total", $i);
$smarty->assign("checked", (($playerinfo[team]==0)?"CHECKED":""));
$smarty->assign("template", $playerinfo['template']);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);
$smarty->assign("l_shout_smiles", $l_shout_smiles);
$smarty->assign("l_shout_else", $l_shout_else);
$smarty->assign("l_shout_refresh", $l_shout_refresh);
$smarty->assign("l_shout_close", $l_shout_close);
$smarty->assign("color_header", $color_header);
$smarty->assign("l_shout_public", $l_shout_public);
$smarty->assign("l_shout_team", $l_shout_team);
$smarty->assign("color_header", $color_header);
$smarty->assign("l_shout_title2", $l_shout_title2);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."shoutbox.tpl");

include ("footer.php");
?>
