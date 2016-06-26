<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: combat_sector_fighters.php

if (preg_match("/combat_sector_fighters.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

include ("languages/$langdir/lang_attack.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_sector_fighters.inc");

function destroy_fighters($sector, $num_fighters)
{
	global $db, $dbtables;

	$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type ='F' order by quantity ASC");
	db_op_result($result3,__LINE__,__FILE__);

	//Put the defence information into the array "defenceinfo"
	if ($result3 > 0)
	{
		while (!$result3->EOF && $num_fighters > 0)
		{
			$row=$result3->fields;
			if ($row['quantity'] > $num_fighters)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity - $num_fighters where defence_id = $row[defence_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$num_fighters = 0;
			}
			else
			{
				$debug_query = $db->Execute("DELETE FROM $dbtables[sector_defence] WHERE defence_id = $row[defence_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$num_fighters -= $row['quantity'];
			}

			$result3->MoveNext();
		}
	}
}

$title = $l_sf_attacking;
bigtitle();

if ($move_type == 'real')
{
	$shipinfo['energy'] += $energy_collected;
}

///  start new combat

// get target beam, shield and armor values

$attacker_armor_left = $shipinfo['armour_pts'];
$attacker_torps_left = $shipinfo['torps'];
$attacker_fighters_left = $shipinfo['fighters'];

$target_torps_left = $total_sector_mines;
$target_fighters_left = $total_sector_fighters;

echo "
		<CENTER>
		<table width='75%' border='0' bgcolor=\"#000000\">
		<tr><td colspan=6 align=center><hr></td></tr>
		<tr ALIGN='CENTER'>
		<td width='25%' height='27'></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_fighters</FONT></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_torps</FONT></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_armor</FONT></td>
		</tr>
		<tr ALIGN='CENTER'>
		<td width='25%'> <FONT COLOR='yellow'><B>$l_cmb_you</B></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_fighters_left)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_torps_left)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_armor_left)."&nbsp;</B></FONT></td></tr><tr ALIGN='CENTER'>
		<td width='25%'> <FONT COLOR='yellow'><B>Sector Defense</B></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($target_fighters_left)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($target_torps_left)."&nbsp;</B></FONT></td><td width='25%'>&nbsp;</td>
		</tr>";

echo "	</tr>
		<tr><td colspan=6 align=center>&nbsp;</td></tr>
		</table>
		</CENTER>
";

// get planet sensors
$highcloak = 0;
$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$destination' order by sensors DESC");
db_op_result($result4,__LINE__,__FILE__);
$planets = $result4->fields;
if ($highcloak < $planets['cloak']){
	$highcloak=$planets['cloak'] + $basedefense;
}

$highsensor = 0;
$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$destination' order by sensors DESC");
db_op_result($result4,__LINE__,__FILE__);
$planets = $result4->fields;
if ($highsensor < $planets['sensors']){
	$highsensor=$planets['sensors'] + $basedefense;
}

$highcomputer = 0;
$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$destination' order by computer DESC");
db_op_result($result4,__LINE__,__FILE__);
$planets = $result4->fields;
$planetplayer = $planets['owner'];
if ($highcomputer < $planets['computer']){
	$highcomputer=$planets['computer'] + $basedefense;
}

$hightorps = 0;
$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$destination' order by torp_launchers DESC");
db_op_result($result4,__LINE__,__FILE__);
$planets = $result4->fields;
if ($hightorps < $planets['torp_launchers']){
	$hightorps=$planets['torp_launchers'] + $basedefense;
}

$attackerlowpercent = ecmcheck($highcloak, $shipinfo['sensors'], $attack_run_modifier);
$targetlowpercent = ecmcheck($shipinfo['ecm'], $highsensor, -$attack_run_modifier);

$targetshiptorp_launchers = $targetship['torp_launchers'];
$targetshipcomputer = $targetship['computer'];
$targetname = "Sector Defense";

if(!class_exists($shipinfo['computer_class'])){
	include ("class/" . $shipinfo['computer_class'] . ".inc");
}

$attackobject = new $shipinfo['computer_class']();
$fighter_damage_shields = $attackobject->fighter_damage_shields;
$fighter_damage_all = $attackobject->fighter_damage_all;
$fighter_hit_pts = $attackobject->fighter_hit_pts;

