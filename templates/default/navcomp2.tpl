<h1>{$title}</h1>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
{if !$allow_navcomp}
$l_nav_nocomp<br><br>
{else}
	{if $autocount != 0}
		<table border=1 cellspacing=1 cellpadding=2 width="100%">
		<TR BGCOLOR="{$color_header}"><TD colspan=5 align=center><font color=#ff0000><B>{$l_autoroute_title}</B></font></TD></TR>
		<TR BGCOLOR="{$color_line2}">
		<TD align='center'><B><font size=2 color='#ff0000'>{$l_autoroute_id}</font></B></TD>
		<TD align='center'><B><font size=2 color='#ff0000'>{$l_autoroute_start}</font></B></TD>
		<TD align='center'><B><font size=2 color='#ff0000'>{$l_autoroute_destination}</font></B></TD>
		<TD align='center'><B><font size=2 color='#ff0000'>{$l_autoroute_warps}</font></B></TD>
		<TD align='center'><B><font size=2 color='#ff0000'>{$l_autoroute_deleteroute}</font></B></TD>
		</TR>
		<FORM ACTION=navcomp.php METHOD=POST>
		<INPUT TYPE=hidden name=state value=dismiss>

{php}
		for($i = 0; $i < $autocount; $i++){
			echo "<TR BGCOLOR=" . $autolinecolor[$i] .">";
			echo "<TD align='center'><font size=2 color='#ff0000'><b>$autorouteid[$i]</b></font></TD>\n";
			echo "<TD align='center'><font size=2 color=yellow><a href=navcomp.php?state=start&autoroute_id=$autorouteid[$i]>$autostart[$i]</a></font></TD>\n";
			echo "<TD align='center'><font size=2 color=yellow><a href=navcomp.php?state=reverse&autoroute_id=$autorouteid[$i]>$autoend[$i]</a></font></TD>\n";
			echo "<TD align='center'><font size=2 color=yellow>$warplist[$i]</font></TD>\n";
			echo "<td align='center'><INPUT TYPE=CHECKBOX NAME=$autodelete[$i] value=$autorouteid[$i]></td></TR>\n";
		}
{/php}

		<TR BGCOLOR="{$color_line2}">
		<TD colspan=5 align=center><font color=white size=2><B>{$l_autoroute_info}</B></font></td></tr>
		<INPUT TYPE=hidden name=autocount value={$autocount}>
		<TR BGCOLOR="{$color_line1}">
		<TD colspan=5 align=center><INPUT TYPE=submit value="{$l_autoroute_delete2}"></td></tr>
		</FORM>
		</TABLE><BR><BR>
	{else}
		<table border=0 cellspacing=1 cellpadding=2 width="100%"><tr><td>
			<B>{$l_autoroute_noroutes}</B><BR><BR>
		</td></tr></table>
	{/if}
	
	{if $state == 0}
		<form action="navcomp.php" method=post>
		{$l_nav_query} <input name=stop_sector>
		<input type=submit value={$l_submit}><br>
		<input name=state value=1 type=hidden>
		</form>
	{else}
		{if $found > 0}
			<h3>{$l_nav_pathfnd}</h3>
			{$start_sector} {$search_results_echo}<br>
			{$l_nav_answ1} {$search_depth} {$l_nav_answ2}<br><br>
		{else}
		{$l_nav_proper}<br><br>
		{/if}
	{/if}
{/if}
</td></tr>			
										<tr><td width="100%" colspan=3><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>

</table>
