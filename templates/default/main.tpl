
{literal}
<style type="text/css">
<!--
.tooltiptitle{COLOR: #FF0000; TEXT-DECORATION: none; CURSOR: Default; font-family: arial; font-weight: bold; font-size: 8pt}
.tooltipcontent{COLOR: #ff0000; TEXT-DECORATION: none; CURSOR: Default; font-family: arial; font-size: 8pt}

#ToolTip{position:absolute; width: 150px; top: 0px; left: 0px; z-index:8; visibility:hidden;}

.NArial   {font-family: arial; font-size: 10pt}
.NArialL  {font-family: arial; font-size: 12pt}
.NArialS  {font-family: arial; font-size: 8pt}
.NArialW  {COLOR: #FF0000; font-family: arial; font-size: 10pt}

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

	topColor = "#ff0000"
	subColor = "#ffffff"
}

if(ns){
	Ex = "e.pageX"
	Ey = "e.pageY"
	window.captureEvents(Event.MOUSEMOVE)
	window.onmousemove=overhere

	topColor = "#ff0000"
	subColor = "#ffffff"
}

function MoveToolTip(layerName, FromTop, FromLeft, e){
	if(ie){eval(doc + layerName + sty + ".top = "  + (eval(FromTop) - 110 + document.body.scrollTop))}
	if(ns){eval(doc + layerName + sty + ".top = "  +  eval(FromTop) - 110)}
	eval(doc + layerName + sty + ".left = " + (eval(FromLeft) - 160))
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

{literal}
 <script type="text/javascript" language="JavaScript1.2" src="templates/{/literal}{$templatename}{literal}stm31.js"></script>

<script type="text/javascript" language="JavaScript1.2">
var key = new Array();  // Define key launcher pages here
key['a'] = "traderoute_create.php";
key['b'] = "shoutbox.php";
key['B'] = "beacon.php";
key['c'] = "genesis.php";
key['C'] = "sectorgenesis.php";
key['D'] = "device.php";
key['d'] = "defence-report.php";
key['e'] = "emerwarp.php"; 
key['f'] = "lrscan.php?sector=*";
key['G'] = "galaxy_local.php";
key['g'] = "galaxy_map.php";
key['i'] = "igb.php";
key['L'] = "logout.php";
key['l'] = "log.php";
key['m'] = "readmail.php";
key['M'] = "mines.php";
key['n'] = "news.php";
key['N'] = "command_sectornotes.php";
key['o'] = "options.php";
key['P'] = "probemenu.php"; 
key['p'] = "planet-report.php?PRepType=1"; 
key['r'] = "ranking.php"; 
key['R'] = "report.php"; 
key['s'] = "mailto2.php";
key['t'] = "traderoute_listroutes.php";
key['T'] = "team-defence-report.php";
key['u'] = "galaxy_map3d.php";
key['w'] = "warpedit.php";
key['['] = "dig.php";
key[']'] = "spy.php";
key['.'] = "galaxy_local.php";

var newwindow = new Array();  // Define key launcher pages here
newwindow['a'] = 0;
newwindow['b']= 1;
newwindow['B'] = 0;
newwindow['c'] = 0;
newwindow['C'] = 0;
newwindow['D'] = 0;
newwindow['d'] = 0;
newwindow['e'] = 0;
newwindow['f'] = 0;
newwindow['G'] = 0;
newwindow['g'] = 0;
newwindow['i'] = 0;
newwindow['L'] = 0;
newwindow['l'] = 0;
newwindow['m'] = 0;
newwindow['M'] = 0;
newwindow['n'] = 0;
newwindow['N'] = 0;
newwindow['o'] = 0;
newwindow['P'] = 0;
newwindow['p'] = 0;
newwindow['R'] = 0;
newwindow['r'] = 0;
newwindow['s'] = 0;
newwindow['t'] = 0;
newwindow['T'] = 0;
newwindow['u'] = 0;
newwindow['w'] = 0;
newwindow['['] = 0;
newwindow[']'] = 0;
newwindow['.'] = 1;

function getKey(keyStroke) {
	isNetscape=(document.layers);
	// Cross-browser key capture routine couresty
	// of Randy Bennett (rbennett@thezone.net)
	eventChooser = (isNetscape) ? keyStroke.which : event.keyCode;
	which = String.fromCharCode(eventChooser);
	for (var i in key){ 
		if (which == i){
			if (newwindow[i])
				window.open(key[i],'','');
			else
				window.location = key[i];
		}
	}
}

document.onkeypress = getKey;

</script>
 {/literal}
 
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>

 
    
    

  </tr>
  <tr>
    <td bgcolor="#000000" align="right" width="31" height="20"></td>
    <td background="templates/{$templatename}images/topbar-mid-bg.gif"  align="left" colspan="2" ID="IEshout1"><img src="templates/{$templatename}images/spacer.gif" width="250" height="1"><br><div style="border-style: dotted1none; border-color:#ff0000" id=scroll3 dir=rtl ;overflow:auto>
{literal}
<script language="javascript" type="text/javascript">
 	function OpenSB()
		{
			f2 = open("shoutbox.php","f2","width=700,height=400,scrollbars=yes");
		}
</script>	
<SCRIPT LANGUAGE="JavaScript1.2" TYPE="text/javascript">
<!--
prefix1=' ';

{/literal}
{php}
$stuff="";
$stuff2="";
for($i = 0; $i < $shoutcount; $i++){ 
$stuff2.="\"shoutbox.php\",";
$stuff.="\"".$shoutmessage[$i]."\",";
}
$stuff.="\"End of Shouts\"";
$stuff2.="\"shoutbox.php\"";
{/php}
{literal}
arURL1 = new Array({/literal}{php}echo $stuff2;{/php}{literal});
arTXT1 = new Array({/literal}{php}echo $stuff;{/php}{literal});


document.write('<LAYER ID=shout1><\/LAYER>');
NS4 = (document.layers);
IE4 = (document.all);

FDRblendInt1 = 5; // seconds between flips
FDRmaxLoops1 = 20; // max number of loops (full set of headlines each loop)
FDRendWithFirst1 = true;

FDRfinite1 = (FDRmaxLoops1 > 0);
blendTimer1 = null;

arTopNews1 = [];
for (i1=0;i1<arTXT1.length;i1++)
{
 arTopNews1[arTopNews1.length] = arTXT1[i1];
 arTopNews1[arTopNews1.length] = arURL1[i1];
}
TopPrefix1 = prefix1;

if(NS4)
{
	shout1 = document.shout1;
	shout1.visibility="hide";

	pos11 = document.images['pht1'];
	pos1E1 = document.images['ph1E1'];
	shout1.left = pos11.x;
	shout1.top = pos11.y;
	shout1.clip.width = 350;
	shout1.clip.height = pos1E1.y - shout1.top;
}
else 
{
	document.getElementById('IEshout1').style.pixelHeight = document.getElementById('IEshout1').offsetHeight;
}

function FDRredo1()
{
	if (innerWidth==origWidth1 && innerHeight==origHeight1) return;
	location.reload();
}

function FDRcountLoads1() 
{
	if (NS4)
	{
		origWidth1 = innerWidth;
		origHeight1 = innerHeight;
		window.onresize = FDRredo1;
	}

	TopnewsCount1 = 0;
	TopLoopCount1 = 0;

	FDRdo1();
	blendTimer1 = setInterval("FDRdo1()",FDRblendInt1*1000)
}

function FDRdo1() 
{
	if (FDRfinite1 && TopLoopCount1>=FDRmaxLoops1) 
	{
		FDRend1();
		return;
	}
	FDRfade1();

	if (TopnewsCount1 >= arTopNews1.length) 
	{
		TopnewsCount1 = 0;
		if (FDRfinite1) TopLoopCount1++;
	}
}

function FDRfade1(){
	if(TopLoopCount1 < FDRmaxLoops1) {
		TopnewsStr1 = "";
		for (var i=0;i<1;i++)
		{
			if(TopnewsCount1 < arTopNews1.length) 
			{
				TopnewsStr1 += "<P><A CLASS=headlines TARGET=_new "
							+ "HREF='" + TopPrefix1 + arTopNews1[TopnewsCount1+1] + "'>"
							+ arTopNews1[TopnewsCount1] + "</" + "A><img src='/images/spacer.gif' width=1 height=15></" + "P>"
				TopnewsCount1 += 2;
			}
		}
		if (NS4) 
		{
			shout1.document.write(TopnewsStr1);
			shout1.document.close();
			shout1.visibility="show";
		}
		else 
		{
			document.getElementById('IEshout1').innerHTML = TopnewsStr1;
		}
	}
}

function FDRend1(){
	clearInterval(blendTimer1);
	if (FDRendWithFirst1) 
	{
		TopnewsCount1 = 0;
		TopLoopCount1 = 0;
		FDRfade1();
	}
}

window.onload = FDRcountLoads1;
//-->
</SCRIPT>
{/literal}
</td>
    <td background="templates/{$templatename}images/topbar-mid-bg.gif" ID="IEfad1" align="right" colspan="2"><img src="templates/{$templatename}images/spacer.gif" width="250" height="1"><br><div style="border-style: dotted1none; border-color:#ff0000" id=scroll3 dir=rtl ;overflow:auto>
{literal}
<SCRIPT LANGUAGE="JavaScript1.2" TYPE="text/javascript">
<!--

prefix=' ';

{/literal}
{php}
$stuff="";
$stuff2="";
for($i = 0; $i < $newscount; $i++){ 
$stuff2.="\"news.php\",";
if (strlen($newsmessage[$i])>40){
	$stuff.="\"".substr($newsmessage[$i],0,40)."...\",";
	}else{
	$stuff.="\"".$newsmessage[$i]."\",";
	}
}
$stuff.="\"End of News\"";
$stuff2.="\"news.php\"";
{/php}
{literal}
arURL = new Array({/literal}{php}echo $stuff2;{/php}{literal});
arTXT = new Array({/literal}{php}echo $stuff;{/php}{literal});

document.write('<LAYER ID=fad1><\/LAYER>');
NS4 = (document.layers);
IE4 = (document.all);

FDRblendInt = 5; // seconds between flips
FDRmaxLoops = 20; // max number of loops (full set of headlines each loop)
FDRendWithFirst = true;

FDRfinite = (FDRmaxLoops > 0);
blendTimer = null;

arTopNews = [];
for (i=0;i<arTXT.length;i++)
{
 arTopNews[arTopNews.length] = arTXT[i];
 arTopNews[arTopNews.length] = arURL[i];
}
TopPrefix = prefix;

if(NS4)
{
	fad1 = document.fad1;
	fad1.visibility="hide";

	pos1 = document.images['pht'];
	pos1E = document.images['ph1E'];
	fad1.left = pos1.x;
	fad1.top = pos1.y;
	fad1.clip.width = 300;
	fad1.clip.height = pos1E.y - fad1.top;
}
else 
{
	document.getElementById('IEfad1').style.pixelHeight = document.getElementById('IEfad1').offsetHeight;
}

function FDRredo()
{
	if (innerWidth==origWidth && innerHeight==origHeight) return;
	location.reload();
}

function FDRcountLoads() 
{
	if (NS4)
	{
		origWidth = innerWidth;
		origHeight = innerHeight;
		window.onresize = FDRredo;
	}

	TopnewsCount = 0;
	TopLoopCount = 0;

	FDRdo();
	blendTimer = setInterval("FDRdo()",FDRblendInt*1000)
		if (NS4)
	{
		origWidth1 = innerWidth;
		origHeight1 = innerHeight;
		window.onresize = FDRredo1;
	}

	TopnewsCount1 = 0;
	TopLoopCount1 = 0;

	FDRdo1();
	blendTimer1 = setInterval("FDRdo1()",FDRblendInt1*1000)
}

function FDRdo() 
{
	if (FDRfinite && TopLoopCount>=FDRmaxLoops) 
	{
		FDRend();
		return;
	}
	FDRfade();

	if (TopnewsCount >= arTopNews.length) 
	{
		TopnewsCount = 0;
		if (FDRfinite) TopLoopCount++;
	}
}

function FDRfade(){
	if(TopLoopCount < FDRmaxLoops) {
		TopnewsStr = "";
		for (var i=0;i<1;i++)
		{
			if(TopnewsCount < arTopNews.length) 
			{
				TopnewsStr += "<P><A CLASS=headlines "
							+ "HREF='" + TopPrefix + arTopNews[TopnewsCount+1] + "'>"
							+ arTopNews[TopnewsCount] + "</" + "A></" + "P>"
				TopnewsCount += 2;
			}
		}
		if (NS4) 
		{
			fad1.document.write(TopnewsStr);
			fad1.document.close();
			fad1.visibility="show";
		}
		else 
		{
			document.getElementById('IEfad1').innerHTML = TopnewsStr;
		}
	}
}

function FDRend(){
	clearInterval(blendTimer);
	if (FDRendWithFirst) 
	{
		TopnewsCount = 0;
		TopLoopCount = 0;
		FDRfade();
	}
}

window.onload = FDRcountLoads;
//-->
</SCRIPT>
{/literal}
	</td>

  </tr>
  <tr>
    <td bgcolor="#000000">&nbsp;</td>
    <td bgcolor="#000000" valign="middle" colspan="3">{literal}<script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu5f8f",400,"","templates/{/literal}{$templatename}{literal}images/spacer.gif",0,"","",0,0,250,0,1000,1,0,0,""],this);
stm_bp("p0",[0,4,0,0,0,3,0,7,100,"",-2,"",-2,50,0,0,"#ff0000","#ff0000","",3,0,0,"#ff0000"]);
stm_ai("p0i0",[0,"{/literal}{$l_device_ports}{literal}","","",-1,-1,0,"device.php","_self","","","","",0,0,0,"","",7,7,0,0,1,"#000000",0,"#000000",0,"","",3,3,0,0,"#00ff00","#ff0000","#ff0000","#00ff00","8pt 'Verdana','Arial','sans-serif'","8pt 'Verdana','Arial','sans-serif;'",0,0]);
stm_bp("p1",[1,4,0,0,2,2,0,0,100,"",-2,"",-2,50,1,2,"#ff0000","#ff0000","",3,0,0,"#ff0000"]);

stm_aix("p1i0","p0i0",[0,"{/literal}{$l_navcomp}{literal}","","",-1,-1,0,"navcomp.php","_self","","","","",0,0,0,"","",0,0]);
stm_aix("p1i1","p0i0",[0,"{/literal}{$l_spacebeacon}{literal}","","",-1,-1,0,"beacon.php","_self","","","","",0,0,0,"","",0,0]);
stm_aix("p1i2","p1i0",[0,"{/literal}{$l_spaceprobes}{literal}","","",-1,-1,0,"probemenu.php?command=drop"]);
stm_aix("p1i3","p1i0",[0,"{/literal}{$l_warpeditors}{literal}","","",-1,-1,0,"warpedit.php"]);
stm_aix("p1i4","p1i0",[0,"{/literal}{$l_genesistorps}{literal}","","",-1,-1,0,"genesis.php"]);
stm_aix("p1i5","p1i0",[0,"{/literal}{$l_sgtorps}{literal}","","",-1,-1,0,"sectorgenesis.php"]);
stm_aix("p1i6","p1i0",[0,"{/literal}{$l_minesfighters}{literal}","","",-1,-1,0,"mines.php"]);
stm_aix("p1i7","p1i0",[0,"{/literal}{$l_ewarp}{literal}","","",-1,-1,0,"emerwarp.php"]);
stm_ep();
stm_aix("p0i1","p0i0",[0,"{/literal}{$l_reports}{literal}","","",-1,-1,0,""]);
stm_bpx("p2","p1",[]);
stm_aix("p2i1","p1i0",[0,"{/literal}{$l_shipinfo}{literal}","","",-1,-1,0,"report.php"]);
stm_aix("p2i2","p1i0",[0,"{/literal}{$l_rankings}{literal}","","",-1,-1,0,"ranking.php"]);
stm_aix("p2i3","p1i0",[0,"IGB","","",-1,-1,0,"igb.php"]);
stm_aix("p2i4","p1i0",[0,"{/literal}{$planets}{literal}","","",-1,-1,0,"planet-report.php"]);
stm_bpx("p3","p1",[1,2]);
stm_aix("p3i0","p1i0",[0,"{/literal}{$l_planetstatus}{literal}","","",-1,-1,0,"planet-report.php?PRepType=1"]);
stm_aix("p3i1","p1i0",[0,"{/literal}{$l_planetdefences}{literal}","","",-1,-1,0,"planet-report.php?PRepType=3"]);
stm_aix("p3i2","p1i0",[0,"{/literal}{$l_changeproduction}{literal}","","",-1,-1,0,"planet-report.php?PRepType=2"]);
stm_ep();
{/literal}{if $spy_success_factor != 0}{literal}
stm_aix("p2i5","p1i0",[0,"{/literal}{$l_spy}{literal}","","",-1,-1,0,"spy.php"]);
{/literal}{/if}{literal}
{/literal}{if $dig_success_factor != 0}{literal}
stm_aix("p2i6","p1i0",[0,"{/literal}{$l_dig}{literal}","","",-1,-1,0,"dig.php"]);
{/literal}{/if}{literal}
stm_aix("p2i7","p1i0",[0,"{/literal}{$l_probe}{literal}","","",-1,-1,0,"probemenu.php"]);
stm_aix("p2i8","p1i0",[0,"{/literal}{$l_autotrade}{literal}","","",-1,-1,0,"autotrades.php"]);
stm_aix("p2i9","p1i0",[0,"{/literal}{$l_sector_def}{literal}","","",-1,-1,0,"defence-report.php"]);
stm_aix("p2i10","p1i0",[0,"{/literal}{$l_sectornotes}{literal}","","",-1,-1,0,"command_sectornotes.php"]);
stm_aix("p2i11","p1i0",[0,"{/literal}{$l_log}{literal}","","",-1,-1,0,"log.php"]);
stm_ep();
{/literal}{if $ksm_allowed == true}{literal}
stm_aix("p0i2","p0i1",[0,"{/literal}{$l_maps}{literal}"]);
stm_bpx("p3","p1",[]);
stm_aix("p3i0","p1i0",[0,"{/literal}{$l_map}{literal}","","",-1,-1,0,"galaxy_map.php"]);
stm_aix("p3i1","p1i0",[0,"{/literal}{$l_localmap}{literal}","","",-1,-1,0,"galaxy_local.php"]);
stm_aix("p3i2","p1i0",[0,"{/literal}{$l_3dmap}{literal}","","",-1,-1,0,"galaxy_map3d.php"]);
stm_ep();
{/literal}{/if}{literal}
stm_aix("p0i3","p0i1",[0,"{/literal}{$l_teams}{literal}"]);
stm_bpx("p4","p1",[]);
stm_aix("p4i0","p1i0",[0,"{/literal}{$l_teams}{literal}","","",-1,-1,0,"teams.php"]);
stm_aix("p4i1","p1i0",[0,"{/literal}{$l_teamforum} - New:{$newposts}{literal}","","",-1,-1,0,"team-forum.php?command=showtopics"]);
stm_aix("p4i2","p1i0",[0,"{/literal}{$l_teamships}{literal}","","",-1,-1,0,"team-report.php"]);
stm_aix("p4i3","p1i0",[0,"{/literal}{$l_teamdefences}{literal} ","","",-1,-1,0,"team-defenses.php"]);
stm_aix("p4i4","p1i0",[0,"{/literal}{$l_teams} {$l_sector_def}{literal} ","","",-1,-1,0,"team-defence-report.php"]);
stm_aix("p4i5","p1i0",[0,"{/literal}{$l_teamplanets}{literal}","","",-1,-1,0,"team-planets.php"]);

stm_ep();
stm_aix("p0i4","p0i1",[0,"{/literal}{$l_messages}{literal}"]);
stm_bpx("p5","p1",[]);
stm_aix("p5i0","p1i0",[0,"{/literal}{$l_read_msg}{literal}","","",-1,-1,0,"readmail.php"]);
stm_aix("p5i1","p1i0",[0,"{/literal}{$l_send_msg}{literal}","","",-1,-1,0,"mailto2.php"]);
stm_aix("p5i2","p1i0",[0,"{/literal}{$l_block_msg}{literal}","","",-1,-1,0,"messageblockmanager.php"]);

stm_ep();
stm_aix("p0i5","p0i1",[0,"{/literal}{$l_options}{literal}","","",-1,-1,0,"options.php"]);
stm_bpx("p6","p1",[]);
stm_aix("p6i0","p1i0",[0,"{/literal}{$l_options}{literal}","","",-1,-1,0,"options.php"]);
stm_ai("p6i1",[6,1,"#000000","",-1,-1,0]);
stm_aix("p6i2","p1i0",[0,"{/literal}{$l_ohno}{literal}","","",-1,-1,0,"self-destruct.php"]);

stm_ep();
stm_aix("p0i6","p0i1",[0,"{/literal}{$l_help}{literal}"]);
stm_bpx("p7","p1",[]);
stm_aix("p7i0","p1i0",[0,"Hotkey Help","","",-1,-1,0,""]);
stm_bpx("p3","p1",[1,2]);
stm_aix("p3i0","p0i0",[1,"The following Hotkeys will execute<br> the following commands:<br><br>\r\nr = Ranking<br>\r\na = Add a traderoutes<br>\r\nb = Shout box<br>\r\nB = Beacon<br>\r\nc = Genesis Device<br>\r\nC = Sector Genesis<br>\r\nD = Device Menu<br>\r\nd = Sector Defence Report<br>\r\ne = Emergency Warp<br>\r\nb = Full Long Range Scan<br>\r\ng = Galaxy Map<br>\r\nG = Local Galaxy Map<br>\r\ni = IGB<br>\r\nl = Log<br>\r\nL = Log Out<br>\r\nm = Read Mail<br>\r\nM = Deploy Mines<br>\r\nn = News<br>\r\nN = Sector Notes<br>\r\no = Options<br>\r\np = Planet Report<br>\r\nP = Probe Menu<br>\r\nr = Rankings<br>\r\nR = Ship Report<br>\r\ns = Send Mail<br>\r\nt = List Trade Routes<br>\r\nT = Team Sector Defences<br>\r\nu = 3D Galaxy Map<br>\r\nw = Warp Editor<br>\r\n[ = Dignitary Menu<br>\r\n] = Spy Menu<br>\r\n","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#000000",0,"#00ff00",0,"","",3,3,1,1,"#ff0000","#ff0000"]);
stm_ep();
stm_aix("p7i1","p1i0",[0,"{/literal}{$l_feedback}{literal}","","",-1,-1,0,"feedback.php","_blank"]);
{/literal}{if $link_forums != 0}{literal}
stm_aix("p7i2","p7i0",[0,"{/literal}{$l_forums}{literal}","","",-1,-1,0,"{/literal}{$forum_link}{literal}","_blank"]);
{/literal}{/if}
{literal}
stm_aix("p7i3","p1i0",[0,"{/literal}FAQ{literal}","","",-1,-1,0,"faq/index.php","_blank"]);

stm_aix("p7i4","p1i0",[0,"{/literal}Profiles{literal}","","",-1,-1,0,"http://profiles.aatraders.com","_blank"]);
stm_ep();
stm_aix("p0i7","p1i0",[0,"{/literal}{$l_logout}{literal}","","",-1,-1,0,"logout.php"]);
stm_ep();
stm_em();
//-->
</script>

{/literal}</td>
{php}
function strip_places($itemin){

$places = explode(",", $itemin);
if (count($places) <= 1){
	return $itemin;
}
else
{
	$places[1] = substr($places[1], 0, 2);
	$placecount=count($places);

	switch ($placecount){
		case 2:
			return "$places[0].$places[1] K";
			break;
		case 3:
			return "$places[0].$places[1] M";
			break;	
		case 4:
			return "$places[0].$places[1] B";
			break;	
		case 5:
			return "$places[0].$places[1] T";
			break;
		case 6:
			return "$places[0].$places[1] Qd";
			break;		
		case 7:
			return "$places[0].$places[1] Qn";
			break;
		case 8:
			return "$places[0].$places[1] Sx";
			break;
		case 9:
			return "$places[0].$places[1] Sp";
			break;
		case 10:
			return "$places[0].$places[1] Oc";
			break;
		}		
	
}

}
{/php}

    <td bgcolor="#000000" valign="middle" align="right">{$l_shiptype}:<a href="report.php"><b>{$classname}</b></a></td>
    <td bgcolor="#000000"></td>
	
	
  </tr>
  <tr bgcolor="#000000">
   
    
    
  </tr>
</table>
<table width="100%" cellpadding=0 cellspacing=0 border=0 align=center>
<tr bgcolor="#000000">
<td>&nbsp;</td>
<td colspan="3" align="left">
<table cellpadding="0" border="0" cellspacing="2" align="left">
<tr>
<td rowspan="2" valign="top">{$l_cargo}:</td>
<td nowrap align='left'>&nbsp;<img src=templates/{$templatename}images/tfighter.png>&nbsp;<a href=mines.php>{$l_fighters}</a></td>
<td nowrap align='left'>&nbsp;<img src=templates/{$templatename}images/torp.png>&nbsp;<a href=mines.php>{$l_torps}</a>&nbsp;</td>
<td nowrap align='left'>&nbsp;<img src=templates/{$templatename}images/armour.png>&nbsp;{$l_armourpts}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_ore}" src="templates/{$templatename}images/ore.png">&nbsp;{$l_ore}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_organics}" src="templates/{$templatename}images/organics.png">&nbsp;{$l_organics}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_goods}" src="templates/{$templatename}images/goods.png">&nbsp;{$l_goods}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_energy}" src="templates/{$templatename}images/energy.png">&nbsp;{$l_energy}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_colonists}" src="templates/{$templatename}images/colonists.png">&nbsp;{$l_colonists}&nbsp;</td>
<td nowrap align='left'>&nbsp;<img height=12 width=12 alt="{$l_credits}" src="templates/{$templatename}images/credits.png">&nbsp;{$l_credits} &nbsp;</td>
</tr>
<tr>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_fighters);{/php} / {php}echo strip_places($ship_fighters_max);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_torps);{/php} / {php}echo strip_places($torps_max);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_armour_pts);{/php} / {php}echo strip_places($armour_pts_max);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_ore);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_organics);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_goods);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_energy);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($shipinfo_colonists);{/php}&nbsp;</span></td>
<td nowrap align='right'><span class=mnu>&nbsp;{php}echo strip_places($playerinfo_credits);{/php}&nbsp;</span></td>
</tr>
</table>
</td>
<td>&nbsp;</td>
</tr>

