<?php
if (preg_match("/port_upgrades.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}


	$title=$l_upgrade_port_title;
	if (isLoanPending($playerinfo['player_id']))
	{
		$smarty->assign("error_msg", $l_port_loannotrade);
		$smarty->assign("error_msg2", "<A HREF=igb.php>$l_igb_term</a>");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
		die();
	}
	bigtitle();

	if($zoneinfo['zone_id'] != 3){
		$res2 = $db->Execute("SELECT SUM(amount) as total_bounty FROM $dbtables[bounty] WHERE placed_by = 0 AND bounty_on = $playerinfo[player_id]");
		if ($res2)
		{
			$bty = $res2->fields;
			if ($bty['total_bounty'] > 0)
			{
				if ($pay < 1)
				{
					echo $l_port_bounty;
					$l_port_bounty2 = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_bounty2);
					echo $l_port_bounty2 . "<BR>";
					echo "<A HREF=\"bounty.php\">$l_by_placebounty</A><BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");
					die();
				}
				elseif($pay==2){
				// Make bounty payments
				$getbountyid = $db->Execute("SELECT * FROM $dbtables[bounty] WHERE placed_by = 0  and bounty_on=$playerinfo[player_id] order by bounty_id ");
							db_op_result($getbountyid,__LINE__,__FILE__);
						$pmt_amt=StripNonNum($pmt_amt);
						  if (($pmt_amt=="") or ($pmt_amt <= 0))
 								$pmt_amt=0;		
						if ($pmt_amt > $playerinfo[credits]){	
							$pmt_amt=$playerinfo[credits];
							}
						$temptotal=$pmt_amt;	
						if ($getbountyid->RecordCount() > 0)
						{
							while (!$getbountyid->EOF)
							{
								$bounty = $getbountyid->fields;
								if ($bounty['amount']<= $temptotal){
								$bountyupdate = $db->Execute("delete from  $dbtables[bounty]  where bounty_id=$bounty[bounty_id]");
								db_op_result($bountyupdate,__LINE__,__FILE__);
								$temptotal=$temptotal-$bounty['amount'];
								}else{
								
								$bountyupdate = $db->Execute("update $dbtables[bounty] set amount=amount-$temptotal where bounty_id=$bounty[bounty_id]");
								$temptotal=0;
								db_op_result($bountyupdate,__LINE__,__FILE__);
								}
							
							$getbountyid->MoveNext();	
							}
						}
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$pmt_amt WHERE player_id = $playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$smarty->assign("error_msg", $l_port_bountypaid);
						$smarty->assign("error_msg2", "");
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."genericdie.tpl");
						include ("footer.php");
						die();
				}
				else
				{
					if ($playerinfo['credits'] < $bty['total_bounty'])
					{
						$l_port_btynotenough = str_replace("[amount]",NUMBER($bty['total_bounty']),$l_port_btynotenough);
						$l_creds_to_spend=str_replace("[credits]",NUMBER($playerinfo['credits']),$l_creds_to_spend);
						$smarty->assign("notenough", $l_port_btynotenough);
						$smarty->assign("l_pay_partial", $l_pay_partial);
						$smarty->assign("l_creds_to_spend", $l_creds_to_spend);
						$smarty->assign("l_pay_button", $l_pay_button);
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."bountypay.tpl");
						include ("footer.php");
						die();
					}
					else
					{
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$bty[total_bounty] WHERE player_id = $playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

						$debug_query = $db->Execute("DELETE from $dbtables[bounty] WHERE bounty_on = $playerinfo[player_id] AND placed_by = 0");
						db_op_result($debug_query,__LINE__,__FILE__);

						$smarty->assign("error_msg", $l_port_bountypaid);
						$smarty->assign("error_msg2", "");
						$smarty->assign("gotomain", $l_global_mmenu);
						$smarty->display($templatename."genericdie.tpl");
						include ("footer.php");
						die();
					}
				}
			}
		}
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

	$fighter_max = NUM_FIGHTERS($shipinfo['computer']);
	$fighter_free = $fighter_max - $shipinfo['fighters'];
	$torpedo_max = NUM_TORPEDOES($shipinfo['torp_launchers']);
	$torpedo_free = $torpedo_max - $shipinfo['torps'];
	$armour_max = NUM_ARMOUR($shipinfo['armour']);
	$armour_free = $armour_max - $shipinfo['armour_pts'];
	$colonist_max = NUM_HOLDS($shipinfo['hull']) - $shipinfo['ore'] - $shipinfo['organics'] - $shipinfo['goods'];
	$colonist_free = $colonist_max - $shipinfo['colonists'];

