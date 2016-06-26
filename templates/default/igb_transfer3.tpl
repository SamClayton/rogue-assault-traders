<H1>{$title}</H1>
{literal}
<STYLE TYPE="text/css">
<!--
	input.term {background-color: #000000; color: #ff0000; font-family:Courier New; font-size:10pt; border-color:#000000;}
	select.term {background-color: #000000; color: #ff0000; font-family:Courier New; font-size:10pt; border-color:#000000;}

-->
</STYLE>
{/literal}
<center>
<table width=604 height=354 border=0>
<tr><td align=center background=templates/{$templatename}images/igbscreen.png>
<table background="" width=520 height=300 border=0>

  {if $isplayer}
	<tr><td colspan=2 align=center valign=top><font size=2 face="courier new" color=#ff0000>{$l_igb_transfersuccessful}<br>---------------------------------</td></tr>
	<tr valign=top><td colspan=2 align=center><font size=2 face="courier new" color=#ff0000>{$transfer} {$l_igb_creditsto} {$targetname}.</tr>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_transferamount} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$amount} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_transferfee} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$amount2} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_amounttransferred} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$transfer} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_igbaccount} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$accountbalance} C<br>
	<tr valign=bottom>
	<td><font size=2 face="courier new" color=#ff0000><a href=igb.php?command=login>{$l_igb_back}</a></td><td align=right><font size=2 face="courier new" color=#ff0000>&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
	</tr>
  {else}
	<tr><td colspan=2 align=center valign=top><font size=2 face="courier new" color=#ff0000>{$l_igb_transfersuccessful}<br>---------------------------------</td></tr>
	<tr valign=top><td colspan=2 align=center><font size=2 face="courier new" color=#ff0000>{$transfer} {$l_igb_ctransferredfrom} {$sourcename} {$l_igb_to} {$destname}.</tr>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_transferamount} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$amount} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_transferfee} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$amount2} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_amounttransferred} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$transfer} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_srcplanet} {$sourcename} {$l_igb_in} {$sourcesector} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$sourcecredits} C<br>
	<tr valign=top>
	<td><font size=2 face="courier new" color=#ff0000>{$l_igb_destplanet} {$destname} {$l_igb_in} {$destsector} :</td><td align=right><font size=2 face="courier new" color=#ff0000>{$destcredits} C<br>
	<tr valign=bottom>
	<td><font size=2 face="courier new" color=#ff0000><a href=igb.php>{$l_igb_back}</a></td><td align=right><font size=2 face="courier new" color=#ff0000>&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
	</tr>
  {/if}

</table>
</td></tr>
</table>

</center>
