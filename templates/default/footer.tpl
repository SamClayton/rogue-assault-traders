{if $instantmessagecount > 0} 
   {literal} 
   <script language="javascript" type="text/javascript"> 
   { alert('{/literal}{$l_youhave} {$instantmessagecount} {$l_messages_wait}{literal}'); } 
   </script> 
   {/literal} 
{/if}
<br>
</td></tr></table>

{literal}
<script language="javascript" type="text/javascript">
 var myi = {/literal}{$seconds_until_update}{literal};
 setTimeout("rmyx();",1000);

  function rmyx()
   {
	myi = myi - 1;
	if (myi <= 0)
	 {
		 myi = {/literal}{$scheduler_ticks}{literal} * 60;
	 }
	document.getElementById("myx").innerHTML = myi;
	setTimeout("rmyx();",1000);
   }
</script>
{/literal}
<table width="100%" border=0 cellspacing=0 cellpadding=0>
	<tr>		  
	  <td align=center class="footer"><b><span id=myx class="footer">{$seconds_until_update}</span></b> {$footer_until_update} <br> 
{$footer_players_online}<br>
<a href="news.php" class="footer">{$l_footer_news}</a></td>
	</tr>			   
  </table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td class="footer">&nbsp;&nbsp;<a href="http://www.springfield.net/games">Springfield Net</a></td>
    <td rowspan="2" valign="middle" align=center class="footer" width="34%">
		{if $currentprogram != ""}
			{$l_footer_click} <a href="#" onClick="window.open('help.php?help={$currentprogram}','help','height=400,width=820,scrollbars=yes,resizable=no');">{$l_here}</a>{$l_footer_help}
		{/if}</td>
  </tr>
  <tr> 
    <td class="footer" width="33%">&nbsp;&nbsp;<a href="http://www.sourceforge.net/projects/aatrade">{$l_footer_title}</a><br><br></td>
    <td align=right class="footer" width="33%">© 2000-2004 <a href="docs/copyright.htm">AATRADE / NGS / BNT Developers</a>&nbsp;&nbsp;<br><br></td>
  </tr>
</table>
  {$banner_bottom}
<!-- End of legally-required footer -->
{if (!empty($maindiv))}
</div>
{/if}
</body>
</html>
