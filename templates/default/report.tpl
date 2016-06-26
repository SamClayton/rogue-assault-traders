<center>

<table width="60%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
  <tr>
		{if $avatar != "default_avatar.gif"}
		<td width="64" align="center" valign=top>
			<img src="images/avatars/{$avatar}" border="1">
		</td>
		<td width="5" align="center" valign=middle>
			<img src="images/spacer.gif" width="5">
		</td>
		{/if}
	<td width=65%>
	  <font size=5 color=#ff0000><b>{$shipname}<br>
	  <font size=3>
	  {$classname}</b></font></font><p>
	  <font size=2><b>
	  {$classdescription}
	  <br><br>
	  </b></font>
	</td>
	<td width=35% align=center valign=center><img src={$classimage}></td>
  </tr>
		</table>
	</td>
    
  </tr>

</table>
<br>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr>
	<td align="center" colspan="2">
	<font size=3 color=#ff0000><b>{$l_ship_levels}</b></font>
	<br>
	</td>
  </tr>

  <tr>
	<td align="right">

	<table cellspacing=0 cellpadding=3>
	<tr>
	  <td>
	  <font size=2><b>
	  {$l_hull_normal}&nbsp;<font color=white>({$shipinfo_hull_normal} / {$classinfo_maxhull})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_hull} / {$classinfo_maxhull})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$hull_normal_bars}&nbsp;<br>{$hull_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_engines_normal}&nbsp;<font color=white>({$shipinfo_engines_normal} / {$classinfo_maxengines})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_engines} / {$classinfo_maxengines})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$engines_normal_bars}&nbsp;<br>{$engines_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_power_normal}&nbsp;<font color=white>({$shipinfo_power_normal} / {$classinfo_maxpower})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_power} / {$classinfo_maxpower})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$power_normal_bars}&nbsp;<br>{$power_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_computer_normal}&nbsp;<font color=white>({$shipinfo_computer_normal} / {$classinfo_maxcomputer})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_computer} / {$classinfo_maxcomputer})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$computer_normal_bars}&nbsp;<br>{$computer_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_sensors_normal}&nbsp;<font color=white>({$shipinfo_sensors_normal} / {$classinfo_maxsensors})&nbsp;&nbsp;</font>	  
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_sensors} / {$classinfo_maxsensors})&nbsp;&nbsp;</font>	  
	  </b></font><td valign=bottom>
	  {$sensors_normal_bars}&nbsp;<br>{$sensors_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  &nbsp;<br><font size=2 color=yellow><b>
	  {$l_avg_stats}&nbsp;<font color=white>({$average_stats} / {$average_stats_max})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  &nbsp;<br>{$average_bars}&nbsp;</td>
	  </td>
	</tr>

  </table>
  </td>

  <td align="left">

  <table cellspacing=0 cellpadding=3>
	<tr>
	  <td>
	  <font size=2><b>
	  {$l_armour_normal}&nbsp;<font color=white>({$shipinfo_armour_normal} / {$classinfo_maxarmour})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_armour} / {$classinfo_maxarmour})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$armour_normal_bars}&nbsp;<br>{$armour_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_shields_normal}&nbsp;<font color=white>({$shipinfo_shields_normal} / {$classinfo_maxshields})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_shields} / {$classinfo_maxshields})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$shields_normal_bars}&nbsp;<br>{$shields_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_beams_normal}&nbsp;<font color=white>({$shipinfo_beams_normal} / {$classinfo_maxbeams})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_beams} / {$classinfo_maxbeams})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$beams_normal_bars}&nbsp;<br>{$beams_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_torp_launch_normal}&nbsp;<font color=white>({$shipinfo_torp_launchers_normal} / {$classinfo_maxtorp_launchers})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_torp_launchers} / {$classinfo_maxtorp_launchers})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$torp_launchers_normal_bars}&nbsp;<br>{$torp_launchers_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_cloak_normal}&nbsp;<font color=white>({$shipinfo_cloak_normal} / {$classinfo_maxcloak})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_cloak} / {$classinfo_maxcloak})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$cloak_normal_bars}&nbsp;<br>{$cloak_bars}&nbsp;</td>
	  </td>
	</tr>

	<tr>
	  <td>
	  <font size=2><b>
	  {$l_ecm_normal}&nbsp;<font color=white>({$shipinfo_ecm_normal} / {$classinfo_maxecm})&nbsp;&nbsp;</font>
	  </b></font><br><font size=2><b>
	  {$l_damaged}&nbsp;<font color=white>({$shipinfo_ecm} / {$classinfo_maxecm})&nbsp;&nbsp;</font>
	  </b></font><td valign=bottom>
	  {$ecm_normal_bars}&nbsp;<br>{$ecm_bars}&nbsp;</td>
	  </td>
	</tr>

  </table>
  </td></tr>
		</table>
	</td>
    
  </tr>
  
</table>

