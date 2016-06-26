<h1>{$title}</h1>

<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
		<td width=18><img src = "templates/{$templatename}images/g-top-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-top-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-top-midright.gif" width="100%" height="20"></td>
		<td width=18><img src = "templates/{$templatename}images/g-top-right.gif"></td>
  </tr>
  <tr>
    <td background="templates/{$templatename}images/g-mid-left.gif">&nbsp;</td>
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
			<TR>

<td align="center">{$l_g3d_wait}</td></tr>
<tr><td align="center">
<img src="spiral2.php?shipsector={$shipsector}&arm={$arm}&distance={$distance}" border="0" alt="{$l_g3d_wait}">
</td></tr>
<tr><td align="center"><br>
<form action="galaxy_map3d.php" method="post" enctype="multipart/form-data">
<select name="arm">
{$armdropdown}
</select><br><br>
<input type="submit" name="view" value="View Arm">
</form>
</td></tr>

	<form action="galaxy_map3d.php" method="post" enctype="multipart/form-data">
	<TR><TD align='center'><br>
	{$l_glxy_select}&nbsp;
	<select name="turns">
	{php}
		echo "	<option value=\"\" " . ($turns == 0 ? "selected" : "") . ">All</option>\n";
		echo "	<option value=1 " . ($turns == 1 ? "selected" : "") . ">1</option>\n";
		echo "	<option value=2 " . ($turns == '2' ? "selected" : "") . ">2</option>\n";
		echo "	<option value=3 " . ($turns == '3' ? "selected" : "") . ">3</option>\n";
		echo "	<option value=4 " . ($turns == '4' ? "selected" : "") . ">4</option>\n";
		echo "	<option value=5 " . ($turns == '5' ? "selected" : "") . ">5</option>\n";
		echo "	<option value=10 " . ($turns == '10' ? "selected" : "") . ">10</option>\n";
		echo "	<option value=25 " . ($turns == '25' ? "selected" : "") . ">25</option>\n";
		echo "	<option value=50 " . ($turns == '50' ? "selected" : "") . ">50</option>\n";
		echo "	<option value=75 " . ($turns == '75' ? "selected" : "") . ">75</option>\n";
		echo "	<option value=100 " . ($turns == '100' ? "selected" : "") . ">100</option>\n";
		echo "	<option value=250 " . ($turns == '250' ? "selected" : "") . ">250</option>\n";
		echo "	<option value=500 " . ($turns == '500' ? "selected" : "") . ">500</option>\n";
		echo "	<option value=750 " . ($turns == '750' ? "selected" : "") . ">750</option>\n";
		echo "	<option value=1000 " . ($turns == '1000' ? "selected" : "") . ">1000</option>\n";
		echo "	<option value=1500 " . ($turns == '1500' ? "selected" : "") . ">1500</option>\n";
		echo "	<option value=2000 " . ($turns == '2000' ? "selected" : "") . ">2000</option>\n";
		echo "	<option value=2500 " . ($turns == '2500' ? "selected" : "") . ">2500</option>\n";
		echo "	<option value=3000 " . ($turns == '3000' ? "selected" : "") . ">3000</option>\n";
		echo "	<option value=3500 " . ($turns == '3500' ? "selected" : "") . ">3500</option>\n";
		echo "	<option value=4000 " . ($turns == '4000' ? "selected" : "") . ">4000</option>\n";
		echo "	<option value=4500 " . ($turns == '4500' ? "selected" : "") . ">4500</option>\n";
		echo "	<option value=5000 " . ($turns == '5000' ? "selected" : "") . ">5000</option>\n";
	{/php}
	</select>
	{$l_glxy_turns}&nbsp;&nbsp;&nbsp;<input type="submit" value="{$l_submit}">
	</TD></tr>
	</form>	

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>
  <tr>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-left.gif"></td>
		<td width=101><img src = "templates/{$templatename}images/g-bottom-midleft.gif"></td>
		<td width="100%"><img src = "templates/{$templatename}images/g-bottom-midright.gif" width="100%" height="12"></td>
		<td width=18><img src = "templates/{$templatename}images/g-bottom-right.gif"></td>
  </tr>
</table>

