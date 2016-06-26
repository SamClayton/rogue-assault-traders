<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: shipyard_purchase.php

include ("config/config.php");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_shipyard2.inc");

if ($switch!="yes"){
$title = $l_ship2_buytitle;
}else{
$title = $l_ship2_storetitle;
}

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

$res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $shipinfo[player_id]");
if ($res2)
{
	$bty = $res2->fields;
	if ($bty['total_bounty'] > 0)
	{
		if ($pay <> 1)
		{
			$l_port_bounty2 = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_bounty2);
			$smarty->assign("l_port_bounty", $l_port_bounty);
			$smarty->assign("l_port_bounty2", $l_port_bounty2);
			$smarty->assign("l_by_placebounty", $l_by_placebounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyardbounty.tpl");
			include ("footer.php");
			die();
		}
		else
		{
			if ($playerinfo['credits'] < $bty['total_bounty'])
			{
				$l_port_btynotenough = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_btynotenough);
				$smarty->assign("error_msg", $l_port_btynotenough);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."shipyarddie.tpl");
				include ("footer.php");
				die();
			}
			else
			{
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$bty[total_bounty] WHERE player_id = $shipinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("DELETE from $dbtables[bounty] WHERE bounty_on = $shipinfo[player_id] AND placed_by = 0");
				db_op_result($debug_query,__LINE__,__FILE__);

				$smarty->assign("error_msg", $l_port_bountypaid);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."shipyarddie.tpl");
				include ("footer.php");
				die();
			}
		}
	}
}

if($zoneinfo['zone_id'] != 3){
	$alliancefactor = 1;
}

$res = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE buyable = 'Y' order by type_id");
while (!$res->EOF)
{
	$ships[] = $res->fields;
	$res->MoveNext();
}

if (isset($stype))
{
	$lastship = end($ships);
	if ($stype < 1 || $stype > $lastship['type_id'])
	{
		$smarty->assign("error_msg", $l_ship2_wrongclass);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."shipyard2die.tpl");
		include ("footer.php");
		die();
	}
}
else
{
	$smarty->assign("error_msg", $l_ship2_wrongclass);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."shipyard2die.tpl");
	include ("footer.php");
	die();
}

foreach($ships as $testship)
{
	if ($testship['type_id'] == $stype)
	{
		$sship = $testship;
		break;
	}
}

