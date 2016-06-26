<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
<center>

<table width=100% border=0 cellspacing=0 cellpadding=0>
{if !$isadmin}
	<tr><td><td><table width=300 border=0 cellspacing=0 cellpadding=0 align=center><tr><td align=center>
	<font color=cyan size =2><b>{$l_log_select}</b></font><br><br>
	<a href=log.php?loglist=&startdate={$startdate}><font color=#ff0000 size =2><b>{$l_log_general}</b></font></a> - 
	<a href=log.php?loglist=1&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_dig}</b></font></a><br>
	<a href=log.php?loglist=2&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_spy}</b></font></a> - 
	<a href=log.php?loglist=3&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_disaster}</b></font></a><br>
	<a href=log.php?loglist=4&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_nova}</b></font></a> - 
	<a href=log.php?loglist=5&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_attack}</b></font></a><br>
	<a href=log.php?loglist=6&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_scan}</b></font></a> - 
	<a href=log.php?loglist=7&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_starv}</b></font></a><br>
	<a href=log.php?loglist=9&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_probe}</b></font></a> - 
	<a href=log.php?loglist=10&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_autotrade}</b></font></a><br>
	<a href=log.php?loglist=8&startdate={$startdate}><font color=#ff0000 size=2><b>{$l_log_combined}</b></font></a><br></td></tr></table></td></td></tr>
{/if}

<tr><td><td width=100%><td></tr>
<tr><td><td background-repeat:no-repeat">
<font size=2 color=#ff0000><b>&nbsp;&nbsp;&nbsp;{$logline}</b></font>
</td><td><td>&nbsp;</tr>
<tr><td valign=bottom>

<td colspan=2><table border=0 width=100%><tr><td>

<div id="divScroller1">
<div id="dynPage0" class="dynPage">
<center>
<br>
<font size=2 color=#ff0000><b>{$l_log_start} {$entry}<br><br>{$logtype}<b></font>
<p>
<hr width=80% size=1 NOSHADE style="color: #ff0000">
</center>

{php}
for($i = 0; $i < $logcount; $i++)
{
	echo "<table border=0 cellspacing=5 width=100%>" .
		 "<tr>" .
		 "<td><font size=2 color=#ff0000><b>$logtitle[$i]</b></td>" .
		 "<td align=right><font size=2 color=#ff0000><b>$logtime[$i]</b></td>" .
		 "<tr><td colspan=2>" .
		 "<font size=2 color=#ff0000>" .
		 "$logbody[$i]" .
		 "</td></tr>" .
		 "</table>" .
		 "<center><hr width=80% size=1 NOSHADE style=\"color: #ff0000\"></center>";
}
{/php}

<center>
<br>
<font size=2 color=#DEDEEF><b>{$l_log_end} {$endentry}<b></font>
<p>
</center>
</div>

</td></tr></table>

</div>

  <tr><td><td align=right>
	<a href=log.php?loglist={$loglist}&startdate={$backlink}><font color=#ff0000 size =3><b>««</b></font></a>&nbsp;&nbsp;&nbsp;
	<a href=log.php?loglist={$loglist}&startdate={$yesterday2}><font color=#ff0000 size=3><b>{$date3}</b></font></a>
	&nbsp;|&nbsp;
	<a href=log.php?loglist={$loglist}&startdate={$yesterday}><font color=#ff0000 size=3><b>{$date2}</b></font></a>
	 | 
	<a href=log.php?loglist={$loglist}&startdate={$newstartdate}><font color=#ff0000 size=3><b>{$date1}</b></font></a>

{if $nonext != 1}
	&nbsp;&nbsp;&nbsp;<a href=log.php?loglist={$loglist}&startdate={$nextlink}><font color=#ff0000 size=3><b>»»</b></font></a>
{/if}

&nbsp;&nbsp;&nbsp;

{if $isadmin}
  <tr><td><td>
	<FORM action=admin.php method=POST>
	<input type=hidden name=md5swordfish value="{$md5swordfish}">
	<input type=hidden name=menu value=logview>
	<input type=submit value="Return to Admin"></td></tr>
{else}
	<tr><td><td><p><font size=2 face=arial>{$l_log_click}</td></tr>
{/if}

</table>
</center>
				</td>
			</tr>
		</table>
	</td>
    
  </tr>
 
</table>
