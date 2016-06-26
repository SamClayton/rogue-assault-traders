<H1>{$title}</H1>
{if $command=="showpersonal"}
<table width="30%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
					<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 align=center>
	<tr><td align=center><font color=#00ff00 size=3><b>{$l_sn_pntitle}</b></font></td></tr><tr><td>
	<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3><tr> 
	{php}
	for($i = 0; $i < $count; $i++)
	{
		echo "<td align=center><a href=\"command_sectornotes.php?sectorid=$sectorlist[$i]\">$sectorlist[$i]</a></td>";
		if($i % 20 == 20)
		{
			echo "</tr><TR>\n";
		}
	}
	{/php}
	</tr></table></td></tr></table>
				</td>
			</tr>
		</table>
	</td>
    
  </tr>
 
</table>
{/if}

{if $command=="showteam"}
<table width="30%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
					<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3 align=center>
	<tr><td align=center><font color=#00ff00 size=3><b>{$l_sn_tntitle}</b></font></td></tr><tr><td>
	<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3><tr>
	{php}
	for($i = 0; $i < $count; $i++)
	{
		echo "<td align=center><a href=\"command_sectornotes.php?sectorid=$sectorlist[$i]\">$sectorlist[$i]</a></td>";
		if($i % 20 == 20)
		{
			echo "</tr><TR>\n";
		}
	}
	{/php}
	</tr></table></td></tr></table>
				</td>
			</tr>
		</table>
	</td>
    
  </tr>
  
</table>

{/if}

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td align=center><font color=#00ff00 size=3><b>{$l_sn_psntitle}</b></font></td></tr><tr><td>

<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3>

{if $editid != 0}
	<FORM ACTION=command_sectornotes.php METHOD=POST>
	<TR nowrap><TD>{$l_sn_editnote}</TD>
	<TD><TEXTAREA NAME=note_data ROWS=3 COLS=60>{$editnoteid}</TEXTAREA></TD>
	<INPUT TYPE=HIDDEN NAME=note_id VALUE={$editid}>
	<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE="{$l_sn_savepersonal}"></TD></TR>
	</form>
{/if}

{php}
for ($i = 0; $i < $notelistcount; $i++)
{
	if($sectorid == $shipsectorid){
		echo "<FORM ACTION=command_sectornotes.php METHOD=POST>";
		echo "<TR nowrap><TD>$l_sn_deleteedit</TD>";
		echo "<TD width=600><font color=cyan><b>$notelistdate[$i]</b></font><br><b>$notelistnote[$i]</b></TD>";
		echo "<INPUT TYPE=HIDDEN NAME=note_id VALUE=$notelistid[$i]>";
		echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"$l_sn_deletepersonal\">&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE=\"$l_sn_editpersonal\"></TD></TR>";
		echo "</FORM>";
	}
	else
	{
		echo "<tr><TD width=600><font color=cyan><b>$notelistdate[$i]</b></font><br><b>$notelistnote[$i]</b></TD></tr>";
	}
}
{/php}

{if $sectorid == $shipsectorid}
	<FORM ACTION=command_sectornotes.php METHOD=POST>
	<TR nowrap><TD>{$l_sn_addnote}</TD>
	<TD><TEXTAREA NAME=note_data ROWS=3 COLS=60></TEXTAREA></TD>
	<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE="{$l_sn_addpersonal}"></TD></TR>
	</form>
{/if}

</TABLE>
</td></tr>
<tr><td align="center"><font color="#00ff00" size="2"><b><a href="command_sectornotes.php?command=showpersonal">{$l_sn_listps}</a></b></font></td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
  
</table>

{if $playerteam != 0}

	<br><br>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td align=center><font color=#00ff00 size=3><b>{$l_sn_tsntitle}</b></font></td></tr><tr><td>
	<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3>

	{if $teameditid != 0}
		<FORM ACTION=command_sectornotes.php METHOD=POST>
		<TR nowrap><TD>{$l_sn_editnote}</TD>
		<TD><TEXTAREA NAME=note_data ROWS=3 COLS=60>{$teameditnoteid}</TEXTAREA></TD>
		<INPUT TYPE=HIDDEN NAME=note_id VALUE={$teameditid}>
		<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE="{$l_sn_saveteam}"></TD></TR>
		</form>
	{/if}

	{php}
	for ($i = 0; $i < $teamnotelistcount; $i++)
	{
		if($sectorid == $shipsectorid){
			echo "<FORM ACTION=command_sectornotes.php METHOD=POST>";
			echo "<TR nowrap><TD>$l_sn_deleteedit</TD>";
			echo "<TD width=600><font color=cyan><b>$teamnotelistdate[$i]</b></font><br><b>$teamnotelistnote[$i]</b></TD>";
			echo "<INPUT TYPE=HIDDEN NAME=note_id VALUE=$teamnotelistid[$i]>";
			echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"$l_sn_deleteteam\">&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE=\"$l_sn_editteam\"></TD></TR>";
			echo "</FORM>";
		}
		else
		{
			echo "<tr><TD width=600><font color=cyan><b>$teamnotelistdate[$i]</b></font><br><b>$teamnotelistnote[$i]</b></TD></tr>";
		}
	}
	{/php}

	{if $sectorid == $shipsectorid}
		<FORM ACTION=command_sectornotes.php METHOD=POST>
		<TR nowrap><TD>{$l_sn_addnote}</TD>
		<TD><TEXTAREA NAME=note_data ROWS=3 COLS=60></TEXTAREA></TD>
		<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE="{$l_sn_addteam}"></TD></TR>
		</form>
	{/if}

	</TABLE>
	</td></tr>
	<tr><td align="center"><font color="#00ff00" size="2"><b><a href="command_sectornotes.php?command=showteam">{$l_sn_listts}</a></b></font></td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>
 
</table>

{/if}

