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
			<TR>
				<TD>
{$l_md_consist}<BR>

{if $defenseid == $playerid}
	{$l_md_youcan}:<BR>
	<FORM ACTION=modify-defences.php METHOD=POST>
	{$l_md_retrieve} <INPUT TYPE=TEST NAME=quantity SIZE=10 MAXLENGTH=10 VALUE=0></INPUT> {$defence_type}<BR>
	<input type=hidden name=response value=retrieve>
	<input type=hidden name=defence_id value={$defence_id}>
	<INPUT TYPE=SUBMIT VALUE={$l_submit} ONCLICK="clean_js()"><BR><BR>
	</FORM>
{else}
	{if $fight == 1}
		<FORM ACTION=modify-defences.php METHOD=POST>
		{$l_md_attdef}<BR><INPUT TYPE=SUBMIT VALUE={$l_md_attack}></INPUT><BR>
		<input type=hidden name=response value=fight>
		<input type=hidden name=defence_id value={$defence_id}>
		</FORM>
	{/if}
{/if}

</td></tr>

<tr><td><br><br>{$gotomain}<br><br>
				</td>
			</tr>
		</table>
	</td>
   
  </tr>

</table>
