<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
	<A HREF=planet-report.php>{$l_pr_menulink}</A><br>
	<BR>
	<A HREF=planet-report.php?PRepType=1>{$l_pr_planetstatus}</A><br><br>
	{php}
	for ($i=0; $i < $creditcount; $i++)
	{
		echo $messagea[$i]."<br>";
		if($messageb[$i] != "")
			echo $messageb[$i]."<br>";
		if($message_takea[$i] != ""){
			echo $message_takea[$i];
			if($message_takeb[$i] != "")
				echo " - " . $message_takeb[$i];
			echo "<BR>";
		}
		echo "<BR>";
	}
	{/php}
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
