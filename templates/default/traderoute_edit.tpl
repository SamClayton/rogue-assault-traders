<h1>{$title}</h1>

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

<table width="600" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>
				<TD>

{$l_tdr_editinga}

{$l_tdr_traderoute}</b>

{$l_tdr_cursector} {$shipsector}<br>

<form action="traderoute_save.php" method=post>
<table border=0 bgcolor="#000000"><tr>
<td align=right><b>{$l_tdr_selspoint} <br>&nbsp;</b></td>
<tr>
<td align=right>{$l_tdr_port} : </td>
<td><input type=radio name="ptype1" value="port" 
{if $source_type == 'P'}
	 checked
{/if}
></td>
<td>&nbsp;&nbsp;<input type=text name=port_id1 size=20 align=center value="
{if $source_type == 'P'}
	{$editsource_id}
{else}
	{$shipsector}
{/if}
"></td>
</tr><tr>

<td align=right>Personal {$l_tdr_planet} : </td>
<td><input type=radio name="ptype1" value="planet"
{if $source_type == 'L'}
	 checked
{/if}
></td>
<td>&nbsp;&nbsp;<select name=planet_id1>

{if $num_planets == 0}
	<option value=none>{$l_tdr_none}</option>
{else}
	{php}
	for ($i=0; $i < $num_planets; $i++)
	{
		echo "<option ";
		echo $planetselected[$i];
		echo " value=" . $planetid[$i] . ">" . $planetname[$i] . " $l_tdr_insector " . $planetsectorid[$i] . "</option>";
	}
	{/php}
{/if}
</select>
</tr>

<tr>
<td align=right>{$l_team} {$l_tdr_planet} : </td>
<td><input type=radio name="ptype1" value="team_planet"
{if $source_type == 'C'}
	 checked
{/if}
></td>
<td>&nbsp;&nbsp;<select name=team_planet_id1>

{if $num_team_planets == 0}
	<option value=none>{$l_tdr_none}</option>
{else}
	{php}
	for ($i=0; $i < $num_team_planets; $i++)
	{
		echo "<option ";
		echo $planetselectedteam[$i];
		echo " value=" . $planetidteam[$i] . ">" . $planetnameteam[$i] . " $l_tdr_insector " . $planetsectoridteam[$i] . "</option>";
	}
	{/php}
{/if}
</select>
</tr>

<tr><td>&nbsp;
</tr><tr>
<td align=right><b>{$l_tdr_selendpoint} : <br>&nbsp;</b></td>
<tr>
<td align=right>{$l_tdr_port} : </td>
<td><input type=radio name="ptype2" value="port" 
{if $dest_type == 'P'}
	 checked
{/if}
></td>
<td>&nbsp;&nbsp;<input type=text name=port_id2 size=20 align=center
{if $dest_type == 'P'}
	value="{$editdest_id}"
{/if}
></td>
</tr>

<tr>
<td align=right>Personal {$l_tdr_planet} : </td>
<td><input type=radio name="ptype2" value="planet"
{if $dest_type == 'L'}
	 checked
{/if}
></td>
<td>&nbsp;&nbsp;<select name=planet_id2>

{if $num_planets == 0}
	<option value=none>{$l_tdr_none}</option>
{else}
	{php}
	for ($i=0; $i < $num_planets; $i++)
	{
		echo "<option ";
		echo $planetdestselected[$i];
		echo " value=" . $planetid[$i] . ">" . $planetname[$i] . " $l_tdr_insector " . $planetsectorid[$i] . "</option>";
	}
	{/php}
{/if}
</select>
</tr>

<tr>
<td align=right>{$l_team} {$l_tdr_planet} : </td>
<td><input type=radio name="ptype2" value="team_planet"
{if $dest_type == 'C'}
	 checked}
{/if}
></td>
<td>&nbsp;&nbsp;<select name=team_planet_id2>

{if $num_team_planets == 0}
	<option value=none>{$l_tdr_none}</option>
{else}
	{php}
	for ($i=0; $i < $num_team_planets; $i++)
	{
		echo "<option ";
		echo $planetdestselectedteam[$i];
		echo " value=" . $planetidteam[$i] . ">" . $planetnameteam[$i] . " $l_tdr_insector " . $planetsectoridteam[$i] . "</option>";
	}
	{/php}
{/if}
</select>
</tr>

<tr>
<td>&nbsp;
</tr><tr>
<td align=right><b>{$l_tdr_selmovetype} : </b></td>
<td colspan=2 valign=top><input type=radio name="move_type" value="realspace" 
{if $move_type == 'R'}
	 checked
{/if}
>&nbsp;{$l_tdr_realspace}&nbsp;&nbsp;<input type=radio name="move_type" value="warp" 
{if $move_type == 'W'}
	 checked
{/if}
>&nbsp;{$l_tdr_warp}</td>
</tr><tr>
<td align=right><b>{$l_tdr_selcircuit} : </b></td>
<td colspan=2 valign=top><input type=radio name="circuit_type" value="1"
{if $circuit == '1'}
	 checked
{/if}
>&nbsp;{$l_tdr_oneway}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio name="circuit_type" value="2" 
{if $circuit == '2'}
	 checked
{/if}
>&nbsp;{$l_tdr_bothways}</td>
</tr><tr>
<td>&nbsp;
</tr><tr>
<td><td><td align=center>
<input type=hidden name=editing value={$editing}>
<input type=submit value="{$l_tdr_modify}" onclick="clean_js()">

</table>
{$l_tdr_returnmenu}<br>
</form>

</td></tr>
<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
