<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
 
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
<BR>
<B><A HREF=planet-report.php>{$l_pr_menulink}</A></B>
<BR>
<BR>
<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=2>
<TR BGCOLOR="{$color_header}">
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=sector>{$l_sector}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=name>{$l_name}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=ore>{$l_ore}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=organics>{$l_organics}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=goods>{$l_goods}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=energy>{$l_energy}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=colonists>{$l_colonists}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=credits>{$l_credits}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=max_credits>{$l_max}<br>{$l_credits}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=fighters>{$l_fighters}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=team-planets.php?sort=torp>{$l_torps}</A>&nbsp;</B></font></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;{$l_base}?&nbsp;</B></TD>
<TD ALIGN=CENTER><font size=2><B>&nbsp;{$l_player}&nbsp;</B></TD>
</TR>

{php}
$color = $color_line1;
for($i = 0; $i < $num_planets; $i++){
	echo "<TR BGCOLOR=\"$color\">";
	echo "<TD ALIGN=CENTER><font size=2>&nbsp;<A HREF=move.php?move_method=real&engage=1&destination=". $planetsector[$i] . ">". $planetsector[$i] ."</A>&nbsp;</font></TD>";
	echo "<TD ALIGN=CENTER><font size=2>&nbsp;" . $planetname[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetore[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetorganics[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetgoods[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetenergy[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetcolonists[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetcredits[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetmaxcredits[$i] . "%&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetfighters[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planettorps[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=CENTER><font size=2>&nbsp;" . $planetbase[$i] . "&nbsp;</font></TD>";
	echo "<TD ALIGN=CENTER><font size=2>&nbsp;" . $planetplayer[$i] . "&nbsp;</font></TD>";
	echo "</TR>";

	if ($color == $color_line1)
	{
		$color = $color_line2;
	}
	else
	{
		$color = $color_line1;
	}
}
echo "<TR BGCOLOR=\"$color\">";
{/php}

<TD ALIGN=CENTER>&nbsp;</TD>
<TD ALIGN=CENTER>&nbsp;{$l_pr_totals}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_ore}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_organics}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_goods}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_energy}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_colonists}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_credits}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_fighters}&nbsp;</TD>
<TD ALIGN=RIGHT>&nbsp;{$total_torp}&nbsp;</TD>
<TD ALIGN=CENTER>&nbsp;{$total_base}&nbsp;</TD>
<TD ALIGN=CENTER>&nbsp;</TD>
</TR>
</TABLE>

</td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
