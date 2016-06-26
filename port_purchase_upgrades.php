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

	if ($sectorinfo['port_type'] == "upgrades")
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
		if($shipinfo['hull'] == $shipinfo['hull_normal']){
			if ($hull_upgrade > $classinfo['maxhull'])
				$hull_upgrade = $classinfo['maxhull'];

			if ($hull_upgrade < $classinfo['minhull'])
				$hull_upgrade = $classinfo['minhull'];

			if ($hull_upgrade > $shipinfo['hull_normal'])
			{
				$hull_upgrade_cost = phpChangeDelta($hull_upgrade, $shipinfo['hull_normal']);
			}
		}
		else
		{
			$hull_upgrade = $shipinfo['hull_normal'];
		}

		$engine_upgrade_cost = 0;
		if($shipinfo['engines'] == $shipinfo['engines_normal']){
			if ($engine_upgrade > $classinfo['maxengines'])
				$engine_upgrade = $classinfo['maxengines'];

			if ($engine_upgrade < $classinfo['minengines'])
				$engine_upgrade = $classinfo['minengines'];

			if ($engine_upgrade > $shipinfo['engines_normal'])
			{
				$engine_upgrade_cost = phpChangeDelta($engine_upgrade, $shipinfo['engines_normal']);
			}
		}
		else
		{
			$engine_upgrade = $shipinfo['engines_normal'];
		}

		$power_upgrade_cost = 0;
		if($shipinfo['power'] == $shipinfo['power_normal']){
			if ($power_upgrade > $classinfo['maxpower'])
				$power_upgrade = $classinfo['maxpower'];

			if ($power_upgrade < $classinfo['minpower'])
				$power_upgrade = $classinfo['minpower'];

			if ($power_upgrade > $shipinfo['power_normal'])
			{
				$power_upgrade_cost = phpChangeDelta($power_upgrade, $shipinfo['power_normal']);
			}
		}
		else
		{
			$power_upgrade = $shipinfo['power_normal'];
		}

		$computer_upgrade_cost = 0;
		if($shipinfo['computer'] == $shipinfo['computer_normal']){
			if ($computer_upgrade > $classinfo['maxcomputer'])
				$computer_upgrade = $classinfo['maxcomputer'];

			if ($computer_upgrade < $classinfo['mincomputer'])
				$computer_upgrade = $classinfo['mincomputer'];

			if ($computer_upgrade > $shipinfo['computer_normal'])
			{
				$computer_upgrade_cost = phpChangeDelta($computer_upgrade, $shipinfo['computer_normal']);
			}
		}
		else
		{
			$computer_upgrade = $shipinfo['computer_normal'];
		}

		$sensors_upgrade_cost = 0;
		if($shipinfo['sensors'] == $shipinfo['sensors_normal']){
			if ($sensors_upgrade > $classinfo['maxsensors'])
				$sensors_upgrade = $classinfo['maxsensors'];

			if ($sensors_upgrade < $classinfo['minsensors'])
				$sensors_upgrade = $classinfo['minsensors'];

			if ($sensors_upgrade > $shipinfo['sensors_normal'])
			{
				$sensors_upgrade_cost = phpChangeDelta($sensors_upgrade, $shipinfo['sensors_normal']);
			}
		}
		else
		{
			$sensors_upgrade = $shipinfo['sensors_normal'];
		}

		$beams_upgrade_cost = 0;
		if($shipinfo['beams'] == $shipinfo['beams_normal']){
			if ($beams_upgrade > $classinfo['maxbeams'])
				$beams_upgrade = $classinfo['maxbeams'];

			if ($beams_upgrade < $classinfo['minbeams'])
				$beams_upgrade = $classinfo['minbeams'];

			if ($beams_upgrade > $shipinfo['beams_normal'])
			{
				$beams_upgrade_cost = phpChangeDelta($beams_upgrade, $shipinfo['beams_normal']);
			}
		}
		else
		{
			$beams_upgrade = $shipinfo['beams_normal'];
		}

		$armour_upgrade_cost = 0;
		if($shipinfo['armour'] == $shipinfo['armour_normal']){
			if ($armour_upgrade > $classinfo['maxarmour'])
				$armour_upgrade = $classinfo['maxarmour'];

			if ($armour_upgrade < $classinfo['minarmour'])
				$armour_upgrade = $classinfo['minarmour'];

			if ($armour_upgrade > $shipinfo['armour_normal'])
			{
				$armour_upgrade_cost = phpChangeDelta($armour_upgrade, $shipinfo['armour_normal']);
			}
		}
		else
		{
			$armour_upgrade = $shipinfo['armour_normal'];
		}

		$cloak_upgrade_cost = 0;
		if($shipinfo['cloak'] == $shipinfo['cloak_normal']){
			if ($cloak_upgrade > $classinfo['maxcloak'])
				$cloak_upgrade = $classinfo['maxcloak'];

			if ($cloak_upgrade < $classinfo['mincloak'])
				$cloak_upgrade = $classinfo['mincloak'];

			if ($cloak_upgrade > $shipinfo['cloak_normal'])
			{
				$cloak_upgrade_cost = phpChangeDelta($cloak_upgrade, $shipinfo['cloak_normal']);
			}
		}
		else
		{
			$cloak_upgrade = $shipinfo['cloak_normal'];
		}

		$torp_launchers_upgrade_cost = 0;
		if($shipinfo['torp_launchers'] == $shipinfo['torp_launchers_normal']){
			if ($torp_launchers_upgrade > $classinfo['maxtorp_launchers'])
				$torp_launchers_upgrade = $classinfo['maxtorp_launchers'];

			if ($torp_launchers_upgrade < $classinfo['mintorp_launchers'])
				$torp_launchers_upgrade = $classinfo['mintorp_launchers'];

			if ($torp_launchers_upgrade > $shipinfo['torp_launchers_normal'])
			{
				$torp_launchers_upgrade_cost = phpChangeDelta($torp_launchers_upgrade, $shipinfo['torp_launchers_normal']);
			}
		}
		else
		{
			$torp_launchers_upgrade = $shipinfo['torp_launchers_normal'];
		}

		$shields_upgrade_cost = 0;
		if($shipinfo['shields'] == $shipinfo['shields_normal']){
			if ($shields_upgrade > $classinfo['maxshields'])
				$shields_upgrade = $classinfo['maxshields'];

			if ($shields_upgrade < $classinfo['minshields'])
				$shields_upgrade = $classinfo['minshields'];

			if ($shields_upgrade > $shipinfo['shields_normal'])
			{
				$shields_upgrade_cost = phpChangeDelta($shields_upgrade, $shipinfo['shields_normal']);
			}
		}
		else
		{
			$shields_upgrade = $shipinfo['shields_normal'];
		}

		$ecm_upgrade_cost = 0;
		if($shipinfo['ecm'] == $shipinfo['ecm_normal']){
			if ($ecm_upgrade > $classinfo['maxecm'])
				$ecm_upgrade = $classinfo['maxecm'];

			if ($ecm_upgrade < $classinfo['minecm'])
				$ecm_upgrade = $classinfo['minecm'];

			if ($ecm_upgrade > $shipinfo['ecm_normal'])
			{
				$ecm_upgrade_cost = phpChangeDelta($ecm_upgrade, $shipinfo['ecm_normal']);
			}
		}
		else
		{
			$ecm_upgrade = $shipinfo['ecm_normal'];
		}

		if ($fighter_number < 0)
			 $fighter_number = 0;
		$fighter_number	= round(abs($fighter_number));
		$fighter_max	 = NUM_FIGHTERS($shipinfo['computer']) - $shipinfo['fighters'];
		if ($fighter_max < 0)
		{
			$fighter_max = 0;
		}
		if ($fighter_number > $fighter_max)
		{
			$fighter_number = $fighter_max;
		}

		if(!class_exists($shipinfo['computer_class'])){
			include ("class/" . $shipinfo['computer_class'] . ".inc");
		}

		$computerobject = new $shipinfo['computer_class']();
		$fighter_price_modifier = $computerobject->fighter_price_modifier;
		$fighter_cost	= $fighter_number * $fighter_price * $fighter_price_modifier;
		if ($torpedo_number < 0)
			 $torpedo_number = 0;
		$torpedo_number	= round(abs($torpedo_number));
		$torpedo_max	 = NUM_TORPEDOES($shipinfo['torp_launchers']) - $shipinfo['torps'];
		if ($torpedo_max < 0)
		{
			$torpedo_max = 0;
		}
		if ($torpedo_number > $torpedo_max)
		{
			$torpedo_number = $torpedo_max;
		}

		if(!class_exists($shipinfo['torp_class'])){
			include ("class/" . $shipinfo['torp_class'] . ".inc");
		}

		$torpobject = new $shipinfo['torp_class']();
		$torp_price_modifier = $torpobject>torp_price_modifier;
		$torpedo_cost = $torpedo_number * $torpedo_price * $torp_price_modifier;
		if ($armour_number < 0)
			 $armour_number = 0;
		$armour_number = round(abs($armour_number));
		$armour_max = NUM_ARMOUR($shipinfo['armour']) - $shipinfo['armour_pts'];
		if ($armour_max < 0)
		{
			$armour_max = 0;
		}
		if ($armour_number > $armour_max)
		{
			$armour_number = $armour_max;
		}

		if(!class_exists($shipinfo['armor_class'])){
			include ("class/" . $shipinfo['armor_class'] . ".inc");
		}

		$armorobject = new $shipinfo['armor_class']();
		$armor_price_modifier = $armorobject->armor_price_modifier;
		$armour_cost	 = $armour_number * $armour_price * $armor_price_modifier;
		if ($colonist_number < 0)
			 $colonist_number = 0;
		$colonist_number = round(abs($colonist_number));
		$colonist_max	= NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] -
			$shipinfo['goods'] - $shipinfo['colonists'];

		if ($colonist_number > $colonist_max)
		{
			$colonist_number = $colonist_max;
		}

		$colonist_cost = $colonist_number * $colonist_price;

		$total_cost = $hull_upgrade_cost + $engine_upgrade_cost + $power_upgrade_cost + $computer_upgrade_cost + $ecm_upgrade_cost +
						$sensors_upgrade_cost + $beams_upgrade_cost + $armour_upgrade_cost + $cloak_upgrade_cost + $shields_upgrade_cost +
						$torp_launchers_upgrade_cost + $fighter_cost + $torpedo_cost + $armour_cost + $colonist_cost;

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

			if ($hull_upgrade > $shipinfo['hull_normal'])
			{
				$query = $query . ", hull=$hull_upgrade, hull_normal=$hull_upgrade";
				BuildOneCol("$l_hull $l_trade_upgraded $hull_upgrade");
			}
			if ($engine_upgrade > $shipinfo['engines_normal'])
			{
				$query = $query . ", engines=$engine_upgrade, engines_normal=$engine_upgrade";
				BuildOneCol("$l_engines $l_trade_upgraded $engine_upgrade");
			}
			if ($power_upgrade > $shipinfo['power_normal'])
			{
				$query = $query . ", power=$power_upgrade, power_normal=$power_upgrade";
				BuildOneCol("$l_power $l_trade_upgraded $power_upgrade");
			}
			if ($computer_upgrade > $shipinfo['computer_normal'])
			{
				$query = $query . ", computer=$computer_upgrade, computer_normal=$computer_upgrade";
				BuildOneCol("$l_computer $l_trade_upgraded $computer_upgrade");
			}
			if ($sensors_upgrade > $shipinfo['sensors_normal'])
			{
				$query = $query . ", sensors=$sensors_upgrade, sensors_normal=$sensors_upgrade";
				BuildOneCol("$l_sensors $l_trade_upgraded $sensors_upgrade");
			}
			if ($beams_upgrade > $shipinfo['beams_normal'])
			{
				$query = $query . ", beams=$beams_upgrade, beams_normal=$beams_upgrade";
				BuildOneCol("$l_beams $l_trade_upgraded $beams_upgrade");
			}
			if ($armour_upgrade > $shipinfo['armour_normal'])
			{
				$query = $query . ", armour=$armour_upgrade, armour_normal=$armour_upgrade";
				BuildOneCol("$l_armour $l_trade_upgraded $armour_upgrade");
			}
			if ($cloak_upgrade > $shipinfo['cloak_normal'])
			{
				$query = $query . ", cloak=$cloak_upgrade, cloak_normal=$cloak_upgrade";
				BuildOneCol("$l_cloak $l_trade_upgraded $cloak_upgrade");
			}
			if ($torp_launchers_upgrade > $shipinfo['torp_launchers_normal'])
			{
				$query = $query . ", torp_launchers=$torp_launchers_upgrade, torp_launchers_normal=$torp_launchers_upgrade";
				BuildOneCol("$l_torp_launch $l_trade_upgraded $torp_launchers_upgrade");
			}
			if ($shields_upgrade > $shipinfo['shields_normal'])
			{
				$query = $query . ", shields=$shields_upgrade, shields_normal=$shields_upgrade";
				BuildOneCol("$l_shields $l_trade_upgraded $shields_upgrade");
			}
			if ($ecm_upgrade > $shipinfo['ecm_normal'])
			{
				$query = $query . ", ecm=$ecm_upgrade, ecm_normal=$ecm_upgrade";
				BuildOneCol("$l_ecm $l_trade_upgraded $ecm_upgrade");
			}
			if ($fighter_number)
			{
				$query = $query . ", fighters=fighters+$fighter_number";
				BuildTwoCol("$l_fighters $l_trade_added:", $fighter_number, "left", "right" );
			}
			if ($torpedo_number)
			{
				$query = $query . ", torps=torps+$torpedo_number";
				BuildTwoCol("$l_torps $l_trade_added:", $torpedo_number, "left", "right" );
			}
			if ($armour_number)
			{
				$query = $query . ", armour_pts=armour_pts+$armour_number";
				BuildTwoCol("$l_armourpts $l_trade_added:", $armour_number, "left", "right" );
			}
			if ($colonist_number)
			{
				$query = $query . ", colonists=colonists+$colonist_number";
				BuildTwoCol("$l_colonists $l_trade_added:", $colonist_number, "left", "right" );
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
