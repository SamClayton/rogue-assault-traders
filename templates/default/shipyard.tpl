<h1>{$title}</h1>

<center><font size=2 color=white><b>{$l_ship_welcome}</font></center><p>
  
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
							<tr bgcolor="black">
								<td width=121 align=center><font size=2 color=white><b>{$l_ship_class}</b></font></td>
								<td colspan=2 width=* align=center><font size=2 color=white><b>{$l_ship_properties}</b></font></TD>
							</tr>
							{php}
								$first = 1;
								for($i = 0; $i < $countship; $i++){
									echo "<tr bgcolor=\"black\"><td height=100 width=121 align=center valign=middle>" .
										 "<a style=\"text-decoration: none\" href=shipyard.php?stype=$currentshipid[$i]><img style=\"border: none\" src=$currentshipimage[$i]><br>" .
										 "<font size=2 color=white>&nbsp;<b>$currentshipname[$i]</a></b></font>";
									if($currentship[$i] != "")
										echo "<font size=2 color=white><br>($currentship[$i])</font>";
			if ($first == 1)
			{
				$first = 0;
				echo "</td><td rowspan=100 valign=top>";
				if($currentstorage == "1"){
					echo "<table border=0 cellpadding=0 bgcolor=\"black\" width=\"100%\">" .
						 "<tr><td valign=top>" .
						 "<font size=4 color=white><b>$name</b></font><p>" .
						 "<font size=2 color=silver><b>$description</b></font><p>" .
						 "</td><td width=215 height=191 background = templates/{$templatename}images/shipysg.gif align=center ><img src=$sship_img></td><td>&nbsp;&nbsp;</td></tr>" .
						 "</table>" .
						 "<table border=0 cellpadding=0>" .
						 "<tr><td colspan=2 valign=top><font size=4 color=white><b>$l_ship_levels</b></font><br>&nbsp;</td></tr>" .
						 "<tr><td width=300><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minhull / $l_ship_max: $sship_maxhull)&nbsp;&nbsp;</font>" .
						 "$l_hull&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$hull_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minengines / $l_ship_max: $sship_maxengines)&nbsp;&nbsp;</font>" .
						 "$l_engines&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$engines_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minpower / $l_ship_max: $sship_maxpower)&nbsp;&nbsp;</font>" .
						 "$l_power&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$power_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mincomputer / $l_ship_max: $sship_maxcomputer)&nbsp;&nbsp;</font>" .
						 "$l_computer&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$computer_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minsensors / $l_ship_max: $sship_maxsensors)&nbsp;&nbsp;</font>" .
						 "$l_sensors&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$sensors_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minarmour / $l_ship_max: $sship_maxarmour)&nbsp;&nbsp;</font>" .
						 "$l_armour&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$armour_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minshields / $l_ship_max: $sship_maxshields)&nbsp;&nbsp;</font>" .
						 "$l_shields&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$shields_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minbeams / $l_ship_max: $sship_maxbeams)&nbsp;&nbsp;</font>" .
						 "$l_beams&nbsp;&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$beams_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mintorp_launchers / $l_ship_max: $sship_maxtorp_launchers)&nbsp;&nbsp;</font>" .
						 "$l_torp_launch&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$torp_launchers_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mincloak / $l_ship_max: $sship_maxcloak)&nbsp;&nbsp;</font>" .
						 "$l_cloak&nbsp;</b></font>" .
						 "</td><td valign=bottom align=left>$cloak_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minecm / $l_ship_max: $sship_maxecm)&nbsp;&nbsp;</font>" .
						 "$l_ecm&nbsp;</b></font>" .
						 "<td valign=bottom align=left>$ecm_bars</td></tr>" .
						 "<tr><td><font color=white size=4><b><br>$l_ship_price: </b></td>" .
						 "<td><font color=red size=4><b><br>$newshipvalue C</b></td>" .
						 "</tr>" .
						 "<tr><td><font color=white size=4><b><br>$l_ship_turns: </b></td>" .
						 "<td><font color=red size=4><b><br>$sship_turnstobuild</b></td>" .
						 "</tr>" .
						 "</table><p>";
					if ($stype != $shipinfo_class)
					{
						echo "<form action=shipyard_purchase.php method=POST>" .
							 "<input type=hidden name=stype value=$stype>" .
							 "&nbsp;<input type=submit value=$l_ship_purchase>" .
							 "</form>";
					}
				}else{
					echo "<table border=0 cellpadding=0 bgcolor=\"black\">" .
						 "<tr><td valign=top>" .
						 "<font size=4 color=white><b>$name</b></font><p>" .
						 "<font size=2 color=silver><b>$description</b></font><p>" .
						 "</td><td valign=top><img src=$sship_img></td></tr>" .
						 "</table>" .
						 "<table border=0 cellpadding=0>" .
						 "<tr><td valign=top><font size=4 color=white><b>$l_ship_levels</b></font><br>&nbsp;</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minhull / $l_ship_max: $sship_maxhull)&nbsp;&nbsp;</font>" .
						 "$l_hull&nbsp;</b></font>" .
						 "<td valign=bottom>$hull_bars</td></td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minengines / $l_ship_max: $sship_maxengines)&nbsp;&nbsp;</font>" .
						 "$l_engines&nbsp;</b></font>" .
						 "<td valign=bottom>$engines_bars</td></td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minpower / $l_ship_max: $sship_maxpower)&nbsp;&nbsp;</font>" .
						 "$l_power&nbsp;</b></font>" .
						 "<td valign=bottom>$power_bars</td></td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mincomputer / $l_ship_max: $sship_maxcomputer)&nbsp;&nbsp;</font>" .
						 "$l_computer&nbsp;</b></font>" .
						 "<td valign=bottom>$computer_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minsensors / $l_ship_max: $sship_maxsensors)&nbsp;&nbsp;</font>" .
						 "$l_sensors&nbsp;</b></font>" .
						 "<td valign=bottom>$sensors_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minarmour / $l_ship_max: $sship_maxarmour)&nbsp;&nbsp;</font>" .
						 "$l_armour&nbsp;</b></font>" .
						 "<td valign=bottom>$armour_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minshields / $l_ship_max: $sship_maxshields)&nbsp;&nbsp;</font>" .
						 "$l_shields&nbsp;</b></font>" .
						 "<td valign=bottom>$shields_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_minbeams / $l_ship_max: $sship_maxbeams)&nbsp;&nbsp;</font>" .
						 "$l_beams&nbsp;&nbsp;</b></font>" .
						 "<td valign=bottom>$beams_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mintorp_launchers / $l_ship_max: $sship_maxtorp_launchers)&nbsp;&nbsp;</font>" .
						 "$l_torp_launch&nbsp;</b></font>" .
						 "<td valign=bottom>$torp_launchers_bars</td></tr>" .
						 "<tr><td><font size=2><b><font color=white>" .
						 "($l_ship_min: $sship_mincloak / $l_ship_max: $sship_maxcloak)&nbsp;&nbsp;</font>" .
						 "$l_cloak&nbsp;</b></font>" .
						 "<td valign=bottom>$cloak_bars</td></tr>";
				 
					if (($stype != $shipinfo_class) or ($ships2id != $shipsid))
					{   
						echo "<tr><td><font color=white size=4><b><br>$l_ship_storagecost: </b></td>" .
							 "<td><font color=red size=4><b><br>" . $ships2fee . " C</b></td>" .
							 "</tr>";
					}
					echo "</table><p>";

					if (($stype != $shipinfo_class) or ($ships2id != $shipsid))
					{
						echo "<form action=shipyard_purchase.php method=POST>" .
							 "<input type=hidden name=stype value=$stype>" .
							 "<input type=hidden name=switch value=yes>" .
							 "<input type=hidden name=confirm value=yes>" .
							  "<input type=hidden name=shipid1 value=$ships2id>" .
							 "&nbsp;<input type=submit value='$l_ship_outstorage'>" .
							 "</form>";
						if (($stype ==1) and ($shipinfo_class==1) and ($ships2id != $shipsid)){
							echo"<p><b><font color=\"red\">$l_ship_storagewarn</font></b></p>";
						}
					}
				}
				echo "</td></tr>";
			}
			else
			{
				echo "</td></tr>";
			}
		}
{/php}
<tr><td align="center" colspan=9><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
