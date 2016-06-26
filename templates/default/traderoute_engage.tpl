<h1>{$title}</h1>

<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
		<td width=18><img src = "templates/{$templatename}images/g-top-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-top-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-top-midright.gif" width="100%" height="20"></td>
		<td width=18><img src = "templates/{$templatename}images/g-top-right.gif"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/g-mid-left.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
<FORM ACTION=traderoute_delete.php METHOD=POST>
<table border=1 cellspacing=1 cellpadding=2 width="100%" align=center bgcolor="#000000">
<tr bgcolor="{$color_header}"><td align="center" colspan=8><b>{$l_tdr_curtdr}</b></td></tr>
<tr align=center bgcolor="{$color_header}">
<td><b>{$l_tdr_src}</b></td>
<td><b>{$l_tdr_srctype}</b></td>
<td><b>{$l_tdr_dest}</b></td>
<td><b>{$l_tdr_desttype}</b></td>
<td><b>{$l_tdr_move}</b></td>
<td><b>{$l_tdr_circuit}</b></td>
<td><b>{$l_tdr_change}</b></td>
<td><b>{$l_tdr_del}</b></td>
</tr>

{php}
	$curcolor = $color_line1;
	for ($i=0; $i < $num_traderoutes; $i++)
	{
		echo "<tr bgcolor=$curcolor>";
		if ($curcolor == $color_line1)
		{
			$curcolor = $color_line2;
		}else{
			$curcolor = $color_line1;
		}

		if ($tradesource_type[$i] == 'P')
		{
			echo "<td>&nbsp;$l_tdr_portin <a href=move.php?move_method=real&engage=1&destination=" . $tradesource_id[$i] . ">" . $tradesource_id[$i] . "</a></td>";
			echo "<td align=center>&nbsp;" . $tradesource_port[$i] . "</td>";
		}else{
			if ($tradesource_planet)
			{
				echo "<td>&nbsp;$l_tdr_planet <b>$tradesource_planetname[$i]</b>$l_tdr_within<a href=\"move.php?move_method=real&engage=1&destination=$tradesource_planet[$i]\">$tradesource_planet[$i]</a></td>";
				echo "<td align=center>&nbsp;$l_tdr_cargo</td>";
			}else{
				echo "<td>&nbsp;$l_tdr_nonexistance</td>";
				echo "<td align=center>&nbsp;$l_tdr_na</td>";
			}
		}

		if ($tradedest_type[$i] == 'P')
		{
			echo "<td>&nbsp;$l_tdr_portin <a href=\"move.php?move_method=real&engage=1&destination=" . $tradedest_id[$i] . "\">". $tradedest_id[$i] . "</a></td>";
			echo "<td align=center>&nbsp;" . $tradedest_port[$i] . "</td>";
		}else{
			if ($tradedest_planet)
			{
				echo "<td>&nbsp;$l_tdr_planet <b>$tradedest_planetname[$i]</b>$l_tdr_within<a href=\"move.php?move_method=real&engage=1&destination=$tradedest_planet[$i]\">$tradedest_planet[$i]</a></td>";
				echo "<td align=center>&nbsp;";
				if ($tradedest_planetcolonist[$i] == 'N' && $tradedest_planetfighters[$i] == 'N' && $tradedest_planettorps[$i] == 'N')
				{
					echo $l_tdr_none;
				}else{
					if ($tradedest_planetcolonist[$i] == 'Y')
					{
						echo $l_tdr_colonists;
					}
					if ($tradedest_planetfighters[$i] == 'Y')
					{
						if ($tradedest_planetcolonist[$i] == 'Y')
						{
							echo ", ";
						}
						echo $l_tdr_fighters;
					}
					if ($tradedest_planettorps[$i] == 'Y')
					{
						echo "<br>$l_tdr_torps";
					}
				}
				echo "</td>";
			}else{
				echo "<td>&nbsp;$l_tdr_nonexistance</td>";
				echo "<td align=center>&nbsp;$l_tdr_na</td>";
			}
		}

		if ($trademove_type[$i] == 'R')
		{
			echo "<td align=center>&nbsp;RS, ";
			echo $tradedest_move[$i];
			echo "</td>";
		}else{
			echo "<td align=center>&nbsp;$l_tdr_warp";
			if ($tradecircuit[$i] == '1')
			{
				echo ", $tradedest_move[$i] $l_tdr_turns";
			}else{
				echo ", $tradedest_move[$i] $l_tdr_turns";
			}
			echo "</td>";
		}

		if ($tradecircuit[$i] == '1')
		{
			echo "<td align=center>&nbsp;1 $l_tdr_way</td>";
		}else{
			echo "<td align=center>&nbsp;2 $l_tdr_ways</td>";
		}
		echo "<td align=center>";
		echo "<a href=\"traderoute_edit.php?traderoute_id=" . $traderoute_id[$i] . "\">";
		echo "$l_tdr_edit</a><br></td>";
		echo "<TD ALIGN=CENTER><font size=2>&nbsp;<INPUT TYPE=CHECKBOX NAME=TRDel[] VALUE=\"" . $traderoute_id[$i] . "\">" . "&nbsp;</font></TD>";
		echo "</tr>";
	}
{/php}
<tr bgcolor="{$color_line2}">
	<td colspan="7">&nbsp;</td><td align="center"><INPUT TYPE=SUBMIT VALUE="{$l_tdr_del}"></td>
</tr>
</table></FORM>
</td></tr>
<tr><td><br>{$l_tdr_newtdr}<br>
<br>{$l_tdr_modtdrset}
</td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>
  <tr>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-bottom-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-bottom-midright.gif" width="100%" height="12"></td>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-right.gif"></td>
  </tr>
</table>
