<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: defence-report.php

include ("config/config.php");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_defence_report.inc");
include ("languages/$langdir/lang_device.inc");

$title = $l_sdf_title;

if (checklogin() or $tournament_setup_access == 1)
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

$query = "SELECT * FROM $dbtables[sector_defence] WHERE player_id=$playerinfo[player_id]";

if (!empty($sort))
{
	$query .= " ORDER BY";
	if ($sort == "quantity")
	{
		$query .= " quantity ASC";
	}
	elseif ($sort == "type")
	{
		$query .= " defence_type ASC";
   }
   else
   {
	   $query .= " sector_id ASC";
   }
}

$res = $db->Execute($query);

$i = 0;
if ($res)
{
	while (!$res->EOF)
	{
		$sector[$i] = $res->fields;
		$i++;
		$res->MoveNext();
	}
}

$num_sectors = $i;
if ($num_sectors < 1)
{
	$smarty->assign("error_msg", $l_sdf_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."defensereportdie.tpl");
}
else
{
	$smarty->assign("l_pr_clicktosort", $l_pr_clicktosort);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_sector", $l_sector);
	$smarty->assign("l_qty", $l_qty);
	$smarty->assign("l_sdf_type", $l_sdf_type);
	$color = $color_line1;
	for($i=0; $i<$num_sectors; $i++)
	{
		$dcolor[$i] = $color;
		$dsector[$i] = $sector[$i]['sector_id'];
		$dquantity[$i] = NUMBER($sector[$i]['quantity']);
		$defence_type[$i] = $sector[$i]['defence_type'] == 'F' ? $l_fighters : $l_mines;

		if ($color == $color_line1)
		{
			$color = $color_line2;
		}
		else
		{
			$color = $color_line1;
		}
	}

	$smarty->assign("dcolor", $dcolor);
	$smarty->assign("dsector", $dsector);
	$smarty->assign("dquantity", $dquantity);
	$smarty->assign("defence_type", $defence_type);
	$smarty->assign("num_sectors", $num_sectors);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."defensereport.tpl");
}

include ("footer.php");
?>
