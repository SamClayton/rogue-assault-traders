<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: combat_sectornova.php

include ("config/config.php");
include ("languages/$langdir/lang_lrscan.inc");
include ("languages/$langdir/lang_novabomb.inc");
$no_gzip = 1;

$title = $l_lrs_title;

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

$sector = $_GET['sector'];

if ((!isset($sector)) || ($sector == ''))
{
	$sector = '';
}

if ((!isset($command)) || ($command == ''))
{
	$command = 'attackcheck';
}

bigtitle();
mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

//-------------------------------------------------------------------------------------------------

if($command == "attackcheck"){

	if($sector <= $totalfedsectors){
		echo $l_novabomb_notfedsec."<br><br>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}

	// user requested a single sector (standard) long range scan

	if($playerinfo[turns] < 50 and ($shipinfo['dev_nova'] == 'Y' and $shipinfo['class'] >= $dev_nova_shiplimit)){
		echo "$l_novabomb_turns<BR>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}

	// get scanned sector information
	$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
	$query96 = $result2->fields;

	// get sectors which can be reached through scanned sector
	$result3 = $db->Execute("SELECT link_dest FROM $dbtables[links] WHERE link_start='$sector' ORDER BY link_dest ASC");

	$i=0;

	if ($result3 > 0)
	{
		while (!$result3->EOF)
		{
			$links[$i] = $result3->fields['link_dest'];
			$i++;
			$result3->MoveNext();
		}
	}
	$num_links=$i;

	// get sectors which can be reached from the player's current sector
	$result3a = $db->Execute("SELECT link_dest FROM $dbtables[links] WHERE link_start='$shipinfo[sector_id]'");

	$i=0;
	$flag=0;

	if ($result3a > 0)
	{
		while (!$result3a->EOF)
		{
			if ($result3a->fields['link_dest'] == $sector)
			{
				$flag=1;
			}
		   $i++;
		   $result3a->MoveNext();
		}
	}

	if ($flag == 0)
	{
		echo "$l_lrs_cantscan<BR><BR>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}
 
	$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$sector");
	db_op_result($zone_query,__LINE__,__FILE__);
	$zones = $zone_query->fields;

	log_scan($playerinfo['player_id'], $sector, $zones['zone_id']);

	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" bgcolor=\"#000000\">";
	echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_sector $sector";
	if ($query96['sector_name'] != "")
	{
		echo " ($query96[sector_name])";
	}

	echo "</B></TD></TR>";
	echo "</TABLE><BR>";

	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" bgcolor=\"#000000\">";
	if($shipinfo['dev_nova'] == 'Y' and $shipinfo['class'] >= $dev_nova_shiplimit){
		echo "<TR BGCOLOR=\"$color_line1\"><TD><B><font color='#ff0000' size='4'>$l_novabomb_query</font></B>";
		echo "&nbsp;<a href=combat_sectornova.php?command=attack&sector=$sector>$l_novabomb_yes$sector.</a><br></TD></TR>";
	}
	echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_links</B></TD></TR>";
	echo "<TR><TD>";

	if ($num_links == 0)
	{
		echo "$l_none";
	}
	else
	{
		for($i = 0; $i < $num_links; $i++)
		{
			echo "$links[$i]";
			if ($i + 1 != $num_links)
			{
				echo ", ";
			}
		}
	}
	echo "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_ships</B></TD></TR>";
	echo "<TR><TD>";
	if ($sector != 1)
	{
		// get ships located in the scanned sector
		$result4 = $db->Execute("SELECT $dbtables[players].player_id,name,character_name,cloak FROM $dbtables[ships] " .
								"LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id " .
								"WHERE $dbtables[players].currentship=$dbtables[ships].ship_id and sector_id='$sector' AND on_planet='N'");
		if ($result4->EOF)
		{
			echo "$l_none";
		}
		else
		{
			$num_detected = 0;
			while (!$result4->EOF)
			{
				$row = $result4->fields;
				// display other ships in sector - unless they are successfully cloaked
				$success = SCAN_SUCCESS($shipinfo['sensors'], $row['cloak']);
				if ($success < 5)
				{
					$success = 5;
				}

				if ($success > 95)
				{
					$success = 95;
				}

				$roll = mt_rand(1, 100);
				if ($roll < $success)
				{
					$num_detected++;
					echo $row['name'] . "(" . $row['character_name'] . ")<BR>";
				}
				$result4->MoveNext();
			}
			if (!$num_detected)
			{
				echo "$l_none";
			}
		}
	}
	else
	{
		echo "$l_lrs_zero";
	}

	echo "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_port</B></TD></TR>";
	echo "<TR><TD>";
  
	if ($query96['port_type'] == "none")
	{
		echo "$l_none";
	}
	else
	{
		if ($query96['port_type'] != "none") 
		{
			$port_type = $query96['port_type'];
			$icon_alt_text = ucfirst(t_port($port_type));
			$icon_port_type_name = $port_type . ".png";
			$image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"templates/".$templatename."images/$icon_port_type_name\">";
		}
		echo "$image_string " . t_port($query96['port_type']);
	}

	echo "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_planets</B></TD></TR>";
	echo "<TR><TD><TABLE bgcolor=\"#000000\">";

	function scanlevel($techlevel){
		global $playerinfo, $shipinfo, $techjammer, $techowner;

		$sc_error= SCAN_ERROR($shipinfo['sensors'], $techjammer);
		$sc_error_plus=100;
		if ($sc_error < 100){
			$sc_error_plus=115;
		}
		if($playerinfo['player_id'] == $techowner or $techowner == 3)
			return $techlevel;

		return round($techlevel * (mt_rand($sc_error , $sc_error_plus) / 100));
	}

	function display_this_planet($this_planet) 
	{
		global $planettypes, $basefontsize, $l_unowned, $l_unnamed, $basefontsize, $dbtables, $db, $colonist_limit, $l_lrs_bounty;
		global $planet_bounty_ratio, $bounty_minturns, $playerinfo;
		global $shipinfo, $playerinfo, $techowner, $techjammer;
		$totalcount=0;
		$curcount=0;
		$i=0;
		$planetlevel=0;

		if ($this_planet['owner'] != 0)
		{
			$result5 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=". $this_planet['owner'] . "");
			$planet_owner = $result5->fields;

			$techowner = $this_planet['owner'];
			$techjammer = $this_planet['jammer'];
			$planetavg = scanlevel($this_planet['computer']) + scanlevel($this_planet['sensors']) + scanlevel($this_planet['beams']) + scanlevel($this_planet['torp_launchers']) + scanlevel($this_planet['shields']) + scanlevel($this_planet['cloak']) + ($this_planet['colonists'] / ($colonist_limit / 54));
			$planetavg = round($planetavg/37.8); // Divide by (54 levels * 7 categories / 4) to get 1-4.
		if ($planetavg > 10)
		{
			$planetavg = 10;
		}

		if ($planetavg < 0)
		{
			$planetavg = 0;
		}


			$planetlevel = $planetavg;
		}

		echo "<td align=center valign=top>";

		$isfedbounty = planet_bounty_check($playerinfo, $this_planet['sector_id'], $planet_owner, 0);

		if($isfedbounty > 0)
		{
			echo "$l_lrs_bounty<BR>";
		}
		else
		{
			echo "<font size=3>&nbsp;</font><BR>";
		}

		echo "<img src=\"$planettypes[$planetlevel]\" border=0><br><font size=", $basefontsize + 1, " color=#ffffff face=\"arial\">";
		if (empty($this_planet['name']))
		{
			echo $l_unnamed;
		}
		else
		{
			echo $this_planet['name'];
		}

		if (@$this_planet['owner'] == 0)
		{
			echo "<br>($l_unowned)";
		}
		else
		{
			echo "<br>($planet_owner[character_name])";
		}

		echo "</font>";
		echo "</td>";
	}

	$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$query96[sector_id]'");

	$planetsfound = 0;
	while (!$res->EOF)
	{
		$uber = 0;
		$success = 0;
		$hiding_planet[$i] = $res->fields;

		if ($hiding_planet[$i]['owner'] == $playerinfo['player_id'])
		{
			$uber = 1;
		}

		if ($hiding_planet[$i]['team'] != 0)
		{
			if ($hiding_planet[$i]['team'] == $playerinfo['team'])
			{
				$uber = 1;
			}
		}

		if ($shipinfo['sensors'] >= $hiding_planet[$i]['cloak'])
		{
			$uber = 1;
		}

		if ($uber == 0) //Not yet 'visible'
		{
			$success = SCAN_SUCCESS($shipinfo['sensors'], $hiding_planet[$i]['cloak']);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);
			if ($roll <= $success) // If able to see the planet
			{
				$uber = 1; //confirmed working
			}

			if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
			{
				$res_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '" . $hiding_planet[$i]['planet_id'] . "' AND owner_id = '$playerinfo[player_id]'");
				if ($res_s->RecordCount())
				{
					$uber = 1;
				}
			}
		}

		if ($uber == 1)
		{
			$planets[$i] = $res->fields;
			display_this_planet($planets[$i]);
			$planetsfound++;
		}
		$i++;
		$res->MoveNext();
	}

	if ($planetsfound == 0)
	{
		echo $l_none;
	}

	$resultSDa = $db->Execute("SELECT * from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='M'");
	$resultSDb = $db->Execute("SELECT * from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='F'");
	//==================================================================
	$has_fighters = 0;
	$highjammer=0;
	if ($resultSDb > 0)
	{
		while (!$resultSDb->EOF)
		{
			$fm_owner = $resultSDb->fields['player_id'];
			$result_fo = $db->Execute("SELECT * from $dbtables[players] where player_id=$fm_owner");
			$fighters_owner = $result_fo->fields;
			$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
			db_op_result($result3,__LINE__,__FILE__);
			$ship_owner = $result3->fields;

			// get planet sensors
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$sector' order by sensors DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;
			if ($highcloak < $planets['cloak']){
				$highcloak=$planets['cloak'];
			}
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$sector' order by jammer DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;
			if ($highjammer < $planets['jammer']){
				$highjammer=$planets['jammer'];
			}

			$sc_error= SCAN_ERROR($shipinfo['sensors'], $highjammer);
			$sc_error_plus=100;
			if ($sc_error < 100){
				$sc_error_plus=115;
			}

			$success = SCAN_SUCCESS($shipinfo['sensors'], $highcloak);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);
			if ($roll < $success)
			{
				$mines = $resultSDb->fields['quantity'];
				$planet_comp_level = round($mines * (mt_rand($sc_error , $sc_error_plus) / 100));

				if ($planet_comp_level > $mines)
				{
					$planetfighters = $mines;
				}
				else
				{
					$planetfighters = $planet_comp_level;
				}

				$has_fighters += $planetfighters;
			}
			$resultSDb->MoveNext();
		}
		$has_fighters = NUMBER($has_fighters);
	}
	//=========================================================================
	//==================================================================
	$has_mines = 0;
	$highjammer=0;
	if ($resultSDa > 0)
	{
		while (!$resultSDa->EOF)
		{
			$mn_owner = $resultSDa->fields['player_id'];
			$result_mn = $db->Execute("SELECT * from $dbtables[players] where player_id=$mn_owner");
			$mine_owner = $result_mn->fields;
			$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$mine_owner[player_id] and ship_id=$mine_owner[currentship]");
			db_op_result($result3,__LINE__,__FILE__);
			$ship_owner = $result3->fields;

			// get planet sensors
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$mn_owner or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$sector' order by sensors DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;
			if ($highcloak < $planets['cloak']){
				$highcloak=$planets['cloak'];
			}
			$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$mn_owner or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$sector' order by jammer DESC");
			db_op_result($result4,__LINE__,__FILE__);
			$planets = $result4->fields;
			if ($highjammer < $planets['jammer']){
				$highjammer=$planets['jammer'];
			}

			$sc_error= SCAN_ERROR($shipinfo['sensors'], $highjammer);
			$sc_error_plus=100;
			if ($sc_error < 100){
				$sc_error_plus=115;
			}

			$success = SCAN_SUCCESS($shipinfo['sensors'], $highcloak);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);
			if ($roll < $success)
			{
				$mines = $resultSDa->fields['quantity'];
				$planet_comp_level = round($mines * (mt_rand($sc_error , $sc_error_plus) / 100));

				if ($planet_comp_level > $mines)
				{
					$planetmines = $mines;
				}
				else
				{
					$planetmines = $planet_comp_level;
				}

				$has_mines += $planetmines;
			}
			$resultSDa->MoveNext();
		}
		$has_mines = NUMBER($has_mines);
	}
	//=========================================================================

	echo "</TABLE></TD></TR>";
	echo "<TR BGCOLOR=\"$color_line1\"><TD><B>$l_mines</B></TD></TR>";
	echo "<TR><TD>" . $has_mines;
	echo "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_fighters</B></TD></TR>";
	echo "<TR><TD>" . $has_fighters;
	echo "</TD></TR>";
	if ($sector != '1')
	{
		echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_lss</B></TD></TR>";
		echo "<TR><TD>";

		$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
		$decaydate = date("Y-m-d H:i:s", $oldstamp);
		$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> $playerinfo[player_id] AND source = $sector and time > '$decaydate' ORDER BY time DESC",1);
		db_op_result($resx,__LINE__,__FILE__);
		$myrow = $resx->fields;
		if (!$myrow)
		{
			echo "$l_none<br><br></tr></td>";
		}
		else
		{
			if ($shipinfo['sensors'] >= $lssd_level_three)
			{
				echo "$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship $l_traveled " . $myrow['destination'] . "<br><br></TD></tr></td>";
			}
			elseif ($shipinfo['sensors'] >= $lssd_level_two)
			{
				echo "$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship. <br><br></TD></tr></td>";
			}
			else
			{
				echo "$l_unknown " . get_shipclassname($myrow['ship_class']) . " $l_classship. <br><br></TD></tr></td>";
			}
		}
	}
	echo "</TABLE><BR>";
	echo "<a href=move.php?move_method=warp&sector=$sector>$l_clickme</a> $l_lrs_moveto $sector.<br><br>";
}

