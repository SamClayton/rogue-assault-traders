{literal}
<SCRIPT LANGUAGE="JavaScript">
var commandpage1 = '<div class=mnu>{/literal}{$commanddevices}{literal}<br>{/literal}{$commandplanetreport}{literal}<br>{/literal}{$commanddefensereport}{literal}<br>{/literal}{$commandreadmail}{literal}<br>{/literal}{$commandsendmail}{literal}<br>{/literal}{$commandteamforum}{literal}<br>{/literal}{$commandnav}{literal}<br>{/literal}{$commandautotrade}{literal}<br>{/literal}{$commandprobe}{literal}<br>'
+ '{/literal}{if $ksm_allowed == true}{$commandmap}{literal}<br>{/literal}{/if}'
+ '{if $ksm_allowed == true}{$commandlocalmap}{literal}<br>{/literal}{/if}'
+ '{if $spy_success_factor != 0}{$commandspy}{literal}<br>{/literal}{/if}' 
+ '{if $dig_success_factor != 0}{$commanddig}{literal}<br>{/literal}{/if}'
+ '{php}for($i = 0; $i < $newcommands; $i++){ echo $newcommandfull[$i]."<br>";}{/php}{literal}<br></div><center><A class=mnu href="#" onClick="rollover2()">Next Page</A></center>';

function rollover1() {
	document.getElementById('commandblock').innerHTML = commandpage1;
}
</script>
<SCRIPT LANGUAGE="JavaScript">
var commandpage2 = '<div class=mnu>{/literal}{$commandlog}{literal}<br>'
+ '{/literal}{$commandranking}{literal}<br>{/literal}{$commandblockmail}{literal}<br>{/literal}{$commandteams}{literal}<br>{/literal}{$commandteamship}{literal}<br>'
+ '{/literal}{$commanddestruct}{literal}<br>{/literal}{$commandoptions}{literal}<br>'
+ '{/literal}{if $ksm_allowed == true and $gd_enabled == true and $enable_spiral_galaxy == 1}{$command3dmap}{literal}<br>{/literal}{/if}'
+ '{$commandfaq}{literal}<br>{/literal}{$commandturorial}{literal}<br>{/literal}{$commandfeedback}{literal}<br>'
+ '{/literal}{if $link_forums != 0}{$commandforums}{literal}<br>{/literal}{/if}'
+ '{$commandlogout}{literal}<br><br></div><center><A class=mnu href="#" onClick="rollover1()">Prev Page</A></center>';

function rollover2() {
	document.getElementById('commandblock').innerHTML = commandpage2;
}
</script>
{/literal}
<br>
<TABLE STYLE="position:absolute;top:33;left:10; z-index:1"> <div style="border-style: dotted1none; border-color:white" id=scroll3 dir=rtl ;overflow:auto>
 <tr>
<TD ID="IEshout1" align="left">{$fadershout}
</TD>
</table>

  </td>

<TABLE STYLE="position:absolute;top:12;left:390; z-index:2"> <div style="border-style: dotted1none; border-color:white" id=scroll2 dir=rtl overflow:auto>
 <tr>
<td>
<img src="templates/{$templatename}images/topnav-mid.gif">

  </td>
</table>

<TABLE STYLE="position:absolute;top:37;left:400; z-index:2"> <div style="border-style: dotted1none; border-color:white" id=scroll3 dir=rtl overflow:auto>
 <tr>
<TD ID="IEfad1">{$fader}
</td>
</table>



<table cellspacing = "0" cellpadding = "0" border = "0" background = "templates/{$templatename}images/topnav-bg.gif" width = "100%">
<tr>
	<td width="100%"><img src="templates/{$templatename}images/topnav-left.gif">
	<td width="100%"><img src="templates/{$templatename}images/topnav-right.gif">

</td>
</tr>
		</table>


<table width="100%" border=0 align=center cellpadding=0 cellspacing=0>
<tr>
<td valign=top>
<table border="0" cellpadding="0" cellspacing="0" align="left"><tr valign="top">
<tr><td><br>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/spacer.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP>
{if $avatar != "default_avatar.gif"}
<p align="center"><img src="images/avatars/{$avatar}"></p>
		{/if}
