<?php
// This program is free software; you can redistribute it and/or modify it	 
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: port_purchase.php

include ("config/config.php");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
$no_gzip = 1;

$title = $l_title_port;

if (checklogin() or $tournament_setup_access == 1)
{
	include ("footer.php");
	die();
}

function buy_them_probe($player_id, $how_many = 1)
{
  global $db;
  global $dbtables;
  global $shipinfo;

  for($i=1; $i<=$how_many; $i++)
  {
	$debug_query = $db->Execute("INSERT INTO $dbtables[probe] (probe_id,  owner_id, ship_id, engines, sensors, cloak,sector_id,type,active) values ('',$player_id,'$shipinfo[ship_id]','0','0','0','0','0','P')");
	db_op_result($debug_query,__LINE__,__FILE__);
  }  
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($zoneinfo['zone_id'] != 3){
	$alliancefactor = 1;
}

//-------------------------------------------------------------------------------------------------

if ($zoneinfo['allow_trade'] == 'N')
{
	$title=$l_no_trade;
		$smarty->assign("error_msg", $l_no_trade_info);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}
elseif ($zoneinfo['allow_trade'] == 'L')
{
	if ($zoneinfo[team_zone] == 'N')
	{
	$res = $db->Execute("SELECT team FROM $dbtables[players] WHERE player_id=$zoneinfo[owner]");
	$ownerinfo = $res->fields;

	if ($playerinfo['player_id'] != $zoneinfo['owner'] && $playerinfo['team'] == 0 || $playerinfo['team'] != $ownerinfo['team'])
	{
		$title=$l_no_trade;
		$smarty->assign("error_msg", $l_no_trade_out);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

		die();
	}
	}
	else
	{
	if ($playerinfo[team] != $zoneinfo['owner'])
	{
		$title=$l_no_trade;
		$smarty->assign("error_msg", $l_no_trade_out);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

		die();
	}
	}
}

bigtitle();

$color_red	 = "red";
$color_green	 = "#00FF00"; //light green
$trade_deficit = "$l_cost : ";
$trade_benefit = "$l_profit : ";


function BuildOneCol( $text = "&nbsp;", $align = "left" ) {
	 echo"
	 <TR>
		<TD colspan=99 align=".$align.">".$text.".</TD>
	 </TR>
	 ";
}

function BuildTwoCol( $text_col1 = "&nbsp;", $text_col2 = "&nbsp;", $align_col1 = "left", $align_col2 = "left" ) {
	 echo"
	 <TR>
		<TD align=".$align_col1.">".$text_col1."</TD>
		<TD align=".$align_col2.">".$text_col2."</TD>
	 </TR>";
}


function phpTrueDelta($futurevalue,$shipvalue)
{
	$tempval = $futurevalue - $shipvalue;
	return $tempval;
}

if ($playerinfo['turns'] < 1)
{
	echo "$l_trade_turnneed<BR><BR>";
}
else
{
	$trade_ore		= round(abs($trade_ore));
	$trade_organics = round(abs($trade_organics));
	$trade_goods	= round(abs($trade_goods));
	$trade_energy	 = round(abs($trade_energy));

	if ($sectorinfo['port_type'] == "energy")
	{
		/*
			Here is the TRADE fonction to strip out some "spaghetti code".
			The function saves about 60 lines of code, I hope it will be
			easier to modify/add something in this part.
																 --Fant0m
		*/
		$newenergyprice = $sectorinfo['energy_price'];
		$neworganicsprice = $sectorinfo['organics_price'];
		$newgoodsprice = $sectorinfo['goods_price'];
		$neworeprice = $sectorinfo['ore_price'];

			$ore_price = $sectorinfo['ore_price'] + $ore_price + $ore_delta * $ore_limit / $ore_limit * $inventory_factor;
			if($ore_price <= 0)
				$ore_price = 0.01;
			$trade_ore = -$trade_ore;

			$organics_price = $sectorinfo['organics_price'] + $organics_price + $organics_delta * $organics_limit / $organics_limit * $inventory_factor;
			if($organics_price <= 0)
				$organics_price = 0.01;
			$trade_organics = -$trade_organics;

			$goods_price = $sectorinfo['goods_price'] + $goods_price + $goods_delta * $goods_limit / $goods_limit * $inventory_factor;
			if($goods_price <= 0)
				$goods_price = 0.01;
			$trade_goods = -$trade_goods;

			$energy_price = $energy_price - $energy_delta * $sectorinfo['port_energy'] / $energy_limit * $inventory_factor;

		$cargo_exchanged = $trade_ore + $trade_organics + $trade_goods;

		$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] -
		$shipinfo['goods'] - $shipinfo['colonists'];
		$free_power = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];
		$total_cost = $trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price +
		$trade_energy * $energy_price;

		 /* debug info
		 echo "$trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price + $trade_energy * $energy_price = $total_cost";
		 */

		if ($free_holds < $cargo_exchanged)
		{
			echo "$l_notenough_cargo	$l_returnto_port<BR><BR>";
		}
		elseif ($trade_energy > $free_power)
		{
			echo "$l_notenough_power	$l_returnto_port<BR><BR>";
		}
		elseif ($playerinfo['turns'] < 1)
		{
			echo "$l_notenough_turns.<BR><BR>";
		}
		elseif ($playerinfo['credits'] < $total_cost)
		{
			echo "$l_notenough_credits <BR><BR>";
		}
		elseif ($trade_ore < 0 && abs($shipinfo['ore']) < abs($trade_ore))
		{
			echo "$l_notenough_ore ";
		}
		elseif ($trade_organics < 0 && abs($shipinfo['organics']) < abs($trade_organics))
		{
			echo "$l_notenough_organics ";
		}
		elseif ($trade_goods < 0 && abs($shipinfo['goods']) < abs($trade_goods))
		{
			echo "$l_notenough_goods ";
		}
		elseif ($trade_energy < 0 && abs($shipinfo['energy']) < abs($trade_energy))
		{
			echo "$l_notenough_energy ";
		}
		elseif (abs($trade_organics) > $sectorinfo['port_organics'])
		{
			echo $l_exceed_organics;
		}
		elseif (abs($trade_ore) > $sectorinfo['port_ore'])
		{
			echo $l_exceed_ore;
		}
		elseif (abs($trade_goods) > $sectorinfo['port_goods'])
		{
			echo $l_exceed_goods;
		}
		elseif (abs($trade_energy) > $sectorinfo['port_energy'])
		{
			echo $l_exceed_energy;
		}
		else
		{
			if ($total_cost == 0 )
			{
				$trade_color	 = "white";
				$trade_result	= "$l_cost : ";
			}
			elseif ($total_cost < 0 )
			{
				$trade_color	 = $color_green;
				$trade_result	= $trade_benefit;
			}
			else
			{
				$trade_color	 = $color_red;
				$trade_result	= $trade_deficit;
			}

			echo "
			<TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
			 <TR>
				<TD colspan=99 align=center><font size=3 color=white><b>$l_trade_result</b></font></TD>
			 </TR>
			 <TR>
				<TD colspan=99 align=center><b><font color=\"". $trade_color . "\">". $trade_result ." " . NUMBER(abs($total_cost)) . " $l_credits</font></b></TD>
			 </TR>
			 <TR bgcolor=$color_line1>
				<TD><b><font size=2 color=white>$l_traded_ore: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_ore) . "</font></b></TD>
			 </TR>
			 <TR bgcolor=$color_line2>
				<TD><b><font size=2 color=white>$l_traded_organics: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_organics) . "</font></b></TD>
			 </TR>
			 <TR bgcolor=$color_line1>
				<TD><b><font size=2 color=white>$l_traded_goods: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_goods) . "</font></b></TD>
			 </TR>
			 <TR bgcolor=$color_line2>
				<TD><b><font size=2 color=white>$l_traded_energy: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_energy) . "</font></b></TD>
			 </TR>
			</TABLE>
			";

			/* Update ship cargo, credits and turns */
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1, rating=rating+1, credits=credits-$total_cost WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute("UPDATE $dbtables[ships] SET ore=GREATEST(ore+$trade_ore, 0), organics=GREATEST(organics+$trade_organics, 0), goods=GREATEST(goods+$trade_goods, 0), energy=GREATEST(energy+$trade_energy, 0) WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			/* Make all trades positive to change port values*/
			$trade_ore		= round(abs($trade_ore));
			$trade_organics	 = round(abs($trade_organics));
			$trade_goods		= round(abs($trade_goods));
			$trade_energy	 = round(abs($trade_energy));

			$trade_date = date("Y-m-d H:i:s");

			/* Decrease supply and demand on port */
			$debug_query = $db->Execute("UPDATE $dbtables[universe] SET ore_price = $neworeprice, goods_price = $newgoodsprice, organics_price = $neworganicsprice, energy_price = $newenergyprice, 
trade_date='$trade_date', port_ore=GREATEST(port_ore-$trade_ore, 0), port_organics=GREATEST(port_organics-$trade_organics, 0), port_goods=GREATEST(port_goods-$trade_goods, 0), port_energy=GREATEST(port_energy-$trade_energy, 0) where sector_id=$sectorinfo[sector_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			echo "$l_trade_complete.<BR><BR>";
		}
	}
}

//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";
TEXT_GOTOMAIN();

include ("footer.php");

?>
