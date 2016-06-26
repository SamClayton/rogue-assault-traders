<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: probemenu.php

include ("config/config.php");
include ("languages/$langdir/lang_probes.inc");
include ("languages/$langdir/lang_planets.inc");
$no_gzip = 1;

$title = $l_probe_title;

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

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

bigtitle();

if ((!isset($command)) || ($command == ''))
{
	$command = '';
}

if ((!isset($by)) || ($by == ''))
{
	$by = '';
}

if ((!isset($by1)) || ($by1 == ''))
{
	$by1 = '';
}

if ((!isset($by2)) || ($by2 == ''))
{
	$by2 = '';
}

if ((!isset($by3)) || ($by3 == ''))
{
	$by3 = '';
}

if ((!isset($planet_id)) || ($planet_id == ''))
{
	$planet_id = '-1';
}

if ((!isset($dismiss)) || ($dismiss == ''))
{
	$dismiss = '';
}

if ((!isset($probe_id)) || ($probe_id == ''))
{
	$probe_id = '';
}

$line_color = $color_line2;
function linecolor()
{
	global $line_color, $color_line1, $color_line2;

	if ($line_color == $color_line1)
	{
		$line_color = $color_line2; 
	}
	else
	{
		$line_color = $color_line1; 
	}

	return $line_color;
}


