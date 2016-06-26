<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_unowned_attackpreview.php

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

	//
	//
	// Preview of possible attack results
	//
	//

	if ($planetinfo['owner'] != 3)
	{

		echo "<p align=\"center\"><font color=\"Yellow\" size=\"3\"><b>$l_planet_combatpreview</b></font></p>";
		if ($playerinfo['turns'] < 1)
		{
			echo "$l_cmb_atleastoneturn<BR><BR>";
			TEXT_GOTOMAIN();
			include ("footer.php");
			die();
		}

		$result2 = $db->Execute ("SELECT * FROM $dbtables[players] WHERE player_id='$planetinfo[owner]'");
		$targetinfo = $result2->fields;

		$sc_error= SCAN_ERROR($shipinfo['sensors'], $planetinfo['jammer']);
		$sc_error_plus=100;
		if ($sc_error < 100){
			$sc_error_plus=115;
		}

		// get attacker beam, shield and armor values
		$attacker_shield_energy = floor($shipinfo['energy'] * 0.4);
		$attacker_beam_energy = $shipinfo['energy'] - $attacker_shield_energy;

		$attackershields = NUM_SHIELDS($shipinfo['shields']);

		if ($attackershields < $attacker_shield_energy)
		{
			$attacker_shield_energy = $attackershields;
		}

		$attacker_shield_hit_pts = $attacker_shield_energy * $ship_shield_hit_pts;
		$attacker_armor_hit_pts = $shipinfo['armour_pts'] * $ship_armor_hit_pts;

		$attackerbeams = NUM_BEAMS($shipinfo['beams']);

		if ($attackerbeams < $attacker_beam_energy)
		{
			$attacker_beam_energy = $attackerbeams;
		}

		$attack_beamtofighter_dmg = floor($attacker_beam_energy * 0.05);
		$attack_beamtotorp_dmg = floor($attacker_beam_energy * 0.025);
		$attacker_beam_energy_dmg = ($attacker_beam_energy - floor($attacker_beam_energy * 0.1) - floor($attacker_beam_energy * 0.1)) * $beam_damage_shields;

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
		while (!$res->EOF)
		{
			$targetshields += NUM_SHIELDS($res->fields['shields']);
			$res->MoveNext();
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

		$target_shield_hit_pts = $target_shield_energy * $ship_shield_hit_pts;

		$target_armor_hit_pts = $planetinfo['armour_pts'] * $ship_armor_hit_pts;

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

		$target_beamtofighter_dmg = floor($target_beam_energy * 0.05);
		$target_beamtotorp_dmg = floor($target_beam_energy * 0.025);
		$target_beam_energy_dmg = ($target_beam_energy - floor($target_beam_energy * 0.1) - floor($target_beam_energy * 0.1)) * $beam_damage_shields;

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
			$target_beam_energy=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$targetfighters=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$target_shield_energy=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$targettorps=0;
		}
		$roll = mt_rand(1, 100);
		if ($roll > $success)
		{
			$targetarmour=0;
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
		<td width='12%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_beam_energy * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='17%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($targetfighters * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='18%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($target_shield_energy * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($targettorps * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		<td width='11%'><FONT COLOR='RED'><B>&nbsp;".NUMBER(round($targetarmour * (mt_rand($sc_error , $sc_error_plus) / 100)))."&nbsp;</B></FONT></td>
		</tr>
		<tr><td colspan=6 align=center>&nbsp;</td></tr>
		</table>
		</CENTER>
";

		if($planetinfo['owner'] != 3){
			$l_planet_att_link="<a href='planet_unowned_attack.php?planet_id=$planet_id'>" . $l_planet_att_link ."</a>";
			$l_planet_att=str_replace("[attack]",$l_planet_att_link,$l_planet_att);
		}
		$l_planet_scn_link="<a href='planet_scan.php?planet_id=$planet_id'>" . $l_planet_scn_link ."</a>";
		$l_planet_scn=str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);
		echo "$l_planet_att <b>$l_planet_att_sure</b><p>";
		echo "$l_planet_scn<BR>";

		if ($sofa_on and $planetinfo['owner'] != 3) echo "<a href='planet_unowned_sofa.php?planet_id=$planet_id'>$l_sofa</a><BR>";

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