if(!class_exists($shipinfo['torp_class'])){
	include ("class/" . $shipinfo['torp_class'] . ".inc");
}

$attackobject = new $shipinfo['torp_class']();
$torp_damage_shields = $attackobject->torp_damage_shields;
$torp_damage_all = $attackobject->torp_damage_all;
$torp_hit_pts = $attackobject->torp_hit_pts;

if(!class_exists($shipinfo['armor_class'])){
	include ("class/" . $shipinfo['armor_class'] . ".inc");
}

$attackobject = new $shipinfo['armor_class']();
$ship_armor_hit_pts = $attackobject->ship_armor_hit_pts;

if(!class_exists("Basic_Computer")){
	include ("class/Basic_Computer.inc");
}

$targetobject = new Basic_Computer();
$fighter_damage_shields = $targetobject->fighter_damage_shields;
$fighter_damage_all = $targetobject->fighter_damage_all;
$fighter_hit_pts = $targetobject->fighter_hit_pts;

if(!class_exists("Basic_Torpedo")){
	include ("class/Basic_Torpedo.inc");
}

$targetobject = new Basic_Torpedo();
$torp_damage_shields = $targetobject->torp_damage_shields;
$torp_damage_all = $targetobject->torp_damage_all;
$torp_hit_pts = $targetobject->torp_hit_pts;

// The closer the number of sector fighters get to what is needed to possibly destroy the attacking
// ship the less effective the attacking ship fighters and torps.  The attacker will then be required
// to do a normal attack because A&R will have become useless.
$max_fighters = max(NUM_FIGHTERS($shipinfo['computer']), $attacker_fighters_left);
$max_torps = max(NUM_TORPEDOES($shipinfo['torp_launchers']), $attacker_torps_left);
$max_armor = max(NUM_ARMOUR($shipinfo['armour']), $attacker_armor_left);

$low_end_scale = round((($max_fighters * $fighter_hit_pts) + ($max_torps * $torp_hit_pts) + ($max_armor * $ship_armor_hit_pts)) / $fighter_damage_all);

if($low_end_scale < $target_fighters_left)
{
	$attacker_ratio = 1 - ($low_end_scale / $target_fighters_left);
	if($attacker_ratio <= 0.5)
	{
		$attacker_torps_left = round($attacker_torps_left * $attacker_ratio);
		$attacker_fighters_left = round($attacker_fighters_left * $attacker_ratio);
	}
}
else
{
	$attacker_torps_left = 0;
	$attacker_fighters_left = 0;
}

$result5 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=". $planetplayer . "");
$planet_owner = $result5->fields;

// If the attacker would get a bounty by attacking the most powerful planet in the sector then their A&R is useless.
$isfedbounty = planet_bounty_check($playerinfo, $destination, $planet_owner, 0);

if($isfedbounty > 0)
{
	$attacker_torps_left = 0;
	$attacker_fighters_left = 0;
}

// Stage 1 fighter to fighter Exchange

echo "<table width=\"75%\" border=\"1\" cellspacing=\"1\" cellpadding=\"4\" align=center bgcolor=\"#000000\">
<tr><td colspan=2 align=center><b><font  color=#00ff00>$l_att_fighters</font></b><tr><td width=50%>";

if($target_fighters_left != 0)
{
	$target_fighter_damage = calc_damage($target_fighters_left, $fighter_damage_all, $targetlowpercent, $highcomputer, $shipinfo['computer']);

	if($target_fighter_damage[2] == 100){
		echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font>$l_sf_fnoattack</b></font><br><br>";
	}

	$attack_fighter_hit_pts = $attacker_fighters_left * $fighter_hit_pts;
	if($target_fighter_damage[0] > $attack_fighter_hit_pts)
	{
		$target_fighter_damage[0] = $target_fighter_damage[0] - $attack_fighter_hit_pts;
		if($attacker_fighters_left > 0)
			echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($attacker_fighters_left) . "</font> $l_att_yfhit</b></font><br>";
		echo "<br><font color='#ff0000' ><b>$l_att_ylostf</b></font><br><br>";
		$attacker_fighters_left2 = 0;
	}
	else
	{
		$attack_fighter_hit_pts = $attack_fighter_hit_pts - $target_fighter_damage[0];
		$attack_fighters_used = floor($target_fighter_damage[0] / $fighter_hit_pts);
		echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($attack_fighters_used) . "</font> $l_att_yfhit</b></font><br>";
		$attacker_fighters_left2 = $attacker_fighters_left - $attack_fighters_used;
	}
}
else
{
	echo "<br><b><font color='#ff0000'><font color=white>" . $targetname . "</font> $l_att_tfnoattack</font><b><br><br>";
	$attacker_fighters_left2 = $attacker_fighters_left;
}

