<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet.php

include ("config/config.php");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_planet_report.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_traderoute.inc");

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

$line_color = $color_line2;

function linecolor()
{
  global $line_color, $color_line1, $color_line2;

  if ($line_color == $color_line1)   
   $line_color = $color_line2; 
  else   
   $line_color = $color_line1; 

  return $line_color;
}

function base_string($base)
{
	global $l_yes, $l_no;
	return ($base=='Y') ? $l_yes : $l_no;
}
//-------------------------------------------------------------------------------------------------

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
  $planetinfo=$result3->fields;

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if ((!isset($command)) || ($command == ''))
{
$command = '';
}

if ((!isset($destroy)) || ($destroy == ''))
{
$destroy = '';
}

// No planet

if (empty($planetinfo))
{
	$smarty->assign("error_msg", $l_planet_none);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planetdie.tpl");
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
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planetdie.tpl");
	include ("footer.php");
	die();
}

if (($planetinfo['owner'] == 0  || $planetinfo['defeated'] == 'Y') && $command != "capture")
{
	$capture_link="<a href='planet_unowned_capture.php?planet_id=$planet_id'>$l_planet_capture1</a>";
	$l_planet_capture2=str_replace("[capture]",$capture_link,$l_planet_capture2);
	$smarty->assign("error_msg", $l_planet_capture2);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."planetdie.tpl");
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

