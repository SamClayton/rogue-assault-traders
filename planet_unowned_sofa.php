<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_unowned_sofa.php

include ("config/config.php");
include ("languages/$langdir/lang_attack.inc");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_traderoute.inc");
include ("combat_functions.php");
$no_gzip = 1;

if (isset($_GET['planet_id']))
{
	$planet_id = $_GET['planet_id'];
}

$title = $l_planet_title;

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

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
	$planetinfo=$result3->fields;

bigtitle();

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

// No planet

if (empty($planetinfo))
{
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

	die();
}

if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
	if ($shipinfo['on_planet'] == 'Y')
	{
	  $debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$shipinfo[ship_id]");
	  db_op_result($debug_query,__LINE__,__FILE__);
	}
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}

if (($planetinfo['owner'] == 0  || $planetinfo['defeated'] == 'Y') && $command != "capture")
{
	if ($planetinfo['owner'] == 0)
		$smarty->assign("error_msg", $l_planet_unowned);
	$capture_link="<a href='planet_unowned_capture.php?planet_id=$planet_id'>$l_planet_capture1</a>";
	$l_planet_capture2=str_replace("[capture]",$capture_link,$l_planet_capture2);
		$smarty->assign("error_msg2", $l_planet_capture2);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}

if ($planetinfo['owner'] != 0)
{
	if ($spy_success_factor)
	{
	  spy_detect_planet($shipinfo['ship_id'], $planetinfo['planet_id'],$planet_detect_success1);
	}
	$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
	$ownerinfo = $result3->fields;

	$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$planetinfo[owner] AND ship_id=$ownerinfo[currentship]");
	$ownershipinfo = $res->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
{
	if ($command != "")
	{
		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";
	}

	if ($allow_ibank)
	{
		echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
	}

	echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";

	TEXT_GOTOMAIN();
	include ("footer.php");
	die();
}
else
{
		$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 0);

		if($isfedbounty > 0)
		{
			echo $l_by_fedbounty . "<BR><BR>";
		}
		else
		{
			echo $l_by_nofedbounty . "<BR><BR>";
		}

		if($planetinfo['owner'] != 3){
			$l_planet_att_link="<a href='planet_unowned_attackpreview.php?planet_id=$planet_id'>" . $l_planet_att_link ."</a>";
			$l_planet_att=str_replace("[attack]",$l_planet_att_link,$l_planet_att);
		}
		$l_planet_scn_link="<a href='planet_scan.php?planet_id=$planet_id'>" . $l_planet_scn_link ."</a>";
		$l_planet_scn=str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);
		echo "$l_planet_att<BR>";
		echo "$l_planet_scn<BR>";

		echo "<a href='planet_unowned_sofabomb.php?planet_id=$planet_id'>$l_sofa</a><b>$l_planet_att_sure</b><BR>";
		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";

		if ($allow_ibank)
		{
			echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
		}

		echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();

}

close_database();
?>