echo "</td><td width=50%>";

if($attacker_fighters_left != 0)
{
	$attack_fighter_damage = calc_damage($attacker_fighters_left, $fighter_damage_all, $attackerlowpercent, $shipinfo['computer'], $highcomputer);

	if($attack_fighter_damage[2] > 0){
		echo "<br><font color='#00ff00'><b><font color='#ff0000'>$l_sf_flaunchmalf1</font><br>$l_sf_flaunchmalf2<font color='#ffffff'>" . (100 - $attack_fighter_damage[2]) . "</font>$l_sf_flaunchmalf3</b></font><br><br>";
	}

	$target_fighter_hit_pts = $target_fighters_left * $fighter_hit_pts;
	if($attack_fighter_damage[0] > $target_fighter_hit_pts)
	{
		$attack_fighter_damage[0] = $attack_fighter_damage[0] - $target_fighter_hit_pts;
		if($target_fighters_left > 0)
			echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_fighters_left) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_efhit</b></font><br>";
		echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font> $l_att_lostf</b></font><br><br>";
		$target_fighters_left2 = 0;
	}
	else
	{
		$target_fighter_hit_pts = $target_fighter_hit_pts - $attack_fighter_damage[0];
		$target_fighters_used = floor($attack_fighter_damage[0] / $fighter_hit_pts);
		echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_fighters_used) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_efhit</b></font><br>";
		$target_fighters_left2 = $target_fighters_left - $target_fighters_used;
	}
}
else
{
	echo "<br><b><font color='#ff0000'>$l_att_anofighters</font><b><br><br>";
	$target_fighters_left2 = $target_fighters_left;
}

echo "</td></tr></table><br><br>";

// Stage 2 fighter to torp/mine Exchange torps

echo "<table width=\"75%\" border=\"1\" cellspacing=\"1\" cellpadding=\"4\" align=center bgcolor=\"#000000\">
<tr><td colspan=2 align=center><b><font  color=#00ff00>$l_sf_fightertorpexg</font></b><tr><td width=50%>";

if($target_fighters_left2 != 0)
{
	$target_fighter_damage = calc_damage($target_fighters_left2, $fighter_damage_all, $targetlowpercent, $highcomputer, $shipinfo['torp_launchers']);

	if($target_fighter_damage[2] == 100){
		echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font>$l_sf_fnoattackt</b></font><br><br>";
	}

	$attack_torp_hit_pts = $attacker_torps_left * $torp_hit_pts;
	if($target_fighter_damage[0] > $attack_torp_hit_pts)
	{
		$target_fighter_damage[0] = $target_fighter_damage[0] - $attack_torp_hit_pts;
		if($attacker_torps_left > 0)
			echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($attacker_torps_left) . "</font> $l_att_ythit</b></font><br>";
		echo "<br><font color='#ff0000' ><b>$l_att_ylostt</b></font><br><br>";
		$attacker_torps_left = 0;
	}
	else
	{
		$attack_torp_hit_pts = $attack_torp_hit_pts - $target_fighter_damage[0];
		$attack_fighters_used = floor($target_fighter_damage[0] / $torp_hit_pts);
		echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($attack_fighters_used) . "</font> $l_att_ythit</b></font><br>";
		$attacker_torps_left = $attacker_torps_left - $attack_fighters_used;
	}
}
else
{
	echo "<br><b><font color='#ff0000'><font color=white>" . $targetname . "</font> $l_att_tfnoattack</font><b><br><br>";
}

echo "</td><td width=50%>";

