<H1>{$title}</H1>
{literal}
<script language="javascript" type="text/javascript">
function clean_js()
{
	// Here we cycle through all form values (other than buy, or full), and regexp out all non-numerics. (1,000 = 1000)
	// Then, if its become a null value (type in just a, it would be a blank value. blank is bad.) we set it to zero.
	var form = document.forms[0];
	var i = form.elements.length;
	while (i > 0)
	{
		if ((form.elements[i-1].type == 'text') && (form.elements[i-1].name != ''))
		{
			var tmpval = form.elements[i-1].value.replace(/\D+/g, "");
			if (tmpval != form.elements[i-1].value)
			{
				form.elements[i-1].value = form.elements[i-1].value.replace(/\D+/g, "");
			}
		}
		if (form.elements[i-1].value == '')
		{
			form.elements[i-1].value ='0';
		}
		i--;
	}
}
</script>
{/literal}

<FORM ACTION=planet_owned_production.php?planet_id={$planet_id} METHOD=POST>
  
<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
 
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td colspan="2" align="right">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_computer}</font></td>
        </tr>
        <tr valign="top"> 
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetcomputer_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
          <td>{$computerbar_normal}</td>
        </tr>
        <tr valign="top"> 
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetcomputer}</font></td>
          <td>{$computerbar}</td>
        </tr>
      </table>
    </td>
    <td rowspan="4" align="center" valign="middle"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" align="center">
        <tr> 
          <td align="center"> 
			<img src="{$planettype}" border=0><br>
            <font color="#ff0000" style="font-size:11px; font-weight:bold;">{$planetname}</font><br><br>
          </td>
        </tr>
		<tr>
		<td align="center">
		<font color="#ff0000" style="font-size:10px; font-weight:bold;">{$l_planet_name}</font><br>
		  <font color="#ff0000" style="font-size:10px; font-weight:bold;">{if $allow_genesis_destroy == 1}
			<A onclick="javascript: alert ('alert:{$l_planet_warning}');" HREF='planet.php?planet_id={$planet_id}&destroy=1'>{$l_planet_destroyplanet}</a><br>
			{/if}
			</font>
		</td>
		</tr>
      </table>
    </td>
    <td colspan="3" height="50">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_sensors}</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$sensorbar_normal}</td>
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetsensors_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$sensorbar}</td>
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetsensors}</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td height="50">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_beams}</font></td>
        </tr>
        <tr valign="top"> 
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetbeams_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
          <td>{$beambar_normal}</td>
        </tr>
        <tr valign="top"> 
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetbeams}</font></td>
          <td>{$beambar}</td>
        </tr>
      </table>
    </td>
    <td rowspan="2" width="55" height="100"> 
      <table width="55" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td align="center">
<font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planet_transfer_link}</font>
</td>
        </tr>
      </table>
    </td>
    <td rowspan="2" width="55" height="100"> 
      <table width="55" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td align="center">
		  	{if $planetbased == "Y"}	 
				<font color="#ff0000" style="font-size:11px; font-weight:bold;"><a href="planet_owned_upgrade.php?planet_id={$planet_id}">{$l_planet_upgrade}</a></font><br><br>
				<font color="#ff0000" style="font-size:11px; font-weight:bold;"><a href="planet_owned_repair.php?planet_id={$planet_id}">{$l_planet_repair}</a></font>
			{else}
				&nbsp;
			{/if}
</td>
        </tr>
      </table>
    </td>
    <td colspan="2" height="50" align="right">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_jammer}</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$jammerbar_normal}</td>
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetjammer_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$jammerbar}</td>
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetjammer}</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td height="50">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_torp_launch}</font></td>
        </tr>
        <tr valign="top"> 
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planettorps_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
          <td>{$torpbar_normal}</td>
        </tr>
        <tr valign="top"> 
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planettorps}</font></td>
          <td>{$torpbar}</td>
        </tr>
      </table>
    </td>
    <td colspan="2" height="50" align="right">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_cloak}</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$cloakbar_normal}</td>
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetcloak_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$cloakbar}</td>
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetcloak}</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td colspan="2" align="right">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_armour}</font></td>
        </tr>
        <tr valign="top"> 
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetarmor_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
          <td>{$armorbar_normal}</td>
        </tr>
        <tr valign="top"> 
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetarmor}</font></td>
          <td>{$armorbar}</td>
        </tr>
      </table>
    </td>
    <td colspan="3" height="50">
      <table width="250" height="42" border="0" cellspacing="0" cellpadding="0" background="">
        <tr align="center" valign="top"> 
          <td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_shields}</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$shieldbar_normal}</td>
          <td width="150" align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_normal}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetshields_normal}</font><font color="#ff0000" style="font-size:11px; font-weight:bold;"> - {$l_max} 54</font></td>
        </tr>
        <tr valign="top"> 
          <td align="right">{$shieldbar}</td>
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_damaged}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetshields}</font></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr> 
    <td colspan="6">
