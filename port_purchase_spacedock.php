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

	if ($sectorinfo['port_type'] == "spacedock")
	{

		if (isLoanPending($playerinfo['player_id']))
		{
			echo "$l_port_loannotrade<p>";
			echo "<A HREF=igb.php>$l_igb_term</a><p>";
			TEXT_GOTOMAIN();
			include ("footer.php");
			die();
		}

		$hull_upgrade_cost = 0;
		if($shipinfo['hull'] != $shipinfo['hull_normal']){
			if ($hull_upgrade > $shipinfo['hull_normal'])
				$hull_upgrade = $shipinfo['hull_normal'];

			if ($hull_upgrade < 0)
				$hull_upgrade = 0;

			if ($hull_upgrade > $shipinfo['hull'])
			{
				$hull_upgrade_cost = phpChangeDelta($hull_upgrade, $shipinfo['hull']);
			}
		}
		else
		{
			$hull_upgrade = $shipinfo['hull'];
		}

		$engine_upgrade_cost = 0;
		if($shipinfo['engines'] != $shipinfo['engines_normal']){
			if ($engine_upgrade > $shipinfo['engines_normal'])
				$engine_upgrade = $shipinfo['engines_normal'];

			if ($engine_upgrade < 0)
				$engine_upgrade = 0;

			if ($engine_upgrade > $shipinfo['engines'])
			{
				$engine_upgrade_cost = phpChangeDelta($engine_upgrade, $shipinfo['engines']);
			}
		}
		else
		{
			$engine_upgrade = $shipinfo['engines'];
		}

		$power_upgrade_cost = 0;
		if($shipinfo['power'] != $shipinfo['power_normal']){
			if ($power_upgrade > $shipinfo['power_normal'])
				$power_upgrade = $shipinfo['power_normal'];

			if ($power_upgrade < 0)
				$power_upgrade = 0;

			if ($power_upgrade > $shipinfo['power'])
			{
				$power_upgrade_cost = phpChangeDelta($power_upgrade, $shipinfo['power']);
			}
		}
		else
		{
			$power_upgrade = $shipinfo['power'];
		}

		$computer_upgrade_cost = 0;
		if($shipinfo['computer'] != $shipinfo['computer_normal']){
			if ($computer_upgrade > $shipinfo['computer_normal'])
				$computer_upgrade = $shipinfo['computer_normal'];

			if ($computer_upgrade < 0)
				$computer_upgrade = 0;

			if ($computer_upgrade > $shipinfo['computer'])
			{
				$computer_upgrade_cost = phpChangeDelta($computer_upgrade, $shipinfo['computer']);
			}
		}
		else
		{
			$computer_upgrade = $shipinfo['computer'];
		}

		$sensors_upgrade_cost = 0;
		if($shipinfo['sensors'] != $shipinfo['sensors_normal']){
			if ($sensors_upgrade > $shipinfo['sensors_normal'])
				$sensors_upgrade = $shipinfo['sensors_normal'];

			if ($sensors_upgrade < 0)
				$sensors_upgrade = 0;

			if ($sensors_upgrade > $shipinfo['sensors'])
			{
				$sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $shipinfo['sensors']);
			}
		}
		else
		{
			$sensors_upgrade = $shipinfo['sensors'];
		}

		$beams_upgrade_cost = 0;
		if($shipinfo['beams'] != $shipinfo['beams_normal']){
			if ($beams_upgrade > $shipinfo['beams_normal'])
				$beams_upgrade = $shipinfo['beams_normal'];

			if ($beams_upgrade < 0)
				$beams_upgrade = 0;

			if ($beams_upgrade > $shipinfo['beams'])
			{
				$beams_upgrade_cost = phpChangeDelta($beams_upgrade, $shipinfo['beams']);
			}
		}
		else
		{
			$beams_upgrade = $shipinfo['beams'];
		}

		$armour_upgrade_cost = 0;
		if($shipinfo['armour'] != $shipinfo['armour_normal']){
			if ($armour_upgrade > $shipinfo['armour_normal'])
				$armour_upgrade = $shipinfo['armour_normal'];

			if ($armour_upgrade < 0)
				$armour_upgrade = 0;

			if ($armour_upgrade > $shipinfo['armour'])
			{
				$armour_upgrade_cost = phpChangeDelta($armour_upgrade, $shipinfo['armour']);
			}
		}
		else
		{
			$armour_upgrade = $shipinfo['armour'];
		}

		$cloak_upgrade_cost = 0;
		if($shipinfo['cloak'] != $shipinfo['cloak_normal']){
			if ($cloak_upgrade > $shipinfo['cloak_normal'])
				$cloak_upgrade = $shipinfo['cloak_normal'];

			if ($cloak_upgrade < 0)
				$cloak_upgrade = 0;

			if ($cloak_upgrade > $shipinfo['cloak'])
			{
				$cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $shipinfo['cloak']);
			}
		}
		else
		{
			$cloak_upgradegrade = $shipinfo['cloak'];
		}

		$torp_launchers_upgrade_cost = 0;
		if($shipinfo['torp_launchers'] != $shipinfo['torp_launchers_normal']){
			if ($torp_launchers_upgrade > $shipinfo['torp_launchers_normal'])
				$torp_launchers_upgrade = $shipinfo['torp_launchers_normal'];

			if ($torp_launchers_upgrade < 0)
				$torp_launchers_upgrade = 0;

			if ($torp_launchers_upgrade > $shipinfo['torp_launchers'])
			{
				$torp_launchers_upgrade_cost = phpChangeDelta($torp_launchers_upgrade, $shipinfo['torp_launchers']);
			}
		}
		else
		{
			$torp_launchers_upgrade = $shipinfo['torp_launchers'];
		}

		$shields_upgrade_cost = 0;
		if($shipinfo['shields'] != $shipinfo['shields_normal']){
			if ($shields_upgrade > $shipinfo['shields_normal'])
				$shields_upgrade = $shipinfo['shields_normal'];

			if ($shields_upgrade < 0)
				$shields_upgrade = 0;

			if ($shields_upgrade > $shipinfo['shields'])
			{
				$shields_upgrade_cost = phpChangeDelta($shields_upgrade, $shipinfo['shields']);
			}
		}
		else
		{
			$shields_upgrade = $shipinfo['shields'];
		}

		$ecm_upgrade_cost = 0;
		if($shipinfo['ecm'] != $shipinfo['ecm_normal']){
			if ($ecm_upgrade > $shipinfo['ecm_normal'])
				$ecm_upgrade = $shipinfo['ecm_normal'];

			if ($ecm_upgrade < 0)
				$ecm_upgrade = 0;

			if ($ecm_upgrade > $shipinfo['ecm'])
			{
				$ecm_upgrade_cost = phpChangeDelta($ecm_upgrade, $shipinfo['ecm']);
			}
		}
		else
		{
			$ecm_upgrade = $shipinfo['ecm'];
		}

		$total_cost = $hull_upgrade_cost + $engine_upgrade_cost + $power_upgrade_cost + $computer_upgrade_cost +
						$sensors_upgrade_cost + $beams_upgrade_cost + $armour_upgrade_cost + $cloak_upgrade_cost +
						$torp_launchers_upgrade_cost + $shields_upgrade_cost + $ecm_upgrade_cost;

		$total_cost = $total_cost * $alliancefactor * ($repair_modifier / 100);

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

			if ($hull_upgrade > $shipinfo['hull'])
			{
				$query = $query . ", hull=$hull_upgrade";
				BuildOneCol("$l_hull $l_trade_upgraded $hull_upgrade");
			}
			if ($engine_upgrade > $shipinfo['engines'])
			{
				$query = $query . ", engines=$engine_upgrade";
				BuildOneCol("$l_engines $l_trade_upgraded $engine_upgrade");
			}
			if ($power_upgrade > $shipinfo['power'])
			{
				$query = $query . ", power=$power_upgrade";
				BuildOneCol("$l_power $l_trade_upgraded $power_upgrade");
			}
			if ($computer_upgrade > $shipinfo['computer'])
			{
				$query = $query . ", computer=$computer_upgrade";
				BuildOneCol("$l_computer $l_trade_upgraded $computer_upgrade");
			}
			if ($sensors_upgrade > $shipinfo['sensors'])
			{
				$query = $query . ", sensors=$sensors_upgrade";
				BuildOneCol("$l_sensors $l_trade_upgraded $sensors_upgrade");
			}
			if ($beams_upgrade > $shipinfo['beams'])
			{
				$query = $query . ", beams=$beams_upgrade";
				BuildOneCol("$l_beams $l_trade_upgraded $beams_upgrade");
			}
			if ($armour_upgrade > $shipinfo['armour'])
			{
				$query = $query . ", armour=$armour_upgrade";
				BuildOneCol("$l_armour $l_trade_upgraded $armour_upgrade");
			}
			if ($cloak_upgrade > $shipinfo['cloak'])
			{
				$query = $query . ", cloak=$cloak_upgrade";
				BuildOneCol("$l_cloak $l_trade_upgraded $cloak_upgrade");
			}
			if ($torp_launchers_upgrade > $shipinfo['torp_launchers'])
			{
				$query = $query . ", torp_launchers=$torp_launchers_upgrade";
				BuildOneCol("$l_torp_launch $l_trade_upgraded $torp_launchers_upgrade");
			}
			if ($shields_upgrade > $shipinfo['shields'])
			{
				$query = $query . ", shields=$shields_upgrade";
				BuildOneCol("$l_shields $l_trade_upgraded $shields_upgrade");
			}
			if ($ecm_upgrade > $shipinfo['ecm'])
			{
				$query = $query . ", ecm=$ecm_upgrade";
				BuildOneCol("$l_ecm $l_trade_upgraded $ecm_upgrade");
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