cleanjs('');
	echo $cleanjs;
	TEXT_JAVASCRIPT_BEGIN();

echo "function MakeMax(name, val)\n";
echo "{\n";
echo " if (document.forms[0].elements[name].value != val)\n";
echo " {\n";
echo "	if (val != 0)\n";
echo "	{\n";
echo "	document.forms[0].elements[name].value = val;\n";
echo "	}\n";
echo " }\n";
echo "}\n";

echo "function Comma(number) {\n";
echo "number = '' + number;\n";
echo "if (number.length > 3) {\n";
echo "var mod = number.length % 3;\n";
echo "var output = (mod > 0 ? (number.substring(0,mod)) : '');\n";
echo "for (i=0 ; i < Math.floor(number.length / 3); i++) {\n";
echo "if ((mod == 0) && (i == 0))\n";
echo "output += number.substring(mod+ 3 * i, mod + 3 * i + 3);\n";
echo "else\n";
echo "output+= ',' + number.substring(mod + 3 * i, mod + 3 * i + 3);\n";
echo "}\n";
echo "return (output);\n";
echo "}\n";
echo "else return number;\n";
echo "}\n";

// changeDelta function //
echo "function changeDelta(desiredvalue,currentvalue)\n";
echo "{\n";
echo "	Delta=0; DeltaCost=0;\n";
echo "	Delta = desiredvalue - currentvalue;\n";
echo "\n";
echo "	while (Delta>0) \n";
echo "	{\n";
echo "	 DeltaCost=DeltaCost + Math.pow($upgrade_factor,desiredvalue-Delta); \n";
echo "	 Delta=Delta-1;\n";
echo "	}\n";
echo "\n";
echo "	DeltaCost=DeltaCost * $upgrade_cost * $alliancefactor\n";
echo "	return DeltaCost;\n";
echo "}\n";

echo "function countTotal()\n";
echo "{\n";
echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
echo "clean_js()\n";
echo "var form = document.forms[0];\n";
echo "var i = form.elements.length;\n";
echo "while (i > 0)\n";
echo "	{\n";
echo " if (form.elements[i-1].value == '')\n";
echo "	{\n";
echo "	form.elements[i-1].value ='0';\n";
echo "	}\n";
echo " i--;\n";
echo "}\n";
echo "// Here we set all 'Max' items to 0 if they are over max - player amt.\n";
echo "if (($fighter_free < form.fighter_number.value) && (form.fighter_number.value != 'Full'))\n";
echo " {\n";
echo " form.fighter_number.value=0\n";
echo " }\n";
echo "if (($torpedo_free < form.torpedo_number.value) && (form.torpedo_number.value != 'Full'))\n";
echo "	{\n";
echo "	form.torpedo_number.value=0\n";
echo "	}\n";
echo "if (($armour_free < form.armour_number.value) && (form.armour_number.value != 'Full'))\n";
echo "	{\n";
echo "	form.armour_number.value=0\n";
echo "	}\n";
echo "if (($colonist_free < form.colonist_number.value) && (form.colonist_number.value != 'Full' ))\n";
echo "	{\n";
echo "	form.colonist_number.value=0\n";
echo "	}\n";
echo "// Done with the bounds checking\n";
echo "// Pluses must be first, or if empty will produce a javascript error\n";
echo "form.total_cost.value =\n";
echo "changeDelta(form.hull_upgrade.value,$shipinfo[hull])\n";
echo "+ changeDelta(form.engine_upgrade.value,$shipinfo[engines])\n";
echo "+ changeDelta(form.power_upgrade.value,$shipinfo[power])\n";
echo "+ changeDelta(form.computer_upgrade.value,$shipinfo[computer])\n";
echo "+ changeDelta(form.sensors_upgrade.value,$shipinfo[sensors])\n";
echo "+ changeDelta(form.beams_upgrade.value,$shipinfo[beams])\n";
echo "+ changeDelta(form.armour_upgrade.value,$shipinfo[armour])\n";
echo "+ changeDelta(form.cloak_upgrade.value,$shipinfo[cloak])\n";
echo "+ changeDelta(form.torp_launchers_upgrade.value,$shipinfo[torp_launchers])\n";
echo "+ changeDelta(form.shields_upgrade.value,$shipinfo[shields])\n";
echo "+ changeDelta(form.ecm_upgrade.value,$shipinfo[ecm])\n";

	if(!class_exists($shipinfo['computer_class'])){
		include ("class/" . $shipinfo['computer_class'] . ".inc");
	}

	$computerobject = new $shipinfo['computer_class']();
	$fighter_price_modifier = $computerobject->fighter_price_modifier;
	if ($shipinfo['fighters'] != $fighter_max)
	{
	echo "+ form.fighter_number.value * $fighter_price * $alliancefactor * $fighter_price_modifier ";
	}

	if(!class_exists($shipinfo['torp_class'])){
		include ("class/" . $shipinfo['torp_class'] . ".inc");
	}

	$torpobject = new $shipinfo['torp_class']();
	$torp_price_modifier = $torpobject>torp_price_modifier;
	if ($shipinfo['torps'] != $torpedo_max)
	{
	echo "+ form.torpedo_number.value * $torpedo_price * $alliancefactor * $torp_price_modifier ";
	}

	if(!class_exists($shipinfo['armor_class'])){
		include ("class/" . $shipinfo['armor_class'] . ".inc");
	}

	$armorobject = new $shipinfo['armor_class']();
	$armor_price_modifier = $armorobject->armor_price_modifier;
	if ($shipinfo['armour_pts'] != $armour_max)
	{
	echo "+ form.armour_number.value * $armour_price * $alliancefactor * $armor_price_modifier ";
	}
	if ($shipinfo['colonists'] != $colonist_max)
	{
	echo "+ form.colonist_number.value * $colonist_price * $alliancefactor ";
	}
	echo ";\n";
	echo"form.total_cost2.value = Comma(form.total_cost.value);\n";
	echo "	if (form.total_cost.value > $playerinfo[credits])\n";
	echo "	{\n";
	echo "	form.total_cost.value = '$l_no_credits';\n";
	echo"		form.total_cost2.value = form.total_cost.value;\n";
