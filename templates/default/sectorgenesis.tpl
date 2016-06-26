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

<form action="sectorgenesis.php" method="post">
<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td colspan=2>{$l_sgns_shipcredits} {$credits}<br>{$l_sgns_createcost} {$sgcostnumber}</td></tr>
<tr><td>{$l_sgcreate}</td><td><input type="hidden" name="sglink" value="1"></td></tr>
<tr><td><br>
<input type="submit" value="{$l_submit}" onclick="clean_js()"><input type="reset" value="{$l_reset}">
</form>
</td></tr>
{if $sector_type == 1}
	<form action="sectorgenesis.php" method="post">

	<tr><td>{$l_sgcreatens}</td><td><input type="text" name="target_sector" size="6" maxlength="6" value=""><input type="hidden" name="rslink" value="1"></td></tr>
	
<tr><td>
	<input type="submit" value="{$l_submit}" onclick="clean_js()"><input type="reset" value="{$l_reset}">
	</form>
	</td></tr>
{/if}


<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
