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

<tr><td colspan=2 align=center valign=top><font size=2 face="courier new" color=#ff0000>{$l_igb_takenaloan}<br>---------------------------------</td></tr>
<tr valign=top><td colspan=2 align=center><font size=2 face="courier new" color=#ff0000>{$l_igb_loancongrats}<br><br></tr>
<tr valign=top>
<td><font size=2 face="courier new" color=#ff0000>{$l_igb_loantransferred} :</td><td nowrap align=right><font size=2 face="courier new" color=#ff0000>{$amount} C<br>.
<tr valign=top>
<td><font size=2 face="courier new" color=#ff0000>{$l_igb_loanfee} :</td><td nowrap align=right><font size=2 face="courier new" color=#ff0000>{$amount2} C<br>
<tr valign=top>
<td><font size=2 face="courier new" color=#ff0000>{$l_igb_amountowned} :</td><td nowrap align=right><font size=2 face="courier new" color=#ff0000>{$amount3} C<br>
<tr valign=top>
<td colspan=2 align=center><font size=2 face="courier new" color=#ff0000>---------------------------------<br><br>{$l_igb_loanreminder}</td>
<tr valign=top>
<td nowrap><font size=2 face="courier new" color=#ff0000><a href=igb.php>{$l_igb_back}</a></td><td nowrap align=right><font size=2 face="courier new" color=#ff0000>&nbsp;<a href="main.php">{$l_igb_logout}</a></td>
</tr>

</table>
</td></tr>
</table>

</center>