//	echo "	form.total_cost.value = 'You are short '+(form.total_cost.value - $playerinfo[credits]) +' credits';\n";
	echo "	}\n";
	echo "	form.total_cost.length = form.total_cost.value.length;\n";
	echo "\n";
	echo "form.engine_costper.value=Comma(changeDelta(form.engine_upgrade.value,$shipinfo[engines]));\n";
	echo "form.power_costper.value=Comma(changeDelta(form.power_upgrade.value,$shipinfo[power]));\n";
	echo "form.computer_costper.value=Comma(changeDelta(form.computer_upgrade.value,$shipinfo[computer]));\n";
	echo "form.sensors_costper.value=Comma(changeDelta(form.sensors_upgrade.value,$shipinfo[sensors]));\n";
	echo "form.beams_costper.value=Comma(changeDelta(form.beams_upgrade.value,$shipinfo[beams]));\n";
	echo "form.armour_costper.value=Comma(changeDelta(form.armour_upgrade.value,$shipinfo[armour]));\n";
	echo "form.cloak_costper.value=Comma(changeDelta(form.cloak_upgrade.value,$shipinfo[cloak]));\n";
	echo "form.torp_launchers_costper.value=Comma(changeDelta(form.torp_launchers_upgrade.value,$shipinfo[torp_launchers]));\n";
	echo "form.hull_costper.value=Comma(changeDelta(form.hull_upgrade.value,$shipinfo[hull]));\n";
	echo "form.shields_costper.value=Comma(changeDelta(form.shields_upgrade.value,$shipinfo[shields]));\n";
	echo "form.ecm_costper.value=Comma(changeDelta(form.ecm_upgrade.value,$shipinfo[ecm]));\n";
	echo "}";
	TEXT_JAVASCRIPT_END();

	$onblur = "ONBLUR=\"countTotal()\"";
	$onfocus =	"ONFOCUS=\"countTotal()\"";
	$onchange =	"ONCHANGE=\"countTotal()\"";
	$onclick =	"ONCLICK=\"countTotal()\"";

