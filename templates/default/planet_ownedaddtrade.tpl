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

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
	<b>
	{$l_tdr_createnew}
	{$l_tdr_traderoute}</b><p>{$l_planet_tradehelp}<br>

	{if $isowner == 1}
		{if $needcargo == 1}
			<br><b>{$l_planet_needcargo}</b><BR>
		{else}
			<form action="planet_owned_addtradefinal.php?planet_id={$planet_id}" method=post>
			<table border=0>
			<tr><td align=right><b>{$l_goods}: {$l_tdr_selendpoint}</b>
			&nbsp;&nbsp;<input type=text name=port_id_goods size=20 align=center value=0></td></tr>
			<tr><td align=right><b>{$l_ore}: {$l_tdr_selendpoint}</b>
			&nbsp;&nbsp;<input type=text name=port_id_ore size=20 align=center value=0></td></tr>
			<tr><td align=right><b>{$l_organics}: {$l_tdr_selendpoint}</b>
			&nbsp;&nbsp;<input type=text name=port_id_organics size=20 align=center value=0></td></tr>
			<tr><td align=right><b>{$l_energy}: {$l_tdr_selendpoint}</b>
			&nbsp;&nbsp;<input type=text name=port_id_energy size=20 align=center value=0></td></tr>
			<tr><td align=center><input type=submit value="{$l_tdr_create}" onclick="clean_js()"></td></tr>
			</table>
			</form>
		{/if}
	{else}
		<br><b>{$l_planet_cargonoown}</b><br>
	{/if}
</td></tr>
<tr>
	<td colspan = "2"><a href='planet.php?planet_id={$planet_id}'>{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>
	{if $allow_ibank}
		{$l_ifyouneedplan} <A HREF="igb.php?planet_id={$planet_id}">{$l_igb_term}</A>.<BR><BR>
	{/if}

	<A HREF ="bounty.php">{$l_by_placebounty}</A><p>

</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
