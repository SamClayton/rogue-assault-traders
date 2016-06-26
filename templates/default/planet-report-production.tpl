<H1>{$title}: {$l_pr_production}</H1>
{literal}
<script language="javascript" type="text/javascript">
function clean_js()
{
	// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)
	// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.
	var form = document.forms[0];
	var i = form.elements.length;
	while (i > 0)
	{
		if ((form.elements[i-1].type == 'text') && (form.elements[i-1].name != ''))
		{
			var tmpval = form.elements[i-1].value.replace(/\D+/g, "");
			if (tmpval != form.elements[i-1].value)
			{
				form.elements[i-1].value = form.elements[i-1].value.replace(/\D+/g, "");
			}
		}
		if (form.elements[i-1].value == '')
		{
			form.elements[i-1].value ='0';
		}
		i--;
	}
}
</script>
{/literal}
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
<B><A HREF=planet-report.php?PRepType=0>{$l_pr_menulink}</A></B><BR>
<BR>
<B><A HREF=planet-report.php?PRepType=1>{$l_pr_planetstatus}</A></B><BR>

{if $playerteam > 0}
	<BR>
	<B><A HREF=team-planets.php>{$l_pr_teamlink}</A></B><BR>
	<BR>
{/if}

{if $num_planets < 1}
	<BR>{$l_pr_noplanet}
{else}
	<FORM ACTION=planet-report-ce.php METHOD=POST>
	{$l_pr_clicktosort}<BR><BR>
	<TABLE WIDTH="100%" BORDER=0 CELLSPACING=0 CELLPADDING=2>
	<TR BGCOLOR="{$color_header}" VALIGN=BOTTOM>
	<TD ALIGN=LEFT>	<B><A HREF=planet-report.php?PRepType=2&sort=sector_id>{$l_pr_sector}</A></B></TD>
	<TD ALIGN=LEFT>	<B><A HREF=planet-report.php?PRepType=2&sort=name>{$l_name}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=ore>{$l_ore}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=organics>{$l_organics}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=goods>{$l_goods}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=energy>{$l_energy}</A></B></TD>
	<TD ALIGN=RIGHT> <B><A HREF=planet-report.php?PRepType=2&sort=colonists>{$l_colonists}</A></B></TD>
	<TD ALIGN=RIGHT> <B><A HREF=planet-report.php?PRepType=2&sort=credits>{$l_credits}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=fighters>{$l_fighters}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=torp>{$l_torps}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=research>{$l_pr_research}</A></B></TD>
	<TD ALIGN=CENTER><B><A HREF=planet-report.php?PRepType=2&sort=build>{$l_pr_build}</A></B></TD>
	{if $playerteam > 0}
		<TD ALIGN=CENTER><B>{$l_team}?</B></TD>
		<TD ALIGN=CENTER><B>{$l_teamcash}?</B></TD>
	{/if}
	</TR>
	{php}
	$color = $color_line1;

	for($i=0; $i<$num_planets; $i++)
	{
		echo "<TR BGCOLOR=\"$color\">";
		echo "<TD><A HREF=move.php?move_method=real&engage=1&destination=". $planetsector[$i] . "><input type=hidden name=\"planetsector[". $planetid[$i] . "]\" value=\"" . $planetsector[$i] . "\">". $planetsector[$i] ."</A></TD>";
		echo "<TD><input type=hidden name=\"planetname[". $planetid[$i] . "]\" value=\"" . $planetname[$i] . "\">" . $planetname[$i] . "</TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_ore[". $planetid[$i] . "]\" value=\"" . $planetore[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_organics[" . $planetid[$i] . "]\" value=\"" . $planetorganics[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_goods["	. $planetid[$i] . "]\" value=\"" . $planetgoods[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_energy[" . $planetid[$i] . "]\" value=\"" . $planetenergy[$i] . "\"></TD>";
		echo "<TD ALIGN=RIGHT>"	. $planetcolonists[$i] . "</TD>";
		echo "<TD ALIGN=RIGHT>"	. $planetcredits[$i] . "</TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_fighters[" . $planetid[$i] . "]\" value=\"" . $planetfighters[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_torp[" . $planetid[$i] . "]\" value=\"" . $planettorps[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_research[" . $planetid[$i] . "]\" value=\"" . $planetresearch[$i] . "\"></TD>";
		echo "<TD ALIGN=CENTER><input size=3 type=text name=\"prod_build[" . $planetid[$i] . "]\" value=\"" . $planetbuild[$i] . "\"></TD>";
		if ($playerteam > 0){
			if ($planetteam[$i] <= 0){
				$selected1 = "";
				$selected2 = "checked";
			}else{
				$selected1 = "checked";
				$selected2 = "";
			}
				echo "<TD ALIGN=CENTER><INPUT TYPE=radio NAME=team[" . $planetid[$i] . "] VALUE=\"1\" $selected1>Yes/";
				echo "No<INPUT TYPE=radio NAME=team[" . $planetid[$i] . "] VALUE=\"0\" $selected2></TD>";
			if ($planettcash[$i] == 'Y'){
				$selected1 = "checked";
				$selected2 = "";
			}else{
				$selected1 = "";
				$selected2 = "checked";
			}
				echo "<TD ALIGN=CENTER><INPUT TYPE=radio NAME=team_cash[" . $planetid[$i] . "] VALUE=\"1\" $selected1>Yes/";
				echo "No<INPUT TYPE=radio NAME=team_cash[" . $planetid[$i] . "] VALUE=\"0\" $selected2></TD>";
		}
		echo "</TR>";
		echo "<input type=hidden name=\"prod_done[" . $planetid[$i] . "]\" value=\"Y\">";

		if ($color == $color_line1)
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

	<TD COLSPAN=2 ALIGN=CENTER>{$l_pr_totals}</TD>
	<TD></TD>
	<TD></TD>
	<TD></TD>
	<TD></TD>
	<TD ALIGN=RIGHT>{$total_colonists}</TD>
	<TD ALIGN=RIGHT>{$total_credits}</TD>
	<TD></TD>
	<TD></TD>
	{if $playerteam > 0}
		<TD></TD>
	{/if}
	<TD></TD>
	</TR>
	</TABLE>

	<BR>
	<INPUT TYPE=SUBMIT VALUE="{$l_submit}" ONCLICK="clean_js()">
	<INPUT TYPE=RESET VALUE="{$l_reset}">
	</FORM>
{/if}

</td></tr>

<table border=0 cellspacing=0 cellpadding=2 width="100%">
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>
 
</table>
