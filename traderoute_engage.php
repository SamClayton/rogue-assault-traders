<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the		 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: traderoute_create.php

include ("config/config.php");
include ("languages/$langdir/lang_traderoute.inc");
include ("languages/$langdir/lang_teams.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_ports.inc");
$no_gzip = 1;
$total_experience = 0;

$title = $l_tdr_title;

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

//-------------------------------------------------------------------------------------------------

bigtitle();

$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($max_query,__LINE__,__FILE__);

$sector_max = $max_query->fields['sector_id'];

$result = $db->Execute("SELECT * FROM $dbtables[traderoutes] WHERE owner=$playerinfo[player_id]");
$num_traderoutes = $result->RecordCount();

$i = 0;
while (!$result->EOF)
{
	$traderoutes[$i] = $result->fields;
	$i++;
	$result->MoveNext();
}

$freeholds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
$maxholds = NUM_HOLDS($shipinfo['hull']);
$maxenergy = NUM_ENERGY($shipinfo['power']);

//echo "Max Holds: $maxholds<br>";
//echo "Freeholds: $freeholds<br>";
//echo "Ore: $shipinfo[ore]<br>";
//echo "Organics: $shipinfo[organics]<br>";
//echo "Goods: $shipinfo[goods]<br>";
//echo "Colonists: $shipinfo[colonists]<br>";
if ($shipinfo['colonists'] < 0 || $shipinfo['ore'] < 0 || $shipinfo['organics'] < 0 || $shipinfo['goods'] < 0 || $shipinfo['energy'] < 0 || $freeholds < 0)
{
	if ($shipinfo['colonists'] < 0 || $shipinfo['colonists'] > $maxholds)
	{
		adminlog(LOG_ADMIN_ILLEGVALUE, "$shipinfo[name]|$shipinfo[colonists]|colonists|$maxholds");
		$shipinfo['colonists'] = 0;
	}

	if ($shipinfo['ore'] < 0 || $shipinfo['ore'] > $maxholds)
	{
		adminlog(LOG_ADMIN_ILLEGVALUE, "$shipinfo[name]|$shipinfo[ore]|ore|$maxholds");
		$shipinfo['ore'] = 0;
	}

	if ($shipinfo['organics'] < 0 || $shipinfo['organics'] > $maxholds)
	{
		adminlog(LOG_ADMIN_ILLEGVALUE, "$shipinfo[name]|$shipinfo[organics]|organics|$maxholds");
		$shipinfo['organics'] = 0;
	}

	if ($shipinfo['goods'] < 0 || $shipinfo['goods'] > $maxholds)
	{
		adminlog(LOG_ADMIN_ILLEGVALUE, "$shipinfo[name]|$shipinfo[goods]|goods|$maxholds");
		$shipinfo['goods'] = 0;
	}

	if ($shipinfo['energy'] < 0 || $shipinfo['energy'] > $maxenergy)
	{
		adminlog(LOG_ADMIN_ILLEGVALUE, "$shipinfo[name]|$shipinfo[energy]|energy|$maxenergy");
		$shipinfo['energy'] = 0;
	}

	if ($freeholds < 0)
	{
		$freeholds = 0;
	}

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=$shipinfo[ore], organics=$shipinfo[organics], goods=$shipinfo[goods], energy=$shipinfo[energy], colonists=$shipinfo[colonists] WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
//echo"UPDATE $dbtables[ships] SET ore=$shipinfo[ore], organics=$shipinfo[organics], goods=$shipinfo[goods], energy=$shipinfo[energy], colonists=$shipinfo[colonists] WHERE ship_id=$shipinfo[ship_id]<br>";
}

if (!isset($tr_repeat) || $tr_repeat <= 0)
{
	$tr_repeat = 1;
}

if (isset($engage)) //performs trade route
{
	for ($i = $tr_repeat; $i > 0 ; $i--)
	{
 		$total_experience += $trading_exp;
		traderoute_engage($i, $shipinfo['ship_id']);
 	}
	update_player_experience($playerinfo['player_id'], $total_experience);
}

	echo "<p>$l_tdr_newtdr<p>";
	echo "<p>$l_tdr_modtdrset<p>";

TEXT_GOTOMAIN();
include ("footer.php");


// Error in trade route

function traderoute_die($error_msg)
{
	global $playerinfo, $total_experience, $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $l_footer_one_player_on, $sched_ticks, $smarty;
	echo "<p>$error_msg<p>";

	update_player_experience($playerinfo['player_id'], $total_experience);

	TEXT_GOTOMAIN();
	include ("footer.php");
	die();
}

// calculate trip time and energy scooped

function traderoute_distance($type1, $type2, $start, $dest, $circuit, $sells = 'N')
{
	global $playerinfo, $shipinfo, $smarty;
	global $level_factor;
//	global $db, $dbtables;

	$retvalue['triptime'] = 0;
	$retvalue['scooped1'] = 0;
	$retvalue['scooped2'] = 0;
	$retvalue['scooped'] = 0;
/*
	if ($type1 == 'L')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$start");
	$start = $query->fields;
	}

	if ($type2 == 'L')
	{
	$query = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$dest");
	$dest = $query->fields;
	}
*/
	if ($start == $dest)
	{
	if ($circuit == '1')
		$retvalue['triptime'] = '1';
	else
		$retvalue['triptime'] = '2';
	return $retvalue;
	}

	$distance=calc_dist($start,$dest);

	$shipspeed = mypw($level_factor, $shipinfo['engines']);
	$triptime = round($distance / $shipspeed);

	if (!$triptime && $destination != $shipinfo['sector_id'])
	$triptime = 1;

	if ($shipinfo['dev_fuelscoop'] == "Y")
		$energyscooped = $distance * 100;
	else
	$energyscooped = 0;

	if ($shipinfo['dev_fuelscoop'] == "Y" && !$energyscooped && $triptime == 1)
	$energyscooped = 100;

	$free_power = NUM_ENERGY($shipinfo['power']);

	if ($free_power < $energyscooped)
	$energyscooped = $free_power;

	if ($energyscooped < 1)
	$energyscooped = 0;

	$retvalue['scooped1'] = floor($energyscooped);

	if ($circuit == '2')
	{
	if ($sells == 'Y' && $shipinfo['dev_fuelscoop'] == 'Y' && $type2 == 'P' && $dest['port_type'] != 'energy')
	{
		$energyscooped = $distance * 100;
		$free_power = NUM_ENERGY($shipinfo['power']);
		if ($free_power < $energyscooped)
		$energyscooped = $free_power;
		$retvalue['scooped2'] = floor($energyscooped);
	}
	elseif ($shipinfo['dev_fuelscoop'] == 'Y')
	{
		$energyscooped = $distance * 100;
		$free_power = NUM_ENERGY($shipinfo['power']);
		if ($free_power < $energyscooped)
		$energyscooped = $free_power;
		$retvalue['scooped2'] = floor($energyscooped);
	}
	}

	if ($circuit == '2')
	{
	$triptime*=2;
	$triptime+=2;
	}
	else
	$triptime+=1;

	$retvalue['triptime'] = $triptime;
	$retvalue['scooped'] = $retvalue['scooped1'] + $retvalue['scooped2'];

	return $retvalue;
}


