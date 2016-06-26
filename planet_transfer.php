<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_transfer.php

include ("config/config.php");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");

if ((!isset($planet_id)) || ($planet_id == ''))
{
	$planet_id = '';
}

$title = $l_planet2_title;

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

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

//-------------------------------------------------------------------------------------------------

$result2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result2)
{
	$planetinfo = $result2->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo['owner'] > 0))
{
	if ($planetinfo['sector_id'] <> $shipinfo['sector_id'])
	{
		$smarty->assign("error_msg", $l_planet2_sector);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_transferdie.tpl");
		include ("footer.php");
		die();
	}

	if ($playerinfo['turns'] < 1)
	{
		$smarty->assign("error_msg", $l_planet2_noturn);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_transferdie.tpl");
		include ("footer.php");
		die();
	}

	if ($spy_success_factor)
	{
		spy_detect_planet($shipinfo['ship_id'], $planetinfo['planet_id'], $planet_detect_success1);
	}

	$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
	$free_power = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];
	$fighter_max = NUM_FIGHTERS($shipinfo['computer']) - $shipinfo['fighters'];
	$torpedo_max = NUM_TORPEDOES($shipinfo['torp_launchers']) - $shipinfo['torps'];

	// first setup the tp flags
	if ($tpore != -1)
	{
		$tpore = 1;
	}
	if ($tporganics != -1)
	{
		$tporganics  = 1;
	}
	if ($tpgoods != -1)
	{
		$tpgoods = 1;
	}
	if ($tpenergy != -1)
	{
		$tpenergy = 1;
	}
	if ($tpcolonists != -1)
	{
		$tpcolonists = 1;
	}
	if ($tpcredits != -1)
	{
		$tpcredits = 1;
	}
	if ($tptorps != -1)
	{
		$tptorps = 1;
	}
	if ($tpfighters != -1)
	{
		$tpfighters = 1;
	}
	if ($tpspies != -1)
	{
		$tpspies = 1;
	}
	if ($tpdigs != -1)
	{
		$tpdigs = 1;
	}

	// now multiply all the transfer amounts by 1 to eliminate any trailing spaces
	$transfer_ore = $transfer_ore * 1;
	$transfer_organics = $transfer_organics * 1;
	$transfer_goods = $transfer_goods * 1;
	$transfer_energy = $transfer_energy * 1;
	$transfer_colonists = $transfer_colonists * 1;
	$transfer_credits = $transfer_credits * 1;
	$transfer_torps = $transfer_torps * 1;
	$transfer_fighters = $transfer_fighters * 1;
	$transfer_spies = $transfer_spies * 1;
	$transfer_dignitary = $transfer_dignitary * 1;

	if ($allore==-1)
	{
		if ($tpore == -1)
		{
			$transfer_ore = $shipinfo['ore'];
		}
		else
		{
			$transfer_ore = $planetinfo['ore'];
		}
	}

	if ($allorganics==-1)
	{
		if ($tporganics==-1)
		{
			$transfer_organics = $shipinfo['organics'];
		}
		else
		{
			$transfer_organics = $planetinfo['organics'];
		}
	}

	if ($allgoods==-1)
	{
		if ($tpgoods==-1)
		{
			$transfer_goods = $shipinfo['goods'];
		}
		else
		{
			$transfer_goods = $planetinfo['goods'];
		}
	}

	if ($allenergy==-1)
	{
		if ($tpenergy==-1)
		{
			$transfer_energy = $shipinfo['energy'];
		}
		else
		{
			$transfer_energy = $planetinfo['energy'];
		}
	}

	if ($allcolonists==-1)
	{
		if ($tpcolonists==-1)
		{
			$transfer_colonists = $shipinfo['colonists'];
		}
		else
		{
			$transfer_colonists = $planetinfo['colonists'];
		}
	}

	if ($allcredits==-1)
	{
		if ($tpcredits==-1)
		{
			$transfer_credits = $playerinfo['credits'];
		}
		else
		{
			$transfer_credits = $planetinfo['credits'];
		}
	}

	if ($alltorps==-1)
	{
		if ($tptorps==-1)
		{
			$transfer_torps = $shipinfo['torps'];
		}
		else
		{
			$transfer_torps = $planetinfo['torps'];
		}
	}

	if ($allfighters==-1)
	{
		if ($tpfighters==-1)
		{
			$transfer_fighters = $shipinfo['fighters'];
		}
		else
		{
			$transfer_fighters = $planetinfo['fighters'];
		}
	}

	if ($allspies==-1)
	{
		if ($tpspies==-1)
		{
			$res = $db->execute("SELECT * FROM $dbtables[spies] WHERE ship_id = '$shipinfo[ship_id]' AND owner_id = '$playerinfo[player_id]' ");
			$transfer_spies = $res->RecordCount();
		}
		else
		{
			$res = $db->execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
			$transfer_spies = $res->RecordCount();
		}
	}

	if ($alldigs==-1)
	{
		if ($tpdigs==-1)
		{
			$res = $db->execute("SELECT * FROM $dbtables[dignitary] WHERE ship_id = '$shipinfo[ship_id]' AND owner_id = '$playerinfo[player_id]' ");
			$transfer_dignitary = $res->RecordCount();
		}
		else
		{
			$res = $db->execute("SELECT * FROM $dbtables[dignitary] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
			$transfer_dignitary = $res->RecordCount();
		}
	}

	// ok now get rid of all negative amounts so that all operations are expressed in terms of positive units
	if ($transfer_ore < 0)
	{
		$transfer_ore = -1 * $transfer_ore;
		$tpore = -1 * $tpore;
	}

	if ($transfer_organics < 0)
	{
		$transfer_organics = -1 * $transfer_organics;
		$tporganics = -1 * $tporganics;
	}

	if ($transfer_goods < 0)
	{
		$transfer_goods = -1 * $transfer_goods;
		$tpgoods = -1 * $tpgoods;
	}

	if ($transfer_energy < 0)
	{
		$transfer_energy = -1 * $transfer_energy;
		$tpenergy = -1 * $tpenergy;
	}

	if ($transfer_colonists < 0)
	{
		$transfer_colonists = -1 * $transfer_colonistst;
		$tpcolonists = -1 * $tpcolonists;
	}

	if ($transfer_credits < 0)
	{
		$transfer_credits = -1 * $transfer_credits;
		$tpcredits = -1 * $tpcredits;
	}

	if ($transfer_torps < 0)
	{
		$transfer_torps = -1 * $transfer_torps;
		$tptorps = -1 * $tptorps;
	}

	if ($transfer_fighters < 0)
	{
		$transfer_fighters = -1 * $transfer_fighters;
		$tpfighters = -1 * $tpfighters;
	}

	if ($transfer_spies < 0)
	{
		$transfer_spies = -1 * $transfer_spies;
		$tpspies = -1 * $tpspies;
	}

	if ($transfer_dignitary < 0)
	{
		$transfer_dignitary = -1 * $transfer_dignitary;
		$tpdigs = -1 * $tpdigs;
	}

	$digtotal = 0;
	if ($spy_success_factor)
	{
		spy_sneak_to_planet($planetinfo['planet_id'], $shipinfo['ship_id']);
		spy_sneak_to_ship($planetinfo['planet_id'], $shipinfo['ship_id']);

		if ($transfer_spies and ($playerinfo['player_id'] == $planetinfo['owner']))
		{
			if($planetinfo['cloak'] < $shipinfo['cloak'])
				$spy_cloak = $shipinfo['cloak'];
			else
				$spy_cloak = $planetinfo['cloak'];

			if ($tpspies<0)
				$spytotal = transfer_to_planet($playerinfo['player_id'], $planetinfo['planet_id'], $spy_cloak, $transfer_spies);
			else
				$spytotal = transfer_to_ship($playerinfo['player_id'], $planetinfo['planet_id'], $shipinfo['cloak'], $transfer_spies);
		}
	}

	$digtotal = 0;
	if ($dig_success_factor)
	{
		if ($transfer_dignitary and ($playerinfo['player_id'] == $planetinfo['owner']))
		{
			if ($tpdigs<0)
				$digtotal = transfer_to_planet_dig($playerinfo['player_id'], $planetinfo['planet_id'], $transfer_dignitary);
			else
				$digtotal = transfer_to_ship_dig($playerinfo['player_id'], $planetinfo['planet_id'], $transfer_dignitary);
		}
	}

	$total_holds = NUM_HOLDS($shipinfo['hull']);
	$ship_fighters_max = NUM_FIGHTERS($shipinfo['computer']);
	$torps_max = NUM_TORPEDOES($shipinfo['torp_launchers']);
	$energy_max = NUM_ENERGY($shipinfo['power']);
	$used_holds = $shipinfo['ore'] + $shipinfo['organics'] + $shipinfo['goods'] + $shipinfo['colonists'];
	$transfer_toplanet = 0;

	// move commodities from ship to planet
	$transfer_ore1 = 0;
	if (($tpore == -1) && ($transfer_ore > $shipinfo['ore']))
	{
		$transfer_ore = $shipinfo['ore'];
		$transfer_ore1 = $transfer_ore;
	}else{
		if($tpore == -1)
			$transfer_toplanet = $transfer_ore;
	}

	$transfer_organics1 = 0;
	if (($tporganics == -1) && ($transfer_organics > $shipinfo['organics']))
	{
		$transfer_organics = $shipinfo['organics'];
		$transfer_organics1 = $transfer_organics;
	}else{
		if($tporganics == -1)
			$transfer_toplanet += $transfer_organics;
	}

	$transfer_goods1 = 0;
	if (($tpgoods == -1) && ($transfer_goods > $shipinfo['goods']))
	{
		$transfer_goods = $shipinfo['goods'];
		$transfer_goods1 = $transfer_goods;
	}else{
		if($tpgoods == -1)
			$transfer_toplanet += $transfer_goods;
	}

	$transfer_energy1 = 0;
	if (($tpenergy == -1) && ($transfer_energy > $shipinfo['energy']))
	{
		$transfer_energy = $shipinfo['energy'];
		$transfer_energy1 = $transfer_energy;
	}

	$transfer_colonists1 = 0;
	if (($tpcolonists == -1) && ($transfer_colonists > $shipinfo['colonists']))
	{
		$transfer_colonists = $shipinfo['colonists'];
		$transfer_colonists1 = $transfer_colonists;
	}else{
		if($tpcolonists == -1)
			$transfer_toplanet += $transfer_colonists;
	}

	$transfer_credits1 = 0;
	if (($tpcredits == -1) && ($transfer_credits > $playerinfo['credits']))
	{
		$transfer_credits = $playerinfo['credits'];
		$transfer_credits1 = $transfer_credits;
	}

	$transfer_credits1a = 0;
	if (($tpcredits == -1) && $planetinfo['base'] == 'N' && ($transfer_credits + $planetinfo['credits'] > $max_credits_without_base))
	{
		$transfer_credits = MAX($max_credits_without_base - $planetinfo['credits'],0);
		$transfer_credits1a = $transfer_credits;
	}

	$transfer_torps1 = 0;
	if (($tptorps == -1) && ($transfer_torps > $shipinfo['torps']))
	{
		$transfer_torps = $shipinfo['torps'];
		$transfer_torps1 = $transfer_torps;
	}

	$transfer_fighters1 = 0;
	if (($tpfighters == -1) && ($transfer_fighters > $shipinfo['fighters']))
	{
		$transfer_fighters = $shipinfo['fighters'];
		$transfer_fighters1 = $transfer_fighters;
	}

	// move commodities from planet to ship
	$transfer_ore2 = 0;
	if (($tpore == 1) && ($transfer_ore > $planetinfo['ore']))
	{
		$transfer_ore = $planetinfo['ore'];
		$transfer_ore2 = $transfer_ore;
	}

	$transfer_organics2 = 0;
	if (($tporganics == 1) && ($transfer_organics > $planetinfo['organics']))
	{
		$transfer_organics = $planetinfo['organics'];
		$transfer_organics2 = $transfer_organics;
	}

	$transfer_goods2 = 0;
	if (($tpgoods == 1) && ($transfer_goods > $planetinfo['goods']))
	{
		$transfer_goods = $planetinfo['goods'];
		$transfer_goods2 = $transfer_goods;
	}

	$transfer_energy2 = 0;
	if (($tpenergy == 1) && ($transfer_energy > $planetinfo['energy']))
	{
		$transfer_energy = $planetinfo['energy'];
		$transfer_energy2 = $transfer_energy;
	}

	$transfer_colonists2 = 0;
	if (($tpcolonists == 1) && ($transfer_colonists > $planetinfo['colonists']))
	{
		$transfer_colonists = $planetinfo['colonists'];
		$transfer_colonists2 = $transfer_colonists;
	}

	$transfer_credits2 = 0;
	if (($tpcredits == 1) && ($transfer_credits > $planetinfo['credits']))
	{
		$transfer_credits = $planetinfo['credits'];
		$transfer_credits2 = $transfer_credits;
	}

	$transfer_torps2 = 0;
	if (($tptorps == 1) && ($transfer_torps > $planetinfo['torps']))
	{
		$transfer_torps = $planetinfo['torps'];
		$transfer_torps2 = $transfer_torps;
	}

	$transfer_fighters2 = 0;
	if (($tpfighters == 1) && ($transfer_fighters > $planetinfo['fighters']))
	{
		$transfer_fighters = $planetinfo['fighters'];
		$transfer_fighters2 = $transfer_fighters;
	}

	// check for overage to ship
	//$transfer_credits + $planetinfo['credits'] > $max_credits_without_base
	//$transfer_credits = MAX($max_credits_without_base - $planetinfo['credits'],0)
	$used_holds -= $transfer_toplanet;

	$transfer_ore3 = 0;
	if (($tpore == 1) && ($transfer_ore + $used_holds) > $total_holds)
	{
		$transfer_ore = MAX($total_holds - $used_holds,0);
		$transfer_ore3 = $transfer_ore;
	}

	$transfer_organics3 = 0;
	if (($tporganics == 1) && (($transfer_ore * $tpore) + $transfer_organics + $used_holds) > $total_holds)
	{
		$transfer_organics = MAX($total_holds - (($transfer_ore * $tpore) + $used_holds),0);
		$transfer_organics3 = $transfer_organics;
	}

	$transfer_goods3 = 0;
	if (($tpgoods == 1) && (($transfer_ore * $tpore) + ($transfer_organics * $tporganics) + $transfer_goods + $used_holds) > $total_holds)
	{
		$transfer_goods = MAX($total_holds - (($transfer_ore * $tpore) + ($transfer_organics * $tporganics) + $used_holds),0);
		$transfer_goods3 = $transfer_goods;
	}

	$transfer_colonists3 = 0;
	if (($tpcolonists == 1) && (($transfer_ore * $tpore) + ($transfer_organics * $tporganics) + ($transfer_goods * $tpgoods) + $transfer_colonists + $used_holds) > $total_holds)
	{
		$transfer_colonists = MAX($total_holds - (($transfer_ore * $tpore) + ($transfer_organics * $tporganics) + ($transfer_goods * $tpgoods) + $used_holds),0);
		$transfer_colonists3 = $transfer_colonists;
	}

	$transfer_energy3 = 0;
	if (($tpenergy == 1) && ($transfer_energy + $shipinfo['energy'] > $energy_max))
	{
		$transfer_energy = MAX($energy_max - $shipinfo['energy'],0);
		$transfer_energy3 = $transfer_energy;
	}

	$transfer_torps3 = 0;
	if (($tptorps == 1) && ($transfer_torps + $shipinfo['torps'] > $torps_max))
	{
		$transfer_torps = MAX($torps_max - $shipinfo['torps'],0);
		$transfer_torps3 = $transfer_torps;
	}

	$transfer_fighters3 = 0;
	if (($tpfighters == 1) && ($transfer_fighters + $shipinfo['fighters'] > $ship_fighters_max))
	{
		$transfer_fighters = MAX($ship_fighters_max - $shipinfo['fighters'],0);
		$transfer_fighters3 = $transfer_fighters;
	}

	// Now that we have the amounts adjusted to suit available resources, go ahead and multiply them by their tpflag.
	$transfer_ore = $transfer_ore * $tpore;
	$transfer_organics = $transfer_organics * $tporganics;
	$transfer_goods = $transfer_goods * $tpgoods;
	$transfer_energy = $transfer_energy * $tpenergy;
	$transfer_colonists = $transfer_colonists * $tpcolonists;
	$transfer_credits = $transfer_credits * $tpcredits;
	$transfer_torps = $transfer_torps * $tptorps;
	$transfer_fighters = $transfer_fighters * $tpfighters;

	$total_holds_needed = $transfer_ore + $transfer_organics + $transfer_goods + $transfer_colonists;

	$smarty->assign("spy_success_factor", $spy_success_factor);
	$smarty->assign("dig_success_factor", $dig_success_factor);
	$smarty->assign("l_spy_transferred", $l_spy_transferred);
	$smarty->assign("l_dig_transferred", $l_dig_transferred);
	$smarty->assign("digtotal", $digtotal);
	$smarty->assign("spytotal", $spytotal);
	$smarty->assign("l_planet2_noten", $l_planet2_noten);
	$smarty->assign("l_planet2_settr", $l_planet2_settr);
	$smarty->assign("l_units", $l_units);
	$smarty->assign("l_ore", $l_ore);
	$smarty->assign("l_organics", $l_organics);
	$smarty->assign("l_goods", $l_goods);
	$smarty->assign("l_energy", $l_energy);
	$smarty->assign("l_colonists", $l_colonists);
	$smarty->assign("l_credits", $l_credits);
	$smarty->assign("l_planet2_baseexceeded", $l_planet2_baseexceeded);
	$smarty->assign("l_torps", $l_torps);
	$smarty->assign("l_fighters", $l_fighters);
	$smarty->assign("l_planet2_losup", $l_planet2_losup);
	$smarty->assign("transfer_fighters1", $transfer_fighters1);
	$smarty->assign("transfer_torps1", $transfer_torps1);
	$smarty->assign("transfer_credits1a", $transfer_credits1a);
	$smarty->assign("transfer_credits1", $transfer_credits1);
	$smarty->assign("transfer_colonists1", $transfer_colonists1);
	$smarty->assign("transfer_energy1", $transfer_energy1);
	$smarty->assign("transfer_goods1", $transfer_goods1);
	$smarty->assign("transfer_organics1", $transfer_organics1);
	$smarty->assign("transfer_ore1", $transfer_ore1);
	$smarty->assign("transfer_fighters2", $transfer_fighters2);
	$smarty->assign("transfer_torps2", $transfer_torps2);
	$smarty->assign("transfer_credits2", $transfer_credits2);
	$smarty->assign("transfer_colonists2", $transfer_colonists2);
	$smarty->assign("transfer_energy2", $transfer_energy2);
	$smarty->assign("transfer_goods2", $transfer_goods2);
	$smarty->assign("transfer_organics2", $transfer_organics2);
	$smarty->assign("transfer_ore2", $transfer_ore2);
	$smarty->assign("transfer_fighters3", $transfer_fighters3);
	$smarty->assign("transfer_torps3", $transfer_torps3);
	$smarty->assign("transfer_energy3", $transfer_energy3);
	$smarty->assign("transfer_colonists3", $transfer_colonists3);
	$smarty->assign("transfer_goods3", $transfer_goods3);
	$smarty->assign("transfer_organics3", $transfer_organics3);
	$smarty->assign("transfer_ore3", $transfer_ore3);

	if ($playerinfo['player_id'] != $planetinfo['owner'] && $transfer_credits != 0 && $team_planet_transfers != 1)
	{
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_planet2_noteamtransfer", $l_planet2_noteamtransfer);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_transfernoteam.tpl");
		include ("footer.php");
		die();
	}

	if ($total_holds_needed > $free_holds)
	{
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_planet2_fortr", $l_planet2_fortr);
		$smarty->assign("l_holds", $l_holds);
		$smarty->assign("l_planet2_noten", $l_planet2_noten);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_transfernoholds.tpl");
		include ("footer.php");
		die();
	}

	if (!empty($planetinfo))
	{
		if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] <> 0))
		{
			$mineteam = 1;
			if ($transfer_ore < 0 && $shipinfo['ore'] < abs($transfer_ore))
			{
				$transfer_ore = 0;
				$transfer_ore4 = 1;
			}
			elseif ($transfer_ore > 0 && $planetinfo['ore'] < abs($transfer_ore))
			{
				$transfer_ore = 0;
				$transfer_ore4 = 1;
			}

			if ($transfer_organics < 0 && $shipinfo['organics'] < abs($transfer_organics))
			{
				$transfer_organics = 0;
				$transfer_organics4 = 1;
			}
			elseif ($transfer_organics > 0 && $planetinfo['organics'] < abs($transfer_organics))
			{
				$transfer_organics = 0;
				$transfer_organics4 = 1;
			}

			if ($transfer_goods < 0 && $shipinfo['goods'] < abs($transfer_goods))
			{
				$transfer_goods = 0;
				$transfer_goods4 = 1;
			}
			elseif ($transfer_goods > 0 && $planetinfo['goods'] < abs($transfer_goods))
			{
				$transfer_goods = 0;
				$transfer_goods4 = 1;
			}

			if ($transfer_energy < 0 && $shipinfo['energy'] < abs($transfer_energy))
			{
				$transfer_energy = 0;
				$transfer_energy4 = 1;
			}
			elseif ($transfer_energy > 0 && $planetinfo['energy'] < abs($transfer_energy))
			{
				$transfer_energy = 0;
				$transfer_energy4 = 1;
			}
			elseif ($transfer_energy > 0 && abs($transfer_energy) > $free_power)
			{
				$transfer_energy = 0;
				$transfer_energy4 = 2;
			}

			if ($transfer_colonists < 0 && $shipinfo['colonists'] < abs($transfer_colonists))
			{
				$transfer_colonists = 0;
				$transfer_colonists4 = 1;
			}
			elseif ($transfer_colonists > 0 && $planetinfo['colonists'] < abs($transfer_colonists))
			{
				$transfer_colonists = 0;
				$transfer_colonists4 = 1;
			}

			if ($transfer_fighters < 0 && $shipinfo['fighters'] < abs($transfer_fighters))
			{
				$transfer_fighters = 0;
				$transfer_fighters4 = 1;
			}
			elseif ($transfer_fighters > 0 && $planetinfo['fighters'] < abs($transfer_fighters))
			{
				$transfer_fighters = 0;
				$transfer_fighters4 = 1;
			}
			elseif ($transfer_fighters > 0 && abs($transfer_fighters) > $fighter_max)
			{
				$transfer_fighters = 0;
				$transfer_fighters4 = 2;
			}

			if ($transfer_torps < 0 && $shipinfo['torps'] < abs($transfer_torps))
			{
				$transfer_torps = 0;
				$transfer_torps4 = 1;
			}
			elseif ($transfer_torps > 0 && $planetinfo['torps'] < abs($transfer_torps))
			{
				$transfer_torps = 0;
				$transfer_torps4 = 1;
			}
			elseif ($transfer_torps > 0 && abs($transfer_torps) > $torpedo_max)
			{
				$transfer_torps = 0;
				$transfer_torps4 = 2;
			}

			if ($transfer_credits < 0 && $playerinfo['credits'] < abs($transfer_credits))
			{
				$transfer_credits = 0;
				$transfer_credits4 = 1;
			}
			elseif ($transfer_credits > 0 && $planetinfo['credits'] < abs($transfer_credits))
			{
				$transfer_credits = 0;
				$transfer_credits4 = 1;
			}

			if ($transfer_credits > 0 and $planetinfo['team_cash'] == "N" and $playerinfo['player_id'] != $planetinfo['owner'])
			{
				$transfer_credits = 0;
			}

			if($transfer_credits < 0){
				if (abs($transfer_credits) + $planetinfo['credits'] > $planetinfo['max_credits'])
				{
					$transfer_credits = -($planetinfo['max_credits'] - $planetinfo['credits']);
				}
			}

			$tcredits=NUMBER(abs($transfer_credits));
			if($transfer_credits > 0)
				$direction = $l_planet_from;
			else $direction = $l_planet_to;

			if (abs($transfer_credits) > 0 and $playerinfo['player_id'] != $planetinfo['owner'] and $planetinfo['team_cash'] == "Y")
			{
				$logtransfer = str_replace("[character_name]", "<font color=white><b>$playerinfo[character_name]</b></font>", $l_planet_teamcredits);
				$logtransfer = str_replace("[tcredits]", "<font color=white><b>$tcredits</b></font>", $logtransfer);
				$logtransfer = str_replace("[name]", "<font color=white><b>$planetinfo[name]</b></font>", $logtransfer);
				$logtransfer = str_replace("[direction]", "<font color=white><b>$direction</b></font>", $logtransfer);
				$logtransfer = str_replace("[sector_id]", "<br><font color=white><b>$planetinfo[sector_id]</b></font>", $logtransfer);
				playerlog($planetinfo['owner'], LOG_RAW, $logtransfer);
				if ($planetinfo['team'] > 0){
					playerlog($planetinfo['team'], LOG_RAW, $logtransfer);
				}
			}

			$averagetechlvl = ($planetinfo['computer'] + $planetinfo['sensors'] + $planetinfo['beams'] + $planetinfo['torp_launchers'] + $planetinfo['shields'] + $planetinfo['jammer'] + $planetinfo['cloak']) / 7;
			if($transfer_colonists < 0)
			{
				if ($planetinfo['colonists'] - $transfer_colonists >= ($colonist_limit + floor($colonist_tech_add * $averagetechlvl)))
				{
					$transfer_colonists = $planetinfo['colonists'] - ($colonist_limit + floor($colonist_tech_add * $averagetechlvl));
				}
			}

			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=ore+$transfer_ore, organics=organics+$transfer_organics, goods=goods+$transfer_goods, energy=energy+$transfer_energy, colonists=colonists+$transfer_colonists, torps=torps+$transfer_torps, fighters=fighters+$transfer_fighters WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$transfer_credits, turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET ore=ore-$transfer_ore, organics=organics-$transfer_organics, goods=goods-$transfer_goods, energy=energy-$transfer_energy, colonists=colonists-$transfer_colonists, torps=torps-$transfer_torps, fighters=fighters-$transfer_fighters, credits=credits-$transfer_credits WHERE planet_id=$planet_id");
			db_op_result($debug_query,__LINE__,__FILE__);

		}
		else
		{
			$mineteam = 0;
		}
		$smarty->assign("l_planet", $l_planet);
		$smarty->assign("mineteam", $mineteam);
		$smarty->assign("l_planet2_notowner", $l_planet2_notowner);
		$smarty->assign("l_planet2_compl", $l_planet2_compl);
		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("l_planet2_fortr", $l_planet2_fortr);
		$smarty->assign("l_planet2_power", $l_planet2_power);
		$smarty->assign("l_planet2_comp", $l_planet2_comp);
		$smarty->assign("l_planet2_laun", $l_planet2_laun);
		$smarty->assign("transfer_credits4", $transfer_credits4);
		$smarty->assign("transfer_torps4", $transfer_torps4);
		$smarty->assign("transfer_fighters4", $transfer_fighters4);
		$smarty->assign("transfer_colonists4", $transfer_colonists4);
		$smarty->assign("transfer_energy4", $transfer_energy4);
		$smarty->assign("transfer_goods4", $transfer_goods4);
		$smarty->assign("transfer_organics4", $transfer_organics4);
		$smarty->assign("transfer_ore4", $transfer_ore4);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_transfer.tpl");
		include ("footer.php");
		die();
	}
}
close_database();
?>
