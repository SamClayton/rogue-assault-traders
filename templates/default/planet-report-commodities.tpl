<H1>{$title}: {$l_pr_status}</H1>

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
	<FORM ACTION=planet-report-takecr.php METHOD=POST>
	{$l_pr_clicktosort}<BR><BR>
	{$l_pr_warning}<BR><BR>
	<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=2>
	<TR BGCOLOR="{$color_header}" VALIGN=BOTTOM>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=sector_id>{$l_pr_sector}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=name>{$l_name}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=ore>{$l_ore}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=organics>{$l_organics}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=goods>{$l_goods}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=energy>{$l_energy}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=colonists>{$l_colonists}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=credits>{$l_credits}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=max_credits>{$l_max}<br>{$l_credits}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;{$l_pr_takecreds}?&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=fighters>{$l_fighters}</A>&nbsp;</B></font></TD>
	<TD ALIGN=CENTER><font size=2><B>&nbsp;<A HREF=planet-report.php?PRepType=1&sort=torp>{$l_torps}</A>&nbsp;</B></font></TD>
	<TD ALIGN=RIGHT><font size=2><B>&nbsp;{$l_base}?&nbsp;</B></font></TD>
	{if $playerteam > 0}
		<TD ALIGN=RIGHT><font size=2><B>&nbsp;{$l_team}?&nbsp;</B></font></TD>
		<TD ALIGN=CENTER><B>&nbsp;{$l_teamcash}?&nbsp;</B></TD>
        
	{/if}
	</TR>
	{php}
	$color = $color_line1;
	for($i=0; $i<$num_planets; $i++)
	{
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
		echo "<TD ALIGN=CENTER><font size=2>&nbsp;<INPUT TYPE=CHECKBOX NAME=TPCreds[] VALUE=\"" . $planetid[$i] . "\">" . "&nbsp;</font></TD>";
		echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planetfighters[$i] . "&nbsp;</font></TD>";
		echo "<TD ALIGN=RIGHT><font size=2>&nbsp;" . $planettorps[$i] . "&nbsp;</font></TD>";

		if ($planetbase[$i] == 'Y')
			echo "<TD ALIGN=CENTER><font size=2>&nbsp;$l_yes&nbsp;</font></TD>";
		elseif ($planetbaseitems[$i])
			echo "<TD ALIGN=CENTER><font size=2>&nbsp;<A HREF=planet-report-buildbase.php?buildp=" . $planetid[$i] . "&builds=" . $planetsector[$i] . ">$l_pr_build</A>&nbsp;</font></TD>";
		else
			echo "<TD ALIGN=CENTER><font size=2>&nbsp;$l_no&nbsp;</font></TD>";

		if ($playerteam > 0){
			echo "<TD ALIGN=CENTER><font size=2>&nbsp;" . ($planetteam[$i] > 0 ? "$l_yes" : "$l_no") . "&nbsp;</font></TD>";
			echo "<TD ALIGN=CENTER><font size=2>&nbsp;" . ($planettcash[$i] == 'Y' ? "$l_yes" : "$l_no") . "&nbsp;</font></TD>";
		}
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

	echo "<TR><TD></TD></TR><TR><TD></TD></TR><TR><TD></TD></TR><TR BGCOLOR=$color>";
	{/php}

	<TD COLSPAN=2 ALIGN=CENTER><font size=2>&nbsp;{$l_pr_totals}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_ore}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_organics}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_goods}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_energy}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_colonists}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_credits}&nbsp;</font></TD>
	<TD ALIGN=RIGHT>&nbsp;</TD>
	<TD ALIGN=RIGHT>&nbsp;</TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_fighters}&nbsp;</font></TD>
	<TD ALIGN=RIGHT><font size=2>&nbsp;{$total_torp}&nbsp;</font></TD>
	<TD ALIGN=CENTER><font size=2>&nbsp;{$total_base}&nbsp;</font></TD>
	{if $playerteam > 0}
		<TD ALIGN=CENTER><font size=2>&nbsp;{$total_team}&nbsp;</font></TD>
		<TD ALIGN=CENTER><font size=2>&nbsp;{$total_teamcash}&nbsp;</font></TD>
	{/if}
	</TR>
	</TABLE>

	<script language="javascript" type="text/javascript">
{literal}
function checkAll(elm,name) 
{
for (var i = 0; i < elm.form.elements.length; i++)
if (elm.form.elements[i].name.indexOf(name) == 0)
elm.form.elements[i].checked = elm.checked;
}
</script>
{/literal}
	<BR> <INPUT TYPE=CHECKBOX onClick="checkAll(this,'TPCreds')"> {$l_pr_selectall} <BR> 
	<INPUT TYPE=SUBMIT VALUE="{$l_pr_collectcreds}">	<INPUT TYPE=RESET VALUE="{$l_reset}">
	</FORM>
{/if}

</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