&nbsp;
    </td>
  </tr>
  
  <tr> 
    <td colspan="6">
&nbsp;
    </td>
  </tr>
  <tr> 
    <td colspan="6">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
	  <tr>
	  	<td width="40%">
		<table align="center" border="1" cellspacing="0" cellpadding="0" bgcolor="#000000" bordercolorlight="#ff0000" bordercolordark="#ff0000">
		<tr>
			<td colspan="2"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_max_credits}: <font color="#ff0000" style="font-size:11px; font-weight:bold;">{$planet_ratio}%</font> - </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$max_credits}</font></td>
</tr><tr>
<td><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planetary_armourpts}: </font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$planetarmorpts}</font></td>
</tr><tr>
<td><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_colonists}: </font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$colonisttotal}</font>
</td>
		</tr>
		<tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=pfighters VALUE="{$fighterprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_fighters}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$fightertotal}</font></td></tr>
<tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=ptorp VALUE="{$torpprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_torps}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$torptotal}</font></td></tr>
<tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=penergy VALUE="{$energyprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_energy}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$energytotal}</font></td></tr>
		<tr><td>
		<INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=pore VALUE="{$oreprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_ore}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$oretotal}</font>
		</td></tr><tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=pgoods VALUE="{$goodsprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_goods}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$goodstotal}</font></td></tr>
<tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" NAME=porganics VALUE="{$organicsprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_organics}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$organicstotal}</font></td></tr>

<tr><td><INPUT TYPE=TEXT style="color:#ff0000; font-size:10px; font-weight:bold; background-color:black; align:right;" readonly NAME=pcredits VALUE="{$creditprod}" SIZE=3 MAXLENGTH=3><font color="#ff0000" style="font-size:11px; font-weight:bold;">%&nbsp;{$l_credits}:&nbsp;</font></td><td><font color="yellow" style="font-size:11px; font-weight:bold;">{$credittotal}</font></td></tr>
<tr><td align="center"><INPUT TYPE=SUBMIT VALUE={$l_planet_update} ONCLICK="clean_js()"></td> <td>&nbsp;</td></tr>
</table>
</td><td width="60%" valign="middle">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_turns_have} </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$playerturns}</font></td><td><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_dig}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$digtotal}</font></td>
<td><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_spy}: </font><font color="yellow" style="font-size:11px; font-weight:bold;">{$spytotal}</font></td></tr>
</table>
<br>
<table width="100%" border="1" cellspacing="0" cellpadding="3" bgcolor="#000000" bordercolorlight="#ff0000" bordercolordark="#ff0000">
		<tr>
			<td colspan="2">
			<table border="1" cellspacing="0" cellpadding="1" bgcolor="#000000" bordercolorlight="#ff0000" bordercolordark="#ff0000">
				<tr>
					<td align="center"><font color="#ff0000" style="font-size:10px; font-weight:bold;">{$l_planet_land}
			{if $onplanet == 1}
			&nbsp;&nbsp;{$logout_link}
			{/if}
		</font></td><td align="center"><font color="#ff0000" style="font-size:10px; font-weight:bold;">{$l_planet_readlog}</font></td>{if $igbplanet != 0}<td align="center">
			<font color="#ff0000" style="font-size:11px; font-weight:bold;"><A HREF="igb.php?planet_id={$igbplanet}">{$l_igb_term}</A></font>
			</td>{/if}<td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;"><A HREF ="bounty.php">{$l_by_placebounty}</A></font></td>
				</tr>
			</table>
<br>
		  <font color="#ff0000" style="font-size:10px; font-weight:bold;">{$l_planet_bbase}</font><br>
		  <font color="#ff0000" style="font-size:10px; font-weight:bold;">{$l_planet_mteam}</font><br>
		  <font color="#ff0000" style="font-size:10px; font-weight:bold;"> 
			{if $spycleaner != 0}
				<a href="spy.php?command=cleanup_planet&planet_id={$spycleaner}">{$l_clickme}</a> {$l_spy_cleanupplanet}
			{else}
				&nbsp;
			{/if}
			</font><br>
			<font color="#ff0000" style="font-size:10px; font-weight:bold;">{$cashstatus} {$l_planet_tcash}</font>
			</td></tr>
        <tr>
          <td width="80" height="80" align="center">{$cargoimage}</td>
          <td align="center"><font color="#ff0000" style="font-size:11px; font-weight:bold;">{$l_planet_autotrade}</font></td>
        </tr>
		</table>
		
		</td>
	  </tr>
       
      </table>
    </td>
  </tr>
</table>
</td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
</FORM>
