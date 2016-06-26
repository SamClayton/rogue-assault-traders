<?php
if (preg_match("/port_devices.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

	$title=$l_device_port_title;
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

	$emerwarp_free = $max_emerwarp - $shipinfo['dev_emerwarp'];
	$res=$db->Execute("SELECT count(probe_id) as probe_num from $dbtables[probe] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='N'");
	$probe_num = $res->fields['probe_num'];
	$probes_free = $max_probes - $probe_num;

	$res=$db->Execute("SELECT count(spy_id) as spy_num from $dbtables[spies] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='N'");
	$spy_num = $res->fields['spy_num'];
	$spies_free = $max_spies - $spy_num;

	$res=$db->Execute("SELECT count(dig_id) as dig_num from $dbtables[dignitary] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] and active='N'");
	$dig_num = $res->fields['dig_num'];
	$digs_free = $max_digs - $dig_num;

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
echo "if (($emerwarp_free < form.dev_emerwarp_number.value) && (form.dev_emerwarp_number.value != 'Full'))\n";
echo " {\n";
echo " form.dev_emerwarp_number.value=0\n";
echo " }\n";

echo "if (($probes_free < form.probe_number.value) && (form.probe_number.value != 'Full'))\n";
echo " {\n";
echo " form.probe_number.value=0\n";
echo " }\n";

echo "if (($spies_free < form.spy_number.value) && (form.spy_number.value != 'Full'))\n";
echo " {\n";
echo " form.spy_number.value=0\n";
echo " }\n";

echo "if (($digs_free < form.dig_number.value) && (form.dig_number.value != 'Full'))\n";
echo " {\n";
echo " form.dig_number.value=0\n";
echo " }\n";

echo "// Done with the bounds checking\n";
echo "// Pluses must be first, or if empty will produce a javascript error\n";
echo "form.total_cost.value = form.dev_genesis_number.value * $dev_genesis_price * $alliancefactor\n";
echo "+ form.dev_sectorgenesis_number.value * $dev_sectorgenesis_price * $alliancefactor \n";
echo "+ form.dev_beacon_number.value * $dev_beacon_price * $alliancefactor\n";
if ($emerwarp_free > 0)
{
	echo "+ form.dev_emerwarp_number.value * $dev_emerwarp_price * $alliancefactor\n";
}
echo "+ form.dev_warpedit_number.value * $dev_warpedit_price * $alliancefactor\n";
echo "+ form.elements['dev_minedeflector_number'].value * $dev_minedeflector_price * $alliancefactor\n";
///
echo "+ form.elements['spy_number'].value * $spy_price * $alliancefactor\n";

echo "+ form.elements['dig_number'].value * $dig_price * $alliancefactor\n";
echo "+ form.elements['probe_number'].value * $dev_probe * $alliancefactor\n";

if ($shipinfo['dev_escapepod'] == 'N')
{
	echo "+ (form.escapepod_purchase.checked ?	$dev_escapepod_price * $alliancefactor : 0)\n";
}
if ($shipinfo['dev_fuelscoop'] == 'N')
{
	echo "+ (form.fuelscoop_purchase.checked ?	$dev_fuelscoop_price * $alliancefactor : 0)\n";
}
if($shipinfo['dev_nova'] == 'N' and $shipinfo['class'] >= $dev_nova_shiplimit)
{
	echo "+ (form.nova_purchase.checked ?	$dev_nova_price * $alliancefactor : 0)\n";
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
	echo " <FORM ACTION=port_purchase_devices.php METHOD=POST>\n";
	echo "	<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">\n";
	echo "	 <TR BGCOLOR=\"$color_header\">\n";
	echo "	<TD><B>$l_device</B></TD>\n";
	echo "	<TD><B>$l_cost</B></TD>\n";
	echo "	<TD><B>$l_current</B></TD>\n";
	echo "	<TD><B>$l_max</B></TD>\n";
	echo "	<TD><B>$l_qty</B></TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD></TD>\n";
	echo "	 </TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_beacons</TD>\n";
	echo "	<TD>" . NUMBER($dev_beacon_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_beacon']) . "</TD>\n";
	echo "	<TD>$l_unlimited</TD>\n";
	echo "	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	 </TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_genesis</TD>\n";
	echo "	<TD>" . NUMBER($dev_genesis_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_genesis']) . "</TD>\n";
	echo "	<TD>$l_unlimited</TD>\n";
	echo "	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	 </TR>\n";
	 echo "	 <TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_sectorgenesis</TD>\n";
	echo "	<TD>" . NUMBER($dev_sectorgenesis_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_sectorgenesis']) . "</TD>\n";
	echo "	<TD>$l_unlimited</TD>\n";
	echo "	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_sectorgenesis_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	 </TR>\n";
echo "	<TR BGCOLOR=\"$color_line1\">\n";

	echo "	<TD>$l_probe</TD>\n";
	echo "	<TD>". NUMBER($dev_probe * $alliancefactor) ."</TD>\n";
	echo "	<TD>". NUMBER($probe_num) . "</TD>\n";
	echo "	<TD>";
		if ($shipinfo['dev_probe'] != $max_probes)
	{
	echo"<a href='#' onClick=\"MakeMax('probe_number', $probes_free);countTotal();return false;\">";
	echo NUMBER($probes_free) . "</a></TD>\n";
	echo "	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=probe_number SIZE=4 MAXLENGTH=10 VALUE=0 $onblur></TD>\n";
	}
	else
	{
	echo "0</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts3' NAME=probe_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
	}
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	 <TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_ewd</TD>\n";
	echo "	<TD>" . NUMBER($dev_emerwarp_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_emerwarp']) . "</TD>\n";
	echo "	<TD>";
	if ($shipinfo['dev_emerwarp'] != $max_emerwarp)
	{
	echo"<a href='#' onClick=\"MakeMax('dev_emerwarp_number', $emerwarp_free);countTotal();return false;\">";
	echo NUMBER($emerwarp_free) . "</a></TD>\n";
	echo"	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur>";
	}
	else
	{
	echo "0</TD>\n";
	echo "	<TD><input type=text readonly class='portcosts3' NAME=dev_emerwarp_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
	}
	echo "</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_warpedit</TD>\n";
	echo "	<TD>" . NUMBER($dev_warpedit_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_warpedit']) . "</TD><TD>$l_unlimited</TD><TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></TD>";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	///
	if ($spy_success_factor)
	{
		echo "	<TD>$l_spy</TD>\n";
		echo "	<TD>". NUMBER($spy_price * $alliancefactor) ."</TD>\n";
		$res=$db->Execute("SELECT count(spy_id) as spy_num from $dbtables[spies] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id]");
		$spy_num = NUMBER($res->fields['spy_num']);
		echo "	<TD>$spy_num</TD>\n";
		echo "	<TD>";
		if ($$res->fields['spy_num'] != $max_spies)
		{
			echo"<a href='#' onClick=\"MakeMax('spy_number', $spies_free);countTotal();return false;\">";
			echo NUMBER($spies_free) . "</a></TD>\n";
			echo"	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=spy_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur>";
		}
		else
		{
			echo "0</TD>\n";
			echo "	<TD><input type=text readonly class='portcosts3' NAME=spy_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
		}
	}
	else
	{
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	}
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
 if($dig_success_factor)
	{
	echo "	<TD>$l_dig</TD>\n";
	echo "	<TD>". NUMBER($dig_price * $alliancefactor) ."</TD>\n";
		$res=$db->Execute("SELECT count(dig_id) as dig_num from $dbtables[dignitary] where owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id]");
		$dig_num = NUMBER($res->fields['dig_num']);
	echo "	<TD>$dig_num</TD>\n";
	echo "	<TD>";
			if ($$res->fields['dig_num'] != $max_digs)
		{
			echo"<a href='#' onClick=\"MakeMax('dig_number', $digs_free);countTotal();return false;\">";
			echo NUMBER($digs_free) . "</a></TD>\n";
			echo"	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dig_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur>";
		}
		else
		{
			echo "0</TD>\n";
			echo "	<TD><input type=text readonly class='portcosts3' NAME=dig_number MAXLENGTH=10 VALUE=$l_full $onblur tabindex='-1'>";
		}

	}
	else
	{
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	}
	 
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_deflect</TD>\n";
	echo "	<TD>" . NUMBER($dev_minedeflector_price * $alliancefactor) . "</TD>\n";
	echo "	<TD>" . NUMBER($shipinfo['dev_minedeflector']) . "</TD>\n";
	echo "	<TD>$l_unlimited</TD>\n";
	echo "	<TD><INPUT TYPE=TEXT style='text-align:right' NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=10 VALUE=0 $onblur></TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_escape_pod</TD>\n";
	echo "	<TD>" . NUMBER($dev_escapepod_price * $alliancefactor) . "</TD>\n";
	if ($shipinfo['dev_escapepod'] == "N")
	{
	echo "	<TD>$l_none</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD><INPUT TYPE=CHECKBOX NAME=escapepod_purchase VALUE=1 $onclick></TD>\n";
	}
	else
	{
	echo "	<TD>$l_equipped</TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD>$l_n_a</TD>\n";
	}
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line2\">\n";
	echo "	<TD>$l_fuel_scoop</TD>\n";
	echo "	<TD>" . NUMBER($dev_fuelscoop_price * $alliancefactor) . "</TD>\n";
	if ($shipinfo['dev_fuelscoop'] == "N")
	{
	echo "	<TD>$l_none</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase VALUE=1 $onclick></TD>\n";
	}
	else
	{
	echo "	<TD>$l_equipped</TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD>$l_n_a</TD>\n";
	}
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	</TR>\n";
	echo "	<TR BGCOLOR=\"$color_line1\">\n";
	echo "	<TD>$l_nova&nbsp;&nbsp;($l_ports_shiplimit$dev_nova_shiplimit)</TD>\n";
	echo "	<TD>" . NUMBER($dev_nova_price * $alliancefactor) . "</TD>\n";
	if($shipinfo['dev_nova'] == "N" and $shipinfo['class'] >= $dev_nova_shiplimit)
	{
	echo "	<TD>$l_none</TD>\n";
	echo "	<TD>&nbsp;</TD>\n";
	echo "	<TD><INPUT TYPE=CHECKBOX NAME=nova_purchase VALUE=1 $onclick></TD>\n";
	}
	else
	{
	if($shipinfo['class'] >= $dev_nova_shiplimit)
	echo "	<TD>$l_equipped</TD>\n";
	else echo "	<TD>$l_none</TD>\n";
	echo "	<TD></TD>\n";
	echo "	<TD>$l_n_a</TD>\n";
	}
	 
	echo " </TABLE>\n";
	echo " <BR>\n";
echo"<input type='hidden' name='total_cost' value='0'>";
	echo " <TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0 bgcolor=\"#000000\">\n";
	echo "	<TR>\n";
	echo "	<TD><INPUT TYPE=SUBMIT VALUE=$l_buy $onclick></TD>\n";
	echo "	<TD ALIGN=RIGHT>$l_totalcost: <INPUT TYPE=TEXT style=\"text-align:right\" NAME=total_cost2 SIZE=22 VALUE=0 $onfocus $onblur $onchange $onclick></td>\n";
	echo "	</TR>\n";
	echo " </TABLE>\n";
	echo "</FORM>\n";
	echo "$l_would_dump <A HREF=colonist_dump.php>$l_here</A>.\n";
	///
	if ($spy_success_factor)
	echo "<BR><BR>$l_spy_cleanupship <A HREF=spy.php?command=cleanup_ship>$l_here</A>.";

echo "\n";
echo "<BR><BR>\n";
TEXT_GOTOMAIN();
echo "\n";

include ("footer.php");

?>
