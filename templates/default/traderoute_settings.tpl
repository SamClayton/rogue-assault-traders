<h1>{$title}</h1>

<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
<p><b>{$l_tdr_globalset}</b><p>
<b>{$l_tdr_tdrsportsrc} :</b><p>
<form action="traderoute_savesettings.php" method=post>
<table border=0 bgcolor="#000000"><tr>
<td> - {$l_tdr_colonists} :</td>
<td>
<input type=checkbox name=colonists
{if $trade_colonists == 'Y'}
	 checked
{/if}
>
</tr>
<tr>
<td> - {$l_tdr_fighters} :</td>
<td>
<input type=checkbox name=fighters
{if $trade_fighters == 'Y'}
	 checked
{/if}
>
</tr>
<tr>
<td> - {$l_tdr_torps} :</td>
<td>
<input type=checkbox name=torps
{if $trade_torps == 'Y'}
	 checked
{/if}
>
</tr>
</table>
<p>
<b>{$l_tdr_tdrescooped} :</b><p>
<table border=0><tr>
<td>&nbsp;&nbsp;&nbsp;{$l_tdr_trade}</td>
<td>
<input type=radio name=energy value="Y"
{if $trade_energy == 'Y'}
	 checked
{/if}
>
</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;{$l_tdr_keep}</td>
<td><input type=radio name=energy value="N"
{if $trade_energy == 'N'}
	 checked
{/if}
>
</td>
</tr>
<tr><td>&nbsp;</td></tr><tr><td>
<td><input type=submit value="{$l_tdr_save}"></td>
</tr></table>
</form>

{$l_tdr_returnmenu}

</td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