if($attacker_fighters_left2 != 0)
{
	$attack_fighter_damage = calc_damage($attacker_fighters_left2, $fighter_damage_all, $attackerlowpercent, $shipinfo['computer'], $hightorps);

	if($attack_fighter_damage[2] > 0){
		echo "<br><font color='#00ff00'><b><font color='#ff0000'>$l_sf_flaunchmalf1</font><br>$l_sf_flaunchmalf2<font color='#ffffff'>" . (100 - $attack_fighter_damage[2]) . "</font>$l_sf_flaunchmalf3</b></font><br><br>";
	}

	$target_torp_hit_pts = $target_torps_left * $torp_hit_pts;
	if($attack_fighter_damage[0] > $target_torp_hit_pts)
	{
		$attack_fighter_damage[0] = $attack_fighter_damage[0] - $target_torp_hit_pts;
		if($target_torps_left > 0)
			echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_torps_left) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_ethit</b></font><br>";
		echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font> $l_att_lostt</b></font><br><br>";
		$target_torps_left = 0;
	}
	else
	{
		$target_torp_hit_pts = $target_torp_hit_pts - $attack_fighter_damage[0];
		$target_fighters_used = floor($attack_fighter_damage[0] / $torp_hit_pts);
		echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_fighters_used) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_ethit</b></font><br>";
		$target_torps_left = $target_torps_left - $target_fighters_used;
	}
}
else
{
	echo "<br><b><font color='#ff0000'>$l_att_anofighters</font><b><br><br>";
}

echo "</td></tr></table><br><br>";

// Stage 3 ship torp to sector fighter Exchange

echo "<table width=\"75%\" border=\"1\" cellspacing=\"1\" cellpadding=\"4\" align=center bgcolor=\"#000000\">
<tr><td colspan=2 align=center><b><font  color=#00ff00>$l_sf_torpfighterexg</font></b><tr><td width=50%>";
echo "&nbsp;</td><td width=50%>";

if($attacker_torps_left != 0)
{
	$attack_torp_damage = calc_damage($attacker_torps_left, $torp_damage_all, $attackerlowpercent, $shipinfo['torp_launchers'], $highcomputer);

	if($attack_torp_damage[2] > 0){
		echo "<br><font color='#00ff00'><b><font color='#ff0000'>$l_sf_tlaunchmalf1</font><br>$l_sf_tlaunchmalf2<font color='#ffffff'>" . (100 - $attack_fighter_damage[2]) . "</font>$l_sf_tlaunchmalf3</b></font><br><br>";
	}

	$target_fighter_hit_pts = $target_fighters_left2 * $fighter_hit_pts;
	if($attack_torp_damage[0] > $target_fighter_hit_pts)
	{
		$attack_torp_damage[0] = $attack_torp_damage[0] - $target_fighter_hit_pts;
		if($target_fighters_left2 > 0)
			echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_fighters_left2) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_efhit</b></font><br>";
		echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font> $l_att_lostf</b></font><br><br>";
		$attacker_torps_left = floor($attack_torp_damage[0] / $torp_damage_all);
		$target_fighters_left = 0;
	}
	else
	{
		$target_fighter_hit_pts = $target_fighter_hit_pts - $attack_torp_damage[0];
		$target_fighters_used = floor($attack_torp_damage[0] / $fighter_hit_pts);
		echo "<font color='#00ff00'><b><FONT COLOR='yellow'>" . NUMBER($target_fighters_used) . "</font> $l_att_of <font color=white>" . $targetname . "</font>$l_att_efhit</b></font><br>";
		$target_fighters_left = $target_fighters_left2 - $target_fighters_used;
		$attacker_torps_left = 0;
	}
}
else
{
	echo "<br><b><font color='#ff0000'>$l_att_anotorps</font><b><br><br>";
	$target_fighters_left = $target_fighters_left2;
}

echo "</td></tr></table><br><br>";

// Stage 4 sector fighter to ship armor Exchange

//  Check to see if planet detects hit and run.
$success = max(min((((5 + $highsensor)-($shipinfo['cloak'])) * 10), 95), 5);