</table>

<table width="100%" cellpadding=0 cellspacing=0 border=0 align=center>
<tr>
<td>&nbsp;</td>
<td>
<font color="{$general_text_color}" size=2 face="arial">&nbsp;{if $sg_sector}
SG&nbsp;
{/if}{$l_sector}: </font><font color="{$general_highlight_color}"><b>{$sector}</b></font><br><span class=mnu>{$ship_coordinates}</span>
</td><td align=center>
<font color="{$general_highlight_color}" size="2" face="arial"><b>{$beacon}</b></font>
</td><td align=right>
<a href="zoneinfo.php?zone={$zoneid}"><b><font size=2 face="arial">{$zonename}</font></b></a>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>
<table width="100%" border=0 align=center cellpadding=0 cellspacing=0>

<tr>
<td valign=top>

<table border="0" cellpadding="0" cellspacing="0" align="left"><tr valign="top">
<td>
<tr><td>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
  
    
   
  </tr>
  <tr>
   
    <td bgcolor="#000000"><img src="templates/{$templatename}images/spacer.gif" width="143" height="21"></td>
   
  </tr>
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP>
{if $avatar != "default_avatar.gif"}
<p align="center"><img src="images/avatars/{$avatar}"></p>
		{/if}
</td></tr>
<tr><td class=normal>{$l_rank}: <img src="templates/{$templatename}images/rank/{$insignia}"></td></tr>
<tr><td class=normal>{$l_name}: <span class=mnu>{$player_name}</font></span></td></tr>
<tr><td class=normal>{$l_ship} {$l_name}:<span class=mnu><a href="report.php">{$shipname}</a></span></td></tr>
<tr><td class=normal>{$l_shiptype}:<span class=mnu>{$classname}</span></td></tr>
<tr><td class=normal>{$l_turns_have}<span class=mnu>{$turns}</span></td></tr>
<tr><td class=normal>{$l_turns_used}<span class=mnu>{$turnsused}</span></td></tr>
<tr><td class=normal>{$l_score}<span class=mnu>{$score}</span></td></tr>
</table>
</td>
    
  </tr>
  <tr>
   
    
   
  </tr>