// Create dropdowns when called
function dropdown($element_name,$current_value, $max_value)
{
global $onchange;
$i = $current_value;
$dropdownvar = "<select size='1' name='$element_name'";
$dropdownvar = "$dropdownvar $onchange>\n";
while ($i <= $max_value)
 {
 if ($current_value == $i)
	{
	$dropdownvar = "$dropdownvar		<option value='$i' selected>$i</option>\n";
	}
 else
	{
	$dropdownvar = "$dropdownvar		<option value='$i'>$i</option>\n";
	}
 $i++;
 }
$dropdownvar = "$dropdownvar		 </select>\n";
return $dropdownvar;
}


	echo "<P>\n";
	$l_creds_to_spend=str_replace("[credits]",NUMBER($playerinfo['credits']),$l_creds_to_spend);
	echo "$l_creds_to_spend<BR>\n";
	if ($allow_ibank)
	{
	$igblink = "\n<A HREF=igb.php>$l_igb_term</a>";
	$l_ifyouneedmore=str_replace("[igb]",$igblink,$l_ifyouneedmore);

	echo "$l_ifyouneedmore<BR>";
	}
	echo "\n";
	echo "<A HREF=\"bounty.php\">$l_by_placebounty</A><BR>\n";
	echo " <FORM ACTION=port_purchase_upgrades.php METHOD=POST>\n";
	echo "	<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">\n";
	echo "	 <TR BGCOLOR=\"$color_header\">\n";
	echo "	<TD><B>$l_ship_levels</B></TD>\n";
	echo "	<TD><B>$l_cost</B></TD>\n";
	echo "	<TD><B>$l_current_level</B></TD>\n";
	echo "	<TD><B>$l_upgrade</B></TD>\n";
	echo "	 </TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_hull</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts1' name=hull_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['hull']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['hull'] == $shipinfo['hull_normal']){
		echo dropdown("hull_upgrade",$shipinfo['hull'], $classinfo['maxhull']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"hull_upgrade\" value=\"" . $shipinfo['hull'] . "\">";
	}
	echo "	</TD>\n";
	echo "	 </TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_engines</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=engine_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['engines']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['engines'] == $shipinfo['engines_normal']){
		echo dropdown("engine_upgrade",$shipinfo['engines'], $classinfo['maxengines']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"engine_upgrade\" value=\"" . $shipinfo['engines'] . "\">";
	}
	echo "	</TD>\n";
	echo "	 </TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_power</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts1' name=power_costper VALUE='0' tabindex='-1' $onblur></td>\n";
	echo "	<TD>" . NUMBER($shipinfo['power']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['power'] == $shipinfo['power_normal']){
		echo dropdown("power_upgrade",$shipinfo['power'], $classinfo['maxpower']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"power_upgrade\" value=\"" . $shipinfo['power'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_computer</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=computer_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['computer']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['computer'] == $shipinfo['computer_normal']){
		echo dropdown("computer_upgrade",$shipinfo['computer'], $classinfo['maxcomputer']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"computer_upgrade\" value=\"" . $shipinfo['computer'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_sensors</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts1' name=sensors_costper VALUE='0' tabindex='-1' $onblur></td>\n";
	echo "	<TD>" . NUMBER($shipinfo['sensors']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['sensors'] == $shipinfo['sensors_normal']){
		echo dropdown("sensors_upgrade",$shipinfo['sensors'], $classinfo['maxsensors']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"sensors_upgrade\" value=\"" . $shipinfo['sensors'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_beams</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=beams_costper VALUE='0' tabindex='-1' $onblur></td>";
	echo "	<TD>" . NUMBER($shipinfo['beams']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['beams'] == $shipinfo['beams_normal']){
		echo dropdown("beams_upgrade",$shipinfo['beams'], $classinfo['maxbeams']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"beams_upgrade\" value=\"" . $shipinfo['beams'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_armour</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts1' name=armour_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['armour']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['armour'] == $shipinfo['armour_normal']){
		echo dropdown("armour_upgrade",$shipinfo['armour'], $classinfo['maxarmour']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"armour_upgrade\" value=\"" . $shipinfo['armour'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_cloak</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=cloak_costper VALUE='0' tabindex='-1' $onblur $onfocus></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['cloak']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['cloak'] == $shipinfo['cloak_normal']){
		echo dropdown("cloak_upgrade",$shipinfo['cloak'], $classinfo['maxcloak']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"cloak_upgrade\" value=\"" . $shipinfo['cloak'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_ecm</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=ecm_costper VALUE='0' tabindex='-1' $onblur $onfocus></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['ecm']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['ecm'] == $shipinfo['ecm_normal']){
		echo dropdown("ecm_upgrade",$shipinfo['ecm'], $classinfo['maxecm']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"ecm_upgrade\" value=\"" . $shipinfo['ecm'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_torp_launch</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts1' name=torp_launchers_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['torp_launchers']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['torp_launchers'] == $shipinfo['torp_launchers_normal']){
		echo dropdown("torp_launchers_upgrade",$shipinfo['torp_launchers'], $classinfo['maxtorp_launchers']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"torp_launchers_upgrade\" value=\"" . $shipinfo['torp_launchers'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_shields</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts2' name=shields_costper VALUE='0' tabindex='-1' $onblur></TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['shields']) . "</TD>\n";
	echo "	<TD>\n		 ";
	if($shipinfo['shields'] == $shipinfo['shields_normal']){
		echo dropdown("shields_upgrade",$shipinfo['shields'], $classinfo['maxshields']);
	}
	else
	{
		echo $l_ports_needrepair;
		echo "<input type=\"hidden\" name=\"shields_upgrade\" value=\"" . $shipinfo['shields'] . "\">";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo " </TABLE>\n";
	echo " <BR>\n";
	echo " <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">\n";
	echo "	<TR BGCOLOR=\"$color_header\">\n";
	echo "	<TD><B>$l_item</B></TD>\n";
	echo "	<TD><B>$l_cost</B></TD>\n";
	echo "	<TD><B>$l_current</B></TD>\n";
	echo "	<TD><B>$l_max</B></TD>\n";
	echo "	<TD><B>$l_qty</B></TD>\n";
	echo "	<TD><B>$l_item</B></TD>\n";
	echo "	<TD><B>$l_cost</B></TD>\n";
	echo "	<TD><B>$l_current</B></TD>\n";
	echo "	<TD><B>$l_max</B></TD>\n";
	echo "	<TD><B>$l_qty</B></TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_fighters</TD>\n";
	echo "	<TD>" . NUMBER($fighter_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['fighters']) . " / " . NUMBER($fighter_max) . "</TD>\n";
	echo "	<TD>";
	if ($shipinfo['fighters'] != $fighter_max)
	{
	echo "<a href='#' onClick=\"MakeMax('fighter_number', $fighter_free);countTotal();return false;\"; $onblur>" . NUMBER($fighter_free) . "</a></TD>\n";
	echo "	<TD><INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
	}
	else
	{
	echo "0<TD><input type=text readonly class='portcosts4' NAME=fighter_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
	}
	echo "	</TD>\n";
	echo "	<TD>$l_torps</TD>\n";
	echo "	<TD>" . NUMBER($torpedo_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['torps']) . " / " . NUMBER($torpedo_max) . "</TD>\n";
	echo "	<TD>";
	if ($shipinfo['torps'] != $torpedo_max)
	{
	echo "<a href='#' onClick=\"MakeMax('torpedo_number', $torpedo_free);countTotal();return false;\"; $onblur>" . NUMBER($torpedo_free) . "</a></TD>\n";
	echo "	<TD><INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
	}
	else
	{
	echo "0<TD><input type=text readonly class='portcosts4' NAME=torpedo_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
	}
	echo "</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_armourpts</TD>\n";
	echo "	<TD>" . NUMBER($armour_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['armour_pts']) . " / " . NUMBER($armour_max) . "</TD>\n";
	echo "	<TD>";
	if ($shipinfo['armour_pts'] != $armour_max)
	{
	echo "<a href='#' onClick=\"MakeMax('armour_number', $armour_free);countTotal();return false;\"; $onblur>" . NUMBER($armour_free) . "</a></TD>\n";
	echo "	<TD><INPUT TYPE=TEXT NAME=armour_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
	}
	else
	{
	echo "0<TD><input type=text readonly class='portcosts5' NAME=armour_number MAXLENGTH=10 VALUE=$l_full tabindex='-1' $onblur>";
	}
	echo "</TD>\n";
	echo "	<TD>$l_colonists</TD>\n";
	echo "	<TD>" . NUMBER($colonist_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['colonists']) . " / ". NUMBER($colonist_max). "</TD>\n";
	echo "	<TD>";
	if ($shipinfo['colonists'] != $colonist_max)
	{
	echo "<a href='#' onClick=\"MakeMax('colonist_number', $colonist_free);countTotal();return false;\"; $onblur>" . NUMBER($colonist_free) . "</a></TD>\n";
	echo "	<TD><INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>";
	}
	else
	{
	echo "0<TD><input type=text readonly class='portcosts5' NAME=colonist_number MAXLENGTH=10 VALUE=$l_full tabindex='-1' $onblur>";
	}
	echo "	</TD>\n";
	echo "	</TR>\n";
	echo " </TABLE><BR>\n";
echo"<input type='hidden' name='total_cost' value='0'>";
	echo " <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">\n";
	echo "	<TR>\n";
	echo "	<TD><INPUT TYPE=SUBMIT VALUE=$l_buy $onclick></TD>\n";
	echo "	<TD ALIGN=RIGHT>$l_totalcost: <INPUT TYPE=TEXT style=\"text-align:right\" NAME=total_cost2 SIZE=32 VALUE=0 $onfocus $onblur $onchange $onclick></td>\n";
	echo "	</TR>\n";
	echo " </TABLE>\n";
	echo "</FORM>\n";
	echo "$l_would_dump <A HREF=colonist_dump.php>$l_here</A>.\n";

echo "\n";
echo "<BR><BR>\n";
TEXT_GOTOMAIN();
echo "\n";

include ("footer.php");

?>
