<body bgcolor="#000000" text="darkred">

<center>
<table border="0" align="center">
  <tr>
	<td>
<p align="center"><img border="1" src="templates/default/images/Roguebanner1.gif"width="450" height="50"><br> 
		<font size="-1" face="Verdana, Arial, Helvetica, sans-serif"><b>[{$version}]</b></font></p>
	</td>
  </tr>
</table><table border="0" align="center">
  <tr>
	<td>
		<p align="center"><img border="1" src="templates/default/images/star1.gif" width="450" height="125"><br>
		
		<font size="-1" face="Verdana, Arial, Helvetica, sans-serif"><b>{$scheduled_reset}</b></font>
		</p>
	</td>
  </tr>
</table>
<center>
<form method="post" action="login2.php" style="background:black; border-style:none;">
<table border="0" align="center">
  <tr>
	<td width="200" height="23"><img border="0" src="templates/{$templatename}images/playername.gif"></td>
	<td width="250" background="templates/{$templatename}images/loginbox.png">
		<p align="center">
		<input type="text" name="character_name" value="{$character_name}" size="32" maxlength="20" style="color:#ff0000; font-weight: bold; background-color:BLACK; align:left; border-style:none;">
		</p>
	</td>
  </tr>
  <tr>
	<td width="200" height="23"><img border="0" src="templates/{$templatename}images/password.gif"></td>
	<td width="250" background="templates/{$templatename}images/loginbox.png">
		<p align="center">
		<input type="password" name="pass" value="{$password}" size="32" maxlength="{$maxlen_password}" style="color:#ff0000; font-weight:bold; background-color:BLACK; align:left; border-style:none;">
		</p>
	</td>
  </tr>
 </table>
<br>
<table border="0" align="center">
  <tr>
	<td width="255"><a href="new.php"><img border="0" src="templates/{$templatename}images/newplayer.gif" align="left"></a></td>
	<td width="134"><input type="image" name="login" src="templates/{$templatename}images/login.gif" value="{$l_login_title}"></td>
  </tr>
  <tr>
	<td width="255"><br><img border="0" src="templates/{$templatename}images/language.gif"></td>
	<td width="134" align="center"><br><select NAME=newlang style="color:#FF0000; font-weight:bold; background-color:black; align:left; border-style:none;">
		{$login_drop_down}
		</select>
	</td>
  </tr></table>
<br>
<table border="0" align="center">
  <tr>
	<td width="164"><a href="{$link_forums}"><img border="0" src="templates/{$templatename}images/forums.gif"></a></td>
	<td width="174"><a href="ranking.php"><img border="0" src="templates/{$templatename}images/ranking.gif"></a></td>
	<td width="144"><a href="settings.php"><img border="0" src="templates/{$templatename}images/setting.gif"></a></td>
	<td width="144"><a href="faq/index.php"><img border="0" src="templates/{$templatename}images/faq.gif"></a></td>
  </tr>
</table>
<br>

{if $main_site != ''}
<table border="0" align="center">
  <tr>
	 <td width="144">
	  <p align="center"><a href="{$main_site}"><img border="0" src="templates/{$templatename}images/returntosite.gif"></a>
	 </td>
  </tr>
</table>
{/if}

{if $serverlist != ''}
<table border="0" align="center">
  <tr>
	 <td width="144">
	  <p align="center"><a href="{$serverlist}servers"><img border="0" src="templates/{$templatename}images/serverlist.gif"></a>
	 </td>
  </tr>
</table>
{/if}

{literal}
<script language="javascript" type="text/javascript">

<!--
var swidth = 0;
if(self.screen)
{
  swidth = screen.width;
  document.write("<input type=\"hidden\" name=\"res\" value=\"" + swidth + "\"><\/input>");
}
if(swidth != 640 && swidth != 800 && swidth != 1024 && swidth != 1280)
{
  document.write("<table><tr><td colspan=2>");
  document.write("{/literal}{$l_login_chooseres}{literal}");
  document.write("<br><center><input type=\"radio\" name=\"res\" value=\"640\">640x480&nbsp;&nbsp;<\/input>");
  document.write("<input type=\"radio\" name=\"res\" checked value=\"800\">800x600&nbsp;&nbsp;<\/input>");
  document.write("<input type=\"radio\" name=\"res\" value=\"1024\">1024x768&nbsp;&nbsp;<\/input>");
  document.write("<input type=\"radio\" name=\"res\" value=\"1280\">1280x1024<\/input><\/center>");
  document.write("<\/td><\/tr><\/table>");
}
-->

</script>
{/literal}

</form>
</center>
<!-- <table border="0" align="center">
  <tr>
	 <td width="144">
<EMBED SRC="templates/{$templatename}sounds/JOCKROCK.MID" hidden="true" width="1" height="1" AUTOSTART="TRUE" REPEAT="TRUE">
	 </td>
  </tr>
</table>
 -->
 
{if $showserverlist == 1}
	<table width="800" border="1" cellspacing="1" cellpadding="1" align="center">
	{php}
	for($i = 0; $i < $servercount; $i++){
		echo "<tr><td class=mnu><a href=\"http://$serverurl[$i]\" class=mnu>$servername[$i]</a></td><td align=\"center\"><span class=mnu>$serversectors[$i] Sectors</span></td><td align=\"center\"><span class=mnu>$serverplayers[$i] Players</span></td><td align=\"center\"><span class=mnu>$servertop[$i]</span></td><td align=\"center\"><span class=mnu>$serverreset[$i]</span></td></tr>";
	}
	{/php}
	</table>
{/if}
<center><br><br>
{if (!empty($adminnews))}
<table border=0 cellpadding=2 cellspacing=0 align="center" width=650>
  <tr>
	<td ID="adminnews" class="News" align=center><br><br></td>
  </tr>
</table>
{/if}

{$adminnews}