</table>
</td></tr>
{if $newcommands == 0}
<tr><td><br>

<table  border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
   
   
  
  </tr>
  <tr>
   

 
  </tr>
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><TR><TD NOWRAP>
<div class=mnu>

{php}
	for($i = 0; $i < $newcommands; $i++){
		echo $newcommandfull[$i]."<br>";
	}
{/php}
</div>
</td></tr>

</table>
	</td>
   
  </tr>
  <tr>
    
  
   
  </tr>
</table>
</td></tr><tr><td><br>
{/if}
<tr><td><br>
				
<table width="195" border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>
   
   
   
  </tr>
  <tr>
    

  
  </tr>
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP>

{if $num_traderoutes == 0}
<TR><TD NOWRAP>
<div class=mnu><center><div class=dis>&nbsp;{$l_none} &nbsp;</div></center><br>
</div>
</td></tr>
{elseif $num_traderoutes == 1}
{php}
echo "<tr><td class=\"nav_title_12\">&nbsp;<a class=mnu href=traderoute_engage.php?engage=" . $traderoute_links[0] . ">" . $traderoute_display[0] . "</a><br>&nbsp;</td><tr>";
{/php}
{else}
{php}
	echo "<tr><td class=\"nav_title_12\" align=center>\n";
	echo "<form name=\"traderoutes\"><select name=\"menu\" onChange=\"location=document.traderoutes.menu.options[document.traderoutes.menu.selectedIndex].value;\" value=\"GO\" class=\"rsform\"><option value=\"\">Select Traderoute</option>\n";
	for($i = 0; $i < count($traderoute_links); $i++){
		echo "<option value=\"traderoute_engage.php?engage=" . $traderoute_links[$i] . "\">$traderoute_display[$i]</option>\n";
	}
	echo "</select></form>";
	echo "</td></tr>\n";
{/php}

