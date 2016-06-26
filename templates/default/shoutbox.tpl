<H1>{$title}</H1>

<FORM NAME='sb' ACTION='shoutbox2.php'>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<TR BGCOLOR='{$color_header}'>
<TH COLSPAN=2 NOWRAP><FONT COLOR=ff0000>{$l_shout_title2}</FONT></TH>
</TR>
<TR><TD COLSPAN=2 NOWRAP ALIGN=CENTER>
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 width=100%>
{if $countflag2 == 1}
	<TR BGCOLOR='{$color_header}'>
	<TD COLSPAN=3 NOWRAP ALIGN=CENTER><FONT size=-1 width='50%'><B>{$l_shout_public}</B></FONT></TD>
	<TD COLSPAN=3 NOWRAP ALIGN=CENTER><FONT size=-1 width='50%'><B>{$l_shout_team}</B></FONT></TD>
	</TR>
{else}
	<TR BGCOLOR='{$color_header}'>
	<TD COLSPAN=3 NOWRAP ALIGN=CENTER><FONT size=-1 width='100%'><B>{$l_shout_public}</B></FONT></TD>
	</TR>
{/if}

{if $countflag > 0}
	{php}
	for ( $i = 0 ; $i < $total ; $i++ )
	{
		if ($countflag2 == 1){
			echo "<tr> 
				<td rowspan=2 align=center valign=middle width=64 height=64><img src='images/$publicavatar[$i]'></td>
				<td ALIGN=LEFT width='25%'><FONT SIZE=-1><B>$playernamea[$i]</B></FONT></td>
				<td ALIGN=RIGHT width='25%'><FONT SIZE=-1><I>$datea[$i]</I></FONT></td>
				<td rowspan=2 align=center valign=middle width=64 height=64><img src='images/$privateavatar[$i]'></td>
				<td ALIGN=LEFT width='25%'><FONT SIZE=-1><B>$playernameb[$i]</B></FONT></td>
				<td ALIGN=RIGHT width='25%'><FONT SIZE=-1><I>$dateb[$i]</I></FONT></td>
			  </tr>
			  <tr> 
				<td colspan=2 ALIGN=LEFT width='50%'>$messagea[$i]</td>
				<td colspan=2 ALIGN=LEFT width='50%'>$messageb[$i]</td>
			  </tr>";
			echo "<TR>";
			echo "<TD COLSPAN=3 ><IMG height=1 width=1 SRC='images/spacer.gif'><hr></TD><TD COLSPAN=3 BGCOLOR='$color_line2'><IMG height=1 width=1 SRC='images/spacer.gif'><hr></TD>";
			echo "</TR>";
		}else{
			echo "<tr> 
				<td rowspan=2 align=center valign=middle width=64 height=64><img src='images/$publicavatar[$i]'></td>
				<td ALIGN=LEFT width='50%'><FONT SIZE=-1><B>$playernamea[$i]</B></FONT></td>
				<td ALIGN=RIGHT width='50%'><FONT SIZE=-1><I>$datea[$i]</I></FONT></td>
			  </tr>
			  <tr> 
				<td colspan=2 ALIGN=LEFT width='100%'>$messagea[$i]</td>
			  </tr>";
			echo "<TR>";
			echo "<TD COLSPAN=3 ><IMG height=1 width=1 SRC='images/spacer.gif'><hr></TD>";
			echo "</TR>";
		}
	}
	{/php}
{/if}

</TABLE>
</TD></TR>

<TR BGCOLOR='{$color_line2}'><TD COLSPAN=2 NOWRAP ALIGN=CENTER>
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2 width=100%>
<TR BGCOLOR='{$color_line1}'>
<TD NOWRAP ALIGN=LEFT><INPUT TYPE=TEXT NAME='sbt' Value='' MAXLENGTH=100></TD>
<TD NOWRAP ALIGN=RIGHT>{$l_shout_public}?&nbsp;({$l_shout_else} {$l_shout_team})&nbsp;<INPUT TYPE=CHECKBOX NAME=SBPB {$checked}></TD>
</TR>
<TR BGCOLOR='{$color_line1}'>
<TD NOWRAP ALIGN=LEFT><INPUT TYPE=SUBMIT VALUE='SHOUT'>&nbsp;&nbsp;<A HREF='shoutbox_smilie.php?template={$template}'>{$l_shout_smiles}</A></TD>
<TD NOWRAP ALIGN=RIGHT><INPUT TYPE=RESET VALUE='CLEAR'></TD>
</TR>
</TABLE>
</TD></TR>
</FORM>
<tr><td align="center"><a href="shoutbox.php">{$l_shout_refresh}</a>&nbsp;&nbsp;&nbsp;<a href="javascript:window.close();">{$l_shout_close}</a>
</td></tr>
		</table>
	</td>
    
  </tr>

</table>
