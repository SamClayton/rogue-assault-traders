<H1>{$title}: {$l_pr_pdefense}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
<B><A HREF=planet-report.php?PRepType=0>{$l_pr_menulink}</A></B><BR>
<BR>
<B><A HREF=planet-report.php?PRepType=2>{$l_pr_changeprods}</A></B> &nbsp;&nbsp; {$l_pr_baserequired}<BR>

{if $playerteam > 0}
	<BR>
	<B><A HREF=team-planets.php>{$l_pr_teamlink}</A></B><BR>
	<BR>
{/if}

{if $num_planets < 1}
	<BR>{$l_pr_noplanet}
{else}
	<BR>
	{$l_pr_clicktosort}<BR><BR>
	<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=2>
	<TR BGCOLOR="{$color_header}" VALIGN=BOTTOM>
	<TD><B><A HREF=planet-report.php?PRepType=3&sort=sector_id>{$l_pr_sector}</A></B></TD>
	<TD><B><A HREF=planet-report.php?PRepType=3&sort=name>{$l_name}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=computer>{$l_computer}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=sensors>{$l_sensors}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=beams>{$l_beams}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=torp_launchers>{$l_torp_launch}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=shields>{$l_shields}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=jammer>{$l_jammer}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=cloak>{$l_cloak}</A></B></TD>
	<TD width="10%"><B><A HREF=planet-report.php?PRepType=3&sort=base>{$l_base}</a></B></TD>
	</TR>
	{php}
	$color = $color_line1;
	for($i=0; $i<$num_planets; $i++)
	{
		echo "<TR BGCOLOR=\"$color\">";
		echo "<TD><A HREF=move.php?move_method=real&engage=1&destination=". $planetsector[$i] . ">". $planetsector[$i] ."</A></TD>";
		echo "<TD>" . $planetname[$i] . "</TD>";
		echo "<TD>" . $planetcomputer[$i] . "</TD>";
		echo "<TD>" . $planetsensors[$i] . "</TD>";
		echo "<TD>" . $planetbeams[$i] . "</TD>";
		echo "<TD>" . $planettorps[$i] . "</TD>";
		echo "<TD>" . $planetshields[$i] . "</TD>";
		echo "<TD>" . $planetjammer[$i] . "</TD>";
		echo "<TD>" . $planetcloak[$i] . "</TD>";

		if ($planetbase[$i] == 'Y')
			echo "<TD>$l_yes</TD>";
		elseif ($planetbaseitems[$i])
			echo "<TD><A HREF=planet-report-ce.php?buildp=" . $planetid[$i] . "&builds=" . $planetsector[$i] . ">$l_pr_build</A></TD>";
		else
			echo "<TD>$l_no</TD>";

		echo "</TR>\n";

		if($color == $color_line1)
		{
			$color = $color_line2;
		}
		else
		{
			$color = $color_line1;
		}
	}
	echo "<TR BGCOLOR=$color>";
	{/php}
	<TD COLSPAN=11 ALIGN=CENTER>{$l_pr_totals}: {$total_base}</TD>
	</TR>
	</TABLE>
{/if}
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