</td></tr>
<tr><td class=normal>{$l_rank}: <img src="templates/{$templatename}images/rank/{$insignia}"></td></tr>
<tr><td class=normal>{$l_name}: <span class=mnu>{$player_name}</font></span></td></tr>
<tr><td class=normal>{$l_ship} {$l_name}:<span class=mnu><a href="report.php">{$shipname}</a></span></td></tr>
<tr><td class=normal>{$l_shiptype}:<span class=mnu>{$classname}</span></td></tr>
<tr><td class=normal>{$l_turns_have}<span class=mnu>{$turns}</span></td></tr>
<tr><td class=normal>{$l_turns_used}<span class=mnu>{$turnsused}</span></td></tr>
<tr><td class=normal>{$l_score}<span class=mnu>{$score}</span></td></tr>
</table>
</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>
<tr><td>
<br>
<table  border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-cmdtitle.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><TR><TD  ID="commandblock" NOWRAP>
<div class=mnu>
{$commanddevices}<br>
{$commandplanetreport}<br>
{$commanddefensereport}<br>
{$commandreadmail}<br>
{$commandsendmail}<br>
{$commandteamforum}<br>
{$commandnav}<br>

{$commandautotrade}<br>
{$commandprobe}<br>

{if $ksm_allowed == true}
	{$commandmap}<br>
{/if}
{if $ksm_allowed == true}
	{$commandlocalmap}<br>
{/if}
{if $spy_success_factor != 0}
	{$commandspy}<br>
{/if}

{if $dig_success_factor != 0}
	{$commanddig}<br>
{/if}

{php}
	for($i = 0; $i < $newcommands; $i++){
		echo $newcommandfull[$i]."<br>";
	}
{/php}
<br></div>
<center><A class=mnu href="#" onClick="rollover2()">Next Page</A></center>
</td></tr>
</table>
	</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>
<tr><td><br>

<table  border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-sbtitle.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><tr></tr>
	<form method="post" action="shoutbox3.php">
	<input type="Hidden" name="" value="1"><td NOWRAP class="shoutform">
	<textarea class="shoutform" wrap cols="18" rows="3">{$quickshout}</textarea><br>
	<input type="Text" name="sbt"  class="shoutform" size="14" maxlength="50"><input type="submit" name="go" value="Go" class="shoutform"><br>Public?&nbsp;
{if $team_id > 0}
	<INPUT TYPE=CHECKBOX NAME=SBPB class="shoutform" >
{else}
	<INPUT TYPE=CHECKBOX NAME=SBPB class="shoutform" checked>
{/if}
</td></form></tr></table>
	</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>

{if $sectorzero != 1}
<tr>
	<td><br>
	<table  border="0" cellspacing="0" cellpadding="0" align="left" width="195">
		<tr>
	    	<td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
		    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    		<td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
		</tr>
		<tr>
	    	<td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
		    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-lsstitle.gif" width="143" height="21"></td>
		    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
		</tr>
		<tr>
    		<td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
		    <td bgcolor="#000000" valign="top" align="center">
				<table cellpadding="0" align="left" cellspacing="0">
					<tr>
						<td valign="top" align="center" class="normal">
<span class=mnu>{$lss_info}</span>
						</td>
					</tr>
				</table>
			</td>
		    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
		</tr>
		<tr>
		    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
		    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
		    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
		</tr>
	</table>
	</td>
</tr>
{/if}

</table>
</td>

<td valign=top align="center">
&nbsp;<br>

<table border=0 width="100%" align="center">
<tr><td align=left class=nav_title_12>{if $sg_sector}
SG&nbsp;
{/if}{$l_sector}: <b>{$sector}</b></td>
<td align=center class=nav_title_12><b>{$beacon}</b>
</td><td align=right>
<a class=nav_title_14b href="zoneinfo.php?zone={$zoneid}"><b>{$zonename}</b></a>&nbsp;
</td></tr>
</table>
<table border=0 width="100%" align="center">
<tr><td colspan="2" class=nav_title_14b><center><b>{$l_tradingport}:</b></center>
</td></tr>
<tr align="center"><td>
<a href=port.php><img src="{$portgraphic}" border="0" alt=""><br>{$portname}</a>
</td>
	{if $shipyard != ""}
		<td><a href=shipyard.php><img src="{$shipyardgraphic}" border="0" alt=""><br>{$shipyard}</a></td>
	{/if}
</tr>
</table>

