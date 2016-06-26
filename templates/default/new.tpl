<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
 
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
{if ($account_creation_closed)}
<tr>
	  <td>{$l_new_closed_message}
</td></tr>
{else}
<form action="new2.php" method="post">
<tr>
	  <td>{$l_login_email}</td>
	  <td><input type="text" name="username" size="20" maxlength="40" value=""></td>
	</tr>
	<tr>
	  <td>{$l_new_shipname}</td>
	  <td><input type="text" name="shipname" size="20" maxlength="20" value=""></td>
	</tr>
	<tr>
	  <td>{$l_new_pname}</td>
	  <td><input type="text" name="character" size="20" maxlength="20" value=""></td>
	</tr>

	<tr><td colspan 2>					  <input type="submit" value="{$l_submit}">
  <input type="reset" value="{$l_reset}">
</td></tr>
<tr><td colspan 2>
  <br><br>{$l_new_info}<br>
</td></tr>
</form>
{/if}
		</table>
	</td>

  </tr>

</table>
