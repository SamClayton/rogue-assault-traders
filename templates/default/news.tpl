<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr>
<td height="73" width="27%"><img src={$bnn_head_image} width="312" height="123" alt="News Network"></td>
<td height="73" width="73%" bgcolor="#000000" valign="bottom" align="right">
<p><font size="-1">{$l_news_info}</font></p>
<p>{$l_news_for} {$today}</p>
</td>
</tr>
<tr>
<td height="22" width="27%" bgcolor="#000000">&nbsp;</td>
<td height="22" width="73%" bgcolor="#000000" align="right"><a href="news.php?startdate={$previousday}">
{$l_news_prev}</a> - <a href="news.php?startdate={$nextday}">{$l_news_next}</a></td>
</tr>

{php}
	for($i = 0; $i < $newscount; $i++){
		echo"<tr>";
		echo"<td bgcolor=\"#000000\" align=\"center\">" . $headline[$i] . "</td>";
		echo"<td bgcolor=\"#000000\"><p align=\"justify\">" . $newstext[$i] ."<br></p></td>";
		echo"</tr>";
		echo"<tr>";
		echo"<td bgcolor=\"#000000\" align=\"center\" colspan=\"2\" height=\"3\"><hr></td>";
		echo"</tr>";
	}
{/php}

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
 
  </tr>

</table>
