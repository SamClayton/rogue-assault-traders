<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "2" border = "1" width="100%">
<TR BGCOLOR="{$color_header}"><TD colspan=9 align=center><font color=#ff0000><B>{$l_autotrade_report}</B></font></TD></TR>
<TR BGCOLOR="{$color_line2}">
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_planet}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_hull}<br>{$l_autotrade_capacity}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_energy}<br>{$l_autotrade_capacity}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_goods}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_ore}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_organics}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_energy}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_credits}</font></B></TD>
<TD align='center'><B><font size=2 color='#ff0000'>{$l_autotrade_delete}</font></B></TD>

</TR>
<FORM ACTION=autotrades.php METHOD=POST>
<INPUT TYPE=hidden name=command value=dismiss>

{if $tradecount != 0}
	{php}
		for($i = 0; $i < $tradecount; $i++){
			echo "<TR BGCOLOR=$color[$i]>";
			echo "<TD align='center'><font size=2 color='#ff0000'><b><a href='move.php?move_method=real&engage=1&destination=$tradesector[$i]'>$tradename[$i]</a></b></font></TD>";
			echo "<TD align='center'><font size=2 color=yellow>$tradehull[$i]<br></font><font size=2 color=#ff0000>".$tradeholds[$i]."</font></TD>";
			echo "<TD align='center'><font size=2 color=yellow>$tradepower[$i]<br></font><font size=2 color=#ff0000>".$tradeenergy[$i]."</font></TD>";
			if($tradegoodsprice[$i] == 0){
				echo "<TD align='center'><font size=2 color=#ff0000>$l_autotrade_noroute</font></TD>";
			}else{
				echo "<TD align='center'><font size=2 color=yellow>$tradegoodsprice[$i]</font> <font size=2 color=#ff0000>$l_autotrade_credit2<br>$l_autotrade_sector</font> <font size=2 color=yellow>$tradegoodsport[$i]</font></TD>";
			}
			if($tradeoreprice[$i] == 0){
				echo "<TD align='center'><font size=2 color=#ff0000>$l_autotrade_noroute</font></TD>";
			}else{
				echo "<TD align='center'><font size=2 color=yellow>$tradeoreprice[$i]</font> <font size=2 color=#ff0000>$l_autotrade_credit2<br>$l_autotrade_sector</font> <font size=2 color=yellow>$tradeoreport[$i]</font></TD>";
			}
			if($tradeorganicsprice[$i] == 0){
				echo "<TD align='center'><font size=2 color=#ff0000>$l_autotrade_noroute</font></TD>";
			}else{
				echo "<TD align='center'><font size=2 color=yellow>$tradeorganicsprice[$i]</font> <font size=2 color=#ff0000>$l_autotrade_credit2<br>$l_autotrade_sector</font> <font size=2 color=yellow>$tradeorganicsport[$i]</font></TD>";
			}
			if($tradeenergyprice[$i] == 0){
				echo "<TD align='center'><font size=2 color=#ff0000>$l_autotrade_noroute</font></TD>";
			}else{
				echo "<TD align='center'><font size=2 color=yellow>$tradeenergyprice[$i]</font> <font size=2 color=#ff0000>$l_autotrade_credit2<br>$l_autotrade_sector</font> <font size=2 color=yellow>$tradeenergyport[$i]</font></TD>";
			}
			echo "<TD align='center'><font size=2 color='#79f487'><b>".$tradecredits[$i]."</b></font></TD>";
			echo "<td align='center'><INPUT TYPE=CHECKBOX NAME=dismiss[$i] value=$tradedismiss[$i]></td></TR>";
		}
	{/php}
{/if}

<INPUT TYPE=hidden name=tradecount value={$tradecount}>
<TR BGCOLOR="{$color_line2}">
<TD colspan=9 align=center><INPUT TYPE=submit value="{$l_autotrade_deletebutton}"></td></tr>
</FORM>
<tr><td align="center" colspan=9><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
<table>
