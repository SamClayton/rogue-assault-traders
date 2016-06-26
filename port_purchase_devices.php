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
else
{
	$res2 = $db->Execute("SELECT COUNT(*) as number_of_bounties FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
	if ($res2)
	{
		$alliancefactor = $alliancefactor * max($res2->fields['number_of_bounties'], 1);
	}
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

	if ($sectorinfo['port_type'] == "devices")
	{

		if (isLoanPending($playerinfo['player_id']))
		{
			echo "$l_port_loannotrade<p>";
			echo "<A HREF=igb.php>$l_igb_term</a><p>";
			TEXT_GOTOMAIN();
			include ("footer.php");
			die();
		}

		$dev_beacon_number		= round(abs($dev_beacon_number));
		$dev_beacon_cost			= $dev_beacon_number * $dev_beacon_price;
		$dev_genesis_number		 = round(abs($dev_genesis_number));
		$dev_genesis_cost		 = $dev_genesis_number * $dev_genesis_price;
		$dev_sectorgenesis_number		 = round(abs($dev_sectorgenesis_number));
		$dev_sectorgenesis_cost		 = $dev_sectorgenesis_number * $dev_sectorgenesis_price;
		$dev_emerwarp_number		= min(round(abs($dev_emerwarp_number)), $max_emerwarp - $shipinfo['dev_emerwarp']);
		$dev_emerwarp_cost		= $dev_emerwarp_number * $dev_emerwarp_price;
		$dev_warpedit_number		= round(abs($dev_warpedit_number));
		$dev_warpedit_cost		= $dev_warpedit_number * $dev_warpedit_price;
		$dev_minedeflector_number = round(abs($dev_minedeflector_number));
		$dev_minedeflector_cost	 = $dev_minedeflector_number * $dev_minedeflector_price;

		if ($spy_success_factor)
		{
			$res=$db->Execute("SELECT count(spy_id) as spy_num from $dbtables[spies] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='N'");
			$spy_num = $res->fields['spy_num'];
			if($spy_num <= $max_spies){
				$spy_number		= min(round(abs($spy_number)), $max_spies - $spy_num);
				$spy_cost				 = $spy_number * $spy_price;
			}
			else
			{
				$spy_number				 = 0;
				$spy_cost				 = 0;
			}
		}
		else
		{
			$spy_number				 = 0;
			$spy_cost				 = 0;
		}

		if($dig_success_factor)
		{
			$res=$db->Execute("SELECT count(dig_id) as dig_num from $dbtables[dignitary] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='N'");
			$dig_num = $res->fields['dig_num'];
			if($dig_num <= $max_digs){
				$dig_number		= min(round(abs($dig_number)), $max_digs - $dig_num);
				$dig_cost				 = $dig_number * $dig_price;
			}
			else
			{
				$dig_number				 = 0;
				$dig_cost				 = 0;
			}
		}
		else
		{
			$dig_number				 = 0;
			$dig_cost				 = 0;
		}

		$res=$db->Execute("SELECT count(probe_id) as probe_num from $dbtables[probe] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='P'");
		$probe_num = $res->fields['probe_num'];
		if($probe_num <= $max_probes){
			$probe_number		= min(round(abs($probe_number)), $max_probes - $probe_num);
			$probe_cost				 = $probe_number * $dev_probe;
		}
		else
		{
			$probe_number				 = 0;
			$probe_cost				 = 0;
		}

		$dev_escapepod_cost = 0;
		$dev_fuelscoop_cost = 0;
		if (($escapepod_purchase) && ($shipinfo['dev_escapepod'] != 'Y'))
		{
			$dev_escapepod_cost = $dev_escapepod_price;
		}
		if (($fuelscoop_purchase) && ($shipinfo['dev_fuelscoop'] != 'Y'))
		{
			$dev_fuelscoop_cost = $dev_fuelscoop_price;
		}

		if(($nova_purchase) && ($shipinfo['dev_nova'] != 'Y'))
		{
			$dev_nova_cost = $dev_nova_price;
		}

		$total_cost = $dev_genesis_cost + $dev_sectorgenesis_cost + $dev_beacon_cost + $dev_emerwarp_cost + $dev_warpedit_cost + $dev_minedeflector_cost +
						$dev_escapepod_cost + $dev_fuelscoop_cost + $dev_nova_cost + $shields_upgrade_cost + $spy_cost + $dig_cost+ $probe_cost;///

		$total_cost = $total_cost * $alliancefactor;

		if ($total_cost > $playerinfo['credits'])
		{
			echo "$l_ports_needcredits " . NUMBER($total_cost) . " $l_ports_needcredits1 " . NUMBER($playerinfo['credits']) . " $l_credits.";
		}
		else
		{
			$trade_credits = NUMBER(abs($total_cost));
			echo "<TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
			 <TR>
				<TD colspan=99 align=center bgcolor=#300030><font size=3 color=white><b>$l_trade_result</b></font></TD>
			 </TR>
			 <TR>
				<TD colspan=99 align=center><b><font color=red>$l_cost : " . $trade_credits . " $l_credits</font></b></TD>
			 </TR>";

			 //	Total cost is " . NUMBER(abs($total_cost)) . " credits.<BR><BR>";
			$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$total_cost,turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$query = "UPDATE $dbtables[ships] SET class=$shipinfo[class] ";

			if ($dev_genesis_number)
			{
				$query = $query . ", dev_genesis=dev_genesis+$dev_genesis_number";
				BuildTwoCol("$l_genesis $l_trade_added:", $dev_genesis_number, "left", "right" );
			}
			if ($dev_sectorgenesis_number)
			{
				$query = $query . ", dev_sectorgenesis=dev_sectorgenesis+$dev_sectorgenesis_number";
				BuildTwoCol("$l_sectorgenesis $l_trade_added:", $dev_sectorgenesis_number, "left", "right" );
			}
			if($dev_beacon_number)
			{
				$query = $query . ", dev_beacon=dev_beacon+$dev_beacon_number";
				BuildTwoCol("$l_beacons $l_trade_added:", $dev_beacon_number , "left", "right" );
			}
			if ($dev_emerwarp_number)
			{
				$query = $query . ", dev_emerwarp=dev_emerwarp+$dev_emerwarp_number";
				BuildTwoCol("$l_ewd $l_trade_added:", $dev_emerwarp_number , "left", "right" );
			}
			if ($dev_warpedit_number)
			{
				$query = $query . ", dev_warpedit=dev_warpedit+$dev_warpedit_number";
				BuildTwoCol("$l_warpedit $l_trade_added:", $dev_warpedit_number , "left", "right" );
			}
			if ($dev_minedeflector_number)
			{
				$query = $query . ", dev_minedeflector=dev_minedeflector+$dev_minedeflector_number";
				BuildTwoCol("$l_deflect $l_trade_added:", $dev_minedeflector_number , "left", "right" );
			}
			if (($escapepod_purchase) && ($shipinfo['dev_escapepod'] != 'Y'))
			{
				$query = $query . ", dev_escapepod='Y'";
				BuildOneCol("$l_escape_pod $l_trade_installed");
			}
			if (($fuelscoop_purchase) && ($shipinfo['dev_fuelscoop'] != 'Y'))
			{
				$query = $query . ", dev_fuelscoop='Y'";
				BuildOneCol("$l_fuel_scoop $l_trade_installed");
			}

			if(($nova_purchase) && ($shipinfo['dev_nova'] != 'Y'))
			{
				$query = $query . ", dev_nova='Y'";
				BuildOneCol("$l_nova $l_trade_installed");
			}

			if ($spy_number && $spy_success_factor)
			{
				buy_them($playerinfo['player_id'], $spy_number);
				BuildTwoCol("$l_spy $l_trade_added:", $spy_number , "left", "right" );
			}

			if($dig_number && $dig_success_factor)
			{
				buy_them_dig($playerinfo['player_id'], $dig_number);
				BuildTwoCol("$l_dig $l_trade_added:", $dig_number , "left", "right" );
			}
			if($probe_number)
			{
					buy_them_probe($playerinfo['player_id'], $probe_number);
					BuildTwoCol("$l_probe $l_trade_added:", $probe_number , "left", "right" );
			}

			$query = $query . " WHERE ship_id=$shipinfo[ship_id]";
			$debug_query = $db->Execute("$query");
			db_op_result($debug_query,__LINE__,__FILE__);

			$hull_upgrade=0;
			echo "
			</table>
			";
		}
		echo "<BR><BR> <A HREF=port.php>$l_clickme</A> $l_port_returntospecial";
	}
}

//-------------------------------------------------------------------------------------------------

echo "<BR><BR>";
TEXT_GOTOMAIN();

include ("footer.php");

?>