<br>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr>
	<td width=33%>
	<font size=3 color=white><b>{$l_holds}</b></font>
	<br>
	</td>
	<td width=33%>
	<font size=3 color=white><b>{$l_arm_weap}</b></font>
	<br></td>
	</td>
	<td width=33%>
	<font size=3 color=white><b>{$l_devices}</b></font>
	<br></td>
	</td>
  </tr>   

  <tr>
	<td valign=top>

	<table border=0 cellspacing=0 cellpadding=3>
	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/credits.png>&nbsp;{$l_credits}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_credits}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		{$l_total_cargo}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$holds_used} / {$holds_max}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/ore.png>&nbsp;{$l_ore}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_ore}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/organics.png>&nbsp;{$l_organics}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_organics}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/goods.png>&nbsp;{$l_goods}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_goods}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/colonists.png>&nbsp;{$l_colonists}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_colonists}
		</b></font>
		</td>
	  </tr>
	  
{if $spy_success_factor}
	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/spy.png>&nbsp;{$l_spy}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$ship_spies}
		</b></font>
		</td>
	  </tr>
{/if}
	  
{if $dig_success_factor}
	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/dig.gif>&nbsp;{$l_dig}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$ship_dig}
		</b></font>
		</td>
	  </tr>
{/if}
		  </table>

  </td><td valign=top>

	<table border=0 cellspacing=0 cellpadding=3>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/energy.png>&nbsp;{$l_energy}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_energy} / {$energy_max}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/tfighter.png>&nbsp;<a href=mines.php>{$l_fighters}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_fighters} / {$ship_fighters_max}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/torp.png>&nbsp;<a href=mines.php>{$l_torps}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_torps} / {$torps_max}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src=templates/{$templatename}images/armour.png>&nbsp;{$l_armourpts}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_armour_pts} / {$armour_pts_max}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src="images/spacer.gif" width="12">&nbsp;{$l_beams} {$l_class}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_beams_class}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src="images/spacer.gif" width="12">&nbsp;{$l_fighters} {$l_class}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_computer_class}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src="images/spacer.gif" width="12">&nbsp;{$l_torps} {$l_class}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_torp_launchers_class}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src="images/spacer.gif" width="12">&nbsp;{$l_shields} {$l_class}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_shields_class}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		&nbsp;<img src="images/spacer.gif" width="12">&nbsp;{$l_armour} {$l_class}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_armour_class}
		</b></font>
		</td>
	  </tr>

	</table>

  <td valign=top>

	<table border=0 cellspacing=0 cellpadding=3>

	  <tr>
		<td>
		<font size=2><b>
		<a href=beacon.php>{$l_beacons}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_beacon}
		</b></font>
		</td>
	  </tr>
<tr>
		<td>
		<font size=2><b>
		<a href=probemenu.php?command=drop>{$l_probe}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$ship_probe}
		</b></font>
		</td>
	  </tr>
	  <tr>
		<td>
		<font size=2><b>
		<a href=warpedit.php>{$l_warpedit}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_warpedit}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		<a href=genesis.php>{$l_genesis}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_genesis}
		</b></font>
		</td>
	  </tr>
	  <tr>
		<td>
		<font size=2><b>
		<a href=sectorgenesis.php>{$l_sectorgenesis}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_sectorgenesis}
		</b></font>
		</td>
	  </tr>
	  <tr>
		<td>
		<font size=2><b>
		{$l_deflect}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_minedeflector}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		<a href=emerwarp.php>{$l_ewd}</a>&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>
		<font color=white><b>
		{$shipinfo_dev_emerwarp}
		</b></font>
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		{$l_escape_pod}&nbsp;&nbsp;&nbsp;
		</b></font> 
		<td>

{if $shipinfo_dev_escapepod == 'Y'}
		<font color=#00ff00><b>
		{$l_installed}
		</b></font>
{else}
		<font color=#ff0000><b>
		{$l_not_installed}
		</b></font>
{/if}
		</td>
	  </tr>

	  <tr>
		<td>
		<font size=2><b>
		{$l_fuel_scoop}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>

{if $shipinfo_dev_fuelscoop == 'Y'}
		<font color=#00FF00><b>
		{$l_installed}
		</b></font>
{else}
		<font color=#FF0000><b>
		{$l_not_installed}
		</b></font>
{/if}
		</td>
	  </tr>
	  <tr>
		<td>
		<font size=2><b>
		{$l_nova}&nbsp;&nbsp;&nbsp;
		</b></font>
		<td>

{if $shipinfo_dev_nova == 'Y'}
		<font color=#00FF00><b>
		{$l_installed}
		</b></font>
{else}
		<font color=#FF0000><b>
		{$l_not_installed}
		</b></font>
{/if}
		</td>
	  </tr>

	</table>
</td></tr>
{if $spycheck}
	<tr><td><BR><a href=spy.php>{$l_clickme}</a> {$l_spy_linkback}<BR></td></tr>
{/if}
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>

</table>
