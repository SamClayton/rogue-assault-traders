<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
	<center><font size=2 color=#ff0000><b>{$l_casino_welcome}</font></center><p>
  <table width=100% border=1 cellpadding=5 cellspacing=0>
	<tr bgcolor={$color_line2}><td width=10% align=center>
	<font size=2 color=#ff0000><b>{$l_casino_option}</b></font>
	</td>
	<td width=* align=center>
	<font size=2 color=#ff0000><b>{$l_casino_detail}</b></font>
	</tr>
{php}
		for($i = 1; $i <= $item_count; $i++){
			echo "<tr bgcolor={$color_line1}><td align=center>" .
				 "<a style=\"text-decoration: none\" href=$casino_link_array[$i]><img style=\"border: none\" src=\"templates/{$templatename}images/casino/$image_array[$i]\"><br>" .
				 "<font size=2 color=#ff0000><b>$name_array[$i]</a></b></font>";
				echo "</td><td valign=top><b>$description_array[$i]</b></td></tr>";
				
		}
{/php}

</table>
				</td>
			</tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>
  

</table>