//echo "$success<br>";
 if (mt_rand(1, 100) < $success)
{
	echo "<table width=\"75%\" border=\"1\" cellspacing=\"1\" cellpadding=\"4\" align=center bgcolor=\"#000000\">
	<tr><td colspan=2 align=center><b><font  color=#00ff00>$l_sf_fighterarmorexg</font></b><tr><td width=50%>";

	if($target_fighters_left != 0)
	{
		$attackerlowpercent = ecmcheck($highcloak, $shipinfo['sensors'], $attack_run_modifier + floor(($shipinfo['engines'] - $highcomputer) * 1.8));
		$targetlowpercent = ecmcheck($shipinfo['ecm'], $highsensor, -($attack_run_modifier + floor(($shipinfo['engines'] - $highcomputer) * 1.8)));
//		echo "alp: $$attackerlowpercent[0] - tlp: $targetlowpercent[0]<br>";
//		echo "alp: $$attackerlowpercent[1] - tlp: $targetlowpercent[1]<br>";
		// we reverse the attack and target tech this will kick in the confusion
		// calculation and cause the sector defense to miss.  The higher the ships engine the more the sector defense will miss.
		$target_fighter_damage = calc_damage($target_fighters_left, $fighter_damage_all, $targetlowpercent, $shipinfo['engines'], $highcomputer);

		if($target_fighter_damage[2] == 100){
			echo "<br><font color='#ff0000' ><b><font color=white >" . $targetname . "</font>$l_sf_fnoattacka</b></font><br><br>";
		}

		$attack_armor_hit_pts = $attacker_armor_left * $ship_armor_hit_pts;
		$ratio = $target_fighter_damage[0] / ($target_fighters_left * $fighter_damage_all);

		// How much damage
		$ddamageroll=mt_rand(1,13);
		if($ddamageroll == 13){
			$ratio = $ratio * 2;
		}
		if($ddamageroll == 12){
			$ratio = $ratio * 1.5;
		}
		if($ddamageroll == 11){
			$ratio = $ratio * 1.25;
		}
		if($ddamageroll == 10){
			$ratio = $ratio;
		}
		if($ddamageroll == 9){
			$ratio = $ratio * 0.9;
		}
		if($ddamageroll == 8){
			$ratio = $ratio * 0.8;
		}
		if($ddamageroll == 7){
			$ratio = $ratio * 0.7;
		}
		if($ddamageroll == 6){
			$ratio = $ratio * 0.5;
		}
		if($ddamageroll == 5){
			$ratio = $ratio * 0.5;
		}
		if($ddamageroll == 4){
			$ratio = $ratio * 0.4;
		}
		if($ddamageroll == 3){
			$ratio = $ratio * 0.3;
		}
		if($ddamageroll == 2){
			$ratio = $ratio * 0.2;
		}
		if($ddamageroll == 1){
			$ratio = $ratio * 0.1;
		}

//echo "max: " . $target_fighters_left * $fighter_damage_all . " tfd: " . $target_fighter_damage[0] . " aahp: $attack_armor_hit_pts per: " . $ratio . " damage: " . ($attack_armor_hit_pts * $ratio) / $ship_armor_hit_pts . "<br>";
		$target_fighter_damage[0] = floor($attack_armor_hit_pts * $ratio);
		if($target_fighter_damage[0] > $attack_armor_hit_pts)
		{
			$target_fighter_damage[0] = $target_fighter_damage[0] - $attack_armor_hit_pts;
			if($attacker_armor_left > 0)
				echo "<font color='#00ff00'><b>$l_att_ayhit <FONT COLOR='yellow'>" . NUMBER($attacker_armor_left) . "</font> $l_att_dmg.</b></font><br>";
			echo "<br><font color='#ff0000' ><b>$l_att_yarm</b></font><br><br>";
			$attacker_armor_left = 0;
		}
		else
		{
			$attack_armor_hit_pts = $attack_armor_hit_pts - $target_fighter_damage[0];
			$attacker_armor_used = $attacker_armor_left - floor($attack_armor_hit_pts / $ship_armor_hit_pts);
			$attacker_armor_left = floor($attack_armor_hit_pts / $ship_armor_hit_pts);
			echo "<font color='#00ff00'><b>$l_att_ayhit <FONT COLOR='yellow'>" . NUMBER($attacker_armor_used) . "</font> $l_att_dmg.</b></font><br>";
		}
	}
	else
	{
		echo "<br><b><font color='#ff0000'><font color=white>" . $targetname . "</font> $l_att_tfnoattack</font><b><br><br>";
	}
	echo "</td><td width=50%>&nbsp;";

	echo "</td></tr></table>";
	/// end new combat
}

