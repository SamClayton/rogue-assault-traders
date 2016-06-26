<?php
include ("config/config.php");
include ("languages/$langdir/lang_beacon.inc");

$title = $l_beacon_title;

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

$result2 = $db->Execute ("SELECT * FROM $dbtables[universe] WHERE sector_id='$shipinfo[sector_id]'");
$sectorinfo = $result2->fields;

$allowed_rsw = "N";

if ($shipinfo['dev_beacon'] > 0)
{
	$res = $db->Execute("SELECT allow_beacon FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
	$zoneinfo = $res->fields;
	if ($zoneinfo['allow_beacon'] == 'N')
	{
		$smarty->assign("error_msg", $l_beacon_notpermitted);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."beaconsdie.tpl");
		include ("footer.php");
		die();
	}
	elseif ($zoneinfo['allow_beacon'] == 'L')
	{
		$result3 = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id='$sectorinfo[zone_id]'");
		$zoneowner_info = $result3->fields;

		$result5 = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id='$zoneowner_info[owner]'");
		$zoneteam = $result5->fields;

		if ($zoneowner_info[owner] != $playerinfo[player_id])
		{
			if (($zoneteam[team] != $playerinfo[team]) || ($playerinfo[team] == 0))
			{
				$smarty->assign("error_msg", $l_beacon_notpermitted);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."beaconsdie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$allowed_rsw = "Y";
			}
		}
		else
		{
			$allowed_rsw = "Y";
		}
	}
	else
	{
		$allowed_rsw = "Y";
	}

	if ($allowed_rsw == "Y")
	{
		if ((!isset($beacon_text)) || ($beacon_text == ''))
		{
			if ($sectorinfo['beacon'] != "")
			{
				$smarty->assign("beacon_info", "$l_beacon_reads: \"$sectorinfo[beacon]\"");
			}
			else
			{
				$smarty->assign("beacon_info", $l_beacon_none);
			}

			$smarty->assign("l_beacon_enter", $l_beacon_enter);
			$smarty->assign("l_submit", $l_submit);
			$smarty->assign("l_reset", $l_reset);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."beacons.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			$beacon_text = clean_words(trim(strip_tags($beacon_text)));
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET beacon='$beacon_text' WHERE sector_id=$sectorinfo[sector_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_beacon=dev_beacon-1 WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("error_msg", "$l_beacon_nowreads: \"$beacon_text\".");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."beaconsdie.tpl");
			include ("footer.php");
			die();
		}
	}
}
else
{
	$smarty->assign("error_msg", $l_beacon_donthave);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."beaconsdie.tpl");
	include ("footer.php");
	die();
}

close_database();
?>
