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
	{literal}
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	function MakeMax(name, val)
	{
	 if (document.forms[0].elements[name].value != val)
	 {
	  if (val != 0)
	  {
	  document.forms[0].elements[name].value = val;			  
	  }			  
	 }	 
	}

	function Comma(number) {
	number = '' + number;
	if (number.length > 3) {
	var mod = number.length % 3;
	var output = (mod > 0 ? (number.substring(0,mod)) : '');
	for (i=0 ; i < Math.floor(number.length / 3); i++) {
	if ((mod == 0) && (i == 0))
	output += number.substring(mod+ 3 * i, mod + 3 * i + 3);
	else
	output+= ',' + number.substring(mod + 3 * i, mod + 3 * i + 3);
	}
	return (output);
	}
	else return number;
	}

	function changeDelta(desiredvalue,currentvalue)
	{ 
	  Delta=0; DeltaCost=0;
	  Delta = desiredvalue - currentvalue;
	
		while (Delta>0) 
		{
		 DeltaCost=DeltaCost + Math.pow({/literal}{$upgrade_factor}{literal},desiredvalue-Delta); 
		 Delta=Delta-1;
		}
	
	  DeltaCost=DeltaCost * {/literal}{$upgrade_cost}{literal}
	  return DeltaCost;
	}

	function countTotal()
	{
	clean_js()
	var form = document.forms[0];
	var i = form.elements.length;
	max_credits=0;
	while (i > 0)
	  {
	 if (form.elements[i-1].value == '')
	  {
	  form.elements[i-1].value ='0';
	  }
	 i--;
	}
	if (form.overall_upgrade.value > 0){
		if (form.overall_upgrade.value > {/literal}{$java_computer}{literal} && {/literal}{$java_computer}{literal} == {/literal}{$java_computer_normal}{literal}){
			form.computer_upgrade.value=form.overall_upgrade.value;
		}else{
			form.computer_upgrade.value={/literal}{$java_computer}{literal};
		}
		if (form.overall_upgrade.value > {/literal}{$java_sensors}{literal} && {/literal}{$java_sensors}{literal} == {/literal}{$java_sensors_normal}{literal}){
			form.sensors_upgrade.value=form.overall_upgrade.value;
		}else{
			form.sensors_upgrade.value={/literal}{$java_sensors}{literal};
		}
		if (form.overall_upgrade.value > {/literal}{$java_beams}{literal} && {/literal}{$java_beams}{literal} == {/literal}{$java_beams_normal}{literal}){
			form.beams_upgrade.value=form.overall_upgrade.value;
		}else{
			form.beams_upgrade.value={/literal}{$java_beams}{literal};
		}
			
		if (form.overall_upgrade.value > {/literal}{$java_cloak}{literal} && {/literal}{$java_cloak}{literal} == {/literal}{$java_cloak_normal}{literal}){
			form.cloak_upgrade.value=form.overall_upgrade.value;
		}else{
			form.cloak_upgrade.value={/literal}{$java_cloak}{literal};
		}
		if (form.overall_upgrade.value > {/literal}{$java_torps}{literal} && {/literal}{$java_torps}{literal} == {/literal}{$java_torps_normal}{literal}){
			form.torp_launchers_upgrade.value=form.overall_upgrade.value;
		}else{
			form.torp_launchers_upgrade.value={/literal}{$java_torps}{literal};
		}
		if (form.overall_upgrade.value > {/literal}{$java_shields}{literal} && {/literal}{$java_shields}{literal} == {/literal}{$java_shields_normal}{literal}){
			form.shields_upgrade.value=form.overall_upgrade.value;
		}else{
			form.shields_upgrade.value={/literal}{$java_shields}{literal};
		}	
		if (form.overall_upgrade.value > {/literal}{$java_jammer}{literal} && {/literal}{$java_jammer}{literal} == {/literal}{$java_jammer_normal}{literal}){
			form.jammer_upgrade.value=form.overall_upgrade.value;
		}else{
			form.jammer_upgrade.value={/literal}{$java_jammer}{literal};
		}
		
	}
	
	form.max_credits.value =
	Comma(((changeDelta(form.computer_upgrade.value, 0)
	+ changeDelta(form.sensors_upgrade.value, 0)
	+ changeDelta(form.beams_upgrade.value, 0)
	+ changeDelta(form.cloak_upgrade.value, 0)
	+ changeDelta(form.torp_launchers_upgrade.value, 0)
	+ changeDelta(form.shields_upgrade.value, 0)
	+ changeDelta(form.jammer_upgrade.value, 0)) * {/literal}{$planet_credit_multi}{literal}) + {/literal}{$base_credits}{literal})
	;

	form.planet_ratio.value =
	Math.round(({/literal}{$planet_credits}{literal} / (((changeDelta(form.computer_upgrade.value, 0)
	+ changeDelta(form.sensors_upgrade.value, 0)
	+ changeDelta(form.beams_upgrade.value, 0)
	+ changeDelta(form.cloak_upgrade.value, 0)
	+ changeDelta(form.torp_launchers_upgrade.value, 0)
	+ changeDelta(form.shields_upgrade.value, 0)
	+ changeDelta(form.jammer_upgrade.value, 0)) * {/literal}{$planet_credit_multi}{literal}) + {/literal}{$base_credits}{literal})) * 100)
	;
	
	form.total_cost.value =
	changeDelta(form.computer_upgrade.value,{/literal}{$java_computer}{literal})
	+ changeDelta(form.sensors_upgrade.value,{/literal}{$java_sensors}{literal})
	+ changeDelta(form.beams_upgrade.value,{/literal}{$java_beams}{literal})
	+ changeDelta(form.cloak_upgrade.value,{/literal}{$java_cloak}{literal})
	+ changeDelta(form.torp_launchers_upgrade.value,{/literal}{$java_torps}{literal})
	+ changeDelta(form.shields_upgrade.value,{/literal}{$java_shields}{literal})
	+ changeDelta(form.jammer_upgrade.value,{/literal}{$java_jammer}{literal})
	;
	  if (form.total_cost.value > {/literal}{$java_credits}{literal})
	  {
		form.total_cost.value = '{/literal}{$l_no_credits}{literal}';
	  }
	  form.total_cost.length = form.total_cost.value.length;
	
	form.computer_costper.value=Comma(changeDelta(form.computer_upgrade.value,{/literal}{$java_computer}{literal}));
	form.sensors_costper.value=Comma(changeDelta(form.sensors_upgrade.value,{/literal}{$java_sensors}{literal}));
	form.beams_costper.value=Comma(changeDelta(form.beams_upgrade.value,{/literal}{$java_beams}{literal}));
	form.cloak_costper.value=Comma(changeDelta(form.cloak_upgrade.value,{/literal}{$java_cloak}{literal}));
	form.torp_launchers_costper.value=Comma(changeDelta(form.torp_launchers_upgrade.value,{/literal}{$java_torps}{literal}));
	form.shields_costper.value=Comma(changeDelta(form.shields_upgrade.value,{/literal}{$java_shields}{literal}));
	form.jammer_costper.value=Comma(changeDelta(form.jammer_upgrade.value,{/literal}{$java_jammer}{literal}));
	
	
	
	}
	// -->
	</SCRIPT>
	{/literal}

	<FORM ACTION='planet_upgrade.php?planet_id={$planet_id}' METHOD=POST>
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>
	<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 width="100%" align="center">
		<TR BGCOLOR="{$color_header}">
			<TD align="center"><B>{$l_planetary_credits}</B></TD>
			<TD align="center"><B>{$l_max_credits}</B></TD>
			<TD align="center"><B>{$l_overall}</B></TD>
		</TR>
		<TR BGCOLOR="{$color_line1}">
			<TD align="center" >{$planet_creditsout} - <input type=text readonly class='portcosts_sm1' name=planet_ratio VALUE='{$planet_ratio}' tabindex='-1' ONBLUR="countTotal()">%</TD>
			<TD align="center"><input type=text readonly class='portcosts1' name=max_credits VALUE='{$planet_max_creditsout}' tabindex='-1' ONBLUR="countTotal()"></TD>
			<TD align="center">{$planet_overall}</TD>
		</TR>
	</table>
	<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
		<tr>
			<td colspan = "2"><img src = "templates/{$templatename}images/spacer.gif" width = "1" height = "10"></td>
		</tr>
		<TR BGCOLOR="{$color_header}">
		<TD><B>{$l_planetary_defense_levels}</B></TD><TD><B>{$l_cost}</B></TD><TD align="center"><B>{$l_current_level}</B></TD><TD align="center"><B>{$l_upgrade}</B></TD>
		</TR>

	<TR BGCOLOR="{$color_line1}"><TD>{$l_computer}</TD>
	<td><input type=text readonly class='portcosts1' name=computer_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetcomputer}</TD>
	<TD align="center">
	{if $planetcomputer_normal  == $planetcomputer}
		{$planet_computer}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="computer_upgrade" value="{$planetcomputer}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line2}"><TD>{$l_sensors}</TD>
	<td><input type=text readonly class='portcosts2' name=sensors_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetsensors}</TD>
	<TD align="center">
	{if $planetsensors_normal  == $planetsensors}
		{$planet_sensors}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="sensors_upgrade" value="{$planetsensors}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line1}"><TD>{$l_beams}</TD>
	<td><input type=text readonly class='portcosts1' name=beams_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetbeams}</TD>
	<TD align="center">
	{if $planetbeams_normal  == $planetbeams}
		{$planet_beams}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="beams_upgrade" value="{$planetbeams}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line2}"><TD>{$l_torp_launch}</TD>
	<td><input type=text readonly class='portcosts2' name=torp_launchers_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planettorps}</TD>
	<TD align="center">
	{if $planettorps_normal  == $planettorps}
		{$planet_torps}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="torp_launchers_upgrade" value="{$planettorps}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line1}"><TD>{$l_shields}</TD>
	<td><input type=text readonly class='portcosts1' name=shields_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetshields}</TD>
	<TD align="center">
	{if $planetshields_normal  == $planetshields}
		{$planet_shields}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="shields_upgrade" value="{$planetshields}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line2}"><TD>{$l_jammer}</TD>
	<td><input type=text readonly class='portcosts1' name=jammer_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetjammer}</TD>
	<TD align="center">
	{if $planetjammer_normal  == $planetjammer}
		{$planet_jammer}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="jammer_upgrade" value="{$planetjammer}">
	{/if}
	</TD></TR>

	<TR BGCOLOR="{$color_line1}"><TD>{$l_cloak}</TD>
	<td><input type=text readonly class='portcosts2' name=cloak_costper VALUE='0' tabindex='-1' ONBLUR="countTotal()"></td>
	<TD align="center">{$planetcloak}</TD>
	<TD align="center">
	{if $planetcloak_normal  == $planetcloak}
		{$planet_cloak}
	{else}
		{$l_planet_repair}
		<input type="hidden" name="cloak_upgrade" value="{$planetcloak}">
	{/if}
	</TD></TR>
	<tr><TD><INPUT TYPE=SUBMIT VALUE={$l_buy} {$onclick}></TD>
	<TD ALIGN=RIGHT>{$l_totalcost}: <INPUT TYPE=TEXT style="text-align:right" NAME=total_cost SIZE=22 VALUE=0 ONFOCUS="countTotal()" ONBLUR="countTotal()" ONCHANGE="countTotal()" ONCLICK="countTotal()"></td>
	<tr>
</table>
</td></tr>
	</FORM>
<tr><td colspan=4>
	<br>{$l_creds_to_spend}<BR>

	{if $allow_ibank}
		{$l_ifyouneedmore}<BR>
	{/if}
	<BR><a href='planet.php?planet_id={$planet_id}'>{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>
	<A HREF ="bounty.php">{$l_by_placebounty}</A>
</td></tr>

<tr><td colspan=4><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
