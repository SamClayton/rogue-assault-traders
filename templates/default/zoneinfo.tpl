<h1>{$title}</h1>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
{if $zoneowner != -1}

<table border=0 cellspacing=0 cellpadding=2 width="100%" align=center><tr><td>
	<center>{$l_zi_control}. <a href="zoneedit.php?zone={$zoneowner}">{$l_clickme}</a> {$l_zi_tochange}</center>
<br><br>
</td></tr>
</table>
{/if}

<tr bgcolor="{$color_line2}"><td align=center colspan=2><b><font color=#ff0000>{$zone_name}</font></b></td></tr>
<tr><td colspan=2>
<table border=0 cellspacing=0 cellpadding=2 width="100%" align=center>
<tr bgcolor="{$color_line1}"><td width="50%"><font color=#ff0000 size=3>&nbsp;{$l_zi_owner}</font></td><td width="50%"><font color=#ff0000 size=3>{$ownername}&nbsp;</font></td></tr>
<tr bgcolor="{$color_line2}"><td><font color=#ff0000 size=3>&nbsp;{$l_beacons}</font></td><td><font color=#ff0000 size=3>{$beacon}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_att_att}</font></td><td><font color=#ff0000 size=3>{$attack}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_md_title}</font></td><td><font color=#ff0000 size=3>{$defense}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_warpedit}</font></td><td><font color=#ff0000 size=3>{$warpedit}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_planets}</font></td><td><font color=#ff0000 size=3>{$planet}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_title_port}</font></td><td><font color=#ff0000 size=3>{$trade}&nbsp;</font></td></tr>
<tr bgcolor="#000000"><td><font color=#ff0000 size=3>&nbsp;{$l_zi_maxhull}</font></td><td><font color=#ff0000 size=3>{$hull}&nbsp;</font></td></tr>
			
</table>
				</td>
			</tr>

<tr><td align="center"><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
