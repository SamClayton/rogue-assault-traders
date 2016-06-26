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

<tr><td colspan=2 align=center valign=top><font size=2 face="courier new" color=#ff0000>{$l_igb_transfersuccessful}<br>---------------------------------</td></tr>
<tr valign=top>
<td><font size=2 face="courier new" color=#ff0000>{$l_igb_currentpl}:<br><br>
{$l_igb_turncost} :</td>
<td align=right><font size=2 face="courier new" color=#ff0000>{$transfer} C<br><br>{$tcost}</td>
<tr valign=bottom>
<td><font size=2 face="courier new" color=#ff0000><a href=igb.php>{$l_igb_back}</a></td><td align=right><font size=2 face="courier new" color=#ff0000>&nbsp;<br><a href="main.php">{$l_igb_logout}</a></td>
</tr>

</table>
</td></tr>
</table>

</center>
