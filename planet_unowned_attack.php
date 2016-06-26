<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_unowned_attack.php

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

	if ($planetinfo['owner'] != 3)
	{

		if ($playerinfo['turns'] < 1)
		{
			echo "$l_cmb_atleastoneturn<BR><BR>";
			TEXT_GOTOMAIN();
			include ("footer.php");
			die();
		}

		$result2 = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id='$planetinfo[owner]'");
		$targetinfo = $result2->fields;

		send_system_im($planetinfo['owner'], $l_planet_imtitleattack, $playerinfo['character_name'] . " $l_planet_imisattacking $planetinfo[name] $l_planet_iminsector $planetinfo[sector_id].", $targetinfo['last_login']);

		$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
		$sc_error_plus=100;
		if ($sc_error < 100){
			$sc_error_plus=115;
		}

		$result4 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		$shipsonplanet = $result4->RecordCount();

		if ($shipsonplanet > 0 and ($attackerarmor > 0 or $attackershields > 0))
		{
			$l_cmb_shipdock = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock);
			echo "<BR><BR><CENTER><b>$l_cmb_shipdock<BR>$l_cmb_engshiptoshipcombat</b></CENTER><BR><BR>\n";

			while (!$result4->EOF)
			{
				$onplanet = $result4->fields;
				echo "<b><BR>-$onplanet[name] $l_cmb_approachattackvector-</b><BR>";
				$result4->MoveNext();
			}
		}
		else
		{
			echo "<BR><BR><CENTER><b>$l_cmb_noshipsdocked</b></CENTER><BR><BR>\n";
		}

		// get attacker beam, shield and armor values
		$attacker_shield_energy = floor($shipinfo['energy'] * 0.4);
		$attacker_beam_energy = $shipinfo['energy'] - $attacker_shield_energy;

		$attackershields = NUM_SHIELDS($shipinfo['shields']);

		if ($attackershields < $attacker_shield_energy)
		{
			$attacker_shield_energy = $attackershields;
		}

		$attackerbeams = NUM_BEAMS($shipinfo['beams']);

		if ($attackerbeams < $attacker_beam_energy)
		{
			$attacker_beam_energy = $attackerbeams;
		}

		$attackerenergyset = $attacker_beam_energy + $attacker_shield_energy;
		$attackerstartenergy = ($attacker_beam_energy + $attacker_shield_energy);

		$attack_beamtofighter_dmg = floor($attacker_beam_energy * 0.05);
		$attack_beamtotorp_dmg = floor($attacker_beam_energy * 0.05);

		$attackertorps = $shipinfo['torps'];

		$attackerarmor = $shipinfo['armour_pts'];

		$attackerfighters = $shipinfo['fighters'];

		$attackerlowpercent = ecmcheck($shipinfo['sensors'], $shipinfo['sensors'], $full_attack_modifier);

		// get target beam, shield and armor values
		$target_shield_energy = floor($planetinfo['energy'] * 0.4);
		$target_beam_energy = $planetinfo['energy'] - $target_shield_energy;

		$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
		if ($planetinfo['shields'] == 0) 
		{
			$targetshields = 0;
		}
		else
		{
			$targetshields = NUM_SHIELDS($planetinfo['shields'] + $base_factor);
		}

		$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		$shipsonplanet=0;
		while (!$res->EOF)
		{
			$targetshields += NUM_SHIELDS($res->fields['shields']);
			$shipsonplanetid[$shipsonplanet] = $res->fields['ship_id'];
			$res->MoveNext();
			$shipsonplanet++;
		}

		$full_target_shield_energy = $target_shield_energy;

		if (($targetshields * $planet_shield_multiplier) < $target_shield_energy)
		{
			$target_shield_energy = $targetshields;
		}
		else
		{
			$target_shield_energy = floor($target_shield_energy / $planet_shield_multiplier);
		}

		$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
		if ($planetinfo['beams'] == 0) 
		{
			$targetbeams = 0;
		}
		else
		{
			$targetbeams = NUM_BEAMS($planetinfo['beams'] + $base_factor);
		}

		$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		while (!$res->EOF)
		{
			$targetbeams += NUM_BEAMS($res->fields['beams']);
			$res->MoveNext();
		}

		if ($targetbeams < $target_beam_energy)
		{
			$target_beam_energy = $targetbeams;
		}

		$targetenergyset = ($target_shield_energy * $planet_shield_multiplier) + $target_beam_energy;

		$target_beamtofighter_dmg = floor($target_beam_energy * 0.05);
		$target_beamtotorp_dmg = floor($target_beam_energy * 0.05);

		$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
		$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		$torp_launchers = NUM_TORPEDOES($planetinfo['torp_launchers'] + $base_factor) ;
		$torps = $planetinfo['torps'];
		if ($res)
		{
		   while (!$res->EOF)
			{
				$torps += $res->fields['torps'];  
				$ship_torps =  NUM_TORPEDOES($res->fields['torp_launchers']);
				$torp_launchers = $torp_launchers + $ship_torps;
				$res->MoveNext();
			}
		}
		if ($torp_launchers > $torps)
		{
			$targettorps = $torps;
		}
		else
		{
			$targettorps = $torp_launchers;
		}

		$targetarmour = $planetinfo['armour_pts'];

		$base_factor = ($planetinfo['base'] == 'Y') ? $basedefense : 0;
		$planet_comp_level = NUM_FIGHTERS($planetinfo['computer'] + $base_factor);
		$figs = $planetinfo['fighters'];
		$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
		if ($res)  
		{
			while (!$res->EOF)  
			{
				$figs += $res->fields['fighters'];  
				$ship_comp =  NUM_FIGHTERS($res->fields['computer']);  
				$planet_comp_level = $planet_comp_level + $ship_comp;  
				$res->MoveNext();  
			}
		}

		if ($planet_comp_level > $figs)
		{
			$targetfighters = $figs;
		}
		else
		{
			$targetfighters = $planet_comp_level;
		}

		$targetlowpercent = ecmcheck($shipinfo['ecm'], $planetinfo['sensors'], -$full_attack_modifier);

		$dtarget_beam_energy = $target_beam_energy;
		$dtargetfighters = $targetfighters;
		$dtarget_shield_energy = $target_shield_energy;
		$dtargettorps = $targettorps;
		$dtargetarmour = $targetarmour;

		$success = (10 - $planetinfo['cloak'] / 2 + $shipinfo['sensors']) * 5;
		if ($success < 5)
		{
			$success = 5;
		}
		if ($success > 95)
		{
			$success = 95;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$dtarget_beam_energy=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$dtargetfighters=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$dtarget_shield_energy=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$dtargettorps=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$dtargetarmour=0;
		}

