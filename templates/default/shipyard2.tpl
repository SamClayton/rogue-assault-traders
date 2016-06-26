<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
	<font size=4 color=white><b>{$l_ship2_buying}</b></font><p>
	<table border=0 cellpadding=5>
	<tr><td align=center><font color=white size=4><b>{$shipname}</b><br><img src=templates/{$shipimage}></font></td>
	<td><font size=2><b>{$shipinfo}</b></font>
	</table>
	<p>
	<table border=0>
		<tr>
		<td colspan=2> <font size=4>{$l_player} {$l_credits}:</font> <font size=4 color=#00FF00>{$numbercredits}</font><br><br></td>
	</tr>		<tr>
		<td colspan=2> <font size=4>{$l_ship2_tradein}</font></td>
	</tr>
	<tr><td><font size=4>{$l_ship2_value}&nbsp;&nbsp;&nbsp;</font></td>
	<td align=right><font size=4 color=#00FF00><b>{$shipvalue}</b></font></td></tr>
	<tr><td>
	<font size=4>{$l_ship2_newvalue}&nbsp;&nbsp;&nbsp;</font></td>
	<td align=right><font size=4 color=#FF0000><b>{$newshipvalue}</b></font></td></tr>
	<tr><td><td><hr></td></tr>
	<tr><td>
	<font size=4>{$l_ship2_totalcost}&nbsp;&nbsp;&nbsp;</font></td>
	<td align=right><font size=4 color=#FF0000><b>{$totalcost}</b></font></td></tr></table>
	<p>

	{if $numbertotalcost > $credits}
		<br><font size=3 color=white><b>&nbsp;{$l_ship2_nomoney}</b></font><p><br>
	{else}
		<form action=shipyard_purchase.php method=POST>
		<input type=hidden name=stype value={$stype}>
		<input type=hidden name=confirm value=yes>
		<input type=submit value="{$l_ship2_purchase}">
		</form><p>
	{/if}

	{if $class != 10}
		<table border=0>
		<tr>
			<td colspan=2> <font size=4>{$l_ship2_buynstore}:</font></td>
		</tr>
		<tr><td><font size=4>{$l_ship2_newvalue}&nbsp;&nbsp;&nbsp;</font></td>
		<td align=right><font size=4 color=#FF0000><b>{$newshipvalue}</b></font></td></tr>
		<tr><td><td><hr></td></tr>
		<tr><td>
		<font size=4>{$l_ship2_totalcost}&nbsp;&nbsp;&nbsp;</font></td>
		<td align=right><font size=4 color=#FF0000><b>{$newshipvalue}</b></font></td></tr></table>
		<p>
		
		{if $newshipcheck > $credits}
			<br><font size=3 color=white><b>&nbsp;{$l_ship2_nomoney}</b></font><p><br>
		{else}
			<form action=shipyard_purchase.php method=POST>
			<input type=hidden name=stype value={$stype}>
			<input type=hidden name=keep value=yes>
			<input type=hidden name=confirm value=yes>
			<input type=submit value="{$l_ship2_purchase}">
			</form><p>
		{/if}
	{/if}
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>

</table>