{/if}
<tr><td nowrap align="center">
<div class=mnu>
[<a class=mnu href=traderoute_create.php>{$l_add}</a>]&nbsp;&nbsp;<a class=mnu href=traderoute_listroutes.php>{$l_trade_control}</a>&nbsp;<br>

</div></td></tr></table>
</td>
  
  </tr>
  
</table>
</tr></td>

<tr><td><br><table  border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
   
    
   
  </tr>
  <tr>

    
   
  </tr>
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><tr></tr>
	<form method="post" action="shoutbox3.php">
	<input type="Hidden" name="" value="1"><td NOWRAP class="shoutform">
	<textarea class="shoutform" wrap cols="26" rows="3">{$quickshout}</textarea><br>
	<input type="Text" name="sbt"  class="shoutform" size="20" maxlength="50" onfocus="document.onkeypress=null" ONBLUR="document.onkeypress = getKey;" ><input type="submit" name="go" value="Go" class="shoutform"><br>Public?&nbsp;
{if $team_id > 0}
	<INPUT TYPE=CHECKBOX NAME=SBPB class="shoutform" >
{else}
	<INPUT TYPE=CHECKBOX NAME=SBPB class="shoutform" checked>
{/if}
</td></form></tr></table>
	</td>
   
  </tr>
  <tr>
    
    
 
  </tr>
