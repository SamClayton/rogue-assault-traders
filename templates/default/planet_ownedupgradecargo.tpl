<H1>{$title}</H1>

<table border=0 cellspacing=0 cellpadding=2 width="100%" align=center>
<tr><td>
	{if $isowner == 1}
		<b>
		{$l_planet_cargoshipupgrade}</b><p>
		{if $ownhull == 1}
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

			function countTotal()
			{
			clean_js()
			var form = document.forms[0];
			var i = form.elements.length;
			while (i > 0)
			  {
			 if (form.elements[i-1].value == '')
			  {
			  form.elements[i-1].value ='0';
			  }
			 i--;
			}

			form.total_cost.value =
			changeDelta(form.cargoshiphull.value,{/literal}{$java_hull}{literal})
			+ changeDelta(form.cargoshippower.value,{/literal}{$java_power}{literal})
			;

			  if (form.total_cost.value > {/literal}{$java_credits}{literal})
			  {
				form.total_cost.value = '{/literal}{$l_no_credits}{literal}';
			  }else{
				form.total_cost.value =
				Comma(changeDelta(form.cargoshiphull.value,{/literal}{$java_hull}{literal})
			+ changeDelta(form.cargoshippower.value,{/literal}{$java_power}{literal})
				);
			  }

			  form.total_cost.length = form.total_cost.value.length;
			
			}
			// -->
			</SCRIPT>
			{/literal}
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr> 
				<td colspan="6" align="center"><font color="#00ff00"><b>{$l_planet_cargoshipupgradeinfo}&nbsp;&nbsp;</b>({$l_credits}: {$playercredits})</font></td>
			  </tr>
			<form action="planet_owned_upgradecargofinal.php?planet_id={$planet_id}" method=post>
			<TR>
				<TD align="center" width="100">{$l_hull}: <font color="yellow">{$cargohull}</font></TD>
				<TD align="center" width="50">{$cargo_hull}</TD>
				<TD align="center" width="100">{$l_power}: <font color="yellow">{$cargopower}</font></TD>
				<TD align="center" width="50">{$cargo_power}</TD>
				<TD align="center" width="50"><INPUT TYPE=SUBMIT VALUE={$l_buy} ONCLICK="countTotal()"></TD>
				<TD align="center" width="400">{$l_totalcost}: <INPUT TYPE=TEXT style="text-align:right" NAME=total_cost SIZE=22 VALUE=0 ONFOCUS="countTotal()" ONBLUR="countTotal()" ONCHANGE="countTotal()" ONCLICK="countTotal()"></td>
			</TR>
			</form>
			<tr>
<td colspan=6>
		{else}
			{$l_planet_cargoatmax}<BR>
		{/if}
	{else}
		<br><b>{$l_planet_cargonoown}</b><br>
	{/if}
		
	<BR><a href='planet.php?planet_id={$planet_id}'>{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>
	{if $allow_ibank}
		{$l_ifyouneedplan} <A HREF="igb.php?planet_id={$planet_id}">{$l_igb_term}</A>.<BR><BR>
	{/if}

	<A HREF ="bounty.php">{$l_by_placebounty}</A><p>

</td></tr>

<tr><td colspan=6><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
  
</table>