// perform trade route

function traderoute_engage($j, $ship_id)
{
	global $playerinfo, $shipinfo, $smarty;
	global $engage;
	global $traderoutes;
	global $fighter_price;
	global $torpedo_price;
	global $colonist_price;
	global $colonist_limit, $colonist_tech_add;
	global $inventory_factor;
	global $ore_price;
	global $ore_delta;
	global $ore_limit;
	global $organics_price;
	global $organics_delta;
	global $organics_limit;
	global $goods_price;
	global $goods_delta;
	global $goods_limit;
	global $energy_price;
	global $energy_delta;
	global $energy_limit;
	global $l_tdr_turnsused, $l_tdr_turnsleft, $l_tdr_credits, $l_tdr_profit, $l_tdr_cost, $l_tdr_totalprofit, $l_tdr_totalcost;
	global $l_tdr_planetisovercrowded, $l_tdr_engageagain, $l_tdr_onlyonewaytdr, $l_tdr_engagenonexist, $l_tdr_notowntdr;
	global $l_tdr_invalidspoint, $l_tdr_inittdr, $l_tdr_invalidsrc, $l_tdr_inittdrsector, $l_tdr_organics, $l_tdr_energy, $l_tdr_loaded;
	global $l_tdr_nothingtoload, $l_tdr_scooped, $l_tdr_dumped, $l_tdr_portisempty, $l_tdr_portisfull, $l_tdr_ore, $l_tdr_sold;
	global $l_tdr_goods, $l_tdr_notyourplanet, $l_tdr_invalidssector, $l_tdr_invaliddport, $l_tdr_invaliddplanet;
	global $l_tdr_invaliddsector, $l_tdr_nowlink1, $l_tdr_nowlink2, $l_tdr_moreturnsneeded, $l_tdr_tdrhostdef;
	global $l_tdr_globalsetbuynothing, $l_tdr_nosrcporttrade, $l_tdr_tradesrcportoutsider, $l_tdr_tdrres, $l_tdr_torps;
	global $l_tdr_nodestporttrade, $l_tdr_tradedestportoutsider, $l_tdr_portin, $l_tdr_planet, $l_tdr_bought, $l_tdr_colonists;
	global $l_tdr_fighters, $l_tdr_nothingtotrade, $l_submit, $l_tdr_nothingtodump, $l_tdr_timestorep;
	global $db, $dbtables;
	///
	global $spy_success_factor, $planet_detect_success1;
	global $goods_reducerate, $ore_reducerate, $organics_reducerate, $energy_reducerate;

	$dist['scooped'] = 0;
	$dist['scooped1'] = 0;
	$dist['scooped2'] = 0;
	$colonists_buy = 0;
	$fighters_buy = 0;
	$torps_buy = 0;

	$setcol=0;
	//10 pages of sanity checks! yeah!

	foreach($traderoutes as $testroute)
	{
		if ($testroute['traderoute_id'] == $engage)
			$traderoute = $testroute;
	}

	if (!isset($traderoute))
		traderoute_die($l_tdr_engagenonexist);

	if ($traderoute['owner'] != $playerinfo['player_id'])
		traderoute_die($l_tdr_notowntdr);


// ********************************
// ***** Source Check ************
// ********************************
	if ($traderoute['source_type'] == 'P')
	{
		//retrieve port info here, we'll need it later anyway
		$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$traderoute[source_id]");
		if (!$result || $result->EOF)
			traderoute_die($l_tdr_invalidspoint);

		$source = $result->fields;

		if ($traderoute['source_id'] != $shipinfo['sector_id'])
		{
			$l_tdr_inittdr = str_replace("[tdr_source_id]", $traderoute['source_id'], $l_tdr_inittdr);
			traderoute_die($l_tdr_inittdr);
		}
	}

	if($traderoute['source_type'] != 'P'){
		if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')	// get data from planet table
		{
			$result = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$traderoute[source_id]");
			if (!$result || $result->EOF)
				traderoute_die($l_tdr_invalidsrc);

			$source = $result->fields;

			if ($source['sector_id'] != $shipinfo['sector_id'])
			{
				$l_tdr_inittdrsector = str_replace("[tdr_source_sector_id]", $source['sector_id'], $l_tdr_inittdrsector);
				traderoute_die($l_tdr_inittdrsector);
			}

			if ($traderoute['source_type'] == 'L')
			{
				if ($source['owner'] != $playerinfo['player_id'])
				{
					$l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source['name'], $l_tdr_notyourplanet);
					$l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source['sector_id'], $l_tdr_notyourplanet);
					traderoute_die($l_tdr_notyourplanet);
				}
			}
			elseif ($traderoute['source_type'] == 'C')	 // check to make sure player and planet are in the same team.
			{
				if ($source['team'] != $playerinfo[team])
				{
					$l_tdr_notyourplanet = str_replace("[tdr_source_name]", $source['name'], $l_tdr_notyourplanet);
					$l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $source['sector_id'], $l_tdr_notyourplanet);
					$not_team_planet = "$source[name] in $source[sector_id] not a Copporate Planet";
					traderoute_die($not_team_planet);
				}
			}

			//store starting port info, we'll need it later
			$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$source[sector_id]");
			if (!$result || $result->EOF)
				traderoute_die($l_tdr_invalidssector);

			$sourceport = $result->fields;
		}
	}

// ********************************
// ***** Destination Check ********
// ********************************
	if ($traderoute['dest_type'] == 'P')
	{
		$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$traderoute[dest_id]");
		if (!$result || $result->EOF)
			traderoute_die($l_tdr_invaliddport);

		$dest = $result->fields;
	}


	if ($traderoute['dest_type'] != 'P'){
		if (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))	// get data from planet table
		{
			$result = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$traderoute[dest_id]");
			if (!$result || $result->EOF)
				traderoute_die($l_tdr_invaliddplanet);

			$dest = $result->fields;
			
			if ($traderoute['dest_type'] == 'L')
			{
				if ($dest['owner'] != $playerinfo['player_id'])
				{
					$l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
					$l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
					traderoute_die($l_tdr_notyourplanet);
				}
			}
			elseif ($traderoute['dest_type'] == 'C')	 // check to make sure player and planet are in the same team.
			{
				if ($dest['team'] != $playerinfo['team'])
				{
					$l_tdr_notyourplanet = str_replace("[tdr_source_name]", $dest['name'], $l_tdr_notyourplanet);
					$l_tdr_notyourplanet = str_replace("[tdr_source_sector_id]", $dest['sector_id'], $l_tdr_notyourplanet);
					$not_team_planet = "$dest[name] in $dest[sector_id] not a Copporate Planet";
					traderoute_die($not_team_planet);
				}
			}

			$result = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$dest[sector_id]");
			if (!$result || $result->EOF)
				traderoute_die($l_tdr_invaliddsector);

			$destport = $result->fields;
		}
	}

	if (!isset($sourceport))
		$sourceport=$source;
	if (!isset($destport))
		$destport=$dest;