echo "
		<CENTER>
		<table width='75%' border='0' bgcolor=\"#000000\">
		<tr><td colspan=6 align=center><hr></td></tr>
		<tr ALIGN='CENTER'>
		<td width='25%' height='27'></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_fighters</FONT></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_torps</FONT></td>
		<td width='25%' height='27'><FONT COLOR='WHITE'>$l_cmb_armor</FONT></td>
		</tr>
		<tr ALIGN='CENTER'>
		<td width='25%'> <FONT COLOR='yellow'><B>$l_cmb_you</B></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_fighters_left2)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_torps_left)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($attacker_armor_left)."&nbsp;</B></FONT></td></tr><tr ALIGN='CENTER'>
		<td width='25%'> <FONT COLOR='yellow'><B>Sector Defense</B></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($target_fighters_left)."&nbsp;</B></FONT></td>
		<td width='25%'><FONT COLOR='RED'><B>&nbsp;".NUMBER($target_torps_left)."&nbsp;</B></FONT></td><td width='25%'>&nbsp;</td>
		</tr>";

echo "	</tr>
		<tr><td colspan=6 align=center>&nbsp;</td></tr>
		</table>
		</CENTER>
";

$total_sector_mines_lost = $total_sector_mines - $target_torps_left;
$total_sector_mines = $target_torps_left;
explode_mines($destination,$total_sector_mines_lost);
$fighterslost = $total_sector_fighters - $target_fighters_left;

$l_sf_sendlog = str_replace("[player]", $playerinfo['character_name'], $l_sf_sendlog);
$l_sf_sendlog = str_replace("[lost]", NUMBER($fighterslost), $l_sf_sendlog);
$l_sf_sendlog = str_replace("[sector]", $destination, $l_sf_sendlog);
				 
message_defence_owner($destination,$l_sf_sendlog);
destroy_fighters($destination,$fighterslost);
playerlog($playerinfo['player_id'], LOG_DEFS_DESTROYED_F, "$fighterslost|$destination");
$armour_lost = $shipinfo['armour_pts'] - $attacker_armor_left;
$fighters_lost = $shipinfo['fighters'] - $attacker_fighters_left2;
$playertorpnum = $shipinfo['torps'] - $attacker_torps_left;
calc_internal_damage($shipinfo['ship_id'], 0, $armour_lost / $shipinfo['armour_pts']);
$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET fighters=$attacker_fighters_left2, armour_pts=$attacker_armor_left, torps=$attacker_torps_left WHERE ship_id=$shipinfo[ship_id]");
db_op_result($debug_query,__LINE__,__FILE__);

$l_sf_lreport = str_replace("[armor]", "<font color=#ffffff>" . NUMBER($armour_lost) . "</font>", $l_sf_lreport);
$l_sf_lreport = str_replace("[fighters]", "<font color=#ffffff>" . NUMBER($fighters_lost) . "</font>", $l_sf_lreport);
$l_sf_lreport = str_replace("[torps]", "<font color=#ffffff>" . NUMBER($playertorpnum) . "</font>", $l_sf_lreport);

echo "<table align=\"center\"><tr><td><font color=\"#ff0000\"><b>$l_sf_lreport<b></font></td></tr></table>";

if ($attacker_armor_left < 1)
{
	echo $l_sf_shipdestroyed;
	playerlog($playerinfo['player_id'], LOG_DEFS_KABOOM, "$sector|$shipinfo[dev_escapepod]");
	$l_sf_sendlog2 = str_replace("[player]", $playerinfo['character_name'], $l_sf_sendlog2);
	$l_sf_sendlog2 = str_replace("[sector]", $sector, $l_sf_sendlog2);
	message_defence_owner($sector,$l_sf_sendlog2);
	if ($shipinfo['dev_escapepod'] == "Y")
	{
		$rating = round($playerinfo['rating']/2);
		echo $l_sf_escape;
		player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $rating, 0, 0);

		if ($spy_success_factor)
		{
			spy_ship_destroyed($shipinfo['ship_id'],0);
		}

		if ($dig_success_factor)
		{
			dig_ship_destroyed($shipinfo['ship_id'],0);
		}

		$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
		db_op_result($debug_query,__LINE__,__FILE__);

		cancel_bounty($playerinfo['player_id']);
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}
	else
	{ 
		cancel_bounty($playerinfo['player_id']);
		db_kill_player($playerinfo['player_id'], 0, 0);
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}		 
}

?>