</table>
</td></tr>
</table>
</td>

<td valign=top align="center">
&nbsp;<br>

<center><font size=3 face="arial" color="{$general_highlight_color}"><b>{$l_tradingport}:</b></font></center>
<table border=0 width="100%" align="center">
<tr align="center"><td>
<a href=port.php><img src="{$portgraphic}" border="0" alt=""><br>{$portname}</a>
</td>
	{if $shipyard != ""}
		<td><a href=shipyard.php><img src="{$shipyardgraphic}" border="0" alt=""><br>{$shipyard}</a></td>
	{/if}
</tr>
</table>

<center><b><font size=3 face="arial" color="{$general_highlight_color}">{$l_planet_in_sec} {$sector}:</font></b></center>
<table border=0 width="100%" align="center">
<tr align="center">

{php}
	if($countplanet != 0){
		for($i = 0; $i < count($planetid); $i++){
			echo "<td align=center valign=top>";
			echo "<A HREF=planet.php?planet_id=" . $planetid[$i] . ">";
			echo "<img src=\"$planetimg[$i]\" border=0></a><BR><font size=2 color=\"". $general_highlight_color ."\" face=\"arial\">";
			echo $planetname[$i];
			echo "<br>($planetowner[$i])";
			echo "</font></td>";
		}
	}else{
		echo "<td valign=top><font color=\"". $general_highlight_color ."\" size=2>$l_none</font></td>";
	}
{/php}

</tr>
</table>