<table border=0 width="100%" align="center">
<tr><td colspan="5" class=nav_title_14b><center><b>{$l_planet_in_sec} {$sector}:</b></center>
</td></tr>
<tr align="center">
{php}
	if($countplanet != 0){
		for($i = 0; $i < count($planetid); $i++){
			echo "<td align=center valign=top class=nav_title_12>";
			echo "<A HREF=planet.php?planet_id=" . $planetid[$i] . ">";
			echo "<img src=\"$planetimg[$i]\" border=0></a><BR>";
			echo $planetname[$i];
			echo "<br>($planetowner[$i])";
			echo "</td>";
		}
	}else{
		echo "<td valign=top class=nav_title_12>$l_none</td>";
	}
{/php}

</tr>
</table>

<table border=0 width="100%">
<tr><td colspan="5" class=nav_title_14b><center><b><br>{$l_ships_in_sec} {$sector}:<br></b></center>
</td></tr>
<tr align="center">
{php}
	if($insector0 != 'sector0'){
		if($playercount != 0){
			$count = 0;
			for($i = 0; $i < $playercount; $i++){
   				if($shipprobe[$i] == "ship"){
					echo "<td align=center valign=top class=nav_title_12>";
					echo "<a href=ship.php?player_id=" . $player_id[$i] . "&ship_id=" . $ship_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR>";
					echo $shipnames[$i];
					echo "<br>($playername[$i])";
					if($teamname[$i] != "")
						echo "&nbsp;(<font color=#33ff00>$teamname[$i]</font>)";
					echo "</td>";
				}
   				if($shipprobe[$i] == "probe"){
					echo "<td align=center valign=top class=nav_title_12>";
					echo "<a href=showprobe.php?probe_id=" . $player_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR>";
					if($shipnames[$i] != "")
						echo $shipnames[$i];
					echo "<br>($playername[$i])";
					if($teamname[$i] != "")
						echo "&nbsp;(<font color=#33ff00>$teamname[$i]</font>)";
					echo "</td>";
				}
   				if($shipprobe[$i] == "debris"){
					echo "<td align=center valign=top class=nav_title_12>";
					echo "<a href=showdebris.php?debris_id=" . $player_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR>";
					echo "<br>($playername[$i])";
					echo "</td>";
				}
				$count++;
				if($count % 5 == 5)
					echo "</tr></table><table border=0 width=\"100%\"><tr>";
			}
		}else{
			echo "<td align=center class=nav_title_12>";
			echo "$l_none";
			echo "</td>";
		}
	}else{
		echo "<td valign=top align=center class=nav_title_12><b>$l_sector_0</b></td>";
	}
{/php}
</tr>
</table>
<br>
<table border=0 width="100%">
<tr><td align="center">
<table cellspacing = "0" cellpadding = "0" border = "0" align="center" width="280">
	<tr>
		<td width=18><img src = "templates/{$templatename}images/g-top-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-top-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-top-midright.gif" width="100%" height="20"></td>
		<td width=18><img src = "templates/{$templatename}images/g-top-right.gif"></td>
	</tr>
	<tr>
		<td colspan=4>
			<table cellspacing = "0" cellpadding = "0" border = "0" width="100%" valign="top">
				<tr>
					<td valign="top">	
						<table cellspacing = "0" cellpadding = "0" border = "0" width="100%" valign="top">
							<tr bgcolor="black">
								<td valign="top" width=18><img src = "templates/{$templatename}images/g-mid-left.gif" height="{php} echo ($fightercount > $minecount) ? ($fightercount * 32) + 60 : ($minecount * 32) + 60; {/php}" width="18"></TD>
								<td valign="top">
									<table cellspacing = "0" cellpadding = "0" border = "0" width="100%" valign="top" align="center">
<tr><td colspan="5"><center><b><font class=nav_title_14b>{$l_sector_def}:</font><br></b></center>
</td></tr>
<tr>
{if $defensecount != 0}
{php}
	$count = 0;
	for($i = 0; $i < $defensecount; $i++){
		if($defensetype[$i] == "F"){
			if($count == 0){
				echo "<td align=center valign=top><img src=templates/" . $templatename . "images/fighters.gif><br>";
			}
			echo "<font class=normal>";
			echo "<a class=mnu href=modify-defences.php?defence_id=" . $defenseid[$i] . ">";
			echo $defplayername[$i];
			echo "</a><br>";
			echo " ($defenseqty[$i] $defensemode[$i])";
			echo "</font><br>";
			$count++;
		}
	}
	if($count != 0)
		echo "</td>";
{/php}
{php}
	$count = 0;
	for($i = 0; $i < $defensecount; $i++){
		if($defensetype[$i] == "M"){
			if($count == 0){
				echo "<td align=center valign=top><img src=templates/" . $templatename . "images/mines.gif><br>";
			}
			echo " <font class=normal>";
			echo "<a class=mnu href=modify-defences.php?defence_id=" . $defenseid[$i] . ">";
			echo $defplayername[$i];
			echo "</a><br>";
			echo " ($defenseqty[$i] $defensemode[$i])";
			echo "</font><br>";
			$count++;
		}
	}
	if($count != 0)
		echo "</td>";

