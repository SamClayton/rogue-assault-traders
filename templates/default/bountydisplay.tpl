<H1>{$title}</H1>

<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
{php}
			echo "$l_by_bountyon " . $playername;
			echo '<table border=1 cellspacing=0 cellpadding=2 align=center width="100%">';
			echo "<TR BGCOLOR=\"$color_header\">";
			echo "<TD><B>$l_amount</TD>";
			echo "<TD><B>$l_by_placedby</TD>";
			echo "<TD><B>$l_by_action</TD>";
			echo "</TR>";
			$color = $color_line1;
			for ($j=0; $j<$num_details; $j++)
			{
				echo "<TR BGCOLOR=\"$color\">";
				echo "<TD>&nbsp;" . $bountyamount[$j] . "&nbsp;</TD>";
				if ($bountyby[$j] == 0)
				{
					echo "<TD>$l_by_thefeds</TD>";
					if ($fed_bounty_count <= $bountydetails[$j])
					{
						echo "<TD>$l_by_fedcollectonly</TD>";
					}
					else
					{
						echo "<TD>$l_none</TD>";
					}
				}
				else
				{
					echo "<TD>" . $bountydetails[$j] . "</TD>";

					if ($bountyby[$j] == $playerid)
					{
						echo "<TD><A HREF=bounty.php?bid=" . $bountyid[$j] . "&response=cancel>$l_by_cancel</A></TD>";
					}
					else
					{
						echo "<TD>$l_none</TD>";
					}
				}
				echo "</TR>";

				if ($color == $color_line1)
				{
					$color = $color_line2;
				}
				else
				{
					$color = $color_line1;
				}
			}
			echo "</TABLE>";
{/php}
				</td>
			</tr>
<tr><td><br><br>{$gotobounty}<br></td></tr>
<tr><td><br>{$gotomain}<br></td></tr>
		</table>
	</td>
   
  </tr>

</table>
