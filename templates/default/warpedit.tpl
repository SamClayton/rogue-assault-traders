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
<tr><td colspan=2>
{$linkmessage}
{if $linkcount != 0}
:&nbsp;
	{php}
			for($i = 0; $i < $linkcount; $i++){
			echo "$linklist[$i] ";
		}
	{/php}
{/if}
<br><br></td></tr>

<form action="warpedit.php" method="post">
<input type="hidden" name="confirm" value="add">
<tr><td>{$l_warp_query}</td><td><input type="text" name="target_sector" size="6" maxlength="6" value=""></td></tr>
<tr><td>{$l_warp_oneway}</td><td><input type="checkbox" name="oneway" value="oneway"></td></tr>

<tr><td><input type="submit" value="{$l_submit}" onclick="clean_js()"><input type="reset" value="{$l_reset}"></td><tr>
</form>
<tr><td colspan=2><br><br>{$l_warp_dest}<br><br></td></tr>
<form action="warpedit.php" method="post">
<input type="hidden" name="confirm" value="delete">
<tr><td>{$l_warp_destquery}</td><td><input type="text" name="target_sector" size="6" maxlength="6" value=""></td></tr>
<tr><td>{$l_warp_bothway}?</td><td><input type="checkbox" name="bothway" value="bothway"></td></tr>

<tr><td><input type="submit" value="{$l_submit}" onclick="clean_js()"><input type="reset" value="{$l_reset}"></td><tr>
</form>

<tr><td colspan=2><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