{/php}
{else}
	<td valign=top align=center><font color="{$general_highlight_color}" size=2>{$l_none}</font></td>
{/if}
</tr>									</table>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" width=18><img src = "templates/{$templatename}images/g-mid-right.gif" height="{php} echo ($fightercount > $minecount) ? ($fightercount * 32) + 60 : ($minecount * 32) + 60; {/php}" width="18"></TD>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-bottom-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-bottom-midright.gif" width="100%" height="12"></td>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-right.gif"></td>
	</tr>
</table>
</td></tr></table>
<td valign=top>
<br>
				
				
<table  border="0" cellspacing="0" cellpadding="0" align="right">

<tr><td>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-cargo-title.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0">
	<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_ore}" src="templates/{$templatename}images/ore.png">&nbsp;{$l_ore}&nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$shipinfo_ore}&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_organics}" src="templates/{$templatename}images/organics.png">&nbsp;{$l_organics}&nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$shipinfo_organics}&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_goods}" src="templates/{$templatename}images/goods.png">&nbsp;{$l_goods}&nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$shipinfo_goods}&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_energy}" src="templates/{$templatename}images/energy.png">&nbsp;{$l_energy}&nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$shipinfo_energy}&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_colonists}" src="templates/{$templatename}images/colonists.png">&nbsp;{$l_colonists}&nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$shipinfo_colonists}&nbsp;</span></td></tr>
<tr><td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_credits}" src="templates/{$templatename}images/credits.png">&nbsp;{$l_credits} &nbsp;</td></tr>
 <tr><td nowrap align='right'><span class=mnu>&nbsp;{$playerinfo_credits}&nbsp;</span></td></tr>
	
	</td></tr></table>
</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>

<tr><td>
<br>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-tr-title.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP>

{if $num_traderoutes == 0}
<TR><TD NOWRAP>
<div class=mnu><center><div class=dis>&nbsp;{$l_none} &nbsp;</div></center><br>
</div>
</td></tr>
{else}
{php}
	for($i = 0; $i < count($traderoute_links); $i++){
		echo "<tr><td class=\"nav_title_12\">&nbsp;<a class=mnu href=traderoute_engage.php?engage=" . $traderoute_links[$i] . ">" . $traderoute_display[$i] . "</a>&nbsp;</td><tr>";
	}
{/php}

{/if}
<tr><td nowrap><br>
<div class=mnu>
&nbsp;<a class=mnu href=traderoute_listroutes.php>{$l_trade_control}</a>&nbsp;<br>

</div></td></tr></table>
</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</tr></td>

<tr><td><br>
<table  border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-wttitle.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><tr><td NOWRAP>
<div class=mnu>
{php}
	if(count($links) == 0)
		echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<b>$linklist<b>&nbsp;</td></tr>\n";

	for($i = 0; $i < count($links); $i++){
		echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"move.php?move_method=warp&sector=$links[$i]\">=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=\"lrscan.php?command=scan&sector=$links[$i]\">[$l_scan]</a>&nbsp;</td></tr>\n";
	}
{/php}
</div>
</td></tr>

<tr><td colspan=2 align=center class=dis><a href="lrscan.php?sector=*" class=dis>[{$l_fullscan}]</a></td></tr>

{if $autototal != 0}
<tr valign="top">
<td nowrap  align="center"><br><font face="verdana" size="1" color="{$main_table_heading}"><b>
{$l_main_autoroute}
</b></font><br><hr></td>
</tr><tr>
<td NOWRAP align="center">
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0  align="center">
{php}
	for($i = 0; $i < count($autolist); $i++){
		if($sector <= $sector_max and $autostart[$i] <= $sector_max)
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</a>&nbsp;</td></tr>\n";

		if($sector <= $sector_max and $autoend[$i] <= $sector_max)
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</a>&nbsp;</td></tr>\n";

		if($sector > $sector_max and $autostart[$i] == $sector)
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</a>&nbsp;</td></tr>\n";

		if($sector > $sector_max and $autoend[$i] == $sector)
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</a>&nbsp;</td></tr>\n";

		if($sector > $sector_max and (($autostart[$i] - 1) == $sector or ($autostart[$i] + 1) == $sector))
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</a>&nbsp;</td></tr>\n";

		if($sector > $sector_max and (($autoend[$i] - 1) == $sector or ($autoend[$i] + 1) == $sector))
			echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</a>&nbsp;</td></tr>\n";
	}
{/php}