$nova_query = $db->Execute("SELECT dev_nova FROM $dbtables[ships] WHERE ship_id=$shipinfo[ship_id]");
db_op_result($nova_query,__LINE__,__FILE__);
$shipinfo['dev_nova'] = $nova_query->fields['dev_nova'];

if($command == "attack" and ($shipinfo['dev_nova'] == 'Y' and $shipinfo['class'] >= $dev_nova_shiplimit)){

	if($sector <= $totalfedsectors){
		echo $l_novabomb_notfedsec."<br><br>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+50, turns=turns-50 WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_nova='N' WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	if(rand(1, 100) <= $dev_nova_sectorexplode){
		$averagetechlvl = ($shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['power'] + $shipinfo['computer'] + $shipinfo['sensors'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['cloak'] + $shipinfo['armour'] + $shipinfo['ecm']) / 11;
		if($averagetechlvl > $dev_nova_destroylevel){
			$shipinfo['hull'] = floor($shipinfo['hull'] * (rand(50, 75) * 0.01));
			$shipinfo['engines'] = floor($shipinfo['engines'] * (rand(50, 75) * 0.01));
			$shipinfo['power'] = floor($shipinfo['power'] * (rand(50, 75) * 0.01));
			$shipinfo['computer'] = floor($shipinfo['computer'] * (rand(50, 75) * 0.01));
			$shipinfo['sensors'] = floor($shipinfo['sensors'] * (rand(50, 75) * 0.01));
			$shipinfo['beams'] = floor($shipinfo['beams'] * (rand(50, 75) * 0.01));
			$shipinfo['torp_launchers'] = floor($shipinfo['torp_launchers'] * (rand(50, 75) * 0.01));
			$shipinfo['shields'] = floor($shipinfo['shields'] * (rand(50, 75) * 0.01));
			$shipinfo['cloak'] = floor($shipinfo['cloak'] * (rand(50, 75) * 0.01));
			$shipinfo['armour'] = floor($shipinfo['armour'] * (rand(50, 75) * 0.01));
			$shipinfo['armour_pts'] = floor($shipinfo['armour_pts'] * (rand(50, 75) * 0.01));
			$shipinfo['torps'] = floor($shipinfo['torps'] * (rand(50, 75) * 0.01));
			$shipinfo['fighters'] = floor($shipinfo['fighters'] * (rand(50, 75) * 0.01));
			$shipinfo['credits'] = floor($shipinfo['credits'] * (rand(50, 75) * 0.01));
			$shipinfo['energy'] = floor($shipinfo['energy'] * (rand(50, 75) * 0.01));
			$shipinfo['ecm'] = floor($shipinfo['ecm'] * (rand(50, 75) * 0.01));

			echo "<font color='#ff0000' size='4'><B>$l_novabomb_novaexplode</B></font>";
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET engines=$shipinfo[engines], power=$shipinfo[power], armour=$shipinfo[armour], armour_pts=$shipinfo[armour_pts], torps=$shipinfo[torps], fighters=$shipinfo[fighters], hull=$shipinfo[hull], energy=$shipinfo[energy], computer=$shipinfo[computer], sensors=$shipinfo[sensors], beams=$shipinfo[beams], torp_launchers=$shipinfo[torp_launchers], shields=$shipinfo[shields], cloak=$shipinfo[cloak], ecm=$shipinfo[ecm] WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			if ($shipinfo['dev_escapepod'] == "Y")
			{
				echo "$l_cmb_escapepodlaunched<BR><BR>";
//				echo "<BR><BR>player_id=$onplanet[player_id]<BR><BR>";
				player_ship_destroyed($shipinfo['ship_id'], $shipinfo['player_id'], $playerinfo['rating'], 0, 0);

				playerlog($shipinfo['player_id'],LOG_SHIP_novaED_D, "$playerinfo[character_name]|Y");

				if ($spy_success_factor)
				{
				  spy_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
				}
				if ($dig_success_factor)
				{
					dig_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
				}

				$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
				db_op_result($debug_query,__LINE__,__FILE__);

// AATrade
				$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
				insert_news($playernames, 1, "targetepod");
// end
			}else{
				playerlog($playerinfo['player_id'], LOG_SHIP_novaED_D, "$playerinfo[character_name]|N");
				db_kill_player($playerinfo['player_id'], 0, 0);
// AATrade
				$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
				insert_news($playernames, 1, "targetdies");
//end
			}
			echo "<font color='#ff0000' size='4'><B>$l_novabomb_novaexplode2</B></font>";
		}

		TEXT_GOTOMAIN();
		include ("footer.php");
		die();
	}

	// user requested a single sector (standard) long range scan

	// get scanned sector information
	$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
	db_op_result($result2,__LINE__,__FILE__);
	$query96 = $result2->fields;

	$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$sector");
	db_op_result($zone_query,__LINE__,__FILE__);
	$zones = $zone_query->fields;

	log_scan($playerinfo['player_id'], $sector, $zones['zone_id']);

	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" bgcolor=\"#000000\">";
	echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_sector $sector";
	if ($query96['sector_name'] != "")
	{
		echo " ($query96[sector_name])";
	}

	echo "</B></TD></TR>";
	echo "</TABLE><BR>";

	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" bgcolor=\"#000000\">";
	echo "<TR BGCOLOR=\"$color_line1\"><TD><B><font color='#ff0000' size='4'>$l_sector $sector$l_novabomb_success</font></B></TD></TR>";
	echo "<TR><TD>";

	$res = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$sector'");
	$qtyfighters = 0;
	$qtymines = 0;
	$i = 0;
	if ($res > 0)
	{
		while (!$res->EOF)
		{
			$defences = $res->fields;
			$defence_id = $defences['defence_id'];
			if ($defences['defence_type'] == 'F')
			{
				$fightersleft = floor($defences['quantity'] * (rand(50, 100) * 0.01));
				$qtyfighters += $defences['quantity'] - $fightersleft;
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=$fightersleft WHERE " .
											"defence_id = $defence_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			if ($defences['defence_type'] == 'M')
			{
				$minesleft = floor($defences['quantity'] * (rand(50, 100) * 0.01));
				$qtymines += $defences['quantity'] - $minesleft;
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] SET quantity=$minesleft WHERE " .
											"defence_id = $defence_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			$i++;
			$res->MoveNext();
		}
	}

	$l_novabomb_fighters = str_replace("[fighters]", "<font color=white><b>$qtyfighters</b></font>", $l_novabomb_fighters);
	$l_novabomb_mines = str_replace("[mines]", "<font color=white><b>$qtymines</b></font>", $l_novabomb_mines);

	if($qtyfighters)
		echo "<TR BGCOLOR=\"$color_line1\"><TD><B>$l_novabomb_fighters</B></TD></TR>";

	if($qtymines)
		echo "<TR BGCOLOR=\"$color_line2\"><TD><B>$l_novabomb_mines</B></TD></TR>";

	$result4 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id=$sector");
	$sectorplanets = $result4->RecordCount();

	$colortoggle = 0;
	if ($sectorplanets > 0)
	{
		echo "<TR BGCOLOR=\"$color_line1\"><TD>&nbsp;</TD></TR>";
		echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_novabomb_planetsdamaged</b></TD></TR>";
		while (!$result4->EOF)
		{
			$sectorplanet = $result4->fields;
			$sectorplanet['computer'] = floor($sectorplanet['computer'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['sensors'] = floor($sectorplanet['sensors'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['beams'] = floor($sectorplanet['beams'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['torp_launchers'] = floor($sectorplanet['torp_launchers'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['shields'] = floor($sectorplanet['shields'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['jammer'] = floor($sectorplanet['jammer'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['cloak'] = floor($sectorplanet['cloak'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['torps'] = floor($sectorplanet['torps'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['fighters'] = floor($sectorplanet['fighters'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['colonists'] = floor($sectorplanet['colonists'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['credits'] = floor($sectorplanet['credits'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorplanet['energy'] = floor($sectorplanet['energy'] * (rand($dev_nova_warpdamage, 100) * 0.01));

			if($sectorplanet['name'] == "")
				$sectorplanet['name'] = $l_unnamed;

			$colorline = $colortoggle + 1;
			echo "<TR BGCOLOR='$color_line$colorline'><TD><B>$l_novabomb_planet$sectorplanet[name]$l_novabomb_damage</B></TD></TR>";
			$colortoggle = 1 - $colortoggle;
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET torps=$sectorplanet[torps], fighters=$sectorplanet[fighters], colonists=$sectorplanet[colonists], credits=$sectorplanet[credits], energy=$sectorplanet[energy], computer=$sectorplanet[computer], sensors=$sectorplanet[sensors], beams=$sectorplanet[beams], torp_launchers=$sectorplanet[torp_launchers], shields=$sectorplanet[shields], jammer=$sectorplanet[jammer], cloak=$sectorplanet[cloak] WHERE planet_id=$sectorplanet[planet_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			set_max_credits($sectorplanet['planet_id']);
// AATrade
			if ($sectorplanet['owner'] != 0)
			{
				$result5 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=". $sectorplanet['owner'] . "");
				$planet_owner = $result5->fields;
				send_system_im($sectorplanet['owner'], $l_lrs_imtitle, $playerinfo['character_name'] . " $l_lrs_imbody $sectorplanet[sector_id].", $planet_owner['last_login']);

				$isfedbounty = planet_bounty_check($playerinfo, $sectorplanet['sector_id'], $planet_owner, 1, 0.1);

				if($isfedbounty > 0)
				{
					echo $l_by_fedbounty2 . "<BR><BR>";
				}
			}
// end<br>
			$result4->MoveNext();
		}
	}

	$result4 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id " .
							"WHERE $dbtables[players].currentship=$dbtables[ships].ship_id and sector_id='$sector' AND on_planet='N'");
	$sectorships = $result4->RecordCount();

	if ($sectorships > 0)
	{
		echo "<TR BGCOLOR=\"$color_line1\"><TD>&nbsp;</TD></TR>";
		echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_novabomb_shipsdamaged</b></TD></TR>";
		while (!$result4->EOF)
		{
			$sectorship = $result4->fields;
			$sectorship['hull'] = floor($sectorship['hull'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['engines'] = floor($sectorship['engines'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['power'] = floor($sectorship['power'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['computer'] = floor($sectorship['computer'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['sensors'] = floor($sectorship['sensors'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['beams'] = floor($sectorship['beams'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['torp_launchers'] = floor($sectorship['torp_launchers'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['shields'] = floor($sectorship['shields'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['cloak'] = floor($sectorship['cloak'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['armour'] = floor($sectorship['armour'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['armour_pts'] = floor($sectorship['armour_pts'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['torps'] = floor($sectorship['torps'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['fighters'] = floor($sectorship['fighters'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['credits'] = floor($sectorship['credits'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['energy'] = floor($sectorship['energy'] * (rand($dev_nova_warpdamage, 100) * 0.01));
			$sectorship['ecm'] = floor($sectorship['ecm'] * (rand($dev_nova_warpdamage, 100) * 0.01));

			$colorline = $colortoggle + 1;
			echo "<TR BGCOLOR='$color_line$colorline'><TD><B>$l_novabomb_ship$sectorship[name]$l_novabomb_damage</B></TD></TR>";
			$colortoggle = 1 - $colortoggle;
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET hull=$sectorship[hull], engines=$sectorship[engines], power=$sectorship[power], 
			computer=$sectorship[computer], sensors=$sectorship[sensors], beams=$sectorship[beams], torp_launchers=$sectorship[torp_launchers], 
			shields=$sectorship[shields], cloak=$sectorship[cloak], armour=$sectorship[armour], armour_pts=$sectorship[armour_pts], 
			torps=$sectorship[torps], fighters=$sectorship[fighters], energy=$sectorship[energy], ecm=$sectorship[ecm] WHERE ship_id=$sectorship[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$result4->MoveNext();
		}
	}
	echo "</table>";
}

//-------------------------------------------------------------------------------------------------
echo "<BR><BR>";
TEXT_GOTOMAIN();

include ("footer.php");

?>
