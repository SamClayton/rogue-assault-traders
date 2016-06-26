<h1>{$title}</h1>

<table width="40%" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
<TD align="center"><B><A HREF=defence-report.php?sort=sector>{$l_sector}</A></B></TD>
<TD align="center"><B><A HREF=defence-report.php?sort=quantity>{$l_qty}</A></B></TD>
<TD align="center"><B><A HREF=defence-report.php?sort=type>{$l_sdf_type}</A></B></TD>
</TR>
{php}
	for($i=0; $i<$num_sectors; $i++)
	{
		echo "<TR BGCOLOR=\"$dcolor[$i]\">";
		echo "<TD align=\"center\"><A HREF=move.php?move_method=real&engage=1&destination=". $dsector[$i] . ">". $dsector[$i] ."</A></TD>";
		echo "<TD align=\"center\">" . $dquantity[$i] . "</TD>";
		echo "<TD align=\"center\"> $defence_type[$i] </TD>";
		echo "</TR>";
	}
{/php}
										<tr><td width="100%" colspan=3><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>

