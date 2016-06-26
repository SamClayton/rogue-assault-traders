defnesectest<H1>{$title}</H1>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
<BR>
<B><A HREF=planet-report.php>{$l_pr_menulink}</A></B>
<BR>
<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR BGCOLOR="{$color_header}" VALIGN=BOTTOM>
<TD><B><A HREF=team-defenses.php?sort=sector_id>{$l_sector}</A></B></TD>
<TD><B><A HREF=team-defenses.php?sort=name>{$l_name}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=computer>{$l_computer}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=sensors>{$l_sensors}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=beams>{$l_beams}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=torp_launchers>{$l_torp_launch}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=shields>{$l_shields}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=jammer>{$l_jammer}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=cloak>{$l_cloak}</A></B></TD>
<TD width="10%"><B><A HREF=team-defenses.php?sort=base>{$l_base}</a></B></TD>
<TD><B><A HREF=team-defenses.php?sort=owner>{$l_teamplanet_owner}</A></B></TD>
</TR>
{php}
$color = $color_line1;
for($i=0; $i<$num_planets; $i++)
{
	echo "<TR BGCOLOR=\"$color\">";
	echo "<TD><A HREF=move.php?move_method=real&engage=1&destination=". $teamsector[$i] . ">". $teamsector[$i] ."</A></TD>";
	echo "<TD>" . $planetname[$i] . "</TD>";
	echo "<TD>" . $planetcomputer[$i] . "</TD>";
	echo "<TD>" . $planetsensors[$i] . "</TD>";
	echo "<TD>" . $planetbeams[$i] . "</TD>";
	echo "<TD>" . $planettorps[$i] . "</TD>";
	echo "<TD>" . $planetshields[$i] . "</TD>";
	echo "<TD>" . $planetjammer[$i] . "</TD>";
	echo "<TD>" . $planetcloak[$i] . "</TD>";
	echo "<TD>" . $planetbase[$i] . "</TD>";
	echo "<TD>" . $playername[$i] . "</TD>";
	echo "</TR>";

	if($color == $color_line1)
	{
		$color = $color_line2;
	}
	else
	{
		$color = $color_line1;
	}
}

if($color == $color_line1)
{
	$color = $color_line2;
}
else
{
	$color = $color_line1;
}

echo "<TR BGCOLOR=$color>";
{/php}

<TD COLSPAN=11 ALIGN=CENTER>{$l_pr_totals}: {$total_base}</TD>
</TR>
</table>
</td>
</tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