if (!isset($confirm)) //display info only
{
	$calc_hull = round(mypw($upgrade_factor,$shipinfo['hull']));
	$calc_engines = round(mypw($upgrade_factor,$shipinfo['engines']));
	$calc_power = round(mypw($upgrade_factor,$shipinfo['power']));
	$calc_computer = round(mypw($upgrade_factor,$shipinfo['computer']));
	$calc_sensors = round(mypw($upgrade_factor,$shipinfo['sensors']));
	$calc_beams = round(mypw($upgrade_factor,$shipinfo['beams']));
	$calc_torp_launchers = round(mypw($upgrade_factor,$shipinfo['torp_launchers']));
	$calc_shields = round(mypw($upgrade_factor,$shipinfo['shields']));
	$calc_armour = round(mypw($upgrade_factor,$shipinfo['armour']));
	$calc_cloak = round(mypw($upgrade_factor,$shipinfo['cloak']));

	$res = $db->Execute("SELECT cost_tobuild FROM $dbtables[ship_types] WHERE type_id = $shipinfo[class]");
	db_op_result($res,__LINE__,__FILE__);
	$shipcost = $res->fields['cost_tobuild'];

	$creditreturn = 0;
	if ($shipinfo['class']!=10){
		$creditreturn = $shipinfo['dev_warpedit']*$dev_warpedit_price;
		$creditreturn += $shipinfo['dev_genesis']*$dev_genesis_price;
		$creditreturn += $shipinfo['dev_sectorgenesis']*$dev_sectorgenesis_price;
		$creditreturn += $shipinfo['dev_emerwarp']*$dev_emerwarp_price;
		$creditreturn += ($shipinfo['dev_escapepod']=='Y') ? $dev_escapepod_price : 0;
		$creditreturn += ($shipinfo['dev_fuelscoop']=='Y') ? $dev_fuelscoop_price : 0;
		$creditreturn += ($shipinfo['dev_nova']=='Y') ? $dev_nova_price : 0;
		$creditreturn += $shipinfo['dev_minedeflector']*$dev_minedeflector_price;
	}

	$creditreturn += $shipinfo['torps']*$torpedo_price;
	$creditreturn += $shipinfo['armour_pts']*$armour_price;
	$creditreturn += $shipinfo['ore']*$ore_price;
	$creditreturn += $shipinfo['organics']*$organics_price;
	$creditreturn += $shipinfo['goods']*$goods_price;
	$creditreturn += $shipinfo['energy']*$energy_price;
	$creditreturn += $shipinfo['colonists']*$colonist_price;
	$creditreturn += $shipinfo['fighters']*$fighter_price;

	$shipvalue = $creditreturn + $shipcost + (($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak+$calc_ecm) * $upgrade_cost);
	$shipvalue /= 2;

	$price_modifier_base = explode("|", $playerinfo['ship_losses']);

	for($i = 0; $i < count($price_modifier_base); $i++){
		$items = explode("-", $price_modifier_base[$i]);
		$element = $items[0];
		$price_modifier[$element] = $items[1];
		if($price_modifier[$element] < 1)
			$price_modifier[$element] = 0;
	}

	$res = $db->Execute("SELECT type_id FROM $dbtables[ship_types] WHERE buyable = 'Y'");
	$type_count = $res->RecordCount(); 

	$new_losses = "";
	for($i = 0; $i < $type_count; $i++){
		$element = $res->fields['type_id'];
		if($price_modifier[$element] < 1)
			$price_modifier[$element] = 0;
		$res->MoveNext();
	}

	$ship_price = ($price_modifier[$stype] * ($sship['cost_tobuild'] * ($ship_cost_increase / 100))) + $sship['cost_tobuild'];
	$newshipvalue=$ship_price * $alliancefactor;

	$totalcost = $newshipvalue - $shipvalue; 

	$smarty->assign("l_player", $l_player);
	$smarty->assign("l_credits", $l_credits);
	$smarty->assign("l_ship2_buying", $l_ship2_buying);
	$smarty->assign("shipname", $sship['name']);
	$smarty->assign("shipimage", $templatename."images/".$sship['image']);
	$smarty->assign("shipinfo", $sship['description']);
	$smarty->assign("l_ship2_tradein", $l_ship2_tradein);
	$smarty->assign("l_ship2_value", $l_ship2_value);
	$smarty->assign("shipvalue", NUMBER($shipvalue));
	$smarty->assign("newshipcheck", $newshipvalue);
	$smarty->assign("newshipvalue", NUMBER($newshipvalue));
	$smarty->assign("numbertotalcost", $totalcost);
	$smarty->assign("totalcost", NUMBER($totalcost));
	$smarty->assign("l_ship2_newvalue", $l_ship2_newvalue);
	$smarty->assign("l_ship2_totalcost", $l_ship2_totalcost);
	$smarty->assign("totalcost", NUMBER($totalcost));
	$smarty->assign("credits", $playerinfo['credits']);
	$smarty->assign("numbercredits", NUMBER($playerinfo['credits']));
	$smarty->assign("l_ship2_nomoney", $l_ship2_nomoney);
	$smarty->assign("stype", $stype);
	$smarty->assign("l_ship2_purchase", $l_ship2_purchase);
	$smarty->assign("class", $shipinfo['class']);
	$smarty->assign("l_ship2_buynstore", $l_ship2_buynstore);
	$smarty->assign("l_ship2_newvalue", $l_ship2_newvalue);
	$smarty->assign("newshipvalue2", NUMBER($newshipvalue));
	$smarty->assign("newshipvalue3", $newshipvalue);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."shipyard2.tpl");
	include ("footer.php");
	die();
}
else //ok, now we buy the ship for true
{
	// place ship in storage and get one out of storage.
	if ($switch=="yes"){
		$res2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$shipid1");

		while (!$res2->EOF)
		{
			$ships2 = $res2->fields;
			$res2->MoveNext();
			$storageflag=1;
		}

		$switchcost=$ships2['store_fee'] * $alliancefactor;

		if ($playerinfo['credits'] < $switchcost)
		{
			$smarty->assign("error_msg", $l_ship2_nomoney2);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyard2die.tpl");
			include ("footer.php");
			die();
		}
		$l_ship2_paystorage =  str_replace("[cost]", NUMBER($switchcost), $l_ship2_paystorage);

		//update old ship and add initial storage fee
		$creditreturn = 0;
		if ($shipinfo['class']!=10)
		{
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET store_fee=10000
										WHERE ship_id=$shipinfo[ship_id]");
		}else{
			$debug_query = $db->Execute("delete from  $dbtables[ships]  " . 
										"WHERE ship_id=$shipinfo[ship_id]");
			//sell stuff back0
			$creditreturn = $shipinfo['dev_warpedit']*$dev_warpedit_price;
			$creditreturn += $shipinfo['dev_genesis']*$dev_genesis_price;
			$creditreturn += $shipinfo['dev_sectorgenesis']*$dev_sectorgenesis_price;
			$creditreturn += $shipinfo['dev_emerwarp']*$dev_emerwarp_price;
			$creditreturn += ($shipinfo['dev_escapepod']=='Y') ? $dev_escapepod_price : 0;
			$creditreturn += ($shipinfo['dev_fuelscoop']=='Y') ? $dev_fuelscoop_price : 0;
			$creditreturn += ($shipinfo['dev_nova']=='Y') ? $dev_nova_price : 0;
			$creditreturn += $shipinfo['dev_minedeflector']*$dev_minedeflector_price;

			$creditreturn += $shipinfo['torps']*$torpedo_price;
			$creditreturn += $shipinfo['armour_pts']*$armour_price;
			$creditreturn += $shipinfo['ore']*$ore_price;
			$creditreturn += $shipinfo['organics']*$organics_price;
			$creditreturn += $shipinfo['goods']*$goods_price;
			$creditreturn += $shipinfo['energy']*$energy_price;
			$creditreturn += $shipinfo['colonists']*$colonist_price;
			$creditreturn += $shipinfo['fighters']*$fighter_price;
			$creditreturn /= 2;
		}

		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$shipinfo[sector_id] WHERE ship_id=$shipid1");
		db_op_result($debug_query,__LINE__,__FILE__);

		if ($spy_success_factor)
		{
			spy_buy_new_ship($shipinfo['ship_id'], $shipid1);
		}

		if ($dig_success_factor)
		{
			dig_buy_new_ship($shipinfo['ship_id'], $shipid1);
		}

		$debug_query = $db->Execute("UPDATE $dbtables[probe] SET ship_id = $shipid1 WHERE ship_id = $shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		//Insert current ship in players table
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET currentship=$shipid1, credits=credits-$switchcost + $creditreturn " . 
									"WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$smarty->assign("error_msg", $l_ship2_paystorage);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."shipyard2die.tpl");
		include ("footer.php");
		die();
	}else{
		// buy new ship
		$calc_hull = round(mypw($upgrade_factor,$shipinfo['hull']));
		$calc_engines = round(mypw($upgrade_factor,$shipinfo['engines']));
		$calc_power = round(mypw($upgrade_factor,$shipinfo['power']));
		$calc_computer = round(mypw($upgrade_factor,$shipinfo['computer']));
		$calc_sensors = round(mypw($upgrade_factor,$shipinfo['sensors']));
		$calc_beams = round(mypw($upgrade_factor,$shipinfo['beams']));
		$calc_torp_launchers = round(mypw($upgrade_factor,$shipinfo['torp_launchers']));
		$calc_shields = round(mypw($upgrade_factor,$shipinfo['shields']));
		$calc_armour = round(mypw($upgrade_factor,$shipinfo['armour']));
		$calc_cloak = round(mypw($upgrade_factor,$shipinfo['cloak']));
		$calc_ecm = round(mypw($upgrade_factor,$shipinfo['ecm']));

		$res = $db->Execute("SELECT cost_tobuild FROM $dbtables[ship_types] WHERE type_id = $shipinfo[class]");
		db_op_result($res,__LINE__,__FILE__);
		$shipcost = $res->fields['cost_tobuild'];

		$shipvalue = $shipcost + (($calc_hull+$calc_engines+$calc_power+$calc_computer+$calc_sensors+$calc_beams+$calc_torp_launchers+$calc_shields+$calc_armour+$calc_cloak+$calc_ecm) * $upgrade_cost);
		$shipvalue /= 2;

		$price_modifier_base = explode("|", $playerinfo['ship_losses']);

		for($i = 0; $i < count($price_modifier_base); $i++){
			$items = explode("-", $price_modifier_base[$i]);
			$element = $items[0];
			$price_modifier[$element] = $items[1];
			if($price_modifier[$element] < 1)
				$price_modifier[$element] = 0;
		}

		$res = $db->Execute("SELECT type_id FROM $dbtables[ship_types] WHERE buyable = 'Y'");
		$type_count = $res->RecordCount(); 

		$new_losses = "";
		for($i = 0; $i < $type_count; $i++){
			$element = $res->fields['type_id'];
			if($price_modifier[$element] < 1)
				$price_modifier[$element] = 0;
			$res->MoveNext();
		}

		$ship_price = ($price_modifier[$stype] * ($sship['cost_tobuild'] * ($ship_cost_increase / 100))) + $sship['cost_tobuild'];
		$newshipvalue=$ship_price * $alliancefactor;

		$creditreturn = 0;
		if ($shipinfo['class']!=10){
			$creditreturn = $shipinfo['dev_warpedit']*$dev_warpedit_price;
			$creditreturn += $shipinfo['dev_genesis']*$dev_genesis_price;
			$creditreturn += $shipinfo['dev_sectorgenesis']*$dev_sectorgenesis_price;
			$creditreturn += $shipinfo['dev_emerwarp']*$dev_emerwarp_price;
			$creditreturn += ($shipinfo['dev_escapepod']=='Y') ? $dev_escapepod_price : 0;
			$creditreturn += ($shipinfo['dev_fuelscoop']=='Y') ? $dev_fuelscoop_price : 0;
			$creditreturn += ($shipinfo['dev_nova']=='Y') ? $dev_nova_price : 0;
			$creditreturn += $shipinfo['dev_minedeflector']*$dev_minedeflector_price;
		}

		$creditreturn += $shipinfo['torps']*$torpedo_price;
		$creditreturn += $shipinfo['armour_pts']*$armour_price;
		$creditreturn += $shipinfo['ore']*$ore_price;
		$creditreturn += $shipinfo['organics']*$organics_price;
		$creditreturn += $shipinfo['goods']*$goods_price;
		$creditreturn += $shipinfo['energy']*$energy_price;
		$creditreturn += $shipinfo['colonists']*$colonist_price;
		$creditreturn += $shipinfo['fighters']*$fighter_price;
		$creditreturn /= 2;

		if ($keep!="yes" or $shipinfo['class']==10){
			$totalcost = $newshipvalue - ($shipvalue + $creditreturn);
		}else{
			$totalcost = $newshipvalue;
		}

		//Let's do the regular sanity checks first

		if ($playerinfo['turns'] < $sship['turnstobuild'])
		{
			$l_ship2_turns =  str_replace("[turns]", NUMBER($sship['turnstobuild']), $l_ship2_turns);
			$smarty->assign("error_msg", $l_ship2_turns);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyarddie.tpl");
			include ("footer.php");
			die();
		}

		if (!isset($sship))
		{
			$smarty->assign("error_msg", $l_ship2_error);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyarddie.tpl");
			include ("footer.php");
			die();
		}

		if ($sship['type_id'] == $shipinfo['class'])
		{
			$smarty->assign("error_msg", $l_ship2_owned);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyarddie.tpl");
			include ("footer.php");
			die();
		}

		if ($playerinfo['credits'] < $totalcost)
		{
			$smarty->assign("error_msg", $l_ship2_nomoney2);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."shipyarddie.tpl");
			include ("footer.php");
			die();
		}

		$shipname = addslashes($shipinfo['name']);

		//Okay, we're done checking. Now time to create the new ship and assign it as current

		$debug_query = $db->Execute("INSERT INTO $dbtables[ships] 
									(player_id,
									class,
									name,
									destroyed,
									basehull,
									hull,
									engines,
									power,
									computer,
									sensors,
									beams,
									torp_launchers,
									torps,
									shields,
									armour,
									armour_pts,
									cloak,
									ecm,
									sector_id,
									ore,
									organics,
									goods,
									energy,
									colonists,
									fighters,
									on_planet,
									dev_warpedit,
									dev_genesis,
									dev_emerwarp,
									dev_escapepod,
									dev_fuelscoop,
									dev_nova,
									dev_minedeflector,
									planet_id,
									cleared_defences,
									hull_normal,
									engines_normal,
									power_normal,
									computer_normal,
									sensors_normal,
									beams_normal,
									torp_launchers_normal,
									shields_normal,
									armour_normal,
									cloak_normal,
									ecm_normal
									)
									VALUES(" .
									"$playerinfo[player_id]," .		 // player_id
									"'$stype'," .					   // class
									"'$shipname'," .					// name
									"'N'," .							// destroyed
									"$sship[basehull]," .				// basehull
									"$sship[minhull]," .				// hull
									"$sship[minengines]," .			 // engines
									"$sship[minpower]," .			   // power
									"$sship[mincomputer]," .			// computer
									"$sship[minsensors]," .			 // sensors
									"$sship[minbeams]," .			   // beams
									"$sship[mintorp_launchers]," .	  // torp_launchers
									"0," .							  // torps
									"$sship[minshields]," .			 // shields
									"$sship[minarmour]," .			  // armour
									"$start_armour," .				  // armour_pts
									"$sship[mincloak]," .			   // cloak
									"$sship[minecm]," .				// ecm
									"$shipinfo[sector_id]," .		   // sector_id
									"0," .							  // ore
									"0," .							  // organics
									"0," .							  // goods
									"$start_energy," .				  // energy
									"0," .							  // colonists
									"$start_fighters," .				// fighters
									"'N'," .							// on_planet
									"0," .		// dev_warpedit
									"0," .		 // dev_genesis
									"0," .		// dev_emerwarp
									"'N'," .	 // dev_escapepod
									"'N'," .	 // dev_fuelscoop
 									"'N'," . 
									"0," .   // dev_minedeflector
									"0," .							  // planet_id
									"'', " .							 // cleared_defences
									"$sship[minhull]," .				// hull
									"$sship[minengines]," .			 // engines
									"$sship[minpower]," .			   // power
									"$sship[mincomputer]," .			// computer
									"$sship[minsensors]," .			 // sensors
									"$sship[minbeams]," .			   // beams
									"$sship[mintorp_launchers]," .	  // torp_launchers
									"$sship[minshields]," .			 // shields
									"$sship[minarmour]," .			  // armour
									"$sship[mincloak]," .			   // cloak
									"$sship[minecm]" .				// ecm
									")");
		db_op_result($debug_query,__LINE__,__FILE__);

		//get new ship_id
		$res = $db->Execute("SELECT max(ship_id) as ship_id from $dbtables[ships] WHERE player_id=$playerinfo[player_id]" .
							" AND class='$stype'");
		$ship_id = $res->fields['ship_id'];

		//update old ship and add initial storage fee
		if ($keep=="yes")
		{
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET store_fee=10000
										WHERE ship_id=$shipinfo[ship_id]");
		}else{
			$debug_query = $db->Execute("delete from  $dbtables[ships]  " . 
										"WHERE ship_id=$shipinfo[ship_id]");
			//move stuff from old ship
			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET store_fee=0, 
								 		dev_warpedit=$shipinfo[dev_warpedit],
										dev_genesis=$shipinfo[dev_genesis],
										dev_sectorgenesis=$shipinfo[dev_sectorgenesis],
										dev_emerwarp=$shipinfo[dev_emerwarp],
										dev_escapepod='$shipinfo[dev_escapepod]',
										dev_fuelscoop='$shipinfo[dev_fuelscoop]',
										dev_nova='$shipinfo[dev_nova]',
										dev_minedeflector=$shipinfo[dev_minedeflector]  
										WHERE ship_id=$ship_id");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		if ($shipinfo['class']==10){
			$debug_query = $db->Execute("DELETE FROM $dbtables[ships] WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		//Insert current ship in players table
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET currentship=$ship_id " . 
									"WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		if ($spy_success_factor)
		{
			spy_buy_new_ship($shipinfo['ship_id'], $ship_id);
		}

		if ($dig_success_factor)
		{
			dig_buy_new_ship($shipinfo['ship_id'], $ship_id);
		}

		$debug_query = $db->Execute("UPDATE $dbtables[probe] SET ship_id = $ship_id WHERE ship_id = $shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		//Now update player credits & turns

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-$sship[turnstobuild], turns_used=turns_used+$sship[turnstobuild], credits=credits-$totalcost WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		gen_score($playerinfo['player_id']);

		$smarty->assign("error_msg", $l_ship2_shipbought);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."shipyarddie.tpl");
		include ("footer.php");
		die();
	}// End switch stuff
}

close_database();
?>