<center><b><font size=3 face="arial" color="{$general_highlight_color}"><br>{$l_ships_in_sec} {$sector}:</font><br></b></center>
<table border=0 width="100%">
<tr align="center">
{php}
	if($insector0 != 'sector0'){
		if($playercount != 0){
			$count = 0;
			for($i = 0; $i < $playercount; $i++){
   				if($shipprobe[$i] == "ship"){
					echo "<td align=center valign=top>";
					echo "<a href=ship.php?player_id=" . $player_id[$i] . "&ship_id=" . $ship_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR><font size=2 color=\"". $general_highlight_color ."\" face=\"arial\">";
					echo $shipnames[$i];
					echo "<br>($playername[$i])";
					if($teamname[$i] != "")
						echo "&nbsp;(<font color=#ff0000>$teamname[$i]</font>)";
					echo "</font></td>";
				}
   				if($shipprobe[$i] == "probe"){
					echo "<td align=center valign=top>";
					echo "<a href=showprobe.php?probe_id=" . $player_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR><font size=2 color=\"". $general_highlight_color ."\" face=\"arial\">";
					if($shipnames[$i] != "")
						echo $shipnames[$i];
					echo "<br>($playername[$i])";
					if($teamname[$i] != "")
						echo "&nbsp;(<font color=#ff0000>$teamname[$i]</font>)";
					echo "</font></td>";
				}
   				if($shipprobe[$i] == "debris"){
					echo "<td align=center valign=top>";
					echo "<a href=showdebris.php?debris_id=" . $player_id[$i] . ">";
					echo "<img src=\"$shipimage[$i]\" border=0></a><BR><font size=2 color=\"". $general_highlight_color ."\" face=\"arial\">";
					echo "<br>($playername[$i])";
					echo "</font></td>";
				}
				$count++;
				if($count % 5 == 5)
					echo "</tr></table><table border=0 width=\"100%\"><tr>";
			}
		}else{
			echo "<td align=center>";
			echo "<font size=2 color=\"". $general_highlight_color ."\">$l_none</font>";
			echo "</td>";
		}
	}else{
		echo "<td valign=top align=center><font size=2 color=\"". $general_highlight_color ."\"><b>$l_sector_0</b></font></td>";
	}
{/php}
</tr>
</table>

{if $sectorzero != 1}
<table border=0 width="100%">
<tr><td>
<center><b><font size=3 face='arial' color='{$general_highlight_color}'>
<br><br>{$l_lss}:</font><br></b>
<font size=2 color="{$general_highlight_color}">{$lss_info}</font>
<br></center>
</td>
</tr>
</table>
{/if}

<center><b><font size=3 face="arial" color="{$general_highlight_color}"><br><br>{$l_sector_def}:</font><br></b></center>
<table border=0 width="100%"><tr>
{php}
	$count = 0;
	for($i = 0; $i < $defensecount; $i++){
		if($defensetype[$i] == "F"){
			if($count == 0){
				echo "<td align=center valign=top><img src=templates/" . $templatename . "images/fighters.gif><br>";
			}
			echo "<font class=normal>";
			echo "<a class=mnu href=modify-defences.php?defence_id=" . $defenseid[$i] . ">";
			echo $defplayername[$i];
			echo "</a><br>";
			echo " (<font color=yellow>".strip_places($defenseqty[$i])."</font> <font color=#ff0000>$defensemode[$i]</font>)";
			echo "</font><br>";
			$count++;
		}
	}
	if($count != 0)
		echo "</td>";
{/php}
{php}
	$count = 0;
	for($i = 0; $i < $defensecount; $i++){
		if($defensetype[$i] == "M"){
			if($count == 0){
				echo "<td align=center valign=top><img src=templates/" . $templatename . "images/mines.gif><br>";
			}
			echo " <font class=normal>";
			echo "<a class=mnu href=modify-defences.php?defence_id=" . $defenseid[$i] . ">";
			echo $defplayername[$i];
			echo "</a><br>";
			echo " (<font color=yellow>".strip_places($defenseqty[$i])."</font> <font color=#ff0000>$defensemode[$i]</font>)";
			echo "</font><br>";
			$count++;
		}
	}
	if($count != 0)
		echo "</td>";

{/php}


</tr></table>
<td valign=top>
<br>
				
				
<table  border="0" cellspacing="0" cellpadding="0" align="right">


<tr><td><table width="195" border="0" cellspacing="0" cellpadding="0" align="right">
  <tr>

   
    
  </tr>
  <tr>
    
 
    
  </tr>
  <tr>
  
    <td bgcolor="#000000" valign="top" align="center"><table cellspacing = "0" cellpadding = "0" border = "0"><TR align="center"><TD NOWRAP><div id="ToolTip"></div>


						<table border="0" cellspacing="0" cellpadding="0" align="center"  style="border: thin inset #ff0000;"><tr><td><TABLE border=0 cellpadding=2 cellspacing=1 align=center>
<tr>
{if $altsector[23] != ""}
		<TD bgcolor={$sectorzonecolor[23]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[23]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[23]} - {$altport[23]}<br>{$altturns[23]}','{$l_galacticarm}: {$galacticarm[23]}<br><br>{php} $coords = explode("|", $nav_scan_coords[23]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[23]}" title="{$sectortitle[23]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[24] != ""}
		<TD bgcolor={$sectorzonecolor[24]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[24]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[24]} - {$altport[24]}<br>{$altturns[24]}','{$l_galacticarm}: {$galacticarm[24]}<br><br>{php} $coords = explode("|", $nav_scan_coords[24]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[24]}" title="{$sectortitle[24]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[9] != ""}
		<TD bgcolor={$sectorzonecolor[9]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[9]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[9]} - {$altport[9]}<br>{$altturns[9]}','{$l_galacticarm}: {$galacticarm[9]}<br><br>{php} $coords = explode("|", $nav_scan_coords[9]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[9]}" title="{$sectortitle[9]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[10] != ""}
		<TD bgcolor={$sectorzonecolor[10]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[10]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[10]} - {$altport[10]}<br>{$altturns[10]}','{$l_galacticarm}: {$galacticarm[10]}<br><br>{php} $coords = explode("|", $nav_scan_coords[10]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[10]}" title="{$sectortitle[10]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[11] != ""}
		<TD bgcolor={$sectorzonecolor[11]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[11]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[11]} - {$altport[11]}<br>{$altturns[11]}','{$l_galacticarm}: {$galacticarm[11]}<br><br>{php} $coords = explode("|", $nav_scan_coords[11]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[11]}" title="{$sectortitle[11]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}