switch ($command)
{
	case "drop": //drop probe
		$res = $db->Execute("SELECT $dbtables[universe].zone_id, $dbtables[zones].allow_planet, $dbtables[zones].team_zone, " .
							"$dbtables[zones].owner FROM $dbtables[zones],$dbtables[universe] WHERE " .
							"$dbtables[zones].zone_id=$sectorinfo[zone_id] AND $dbtables[universe].sector_id = $shipinfo[sector_id]");
		$query97 = $res->fields;

		if($probe_id == "")
		{
			$debug_query = $db->Execute("SELECT * from $dbtables[probe] WHERE owner_id = $playerinfo[player_id] AND ship_id = $shipinfo[ship_id] and active='P'");
			db_op_result($debug_query,__LINE__,__FILE__);

			$ship_probe = $debug_query->RecordCount();
			$probe_info = $debug_query->fields;
			if ($ship_probe > 0){
				$probe_id=$probe_info['probe_id'];
			}
		}

		if ($query97['allow_planet'] == 'N')
		{
			// foo - error occurs here, although removing this section leaves no way for the creation to occur.
		   echo $l_probe_forbid;
		}
		elseif ($query97['allow_planet'] == 'L')
		{
			if ($query97['team_zone'] == 'N')
			{
				$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$query97[owner]");
				$ownerinfo = $res->fields;
				if ($ownerinfo['team'] != $playerinfo['team'])
				{
					echo $l_probe_forbid;
				}
				else
				{
					$query1 ="update $dbtables[probe] set active='Y', sector_id=$shipinfo[sector_id] where probe_id=$probe_id";
					$debug_query = $db->Execute($query1);
					db_op_result($debug_query,__LINE__,__FILE__);
					echo "<a href=probemenu.php>$l_clickme</a> $l_probe_linkback<BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");	
					die();
				}
			}else{
				$query1 ="update $dbtables[probe] set active='Y', sector_id=$shipinfo[sector_id] where probe_id=$probe_id";
				$debug_query = $db->Execute($query1);
				db_op_result($debug_query,__LINE__,__FILE__);
				echo "<a href=probemenu.php>$l_clickme</a> $l_probe_linkback<BR><BR>";
				TEXT_GOTOMAIN();
				include ("footer.php");	
				die();
			}
		}else{
			$query1 ="update $dbtables[probe] set active='Y', sector_id=$shipinfo[sector_id] where probe_id=$probe_id";
			$debug_query = $db->Execute($query1);
			db_op_result($debug_query,__LINE__,__FILE__);
			echo "<a href=probemenu.php>$l_clickme</a> $l_probe_linkback<BR><BR>";
			TEXT_GOTOMAIN();
			include ("footer.php");	
			die();
		}
	break;

	case "detect":   //DETECTED data
		$query ="select * from  $dbtables[probe]  where probe_id=$probe_id";
		$res0 = $db->Execute($query);
		db_op_result($res0,__LINE__,__FILE__);

		if ($res0->RecordCount()!=0){
   			$probeinfo=$res0->fields;
			$sensors = $probeinfo['sensors'];
			$probe_id=$probeinfo['probe_id'];
			$owner_id=$probeinfo['owner_id'];
			$sector=$probeinfo['sector_id'];
			$success = SCAN_SUCCESS($sensors, 5);
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
				// Warp Links
				$result2 = $db->Execute("SELECT * FROM $dbtables[links] WHERE link_start='$sector'");
				$num_links = $result2->RecordCount();
				$warplinks="";
				if ($num_links == 0)
				{
					$warplinks= "";
				}
				else
				{
					$warplinks= "";
					$linknumber= 0;
					for($i = 0; $i < $num_links; $i++)
					{
						$links[$i] = $result2->fields;
						// Last Ship Seen
						if ($links[$i] != '0')
						{
							$linknumber++;
							$warplinks.="$l_probe2_warplink $linknumber: ".$links[$i]['link_dest'];
							$destination=$links[$i]['link_dest'];
							$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
							$decaydate = date("Y-m-d H:i:s", $oldstamp);
							$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> '$owner_id' AND source = $destination and time > '$decaydate' ORDER BY time DESC",1);
							db_op_result($resx,__LINE__,__FILE__);
							$myrow = $resx->fields;
							$count = $resx->RecordCount;
							echo $count;
							if (!$myrow)
							{
								$warplinks.= " - $l_none<br>";
							}
							else
							{
								if($destination != 1){
									if ($sensors >= $lssd_level_three)
									{
										$warplinks.= " - $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship $l_probe2_traveled " . $myrow['destination'] . " <br>";
									}
									elseif ($sensors >= $lssd_level_two)
									{
										$warplinks.= " - $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
									}
									else
									{
										$warplinks.= " - " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
									}
								}
								else
								{
									$warplinks.= " - $l_probe2_fedjammed <br>";
								}
							}
						}
						$result2->MoveNext();
					}
				}
			}else{
				$warplinks="";
			}
			// Last Ship Seen in sector
			$success = SCAN_SUCCESS($sensors, 10);
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
				$oldstamp = strtotime(date("Y-m-d H:i:s")) - ($lss_decay_time * 86400);
				$decaydate = date("Y-m-d H:i:s", $oldstamp);
				$resx = $db->SelectLimit("SELECT * from $dbtables[movement_log] WHERE player_id <> '$owner_id' AND source = $sector and time > '$decaydate' ORDER BY time DESC",1);
				db_op_result($resx,__LINE__,__FILE__);
				$myrow = $resx->fields;
				$count = $resx->RecordCount;
				echo $count;
				$lastship="";
				if (!$myrow)
				{
					$lastship.= "Last Ship Seen: $l_none<br>";
				}
				else
				{
				if($sector != 1){
					if ($sensors >= $lssd_level_three)
					{
						$lastship.= "$l_probe2_lastseen: $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship $l_probe2_traveled " . $myrow['destination'] . " <br>";
					}
					elseif ($sensors >= $lssd_level_two)
					{
						$lastship.= "$l_probe2_lastseen: $l_probe2_player " . get_player($myrow['player_id']) . " $l_probe2_onboarda " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
					}
					else
					{
						$lastship.= "$l_probe2_lastseen: " . get_shipclassname($myrow['ship_class']) . " $l_probe2_classship. <br>";
					}
				}
				else
				{
					$lastship = "$l_probe2_fedjammed <br>";
				}
			}
		}else{
			$lastship="";
		}
		// Detect port and sun and warps
		$success = SCAN_SUCCESS($sensors, 5);
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
			$result2 = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id='$sector'");
			$query96 = $result2->fields;
			 $port_type = $query96['port_type'];
			 $star_size = $query96['star_size']; 
			 $portinfo="$l_probe2_portfound: ".$port_type." - $l_probe2_supports ".$star_size." $l_probe2_planets.<br>";
		}else{
			$portinfo="";
			}
		$success = SCAN_SUCCESS($sensors, 15);
		if ($success < 15)
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
			// Detect Sector Defence
			$resultSDa = $db->Execute("SELECT SUM(quantity) as mines from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='M'");
			$resultSDb = $db->Execute("SELECT SUM(quantity) as fighters from $dbtables[sector_defence] WHERE sector_id='$sector' and defence_type='F'");
			$defM = $resultSDa->fields;
			$defF = $resultSDb->fields;
			$has_mines = NUMBER($defM['mines']);
			$has_fighters = NUMBER($defF['fighters']);
			$sector_def="$l_probe2_sectord: ".$has_mines." $l_probe2_mines ".$has_fighters." $l_probe2_fighters<br>";
		}else{
			$sector_def="";
		}
		// Detect ships
		$success = SCAN_SUCCESS($sensors, 10);
		if ($success < 15)
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
			if ($sector != 0)
			{
				// get ships located in the scanned sector
				$result4 = $db->Execute("SELECT * FROM $dbtables[ships] " .
										"LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id " .
										"WHERE sector_id='$sector' AND on_planet='N'");
				$shipdetect="";
				if ($result4->EOF)
				{
					$shipdetect.= "$l_probe2_ships: $l_none<br>";
				}
				else
				{
					$num_detected = 0;
					while (!$result4->EOF)
					{
						$row = $result4->fields;
						// display other ships in sector - unless they are successfully cloaked
						$success = SCAN_SUCCESS($sensors, $row['cloak']);
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
							$shipdetect.="$l_probe2_ships2 $num_detected: ".$row['name'] . "(" . $row['character_name'] . ") - ";
							// probe detect incoming ship
							// Get type of ship
		 					$roll = mt_rand(1, 100);
							$shiptype=$l_unknown;
							if ($roll < $success)
							{
								$res2 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$row[class];");
								db_op_result($res2,__LINE__,__FILE__);
								$shiptype = $res2->fields['name'];
							}
							$res3 = $db->Execute("SELECT name FROM $dbtables[ship_types] WHERE type_id=$row[class];");
							db_op_result($res3,__LINE__,__FILE__);
							$shiptype = $res3->fields['name'];
							$shipdetect.="($shiptype)<br>";
							$roll = mt_rand(1, 100);
							//scan ship
							if($sensors > 1){
								if ($roll < $success)
								{
									$sc_error = SCAN_ERROR($sensors, $row['cloak']);
									$sc_hull = (mt_rand(1, 100) < $success) ? round($row['hull'] * $sc_error / 100) : "???";
									$sc_engines = (mt_rand(1, 100) < $success) ? round($row['engines'] * $sc_error / 100) : "???";
									$sc_power = (mt_rand(1, 100) < $success) ? round($row['power'] * $sc_error / 100) : "???";
									$sc_computer = (mt_rand(1, 100) < $success) ? round($row['computer'] * $sc_error / 100) : "???";
									$sc_sensors = (mt_rand(1, 100) < $success) ? round($row['sensors'] * $sc_error / 100) : "???";
									$sc_beams = (mt_rand(1, 100) < $success) ? round($row['beams'] * $sc_error / 100) : "???";
									$sc_torp_launchers = (mt_rand(1, 100) < $success) ? round($row['torp_launchers'] * $sc_error / 100) : "???";
									$sc_armour = (mt_rand(1, 100) < $success) ? round($row['armour'] * $sc_error / 100) : "???";
									$sc_shields = (mt_rand(1, 100) < $success) ? round($row['shields'] * $sc_error / 100) : "???";
									$sc_cloak = (mt_rand(1, 100) < $success) ? round($row['cloak'] * $sc_error / 100) : "???";
									$sc_ecm = (mt_rand(1, 100) < $success) ? round($row['ecm'] * $sc_error / 100) : "???";
									$sc_armour_pts = (mt_rand(1, 100) < $success) ? round($row['armour_pts'] * $sc_error / 100) : "???";
									$sc_ship_fighters = (mt_rand(1, 100) < $success) ? round($row['fighters'] * $sc_error / 100) : "???";
									$sc_torps = (mt_rand(1, 100) < $success) ? round($row['torps'] * $sc_error / 100) : "???";
									$sc_credits = (mt_rand(1, 100) < $success) ? round($row['credits'] * $sc_error / 100) : "???";
									$sc_ship_energy = (mt_rand(1, 100) < $success) ? round($row['energy'] * $sc_error / 100) : "???";
									$sc_dev_minedeflector = (mt_rand(1, 100) < $success) ? round($row['dev_minedeflector'] * $sc_error / 100) : "???";
									$sc_dev_emerwarp = (mt_rand(1, 100) < $success) ? round($row['dev_emerwarp'] * $sc_error / 100) : "???";
									$sc_dev_pod = (mt_rand(1, 100) < $success) ? round($row['dev_escapepod'] * $sc_error / 100) : "???";
									$sc_ship_colonists = (mt_rand(1, 100) < $success) ? round($row['colonists'] * $sc_error / 100) : "???";
									$sc_ship_ore = (mt_rand(1, 100) < $success) ? round($row['ore'] * $sc_error / 100) : "???";
									$sc_ship_organics = (mt_rand(1, 100) < $success) ? round($row['organics'] * $sc_error / 100) : "???";
									$sc_ship_goods = (mt_rand(1, 100) < $success) ? round($row['goods'] * $sc_error / 100) : "???";
									$sc_dev_warpedit = (mt_rand(1, 100) < $success) ? round($row['dev_warpedit'] * $sc_error / 100) : "???";
									$sc_dev_genesis = (mt_rand(1, 100) < $success) ? round($row['dev_genesis'] * $sc_error / 100) : "???";
									$sc_scoop = (mt_rand(1, 100) < $success) ? round($row['dev_fuelscoop'] * $sc_error / 100) : "???";
									$shipdetect.="&nbsp;&nbsp;&nbsp;$l_hull: ".$sc_hull." $l_engines: ".$sc_engines." $l_power: ".$sc_power." $l_computer: ".$sc_computer." $l_sensors: ".$sc_sensors."<br>&nbsp;&nbsp;&nbsp;$l_beams: ".$sc_beams." $l_torp_launch: ".$sc_torp_launchers." $l_armour: ".$sc_armour." $l_shields: ".$sc_shields." $l_cloak: ".$sc_cloak." $l_ecm: ".$sc_ecm."<br>&nbsp;&nbsp;&nbsp;$l_armourpts: ".$sc_armour_pts." $l_fighters: ".$sc_ship_fighters." $l_torps: ".$sc_torps." $l_energy: ".$sc_ship_energy." $l_credits: ".$sc_credits."<br>&nbsp;&nbsp;&nbsp;$l_deflect: ".$sc_dev_minedeflector." $l_ewd: ".$sc_dev_emerwarp." $l_escape_pod: ".$sc_dev_pod."<br>";
								}
							}
						}
						$result4->MoveNext();
					}
					if (!$num_detected)
					{
						$shipdetect = "$l_probe2_ships: $l_none<br>";
					}
				}
			}
		}else{
			$shipdetect="";
		}
		$success = SCAN_SUCCESS($sensors, 5);
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
			// Detect Planets
			$has_planets = 0;
			$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id='$sector' and owner!=$owner_id");

			db_op_result($result3,__LINE__,__FILE__);
			$planetinfo="";
			if ($result3->RecordCount() ==0)
		  		$planetinfo="$l_probe2_noneowned<br>";

			$totalplanetsfound = 0;
			while (!$result3->EOF)
			{
				$uber = 0;
				$success = 0;
				$hiding_planet[$i] = $result3->fields;
				$powner=$hiding_planet[$i]['owner'];
				echo $powner;
				// Get Char name
				$pname = $db->Execute("SELECT character_name FROM $dbtables[players] WHERE player_id = $powner");
 				db_op_result($pname,__LINE__,__FILE__);
				if ($pname)
				{
					$resn = $pname->fields;
					$playername = $resn['character_name'];
				}
				else
				{
					$playername=$l_unknown;
				}
				if ($hiding_planet[$i]['owner'] == $owner_id)
				{
					$uber = 1;
				}

				if ($hiding_planet[$i]['team'] != 0)
				{
					if ($hiding_planet[$i]['team'] == $owner_id)
					{
						$uber = 1;
					}
				}

				if ($sensors >= $hiding_planet[$i]['cloak'])
				{
					$uber = 1;
				}

				if ($uber == 0) //Not yet 'visible'
				{
					$success = SCAN_SUCCESS($sensors, $hiding_planet[$i]['cloak']);
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
							$uber = 1;
					}
				}

				if ($uber == 1)
				{
					$totalplanetsfound++;
					$planets[$i] = $result3->fields;
					$success = (10 - $hiding_planet[$i]['cloak'] / 2 + $sensors) * 5;
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
						// if scan fails - inform both player and target. 
						$planetinfo.= "$l_planet_noscan<BR><BR>";
					}
					else
					{
						// scramble results by scan error factor. 
						$sc_error= SCAN_ERROR($sensors, $hiding_planet[$i]['jammer']);
						$sc_error_plus=100;
						if ($sc_error < 100){
							$sc_error_plus=115;
						}
						if (empty($hiding_planet[$i]['name']))
							$hiding_planet[$i]['name'] = $l_unnamed;
						$preport = str_replace("[name]",$hiding_planet[$i]['name'] ,$l_planet_scn_report );
						$preport = str_replace("[owner]",$playername ,$preport );
						$planetinfo.= "$l_probe_planetname $totalplanetsfound: $preport<BR>";
						$planetinfo.= "&nbsp;&nbsp;&nbsp;$l_organics: ";
						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_organics=NUMBER(round($hiding_planet[$i]['organics'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_organics";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_ore: ";
						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_ore=NUMBER(round($hiding_planet[$i]['ore'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_ore";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_goods: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_goods=NUMBER(round($hiding_planet[$i]['goods'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_goods";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_energy: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_energy=NUMBER(round($hiding_planet[$i]['energy'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_energy";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_colonists: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_colonists=NUMBER(round($hiding_planet[$i]['colonists'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_colonists";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_credits: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_credits=NUMBER(round($hiding_planet[$i]['credits'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_credits";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= "<br><br>$l_defense:<br>";
						$planetinfo.= "&nbsp;&nbsp;&nbsp;$l_base: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$planetinfo.= $hiding_planet[$i]['base'];
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_torps: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_base_torp=NUMBER(round($hiding_planet[$i]['torps'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_base_torp";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_fighters: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_fighters=NUMBER(round($hiding_planet[$i]['fighters'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_fighters";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= "<br>&nbsp;&nbsp;&nbsp;$l_planetary_computer: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_planet_computer=NUMBER(round($hiding_planet[$i]['computer'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_planet_computer";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_beams: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_beams=NUMBER(round($hiding_planet[$i]['beams'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_beams";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_torp_launch: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_torp_launchers=NUMBER(round($hiding_planet[$i]['torp_launchers'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_torp_launchers";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_sensors: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_sensors=NUMBER(round($hiding_planet[$i]['sensors'] *(mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_sensors";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= "<br>&nbsp;&nbsp;&nbsp;$l_planetary_cloak: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_cloak=NUMBER(round($hiding_planet[$i]['cloak'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_cloak";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_shields: ";
						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_cloak=NUMBER(round($hiding_planet[$i]['shields'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_shields";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_jammer: ";
						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_jammer=NUMBER(round($hiding_planet[$i]['jammer'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_jammer";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_planetary_armour: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_armour=NUMBER(round($hiding_planet[$i]['armour'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_armour";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= " - $l_armourpts: ";

						$roll = mt_rand(1, 100);
						if ($roll < $success)
						{
							$sc_armour_pts=NUMBER(round($hiding_planet[$i]['armour_pts'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
							$planetinfo.= "$sc_armour_pts";
						}
						else
						{
							$planetinfo.= "???";
						}
						$planetinfo.= "<BR>";
						$planet_id=$hiding_planet[$i]['planet_id'];
						$resa = $db->Execute("SELECT $dbtables[ships].*, $dbtables[players].character_name FROM $dbtables[ships] LEFT JOIN $dbtables[players] ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE on_planet = 'Y' and planet_id = $planet_id");
						db_op_result($resa,__LINE__,__FILE__);
						while (!$resa->EOF)
						{
							$row = $resa->fields;
							$success = SCAN_SUCCESS($sensors, $row['cloak']);
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
								$planetinfo.= "&nbsp;&nbsp;&nbsp;<B>$row[character_name] $l_planet_ison</B><BR>";
							}
							$resa->MoveNext();
						}
					}
					$has_planets++;
				}
				$planetinfo.="<br>";
				$i++;
				$result3->MoveNext();
			}
		}else{
			$planetinfo="";
		}
	}//end rec count

	$l_probe_scan=str_replace("[sector]",$sector,$l_probe_scan);
	echo $l_probe_scan."<br>";
	if ($warplinks !="")
	{
		echo $warplinks;
	}
	if ($lastship !="")
	{
		echo $lastship;
	}
	if ($portinfo !="")
	{
		echo $portinfo;
	}
	if ($sector_def !="")
	{
		echo $sector_def;
	}
	if ($shipdetect !="")
	{
		echo $shipdetect;
	}
	if ($planetinfo !="")
	{
		echo $planetinfo;
	}
	if (($warplinks=="")and($lastship=="")and($portinfo=="")and($sector_def=="")and($shipdetect=="")and($planetinfo=="")){
		echo "$l_probe2_nothing<p>";
	}else{
		echo "<p>";
	}

	echo "<a href=probemenu.php>$l_clickme</a> $l_probe_linkback<BR><BR>";
	TEXT_GOTOMAIN();
	include ("footer.php");	
	die();
break;


default:

	if ($by1 == 'sector')   $by11 = "sector_id asc";
	elseif ($by1 == 'tsector')   $by11 = "sector_id asc";
	elseif ($by1 == 'engines')   $by11 = "engines asc";
	elseif ($by1 == 'sensors')   $by11 = "sensors asc";
	elseif ($by1 == 'cloak')   $by11 = "cloak asc";
	elseif ($by1 == 'move_type')   $by11 = "type asc, probe_id asc";
	else						  $by11 = "probe_id asc";

	$res = $db->Execute("SELECT * FROM $dbtables[probe] WHERE  owner_id=$playerinfo[player_id] ");
	if ($res->RecordCount())
	{
		$line_color = $color_line2;
		$res = $db->Execute("SELECT * from $dbtables[probe] where owner_id=$playerinfo[player_id] and active='Y' ORDER BY $by11");
		if ($res->RecordCount())
		{
			echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
			echo "<TR BGCOLOR=\"$color_header\"><TD colspan=8 align=center><font color=white><B>$l_probe_defaulttitle1</B></font></TD></TR>";
			echo "<TR BGCOLOR=\"$color_line2\">";
			echo "<TD><B><A HREF=probemenu.php>$l_probe_codenumber</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=type>$l_probe_type</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=sector&>$l_probe_sector</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=tsector>$l_probe_tsector</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=engines>$l_probe_engine</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=sensors>$l_probe_sensors</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=cloak>$l_probe_cloak</A></B></TD>";
			echo "<TD><B>$l_probe_detect</B></TD>";
			echo "</TR>";

			while (!$res->EOF)
			{
				$probe = $res->fields;
				$probe['sector_id'] = "<a href=move.php?move_method=real&engage=1&destination=$probe[sector_id]>$probe[sector_id]</a>";
				$move = $l_probe_moves[$probe['move_type']];
  				if ($probe['target_sector']==0 && $probe['target_sector']==0){
					$probe['target_sector']=$probe['sector_id'];
				}else{
					$probe['target_sector'] = "<a href=move.php?move_method=real&engage=1&destination=$probe[target_sector]>$probe[target_sector]</a>";
				}
				echo "<TR BGCOLOR=" . linecolor() ."><TD><font size=2 color=white>$probe[probe_id]</font></TD><TD><font size=2 color=white>".$l_probe_typen[$probe[type]]."</font></TD><TD><font size=2 color=white> $probe[sector_id]</font></TD><TD><font size=2 color=white> $probe[target_sector]</font></TD><TD><font size=2 color=white> $probe[engines]</font></TD><TD><font size=2 color=white> $probe[sensors]</font></TD><TD><font size=2 color=white> $probe[cloak]</font></TD></TD><TD><font size=2><a href=probemenu.php?command=detect&probe_id=$probe[probe_id]>$l_probe_view</a></font></TD></TR>";
				$res->MoveNext();
			}
			echo "</TABLE><BR><BR>";
		}
		else
		{
			echo "<B>$l_probe_no1</B><BR><BR>";
		}

		$line_color = $color_line2;
		$res = $db->Execute("SELECT * from $dbtables[probe] where owner_id=$playerinfo[player_id] and active='P' ORDER BY $by11");
		if ($res->RecordCount())
		{
			echo "<table border=1 cellspacing=1 cellpadding=2 width=\"100%\">";
			echo "<TR BGCOLOR=\"$color_header\"><TD colspan=7 align=center><font color=white><B>$l_probe_defaulttitle2</B></font></TD></TR>";
			echo "<TR BGCOLOR=\"$color_line2\">";
			echo "<TD><B><A HREF=probemenu.php>$l_probe_codenumber</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=type>$l_probe_type</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=engines>$l_probe_engine</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=sensors>$l_probe_sensors</A></B></TD>";
			echo "<TD><B><A HREF=probemenu.php?by1=cloak>$l_probe_cloak</A></B></TD>";
			
			echo "<TD><B>$l_probe_launch</B></TD>";
			echo "</TR>";

			while (!$res->EOF)
			{
				$probe = $res->fields;
				$probe['sector_id'] = "<a href=move.php?move_method=real&engage=1&destination=$probe[sector_id]>$probe[sector_id]</a>";
				$move = $l_probe_moves[$probe['move_type']];
  				if ($probe['target_sector']==0 && $probe['target_sector']==0){
					$probe['target_sector']=$probe['sector_id'];
				}
				echo "<TR BGCOLOR=" . linecolor() ."><TD><font size=2 color=white>$probe[probe_id]</font></TD><TD><font size=2 color=white>".$l_probe_typen[$probe[type]]."</font></TD><TD><font size=2 color=white> $probe[engines]</font></TD><TD><font size=2 color=white> $probe[sensors]</font></TD><TD><font size=2 color=white> $probe[cloak]</font></TD></TD><TD><font size=2><a href=probemenu.php?command=drop&probe_id=$probe[probe_id]>$l_probe_launch</a></font></TD></TR>";
				$res->MoveNext();
			}
			echo "</TABLE><BR><BR>";
		}
		else
		{
			echo "<B>$l_probe_no2</B><BR><BR>";
		}
	}
	else
	{
		echo $l_probe_noprobeatall. "<BR>";
	}
	break;
}   //swich

echo "<BR>";
TEXT_GOTOMAIN();
include ("footer.php");
?>
