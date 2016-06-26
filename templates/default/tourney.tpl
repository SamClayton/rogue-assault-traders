<table cellspacing = "0" cellpadding = "0" border = "0" width = "600" align="center">
<tr>
	<td><img src="templates/{$templatename}images/topnav-left.gif" width="41" height="55"></td>
	<td background="templates/{$templatename}images/topnav-bg.gif" width="100%" height="55" ID="IEshout1" align="center">
{literal}
<script language="javascript" type="text/javascript">
 	function OpenSB()
		{
			f2 = open("shoutbox.php","f2","width=700,height=400,scrollbars=yes");
		}
</script>	
<SCRIPT LANGUAGE="JavaScript1.2" TYPE="text/javascript">
<!--
arTXTa = new Array({/literal}{php}for($i = 0; $i < $shoutcount; $i++) echo "\"" . $shoutmessage[$i] . "\", "; {/php}{literal}"End of Shouts");

document.write('<LAYER ID=shout><\/LAYER>');
NS4 = (document.layers);
IE4 = (document.all);

FDRblendInta = 3; // seconds between flips
FDRmaxLoopsa = 200; // max number of loops (full set of headlines each loop)
FDRendWithFirsta = true;

FDRfinitea = (FDRmaxLoopsa > 0);
blendTimera = null;

arTopNewsa = [];
for (i1=0;i1<arTXTa.length;i1++)
{
 arTopNewsa[arTopNewsa.length] = arTXTa[i1];
}

if(NS4)
{
	shout1 = document.shout;
	shout1.visibility="hide";

	pos11 = document.images['pht1'];
	pos1E1 = document.images['ph1E1'];
	shout1.left = pos11.x;
	shout1.top = pos11.y;
	shout1.clip.width = 350;
	shout1.clip.height = pos1E1.y - shout1.top;
}
else 
{
	document.getElementById('IEshout1').style.pixelHeight = document.getElementById('IEshout1').offsetHeight;
}

function FDRredoa()
{
	if (innerWidth==origWidtha && innerHeight==origHeighta) return;
	location.reload();
}

function FDRcountLoadsa() 
{
	if (NS4)
	{
		origWidtha = innerWidth;
		origHeighta = innerHeight;
		window.onresize = FDRredoa;
	}

	TopnewsCounta = 0;
	TopLoopCounta = 0;

	FDRdoa();
	blendTimera = setInterval("FDRdoa()",FDRblendInta*1000)
}

function FDRdoa() 
{
	if (FDRfinitea && TopLoopCounta>=FDRmaxLoopsa) 
	{
		FDRenda();
		return;
	}
	FDRfadea();

	if (TopnewsCounta >= arTopNewsa.length) 
	{
		TopnewsCounta = 0;
		if (FDRfinitea) TopLoopCounta++;
	}
}

function FDRfadea(){
	if(TopLoopCounta < FDRmaxLoopsa) {
		TopnewsStra = "";
		for (var i1=0;i1<1;i1++)
		{
			if(TopnewsCounta < arTopNewsa.length) 
			{
				TopnewsStra += "<P><A CLASS=headlines TARGET=_new HREF='shoutbox.php'>"
							+ arTopNewsa[TopnewsCounta] + "</" + "A><img src='/images/spacer.gif' width=1 height=15></" + "P>"
				TopnewsCounta += 1;
			}
		}
		if (NS4) 
		{
			shout1.document.write(TopnewsStra);
			shout1.document.close();
			shout1.visibility="show";
		}
		else 
		{
			document.getElementById('IEshout1').innerHTML = TopnewsStra;
		}
	}
}

function FDRenda(){
	clearInterval(blendTimera);
	if (FDRendWithFirsta) 
	{
		TopnewsCounta = 0;
		TopLoopCounta = 0;
		FDRfadea();
	}
}

//-->
</SCRIPT>
{/literal}
	</td>
	<td><img src="templates/{$templatename}images/topnav-right.gif" width="22" height="55"></td>
</tr>
</table>

<table cellspacing = "0" cellpadding = "0" border = "0" width = "650" align="center">
<tr>
	<td><img src="templates/{$templatename}images/topnav-left.gif" width="41" height="55"></td>
	<td background="templates/{$templatename}images/topnav-bg.gif" width="100%" height="55" align="center">
   <font color="{$general_text_color}" size=3 face="arial"><img src="templates/{$templatename}images/rank/{$insignia}" height="18">
	<b>
	 <font color="{$general_highlight_color}">{$player_name}
	 </font>
	</b>
   </font>
  <font color=silver size=3 face=arial>
  {$l_abord}
   <b>
	<font color="{$general_highlight_color}">
	 <a href="report.php">{$shipname}</a> ({$classname})
	</font>
   </b></font></td>
	<td><img src="templates/{$templatename}images/topnav-right.gif" width="22" height="55"></td>
</tr>
</table>

<table border=0 align=center cellpadding=0 cellspacing=0>
<tr>
<td valign=top align="right">
		{if $avatar != "default_avatar.gif"}
			<table BORDER=1 CELLPADDING=0 CELLSPACING=0 align="center">
			<tr><td>
			<img src="images/avatars/{$avatar}">
			</td></tr></table>
		{/if}
		{if $teamicon != "default_icon.gif"}
			</td><td align="center">&nbsp;&nbsp;<=-=>&nbsp;&nbsp;</td><td align="left">
			<table BORDER=1 CELLPADDING=0 CELLSPACING=0 align="center">
			<tr><td>
			<img src="images/icons/{$teamicon}">
			</td></tr></table>
		{/if}
</td></tr></table>
<br>

<table width="170" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%" align="center">
</tr><td nowrap  align="center"><font face="verdana" size="2" color="{$main_table_heading}"><b>
{$l_commands}
</b></font><br><br></td></tr>
<TR><TD NOWRAP>
<div class=mnu>
&nbsp;<a class=mnu href="javascript:OpenSB()">{$shoutboxtitle}</a><br>
{$commandreadmail}<br>
{$commandsendmail}<br>
{$commandranking}<br>
{$commandteams}<br>
{$commandteamforum}<br>
{$commandteamship}<br>
{$commanddestruct}<br>
{$commandoptions}<br><br>
</div>
</td></tr>
<tr><td nowrap>
<div class=mnu>
{$commandfeedback}<br><br>
</div>
</td></tr>
<tr><td nowrap>
{$commandlogout}<br>
</td></tr>
		</table>
	</td>
   
  </tr>

</table>
