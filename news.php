<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: news.php

include ("config/config.php");
include_once ("includes/newsservices.php");

$title = $l_news_title;

$noreturn = 1;
$isonline = checklogin();

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($isonline != 1){
	if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
		base_template_data();
	}
	else
	{
		$smarty->assign("title", $title);
		$smarty->assign("templatename", $templatename);
	}
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}
//Check to see if the date was passed in the query string

if ((!isset($startdate)) || ($startdate == ''))
{
	//The date wasn't supplied so use today's date
	$startdate = date("Y-m-d");
}

$previousday = getpreviousday($startdate);
$nextday = getnextday($startdate);

$month = substr($startdate, 5, 2);
$day = substr($startdate, 8, 2);
$year = substr($startdate, 0, 4);
$today = mktime (0,0,0,$month,$day,$year);
$today = date($local_date_short_format, $today);

$smarty->assign("isonline", $isonline);
$smarty->assign("bnn_head_image", $bnn_head_image);
$smarty->assign("l_news_info", $l_news_info);
$smarty->assign("l_news_for", $l_news_for);
$smarty->assign("today", $today);
$smarty->assign("l_news_prev", $l_news_prev);
$smarty->assign("nextday", $nextday);
$smarty->assign("l_news_next", $l_news_next);
$smarty->assign("previousday", $previousday);

$newscount = 0;

//Select news for date range
$res = $db->Execute("SELECT * from $dbtables[news] where LEFT(date,10) = '$startdate' order by news_id desc");

//Check to see if there was any news to be shown
if ($res->EOF && $res)
{
	//No news
	//Display link to the main page
	if (empty($username))
	{
		$smarty->assign("gotomain", $l_global_mlogin);
	}
	else
	{
		$smarty->assign("gotomain", $l_global_mmenu);
	}
	
	$headline[$newscount] = $l_news_flash;
	$newstext[$newscount] = $l_news_none;
	$newscount++;
	$smarty->assign("newscount", $newscount);
	$smarty->assign("headline", $headline);
	$smarty->assign("newstext", $newstext);
	$smarty->display($templatename."news.tpl");
	include ("footer.php");
	die();
}

while (!$res->EOF && $res)
{
	$row = $res->fields;
	$newsdata = translate_news($row);
	$headline[$newscount] = $newsdata['headline'];
	$newstext[$newscount] = $newsdata['newstext'];
	$newscount++;
	$res->MoveNext();
}

if (empty($username))
{
	$smarty->assign("gotomain", $l_global_mlogin);
}
else
{
	$smarty->assign("gotomain", $l_global_mmenu);
}

$smarty->assign("newscount", $newscount);
$smarty->assign("headline", $headline);
$smarty->assign("newstext", $newstext);
$smarty->display($templatename."news.tpl");
include ("footer.php");
?>
