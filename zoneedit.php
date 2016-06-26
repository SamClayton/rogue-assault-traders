<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: zoneedit.php

include ("config/config.php");
include ("languages/$langdir/lang_zoneinfo.inc");
include ("languages/$langdir/lang_zoneedit.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");

$title = $l_ze_title;

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

$command = '';

if (isset($_GET['command']))
{
	$command = $_GET['command'];
}

if ((!isset($nbeacon)) || ($nbeacon == '')){
	$nbeacon = '';
}

if ((!isset($lbeacon)) || ($lbeacon == '')){
	$lbeacon = '';
}

if ((!isset($zone)) || ($zone == ''))
{
	$zone = '';
}

if ((!isset($nattack)) || ($nattack == ''))
{
	$nattack = '';
}

if ((!isset($nwarpedit)) || ($nwarpedit == ''))
{
	$nwarpedit = '';
}

if ((!isset($lwarpedit)) || ($lwarpedit == ''))
{
	$lwarpedit = '';
}

if ((!isset($ndefense)) || ($ndefense == ''))
{
	$ndefense = '';
}

if ((!isset($ldefense)) || ($ldefense == ''))
{
	$ldefense = '';
}

if ((!isset($nplanet)) || ($nplanet == ''))
{
	$nplanet = '';
}

if ((!isset($lplanet)) || ($lplanet == ''))
{
	$lplanet = '';
}

if ((!isset($ntrade)) || ($ntrade == ''))
{
	$ntrade = '';
}

if ((!isset($ltrade)) || ($ltrade == ''))
{
	$ltrade = '';
}

if ((!isset($yplanet)) || ($yplanet == ''))
{
	$yplanet = '';
}

if ($zoneinfo['team_zone'] == 'N')
{
	$result = $db->Execute("SELECT player_id FROM $dbtables[players] WHERE email='$username'");
	$ownerinfo = $result->fields;
}
else
{
	$result = $db->Execute("SELECT creator, id FROM $dbtables[teams] WHERE creator=$zoneinfo[owner]");
	$ownerinfo = $result->fields;
}

if (($zoneinfo['team_zone'] == 'N' && $zoneinfo['owner'] != $ownerinfo['player_id']) || ($zoneinfo['team_zone'] == 'Y' && $zoneinfo['owner'] != $ownerinfo['id'] && $row[owner] == $ownerinfo['creator']))
{
	zoneedit_die($l_ze_notowner);
}

if ($command == 'change')
{
	zoneedit_change();
}

if ($zoneinfo['allow_beacon'] == 'Y'){
	$ybeacon = "checked";
}
elseif ($zoneinfo['allow_beacon'] == 'N'){
	$nbeacon = "checked";
}else{
	$lbeacon = "checked";
}

if ($zoneinfo['allow_attack'] == 'Y')
{
	$yattack = "checked";
}
else
{
	$nattack = "checked";
}

if ($zoneinfo['allow_warpedit'] == 'Y')
{
	$ywarpedit = "checked";
}
elseif ($zoneinfo['allow_warpedit'] == 'N')
{
	$nwarpedit = "checked";
}
else
{
	$lwarpedit = "checked";
}

if ($zoneinfo['allow_planet'] == 'Y')
{
	$yplanet = "checked";
}
elseif ($zoneinfo['allow_planet'] == 'N')
{
	$nplanet = "checked";
}
else
{
	$lplanet = "checked";
}

if ($zoneinfo['allow_trade'] == 'Y')
{
	$ytrade = "checked";
}
elseif ($zoneinfo['allow_trade'] == 'N')
{
	$ntrade = "checked";
}
else
{
	$ltrade = "checked";
}

if ($zoneinfo['allow_defenses'] == 'Y')
{
	$ydefense = "checked";
}
elseif ($zoneinfo['allow_defenses'] == 'N')
{
	$ndefense = "checked";
}
else
{
	$ldefense = "checked";
}

