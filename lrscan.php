<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: lrscan.php

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
	$command = 'scan';
}

bigtitle();
mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

//-------------------------------------------------------------------------------------------------

if ($sector == "*")
{
	if (!$allow_fullscan)
	{
		$smarty->assign("error_msg", $l_lrs_nofull);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}

	if ($playerinfo['turns'] < $fullscan_cost)
	{
		$smarty->assign("error_msg", $l_lrs_noturns);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}

	echo "$l_lrs_used " . NUMBER($fullscan_cost) . " $l_lrs_turns. " . NUMBER($playerinfo['turns'] - $fullscan_cost) . " $l_lrs_left.<BR><BR>";

	// deduct the appropriate number of turns
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-$fullscan_cost, turns_used=turns_used+$fullscan_cost WHERE player_id='$playerinfo[player_id]'");
	db_op_result($debug_query,__LINE__,__FILE__);

	// user requested a full long range scan
	$l_lrs_reach=str_replace("[sector]",$shipinfo['sector_id'],$l_lrs_reach);
	echo "$l_lrs_reach<BR><BR>";

	// get sectors which can be reached from the player's current sector
	$result = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$shipinfo[sector_id]' ORDER BY link_dest");
	echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=\"100%\" bgcolor=\"#000000\">\n";
	echo " <TR BGCOLOR=\"$color_header\">\n<TD><B>$l_sector</B><TD>\n</TD></TD><TD><B>$l_lrs_links</B></TD><TD><B>$l_lrs_ships</B></TD><TD colspan=2><B>$l_port</B></TD><TD><B>$l_planets</B></TD><TD><B>$l_mines</B></TD><TD><B>$l_fighters</B></TD><TD><B>$l_lss</B></TD></TR>";

	$color = $color_line1;
	while (!$result->EOF)
	{
		$row = $result->fields;
		// get number of sectors which can be reached from scanned sector
		$result2 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$row[link_dest]'");
		$num_links = $result2->RecordCount();

		// get number of ships in scanned sector
		$result2 = $db->Execute("SELECT * FROM $dbtables[ships] LEFT JOIN $dbtables[players] ON $dbtables[ships].player_id=$dbtables[players].player_id WHERE   $dbtables[players].currentship=$dbtables[ships].ship_id AND sector_id='$row[link_dest]' AND on_planet='N' and destroyed='N'");
		
		$num_ships = 0;
		while (!$result2->EOF)
		{
			$shiprow = $result2->fields;
			$success = SCAN_SUCCESS($shipinfo['sensors'], $shiprow['cloak']);
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
				$num_ships++;
			}
			$result2->MoveNext();
		}
		
		// get port type and discover the presence of a planet in scanned sector
		$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$row[link_dest]'");
		$query96 = $result2->fields;
		$port_type = $query96['port_type'];
		$has_planets = 0;

		$resultSDa = $db->Execute("SELECT * from $dbtables[sector_defence] WHERE sector_id='$row[link_dest]' and defence_type='M'");
		$resultSDb = $db->Execute("SELECT * from $dbtables[sector_defence] WHERE sector_id='$row[link_dest]' and defence_type='F'");

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
				$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$row[link_dest]' order by sensors DESC");
				db_op_result($result4,__LINE__,__FILE__);
				$planets = $result4->fields;
				if ($highcloak < $planets['cloak']){
					$highcloak=$planets['cloak'];
				}
				$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$fm_owner or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$row[link_dest]' order by jammer DESC");
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
				$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$mn_owner or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$row[link_dest]' order by sensors DESC");
				db_op_result($result4,__LINE__,__FILE__);
				$planets = $result4->fields;
				if ($highcloak < $planets['cloak']){
					$highcloak=$planets['cloak'];
				}
				$result4 = $db->execute("SELECT * from $dbtables[planets] where (owner=$mn_owner or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$row[link_dest]' order by jammer DESC");
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

		$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$row[link_dest]'");
		while (!$result3->EOF)
		{
			$uber = 0;
			$success = 0;
			$hiding_planet[$i] = $result3->fields;

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
				if ($roll < $success) // If able to see the planet
				{
					$uber = 1; //confirmed working
				}
	
			 ///
				if ($uber == 0 && $spy_success_factor)  // Still not yet 'visible'
				{
					$res_s = $db->Execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '" . $hiding_planet[$i]['planet_id'] . "' AND owner_id = '$playerinfo[player_id]'");
					if ($res_s->RecordCount())
					$uber = 1;
				}
			}

			if ($uber == 1)
			{
				$planets[$i] = $result3->fields;
				$has_planets++;
			}
			$i++;
			$result3->MoveNext();
		}

		if ($port_type != "none") 
		{
			$icon_alt_text = ucfirst(t_port($port_type));
			$icon_port_type_name = $port_type . ".png";
			$image_string = "<img align=absmiddle height=12 width=12 alt=\"$icon_alt_text\" src=\"templates/".$templatename."images/$icon_port_type_name\">&nbsp;";
		} 
		else 
		{
			$image_string = "&nbsp;";
		}

		$zone_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$row[link_dest]");
		db_op_result($zone_query,__LINE__,__FILE__);
		$zones = $zone_query->fields;

		log_scan($playerinfo['player_id'], $row['link_dest'],$zones['zone_id']);
		
		echo "<TR BGCOLOR=\"$color\"><TD><A HREF=move.php?move_method=warp&sector=$row[link_dest]>$row[link_dest]</A></TD><TD><A HREF=lrscan.php?command=scan&sector=$row[link_dest]>$l_scan</A></TD><TD>$num_links</TD><TD>$num_ships</TD><TD WIDTH=12>$image_string</TD><TD>" . t_port($port_type) . "</TD><TD>$has_planets</TD><TD>$has_mines</TD><TD>$has_fighters</TD>";
		if ($row['link_dest'] != '1')
		{
			$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
			$decaydate = date("Y-m-d H:i:s", $oldstamp);
			$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> $playerinfo[player_id] AND source = $row[link_dest] and time > '$decaydate' ORDER BY time DESC",1);
			db_op_result($resx,__LINE__,__FILE__);
			$myrow = $resx->fields;
			if (!$myrow)
			{
				echo "<td>$l_none</td>";
			}
			else
			{
				if ($shipinfo['sensors'] >= $lssd_level_three)
				{
					echo "<td>$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship $l_traveled " . $myrow['destination'] . "</td>";
				}
				elseif ($shipinfo['sensors'] >= $lssd_level_two)
				{
					echo "<td>$l_player " . get_player($myrow['player_id']) . " $l_onboard " . get_shipclassname($myrow['ship_class']) . " $l_classship. </td>";
				}
				else
				{
					echo "<td>$l_unknown " . get_shipclassname($myrow['ship_class']) . " $l_classship. </td>";
				}
			}
		}
		else
		{
			echo "<td>$l_lrs_fedjammed</td>";
		}

		if ($color == $color_line1)
		{
			$color = $color_line2;
		}
		else
		{
			$color = $color_line1;
		}
		$result->MoveNext();
	}
	echo "</TABLE>";

	if ($num_links == 0)
	{
		echo "$l_none.";
	}
	else
	{
		echo "<BR>$l_lrs_click";
	}
}


if($command == "scan"){

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
	if($sector > $totalfedsectors and $command == "scan" and ($shipinfo['dev_nova'] == 'Y' and $shipinfo['class'] >= $dev_nova_shiplimit))
		echo "<a href=combat_sectornova.php?command=attackcheck&sector=$sector>$l_clickme</a>$l_novabomb_question$sector.";
}

//-------------------------------------------------------------------------------------------------
echo "<BR><BR>";
TEXT_GOTOMAIN();

include ("footer.php");

?>
