<H1>{$title}</H1>

{literal}
<style type="text/css">
<!--
.tooltiptitle{COLOR: ##ff0000; TEXT-DECORATION: none; CURSOR: Default; font-family: arial; font-weight: bold; font-size: 8pt}
.tooltipcontent{COLOR: #ff0000; TEXT-DECORATION: none; CURSOR: Default; font-family: arial; font-size: 8pt}

#ToolTip{position:absolute; width: 150px; top: 0px; left: 0px; z-index:8; visibility:hidden;}

.NArial   {font-family: arial; font-size: 10pt}
.NArialL  {font-family: arial; font-size: 12pt}
.NArialS  {font-family: arial; font-size: 8pt}
.NArialW  {COLOR: #ff0000; font-family: arial; font-size: 10pt}

-->
</style>

<script language = "javascript">
<!--
var ie = document.all ? 1 : 0
var ns = document.layers ? 1 : 0

if(ns){doc = "document."; sty = ""}
if(ie){doc = "document.all."; sty = ".style"}

var initialize = 0
var Ex, Ey, topColor, subColor, ContentInfo

if(ie){
	Ex = "event.x"
	Ey = "event.y"

	topColor = "#7D92A9"
	subColor = "#A5B4C4"
}

if(ns){
	Ex = "e.pageX"
	Ey = "e.pageY"
	window.captureEvents(Event.MOUSEMOVE)
	window.onmousemove=overhere

	topColor = "#7D92A9"
	subColor = "#A5B4C4"
}

function MoveToolTip(layerName, FromTop, FromLeft, e){
	if(ie){eval(doc + layerName + sty + ".top = "  + (eval(FromTop) + 55 + document.body.scrollTop))}
	if(ns){eval(doc + layerName + sty + ".top = "  +  eval(FromTop) + 55)}
	eval(doc + layerName + sty + ".left = " + (eval(FromLeft) - 70))
}

function ReplaceContent(layerName){

	if(ie){document.all[layerName].innerHTML = ContentInfo}
	if(ns){
		with(document.layers[layerName].document) 
		{ 
			open(); 
			write(ContentInfo); 
			close(); 
		}
	}
}

function Activate(){initialize=1}
function deActivate(){initialize=0}

function overhere(e){
	if(initialize){
		MoveToolTip("ToolTip", Ey, Ex, e)
		eval(doc + "ToolTip" + sty + ".visibility = 'visible'")
	}
	else{
		MoveToolTip("ToolTip", 0, 0)
		eval(doc + "ToolTip" + sty + ".visibility = 'hidden'")
	}
}

function EnterContent(layerName, TTitle, TContent){
	ContentInfo = '<table border="0" width="150" cellspacing="0" cellpadding="0">'+
	'<tr><td width="100%" bgcolor="#000000">'+

	'<table border="0" width="100%" cellspacing="1" cellpadding="0">'+
	'<tr><td width="100%" bgcolor='+topColor+'>'+

	'<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">'+
	'<tr><td width="100%" align="center">'+

	'<font class="tooltiptitle">'+TTitle+'</font>'+

	'</td></tr>'+
	'</table>'+

	'</td></tr>'+

	'<tr><td width="100%" bgcolor='+subColor+'>'+

	'<table border="0" width="90%" cellpadding="0" cellspacing="1" align="center">'+

	'<tr><td width="100%">'+

	'<font class="tooltipcontent">'+TContent+'</font>'+

	'</td></tr>'+
	'</table>'+

	'</td></tr>'+
	'</table>'+

	'</td></tr>'+
	'</table>';


	ReplaceContent(layerName)
}

//-->
</script>
{/literal}
<div id="ToolTip"></div>

<table width="50%" border="0" cellspacing="0" cellpadding="0" align="center">
 
  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
	<form action="galaxy_local.php" method="post">
	<TR><TD align='right'>
	{$l_glxy_select}&nbsp;
	</td><td align='left'><select name="turns">
	{php}
		
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
	</table>

<br><TABLE border=0 cellpadding=2 cellspacing=1 align=center>

{php}
for($i = 1; $i < $mapsectorcount; $i++){

	$break = $i % $map_width;
	if($i == 0)
		$break = 1;
		
	if($sectorid[$i] >= $startsector and $sectorid[$i] < $endsector){
	   	if ($break == 1)
		{
			echo "<TR><TD>$sectorid[$i]</TD>\n";
		}
	}

	if($sectorid[$i] >= $startsector and $sectorid[$i] < $endsector){
		echo "<TD bgcolor=$sectorzonecolor[$i]><A HREF=move.php?move_method=real&engage=1&destination=$sectorid[$i] onmousemove=\"overhere()\" onMouseover=\"EnterContent('ToolTip','$l_sector: $altsector[$i] - $altport[$i]<br>$altturns[$i]','$l_galacticarm: $galacticarm[$i]<br>";
		$coords = explode("|", $nav_scan_coords[$i]); 
		echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]<br>$notelistnote[$i] $teamnotelistnote[$i]'); Activate();\" onMouseout=\"deActivate(); overhere();\"><img src=" . $sectorimage[$i] . " title=\"$sectortitle[$i]\" border=0></A></TD>\n";

		if ($break == 0)
		{
			echo "<TD>$sectorid[$i]</TD></TR>\n";
		}

	}
}	
{/php}
				</td>
			</tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>

</table>


<br><br>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">

<TR><TD valign='top' rowspan="{$totalzones}">
<img src={$t_devices}> - {$l_device_ports}<br>
<img src={$t_upgrades}> - {$l_upgrade_ports}<br>
<img src={$t_spacedock}> - {$l_spacedock}<br>
<img src={$t_casino}> - {$l_casino}<br>
<img src={$t_ore}> - {$l_ore}<br>
<img src={$t_organics}> - {$l_organics}<br>
<img src={$t_energy}> - {$l_energy}<br>
<img src={$t_goods}> - {$l_goods}<br>
<img src={$t_none}> - {$l_none}<br><br>
<img src={$t_unknown}> - {$l_unknown}<br><br>

</TD><td class="footer" colspan=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$zoneonename}{$l_glxy_nonteamed}&nbsp;&nbsp;&nbsp;&nbsp;</td><td class="footer" bgcolor=000000>&nbsp;&nbsp;&nbsp;</td></tr>
<tr>
{php}
for($i = 1; $i <= $count; $i++){
	echo "<td class=\"footer\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$namezone[$i]."&nbsp;&nbsp;&nbsp;&nbsp;</td><td class=\"footer\" bgcolor=".$namezonecolor[$i].">&nbsp;&nbsp;&nbsp;</td>";
	if($i/3 == floor($i/3))
		echo"</tr><tr>";
}
if($i/3 != floor($i/3))
	echo"</tr>";
{/php}

<tr><td colspan=3><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    <td background="templates/{$templatename}images/g-mid-right.gif">&nbsp;</td>
  </tr>

</table>