// ***************************************************
// ***** Warp or RealSpace and generate distance *****
// ***************************************************
	if ($traderoute['move_type'] == 'W')
	{
		$query = $db->Execute("SELECT link_id FROM $dbtables[links] WHERE link_start=$source[sector_id] AND link_dest=$dest[sector_id]");
		if ($query->EOF)
		{
			$l_tdr_nowlink1 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink1);
			$l_tdr_nowlink1 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink1);
			traderoute_die($l_tdr_nowlink1);
		}
		if ($traderoute['circuit'] == '2')
		{
			$query = $db->Execute("SELECT link_id FROM $dbtables[links] WHERE link_start=$dest[sector_id] AND link_dest=$source[sector_id]");
			if ($query->EOF)
			{
				$l_tdr_nowlink2 = str_replace("[tdr_src_sector_id]", $source['sector_id'], $l_tdr_nowlink2);
				$l_tdr_nowlink2 = str_replace("[tdr_dest_sector_id]", $dest['sector_id'], $l_tdr_nowlink2);
				traderoute_die($l_tdr_nowlink2);
			}
			$dist['triptime'] = 4;
		}
		else
			$dist['triptime'] = 2;

		$dist['scooped'] = 0;
	}
	else
		$dist = traderoute_distance('P', 'P', $sourceport['sector_id'], $destport['sector_id'], $traderoute['circuit']);


// ********************************************
// ***** Check if player has enough turns *****
// ********************************************
	if ($playerinfo['turns'] < $dist['triptime'])
	{
		$l_tdr_moreturnsneeded = str_replace("[tdr_dist_triptime]", $dist['triptime'], $l_tdr_moreturnsneeded);
		$l_tdr_moreturnsneeded = str_replace("[tdr_playerinfo_turns]", $playerinfo['turns'], $l_tdr_moreturnsneeded);
		traderoute_die($l_tdr_moreturnsneeded);
	}


// ********************************
// ***** Sector Defense Check *****
// ********************************
	$hostile = 0;

	$result99 = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE sector_id = $source[sector_id] AND player_id <> $playerinfo[player_id]");
	if (!$result99->EOF)
	{
		 $fighters_owner = $result99->fields;
		 $nsresult = $db->Execute("SELECT * from $dbtables[players] where player_id=$fighters_owner[player_id]");
		 $nsfighters = $nsresult->fields;
		 if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
			$hostile = 1;
	}

	$result98 = $db->Execute("SELECT * FROM $dbtables[sector_defence] WHERE sector_id = $dest[sector_id] AND player_id <> $playerinfo[player_id]");
	if (!$result98->EOF)
	{
		 $fighters_owner = $result98->fields;
		 $nsresult = $db->Execute("SELECT * from $dbtables[players] where player_id=$fighters_owner[player_id]");
		 $nsfighters = $nsresult->fields;
		 if ($nsfighters['team'] != $playerinfo['team'] || $playerinfo['team']==0)
			$hostile = 1;
	}

	if ($hostile > 0)
		 traderoute_die($l_tdr_tdrhostdef);

// ***************************************
// ***** Upgrades Port Nothing to do *****
// ***************************************
	if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades' && $playerinfo['trade_colonists'] == 'N' && $playerinfo['trade_fighters'] == 'N' && $playerinfo['trade_torps'] == 'N')
		traderoute_die($l_tdr_globalsetbuynothing);


