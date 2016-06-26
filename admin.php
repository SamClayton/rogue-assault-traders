<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
// 
// File: admin.php

include ("config/config.php");
$no_gzip = 1;

$title = "Administration";
if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

bigtitle();

if ((!isset($_POST['menu'])) || ($_POST['menu'] == ''))
{
	$_POST['menu'] = '';
}

if ((!isset($_POST['swordfish'])) || ($_POST['swordfish'] == ''))
{
	$_POST['swordfish'] = '';
}

if (isset($_POST['md5swordfish']) && md5($adminpass) == $_POST['md5swordfish']) //md5 sent from log.php
{
	$_POST['swordfish'] = $adminpass;
}

if ((!isset($command)) || ($command == ''))
{
	$command = '';
}

if ((!isset($cmd)) || ($cmd == ''))
{
	$cmd = '';
}

function CHECKED($yesno)
{
	return(($yesno == "Y") ? "CHECKED" : "");
}

function YESNO($onoff)
{
	return(($onoff == "ON") ? "Y" : "N");
}


$login_ip = getenv("REMOTE_ADDR");
if ($_POST['swordfish'] != $adminpass || $_POST['swordfish'] == md5($adminpass))
{
	adminlog(LOG_RAW,"Admin Login attempt from $login_ip");
	echo "<form action=\"admin.php\" method=\"post\">";
	echo "Password: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
	echo "<input type=submit value=Submit><input type=reset value=Reset>";
	echo "</form>";
}
else
{
	if (empty($_POST['menu']))
	{
		adminlog(LOG_RAW,"Admin Login successful from $login_ip");
		echo "Welcome to the Rogue Assault Traders administration module<BR><BR>\n";
		echo "Select a function from the list below:<BR><BR>\n\n";
		echo "<form action=\"admin.php\" name=\"adminstart\" method=\"post\">\n";
		echo "  <select size=\"22\" name=\"menu\">\n";

		$count = 0;
		$filelist = get_dirlist($gameroot."admin");
		for ($c=0; $c<count($filelist); $c++) { 
			$filenameroot =  str_replace(".inc", "", $filelist[$c]); 
			if(strstr($filelist[$c], ".inc")){
				$fs = fopen($gameroot."admin/".$filelist[$c], "r");
				$items = fgets($fs);
				$items = fgets($fs);
				$name = substr(trim($items), 3);
				fclose($fs);
				$fileroot[$count] = $filenameroot;
				$description[$count] = $name;
				$count++;
			}
		}

		asort ($description);
		reset ($description);
		while (list ($key, $val) = each ($description)) {
			echo "	<option value=\"$fileroot[$key]\">$val</option>\n";
		}

		echo "  </select>\n";
		echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
		echo "  &nbsp;<input type=\"submit\" value=\"Submit\">\n";
		echo "</form>\n";
	}
	else
	{
		$button_main = true;
		@include ("admin/". $_POST['menu'] . ".inc");
		if ($button_main)
		{
			echo "<p>\n";
			echo "<form action=\"admin.php\" method=\"post\">\n";
			echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
			echo "  <input type=\"submit\" value=\"Return to main menu\">\n";
			echo "</form>\n";
		}
	}
}

echo $l_global_mlogin;

include ("footer.php");
?> 
