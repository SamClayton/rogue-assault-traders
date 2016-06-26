<br><br>
<form action=create_universe.php method=post>
<input type=hidden name=step value=2.1>
<input type=hidden name=sektors value={$sektors}>
<table border=1 cellspacing=1 cellpadding=5>
<tr>
<td><b>Variable Name</b></td>
<td><b>Variable Value</b></td>
<td><b>Variable Description</b></td>
</tr>
{php}
for($i = 0; $i < $count; $i++){
  echo"<tr>\n";
    echo"<td>\n";
      echo"<b>$db_config_name[$i] :</b>&nbsp;&nbsp;\n";
    echo"</td>\n";
   echo" <td>\n";
      echo"<input type=\"hidden\" name=\"name[$i]\" value=\"$db_config_name[$i]\">\n";
      echo"<input type=\"text\" name=\"value[$i]\" value=\"$db_config_value[$i]\" size=\"40\">\n";
    echo"</td>\n";
    echo"<td>\n";
      echo "<font color=\"#00ff00\"><i><b>$db_config_info[$i]</b></i></font>\n";
    echo"</td>\n";
  echo"</tr>\n";
}
{/php}

<tr><TD ALIGN=center colspan=3><INPUT TYPE=SUBMIT NAME=command VALUE="save">
<input type=hidden name=count value={$count}>
<input type=hidden name=swordfish value={$swordfish}></td></td>
</table>
</form><br><br><hr><br>
<form action=create_universe.php method=post>
<input type=hidden name=step value=3>
<input type=hidden name=sektors value={$sektors}>
<input type=hidden name=swordfish value={$swordfish}>
<INPUT TYPE=SUBMIT NAME=command VALUE="Continue Universe Creation">
</form>
