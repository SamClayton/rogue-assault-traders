<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: showprobe.php

include ("config/config.php");
include ("languages/$langdir/lang_probes.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
$no_gzip = 1;

$probe_id = '';
if (isset($_GET['probe_id']))
{
	$probe_id = $_GET['probe_id'];
}

if (checklogin() or $tournament_setup_access == 1)
{
	include ("footer.php");
	die();
}

$title = $l_probe_title;

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

$probe_id = stripnum($probe_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[probe] WHERE probe_id=$probe_id and active='Y'");
if ($result3)
	$probeinfo=$result3->fields;

if ((!isset($command)) || ($command == ''))
{
	$command = '';
}

if ((!isset($destroy)) || ($destroy == ''))
{
	$destroy = '';
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

if (!empty($probeinfo))
/* if there is a probe in the sector show appropriate menu */
{
	if ($shipinfo['sector_id'] == $probeinfo['sector_id'])
	{
		if ($probeinfo['owner_id'] != 0)
		{
			$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$probeinfo[owner_id]");
			$ownerinfo = $result3->fields;

			$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$probeinfo[owner_id] AND ship_id=$ownerinfo[currentship]");
			$ownershipinfo = $res->fields;
		}

		if (empty($command))
		{
			/* ...if there is no probe command already */
			$l_probe_named=str_replace("[name]",$ownerinfo['character_name'],$l_probe_named);
			$l_probe_named=str_replace("[probename]",$probeinfo['probe_id'],$l_probe_named);
			$l_probe_named=str_replace("[sector]",$probeinfo['sector_id'],$l_probe_named);
			echo "$l_probe_named<BR><BR>";

			if ($playerinfo['player_id'] == $probeinfo['owner_id'])
			{
				if ($destroy==1)
				{
					echo "<font color=red>$l_probe_confirm</font><br><A HREF='showprobe.php?probe_id=$probe_id&destroy=2'>$l_yes</A><br>";
					echo "<A HREF='showprobe.php?probe_id=$probe_id'>$l_no!</A><BR><br>";
				}
				elseif ($destroy==2)
				{
					if ($playerinfo[turns] > 0)
					{
						$debug_query = $db->Execute("DELETE from $dbtables[probe] where probe_id=$probe_id");
						db_op_result($debug_query,__LINE__,__FILE__);
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						close_database();
						echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
					}
					else
					{
						if ($playerinfo[turns] < 1)
							echo "$l_probe2_turn<br>";
					}
				}
				else
				{
					echo "<A onclick=\"javascript: alert ('alert:$l_probe_warning');\" HREF='showprobe.php?probe_id=$probe_id&destroy=1'>$l_probe_destroyprobe</a><br>";
				}
			}

			if ($probeinfo['owner_id'] == $playerinfo['player_id']&& $probeinfo['owner_id'] > 0)
			{
				/* owner menu */
				echo "$l_turns_have $playerinfo[turns]<p>";
				$ptype="l_probe_type$probeinfo[type]";
				$ptype=${$ptype};
				$l_probe_ordersout =str_replace("[type]",$ptype,$l_probe_order2);
				$l_probe_ordersout =str_replace("[target]",$probeinfo['target_sector'],$l_probe_ordersout);
				echo $l_probe_ordersout."<p>";
				$l_probe_name =str_replace("[name]",$l_probe_name_link,$l_probe_name2);
				$l_probe_pickup_link="<a href='showprobe.php?probe_id=$probe_id&command=pickup'>" . $l_probe_pickup_link . "</a>";
				$l_probe_pickup=str_replace("[pickup]",$l_probe_pickup_link,$l_probe_pickup);
				echo "$l_probe_pickup<p>";
				$l_probe_orders_link="<a href='showprobe.php?probe_id=$probe_id&command=orders'>" . $l_probe_orders_link . "</a>";
				$l_probe_orders=str_replace("[orders]",$l_probe_orders_link,$l_probe_orders);
				echo "$l_probe_orders<p>";
				$l_probe_pickup_link="<a href='showprobe.php?probe_id=$probe_id&command=pickup'>" . $l_probe_pickup_link . "</a>";
				$l_probe_pickup=str_replace("[pickup]",$l_probe_pickup_link,$l_pickup_orders);
				echo "$l_probe_pickup<p>";
				$l_probe_upgrade_link="<a href='showprobe.php?probe_id=$probe_id&command=defenses'>" . $l_probe_upgrade_link . "</a>";
				$l_probe_upgrade=str_replace("[upgrade]",$l_probe_upgrade_link,$l_probe_upgrade);
				echo $l_probe_upgrade;
				/* change production rates */
				echo "<p>\n\n";
				cleanjs('');
				echo $cleanjs;

				echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>";
				echo "<TR BGCOLOR=\"$color_header\"><TD></TD><TD></TD><TD><B>$l_probe_engine</B></TD><TD><B>$l_probe_sensors</B></TD>";
				echo "<TD><B>$l_probe_cloak</B></TD></TR>";
				echo "<TR BGCOLOR=\"$color_line2\"><TD>" . $l_probe_defense_levels . "</TD>";
				echo "<td></td>";
				echo "<TD>" . NUMBER($probeinfo['engines']) . "</TD>";
				echo "<TD>" . NUMBER($probeinfo['sensors']) . "</TD>";
				echo "<TD>" . NUMBER($probeinfo['cloak']) . "</TD>";
				echo "</TR>";
				echo "</TABLE><BR><BR>";
			}
			else
			{
				/* visitor menu */
				if($probeinfo['owner_id'] != 3){
					$l_probe_att_link="<a href='showprobe.php?probe_id=$probe_id&command=attack'>" . $l_probe_att_link ."</a>";
					$l_probe_att=str_replace("[attack]",$l_probe_att_link,$l_probe_att);
				}
				$l_probe_scn_link="<a href='showprobe.php?probe_id=$probe_id&command=scan'>" . $l_probe_scn_link ."</a>";
				$l_probe_scn=str_replace("[scan]",$l_probe_scn_link,$l_probe_scn);

				if($probeinfo['owner_id'] != 3)
					echo "$l_probe_att<BR>";
			}
		}
		elseif ($command == "attack" && $probeinfo['owner'] != 3)
		{
			if ($playerinfo['turns'] > 0)
			{
				$debug_query = $db->Execute("DELETE from $dbtables[probe] where probe_id=$probe_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+1, turns=turns-1 WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo $l_probe_destroyed."<p>";
				close_database();
				echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
			}
			else
			{
				echo "$l_probe2_turn<br>";
			}
		}
		elseif ($probeinfo['owner_id'] == $playerinfo['player_id']  && $probeinfo['owner_id'] > 0)
		{
			/* player owns probe and there is a command */
			if ($command == "defenses"){
				/* defenses menu */
				cleanjs('');
				echo $cleanjs;
				TEXT_JAVASCRIPT_BEGIN();
				echo "function MakeMax(name, val)\n";
				echo "{\n";
				echo " if (document.forms[0].elements[name].value != val)\n";
				echo " {\n";
				echo "  if (val != 0)\n";
				echo "  {\n";
				echo "  document.forms[0].elements[name].value = val;\n";			  
				echo "  }\n";			  
				echo " }\n";	 
				echo "}\n";
				// changeDelta function //
				echo "function changeDelta(desiredvalue,currentvalue)\n";
				echo "{\n"; 
				echo "  Delta=0; DeltaCost=0;\n";
				echo "  Delta = desiredvalue - currentvalue;\n";
				echo "\n";
				echo "	while (Delta>0) \n";
				echo "	{\n";
				echo "	 DeltaCost=DeltaCost + Math.pow($upgrade_factor,desiredvalue-Delta); \n";
				echo "	 Delta=Delta-1;\n";
				echo "	}\n";
				echo "\n";
				echo "  DeltaCost=DeltaCost * $upgrade_cost\n";
				echo "  return DeltaCost;\n";
				echo "}\n";
				echo "function countTotal()\n";
				echo "{\n";
				echo "// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)\n";
				echo "// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.\n";
				echo "clean_js()\n";
				echo "var form = document.forms[0];\n";
				echo "var i = form.elements.length;\n";
				echo "while (i > 0)\n";
				echo "  {\n";
				echo " if (form.elements[i-1].value == '')\n";
				echo "  {\n";
				echo "  form.elements[i-1].value ='0';\n";
				echo "  }\n";
				echo " i--;\n";
				echo "}\n";
				echo "// Pluses must be first, or if empty will produce a javascript error\n";
				echo "form.total_cost.value =\n";
				//  echo "+ changeDelta(form.power_upgrade.value,$planetinfo[power])\n";
				echo "changeDelta(form.sensors_upgrade.value,$probeinfo[sensors])\n";
				echo "+ changeDelta(form.cloak_upgrade.value,$probeinfo[cloak])\n";
				echo "+ changeDelta(form.engines_upgrade.value,$probeinfo[engines])\n";
				echo ";\n";
				echo "  if (form.total_cost.value > $playerinfo[credits])\n";
				echo "  {\n";
				echo "	form.total_cost.value = '$l_no_credits';\n";
				//  echo "	form.total_cost.value = 'You are short '+(form.total_cost.value - $playerinfo[credits]) +' credits';\n";
				echo "  }\n";
				echo "  form.total_cost.length = form.total_cost.value.length;\n";
				echo "\n";

				echo "form.sensors_costper.value=changeDelta(form.sensors_upgrade.value,$probeinfo[sensors]);\n";
				echo "form.cloak_costper.value=changeDelta(form.cloak_upgrade.value,$probeinfo[cloak]);\n";
				echo "form.engines_costper.value=changeDelta(form.engines_upgrade.value,$probeinfo[engines]);\n";
				echo "}";
				TEXT_JAVASCRIPT_END();

				$onblur = "ONBLUR=\"countTotal()\"";
				$onfocus =  "ONFOCUS=\"countTotal()\"";
				$onchange =  "ONCHANGE=\"countTotal()\"";
				$onclick =  "ONCLICK=\"countTotal()\"";
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
					$dropdownvar = "$dropdownvar	   </select>\n";
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

				echo "<FORM ACTION='showprobe_upgrade.php?probe_id=$probe_id&probeupgrade=yes' METHOD=POST>\n";
				echo "<TABLE WIDTH=\"100%\" BORDER=0 CELLSPACING=0 CELLPADDING=0>";
				echo"<TR BGCOLOR=\"$color_header\"><TD><B>" . $l_probe_defense_levels . "</B></TD><TD><B>$l_cost</B></TD><TD><B>$l_current_level</B></TD><TD><B>$l_upgrade</B></TD></TR>\n";

				// engine
				echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_probe_engine</TD>";
				echo "<td><input type=text readonly class='portcosts1' name=engines_costper  VALUE='0' tabindex='-1' $onblur></td>";
				echo "<TD>" . NUMBER($probeinfo['engines']) . "</TD>";
				echo "<TD>";
				echo dropdown("engines_upgrade",$probeinfo['engines'], 54);
				echo "</TD></TR>";

				// sensors
				echo "<TR BGCOLOR=\"$color_line2\"><TD>$l_sensors</TD>";
				echo "<td><input type=text readonly class='portcosts2' name=sensors_costper  VALUE='0' tabindex='-1' $onblur></td>";
				echo "<TD>" . NUMBER($probeinfo['sensors']) . "</TD>";
				echo "<TD>";
				echo dropdown("sensors_upgrade",$probeinfo['sensors'], 54);
				echo "</TD></TR>";

				// CLOAKS
				echo "<TR BGCOLOR=\"$color_line1\"><TD>$l_cloak</TD>";
				echo "<td><input type=text readonly class='portcosts1' name=cloak_costper  VALUE='0' tabindex='-1' $onblur></td>";
				echo "<TD>" . NUMBER($probeinfo['cloak']) . "</TD>";
				echo "<TD>";
				echo dropdown("cloak_upgrade",$probeinfo['cloak'], 54);
				echo "</TD></TR>";
				echo "<tr><TD><INPUT TYPE=SUBMIT VALUE=$l_buy $onclick></TD>\n";
				echo "<TD ALIGN=RIGHT>$l_totalcost: <INPUT TYPE=TEXT style=\"text-align:right\" NAME=total_cost SIZE=22 VALUE=0 $onfocus $onblur $onchange $onclick></td></tr>\n";
				//	echo "<INPUT TYPE=SUBMIT VALUE=$l_planet_transfer_link>&nbsp;<INPUT TYPE=RESET VALUE=Reset>";
				echo "</TABLE><BR>";
				echo "</FORM>";
			}
			elseif ($command == "orders")
			{
				if ($playerinfo['turns'] < 1)
				{
					echo "$l_plant_scn_turn<BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");
					die();
				}
				// Order probe to do something
				cleanjs('');
				echo $cleanjs;
				$selected0="";
				$selected1="";
				$selected2="";
				$selected3="";
				if ($probeinfo['type']==0){
					$selected0="selected";
				}elseif($probeinfo['type']==1){
					$selected1="selected";
				}elseif($probeinfo['type']==2){
					$selected2="selected";
				}elseif($probeinfo['type']==3){
					$selected3="selected";
				}elseif($probeinfo['type']==4){
					$selected4="selected";
				}
				echo "<form action=\"showprobe.php?probe_id=$probe_id&command=orders2\" method=\"post\">";
				echo "<table>";
				echo "<tr><td>$l_probe_type</td><td><select  name=\"type\" ><option value=\"0\" $selected0>$l_probe_type0</option><option value=\"1\" $selected1>$l_probe_type1</option><option value=\"2\" $selected2>$l_probe_type2</option><option value=\"3\" $selected3>$l_probe_type3</option><option value=\"4\" $selected4>$l_probe_type4</option></select></td></tr>";
				echo "<tr><td>$l_probe_target</td><td><input type=\"text\" name=\"target_sector\" size=\"6\" maxlength=\"6\" value=\"$probeinfo[target_sector]\"></td></tr>";
				echo "</table>";
				echo "<input type=\"submit\" value=\"$l_submit\" onclick=\"clean_js()\"><input type=\"reset\" value=\"$l_reset\">";
				echo "</form>";

				TEXT_GOTOMAIN();
				include ("footer.php");
			}
			elseif ($command == "orders2")
			{
				// Probe Ordered
				if ($playerinfo['turns'] < 1)
				{
					echo "$l_plant_scn_turn<BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");
					die();
				}
				$debug_query = $db->Execute("UPDATE $dbtables[probe] SET  type=$type,target_sector=$target_sector WHERE probe_id=$probe_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo $l_probe_ordered."<p>";
				TEXT_GOTOMAIN();
				include ("footer.php");
			}
			elseif ($command == "pickup")
			{
				// Probe Ordered
				if ($playerinfo['turns'] < 1)
				{
					echo "$l_plant_scn_turn<BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");
					die();
				}
				$debug_query = $db->Execute("UPDATE $dbtables[probe] SET  active='P' WHERE probe_id=$probe_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo $l_probe_pickedup."<p>";
				TEXT_GOTOMAIN();
				include ("footer.php");
			}
			elseif ($command == "scan")
			{
				/* scan menu */
				if ($playerinfo['turns'] < 1)
				{
					echo "$l_plant_scn_turn<BR><BR>";
					TEXT_GOTOMAIN();
					include ("footer.php");
					die();
				}
				probe_log($probeinfo['probe_id'],$probeinfo['owner'],$playerinfo['player_id'],PLOG_SCANNED);

				/* determine per cent chance of success in scanning target ship - based on player's sensors and opponent's probe's cloak */
				$success = (10 - $probeinfo['cloak'] / 2 + $shipinfo['sensors']) * 5;
				if ($success < 5)
				{
					$success = 5;
				}
				if ($success > 95)
				{
					$success = 95;
				}
				$roll = mt_rand(1, 100);
				if ($roll > $success)
				{
					/* if scan fails - inform both player and target. */
					echo "$l_probe_noscan<BR><BR>";
					TEXT_GOTOMAIN();
					playerlog($ownerinfo['player_id'], LOG_probe_SCAN_FAIL, "$probeinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
					include ("footer.php");
					die();
				}
				else
				{
					playerlog($ownerinfo['player_id'], LOG_probe_SCAN, "$probeinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
					/* scramble results by scan error factor. */
					$sc_error= SCAN_ERROR($shipinfo['sensors'], $probeinfo['cloak']);
					$sc_error_plus=100;
					if ($sc_error < 100){
						$sc_error_plus=115;
					}
					if (empty($probeinfo['name']))
						$probeinfo['name'] = $l_unnamed;
					$l_probe_scn_report=str_replace("[name]",$probeinfo['name'],$l_probe_scn_report);
					$l_probe_scn_report=str_replace("[owner]",$ownerinfo['character_name'],$l_probe_scn_report);
					echo "$l_probe_scn_report<BR><BR>";
					echo "<table>";
					echo "<tr><td>$l_commodities:</td><td></td>";
					echo "<tr><td>$l_organics:</td>";
					$roll = mt_rand(1, 100);
					if ($roll < $success)
					{
						$sc_probe_organics=NUMBER(round($probeinfo['organics'] * (mt_rand($sc_error , $sc_error_plus) / 100)));
						echo "<td>$sc_probe_organics</td></tr>";
					}
					else
					{
						echo "<td>???</td></tr>";
					}
					echo "</table><BR>";
				}
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1, turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				echo "$l_command_no<BR>";
			}
		}
	}
}
else
{
	echo "$l_probe_none<p>";
}
if ($command != "")
{
	echo "<BR><a href='showprobe.php?probe_id=$probe_id'>$l_clickme</a> $l_toprobemenu<BR><BR>";
}

TEXT_GOTOMAIN();

include ("footer.php");


?>