$smarty->assign("zone", $zoneinfo['zone_id']);
$smarty->assign("l_ze_name", $l_ze_name);
$smarty->assign("name", $zoneinfo['zone_name']);
$smarty->assign("l_beacons", $l_beacons);
$smarty->assign("ybeacon", $ybeacon);
$smarty->assign("l_yes", $l_yes);
$smarty->assign("nbeacon", $nbeacon);
$smarty->assign("l_no", $l_no);
$smarty->assign("lbeacon", $lbeacon);
$smarty->assign("l_zi_limit", $l_zi_limit);
$smarty->assign("l_ze_attacks", $l_ze_attacks);
$smarty->assign("yattack", $yattack);
$smarty->assign("nattack", $nattack);
$smarty->assign("l_ze_allow", $l_ze_allow);
$smarty->assign("l_warpedit", $l_warpedit);
$smarty->assign("ywarpedit", $ywarpedit);
$smarty->assign("nwarpedit", $nwarpedit);
$smarty->assign("lwarpedit", $lwarpedit);
$smarty->assign("l_sector_def", $l_sector_def);
$smarty->assign("ydefense", $ydefense);
$smarty->assign("ndefense", $ndefense);
$smarty->assign("ldefense", $ldefense);
$smarty->assign("l_ze_genesis", $l_ze_genesis);
$smarty->assign("yplanet", $yplanet);
$smarty->assign("nplanet", $nplanet);
$smarty->assign("lplanet", $lplanet);
$smarty->assign("l_title_port", $l_title_port);
$smarty->assign("ytrade", $ytrade);
$smarty->assign("ntrade", $ntrade);
$smarty->assign("ltrade", $ltrade);
$smarty->assign("l_submit", $l_submit);
$smarty->assign("l_clickme", $l_clickme);
$smarty->assign("zone2", $zone);
$smarty->assign("l_ze_return", $l_ze_return);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."zoneedit.tpl");

include ("footer.php");

//-----------------------------------------------------------------

function zoneedit_change()
{
	global $zone, $templatename, $smarty;
	global $name, $l_global_mmenu, $l_ze_namematch;
	global $attacks;
	global $warpedits;
	global $planets;
	global $beacons;
	global $trades;
	global $defenses;
	global $l_clickme, $l_ze_saved, $l_ze_return;
	global $db,$dbtables;

	$name = trim($name);
	
	$result = $db->Execute ("SELECT zone_name FROM $dbtables[zones] where zone_id!=$zone");

	if ($result>0)
	{
		while (!$result->EOF)
		{
			$zone_name = $result->fields['zone_name'];

			if (strtolower($zone_name) == strtolower($name) || metaphone($zone_name) == metaphone($name) || $name == '') 
			{ 
				$smarty->assign("l_ze_namematch", $l_ze_namematch);
				$smarty->assign("zone", $zone);
				$smarty->assign("l_clickme", $l_clickme);
				$smarty->assign("l_ze_return", $l_ze_return);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."zoneeditsamename.tpl");
				include ("footer.php");
				die();
			}
			$result->MoveNext();
		}
	}

	if (metaphone("unowned") == metaphone($name) || metaphone("unknown") == metaphone($name) || metaphone("unchartered") == metaphone($name) || metaphone("uncharted") == metaphone($name) || metaphone("federation") == metaphone($name) || metaphone("independent") == metaphone($name)) 
	{
		$name = "Cheater";
	}

	$name = clean_words($name);

	if (!get_magic_quotes_gpc())
	{
		$name = addslashes($name);
	}

	$debug_query = $db->Execute("UPDATE $dbtables[zones] SET zone_name='$name', allow_beacon='$beacons', allow_attack='$attacks', allow_warpedit='$warpedits', allow_planet='$planets', allow_trade='$trades', allow_defenses='$defenses' WHERE zone_id=$zone");
	db_op_result($debug_query,__LINE__,__FILE__);

	$smarty->assign("l_ze_saved", $l_ze_saved);
	$smarty->assign("zone", $zone);
	$smarty->assign("l_clickme", $l_clickme);
	$smarty->assign("l_ze_return", $l_ze_return);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."zoneeditchange.tpl");

	include ("footer.php");
	die();
}

function zoneedit_die($error_msg)
{

	global $templatename, $smarty, $l_global_mmenu;
	$smarty->assign("error_msg", $error_msg);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."zoneeditdie.tpl");

	include ("footer.php");
	die();
}

?>
