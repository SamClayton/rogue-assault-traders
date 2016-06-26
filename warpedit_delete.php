<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: warpedit3.php

include ("config/config.php");
include ("languages/$langdir/lang_warpedit3.inc");

$title = $l_warp_title;

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

$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($max_query,__LINE__,__FILE__);

$sector_max = $max_query->fields['sector_id'];

if ((!isset($target_sector)) || ($target_sector == ''))
{
	$target_sector = '';
}

if ((!isset($flag)) || ($flag == ''))
{
	$flag = '';
}

$distance = floor(calc_dist($shipinfo['sector_id'],$target_sector));
$cost = $distance * $warplink_build_cost;
$energycost = $distance * $warplink_build_energy;

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_warp_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();

}

if ($shipinfo['dev_warpedit'] < 1)
{
	$smarty->assign("error_msg", $l_warp_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();

}

if ($shipinfo['energy'] < $energycost)
{
	$smarty->assign("error_msg", $l_warp_noenergy);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();
}

if ($playerinfo['credits'] < $cost)
{
	$smarty->assign("error_msg", $l_warp_nomoney);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();
}

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE " .
					"sector_id=$shipinfo[sector_id] AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$query97 = $res->fields;

$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$target_sector");
$target_type = $sector_res->fields['sg_sector'];

$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
$sector_type = $sector_res->fields['sg_sector'];

if (($query97['allow_warpedit'] == 'N')or($shipinfo['sector_id'] > $sector_max) or ($target_sector > $sector_max) or ($target_type == 1) or ($sector_type == 1))
{
	$smarty->assign("error_msg", $l_warp_forbid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();

}

$target_sector = round($target_sector);

$res = $db->Execute("SELECT allow_warpedit,$dbtables[universe].zone_id FROM $dbtables[zones],$dbtables[universe] WHERE " .
					"sector_id=$target_sector AND $dbtables[universe].zone_id=$dbtables[zones].zone_id");
$query97 = $res->fields;
if ($query97['allow_warpedit'] == 'N' && $bothway)
{
	$l_warp_forbidtwo = str_replace("[target_sector]", $target_sector, $l_warp_forbidtwo);
	$smarty->assign("error_msg", $l_warp_forbidtwo);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();

}

$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$target_sector");
$row = $result2->fields;
if (!$row)
{
	$smarty->assign("error_msg", $l_warp_nosector);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."warpedit_delete.tpl");
	include ("footer.php");
	die();
}

$result3 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start=$shipinfo[sector_id]");
if ($result3 > 0)
{
	while (!$result3->EOF)
	{
		$row = $result3->fields;
		if ($target_sector == $row['link_dest'])
		{
			$flag = 1;
		}

		$result3->MoveNext();
	}

	if ($flag != 1)
	{
		$l_warp_unlinked = str_replace("[target_sector]", $target_sector, $l_warp_unlinked);
		$smarty->assign("error_msg", $l_warp_unlinked);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."warpedit_delete.tpl");
		include ("footer.php");
		die();
	}
	else
	{
		$debug_query = $db->Execute("DELETE FROM $dbtables[links] WHERE link_start=$shipinfo[sector_id] AND " .
									"link_dest=$target_sector");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=energy-$energycost, dev_warpedit=dev_warpedit - 1 WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$cost, turns=turns-1, " .
									"turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		if (!$bothway)
		{
			$smarty->assign("error_msg", "$l_warp_removed $target_sector.");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."warpedit_delete.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			$debug_query = $db->Execute("DELETE FROM $dbtables[links] WHERE link_start=$target_sector AND " .
										"link_dest=$shipinfo[sector_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("error_msg", "$l_warp_removedtwo $target_sector.");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."warpedit_delete.tpl");
			include ("footer.php");
			die();
		}
	}
}

close_database();
?>
