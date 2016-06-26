<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: modify-defences.php

include ("config/config.php");
include ("combat_functions.php");
$no_gzip = 1;

$title = $l_md_title;

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

if (!isset($defence_id))
{
	$smarty->assign("error_msg", $l_md_invalid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."modify-defensesdie.tpl");
	include ("footer.php");
	die();
}

if ((!isset($response)) || ($response == ''))
{
	$response = '';
}

if ($playerinfo['turns']<1)
{
	$smarty->assign("error_msg", $l_md_noturn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."modify-defensesdie.tpl");
	include ("footer.php");
	die();
}

$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE defence_id=$defence_id ");
//Put the defence information into the array "defenceinfo"
if ($result3 == 0)
{
	$smarty->assign("error_msg", $l_md_nolonger);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."modify-defensesdie.tpl");
	include ("footer.php");
	die();
}

$defenceinfo = $result3->fields;
if ($defenceinfo['sector_id'] <> $shipinfo['sector_id'])
{
	$smarty->assign("error_msg", $l_md_nothere);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."modify-defensesdie.tpl");
	include ("footer.php");
	die();
}
if ($defenceinfo['player_id'] == $playerinfo['player_id'])
{
	$defence_owner = $l_md_you;
}
else
{
	$defence_player_id = $defenceinfo['player_id'];
	$resulta = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id = $defence_player_id ");
	$ownerinfo = $resulta->fields;
	$defence_owner = $ownerinfo['character_name'];
}

$defence_type = $defenceinfo['defence_type'] == 'F' ? $l_fighters : $l_mines;
$qty = $defenceinfo['quantity'];

