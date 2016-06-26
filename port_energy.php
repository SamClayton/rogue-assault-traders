<?php
if (preg_match("/port_energy.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

	$title=$l_title_trade;
	bigtitle();

	$ore_price = $sectorinfo['ore_price'] + $ore_price + $ore_delta * $ore_limit / $ore_limit * $inventory_factor;

	if($ore_price <= 0)
		$ore_price = 0.01;

	$sb_ore = $l_buying;

	$organics_price = $sectorinfo['organics_price'] + $organics_price + $organics_delta * $organics_limit / $organics_limit * $inventory_factor;

	if($organics_price <= 0)
		$organics_price = 0.01;

	$sb_organics = $l_buying;

	$goods_price = $sectorinfo['goods_price'] + $goods_price + $goods_delta * $goods_limit / $goods_limit * $inventory_factor;

	if($goods_price <= 0)
		$goods_price = 0.01;

	$sb_goods = $l_buying;

	$energy_price = $energy_price - $energy_delta * $sectorinfo['port_energy'] / $energy_limit * $inventory_factor;
	$sb_energy = $l_selling;

	// establish default amounts for each commodity
	if ($sb_ore == $l_buying)
	{
	$amount_ore = $shipinfo['ore'];
	}
	else
	{
	$amount_ore = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['colonists'];
	}

	if ($sb_organics == $l_buying)
	{
	$amount_organics = $shipinfo['organics'];
	}
	else
	{
	$amount_organics = NUM_HOLDS($shipinfo['hull']) - $shipinfo['organics'] - $shipinfo['colonists'];
	}

	if ($sb_goods == $l_buying)
	{
	$amount_goods = $shipinfo['goods'];
	}
	else
	{
	$amount_goods = NUM_HOLDS($shipinfo['hull']) - $shipinfo['goods'] - $shipinfo['colonists'];
	}

	if ($sb_energy == $l_buying)
	{
	$amount_energy = $shipinfo['energy'];
	}
	else
	{
	$amount_energy = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];
	}

	// limit amounts to port quantities
	$amount_ore = min($amount_ore, $sectorinfo['port_ore']);
	$amount_organics = min($amount_organics, $sectorinfo['port_organics']);
	$amount_goods = min($amount_goods, $sectorinfo['port_goods']);
	$amount_energy = min($amount_energy, $sectorinfo['port_energy']);

	// limit amounts to what the player can afford
	if ($sb_ore == $l_selling)
	{
	$amount_ore = min($amount_ore, floor(($playerinfo['credits'] + $amount_organics * $organics_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $ore_price));
	}
	if ($sb_organics == $l_selling)
	{
	$amount_organics = min($amount_organics, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_goods * $goods_price + $amount_energy * $energy_price) / $organics_price));
	}
	if ($sb_goods == $l_selling)
	{
	$amount_goods = min($amount_goods, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_energy * $energy_price) / $goods_price));
	}
	if ($sb_energy == $l_selling)
	{
	$amount_energy = min($amount_energy, floor(($playerinfo['credits'] + $amount_ore * $ore_price + $amount_organics * $organics_price + $amount_goods * $goods_price) / $energy_price));
	}
	cleanjs('');
	echo $cleanjs;
	echo "<FORM ACTION=port_purchase_energy.php METHOD=POST>";
	echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">";
	echo "<TR BGCOLOR=\"$color_header\"><TD><B>$l_commodity</B></TD><TD><B>$l_buying/$l_selling</B></TD><TD><B>$l_amount</B></TD><TD><B>$l_price</B></TD><TD><B>$l_buy/$l_sell</B></TD><TD><B>$l_cargo</B></TD></TR>";
	echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_ore</TD><TD>$sb_ore</TD><TD>" . NUMBER($sectorinfo['port_ore']) . "</TD><TD>".floor($ore_price)."</TD><TD><INPUT TYPE=TEXT style='text-align:right' NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE=$amount_ore></TD><TD>" . NUMBER($shipinfo['ore']) . "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_organics</TD><TD>$sb_organics</TD><TD>" . NUMBER($sectorinfo['port_organics']) . "</TD><TD>".floor($organics_price)."</TD><TD><INPUT TYPE=TEXT style='text-align:right' NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE=$amount_organics></TD><TD>" . NUMBER($shipinfo['organics']) . "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_goods</TD><TD>$sb_goods</TD><TD>" . NUMBER($sectorinfo['port_goods']) . "</TD><TD>".floor($goods_price)."</TD><TD><INPUT TYPE=TEXT style='text-align:right' NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE=$amount_goods></TD><TD>" . NUMBER($shipinfo['goods']) . "</TD></TR>";
	echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_energy</TD><TD>$sb_energy</TD><TD>" . NUMBER($sectorinfo['port_energy']) . "</TD><TD>".floor($energy_price)."</TD><TD><INPUT TYPE=TEXT style='text-align:right' NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE=$amount_energy></TD><TD>" . NUMBER($shipinfo['energy']) . "</TD></TR>";
	echo "</TABLE><BR>";
	echo "<INPUT TYPE=SUBMIT VALUE=$l_trade ONCLICK=\"clean_js()\">";
	echo "</FORM>";

	$free_holds = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'] - $shipinfo['colonists'];
	$free_power = NUM_ENERGY($shipinfo['power']) - $shipinfo['energy'];

 $l_trade_st_info=str_replace("[free_holds]",NUMBER($free_holds),$l_trade_st_info);
 $l_trade_st_info=str_replace("[free_power]",NUMBER($free_power),$l_trade_st_info);
 $l_trade_st_info=str_replace("[credits]",NUMBER($playerinfo['credits']),$l_trade_st_info);

 echo $l_trade_st_info;

echo "\n";
echo "<BR><BR>\n";
TEXT_GOTOMAIN();
echo "\n";

include ("footer.php");

?>