echo "
		<CENTER>
		<table width='75%' border='0' bgcolor=\"#000000\">
		<tr><td colspan=6 align=center><hr></td></tr>
		<tr ALIGN='CENTER'>
		<td width='9%' height='27'></td>
		<td width='12%' height='27'><FONT COLOR='WHITE'>$l_cmb_beams</FONT></td>
		<td width='17%' height='27'><FONT COLOR='WHITE'>$l_cmb_fighters</FONT></td>
		<td width='18%' height='27'><FONT COLOR='WHITE'>$l_cmb_shields</FONT></td>
		<td width='11%' height='27'><FONT COLOR='WHITE'>$l_cmb_torps</FONT></td>
		<td width='11%' height='27'><FONT COLOR='WHITE'>$l_cmb_armor</FONT></td>
		</tr>
		<tr ALIGN='CENTER'>
		<td width='9%'> <FONT COLOR='yellow'><B>$l_cmb_you</B></td>
		<td width='12%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_beam_energy)."&nbsp;</B></FONT></td>
		<td width='17%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attackerfighters)."&nbsp;</B></FONT></td>
		<td width='18%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_shield_energy)."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attackertorps)."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attackerarmor)."&nbsp;</B></FONT></td>
		</tr>
		<tr ALIGN='CENTER'>
		<td width='9%'> <FONT COLOR='RED'>$l_cmb_planet $planetinfo[name]</td>
		<td width='12%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($dtarget_beam_energy * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='17%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($dtargetfighters * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='18%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($dtarget_shield_energy * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($dtargettorps * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($dtargetarmour * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		</tr>
		<tr><td colspan=6 align=center>&nbsp;</td></tr>
		</table>
		</CENTER>
";

		update_player_experience($playerinfo['player_id'], $attacking_planet);
		planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_ATTACKED);
		// Planetary defense system calculation

		// Begin actual combat calculations

		$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 1);

		if($isfedbounty > 0)
		{
			echo $l_by_fedbounty2 . "<BR><BR>";
		}

		$targetshipshields = $planetinfo['shields'];
		$targetshiparmour = $planetinfo['armour'];
		$targetshiptorp_launchers = $planetinfo['torp_launchers'];
		$targetshipbeams = $planetinfo['beams'];
		$targetshipcomputer = $planetinfo['computer'];

		$planetinfoarmour_pts = $targetarmour;
		$planetinfofighters = $targetfighters;
		$planetinfotorps = $targettorps;
		$planetinfoenergy = ($target_beam_energy + $target_shield_energy);

		$targetname = "$l_cmb_planet " . $planetinfo['name'];

		if(!class_exists($shipinfo['computer_class'])){
			include ("class/" . $shipinfo['computer_class'] . ".inc");
		}

		$attackobject = new $shipinfo['computer_class']();
		$fighter_damage_shields = $attackobject->fighter_damage_shields;
		$fighter_damage_all = $attackobject->fighter_damage_all;
		$fighter_hit_pts = $attackobject->fighter_hit_pts;

		if(!class_exists($shipinfo['beam_class'])){
			include ("class/" . $shipinfo['beam_class'] . ".inc");
		}

		$attackobject = new $shipinfo['beam_class']();
		$beam_damage_shields = $attackobject->beam_damage_shields;
		$beam_damage_all = $attackobject->beam_damage_all;

		if(!class_exists($shipinfo['torp_class'])){
			include ("class/" . $shipinfo['torp_class'] . ".inc");
		}

		$attackobject = new $shipinfo['torp_class']();
		$torp_damage_shields = $attackobject->torp_damage_shields;
		$torp_damage_all = $attackobject->torp_damage_all;
		$torp_hit_pts = $attackobject->torp_hit_pts;

		if(!class_exists($shipinfo['shield_class'])){
			include ("class/" . $shipinfo['shield_class'] . ".inc");
		}

		$attackobject = new $shipinfo['shield_class']();
		$ship_shield_hit_pts = $attackobject->ship_shield_hit_pts;

		if(!class_exists($shipinfo['armor_class'])){
			include ("class/" . $shipinfo['armor_class'] . ".inc");
		}

		$attackobject = new $shipinfo['armor_class']();
		$ship_armor_hit_pts = $attackobject->ship_armor_hit_pts;

		if(!class_exists($planetinfo['computer_class'])){
			include ("class/" . $planetinfo['computer_class'] . ".inc");
		}

		$targetobject = new $planetinfo['computer_class']();
		$fighter_damage_shields = $targetobject->fighter_damage_shields;
		$fighter_damage_all = $targetobject->fighter_damage_all;
		$fighter_hit_pts = $targetobject->fighter_hit_pts;

		if(!class_exists($planetinfo['beam_class'])){
			include ("class/" . $planetinfo['beam_class'] . ".inc");
		}

		$targetobject = new $planetinfo['beam_class']();
		$beam_damage_shields = $targetobject->beam_damage_shields;
		$beam_damage_all = $targetobject->beam_damage_all;

		if(!class_exists($planetinfo['torp_class'])){
			include ("class/" . $planetinfo['torp_class'] . ".inc");
		}

		$targetobject = new $planetinfo['torp_class']();
		$torp_damage_shields = $targetobject->torp_damage_shields;
		$torp_damage_all = $targetobject->torp_damage_all;
		$torp_hit_pts = $targetobject->torp_hit_pts;

		if(!class_exists($planetinfo['shield_class'])){
			include ("class/" . $planetinfo['shield_class'] . ".inc");
		}

		$targetobject = new $planetinfo['shield_class']();
		$ship_shield_hit_pts = $targetobject->planet_shield_hit_pts;

		if(!class_exists($planetinfo['armor_class'])){
			include ("class/" . $planetinfo['armor_class'] . ".inc");
		}

		$targetobject = new $planetinfo['armor_class']();
		$ship_armor_hit_pts = $targetobject->planet_armor_hit_pts;

		echo "<center>";
		include ("combat.php");

		echo "
			<CENTER>
			<table width='75%' border='0' bgcolor=\"#000000\">
			<tr><td colspan=6 align=center><hr></td></tr>
			<tr ALIGN='CENTER'>
			<td width='9%' height='27'></td>
			<td width='12%' height='27'><FONT COLOR='WHITE'>$l_cmb_beams</FONT></td>
			<td width='17%' height='27'><FONT COLOR='WHITE'>$l_cmb_fighters</FONT></td>
			<td width='18%' height='27'><FONT COLOR='WHITE'>$l_cmb_shields</FONT></td>
			<td width='11%' height='27'><FONT COLOR='WHITE'>$l_cmb_torps</FONT></td>
			<td width='11%' height='27'><FONT COLOR='WHITE'>$l_cmb_armor</FONT></td>
			</tr>
			<tr ALIGN='CENTER'>
			<td width='9%'> <FONT COLOR='yellow'><B>$l_cmb_you</B></td>
			<td width='12%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attack_energy_left)."&nbsp;</B></FONT></td>
			<td width='17%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_fighters_left2)."&nbsp;</B></FONT></td>
			<td width='18%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_shields_left)."&nbsp;</B></FONT></td>
			<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_torps_left)."&nbsp;</B></FONT></td>
			<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_armor_left)."&nbsp;</B></FONT></td>
			</tr>
			<tr ALIGN='CENTER'>
			<td width='9%'> <FONT COLOR='RED'>$targetname</td>
			<td width='12%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_energy_left * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
			<td width='17%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_fighters_left2 * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
			<td width='18%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_shields_left * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
			<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_torps_left * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
			<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_armor_left * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
			</tr>";

		echo "			<tr><td colspan=6 align=center>&nbsp;</td></tr>
			</table>
			</CENTER>
	";

		echo "</center>";


		$armour_lost=$shipinfo['armour_pts']-$attacker_armor_left;
		$fighters_lost=$shipinfo['fighters']-$attacker_fighters_left2;
		$torps_lost=$shipinfo['torps']-$attacker_torps_left;
		$energy_lost=$attackerenergyset - ($attacker_energy_left + $attacker_shields_left);

		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=GREATEST(armour_pts - $armour_lost, 0), energy=GREATEST(energy - $energy_lost, 0), fighters=GREATEST(fighters - $fighters_lost, 0), torps=GREATEST(torps - $torps_lost, 0) WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$armour_lost=$planetinfoarmour_pts-$target_armor_left;
		$fighters_lost=$planetinfofighters-$target_fighters_left2;
		$torps_lost=$planetinfotorps-$target_torps_left;
		$energy_lost=$targetenergyset - ($target_energy_left + ($target_shields_left * $planet_shield_multiplier));

		$debug_query = $db->Execute("UPDATE $dbtables[planets] SET armour_pts=GREATEST(armour_pts - $armour_lost, 0), energy=GREATEST(energy - $energy_lost, 0), fighters=GREATEST(fighters - $fighters_lost, 0), torps=GREATEST(torps - $torps_lost, 0) WHERE planet_id=$planetinfo[planet_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		if($shipsonplanet > 0)
		{
			$leftoverenergy = max(floor(($energy_lost - $planetinfo['energy']) / $shipsonplanet), 0);
			$leftoverfighters = max(floor(($fighters_lost - $planetinfo['fighters']) / $shipsonplanet), 0);
			$leftovertorps = max(floor(($torps_lost - $planetinfo['torps']) / $shipsonplanet), 0);
			for($i = 0; $i < $shipsonplanet; $i++)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST(energy - $leftoverenergy, 0), fighters=GREATEST(fighters - $leftoverfighters, 0), torps=GREATEST(torps - $leftovertorps, 0) WHERE ship_id=$shipsonplanetid[$i]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}

		if(($attacker_armor_left < 1 and $attacker_shields_left < 1) and ($target_armor_left < 1 and $target_shields_left < 1)){

			update_player_experience($playerinfo['player_id'], $losing_yourship);
			echo "<BR><BR><CENTER><font color=#ff0000>$l_cmb_planetnotdefeated $l_planet $targetname</font></b></CENTER><BR><BR>";

			$armour_lost=$planetinfoarmour_pts-$target_armor_left;
			$fighters_lost=$planetinfofighters-$target_fighters_left2;
			$torps_lost=$planetinfotorps-$target_torps_left;
			$energy_lost=$targetenergyset - ($attacker_energy_left + $attacker_shields_left);

			echo "<CENTER><font color=#00ff00>$targetname $l_att_lost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font></CENTER><BR><BR>";

			playerlog($planetinfo['owner'], LOG_PLANET_NOT_DEFEATED, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]|$free_ore|$free_organics|$free_goods|$ship_salvage_rate|$ship_salvage");
			gen_score($planetinfo['owner']);

			if($planetinfo['owner'] != 2)
			{
				$playernames = $playerinfo['character_name']."|".get_player($planetinfo['owner']);
				insert_news($playernames, 1, "planetnotdefeated");
			}

			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			//	attacker_died();
			echo "<CENTER><font color=#ff0000><b>$l_att_yshiplost</b></font></CENTER><BR><BR>";
			if ($shipinfo['dev_escapepod'] == "Y")
			{
				$rating=round($playerinfo['rating']/2);
				echo "<b><font color=#ffff00>$l_att_loosepod</font></b><BR><BR>";

				player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $rating, $targetinfo['player_id'], $targetinfo['rating']);

				if ($spy_success_factor)
				{
					spy_ship_destroyed($shipinfo['ship_id'],$targetinfo['player_id']);
				}

				if ($dig_success_factor)
				{
					dig_ship_destroyed($shipinfo['ship_id'],$targetinfo['player_id']);
				}

				$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
				db_op_result($debug_query,__LINE__,__FILE__);

				collect_bounty($targetinfo['player_id'],$playerinfo['player_id']);
			}
			else
			{
				db_kill_player($playerinfo['player_id'], $targetinfo['player_id'], $targetinfo['rating']);
				collect_bounty($targetinfo['player_id'],$playerinfo['player_id']);
			}

			TEXT_GOTOMAIN();

			include ("footer.php");
			die();
		}

		if(($attacker_armor_left < 1 and $attacker_shields_left < 1) or ($target_armor_left < 1 and $target_shields_left < 1)){

			if ($target_armor_left < 1 and $target_shields_left < 1)
			{
				//	target_died();
				update_player_experience($planetinfo['owner'], $losing_planet);
				echo "<BR><BR><CENTER><FONT COLOR='GREEN'><B>$l_cmb_planetdefeated</b></FONT></CENTER><BR><BR>";
				planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_DEFEATED);

				if ($min_value_capture != 0)
				{
					$planetplayerscore = gen_score($playerinfo['player_id']);
					$planetplayerscore = $planetplayerscore * $planetplayerscore;
		
					if ($planetplayerscore == 0) 
					{
						$planetplayerscore = 1;
					}				
				
				
					$playerscore = $planetplayerscore;

					$planetscore = $planetinfo['organics'] * $organics_price + $planetinfo['ore'] * $ore_price + $planetinfo['goods'] * $goods_price + $planetinfo['energy'] * $energy_price + $planetinfo['fighters'] * $fighter_price + $planetinfo['torps'] * $torpedo_price + $planetinfo['colonists'] * $colonist_price + $planetinfo['credits'];
					$planetscore = $planetscore * $min_value_capture / 100;

					if ($playerscore < $planetscore)
					{
						planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_PLANET_DESTRUCT);
						echo "<CENTER><b>$l_cmb_citizenswanttodie</b></CENTER><BR><BR>";
						if ($spy_success_factor)
						{
						   spy_planet_destroyed($planetinfo['planet_id']);
						}
						$db->Execute("DELETE FROM $dbtables[planets] WHERE planet_id=$planetinfo[planet_id]");
						playerlog($planetinfo['owner'], LOG_PLANET_DEFEATED_D, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
						adminlog(LOG_ADMIN_PLANETDEL, "$playerinfo[character_name]|$ownerinfo[character_name]|$shipinfo[sector_id]");
						gen_score($planetinfo['owner']);
						calc_ownership($planetinfo['sector_id']); // Doesnt seem to run otherwise - per SF BUG # 588421
						$debug_query = $db->Execute("DELETE from $dbtables[dignitary] WHERE planet_id = $planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("DELETE from $dbtables[autotrades] WHERE planet_id = $planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						if($planetinfo['owner'] != 2){
							$playernames = $playerinfo['character_name']."|".get_player($planetinfo['owner']);
							insert_news($playernames, 1, "planetdestroyed");
						}
					}
					else
					{
						if($planetinfo['owner'] != 0 && $planetinfo['owner'] != 2 && $planetinfo['owner'] != 3)
							update_player_experience($playerinfo['player_id'], $defeating_planet);
						if($auto_capture_planets != 1){
							echo "<CENTER><font color=red>$l_cmb_youmaycapture1";
							echo "<a href=planet_unowned_capture.php?planet_id=".$planetinfo['planet_id'].">";
							echo $l_cmb_youmaycapture2;
							echo "</a> ".$l_cmb_youmaycapture3;
							echo "</font></CENTER><BR><BR>";
						}
						playerlog($ownerinfo['player_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
						gen_score($ownerinfo['player_id']);
						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, base='N', defeated='Y' WHERE planet_id=$planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$debug_query = $db->Execute("DELETE from $dbtables[dignitary] WHERE planet_id = $planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("DELETE from $dbtables[autotrades] WHERE planet_id = $planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						$old_owner_id = $planetinfo['owner'];
						if ($spy_success_factor)
						{
						   change_planet_ownership($planetinfo['planet_id'],$old_owner_id,0);
						}
						if($ownerinfo['player_id'] != 2){
							$playernames = $playerinfo['character_name']."|".get_player($old_owner_id);
							insert_news($playernames, 1, "planetdefeated");
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET planets_lost=planets_lost+1 WHERE player_id=$old_owner_id");
							db_op_result($debug_query,__LINE__,__FILE__);
						}
						if($auto_capture_planets == 1){
							echo "<CENTER><FONT COLOR='GREEN'><B>$l_planet_captured</b></font></center><BR>";
							if ($spy_success_factor)
							{
								change_planet_ownership($planetinfo['planet_id'], 0, $playerinfo['player_id']);
							}

							$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, team=null, owner=$playerinfo[player_id], base='N', defeated='N' WHERE planet_id=$planetinfo[planet_id]");
							db_op_result($debug_query,__LINE__,__FILE__);

							$ownership = calc_ownership($shipinfo['sector_id']);

							if (!empty($ownership))
								echo "<CENTER><FONT COLOR='YELLOW'><B>$ownership</b></font></center><p>";

							$planetowner=$playerinfo['character_name'];

							planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_CAPTURE);
							playerlog($playerinfo['player_id'], LOG_PLANET_CAPTURED, "$planetinfo[colonists]|$planetinfo[credits]|$planetowner");

							if($planetinfo['team'] != $playerinfo['team'] and $playerinfo['team'] != 0){
								$debug_query = $db->Execute("UPDATE $dbtables[players] SET captures=captures+1 WHERE player_id=$playerinfo[player_id]");
								db_op_result($debug_query,__LINE__,__FILE__);
							}
						}
					}
				}
				else
				{
					if($planetinfo['owner'] != 0 && $planetinfo['owner'] != 2 && $planetinfo['owner'] != 3)
						update_player_experience($playerinfo['player_id'], $defeating_planet);
					if($auto_capture_planets != 1){
						echo "<CENTER><font color=red>$l_cmb_youmaycapture1";
						echo "<a href=planet_unowned_capture.php?planet_id=".$planetinfo['planet_id'].">";
						echo $l_cmb_youmaycapture2;
						echo "</a> ".$l_cmb_youmaycapture3;
						echo "</font></CENTER><BR><BR>";
					}
					playerlog($ownerinfo['player_id'], LOG_PLANET_DEFEATED, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
					gen_score($ownerinfo['player_id']);
					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, base='N', defeated='Y' WHERE planet_id=$planetinfo[planet_id]");
					db_op_result($debug_query,__LINE__,__FILE__);

					$debug_query = $db->Execute("DELETE from $dbtables[dignitary] WHERE planet_id = $planetinfo[planet_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
					$debug_query = $db->Execute("DELETE from $dbtables[autotrades] WHERE planet_id = $planetinfo[planet_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
					$old_owner_id = $planetinfo['owner'];
					if ($spy_success_factor)
					{
					   change_planet_ownership($planetinfo['planet_id'],$old_owner_id,0);
					}
					if($ownerinfo['player_id'] != 2){
						$playernames = $playerinfo['character_name']."|".get_player($old_owner_id);
						insert_news($playernames, 1, "planetdefeated");
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET planets_lost=planets_lost+1 WHERE player_id=$old_owner_id");
						db_op_result($debug_query,__LINE__,__FILE__);
					}
					if($auto_capture_planets == 1){
						echo "<CENTER><FONT COLOR='GREEN'><B>$l_planet_captured</b></font></center><BR>";
						if ($spy_success_factor)
						{
							change_planet_ownership($planetinfo['planet_id'], 0, $playerinfo['player_id']);
						}

						$debug_query = $db->Execute("UPDATE $dbtables[planets] SET cargo_hull = 0, cargo_power = 0, team=null, owner=$playerinfo[player_id], base='N', defeated='N' WHERE planet_id=$planetinfo[planet_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$ownership = calc_ownership($shipinfo['sector_id']);

						if (!empty($ownership))
							echo "<CENTER><FONT COLOR='YELLOW'><B>$ownership</b></font></center><p>";

						$planetowner=$playerinfo['character_name'];

						planet_log($planetinfo['planet_id'],$planetinfo['owner'],$playerinfo['player_id'],PLOG_CAPTURE);
						playerlog($playerinfo['player_id'], LOG_PLANET_CAPTURED, "$planetinfo[colonists]|$planetinfo[credits]|$planetowner");

						if($planetinfo['team'] != $playerinfo['team'] and $playerinfo['team'] != 0){
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET captures=captures+1 WHERE player_id=$playerinfo[player_id]");
							db_op_result($debug_query,__LINE__,__FILE__);
						}
					}
				}

				$result4 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planetinfo[planet_id] AND on_planet='Y'");
				$shipsonplanet = $result4->RecordCount();

				if ($shipsonplanet > 0)
				{
					$l_cmb_shipdock2 = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock2);
					echo "<BR><BR><CENTER><b>$l_cmb_shipdock2</b></CENTER><BR><BR>\n";

					while (!$result4->EOF)
					{
						$onplanet = $result4->fields;

						$ship_id = stripnum($onplanet['ship_id']);

						$result2 = $db->Execute ("SELECT * FROM $dbtables[players] WHERE currentship=$onplanet[ship_id]");
						$targetinfo = $result2->fields;

						$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE ship_id='$ship_id'");
						$targetship = $result->fields;

						// determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak 
						$targetcloak = floor($targetship['cloak'] * 0.75);

						$success = (10 - $targetcloak + $shipinfo['sensors']) * 5;
						if ($success < 5)
						{
							$success = 5;
						}

						if ($success > 95)
						{
							$success = 95;
						}

						$targetengines = floor($targetship['engines'] * 0.50);

						$flee = (10 - $targetengines + $shipinfo['engines']) * 5;
						$roll = mt_rand(1, 100);
						$roll2 = mt_rand(1, 100);

						if ($flee < $roll2)
						{
							echo "$l_att_flee<BR><BR>";
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
							db_op_result($debug_query,__LINE__,__FILE__);
							playerlog($targetinfo['player_id'], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
							$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$ship_id");
							db_op_result($debug_query,__LINE__,__FILE__);
						}else if ($roll > $success)
						{
							// if scan fails - inform both player and target. 
							echo "$l_planet_noscan<BR><BR>";
							$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
							db_op_result($debug_query,__LINE__,__FILE__);
							playerlog($targetinfo['player_id'], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
							$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$ship_id");
							db_op_result($debug_query,__LINE__,__FILE__);
						}
						else
						{
							// if scan succeeds, show results and inform target. 
							$shipavg = ($targetship['hull'] + $targetship['engines'] + $targetship['power'] + $targetship['computer'] + $targetship['sensors'] + $targetship['beams'] + $targetship['torp_launchers'] + $targetship['shields'] + $targetship['cloak'] + $targetship['armour'] + $targetship['ecm']) / 11;
							if ($shipavg > $ewd_maxavgtechlevel)
							{
								$chance = round($shipavg / 40) * 100;
							}
							else
							{
								$chance = 0;
							}

							$random_value = mt_rand(1,100);
							if ($targetship['dev_emerwarp'] > 0 && $random_value > $chance)
							{
								// need to change warp destination to random sector in universe 
								$rating_change=round($targetinfo['rating']*.1);
								$source_sector = $shipinfo['sector_id'];
								$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] where sg_sector != 1 and sector_id > 3");
								$totrecs=$findem->RecordCount(); 
								$getit=$findem->GetArray();
								$randplay=mt_rand(0,($totrecs-1));
								$dest_sector = $getit[$randplay]['sector_id'];

								$debug_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$source_sector");
								db_op_result($debug_query,__LINE__,__FILE__);
								$zones = $debug_query->fields;

								$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
								db_op_result($debug_query,__LINE__,__FILE__);

								playerlog($targetinfo['player_id'], LOG_ATTACK_EWD, "$playerinfo[character_name]");

								$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1,cleared_defences=' ', on_planet='N' WHERE ship_id=$ship_id");
								db_op_result($debug_query,__LINE__,__FILE__);

								log_move($targetinfo['player_id'],$targetship['ship_id'],$source_sector,$dest_sector,$shipinfo['class'],$shipinfo['cloak'],$zones['zone_id']);
								echo "$l_att_ewd<BR><BR>";
							}
							else
							{
								echo "<BR>$targetinfo[character_name]". $l_att_sdest ."<BR>";
								if ($targetship['dev_escapepod'] == "Y")
								{
									$rating=round($targetinfo['rating']/2);
									echo "$l_att_espod<BR><BR>";

									player_ship_destroyed($ship_id, $targetinfo['player_id'], $rating, $playerinfo['player_id'], $playerinfo['rating']);

									playerlog($targetinfo['player_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
									///
									if ($spy_success_factor)
									{
										spy_ship_destroyed($targetship['ship_id'], $playerinfo['player_id']);
									}

								   if ($dig_success_factor)
								   {
									   dig_ship_destroyed($targetship['ship_id'], $playerinfo['player_id']);
								   }

									$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $targetship[ship_id] and active='P'"); 
									db_op_result($debug_query,__LINE__,__FILE__);

									collect_bounty($playerinfo['player_id'],$targetinfo['player_id']);
								}
								else
								{
									playerlog($targetinfo['player_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
									db_kill_player($targetinfo['player_id'], $playerinfo['player_id'], $playerinfo['rating']);
									collect_bounty($playerinfo['player_id'],$targetinfo['player_id']);
								}
							}
						}
						$result4->MoveNext();
					}
				}

				if($planetinfoarmour_pts > 0)
					calc_internal_damage($planetinfo['planet_id'], 1, ($planetinfoarmour_pts-$target_armor_left) / $planetinfoarmour_pts);
				$armour_lost=$planetinfoarmour_pts-$target_armor_left;
				$fighters_lost=$planetinfofighters-$target_fighters_left2;
				$torps_lost=$planetinfotorps-$target_torps_left;
				$energy_lost=$targetenergyset - ($attacker_energy_left + $attacker_shields_left);

				echo "<CENTER><font color=#00ff00>$targetname $l_att_lost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font></CENTER><BR><BR>";
			}
			else
			{
				echo "<BR><BR><CENTER><font color=#ff0000>$l_cmb_planetnotdefeated $l_planet $targetname</font></b></CENTER><BR><BR>";

				if($planetinfoarmour_pts > 0)
					calc_internal_damage($planetinfo['planet_id'], 1, ($planetinfoarmour_pts-$target_armor_left) / $planetinfoarmour_pts);
				$armour_lost=$planetinfoarmour_pts-$target_armor_left;
				$fighters_lost=$planetinfofighters-$target_fighters_left2;
				$torps_lost=$planetinfotorps-$target_torps_left;
				$energy_lost=$targetenergyset - ($attacker_energy_left + $attacker_shields_left);

				playerlog($planetinfo['owner'], LOG_ATTACKED_WIN, "$playerinfo[character_name]|$armour_lost|$fighters_lost");
				echo "<CENTER><font color=#00ff00>$targetname $l_att_lost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font></CENTER><BR><BR>";
			}

			if ($attacker_armor_left < 1 and $attacker_shields_left < 1)
			{
				//	attacker_died();
				update_player_experience($targetinfo['player_id'], $destroying_enemyship);
				update_player_experience($playerinfo['player_id'], $losing_yourship);
				echo "<CENTER><font color=#ff0000><b>$l_att_yshiplost</b></font></CENTER><BR><BR>";
				if ($shipinfo['dev_escapepod'] == "Y")
				{
					$rating=round($playerinfo['rating']/2);
					echo "<CENTER><b><font color=#ffff00>$l_att_loosepod</font></b></CENTER><BR><BR>";

					player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $rating, $targetinfo['player_id'], $targetinfo['rating']);

					if ($spy_success_factor)
					{
						spy_ship_destroyed($shipinfo['ship_id'],$targetinfo['player_id']);
					}

					if ($dig_success_factor)
					{
						dig_ship_destroyed($shipinfo['ship_id'],$targetinfo['player_id']);
					}

					$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
					db_op_result($debug_query,__LINE__,__FILE__);

					collect_bounty($targetinfo['player_id'],$playerinfo['player_id']);
				}
				else
				{
					db_kill_player($playerinfo['player_id'], $targetinfo['player_id'], $targetinfo['rating']);
					collect_bounty($targetinfo['player_id'],$playerinfo['player_id']);
				}

				$free_ore = round($shipinfo['ore']/2);
				$free_organics = round($shipinfo['organics']/2);
				$free_goods = round($shipinfo['goods']/2);
				$free_holds = NUM_HOLDS($targetship['hull']) - $targetship['ore'] - $targetship['organics'] - $targetship['goods'] - $targetship['colonists'];
				if ($free_holds > $free_goods)
				{
					$salv_goods=$free_goods;
					$free_holds=$free_holds-$free_goods;
				}
				elseif ($free_holds > 0)
				{
					$salv_goods=$free_holds;
					$free_holds=0;
				}
				else
				{
					$salv_goods=0;
				}
				if ($free_holds > $free_ore)
				{
					$salv_ore=$free_ore;
					$free_holds=$free_holds-$free_ore;
				}
				elseif ($free_holds > 0)
				{
					$salv_ore=$free_holds;
					$free_holds=0;
				}
				else
				{
					$salv_ore=0;
				}
				if ($free_holds > $free_organics)
				{
					$salv_organics=$free_organics;
					$free_holds=$free_holds-$free_organics;
				}
				elseif ($free_holds > 0)
				{
					$salv_organics=$free_holds;
					$free_holds=0;
				}
				else
				{
					$salv_organics=0;
				}

				$ship_value=$upgrade_cost*(round(mypw($upgrade_factor, $shipinfo['hull']))+round(mypw($upgrade_factor, $shipinfo['engines']))+round(mypw($upgrade_factor, $shipinfo['power']))+round(mypw($upgrade_factor, $shipinfo['computer']))+round(mypw($upgrade_factor, $shipinfo['sensors']))+round(mypw($upgrade_factor, $shipinfo['beams']))+round(mypw($upgrade_factor, $shipinfo['torp_launchers']))+round(mypw($upgrade_factor, $shipinfo['shields']))+round(mypw($upgrade_factor, $shipinfo['armour']))+round(mypw($upgrade_factor, $shipinfo['cloak']))+round(mypw($upgrade_factor, $shipinfo['ecm'])));
				$ship_salvage_rate = mt_rand(10,20);
				$ship_salvage=$ship_value*$ship_salvage_rate/100;

				$l_att_salv=str_replace("[salv_ore]","<font color=#ffffff>". NUMBER($salv_ore) . "</font>",$l_att_salv);
				$l_att_salv=str_replace("[salv_organics]","<font color=#ffffff>". NUMBER($salv_organics) . "</font>",$l_att_salv);
				$l_att_salv=str_replace("[salv_goods]","<font color=#ffffff>". NUMBER($salv_goods) . "</font>",$l_att_salv);
				$l_att_salv=str_replace("[ship_salvage_rate]","<font color=#ffffff>". $ship_salvage_rate . "</font>",$l_att_salv);
				$l_att_salv=str_replace("[ship_salvage]","<font color=#ffffff>". NUMBER($ship_salvage) . "</font>",$l_att_salv);
				$l_att_salv=str_replace("[name]","<font color=#00ffff>". $targetinfo['character_name'] . "</font>",$l_att_salv);

				echo "<CENTER><font color=#00ff00>$l_att_salv</font><BR></CENTER>";

				$debug_query = $db->Execute ("UPDATE $dbtables[planets] SET credits=credits+$ship_salvage, ore=ore+$salv_ore, organics=organics+$salv_organics, goods=goods+$salv_goods WHERE planet_id=$planetinfo[planet_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				$armour_lost=$shipinfo['armour_pts']-$attacker_armor_left;
				$fighters_lost=$shipinfo['fighters']-$attacker_fighters_left2;
				$torps_lost=$shipinfo['torps']-$attacker_torps_left;
				$energy_lost=$attackerenergyset - ($attacker_energy_left + $attacker_shields_left);

				echo "<CENTER><font color=#00ff00>$l_att_ylost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font><BR><BR></CENTER>";
			}
			else
			{
				if($attacker_armor_left > 0)
					calc_internal_damage($shipinfo['ship_id'], 0, ($shipinfo['armour_pts']-$attacker_armor_left) / $shipinfo['armour_pts']);
				$armour_lost=$shipinfo['armour_pts']-$attacker_armor_left;
				$fighters_lost=$shipinfo['fighters']-$attacker_fighters_left2;
				$torps_lost=$shipinfo['torps']-$attacker_torps_left;
				$energy_lost=$attackerenergyset - ($attacker_energy_left + $attacker_shields_left);

				$rating_change=round($targetinfo['rating']*.1);
				$debug_query = $db->Execute ("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);

				echo "<CENTER><font color=#00ff00>$l_att_ylost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font><BR><BR></CENTER>";
			}
			TEXT_GOTOMAIN();

			include ("footer.php");
			die();
		}


		// both players bounced

		echo "<BR><BR><CENTER><font color=#ff0000>$l_cmb_planetnotdefeated $l_planet $targetname</font></b></CENTER><BR><BR>";

		if($planetinfoarmour_pts > 0)
			calc_internal_damage($planetinfo['planet_id'], 1, ($planetinfoarmour_pts-$target_armor_left) / $planetinfoarmour_pts);
		$armour_lost=$planetinfoarmour_pts-$target_armor_left;
		$fighters_lost=$planetinfofighters-$target_fighters_left2;
		$torps_lost=$planetinfotorps-$target_torps_left;
		$energy_lost=$targetenergyset - ($target_energy_left + $target_shields_left);

		playerlog($planetinfo['owner'], LOG_PLANET_BOMBED, "$targetname|$shipinfo[sector_id]|$playerinfo[character_name]|$energy_lost|$torps_lost|$fighters_lost");
		echo "<CENTER><font color=#00ff00>$targetname $l_att_lost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font></CENTER><BR><BR>";

		if($attacker_armor_left > 0)
			calc_internal_damage($shipinfo['ship_id'], 0, ($shipinfo['armour_pts']-$attacker_armor_left) / $shipinfo['armour_pts']);
		$armour_lost=$shipinfo['armour_pts']-$attacker_armor_left;
		$fighters_lost=$shipinfo['fighters']-$attacker_fighters_left2;
		$torps_lost=$shipinfo['torps']-$attacker_torps_left;
		$energy_lost=$attackerenergyset - ($attacker_energy_left + $attacker_shields_left);

		$rating_change=round($targetinfo['rating']*.1);
		$debug_query = $db->Execute ("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1, rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "<CENTER><font color=#00ff00>$l_att_ylost <font color=#ffffff>". NUMBER($armour_lost) . "</font> $l_armourpts, <font color=#ffffff>" . NUMBER($fighters_lost) . "</font> $l_fighters, $l_att_andused <font color=#ffffff>". NUMBER($energy_lost) . "</font> $l_energy, <font color=#ffffff>" . NUMBER($torps_lost) . "</font> $l_torps.</font></CENTER><BR><BR></CENTER>";

		echo "<br><br>";

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
else
	{
		echo "$l_command_no<BR>";
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
}

close_database();
?>