switch($response)
{
	case "fight":
		if ($defenceinfo['player_id'] == $playerinfo['player_id'])
		{
			$smarty->assign("error_msg", $l_md_yours);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."modify-defensesdie.tpl");
			include ("footer.php");
			die();
		}

		$sector = $shipinfo['sector_id'] ;
		$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and player_id != '$playerinfo[player_id]' ORDER BY quantity DESC");
		if ($result3 > 0)
		{
			while (!$result3->EOF)
			{
				$row = $result3->fields;
				$defences[$num_defences] = $row;
				if ($defences[$num_defences]['defence_type'] == 'F')
				{
					$total_sector_fighters += $defences[$num_defences]['quantity'];
				}
				elseif ($defences[$num_defences]['defence_type'] == 'M')
				{
					$total_sector_mines += $defences[$num_defences]['quantity'];
				}

				$num_defences++;
				$result3->MoveNext();
			}
		}

		$fm_owner = $defences[0]['player_id'];
		$result9 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
		$fighters_owner = $result9->fields;

		if ($defenceinfo['defence_type'] == 'F')
		{
			$destination = $sector;
			include ("combat_sector_fighters.php");

			$smarty->assign("error_msg", "");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."modify-defensesdie.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			// Attack mines goes here

			$attacker_shield_energy = floor($shipinfo['energy'] * 0.4);
			$attacker_beam_energy = $shipinfo['energy'] - $attacker_shield_energy;

			$attackerbeams = NUM_BEAMS($shipinfo['beams']);

			if ($attackerbeams < $attacker_beam_energy)
			{
				$attacker_beam_energy = $attackerbeams;
			}

			$attack_beamtomine_dmg = floor($attacker_beam_energy * 0.025);

			$highsensors=0;
			// get planet sensors
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner!=$playerinfo[player_id] and (team > 0 and team!=$playerinfo[team])) and base='Y' and sector_id='$destination' order by sensors DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;

			if ($highsensors < $planets['sensors']){
				$highsensors=$planets['sensors'];
			}

			$highcloak=0;
			// get planet sensors
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner!=$playerinfo[player_id] and (team > 0 and team!=$playerinfo[team])) and base='Y' and sector_id='$destination' order by cloak DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;

			if ($highcloak < $planets['cloak']){
				$highcloak=$planets['cloak'];
			}

			$attackerlowpercent = ecmcheck($highcloak, $shipinfo['sensors'], $full_attack_modifier);
			$targetlowpercent = ecmcheck($shipinfo['cloak'], $highsensors, -$full_attack_modifier);

			if(!class_exists($shipinfo['beam_class'])){
				include ("class/" . $shipinfo['beam_class'] . ".inc");
			}

			$attackobject = new $shipinfo['beam_class']();
			$beam_damage_all = $attackobject->beam_damage_all;

			if(!class_exists("Basic_Torpedo")){
				include ("class/Basic_Torpedo.inc");
			}

			$targetobject = new Basic_Torpedo();
			$mine_hit_pts = $targetobject->mine_hit_pts;

			$target_mines_used = 0;
			$attack_energy_left = $attack_beamtomine_dmg;
			if($attack_beamtomine_dmg != 0)
			{
				$attack_mine_damage = calc_damage($attack_beamtomine_dmg, $beam_damage_all, $attackerlowpercent, $shipinfo['beams'], $highcloak);
				$attack_energy_left = $attack_mine_damage[1];

				$target_mine_hit_pts = $total_sector_mines * $mine_hit_pts;
				if($attack_mine_damage[0] > $target_mine_hit_pts)
				{
					$attack_mine_damage[0] = $attack_mine_damage[0] - $target_mine_hit_pts;
					$attack_energy_left = floor($attack_mine_damage[0] / $beam_damage_all);
					$target_mine_used = $total_sector_mines;
				}
				else
				{
					$target_mine_used = floor($attack_mine_damage[0] / $mine_hit_pts);
				}
			}

			$attackerenergyused = $attack_beamtomine_dmg - $attack_energy_left;

			$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET energy=energy-$attackerenergyused WHERE " .
										"ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			if($target_mine_used > 0){
				explode_mines($sector, $target_mine_used);
			}

			$char_name = $playerinfo['character_name'];
			$l_md_msgdownerb=str_replace("[sector]",$sector,$l_md_msgdownerb);
			$l_md_msgdownerb=str_replace("[mines]",$target_mine_used,$l_md_msgdownerb);
			$l_md_msgdownerb=str_replace("[name]",$char_name,$l_md_msgdownerb);
			message_defence_owner($sector,"$l_md_msgdownerb");
			$smarty->assign("error_msg", $l_md_msgdownerb);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."modify-defensesdie.tpl");
			include ("footer.php");
			die();
		}
		break;

	case "retrieve":
		if ($defenceinfo['player_id'] <> $playerinfo['player_id'])
		{
			$smarty->assign("error_msg", "$l_md_bmines $playerbeams $l_mines");
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."modify-defensesdie.tpl");
			include ("footer.php");
			die();
		}

		$quantity = stripnum($quantity);
		if ($quantity < 0)
		{
			$quantity = 0;
		}

		if ($quantity > $defenceinfo['quantity'])
		{
			$quantity = $defenceinfo['quantity'];
		}

		$torpedo_max = NUM_TORPEDOES($shipinfo['torp_launchers']) - $shipinfo['torps'];
		$fighter_max = NUM_FIGHTERS($shipinfo['computer']) - $shipinfo['fighters'];
		if ($defenceinfo['defence_type'] == 'F')
		{
			if ($quantity > $fighter_max)
			{
				$quantity = $fighter_max;
			}
		}

		if ($defenceinfo['defence_type'] == 'M')
		{
			if ($quantity > $torpedo_max)
			{
				$quantity = $torpedo_max;
			}
		}

		$ship_id = $shipinfo['ship_id'];
		if ($quantity > 0)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=quantity - $quantity WHERE " .
										"defence_id = $defence_id");
			db_op_result($debug_query,__LINE__,__FILE__);

			if ($defenceinfo['defence_type'] == 'M')
			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] set torps=torps + $quantity WHERE ship_id = $ship_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] set fighters=fighters + $quantity WHERE ship_id = $ship_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE quantity <= 0");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		$stamp = date("Y-m-d H:i:s");

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1 " .
									"WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$shipinfo[sector_id] WHERE " .
									"player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$smarty->assign("error_msg", "$l_md_retr $quantity $defence_type.");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."modify-defensesdie.tpl");
		include ("footer.php");
		die();
		break;

	default:
		$l_md_consist=str_replace("[qty]",$qty,$l_md_consist);
		$l_md_consist=str_replace("[type]",$defence_type,$l_md_consist);
		$l_md_consist=str_replace("[owner]",$defence_owner,$l_md_consist);

		if ($defenceinfo['player_id'] != $playerinfo['player_id'])
		{
			$player_id = $defenceinfo['player_id'];
			$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$player_id");
			$fighters_owner = $result2->fields;

			if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
			{
				$fight = 1;
			}else{
				$fight = 0;
			}
		}

		$smarty->assign("l_md_consist", $l_md_consist);
		$smarty->assign("defenseid", $defenceinfo['player_id']);
		$smarty->assign("playerid", $playerinfo['player_id']);
		$smarty->assign("l_md_youcan", $l_md_youcan);
		$smarty->assign("l_md_retrieve", $l_md_retrieve);
		$smarty->assign("defence_type", $defence_type);
		$smarty->assign("defence_id", $defence_id);
		$smarty->assign("l_submit", $l_submit);
		$smarty->assign("defensetype", $defenceinfo['defence_type']);
		$smarty->assign("l_md_attack", $l_md_attack);
		$smarty->assign("fight", $fight);
		$smarty->assign("l_md_consist", $l_md_consist);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."modify-defenses.tpl");
		include ("footer.php");
		die();
		break;
}

close_database();
?>