</tr>
<tr>
	{if $altsector[22] != ""}
		<TD bgcolor={$sectorzonecolor[22]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[22]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[22]} - {$altport[22]}<br>{$altturns[22]}','{$l_galacticarm}: {$galacticarm[22]}<br><br>{php} $coords = explode("|", $nav_scan_coords[22]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[22]}" title="{$sectortitle[22]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[8] != ""}
		<TD bgcolor={$sectorzonecolor[8]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[8]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[8]} - {$altport[8]}<br>{$altturns[8]}','{$l_galacticarm}: {$galacticarm[8]}<br><br>{php} $coords = explode("|", $nav_scan_coords[8]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[8]}" title="{$sectortitle[8]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[1] != ""}
		<TD bgcolor={$sectorzonecolor[1]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[1]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[1]} - {$altport[1]}<br>{$altturns[1]}','{$l_galacticarm}: {$galacticarm[1]}<br><br>{php} $coords = explode("|", $nav_scan_coords[1]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[1]}" title="{$sectortitle[1]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[2] != ""}
		<TD bgcolor={$sectorzonecolor[2]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[2]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[2]} - {$altport[2]}<br>{$altturns[2]}','{$l_galacticarm}: {$galacticarm[2]}<br><br>{php} $coords = explode("|", $nav_scan_coords[2]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[2]}" title="{$sectortitle[2]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[12] != ""}
		<TD bgcolor={$sectorzonecolor[12]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[12]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[12]} - {$altport[12]}<br>{$altturns[12]}','{$l_galacticarm}: {$galacticarm[12]}<br><br>{php} $coords = explode("|", $nav_scan_coords[12]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[12]}" title="{$sectortitle[12]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}
</tr>
<tr>
	{if $altsector[21] != ""}
		<TD bgcolor={$sectorzonecolor[21]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[21]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[21]} - {$altport[21]}<br>{$altturns[21]}','{$l_galacticarm}: {$galacticarm[21]}<br><br>{php} $coords = explode("|", $nav_scan_coords[21]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[21]}" title="{$sectortitle[21]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[7] != ""}
		<TD bgcolor={$sectorzonecolor[7]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[7]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[7]} - {$altport[7]}<br>{$altturns[7]}','{$l_galacticarm}: {$galacticarm[7]}<br><br>{php} $coords = explode("|", $nav_scan_coords[7]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[7]}" title="{$sectortitle[7]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	<td border=1 valign="middle" align="center"><A HREF="#" onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$sector}','{$l_galacticarm}: {$ship_galacticarm}<br><br>{php} $coords = explode("|", $ship_coordinates); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="templates/{$templatename}images/yourhere.gif" border="0"></a></td>
	{if $altsector[3] != ""}
		<TD bgcolor={$sectorzonecolor[3]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[3]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[3]} - {$altport[3]}<br>{$altturns[3]}','{$l_galacticarm}: {$galacticarm[3]}<br><br>{php} $coords = explode("|", $nav_scan_coords[3]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[3]}" title="{$sectortitle[3]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[13] != ""}
		<TD bgcolor={$sectorzonecolor[13]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[13]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[13]} - {$altport[13]}<br>{$altturns[13]}','{$l_galacticarm}: {$galacticarm[13]}<br><br>{php} $coords = explode("|", $nav_scan_coords[13]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[13]}" title="{$sectortitle[13]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}
</tr>
<tr>
	{if $altsector[20] != ""}
		<TD bgcolor={$sectorzonecolor[20]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[20]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[20]} - {$altport[20]}<br>{$altturns[20]}','{$l_galacticarm}: {$galacticarm[20]}<br><br>{php} $coords = explode("|", $nav_scan_coords[20]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[20]}" title="{$sectortitle[20]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[6] != ""}
		<TD bgcolor={$sectorzonecolor[6]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[6]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[6]} - {$altport[6]}<br>{$altturns[6]}','{$l_galacticarm}: {$galacticarm[6]}<br><br>{php} $coords = explode("|", $nav_scan_coords[6]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[6]}" title="{$sectortitle[6]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[5] != ""}
		<TD bgcolor={$sectorzonecolor[5]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[5]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[5]} - {$altport[5]}<br>{$altturns[5]}','{$l_galacticarm}: {$galacticarm[5]}<br><br>{php} $coords = explode("|", $nav_scan_coords[5]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[5]}" title="{$sectortitle[5]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[4] != ""}
		<TD bgcolor={$sectorzonecolor[4]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[4]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[4]} - {$altport[4]}<br>{$altturns[4]}','{$l_galacticarm}: {$galacticarm[4]}<br><br>{php} $coords = explode("|", $nav_scan_coords[5]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[4]}" title="{$sectortitle[4]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[14] != ""}
		<TD bgcolor={$sectorzonecolor[14]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[14]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[14]} - {$altport[14]}<br>{$altturns[14]}','{$l_galacticarm}: {$galacticarm[14]}<br><br>{php} $coords = explode("|", $nav_scan_coords[14]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[14]}" title="{$sectortitle[14]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}