if (empty($command))
{
	if ($playerinfo['player_id'] == $planetinfo['owner'])
	{
	/* ...if there is no planet command already */
		if (($destroy==1 or $destroy==2) && $allow_genesis_destroy){
			if (empty($planetinfo['name']))
			{
				$l_planet_unnamed=str_replace("[name]",$ownerinfo['character_name'],$l_planet_unnamed);
				$l_planet_unnamed=str_replace("[sector]",$planetinfo['sector_id'],$l_planet_unnamed);
				$planetname = $l_planet_unnamed;
			}
			else
			{
				$l_planet_named=str_replace("[name]",$ownerinfo['character_name'],$l_planet_named);
				$l_planet_named=str_replace("[planetname]",$planetinfo['name'],$l_planet_named);
				$l_planet_named=str_replace("[sector]",$planetinfo['sector_id'],$l_planet_named);
				$planetname = $l_planet_named;
			}

			if ($destroy==2 && $allow_genesis_destroy)
			{
				if ($shipinfo[dev_genesis] < 1)
				{
					$smarty->assign("error_msg", $l_planet_nogenesis);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planetdie.tpl");
					include ("footer.php");
					die();
				}

				if ($playerinfo[turns] < 1)
				{
					$smarty->assign("error_msg", $l_planet_genesisnoturn);
					$smarty->assign("gotomain", $l_global_mmenu);
					$smarty->display($templatename."planetdie.tpl");
					include ("footer.php");
					die();
				}

				if ($spy_success_factor)
				{
					spy_planet_destroyed($planet_id);
				}

				$debug_query = $db->Execute("DELETE from $dbtables[planets] where planet_id=$planet_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("DELETE from $dbtables[dignitary] where planet_id=$planet_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_genesis=dev_genesis-1 WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query=$db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE planet_id=$planet_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				calc_ownership($shipinfo['sector_id']);

				close_database();
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
				die();
			}

			$smarty->assign("planetname", $planetname);
			$smarty->assign("destroy", $destroy);
			$smarty->assign("allow_genesis_destroy", $allow_genesis_destroy);
			$smarty->assign("l_planet_confirm", $l_planet_confirm);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_no", $l_no);
			$smarty->assign("l_yes", $l_yes);
			$smarty->assign("l_gns_nogenesis", $l_gns_nogenesis);
			$smarty->assign("l_gns_turn", $l_gns_turn);
			$smarty->assign("shipgenesis", $shipinfo['dev_genesis']);
			$smarty->assign("turns", $playerinfo['turns']);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."planetdestroy.tpl");
			include ("footer.php");
			die();
		}
	}

	if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
	{
		$smarty->assign("planet_id", $planet_id);

		$planetavg = $planetinfo['computer'] + $planetinfo['sensors'] + $planetinfo['beams'] + $planetinfo['torp_launchers'] + $planetinfo['shields'] + $planetinfo['cloak'] + ($planetinfo['colonists'] / ($colonist_limit / 54));
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
		$smarty->assign("planettype", $planettypes[$planetlevel]);

		if (empty($planetinfo['name']))
		{
			$l_planet_unnamed=str_replace("[name]","<b><font color='yellow'>$ownerinfo[character_name]</font></b>",$l_planet_unnamed);
			$l_planet_unnamed=str_replace("[sector]","<b><font color='#79f487'>$planetinfo[sector_id]</font></b>",$l_planet_unnamed);
			$l_planet_unnamed=str_replace(", ","<br>",$l_planet_unnamed);
			$l_planet_unnamed=str_replace(".","",$l_planet_unnamed);
			if ($planetinfo['base'] == "Y")
	  		{
				$l_planet_unnamed.= " $l_planet_based";
			}
			$planetname = $l_planet_unnamed;
		}
		else
		{
			$l_planet_named=str_replace("[name]","<b><font color='yellow'>$ownerinfo[character_name]</font></b>",$l_planet_named);
			$l_planet_named=str_replace("[planetname]","<b><font color='#87d8ec'>$planetinfo[name]</font></b>",$l_planet_named);
			if ($planetinfo['base'] == "Y")
			{
				$l_planet_named.= " $l_planet_based";
			}
			$l_planet_named=str_replace("[sector]","<b><font color='#79f487'>$planetinfo[sector_id]</font></b>",$l_planet_named);
			$l_planet_named=str_replace(", ","<br>",$l_planet_named);
			$l_planet_named=str_replace(".","",$l_planet_named);
			$planetname = $l_planet_named;
		}
		$smarty->assign("planetname", $planetname);
		$l_planet_name_link = "<a href='planet_owned_name.php?planet_id=$planet_id'>" . $l_planet_name_link . "</a>";
		$l_planet_name =str_replace("[name]",$l_planet_name_link,$l_planet_name2);
		$smarty->assign("l_planet_name", $l_planet_name);
		$smarty->assign("allow_genesis_destroy", $allow_genesis_destroy);
		if ($allow_genesis_destroy)
		{
			$smarty->assign("l_planet_destroyplanet", $l_planet_destroyplanet);
			$smarty->assign("l_planet_warning", $l_planet_warning);
		}
		$l_planet_readlog_link="<a href=log.php>" . $l_planet_readlog_link ."</a>";
		$l_planet_readlog=str_replace("[View]",$l_planet_readlog_link,$l_planet_readlog);
		$smarty->assign("l_planet_readlog", $l_planet_readlog);

		$l_planet_leave_link = "<a href='planet_owned_leave.php?planet_id=$planet_id'>" . $l_planet_leave_link . "</a>";
		$l_planet_leave=str_replace("[leave]",$l_planet_leave_link,$l_planet_leave);

		$l_planet_land_link = "<a href='planet_owned_land.php?planet_id=$planet_id'>" . $l_planet_land_link . "</a>";
		$l_planet_land=str_replace("[land]",$l_planet_land_link,$l_planet_land);

		if ($shipinfo['on_planet'] == 'Y' && $shipinfo['planet_id'] == $planet_id)
		{
			$smarty->assign("l_planet_land", $l_planet_leave);
		}
		else
		{
			$smarty->assign("l_planet_land", $l_planet_land);
		}

		if ($shipinfo['on_planet'] == 'Y' && $shipinfo['planet_id'] == $planet_id)
		{
			$logout_link ="<a href=logout.php>$l_planet_logout1</a>";
			$l_planet_logout2=str_replace("[logout]",$logout_link,$l_planet_logout2);
			$smarty->assign("l_planet_logout2", $l_planet_logout2);
			$smarty->assign("logout_link", $logout_link);
			$smarty->assign("onplanet", 1);
		}else{
			$smarty->assign("l_planet_logout2", "");
			$smarty->assign("logout_link", "");
			$smarty->assign("onplanet", 0);
		}
		$l_planet_transfer_link="<a href='planet_owned_transfer.php?planet_id=$planet_id'>" . $l_planet_transfer_link . "</a>";
		$l_planet_transfer=str_replace("[transfer]",$l_planet_transfer_link,$l_planet_transfer);
		$smarty->assign("l_planet_transfer_link", $l_planet_transfer_link);
		$smarty->assign("planetbased", $planetinfo['base']);
		$smarty->assign("l_planet_upgrade", $l_planet_upgrade);
		$smarty->assign("l_planetary_defense_levels", $l_planetary_defense_levels);
		$smarty->assign("l_turns_have", $l_turns_have);
		$smarty->assign("playerturns", NUMBER($playerinfo['turns']));
		$smarty->assign("l_planetary_armourpts", $l_planetary_armourpts);
		$smarty->assign("planetarmorpts", NUMBER($planetinfo['armour_pts']));
		$smarty->assign("torpprod", $planetinfo['prod_torp']);
		$smarty->assign("l_torps", $l_torps);
		$smarty->assign("torptotal", NUMBER($planetinfo['torps']));
		$smarty->assign("fighterprod", $planetinfo['prod_fighters']);
		$smarty->assign("l_fighters", $l_fighters);
		$smarty->assign("fightertotal", NUMBER($planetinfo['fighters']));
		$smarty->assign("energyprod", $planetinfo['prod_energy']);
		$smarty->assign("l_energy", $l_energy);
		$smarty->assign("energytotal", NUMBER($planetinfo['energy']));
		$smarty->assign("oreprod", $planetinfo['prod_ore']);
		$smarty->assign("l_ore", $l_ore);
		$smarty->assign("oretotal", NUMBER($planetinfo['ore']));
		$smarty->assign("organicsprod", $planetinfo['prod_organics']);
		$smarty->assign("l_organics", $l_organics);
		$smarty->assign("organicstotal", NUMBER($planetinfo['organics']));
		$smarty->assign("goodsprod", $planetinfo['prod_goods']);
		$smarty->assign("l_goods", $l_goods);
		$smarty->assign("goodstotal", NUMBER($planetinfo['goods']));
		$smarty->assign("creditprod", 100 - $planetinfo['prod_torp'] - $planetinfo['prod_fighters'] - $planetinfo['prod_energy'] - $planetinfo['prod_ore'] - $planetinfo['prod_organics'] - $planetinfo['prod_goods'] - $planetinfo['prod_research'] - $planetinfo['prod_build']);
		$smarty->assign("researchprod", $planetinfo['prod_research']);
		$smarty->assign("l_pr_research", $l_pr_research);
		$smarty->assign("buildprod", $planetinfo['prod_build']);
		$smarty->assign("l_pr_build", $l_pr_build);

 		$res = $db->execute("SELECT * FROM $dbtables[dignitary] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
		$n = $res->RecordCount();
		$smarty->assign("l_dig", $l_dig);
		$smarty->assign("digtotal", $n);
		$res = $db->execute("SELECT * FROM $dbtables[spies] WHERE planet_id = '$planet_id' AND owner_id = '$playerinfo[player_id]' ");
		$n = $res->RecordCount();
		$smarty->assign("l_spy", $l_spy);
		$smarty->assign("spytotal", $n);
		$smarty->assign("l_colonists", $l_colonists);
		$smarty->assign("colonisttotal", NUMBER($planetinfo['colonists']));
		$smarty->assign("l_credits", $l_credits);
		$smarty->assign("credittotal", NUMBER($planetinfo['credits']));
		$smarty->assign("max_credits", NUMBER($planetinfo['max_credits']));
		$smarty->assign("l_planet_interest", $l_planet_interest);
		$smarty->assign("l_planet_update", $l_planet_update);
		$smarty->assign("l_planet_repair", $l_planet_repair);

		$smarty->assign("l_planetary_computer", $l_planetary_computer);
		$smarty->assign("l_level", $l_level);
		$smarty->assign("l_planetary_sensors", $l_planetary_sensors);
		$smarty->assign("l_planetary_beams", $l_planetary_beams);
		$smarty->assign("l_planetary_torp_launch", $l_planetary_torp_launch);
		$smarty->assign("l_planetary_shields", $l_planetary_shields);
		$smarty->assign("l_planetary_jammer", $l_planetary_jammer);
		$smarty->assign("l_planetary_cloak", $l_planetary_cloak);
		$smarty->assign("l_planetary_armour", $l_planetary_armour);
		$smarty->assign("l_max", $l_max);

		$smarty->assign("l_damaged", $l_damaged);
		$smarty->assign("l_normal", $l_normal);

		$smarty->assign("planetcomputer", $planetinfo['computer']);
		$smarty->assign("planetsensors", $planetinfo['sensors']);
		$smarty->assign("planetbeams", $planetinfo['beams']);
		$smarty->assign("planettorps", $planetinfo['torp_launchers']);
		$smarty->assign("planetshields", $planetinfo['shields']);
		$smarty->assign("planetjammer", $planetinfo['jammer']);
		$smarty->assign("planetcloak", $planetinfo['cloak']);
		$smarty->assign("planetarmor", $planetinfo['armour']);

		$smarty->assign("planetcomputer_normal", $planetinfo['computer_normal']);
		$smarty->assign("planetsensors_normal", $planetinfo['sensors_normal']);
		$smarty->assign("planetbeams_normal", $planetinfo['beams_normal']);
		$smarty->assign("planettorps_normal", $planetinfo['torp_launchers_normal']);
		$smarty->assign("planetshields_normal", $planetinfo['shields_normal']);
		$smarty->assign("planetjammer_normal", $planetinfo['jammer_normal']);
		$smarty->assign("planetcloak_normal", $planetinfo['cloak_normal']);
		$smarty->assign("planetarmor_normal", $planetinfo['armour_normal']);

		$smarty->assign("computerbar", MakeBars($planetinfo['computer'], 54, "damage"));
		$smarty->assign("sensorbar", MakeBars($planetinfo['sensors'], 54, "damage"));
		$smarty->assign("beambar", MakeBars($planetinfo['beams'], 54, "damage"));
		$smarty->assign("torpbar", MakeBars($planetinfo['torp_launchers'], 54, "damage"));
		$smarty->assign("shieldbar", MakeBars($planetinfo['shields'], 54, "damage"));
		$smarty->assign("jammerbar", MakeBars($planetinfo['jammer'], 54, "damage"));
		$smarty->assign("cloakbar", MakeBars($planetinfo['cloak'], 54, "damage"));
		$smarty->assign("armorbar", MakeBars($planetinfo['armour'], 54, "damage"));

		$smarty->assign("computerbar_normal", MakeBars($planetinfo['computer_normal'], 54, "normal"));
		$smarty->assign("sensorbar_normal", MakeBars($planetinfo['sensors_normal'], 54, "normal"));
		$smarty->assign("beambar_normal", MakeBars($planetinfo['beams_normal'], 54, "normal"));
		$smarty->assign("torpbar_normal", MakeBars($planetinfo['torp_launchers_normal'], 54, "normal"));
		$smarty->assign("shieldbar_normal", MakeBars($planetinfo['shields_normal'], 54, "normal"));
		$smarty->assign("jammerbar_normal", MakeBars($planetinfo['jammer_normal'], 54, "normal"));
		$smarty->assign("cloakbar_normal", MakeBars($planetinfo['cloak_normal'], 54, "normal"));
		$smarty->assign("armorbar_normal", MakeBars($planetinfo['armour_normal'], 54, "normal"));

		if ($planetinfo['base'] == "N")
		{
			$l_planet_bbase_link = "<a href='planet_owned_base.php?planet_id=$planet_id'>" . $l_planet_bbase_link . "</a>";
			$l_planet_bbase=str_replace("[build]",$l_planet_bbase_link,$l_planet_bbase);
			$smarty->assign("l_planet_bbase", $l_planet_bbase);
		}else{
			$smarty->assign("l_planet_bbase", "&nbsp;");
		}
		
		if ($playerinfo['player_id'] == $planetinfo['owner'])
		{
			if ($playerinfo['team'] <> 0)
			{
				if ($planetinfo['team'] == 0)
				{
					$l_planet_mteam_linkC = "<a href='team.php?planet_id=$planet_id&action=planetteam'>" . $l_planet_mteam_linkC . "</a>";
					$l_planet_mteam=str_replace("[planet]",$l_planet_mteam_linkC,$l_planet_mteam);
					$smarty->assign("l_planet_mteam", $l_planet_mteam);
				}
				else
				{
					$l_planet_mteam_linkP = "<a href='team.php?planet_id=$planet_id&action=planetpersonal'>" . $l_planet_mteam_linkP . "</a>";
					$l_planet_mteam=str_replace("[planet]",$l_planet_mteam_linkP,$l_planet_mteam);
					$smarty->assign("l_planet_mteam", $l_planet_mteam);
				}
			}
		}

		if ($playerinfo['player_id'] == $planetinfo['owner'])
		{
			if ($planetinfo['team_cash'] == "Y")
			{
				$smarty->assign("cashstatus", $l_planet_teamcash);
			}
			else
			{
				$smarty->assign("cashstatus", $l_planet_not_teamcash);
			}
			$l_planet_tcash_link="<a href='planet_owned_teamcash.php?planet_id=$planet_id'>" . $l_planet_tcash_link ."</a>";
			$l_planet_tcash=str_replace("[teamcash]",$l_planet_tcash_link,$l_planet_tcash);
			$smarty->assign("l_planet_tcash", $l_planet_tcash);
		}

		$smarty->assign("l_spy_cleanupplanet", $l_spy_cleanupplanet);
		$smarty->assign("l_clickme", $l_clickme);

		if ($spy_success_factor)
		{
			$smarty->assign("spycleaner", $planetinfo['planet_id']);
		}
		else
		{
			$smarty->assign("spycleaner", 0);
		}

		if ($planetinfo['base'] == "Y")
		{	 
			$smarty->assign("digtransfer", $planet_id);
		}else{
			$smarty->assign("digtransfer", 0);
		}
		
		$smarty->assign("l_igb_term", $l_igb_term);
		if ($allow_ibank)
		{
			$smarty->assign("igbplanet", $planet_id);
		}else{
			$smarty->assign("igbplanet", 0);
		}
		$smarty->assign("l_by_placebounty", $l_by_placebounty);

		$result_auto = $db->Execute("SELECT * FROM $dbtables[autotrades] WHERE owner=$playerinfo[player_id] and planet_id=$planetinfo[planet_id]");
		$num_traderoute = $result_auto->RecordCount();

		$smarty->assign("num_traderoute", $num_traderoute);
		if($num_traderoute != 0){
			$traderoute = $result_auto->fields;
			$routecount = 0;
			$l_planet_autotrade .= " - $l_hull: <font color='yellow'>$planetinfo[cargo_hull]</font>&nbsp;&nbsp;$l_planet_cargocapacity<font color='yellow'>".NUMBER(NUM_HOLDS($planetinfo['cargo_hull']))."</font> - $l_power: <font color='yellow'>$planetinfo[cargo_power]</font>&nbsp;&nbsp;$l_planet_cargocapacity<font color='yellow'>".NUMBER(NUM_ENERGY($planetinfo['cargo_power']))."</font><br>";
			$smarty->assign("cargohullsize", $planetinfo['cargo_hull']);
			$smarty->assign("l_planet_cargocapacity", $l_planet_cargocapacity);
			$smarty->assign("cargototalholds", NUMBER(NUM_HOLDS($planetinfo['cargo_hull'])));
			$smarty->assign("l_hull", $l_hull);
			$smarty->assign("l_power", $l_power);
			$smarty->assign("cargopowersize", $planetinfo['cargo_power']);
			$smarty->assign("cargototalpower", NUMBER(NUM_ENERGY($planetinfo['cargo_power'])));

			$smarty->assign("cargoportidgoods", $traderoute['port_id_goods']);
			$smarty->assign("l_goods", $l_goods);
			if($traderoute['port_id_goods'] != 0){
				$temp = str_replace("[source_type]","<b><font color='yellow'>$l_goods</font></b>",$l_planet_autotradetype);
				if($traderoute['goods_price'] != 0)
					$tradeprice = " @ <font color='yellow'>$traderoute[goods_price]c</font>";
				else $tradeprice = "";
				$l_planet_autotrade .= str_replace("[dest_id]","<b><font color='#87d8ec'>$traderoute[port_id_goods]</font>$tradeprice</b>",$temp);
				$smarty->assign("goodstradeprice", $tradeprice);
				$routecount++;
			}

			$smarty->assign("cargoportidore", $traderoute['port_id_ore']);
			$smarty->assign("l_ore", $l_ore);
			if($traderoute['port_id_ore'] != 0){
				$temp = str_replace("[source_type]","<b><font color='yellow'>$l_ore</font></b>",$l_planet_autotradetype);
				if($traderoute['ore_price'] != 0)
					$tradeprice = " @ <font color='yellow'>$traderoute[ore_price]c</font>";
				else $tradeprice = "";
				$l_planet_autotrade .= str_replace("[dest_id]","<b><font color='#87d8ec'>$traderoute[port_id_ore]</font>$tradeprice</b>",$temp);
				$smarty->assign("oretradeprice", $tradeprice);
				$routecount++;
				if($routecount == 2)
					$l_planet_autotrade .= "-<br>";
			}

			$smarty->assign("cargoportidorganics", $traderoute['port_id_organics']);
			$smarty->assign("l_organics", $l_organics);
			if($traderoute['port_id_organics'] != 0){
				$temp = str_replace("[source_type]","<b><font color='yellow'>$l_organics</font></b>",$l_planet_autotradetype);
				if($traderoute['organics_price'] != 0)
				 	$tradeprice = " @ <font color='yellow'>$traderoute[organics_price]c</font>";
				else $tradeprice = "";
				$l_planet_autotrade .= str_replace("[dest_id]","<b><font color='#87d8ec'>$traderoute[port_id_organics]</font>$tradeprice</b>",$temp);
				$smarty->assign("organicstradeprice", $tradeprice);
				$routecount++;
				if($routecount == 2)
					$l_planet_autotrade .= "-<br>";
			}

			$smarty->assign("cargoportidenergy", $traderoute['port_id_energy']);
			$smarty->assign("l_energy", $l_energy);
			if($traderoute['port_id_energy'] != 0){
				$temp = str_replace("[source_type]","<b><font color='yellow'>$l_energy</font></b>",$l_planet_autotradetype);
				if($traderoute['energy_price'] != 0)
				 	$tradeprice = " @ <font color='yellow'>$traderoute[energy_price]c</font>";
				else $tradeprice = "";
				$l_planet_autotrade .= str_replace("[dest_id]","<b><font color='#87d8ec'>$traderoute[port_id_energy]</font>$tradeprice</b>",$temp);
				$routecount++;
				$smarty->assign("energytradeprice", $tradeprice);
				if($routecount == 2)
					$l_planet_autotrade .= "-<br>";
			}
			if($routecount != 2)
				$l_planet_autotrade .= "-<br>";

			$smarty->assign("l_planet_cargocredits", $l_planet_cargocredits);
			$smarty->assign("tradecredits", number($traderoute['current_trade']));
			$smarty->assign("traderoute_id", $traderoute['traderoute_id']);
			$smarty->assign("l_planet_autotradedelete", $l_planet_autotradedelete);
			$smarty->assign("l_planet_upgradecargo", $l_planet_upgradecargo);
			$smarty->assign("l_clickme", $l_clickme);

			$l_planet_autotrade .= "$l_planet_cargocredits <font color='yellow'>".number($traderoute[current_trade])."</font><br>";
			$l_planet_autotrade .= "<a href='planet_owned_deletetrade.php?planet_id=$planet_id&traderoute_id=$traderoute[traderoute_id]'>$l_planet_autotradedelete</a><br>$l_planet_upgradecargo <a href='planet_owned_upgradecargo.php?planet_id=$planet_id'>$l_clickme</a>.</font>";
			$cargolevel = floor($planetinfo['cargo_hull'] / 5);
			$cargoimage="<img src='templates/".$templatename."images/cargo/$cargolevel.png' border='0'>";
		}else{
			$l_planet_autotrade = $l_planet_noautotrade."&nbsp;-&nbsp;<a href='planet_owned_addtrade.php?planet_id=$planet_id'>$l_planet_autotradeadd</a><br>$l_planet_purchasecargo <a href='planet_owned_buycargoship.php?planet_id=$planet_id'>$l_clickme</a>.";
			$smarty->assign("l_planet_autotradeadd", $l_planet_autotradeadd);
			$smarty->assign("l_planet_purchasecargo", $l_planet_purchasecargo);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("planetcargohull", $planetinfo['cargo_hull']);
			if($planetinfo['cargo_hull'] == 0){
				$l_planet_autotrade = $l_planet_noautotrade."&nbsp;-&nbsp;<a href='planet_owned_addtrade.php?planet_id=$planet_id'>$l_planet_autotradeadd</a><br>$l_planet_purchasecargo <a href='planet_owned_buycargoship.php?planet_id=$planet_id'>$l_clickme</a>.";
				$smarty->assign("l_planet_autotradeadd", $l_planet_autotradeadd);
				$smarty->assign("l_planet_purchasecargo", $l_planet_purchasecargo);
				$smarty->assign("l_clickme", $l_clickme);
				$cargoimage="<img src='templates/".$templatename."images/cargo/empty.png' border='0'>";
			}else{
				$l_planet_autotrade = $l_planet_noautotrade."&nbsp;-&nbsp;<a href='planet_owned_addtrade.php?planet_id=$planet_id'>$l_planet_autotradeadd</a><br>$l_hull: <font color='yellow'>$planetinfo[cargo_hull]</font>&nbsp;&nbsp;$l_planet_cargocapacity<font color='yellow'>".NUMBER(NUM_HOLDS($planetinfo['cargo_hull']))."</font> - $l_power: <font color='yellow'>$planetinfo[cargo_power]</font>&nbsp;&nbsp;$l_planet_cargocapacity<font color='yellow'>".NUMBER(NUM_HOLDS($planetinfo['cargo_power']))."</font><br>$l_planet_upgradecargo <a href='planet_owned_upgradecargo.php?planet_id=$planet_id'>$l_clickme</a>.";
				$smarty->assign("cargohullsize", $planetinfo['cargo_hull']);
				$smarty->assign("l_planet_cargocapacity", $l_planet_cargocapacity);
				$smarty->assign("cargototalholds", NUMBER(NUM_HOLDS($planetinfo['cargo_hull'])));
				$smarty->assign("l_hull", $l_hull);
				$smarty->assign("l_power", $l_power);
				$smarty->assign("cargopowersize", $planetinfo['cargo_power']);
				$smarty->assign("cargototalpower", NUMBER(NUM_ENERGY($planetinfo['cargo_power'])));
				$smarty->assign("l_planet_autotradeadd", $l_planet_autotradeadd);
				$smarty->assign("l_planet_upgradecargo", $l_planet_upgradecargo);
				$smarty->assign("l_clickme", $l_clickme);
				$cargolevel = floor($planetinfo['cargo_hull'] / 5);
				$cargoimage="<img src='templates/".$templatename."images/cargo/$cargolevel.png' border='0'>";
			}
		}

		$smarty->assign("cargoimage", $cargoimage);
		$smarty->assign("l_planet_autotrade", $l_planet_autotrade);
		$planet_ratio=round(($planetinfo['credits']/$planetinfo['max_credits'])*100);
		$smarty->assign("l_max_credits", $l_max_credits);		
		$smarty->assign("planet_ratio", $planet_ratio);	
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_display.tpl");
		include ("footer.php");
		die();
	}
	else
	{
		$smarty->assign("planetowner", $planetinfo['owner']);
		if($planetinfo['owner'] != 3){
			$l_planet_att_link="<a href='planet_unowned_attackpreview.php?planet_id=$planet_id'>" . $l_planet_att_link ."</a>";
			$l_planet_att=str_replace("[attack]",$l_planet_att_link,$l_planet_att);
		}
		$l_planet_scn_link="<a href='planet_scan.php?planet_id=$planet_id'>" . $l_planet_scn_link ."</a>";
		$l_planet_scn=str_replace("[scan]",$l_planet_scn_link,$l_planet_scn);

		if($planetinfo['owner'] != 3){
			$smarty->assign("l_planet_scn", $l_planet_scn);
			$smarty->assign("l_planet_att", $l_planet_att);
		}

		if($shipinfo['dev_nova'] == "Y" and $planetinfo['owner'] != 3){
			$smarty->assign("novaavailible", 1);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_planet_firenova", $l_planet_firenova);
		}

		if ($sofa_on and $planetinfo['owner'] != 3){
			$smarty->assign("sofaavailible", 1);
			$smarty->assign("planet_id", $planet_id);
			$smarty->assign("l_sofa", $l_sofa);
		}

		$smarty->assign("spy_success_factor", $spy_success_factor);
		if ($spy_success_factor)
		{
			if (!isset($by)) 
			{ 
				$by = ''; 
			}
			if ($by == 'job_id')
				$by = "job_id desc, spy_id asc";
			elseif ($by == 'percent')	 
					$by = "spy_percent desc, spy_id asc";
			elseif ($by == 'move_type')   
					$by = "move_type asc, spy_id asc";
			else
			   $by = "spy_id asc";

			$r = $db->Execute("SELECT * FROM $dbtables[spies] WHERE active = 'Y' AND planet_id = $planet_id AND owner_id = $playerinfo[player_id] ORDER BY $by");
			$numspies = $r->RecordCount();
			$smarty->assign("numspies", $numspies);
			if ($numspies)
			{			
				if ($numspies<$max_spies_per_planet and $planetinfo['owner'] != 3)
					$smarty->assign("addaspy", 1);

				$smarty->assign("l_spy_yourspies", $l_spy_yourspies);
				$smarty->assign("planet_id", $planet_id);
				$smarty->assign("l_spy_sendnew", $l_spy_sendnew);
				$smarty->assign("ID", "ID");
				$smarty->assign("l_spy_job", $l_spy_job);
				$smarty->assign("l_spy_percent", $l_spy_percent);
				$smarty->assign("l_spy_move", $l_spy_move);
				$smarty->assign("l_spy_action", $l_spy_action);
				$smarty->assign("color_header", $color_header);
				$smarty->assign("color_line1", $color_line1);
				$smarty->assign("color_line2", $color_line2);

				$counter = 0;
				while (!$r->EOF)
				{
					$spy = $r->fields;
					if ($spy['job_id']==0)
					{
						$job[$counter]="$l_spy_jobs[0]";
					}
					else
					{
						$temp = $spy['job_id'];
						$job[$counter] = "<a href=spy.php?command=change&spy_id=$spy[spy_id]&planet_id=$planet_id>$l_spy_jobs[$temp]</a>";
					}
			
					$temp = $spy['move_type'];
					$move = $l_spy_moves[$temp];
		   
					if ($spy['spy_percent'] == 0)
						$spy['spy_percent'] = "-";
					else
						$spy['spy_percent'] = NUMBER(100*$spy['spy_percent'],5);
			
					$color[$counter] = linecolor();
					$spyid[$counter] = $spy['spy_id'];
					$spypercent[$counter] = $spy['spy_percent'];
					$spymove[$counter] = $move;
					$counter++;
					$r->MoveNext();
				}

				$smarty->assign("job", $job);
				$smarty->assign("color", $color);
				$smarty->assign("spyid", $spyid);
				$smarty->assign("spypercent", $spypercent);
				$smarty->assign("spymove", $spymove);
				$smarty->assign("l_spy_comeback", $l_spy_comeback);
				$smarty->assign("spymove", $spymove);

				$smarty->assign("l_base", $l_base);
				$smarty->assign("l_planetary_computer", $l_planetary_computer);
				$smarty->assign("l_planetary_sensors", $l_planetary_sensors);
				$smarty->assign("l_planetary_beams", $l_planetary_beams);
				$smarty->assign("l_planetary_torp_launch", $l_planetary_torp_launch);
				$smarty->assign("l_planetary_shields", $l_planetary_shields);
				$smarty->assign("l_planetary_jammer", $l_planetary_jammer);
				$smarty->assign("l_planetary_cloak", $l_planetary_cloak);
				$smarty->assign("l_planetary_defense_levels", $l_planetary_defense_levels);
				$smarty->assign("planetbased", base_string($planetinfo['base']));
				$smarty->assign("planetcomputer", NUMBER($planetinfo['computer']));
				$smarty->assign("planetsensors", NUMBER($planetinfo['sensors']));
				$smarty->assign("planetbeams", NUMBER($planetinfo['beams']));
				$smarty->assign("planetlaunchers", NUMBER($planetinfo['torp_launchers']));
				$smarty->assign("planetshields", NUMBER($planetinfo['shields']));
				$smarty->assign("planetjammer", NUMBER($planetinfo['jammer']));
				$smarty->assign("planetcloak", NUMBER($planetinfo['cloak']));

				$smarty->assign("l_ore", $l_ore);
				$smarty->assign("l_organics", $l_organics);
				$smarty->assign("l_goods", $l_goods);
				$smarty->assign("l_energy", $l_energy);
				$smarty->assign("l_colonists", $l_colonists);
				$smarty->assign("l_credits", $l_credits);
				$smarty->assign("l_fighters", $l_fighters);
				$smarty->assign("l_torps", $l_torps);
				$smarty->assign("l_current_qty", $l_current_qty);

				$smarty->assign("planetore", NUMBER($planetinfo['ore']));
				$smarty->assign("planetorganics", NUMBER($planetinfo['organics']));
				$smarty->assign("planetgoods", NUMBER($planetinfo['goods']));
				$smarty->assign("planetenergy", NUMBER($planetinfo['energy']));
				$smarty->assign("planetcolonists", NUMBER($planetinfo['colonists']));
				$smarty->assign("planetcredits", NUMBER($planetinfo['credits']));
				$smarty->assign("planetfighters", NUMBER($planetinfo['fighters']));
				$smarty->assign("planettorps", NUMBER($planetinfo['torps']));

				$smarty->assign("l_planet_perc", $l_planet_perc);
				$smarty->assign("l_planet_interest", $l_planet_interest);
				$smarty->assign("prodore", $planetinfo['prod_ore']);
				$smarty->assign("prodorganics", $planetinfo['prod_organics']);
				$smarty->assign("prodgoods", $planetinfo['prod_goods']);
				$smarty->assign("prodenergy", $planetinfo['prod_energy']);
				$smarty->assign("na", "n/a");
				$smarty->assign("prodfighters", $planetinfo['prod_fighters']);
				$smarty->assign("prodtorp", $planetinfo['prod_torp']);

			}
			else 
			{
				$smarty->assign("planetowner", $planetinfo['owner']);
				if($planetinfo['owner'] != 3){
					$smarty->assign("l_spy_nospieshere", $l_spy_nospieshere);
					$smarty->assign("l_spy_sendnew", $l_spy_sendnew);
					$smarty->assign("planet_id", $planet_id);
				}
			}  
		}  

		$smarty->assign("planet_id", $planet_id);
		$smarty->assign("l_clickme", $l_clickme);
		$smarty->assign("l_toplanetmenu", $l_toplanetmenu);
		$smarty->assign("allow_ibank", $allow_ibank);
		$smarty->assign("l_ifyouneedplan", $l_ifyouneedplan);
		$smarty->assign("l_igb_term", $l_igb_term);
		$smarty->assign("l_by_placebounty", $l_by_placebounty);

		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."planet_display_enemy.tpl");
		include ("footer.php");
		die();
	}
}

close_database();


?>
