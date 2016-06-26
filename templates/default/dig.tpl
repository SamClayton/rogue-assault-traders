<h1>{$title}</h1>

{literal}
	<style type="text/css">
		.border {
			border-collapse: collapse; 
			border: 1px solid #ccc; 
		}
		.yellow { color: yellow; }
		.white { color: #ff0000; }
	</style>
{/literal}

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
{if $command == "dismiss"}
	<tr><td>{$dismisstotal} {$l_dig_dismiss2}<br><br>
	<a href=dig.php>{$l_clickme}</a> {$l_dig_menu}</td></tr>
{else}
	{if $totaldigs}
		{if $totaldigsbyplanet}
<TR BGCOLOR="{$color_header}"><TD colspan=7 align=center><font color=white><B>{$l_dig_defaulttitle2}</B></font></TD></TR>
			<TR BGCOLOR="$color_line2}">
			<TD><B><A HREF=dig.php?by2=id>{$l_dig_codenumber}</A></B></TD>
			<TD><B><A HREF=dig.php?by2=planet>{$l_dig_planetname}</A></B></TD>
			<TD><B><A HREF=dig.php?by2=sector>{$l_dig_sector}</A></B></TD>
			<TD><B><A HREF=dig.php?by2=job_id>{$l_dig_job}</A></B></TD>
			<TD><B>{$l_dig_dismiss}</B></TD>
			</TR>
			<FORM ACTION=dig.php METHOD=POST>
			<INPUT TYPE=hidden name=command value=dismiss>
			{php}
			$line_color = $color_line2;
			for($i = 0; $i < $digcount; $i++)
			{
				if($line_color == $color_line1)   
					$line_color = $color_line2; 
				else
					$line_color = $color_line1; 

				echo "<TR BGCOLOR=$line_color><TD><font size=2 color=#ff0000>$digid[$i]</font></TD><TD><font size=2 color=#ff0000>$digname[$i]</font></TD><TD><font size=2><a href=move.php?move_method=real&engage=1&destination=$digsector[$i]>$digsector[$i]</a></font></TD><TD><font size=2 color=#ff0000>$digjob[$i]</font></TD><td><INPUT TYPE=CHECKBOX NAME=dismiss[$i] value=$digid[$i]></td></TR>";
			}
			{/php}
			<INPUT TYPE=hidden name=digcount value={$digcount}>
			<TR BGCOLOR="{$color_line2}">
			<TD colspan=7 align=center><INPUT TYPE=submit value="{$l_dig_changebutton}"></td></tr>
			</FORM>
			</TABLE><BR><BR>
		{else}
			<B>{$l_dig_no2}</B><BR><BR>
		{/if}

		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td bgcolor="{$color_header}"><font color=#ff0000><b>&nbsp;
					{if $digonship}
						{$l_dig_defaulttitle4}:  <span class="yellow">{$digshiptotal}</span>
					{else} 
						{$l_dig_no4}
					{/if}
					</b></font>
				</td>
			</tr>

		&nbsp;<br>
	{else}
		<tr><td>{$l_dig_nodignitaryatall}.</td><tr>
	{/if}
{/if}

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
   
  </tr>
 
</table>