</tr>
<tr>
	{if $altsector[19] != ""}
		<TD bgcolor={$sectorzonecolor[19]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[19]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[19]} - {$altport[19]}<br>{$altturns[19]}','{$l_galacticarm}: {$galacticarm[19]}<br><br>{php} $coords = explode("|", $nav_scan_coords[19]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[19]}" title="{$sectortitle[19]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[18] != ""}
		<TD bgcolor={$sectorzonecolor[18]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[18]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[18]} - {$altport[18]}<br>{$altturns[18]}','{$l_galacticarm}: {$galacticarm[18]}<br><br>{php} $coords = explode("|", $nav_scan_coords[18]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[18]}" title="{$sectortitle[18]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[17] != ""}
		<TD bgcolor={$sectorzonecolor[17]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[17]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[17]} - {$altport[17]}<br>{$altturns[17]}','{$l_galacticarm}: {$galacticarm[17]}<br><br>{php} $coords = explode("|", $nav_scan_coords[17]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[17]}" title="{$sectortitle[17]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[16] != ""}
		<TD bgcolor={$sectorzonecolor[16]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[16]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[16]} - {$altport[16]}<br>{$altturns[16]}','{$l_galacticarm}: {$galacticarm[16]}<br><br>{php} $coords = explode("|", $nav_scan_coords[16]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[16]}" title="{$sectortitle[16]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

	{if $altsector[15] != ""}
		<TD bgcolor={$sectorzonecolor[15]}><A HREF=move.php?move_method=real&engage=1&destination={$altsector[15]} onmousemove="overhere()" onMouseover="EnterContent('ToolTip','{$l_sector}: {$altsector[15]} - {$altport[15]}<br>{$altturns[15]}','{$l_galacticarm}: {$galacticarm[15]}<br><br>{php} $coords = explode("|", $nav_scan_coords[15]); echo "X: $coords[0]<br>Y: $coords[1]<br>Z: $coords[2]"{/php}'); Activate();" onMouseout="deActivate(); overhere();"><img src="{$sectorimage[15]}" title="{$sectortitle[15]}" border=0 width = "12" height = "12"></A></TD>
	{else}
		<TD bgcolor=Black><img src="templates/{$templatename}images/spacer.gif"  border=0 width = "12" height = "12"></td>
	{/if}

</tr>
</table></td></tr></table><br></td></tr><TR align="center"><TD NOWRAP><div class=mnu align=center>

{if ($shipinfo_sector_id -1) >= 1}
&nbsp;<a class="mnu" href="move.php?move_method=real&engage=1&destination={$rslink_sector_back}">{$rslink_sector_back} ({$rslink_sector_back_dist})&lt;=</a>

{/if}

{if ($shipinfo_sector_id +1) <= $sector_max}
&nbsp;<a class="mnu" href="move.php?move_method=real&engage=1&destination={$rslink_sector_forward}">=&gt;{$rslink_sector_forward} ({$rslink_sector_forward_dist})</a>&nbsp;
<br>
{/if}
<br>
</div></td></tr><tr><td nowrap=""><div class=mnu>
<TABLE BORDER=0 CELLPADDING=1 CELLSPACING=0 BGCOLOR="#000000" >
<form name="lastsector"><tr><td class="nav_title_12" align=center>
<select name="menu" onChange="location=document.lastsector.menu.options[document.lastsector.menu.selectedIndex].value;" value="GO" class="rsform"><option value="">RS to Last Sector</option>
{if $lastsectors[0] != ""}
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[0]}">{$lastsectors[0]}({$lastsectorsdist[0]})</option>
{/if}
{if $lastsectors[1] != ""}
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[1]}">{$lastsectors[1]}({$lastsectorsdist[1]})</option>
{/if}
{if $lastsectors[2] != ""}
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[2]}">{$lastsectors[2]}({$lastsectorsdist[2]})</option>
{/if}
{if $lastsectors[3] != ""}
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[3]}">{$lastsectors[3]}({$lastsectorsdist[3]})</option>
{/if}
{if $lastsectors[4] != ""}
<option value="move.php?move_method=real&engage=1&destination={$lastsectors[4]}">{$lastsectors[4]}({$lastsectorsdist[4]})</option>
{/if}
</select></form></td></tr>

{php}
	echo "<form name=\"presets\"><tr><td class=\"nav_title_12\" align=center>\n";
	echo "<select  name=\"menu\" onChange=\"location=document.presets.menu.options[document.presets.menu.selectedIndex].value;\" value=\"GO\" class=\"rsform\"><option value=\"\">RS to Sector</option>\n";
	for($i = 0; $i < count($preset_display); $i++){
		echo "<option value=\"move.php?move_method=real&engage=1&amp;destination=$preset_display[$i]\">$preset_display[$i] - $preset_info[$i] ($preset_dist[$i])</option>\n";
	}
	echo "</select></td></tr>\n";
	
{/php}

<tr><td class="nav_title_12" align=center>&nbsp;<a class=dis href="preset.php?name=set">[{$l_set}]</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a class=dis href="preset.php?name=add">[{$l_add}]</a>&nbsp;</td></tr></form>
<form method="post" action="move.php"><input type="hidden" name="move_method" value="real"><tr><td class="nav_title_12" align=center>

<input type="text" name="destination" class="rsform" maxlength="10" size="8" onfocus="document.onkeypress=null" ONBLUR="document.onkeypress = getKey;" ><br>
<input type="submit" name="explore" value="&nbsp;?&nbsp;" class="rsform">
<input type="submit" name="go" value="Go" class="rsform">
</td></tr></form>
</table></td></tr></table></td>
  
  </tr>
  <tr>
 
   
    
  </tr>
</table>
</td></tr>


<tr><td><br>
<table  border="0" cellspacing="0" cellpadding="0" align="left">
  <tr>
    

    
  </tr>
  <tr>
    
   
    
  </tr>
  <tr>
   
    <td bgcolor="#000000" valign="top" align="center"><table cellpadding="0" align="left" cellspacing="0"><tr><td NOWRAP>
<div class=mnu>
{php}
	if(count($links) == 0)
		echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<b>$linklist<b>&nbsp;</td></tr>\n";

	for($i = 0; $i < count($links); $i++){
		echo "<tr><td width=100 class=\"nav_title_12\">&nbsp;<a class=\"mnu\" href=\"move.php?move_method=warp&sector=$links[$i]\">=&gt;&nbsp;$links[$i]</a>&nbsp;<a class=dis href=\"lrscan.php?command=scan&sector=$links[$i]\">[$l_scan]</a>&nbsp;</td></tr>\n";
	}
{/php}
</div>
</td></tr>

<tr><td colspan=2 align=center class=dis><a href="lrscan.php?sector=*" class=dis>[{$l_fullscan}]</a></td></tr>

{if $autototal != 0}
<tr>
<td NOWRAP align="center">
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0  align="center">
{php}
	echo "<tr><td width=100 class=\"nav_title_12\" align=center><br>\n";
	echo "<form name=\"autoroutes\"><select name=\"menu\" onChange=\"location=document.autoroutes.menu.options[document.autoroutes.menu.selectedIndex].value;\" value=\"GO\" class=\"rsform\"><option value=\"\">Select Autoroute</option>\n";
	for($i = 0; $i < count($autolist); $i++){
		if($sector <= $sector_max and $autostart[$i] <= $sector_max)
			echo "<option value=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</option>\n";

		if($sector <= $sector_max and $autoend[$i] <= $sector_max)
			echo "<option value=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</option>\n";

		if($sector > $sector_max and $autostart[$i] == $sector)
			echo "<option value=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</option>\n";

		if($sector > $sector_max and $autoend[$i] == $sector)
			echo "<option value=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</option>\n";

		if($sector > $sector_max and (($autostart[$i] - 1) == $sector or ($autostart[$i] + 1) == $sector))
			echo "<option value=\"navcomp.php?state=start&autoroute_id=$autolist[$i]\">$autostart[$i]&nbsp;=&gt;&nbsp;$autoend[$i]</option>\n";

		if($sector > $sector_max and (($autoend[$i] - 1) == $sector or ($autoend[$i] + 1) == $sector))
			echo "<option value=\"navcomp.php?state=reverse&autoroute_id=$autolist[$i]\">$autoend[$i]&nbsp;=&gt;&nbsp;$autostart[$i]</option>\n";
	}
	echo "</select></form>";
	echo "</td></tr>\n";
{/php}

</td></tr>
</table>
{/if}</td></tr></table>
	</td>
    
</table>
</td></tr>

</table>
<center>
				</tr>
				</table>
			
</center>
<img src="{$starsize}" border="0" alt="" style="position: absolute; z-index:-1; left: 70%; top: 30%; width: 480px; height: 480px; margin-left: -240px; margin-top: -180px;">