// *********************************************
// ***** Check if zone allows trading	SRC *****
// *********************************************
	if ($traderoute['source_type'] == 'P')
	{
		$res = $db->Execute("SELECT * FROM $dbtables[zones],$dbtables[universe] WHERE $dbtables[universe].sector_id=$traderoute[source_id] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
		$query97 = $res->fields;
		if ($query97['allow_trade'] == 'N')
			traderoute_die($l_tdr_nosrcporttrade);
		elseif ($query97['allow_trade'] == 'L')
		{
			if ($query97['team_zone'] == 'N')
			{
				$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$query97[owner]");
				$ownerinfo = $res->fields;

				if ($playerinfo['player_id'] != $query97['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
					traderoute_die($l_tdr_tradesrcportoutsider);
			}
			else
			{
				if ($playerinfo['team'] != $query97['owner'])
					traderoute_die($l_tdr_tradesrcportoutsider);
			}
		}
	}

// **********************************************
// ***** Check if zone allows trading	DEST *****
// **********************************************
	if ($traderoute['dest_type'] == 'P')
	{
		$res = $db->Execute("SELECT * FROM $dbtables[zones],$dbtables[universe] WHERE $dbtables[universe].sector_id=$traderoute[dest_id] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
		$query97 = $res->fields;
		if ($query97['allow_trade'] == 'N')
			traderoute_die($l_tdr_nodestporttrade);
		elseif ($query97['allow_trade'] == 'L')
		{
			if ($query97[team_zone] == 'N')
			{
				$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$query97[owner]");
				$ownerinfo = $res->fields;

				if ($playerinfo['player_id'] != $query97['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
					traderoute_die($l_tdr_tradedestportoutsider);
			}
			else
			{
				if ($playerinfo['team'] != $query97['owner'])
					traderoute_die($l_tdr_tradedestportoutsider);
			}
		}
	}

// **********************************************
// ***** Check if player has a loan pending *****
// **********************************************

	if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades' && isLoanPending($playerinfo['player_id']))
	{
		global $l_port_loannotrade, $l_igb_term;
		traderoute_die("$l_port_loannotrade<p><A HREF=igb.php>$l_igb_term</a><p>");
	}

// *******************************************
// ***** Check if player has a fedbounty *****
// *******************************************

	if ($traderoute['source_type'] == 'P' && $source['port_type'] == 'upgrades')
	{
		global $l_port_bounty, $l_port_bounty2, $l_by_placebounty;
		$res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
		if ($res2)
		{
			$bty = $res2->fields;
			if ($bty['total_bounty'] > 0)
			{
				$l_port_bounty2 = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_bounty2);
				traderoute_die("$l_port_bounty $l_port_bounty2 <BR> <A HREF=\"bounty.php\">$l_by_placebounty</A><BR><BR>");
			}
		}
	}

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
//--------- We're done with checks!	All that's left is to make it happen --------
//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
	echo "
		<table border=1 cellspacing=1 cellpadding=2 width=\"65%\" align=center bgcolor=\"#000000\">
		<tr bgcolor=\"#400040\"><td align=\"center\" colspan=2><b>$l_tdr_tdrres</b></td></tr>
		<tr align=center bgcolor=\"#400040\">
		<td width=\"50%\"><b>
		";


// ------------ Determine if Source is Planet or Port
	if ($traderoute['source_type'] == 'P')
		echo "$l_tdr_portin $source[sector_id]";
	elseif (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
		echo "$l_tdr_planet $source[name] in $sourceport[sector_id]";

	echo '
		</b></td>
		<td width="50%"><b>
		';

// ------------ Determine if Destination is Planet or Port
	if ($traderoute['dest_type'] == 'P')
		echo "$l_tdr_portin $dest[sector_id]";
	elseif (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
		echo "$l_tdr_planet $dest[name] in $destport[sector_id]";

	echo '
		</b></td>
		</tr><tr bgcolor="#300030">
		<td align=center>
		';

	$sourcecost=0;

//-------- Source is Port ------------
//-------- Source is Port ------------
//-------- Source is Port ------------

	if ($traderoute['source_type'] == 'P')
	{
		$debug_query = $db->execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
		db_op_result($debug_query,__LINE__,__FILE__);
		$shipinfo = $debug_query->fields;

		//-------- Upgrade Port Section (begin) ------
		if ($source['port_type'] == 'upgrades')
		{
			$total_credits = $playerinfo['credits'];

			if ($playerinfo['trade_colonists'] == 'Y')
			{
				$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
				$colonists_buy = $free_holds;

				if ($playerinfo['credits'] < $colonist_price * $colonists_buy)
					$colonists_buy = $playerinfo['credits'] / $colonist_price;

				if ($colonists_buy > 0)
					echo "$l_tdr_bought " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
				else $colonists_buy = 0;

				$sourcecost-=$colonists_buy * $colonist_price;
				$total_credits-=$colonists_buy * $colonist_price;
			}
			else
				$colonists_buy = 0;

			if ($playerinfo['trade_fighters'] == 'Y')
			{
				$free_fighters = NUM_FIGHTERS($shipinfo['computer']) - $shipinfo['fighters'];
				$fighters_buy = $free_fighters;

				if ($total_credits < $fighters_buy * $fighter_price)
					$fighters_buy = $total_credits / $fighter_price;

				if ($fighters_buy > 0)
					echo "$l_tdr_bought " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";
				else $fighters_buy = 0;

				$sourcecost-=$fighters_buy * $fighter_price;
				$total_credits-=$fighters_buy * $fighter_price;
			}
			else
				$fighters_buy = 0;

			if ($playerinfo['trade_torps'] == 'Y')
			{
				$free_torps = NUM_FIGHTERS($shipinfo['torp_launchers']) - $shipinfo['torps'];
				$torps_buy = $free_torps;

				if ($total_credits < $torps_buy * $torpedo_price)
					$torps_buy = $total_credits / $torpedo_price;

				if ($torps_buy > 0)
					echo "$l_tdr_bought " . NUMBER($torps_buy) . " $l_tdr_torps<br>";
				else $torps_buy = 0;

				$sourcecost-=$torps_buy * $torpedo_price;
			}
			else
				$torps_buy = 0;

			if ($torps_buy == 0 && $colonists_buy == 0 && $fighters_buy == 0)
				echo "$l_tdr_nothingtotrade<br>";

			if ($traderoute['circuit'] == '1')
				{
					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET colonists=colonists+$colonists_buy, fighters=fighters+$fighters_buy,torps=torps+$torps_buy WHERE ship_id=$shipinfo[ship_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}
		}

//-------- Upgrade Port Section (end) ------
//-------- Upgrade Port Section (end) ------
//-------- Upgrade Port Section (end) ------



//-------- Normal Port Section (begin) ------
//-------- Normal Port Section (begin) ------
//-------- Normal Port Section (begin) ------
		if ($source['port_type'] != 'upgrades')
		{
			// sells commodities from your ship to the source port
			// sells commodities from your ship to the source port
			// sells commodities from your ship to the source port

			// initialize variables to 0, prevents sql error.
			$ore_buy = 0;
			$goods_buy = 0;
			$organics_buy = 0;
			$energy_buy = 0;
			$portfull = 0;

			$neworeprice = $source['ore_price'];
			if ($source['port_type'] != 'ore')
			{
				$ore_price1 = $source['ore_price'] + $ore_price + $ore_delta * $ore_limit / $ore_limit * $inventory_factor;

				if($ore_price1 <= 0)
					$ore_price1 = 0.01;

				if ($source['port_ore'] - $shipinfo['ore'] < 0)
				{
					$ore_buy = $source['port_ore'];
					$portfull = 1;
				}
				else
					$ore_buy = $shipinfo['ore'];

				$sourcecost += $ore_buy * $ore_price1;
				if ($ore_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c<br>";
				}
				$neworeprice = $source['ore_price'] - ($ore_reducerate * $shipinfo['ore'] / 10000);
				$shipinfo['ore'] -= $ore_buy;
			}

			$newgoodsprice = $source['goods_price'];
			$portfull = 0;
			if ($source['port_type'] != 'goods')
			{
				$goods_price1 = $source['goods_price'] + $goods_price + $goods_delta * $goods_limit / $goods_limit * $inventory_factor;

				if($goods_price1 <= 0)
					$goods_price1 = 0.01;

				if ($source['port_goods'] - $shipinfo['goods'] < 0)
				{
					$goods_buy = $source['port_goods'];
					$portfull = 1;
				}
				else
					$goods_buy = $shipinfo['goods'];

				$sourcecost += $goods_buy * $goods_price1;
				if ($goods_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c<br>";
				}
				$newgoodsprice = $source['goods_price'] - ($goods_reducerate * $shipinfo['goods'] / 10000);
				$shipinfo['goods'] -= $goods_buy;
			}

			$neworganicsprice = $source['organics_price'];
			$portfull = 0;
			if ($source['port_type'] != 'organics')
			{
				$organics_price1 = $source['organics_price'] + $organics_price + $organics_delta * $organics_limit / $organics_limit * $inventory_factor;

				if($organics_price1 <= 0)
					$organics_price1 = 0.01;

				if ($source['port_organics'] - $shipinfo['organics'] < 0)
				{
					$organics_buy = $source['port_organics'];
					$portfull = 1;
				}
				else
					$organics_buy = $shipinfo['organics'];

				$sourcecost += $organics_buy * $organics_price1;
				if ($organics_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c<br>";
				}
				$neworganicsprice = $source['organics_price'] - ($organics_reducerate * $shipinfo['organics'] / 10000);
				$shipinfo['organics'] -= $organics_buy;
			}

			$newenergyprice = $source['energy_price'];
			$portfull = 0;
			if ($source['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
			{
				$energy_price1 = $source['energy_price'] + $energy_price + $energy_delta * $energy_limit / $energy_limit * $inventory_factor;

				if($energy_price1 <= 0)
					$energy_price1 = 0.01;

				if ($source['port_energy'] - $shipinfo['energy'] < 0)
				{
					$energy_buy = $source['port_energy'];
					$portfull = 1;
				}
				else
					$energy_buy = $shipinfo['energy'];

				$sourcecost += $energy_buy * $energy_price1;
				if ($energy_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c<br>";
				}
				$newenergyprice = $source['energy_price'] - ($energy_reducerate * $shipinfo['energy'] / 10000);
				$shipinfo['energy'] -= $energy_buy;
			}
			else
				$energy_buy = 0;

			$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
			$trade_date = date("Y-m-d H:i:s");

			// buys commodities from the source port
			// buys commodities from the source port
			// buys commodities from the source port

			if ($source['port_type'] == 'ore')
			{
				$ore_price1 = $ore_price - $ore_delta * $source['port_ore'] / $ore_limit * $inventory_factor;

				$ore_buy = $free_holds;
				if ($playerinfo['credits'] + $sourcecost < $ore_buy * $ore_price1)
					$ore_buy = ($playerinfo['credits'] + $sourcecost) / $ore_price1;
				if ($source['port_ore'] < $ore_buy)
				{
					$ore_buy = $source['port_ore'];
					if ($source[port_ore] == 0)
						echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c ($l_tdr_portisempty)<br>";
				}
				if ($ore_buy != 0)
					echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c<br>";

				$shipinfo['ore'] += $ore_buy;
				$sourcecost -= $ore_buy * $ore_price1;
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$source[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($source['port_type'] == 'goods')
			{
				$goods_price1 = $goods_price - $goods_delta * $source['port_goods'] / $goods_limit * $inventory_factor;

				$goods_buy = $free_holds;
				if ($playerinfo['credits'] + $sourcecost < $goods_buy * $goods_price1)
					$goods_buy = ($playerinfo['credits'] + $sourcecost) / $goods_price1;
				if ($source['port_goods'] < $goods_buy)
				{
					$goods_buy = $source['port_goods'];
					if ($source['port_goods'] == 0)
						echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c ($l_tdr_portisempty)<br>";
				}
				if ($goods_buy != 0)
					echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c<br>";

				$shipinfo['goods'] += $goods_buy;
				$sourcecost -= $goods_buy * $goods_price1;
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$source[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($source['port_type'] == 'organics')
			{
				$organics_price1 = $organics_price - $organics_delta * $source['port_organics'] / $organics_limit * $inventory_factor;

				$organics_buy = $free_holds;
				if ($playerinfo['credits'] + $sourcecost < $organics_buy * $organics_price1)
					$organics_buy = ($playerinfo['credits'] + $sourcecost) / $organics_price1;
				if ($source['port_organics'] < $organics_buy)
				{
					$organics_buy = $source['port_organics'];
					if ($source['port_organics'] == 0)
						echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c ($l_tdr_portisempty)<br>";
				}
				if ($organics_buy != 0)
					echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c<br>";

				$shipinfo['organics'] += $organics_buy;
				$sourcecost -= $organics_buy * $organics_price1;
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$source[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($source['port_type'] == 'energy')
			{
				$energy_price1 = $energy_price - $energy_delta * $source['port_energy'] / $energy_limit * $inventory_factor;

				$energy_buy = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'] - $dist['scooped1'];
				if ($playerinfo['credits'] + $sourcecost < $energy_buy * $energy_price1)
					$energy_buy = ($playerinfo['credits'] + $sourcecost) / $energy_price1;
				if ($source['port_energy'] < $energy_buy)
				{
					$energy_buy = $source['port_energy'];
					if ($source['port_energy'] == 0)
						echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c ($l_tdr_portisempty)<br>";
				}
				if ($energy_buy != 0)
					echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c<br>";

				$shipinfo['energy'] += $energy_buy;
				$sourcecost -= $energy_buy * $energy_price1;
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$source[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
				echo "$l_tdr_nothingtotrade<br>";

			if ($traderoute['circuit'] == '1')
			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=GREATEST($shipinfo[ore], 0), goods=GREATEST($shipinfo[goods], 0), organics=GREATEST($shipinfo[organics], 0) WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
	}
//------------- Source is port (end) ---------
//------------- Source is port (end) ---------
//------------- Source is port (end) ---------


//------------- Source is planet (begin) -----
//------------- Source is planet (begin) -----
//------------- Source is planet (begin) -----
	if ($traderoute['source_type'] != 'P')
	{
		$debug_query = $db->execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
		db_op_result($debug_query,__LINE__,__FILE__);
		$shipinfo = $debug_query->fields;

		if (($traderoute['source_type'] == 'L') || ($traderoute['source_type'] == 'C'))
		{
			$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
			$goods_buy = 0;
			$ore_buy = 0;
			$organics_buy = 0;

			if ($traderoute['dest_type'] == 'P')
			{
				//pick stuff up to sell at port
				if (($playerinfo['player_id'] == $source['owner']) || ($playerinfo['team'] == $source['team']))
				{
					if ($source['goods'] > 0 && $free_holds > 0 && $dest['port_type'] != 'goods')
					{
						if ($source['goods'] > $free_holds)
							$goods_buy = $free_holds;
						else
							$goods_buy = $source['goods'];

						$free_holds -= $goods_buy;
						$shipinfo['goods'] += $goods_buy;
						echo "$l_tdr_loaded " . NUMBER($goods_buy) . " $l_tdr_goods<br>";
					}
					else
						$goods_buy = 0;

					if ($source['ore'] > 0 && $free_holds > 0 && $dest['port_type'] != 'ore')
					{
						if ($source['ore'] > $free_holds)
							$ore_buy = $free_holds;
						else
							$ore_buy = $source['ore'];

						$free_holds -= $ore_buy;
						$shipinfo['ore'] += $ore_buy;
						echo "$l_tdr_loaded " . NUMBER($ore_buy) . " $l_tdr_ore<br>";
					}
					else
						$ore_buy = 0;

					if ($source['organics'] > 0 && $free_holds > 0 && $dest['port_type'] != 'organics')
					{
						if ($source['organics'] > $free_holds)
							$organics_buy = $free_holds;
						else
							$organics_buy = $source['organics'];

						$free_holds -= $organics_buy;
						$shipinfo['organics'] += $organics_buy;
						echo "$l_tdr_loaded " . NUMBER($organics_buy) . " $l_tdr_organics<br>";
					}
					else
						$organics_buy = 0;

					if ($ore_buy == 0 && $goods_buy == 0 && $organics_buy == 0)
						echo "$l_tdr_nothingtoload<br>";
	
					if ($traderoute['circuit'] == '1')
					{
						$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=GREATEST($shipinfo[ore], 0), goods=GREATEST($shipinfo[goods], 0), organics=GREATEST($shipinfo[organics], 0) WHERE ship_id=$shipinfo[ship_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
					}
				}
			}

			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET ore=GREATEST(ore-$ore_buy, 0), goods=GREATEST(goods-$goods_buy, 0), organics=GREATEST(organics-$organics_buy, 0) WHERE planet_id=$source[planet_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			///
			if ($spy_success_factor)
			{
				//echo "Start : $source[planet_id], $shipinfo[ship_id], $planet_detect_success1<BR>";
				spy_sneak_to_planet($source['planet_id'], $shipinfo['ship_id']);
				spy_sneak_to_ship($source['planet_id'], $shipinfo['ship_id']);
				spy_detect_planet($shipinfo['ship_id'], $source['planet_id'], $planet_detect_success1);
			}
		}

// ---------- destination is a planet, so load cols and weapons
// ---------- destination is a planet, so load cols and weapons
// ---------- destination is a planet, so load cols and weapons

		if (($traderoute['dest_type'] == 'L') || ($traderoute['dest_type'] == 'C'))
		{
			$debug_query = $db->execute("SELECT * FROM $dbtables[ships] WHERE ship_id=$ship_id");
			db_op_result($debug_query,__LINE__,__FILE__);
			$shipinfo = $debug_query->fields;
			$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
//echo "Ship Colonists: $shipinfo[colonists]<br>";
//echo "Free holds: $free_holds<br>";
			$colonists_buy = 0;
			$torps_buy = 0;
			$fighters_buy = 0;
			if ($source['colonists'] > 0 && $free_holds > 0 && $playerinfo['trade_colonists'] == 'Y')
			{
				if ($source['colonists'] > $free_holds)
					$colonists_buy = $free_holds;
				else
					$colonists_buy = $source['colonists'];

				$free_holds -= $colonists_buy;
				$shipinfo['colonists'] += $colonists_buy;
				echo "$l_tdr_loaded " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
			}
			else
				$colonists_buy = 0;

			$free_torps = NUM_TORPEDOES($shipinfo['torp_launchers']) - $shipinfo['torps'];
			if ($source['torps'] > 0 && $free_torps > 0 && $playerinfo['trade_torps'] == 'Y')
			{
				if ($source['torps'] > $free_torps)
					$torps_buy = $free_torps;
				else
					$torps_buy = $source['torps'];

				$free_torps -= $torps_buy;
				$shipinfo['torps'] += $torps_buy;
				echo "$l_tdr_loaded " . NUMBER($torps_buy) . " $l_tdr_torps<br>";
			}
			else
				$torps_buy = 0;

			$free_fighters = NUM_FIGHTERS($shipinfo['computer']) - $shipinfo['fighters'];
			if ($source['fighters'] > 0 && $free_fighters > 0 && $playerinfo['trade_fighters'] == 'Y')
			{
				if ($source['fighters'] > $free_fighters)
					$fighters_buy = $free_fighters;
				else
					$fighters_buy = $source['fighters'];

				$free_fighters -= $fighters_buy;
				$shipinfo['fighters'] += $fighters_buy;
				echo "$l_tdr_loaded " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";
			}
			else
				$fighters_buy = 0;

			if ($fighters_buy == 0 && $torps_buy == 0 && $colonists_buy == 0)
				echo "$l_tdr_nothingtoload<br>";

//			if ($traderoute['circuit'] == '1')
//			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET torps=GREATEST($shipinfo[torps], 0), fighters=GREATEST($shipinfo[fighters], 0), colonists=GREATEST($shipinfo[colonists], 0) WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->execute("SELECT torps, fighters, colonists FROM $dbtables[ships] WHERE ship_id=$ship_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				$shipinfo['torps'] = $debug_query->fields['torps'];
				$shipinfo['fighters'] = $debug_query->fields['fighters'];
				$shipinfo['colonists'] = $debug_query->fields['colonists'];
//			}
			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists=GREATEST(colonists-$colonists_buy, 0), torps=GREATEST(torps-$torps_buy, 0), fighters=GREATEST(fighters-$fighters_buy, 0) WHERE planet_id=$source[planet_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			///
			if ($spy_success_factor)
			{
				//echo "Start pl : $source[planet_id], $shipinfo[ship_id], $planet_detect_success1<BR>";
				spy_sneak_to_planet($source['planet_id'], $shipinfo['ship_id']);
				spy_sneak_to_ship($source['planet_id'], $shipinfo['ship_id']);
				spy_detect_planet($shipinfo['ship_id'], $source['planet_id'], $planet_detect_success1);
			}
		}
	}
// ----	energy scooped on the way to the destination
// ----	energy scooped on the way to the destination
// ----	energy scooped on the way to the destination
	if ($dist['scooped1'] != 0 and $shipinfo['dev_fuelscoop'] == "Y"){
		$scoopedenergy = $dist['scooped1'];
		$shipinfo['energy']+= $dist['scooped1'];

		if ($shipinfo['energy'] > NUM_ENERGY($shipinfo['power'])){
			$scoopedenergy = $dist['scooped1'] - ($shipinfo['energy'] - NUM_ENERGY($shipinfo['power']));
			$shipinfo['energy'] = NUM_ENERGY($shipinfo['power']);
		}
		echo "$l_tdr_scooped " . NUMBER($scoopedenergy) . " $l_tdr_energy<br>";
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST($shipinfo[energy], 0) WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}else{
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST($shipinfo[energy], 0) WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	echo '
		</td>
		<td align=center>
	';


// Return to source from destination
// Return to source from destination
// Return to source from destination

	if ($traderoute['circuit'] == '2')
	{
		$playerinfo['credits'] += $sourcecost;
		$destcost = 0;
		if ($traderoute['dest_type'] == 'P')
		{
			//sells commodities from the ship to the port
			//sells commodities from the ship to the port
			//sells commodities from the ship to the port
			$ore_buy = 0;
			$goods_buy = 0;
			$organics_buy = 0;
			$energy_buy = 0;
			$neworeprice = $dest['ore_price'];
			$portfull = 0;
			if ($dest['port_type'] != 'ore')
			{
				$ore_price1 = $dest['ore_price'] + $ore_price + $ore_delta * $ore_limit / $ore_limit * $inventory_factor;

				if($ore_price1 <= 0)
					$ore_price1 = 0.01;

				if ($dest['port_ore'] - $shipinfo['ore'] < 0)
				{
					$ore_buy = $dest['port_ore'];
					$portfull = 1;
				}
				else
					$ore_buy = $shipinfo['ore'];

				$destcost += $ore_buy * $ore_price1;
				if ($ore_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c<br>";
				}
				$neworeprice = $dest['ore_price'] - ($ore_reducerate * $shipinfo['ore'] / 10000);
				$shipinfo['ore'] -= $ore_buy;
			}

			$newgoodsprice = $dest['goods_price'];
			$portfull = 0;
			if ($dest['port_type'] != 'goods')
			{
				$goods_price1 = $dest['goods_price'] + $goods_price + $goods_delta * $goods_limit / $goods_limit * $inventory_factor;

				if($goods_price1 <= 0)
					$goods_price1 = 0.01;

				if ($dest['port_goods'] - $shipinfo['goods'] < 0)
				{
					$goods_buy = $dest['port_goods'];
					$portfull = 1;
				}
				else
					$goods_buy = $shipinfo['goods'];

				$destcost += $goods_buy * $goods_price1;
				if ($goods_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c<br>";
				}
				$newgoodsprice = $dest['goods_price'] - ($goods_reducerate * $shipinfo['goods'] / 10000);
				$shipinfo['goods'] -= $goods_buy;
			}

			$neworganicsprice = $dest['organics_price'];
			$portfull = 0;
			if ($dest['port_type'] != 'organics')
			{
				$organics_price1 = $dest['organics_price'] + $organics_price + $organics_delta * $organics_limit / $organics_limit * $inventory_factor;

				if($organics_price1 <= 0)
					$organics_price1 = 0.01;

				if ($dest['port_organics'] - $shipinfo['organics'] < 0)
				{
					$organics_buy = $dest['port_organics'];
					$portfull = 1;
				}
				else
					$organics_buy = $shipinfo['organics'];

				$destcost += $organics_buy * $organics_price1;
				if ($organics_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c<br>";
				}
				$neworganicsprice = $dest['organics_price'] - ($organics_reducerate * $shipinfo['organics'] / 10000);
				$shipinfo['organics'] -= $organics_buy;
			}

			$newenergyprice = $dest['energy_price'];
			$portfull = 0;
			if ($dest['port_type'] != 'energy' && $playerinfo['trade_energy'] == 'Y')
			{
				$energy_price1 = $dest['energy_price'] + $energy_price + $energy_delta * $energy_limit / $energy_limit * $inventory_factor;

				if($energy_price1 <= 0)
					$energy_price1 = 0.01;

				if ($dest['port_energy'] - $shipinfo['energy'] < 0)
				{
					$energy_buy = $dest['port_energy'];
					$portfull = 1;
				}
				else
					$energy_buy = $shipinfo['energy'];

				$destcost += $energy_buy * $energy_price1;
				if ($energy_buy != 0)
				{
					if ($portfull == 1)
						echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c ($l_tdr_portisfull)<br>";
					else
						echo "$l_tdr_sold " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c<br>";
				}
				$newenergyprice = $dest['energy_price'] - ($energy_reducerate * $shipinfo['energy'] / 10000);
				$shipinfo['energy'] -= $energy_buy;
			}
			else
				$energy_buy = 0;

			$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
			$trade_date = date("Y-m-d H:i:s");

			// ship buys commodities from the port
			// ship buys commodities from the port
			// ship buys commodities from the port

			if ($dest['port_type'] == 'ore')
			{
				$ore_price1 = $ore_price - $ore_delta * $dest['port_ore'] / $ore_limit * $inventory_factor;

				if ($traderoute['source_type'] == 'L')
					$ore_buy = 0;
				else
				{
					$ore_buy = $free_holds;
					if ($playerinfo['credits'] + $destcost < $ore_buy * $ore_price1)
					$ore_buy = ($playerinfo['credits'] + $destcost) / $ore_price1;
					if ($dest['port_ore'] < $ore_buy)
					{
						$ore_buy = $dest['port_ore'];
						if ($dest['port_ore'] == 0)
							echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c ($l_tdr_portisempty)<br>";
					}
					if ($ore_buy != 0)
						echo "$l_tdr_bought " . NUMBER($ore_buy) . " $l_tdr_ore @ " . floor($ore_price1) . "c<br>";
					$shipinfo['ore'] += $ore_buy;
					$destcost -= $ore_buy * $ore_price1;
				}
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$dest[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($dest['port_type'] == 'goods')
			{
				$goods_price1 = $goods_price - $goods_delta * $dest['port_goods'] / $goods_limit * $inventory_factor;

				if ($traderoute['source_type'] == 'L')
					$goods_buy = 0;
				else
				{
					$goods_buy = $free_holds;
					if ($playerinfo['credits'] + $destcost < $goods_buy * $goods_price1)
						$goods_buy = ($playerinfo['credits'] + $destcost) / $goods_price1;
					if ($dest['port_goods'] < $goods_buy)
					{
						$goods_buy = $dest['port_goods'];
						if ($dest['port_goods'] == 0)
							echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c ($l_tdr_portisempty)<br>";
					}
					if ($goods_buy != 0)
						echo "$l_tdr_bought " . NUMBER($goods_buy) . " $l_tdr_goods @ " . floor($goods_price1) . "c<br>";
					$shipinfo['goods'] += $goods_buy;
					$destcost -= $goods_buy * $goods_price1;
				}
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$dest[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($dest['port_type'] == 'organics')
			{
				$organics_price1 = $organics_price - $organics_delta * $dest['port_organics'] / $organics_limit * $inventory_factor;

				if ($traderoute['source_type'] == 'L')
					$organics_buy = 0;
				else
				{
					$organics_buy = $free_holds;
					if ($playerinfo['credits'] + $destcost < $organics_buy * $organics_price1)
						$organics_buy = ($playerinfo['credits'] + $destcost) / $organics_price1;
					if ($dest['port_organics'] < $organics_buy)
					{
						$organics_buy = $dest['port_organics'];
						if ($dest['port_organics'] == 0)
							echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c ($l_tdr_portisempty)<br>";
					}
					if ($organics_buy != 0)
						echo "$l_tdr_bought " . NUMBER($organics_buy) . " $l_tdr_organics @ " . floor($organics_price1) . "c<br>";
					$shipinfo['organics'] += $organics_buy;
					$destcost -= $organics_buy * $organics_price1;
				}
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$dest[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($dest['port_type'] == 'energy')
			{
				$energy_price1 = $energy_price - $energy_delta * $dest['port_energy'] / $energy_limit * $inventory_factor;

				if ($traderoute['source_type'] == 'L')
					$energy_buy = 0;
				else
				{
					$energy_buy = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];
					if ($playerinfo['credits'] + $destcost < $energy_buy * $energy_price1)
						$energy_buy = ($playerinfo['credits'] + $destcost) / $energy_price1;
					if ($dest['port_energy'] < $energy_buy)
					{
						$energy_buy = $dest['port_energy'];
						if ($dest['port_energy'] == 0)
							echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c ($l_tdr_portisempty)<br>";
					}
					if ($energy_buy != 0)
						echo "$l_tdr_bought " . NUMBER($energy_buy) . " $l_tdr_energy @ " . floor($energy_price1) . "c<br>";
					$shipinfo['energy'] += $energy_buy;
					$destcost -= $energy_buy * $energy_price1;
				}

				if ($ore_buy == 0 && $goods_buy == 0 && $energy_buy == 0 && $organics_buy == 0)
					echo "$l_tdr_nothingtotrade<br>";

				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$ore_buy, 0), port_energy=GREATEST(port_energy-$energy_buy, 0), port_goods=GREATEST(port_goods-$goods_buy, 0), port_organics=GREATEST(port_organics-$organics_buy, 0) WHERE sector_id=$dest[sector_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=$shipinfo[ore], goods=$shipinfo[goods], organics=$shipinfo[organics] WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

// destination is a planet
// destination is a planet
// destination is a planet
		if ($traderoute['dest_type'] != 'P')
		{
				if ($traderoute['source_type'] == 'L'	|| $traderoute['source_type'] == 'C')
				{
				$colonists_buy=0;
				$fighters_buy=0;
				$torps_buy=0;
			}

			if ($playerinfo['trade_colonists'] == 'Y')
			{
				$colonists_buy += $shipinfo['colonists'];
				$col_dump = $shipinfo['colonists'];
				$averagetechlvl = ($dest['computer'] + $dest['sensors'] + $dest['beams'] + $dest['torp_launchers'] + $dest['shields'] + $dest['jammer'] + $dest['cloak']) / 7;
				if ($dest['colonists'] + $colonists_buy >= ($colonist_limit + floor($colonist_tech_add * $averagetechlvl)))
				{
					$exceeding = $dest['colonists'] + $colonists_buy - ($colonist_limit + floor($colonist_tech_add * $averagetechlvl));
					$col_dump = $exceeding;
					$setcol = 1;
					$colonists_buy-=$exceeding;
					if ($colonists_buy < 0){
						$colonists_buy = 0;
						$col_dump = $shipinfo['colonists'];
					}
				}
			}
			else
				$col_dump = 0;

			if ($colonists_buy != 0)
			{
				if ($setcol ==1)
					echo "$l_tdr_dumped " . NUMBER($colonists_buy) . " $l_tdr_colonists ($l_tdr_planetisovercrowded)<br>";
				else
					echo "$l_tdr_dumped " . NUMBER($colonists_buy) . " $l_tdr_colonists<br>";
			}

			if ($playerinfo['trade_fighters'] == 'Y')
			{
				$fighters_buy += $shipinfo['fighters'];
				$fight_dump = $shipinfo['fighters'];
			}
			else
				$fight_dump = 0;

			if ($fighters_buy != 0)
				echo "$l_tdr_dumped " . NUMBER($fighters_buy) . " $l_tdr_fighters<br>";

			if ($playerinfo['trade_torps'] == 'Y')
			{
				$torps_buy += $shipinfo['torps'];
				$torps_dump = $shipinfo['torps'];
			}
			else
				$torps_dump = 0;

			if ($torps_buy != 0)
				echo "$l_tdr_dumped " . NUMBER($torps_buy) . " $l_tdr_torps<br>";

			if ($torps_buy == 0 && $fighters_buy == 0 && $colonists_buy == 0 && $organics_buy == 0)
				echo "$l_tdr_nothingtodump<br>";

			if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
			{
				if ($playerinfo['trade_colonists'] == 'Y')
				{
					if ($setcol != 1)
						$col_dump = 0;
				}
				else
					$col_dump = $shipinfo['colonists'];

				if ($playerinfo['trade_fighters'] == 'Y')
					$fight_dump = 0;
				else
					$fight_dump = $shipinfo['fighters'];

				if ($playerinfo['trade_torps'] == 'Y')
					$torps_dump = 0;
				else
					$torps_dump = $shipinfo['torps'];
			}

			$debug_query = $db->Execute("UPDATE $dbtables[planets] SET colonists=GREATEST(colonists+$colonists_buy, 0), fighters=GREATEST(fighters+$fighters_buy, 0), torps=GREATEST(torps+$torps_buy, 0) WHERE planet_id=$traderoute[dest_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			///
			if ($spy_success_factor)
			{
				//echo "Finish : $traderoute[dest_id], $shipinfo[ship_id], $planet_detect_success1<BR>";
				spy_sneak_to_planet($traderoute['dest_id'], $shipinfo['ship_id']);
				spy_sneak_to_ship($traderoute['dest_id'], $shipinfo['ship_id']);
				spy_detect_planet($shipinfo['ship_id'], $traderoute['dest_id'], $planet_detect_success1);
			}

			if ($traderoute['source_type'] == 'L' || $traderoute['source_type'] == 'C')
			{
				$debug_query = $db->Execute("UPDATE $dbtables[ships] SET colonists=GREATEST($col_dump, 0), fighters=GREATEST($fight_dump, 0), torps=GREATEST($torps_dump, 0) WHERE ship_id=$shipinfo[ship_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				if ($setcol == 1)
				 {
					$debug_query = $db->Execute("UPDATE $dbtables[ships] SET colonists=GREATEST($col_dump, 0), fighters=GREATEST(fighters-$fight_dump, 0), torps=GREATEST(torps-$torps_dump, 0) WHERE ship_id=$shipinfo[ship_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				 }
					else
				 {
					 $debug_query = $db->Execute("UPDATE $dbtables[ships] SET colonists=GREATEST(colonists-$col_dump, 0), fighters=GREATEST(fighters-$fight_dump, 0), torps=GREATEST(torps-$torps_dump, 0) WHERE ship_id=$shipinfo[ship_id]");
					 db_op_result($debug_query,__LINE__,__FILE__);
				 }
			}
		}

//echo"$dist[scooped2]<br>";
	if ($dist['scooped2'] != 0 and $shipinfo['dev_fuelscoop'] == "Y"){
		$scoopedenergy = $dist['scooped2'];
		$shipinfo['energy']+= $dist['scooped2'];
//echo"$shipinfo[energy]<br>";
//echo NUM_ENERGY($shipinfo['power'])."<br>";
//echo ($shipinfo['energy'] - NUM_ENERGY($shipinfo['power']))."<br>";
		if ($shipinfo['energy'] > NUM_ENERGY($shipinfo['power'])){
			$scoopedenergy = $dist['scooped2'] - ($shipinfo['energy'] - NUM_ENERGY($shipinfo['power']));
//echo"$scoopedenergy<br>";
			$shipinfo['energy'] = NUM_ENERGY($shipinfo['power']);
		}
		echo "$l_tdr_scooped " . NUMBER($scoopedenergy) . " $l_tdr_energy<br>";
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST($shipinfo[energy], 0) WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}else{
		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET energy=GREATEST($shipinfo[energy], 0) WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}


	}
	else
	{
		echo $l_tdr_onlyonewaytdr;
		$destcost = 0;
	}

	echo "</td></tr><tr bgcolor=\"#400040\"><td align=center>";

	if ($sourcecost > 0)
		echo "$l_tdr_profit : " . NUMBER(abs($sourcecost));
	else
		echo "$l_tdr_cost : " . NUMBER(abs($sourcecost));

	echo "</td><td align=center>";

	if ($destcost > 0)
		echo "$l_tdr_profit : " . NUMBER(abs($destcost));
	else
		echo "$l_tdr_cost : " . NUMBER(abs($destcost));

	echo '
		</td></tr>
		</table>
		<p>
		<center>
		<b>
		';

	$total_profit = $sourcecost + $destcost;
	if ($total_profit > 0)
		echo "$l_tdr_totalprofit : " . NUMBER(abs($total_profit)) . "</b><br>";
///- end with <p> instead of <br> above.
	else
		echo "$l_tdr_totalcost : " . NUMBER(abs($total_profit)) . "</b><br>";

	if ($traderoute['circuit'] == '1')
		$newsec = $destport['sector_id'];
	else
		$newsec = $sourceport['sector_id'];

	$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-$dist[triptime], credits=GREATEST(credits+$total_profit, 0), turns_used=turns_used+$dist[triptime] WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[ships] SET sector_id=$newsec WHERE ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$playerinfo['credits']+=$total_profit - $sourcecost;
	$playerinfo['turns']-=$dist['triptime'];

	echo "<b>$l_tdr_turnsused : $dist[triptime]</b><br>";
	echo "<b>$l_tdr_turnsleft : $playerinfo[turns]</b><br>";
///- add a <p> before ending.

	echo "<b>$l_tdr_credits : " . NUMBER($playerinfo['credits']) . "</b><br><br></center>";

 // stupid user limiter.
	if ( ($playerinfo['turns'] <= 0) || ($playerinfo['credits'] < 0) || (($traderoute['source_type'] != 'L') && ($traderoute['source_type'] != 'C') && ($traderoute['dest_type'] != 'L') && ($traderoute['dest_type'] != 'C') && (($total_profit <= 0) || (($ore_buy == 0) && ($goods_buy == 0) && ($organics_buy == 0)))) )
	{
		 traderoute_die("");
	}

	if ($j == 1)
	{
		$l_tdr_engageagain = str_replace("[tdr_engage]", $engage, $l_tdr_engageagain);
		echo "<br><br>$l_tdr_engageagain";
		cleanjs('');
		echo $cleanjs;
		echo "<form action=\"traderoute_engage.php?engage=$engage\" method=post>" .
			 "<br>$l_tdr_timestorep <input type=text name=tr_repeat value=1 size=5> <input type=submit value=$l_submit onclick=\"clean_js()\">";
		echo "<p></form>";
		traderoute_die("");
	}
}

close_database();
?>
