<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
	{if $isprobe}
		{if $total_cost > $playercredits}
			{$l_probe2_nocredits1} {$total_cost_n} {$l_probe2_nocredits2} {$playercredits_n} {$l_credits}.
		{else}
			<TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
			 <TR>
				<TD colspan=99 align=center bgcolor=#300030><font size=3 color=white><b>{$l_trade_result}</b></font></TD>
			 </TR>
			 <TR>
				<TD colspan=99 align=center><b><font color=red>{$l_cost}: {$trade_credits} {$l_credits}</font></b></TD>
			 </TR>

			{if $isengineupgrade}
				<TR><TD colspan=99 align=left>{$l_engines} {$l_trade_upgraded} {$engines_upgrade}.</TD></TR>
			{/if}

			{if $issensorupgrade}
				<TR><TD colspan=99 align=left>{$l_sensors} {$l_trade_upgraded} {$sensors_upgrade}.</TD></TR>
			{/if}

			{if $iscloakupgrade}
				<TR><TD colspan=99 align=left>{$l_cloak} {$l_trade_upgraded} {$cloak_upgrade}.</TD></TR>
			{/if}

			</table>
			<BR><BR>
			<A HREF=showprobe.php?probe_id={$probe_id}>{$l_clickme}</A> {$l_toprobemenu}<BR><BR>
		{/if}
	{else}
		<A HREF=showprobe.php?probe_id={$probe_id}>{$l_clickme}</A> {$l_toprobemenu}<BR><BR>
	{/if}
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