</td></tr>
</table>
{/if}</td></tr></table>
	</td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>
<tr><td><br>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-lt1.gif" width="23" height="13"></td>
    <td align="right" background="templates/{$templatename}images/b-tbar-bg.gif"><img src="templates/{$templatename}images/b-tbar-cnt.gif" width="143" height="13"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rt.gif" width="29" height="13"></td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-tbar-ls.gif" width="23" height="21"></td>
    <td bgcolor="#000000"><img src="templates/{$templatename}images/b-tbar-rstitle.gif" width="143" height="21"></td>
    <td><img src="templates/{$templatename}images/b-tbar-rs.gif" width="29" height="21"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/b-lbar-bg.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP><div class=mnu align=center>

{if ($shipinfo_sector_id -1) >= 1}
&nbsp;<a class="mnu" href="move.php?move_method=real&engage=1&destination={$rslink_sector_back}">{$rslink_sector_back} ({$rslink_sector_back_dist})&lt;=</a>

{/if}

{if ($shipinfo_sector_id +1) <= $sector_max}
&nbsp;<a class="mnu" href="move.php?move_method=real&engage=1&destination={$rslink_sector_forward}">=&gt;{$rslink_sector_forward} ({$rslink_sector_forward_dist})</a>&nbsp;
<br>
{/if}
<br>
</div></td></tr><tr><td nowrap=""><div class=mnu>
<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=0 BGCOLOR="#000000" width="160">
<form name="lastsector"><tr><td class="nav_title_12" align=center>
<select name="menu" onChange="location=document.lastsector.menu.options[document.lastsector.menu.selectedIndex].value;" value="GO" class="rsform"><option value="">RS to Last Sector</option>
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[0]}">{$lastsectors[0]}</option>
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[1]}">{$lastsectors[1]}</option>
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[2]}">{$lastsectors[2]}</option>
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[3]}">{$lastsectors[3]}</option>
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[4]}">{$lastsectors[4]}</option>
</select></form></td></tr>

{php}
	echo "<form name=\"presets\"><tr><td class=\"nav_title_12\" align=center>\n";
	echo "<select name=\"menu\" onChange=\"location=document.presets.menu.options[document.presets.menu.selectedIndex].value;\" value=\"GO\" class=\"rsform\"><option value=\"\">RS to Sector</option>\n";
	for($i = 0; $i < count($preset_display); $i++){
		echo "<option value=\"move.php?move_method=real&engage=1&amp;destination=$preset_display[$i]\">$preset_display[$i] - $preset_info[$i] ($preset_dist[$i])</option>\n";
	}
	echo "</select></td></tr>\n";
	
{/php}

<tr><td class="nav_title_12" align=center>&nbsp;<a class=dis href="preset.php?name=set">[{$l_set}]</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a class=dis href="preset.php?name=add">[{$l_add}]</a>&nbsp;</td></tr></form>
<tr><td class="nav_title_12" align=center>
<form method="post" action="move.php"><input type="hidden" name="move_method" value="real">
<input type="text" name="destination" class="rsform" maxlength="10" size="8"><br>
<input type="submit" name="explore" value="&nbsp;?&nbsp;" class="rsform">
<input type="submit" name="go" value="Go" class="rsform">
</form></td></tr>
</table></td></tr></table></td>
    <td background="templates/{$templatename}images/b-rbar.gif">&nbsp;</td>
  </tr>
  <tr>
    <td><img src="templates/{$templatename}images/b-bar-ls_01.gif" width="23" height="12"></td>
    <td background="templates/{$templatename}images/b-bar-bg.gif"></td>
    <td><img src="templates/{$templatename}images/b-bar-rs_03.gif" width="29" height="12"></td>
  </tr>
</table>
</td></tr>
</table>
<center>
				</tr>
				</table>
</center>
<img src="{$starsize}" border="0" alt="" style="position: absolute; z-index:-1; left: 70%; top: 30%; width: 480px; height: 480px; margin-left: -240px; margin-top: -240px;">
