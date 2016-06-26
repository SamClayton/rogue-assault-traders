<h1>{$title}</h1>

<table width="40%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>{if $sort!="sector"}
<TD align="center"><B><A HREF=team-defence-report.php?sort=character>{$l_team_members}</A></B></TD>			
<TD align="center"><B><A HREF=team-defence-report.php?sort=sector>{$l_sector}</A></B></TD>
<TD align="center"><B><A HREF=team-defence-report.php?sort=quantity>{$l_qty}</A></B></TD>
<TD align="center"><B><A HREF=team-defence-report.php?sort=type>{$l_sdf_type}</A></B></TD>
	{else}
<TD align="center"><B><A HREF=team-defence-report.php?sort=sector>{$l_sector}</A></B></TD>	
	<TD align="center"><B><A HREF=team-defence-report.php?sort=character>{$l_team_members}</A></B></TD>			
<TD align="center"><B><A HREF=team-defence-report.php?sort=quantity>{$l_qty}</A></B></TD>
<TD align="center"><B><A HREF=team-defence-report.php?sort=type>{$l_sdf_type}</A></B></TD>
	{/if}
</TR>
{php}

	$sectorchk=-1;
	$mine_count=0;
	$fighter_count=0;
	for($i=0; $i<$num_sectors; $i++)
	{
	if ($sort !="sector"){
		echo "<TR BGCOLOR=\"$dcolor[$i]\">";
		echo "<TD align=\"center\">" . $playername[$i] . "</TD>";
		echo "<TD align=\"center\"><A HREF=move.php?move_method=real&engage=1&destination=". $dsector[$i] . ">". $dsector[$i] ."</A></TD>";
		echo "<TD align=\"center\">" . $dquantity[$i] . "</TD>";
		echo "<TD align=\"center\"> $defence_type[$i] </TD>";
		echo "</TR>";
		}else{
		
		if (($sectorchk != $dsector[$i]) and ($i >0)){
			$sectorchk=$dsector[$i];
			echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"4\"><hr></TD>";
			echo "</TR>";
			echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"2\">Fighters:". Number($fighter_count)."</TD>";
			echo "<TD align=\"center\" colspan=\"2\">Mines: ". Number($mine_count)."</TD>";
			echo "</TR>";
					echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"4\">&nbsp;</TD>";
			echo "</TR>";
			$mine_count=0;
			$fighter_count=0;
			}
		echo "<TR BGCOLOR=\"$dcolor[$i]\">";
		echo "<TD align=\"center\"><A HREF=move.php?move_method=real&engage=1&destination=". $dsector[$i] . ">". $dsector[$i] ."</A></TD>";
		echo "<TD align=\"center\">" . $playername[$i] . "</TD>";
		
		echo "<TD align=\"center\">" . $dquantity[$i] . "</TD>";
		echo "<TD align=\"center\"> $defence_type[$i] </TD>";
		echo "</TR>";
		if ($defence_type[$i]=="Mines")
		$mine_count+=$dquantityraw[$i];
		if ($defence_type[$i]=="Fighters")
		$fighter_count+=$dquantityraw[$i];
		
		
		}
	}
	if ($sort =="sector"){
	echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"4\"><hr></TD>";
			echo "</TR>";
			echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"2\">Fighters:". Number($fighter_count)."</TD>";
			echo "<TD align=\"center\" colspan=\"2\">Mines: ". Number($mine_count)."</TD>";
			echo "</TR>";
			echo "<TR BGCOLOR=\"$dcolor[$i]\">";
			echo "<TD align=\"center\" colspan=\"4\">&nbsp;</TD>";
			echo "</TR>";
			$mine_count=0;
			$fighter_count=0;
	}
{/php}
										<tr><td width="100%" colspan=3><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>

  </tr>

</table>

