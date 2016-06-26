<?php
// This program is free software; you can redistribute it and/or modify it	
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: create_universe.php

$create_universe = 1;
include ("config/config.php");
$no_gzip = 1;

// Define Functions for this script											

// For this page, we want SQL debug on the screen (0 is shown on screen, 1 is hidden)
$silent = 0;

function check_php_version () {
   $testSplit = explode ('.', '4.3.0');
   $currentSplit = explode ('.', phpversion());

   if ($testSplit[0] < $currentSplit[0])
       return True;
   if ($testSplit[0] == $currentSplit[0]) {
       if ($testSplit[1] < $currentSplit[1])
           return True;
       if ($testSplit[1] == $currentSplit[1]) {
           if ($testSplit[2] <= $currentSplit[2])
               return True;
       }
   }
   return False;
}

// Takes the default values from the ini file and puts them in variables.
// Only used during create_universe.
if($_POST['step'] <= 2){
	$default_template = "default/";
}else{
	 // Get the config_values from the DB - silently.
	 $silent = 1;
	 connectdb();
	 $debug_query = $db->Execute("SELECT * FROM $dbtables[config_values]");
	 db_op_result($debug_query,__LINE__,__FILE__);	

	 while (!$debug_query->EOF && $debug_query)	
	 {	
			$row = $debug_query->fields;	
			$$row['name'] = $row['value'];	
			$debug_query->MoveNext();	
	 }
}

function ini_to_db ($ini_file, $ini_table)
{
	global $dbtables, $db, $silent;

	$silent = 0;
	$cumulative = 0;												
	TextFlush("<b>Storing values in the database [$ini_table]: </b><br>\n");

	// No need to see the debug from the select and the sql generation.
	$silent = 1;
	$sql = "SELECT * FROM $ini_table WHERE id = -1";

	// Execute the query and get the empty recordset
	$debug_query_rs = $db->Execute($sql);
	if ($debug_query_rs)
	{
		db_op_result($debug_query_rs,__LINE__,__FILE__);
		$lines = file ($ini_file);

		// This is a loop, that reads a config.ini file, of the type variable = value.
		// It will loop thru the list of the config.ini variables, and push them into the db
		// along with the description of the variable.
		for($i = 0; $i < count($lines); $i++){

			if(substr($lines[$i], 0, 1) == "[")
				$section = str_replace("[", "", str_replace("]", "", $lines[$i]));

			$text = eregi('^[-!#$%&\'*+\\./0-9=?A-Z^_`{|}~]+', trim($lines[$i]));

			$items = explode(";", trim($lines[$i]));
			$variable = explode("=", $items[0]);
			$variable[0] = trim($variable[0]);
			$variable[1] = str_replace("\"", "", trim($variable[1]));

			if($text)
			{
				$record = array();

				// Set the values for the fields in the record
				$silent = 1; // otherwise its noisy during creation.
				$record['name'] = $variable[0];
				$record['value'] = $variable[1];
				$record['description'] = trim($items[2]);
				$record['section'] = trim($section);
				$debug_query_insert = $db->GetInsertSQL($debug_query_rs, $record);
				db_op_result($debug_query_insert,__LINE__,__FILE__);

				$silent = 0;
				echo "Storing " . stripslashes($record['name']) . " ";
				$debug_query = $db->Execute($debug_query_insert);
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}

		if ($cumulative == 0)
		{
			echo "<b>All ini values have been successfully stored.</b><p>";
		}
		else
		{
			echo "<b><font color=\"yellow\">Issues occured during ini value storage.</font></b><br>\n";
		}
	}
	else
	{
		echo "<b><font color=\"yellow\">Error! database table does not exist!</font></b><p>\n";
	}
}

function newplayer($email, $char, $pass, $ship_name, $player_id)
{
	global $db, $dbtables, $db_type;
	global $start_credits, $start_turns, $default_lang;
	global $start_armour, $start_energy, $start_fighters, $max_turns, $default_template;

	$stamp = date("Y-m-d H:i:s");

	$query = $db->Execute("SELECT MAX(turns_used + turns) AS mturns FROM $dbtables[players]");
	db_op_result($query,__LINE__,__FILE__);
	$res = $query->fields;

	$mturns = $res['mturns'];

	if ($mturns > $max_turns)
	{
		$mturns = $max_turns;
	}

	if ($mturns < $start_turns)
	{
		$mturns = $start_turns;
	}

	$query = $db->Execute("delete FROM $dbtables[players] where player_id = $player_id");
	$query = $db->Execute("delete FROM $dbtables[ships] where player_id = $player_id");

	//Create player
	$debug_query = $db->Execute("INSERT INTO $dbtables[players] (player_id, currentship, ".
								"character_name, password, email, credits, turns, ".
								"turns_used, last_login, rating, ".
								"score, team, team_invite, ip_address, 
								trade_colonists, trade_fighters, ".
								"trade_torps, trade_energy, template, avatar, npc) VALUES(" .
								"$player_id," .			 //player_id
								"$player_id," .				//currentship
								"'$char'," .		//character_name
								"'$pass'," .		//password
								"'$email'," .		//email
								"$start_credits," . //credits
								"$mturns," .		//turns
								"0," .				//turns_used
								"'$stamp'," .		//last_login
								"0," .				//rating
								"0," .				//score
								"0," .				//team
								"0," .				//team_invite
								"'". getenv("REMOTE_ADDR") ."'," .			//ip_address
								"'Y'," .			//trade_colonists
								"'N'," .			//trade_fighters
								"'N'," .			//trade_torps
								"'Y'," .			//trade_energy
								"'$default_template',
								'default_avatar.gif', 1)");
	db_op_result($debug_query,__LINE__,__FILE__);

	// Create player's ship
	$debug_query = $db->Execute("INSERT INTO $dbtables[ships] (player_id, ".
								"class, name, destroyed, basehull, hull, engines, ".
								"power, computer, sensors, beams, ".
								"torp_launchers, torps, shields, armour, ".
								"armour_pts, cloak, sector_id, ore, ".
								"organics, goods, energy, colonists, ".
								"fighters, on_planet, dev_warpedit, ".
								"dev_genesis, dev_emerwarp, ".
								"dev_escapepod, dev_fuelscoop, ".
								"dev_minedeflector, planet_id, ".
								"cleared_defences,dev_nova) VALUES(" .
								"'$player_id'," .	 //player_id
								"'10'," .			//class
								"'$ship_name'," .	//name
								"'N'," .			//destroyed
								"10,".				//basehull
								"0," .				//hull
								"0," .				//engines
								"0," .				//power
								"0," .				//computer
								"0," .				//sensors
								"0," .				//beams
								"0," .				//torp_launchers
								"0," .				//torps
								"0," .				//shields
								"0," .				//armour
								"$start_armour," .	//armour_pts
								"0," .				//cloak
								"1," .				//sector_id
								"0," .				//ore
								"0," .				//organics
								"0," .				//goods
								"$start_energy," .	//energy
								"0," .				//colonists
								"$start_fighters," .//fighters
								"'N'," .			//on_planet
								"0," .				//dev_warpedit
								"0," .				//dev_genesis
								"0," .				//dev_emerwarp
								"'Y'," .			//dev_escapepod
								"'N'," .			//dev_fuelscoop
								"0," .				//dev_minedeflector
								"0," .				//planet_id
								"''," .			 //cleared_defences
								 "'N'" .			//dev_nova
								")");
	db_op_result($debug_query,__LINE__,__FILE__);

	for($total = 0; $total < 3; $total++){
		$debug_query = $db->Execute("INSERT INTO $dbtables[presets] (player_id,preset) VALUES ('$player_id',1)");	 
		db_op_result($debug_query,__LINE__,__FILE__);
	}

	sql_insert_identity_on($dbtables['ibank_accounts']);

	$stamp = date("Y-m-d H:i:s");	 
	$debug_query = $db->Execute("INSERT INTO $dbtables[ibank_accounts] (player_id,balance,loan,loantime) VALUES ('$player_id',0,0,'$stamp')");	 
	db_op_result($debug_query,__LINE__,__FILE__);

	sql_insert_identity_off($dbtables['ibank_accounts']);

	return $player_id;
}

function TextFlush($Text="") 
{
	echo "$Text";
	flush();
}

function sector_todb($array,$method,$sector_id,$be_quiet)
{
	global $db, $db_type, $dbtables, $silent;

	// There shouldnt be a need to see anything but the update debug.
	$silent = 1;

	$sql = "SELECT * FROM $dbtables[universe] WHERE sector_id = $sector_id"; 

	// Execute the query and get the empty recordset
	$debug_query_rs = $db->Execute($sql);
	db_op_result($debug_query_rs,__LINE__,__FILE__);

	if ($be_quiet == 1)
	{
		$silent = 1;
	}

	if ($method == "Updat")
	{
		// Adodb generates the update statement will be for the array.
		if ($debug_query_rs == '')
		{
			echo "<br><br>SQL is: \n";
			var_dump($sql);
			echo "<br><br>debug_query_rs is: \n";
			var_dump($debug_query_rs);
			break;
		}


		$debug_query_insert	= $db->GetUpdateSQL($debug_query_rs, $array);
		db_op_result($debug_query_insert,__LINE__,__FILE__);
	}
	else
	{
		// Adodb generates the insert statement will be for the array.
		$debug_query_insert	= $db->GetInsertSQL($debug_query_rs, $array);
		db_op_result($debug_query_insert,__LINE__,__FILE__);
	}

	if ($be_quiet == 0)
	{
		echo $method."ing sector ". $sector_id . " ";
	}

	$debug_query = $db->Execute($debug_query_insert);
	db_op_result($debug_query,__LINE__,__FILE__);

}

// End defining functions.

// Description: Create Benchmark Class

class c_Timer
{
	var $t_start = 0;
	var $t_stop = 0;
	var $t_elapsed = 0;

	function start()
	{
		$this->t_start = microtime();
	}

	function stop()
	{
		$this->t_stop	= microtime();
	}

	function elapsed()
	{
		$start_u = substr($this->t_start,0,10); $start_s = substr($this->t_start,11,10);
		$stop_u	= substr($this->t_stop,0,10);	$stop_s	= substr($this->t_stop,11,10);
		$start_total = doubleval($start_u) + $start_s;
		$stop_total	= doubleval($stop_u) + $stop_s;
		$this->t_elapsed = $stop_total - $start_total;
		return $this->t_elapsed;
	}
}

// Start Timer
$BenchmarkTimer = new c_Timer;
$BenchmarkTimer->start();

// Set timelimit

$sf = (bool) ini_get('safe_mode');
if (!$sf)
{
	set_time_limit(0);
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

// Include config files and db schema.

include ("includes/schema.php");

$title = "Create Universe";
if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

// Connect to the database.

connectdb();

// Print Title on Page.

bigtitle();

// Manually set step var if info isn't correct.
if (!isset($_POST['swordfish']))
{
	$_POST['swordfish'] = '';
}

if ($_POST['swordfish'] != $adminpass) 
{
	$_POST['step'] = 0;
}

global $silent;
global $maxlen_password;

// Main switch statement.

switch ($_POST['step']) {
// Stage 1, Config values

	case "1":

		echo "<form action=create_universe.php method=post>";
		echo "<table>";
		echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td>Suggested value &nbsp;</td><td>Safe field range</td></tr>";
		echo "<tr><td>Number of sectors total &nbsp;</td><td><input type=text name=sektors size=5 maxlength=5 value=10000></td><td>[100-50,000]</td></tr>";
		echo "<tr><td></td><td><input type=hidden name=swordfish value=$_POST[swordfish]></td><td></td></tr>";
		echo "<tr><td></td><td>Set the date you would like to schedule a game reset.	Set to all zeros for no reset date.<br><br>";
		$thedate = date("Y-m-d");
		$yr=strval(substr($thedate,0,4)); 
		$mo=strval(substr($thedate,5,2)); 
		$da=strval(substr($thedate,8,2));	
		?>
		Month: <select name="reset_month">
		<option value="00">00
		<?
		for($x=1;$x <=12;$x++){
			if (strlen($x) < 2){
				$x="0".$x;
			}
?>
				<option value="<?=$x;?>"><?=$x;?>
			<?
		}?>
		</select>
		&nbsp;&nbsp;&nbsp;Day: </select><select name="reset_day">
		<option value="00">00
		<?
		for($x=1;$x <=31;$x++){
			if (strlen($x) < 2){
				$x="0".$x;
			}
?>
				<option value="<?=$x;?>"><?=$x;?>
			<?
		}?>
		</select>
		&nbsp;&nbsp;&nbsp;Year: </select><select name="reset_year">
		<option value="0000">0000
		<?for($x=$yr;$x <=$yr+5;$x++){?>
			<option value="<?=$x;?>"><?=$x;?>
		<?}?>
		</select> 
		<br><br></td><td></td></tr>
		<tr><td></td><td>Setup File: <select name="setup_file">
<?
	$filelist = get_dirlist($gameroot."config/");
	$newcommands = 0;
	for ($c=0; $c<count($filelist); $c++) { 
		$filenameroot =  str_replace(".ini", "", $filelist[$c]); 
		if(strstr($filelist[$c], "setup_")){
		?>
		<option value="<?=$filelist[$c];?>" <?
			if($filelist[$c] == "setup_default")
				echo " selected";
		?>><?=str_replace("setup_", "", $filenameroot);?>
		<?
		}
	}?>
		</select><br><br>
	</td></tr></table>
<?

		echo "<input type=hidden name=step value=2>";
		echo "<input type=submit value=Submit><input type=reset value=Reset>";
		echo "</form>";
		echo "<BR><BR><FONT COLOR=yellow>";
		echo "WARNING: ALL TABLES WILL BE DROPPED AND THE GAME WILL BE RESET WHEN YOU CLICK 'SUBMIT'!</FONT></font><br><br>";
		break;

// Stage 2, Getting things started
	case "2":

        $is_there = $db->Execute("SELECT * from $dbtables[players]");

		if($enable_profilesupport == 1 and $is_there){

			$debug_query = $db->Execute("SELECT * FROM $dbtables[players], $dbtables[ships] WHERE $dbtables[players].turns_used != 0 and $dbtables[players].player_id = $dbtables[ships].player_id and $dbtables[players].currentship=$dbtables[ships].ship_id and destroyed!='Y' " .
	 		 						 "and email NOT LIKE '%@npc' AND $dbtables[players].player_id > 3 ORDER BY score DESC,character_name ASC");
		 	db_op_result($debug_query,__LINE__,__FILE__);

			if ($debug_query)
			{
				$num_players = $debug_query->RecordCount();

				if($num_players > 0){
					$rank = 0;
					@unlink($gameroot."templates_c/profile_data.txt");
					$fs = @fopen($gameroot.'templates_c/profile_data.txt', 'w');
					$gm_url = $_SERVER['HTTP_HOST'] . $gamepath;
					@fwrite($fs, "server:$game_name\n");
					@fwrite($fs, "url:$gm_url\n");
					while (!$debug_query->EOF)
					{
						$playerinfo = $debug_query->fields;
						if ((isset($playerinfo['profile_name'])) && ($playerinfo['profile_name'] != ''))
						{
							$rank++;
							$resavg = $db->Execute("SELECT SUM(credits) AS a1 , AVG(computer_normal) AS a4 , " .
												"AVG(sensors_normal) AS a5 , AVG(beams_normal) AS a6 , AVG(torp_launchers_normal) AS a7 , AVG(shields_normal) AS a8 , " .
												"AVG(armour_normal) AS a9 , AVG(cloak_normal) AS a10, AVG(jammer_normal) AS a11 FROM $dbtables[planets],$dbtables[players] WHERE " .
												"$dbtables[planets].owner = $dbtables[players].player_id AND $dbtables[players].player_id = $playerinfo[player_id]");
							$row = $resavg->fields;
							$dyn_avg_lvl = $row['a4'] + $row['a5'] + $row['a6'] + $row['a7'] + $row['a8'] + $row['a9'] + $row['a10'] + $row['a11'];
							$dyn_avg = $dyn_avg_lvl / 8;
							$gm_all = 	"player_name=" . rawurlencode($playerinfo['character_name']) . "\n" .
										"planets_built=" . $playerinfo['planets_built'] . "\n" .
										"planets_lost=" . $playerinfo['planets_lost'] . "\n" .
										"captures=" . $playerinfo['captures'] . "\n" .
										"deaths=" . $playerinfo['deaths'] . "\n" .
										"kills=" . $playerinfo['kills'] . "\n" .
										"rating=" . $playerinfo['rating'] . "\n" .
										"turns_used=" . $playerinfo['turns_used'] . "\n" .
										"credits=" . rawurlencode($playerinfo['credits'] + $row['a1']) . "\n" .
										"score=" . rawurlencode($playerinfo['score']) . "\n" .
										"max_defense=" . $dyn_avg . "\n" .
										"rank=" . $rank . "\n" .
										"ptotal=" . $num_players . "\n" .
										"ship_losses=" . $playerinfo['ship_losses'] . "\n" .
										"self_destruct=0\n" .
										"name=" . $playerinfo['profile_name'] . "\n" .
										"password=" . $playerinfo['profile_password'] . "\n";

							@fwrite($fs, "$gm_all\n");
						}
						$debug_query->MoveNext();
					}
					@fclose($fs);
					$url = "http://profiles.aatraders.com/update_gameover.php?server=" . rawurlencode($gm_url);

					echo "\n\n<!--" . $url . "-->\n\n";

					$i = @file($url);
				}
			}
		}

		// Drop all tables.
		destroy_schema();

		if ($db_type=="postgres7") // So far, only used on postgres
		{
			// Drop all sequences.
			destroy_seq();
		}

		$lines = file ("config/$setup_file");
		for($i = 0; $i < count($lines); $i++){

		$text = eregi('^[-!#$%&\'*+\\./0-9=?A-Z^_`{|}~]+', trim($lines[$i]));

		$items = explode(";", trim($lines[$i]));
		$variable = explode("=", $items[0]);
		$variable[0] = trim($variable[0]);
		$variable[1] = str_replace("\"", "", trim($variable[1]));

			if($text)
				$$variable[0] = $variable[1];
		}

		$silent = 0;
		// Create the new schema.
		create_schema();

		ini_to_db("config/$setup_file", $dbtables['config_values']);

		if (!check_php_version ())
		{
			$debug_query = $db->Execute("UPDATE $dbtables[config_values] SET value=0 WHERE name='enable_spiral_galaxy'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		ini_to_db("config/wordcensor.ini", $dbtables['wordcensor']);

		$resetdescription = "Date server scheduled to reset game";
		
		$scheduled_reset = $reset_year."-".$reset_month."-".$reset_day;
		$debug_query = $db->Execute("INSERT INTO $dbtables[config_values] (name,value, description, section) VALUES ('scheduled_reset','$scheduled_reset', '$resetdescription', 'Reset Date')");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debris_max = max(50, floor($_POST['sektors']/1000) * 100);
		echo "Setting debris_max to ". $debris_max ."<br><br>";
		$debug_query = $db->Execute("UPDATE $dbtables[config_values] SET value=". $debris_max ." WHERE name='debris_max'");
		db_op_result($debug_query,__LINE__,__FILE__);

		ini_to_db("config/languages.ini", $dbtables['languages']);

		echo "<FORM ACTION=\"{$returnlink}\" METHOD=\"POST\"	enctype=\"multipart/form-data\">";
		echo "<input type=hidden name=step value=2.1>";
		echo "<input type=hidden name=sektors value=$sektors>";
		echo "<INPUT TYPE=SUBMIT NAME=command VALUE=\"Continue\">";
		echo "<input type=hidden name=count value=$count>";
		echo "<input type=hidden name=swordfish value=$swordfish>";
		echo "</form>";
		break;

// Stage 2.1, Editing Config Ini
	case "2.1":

		echo "<br><b>You should now edit any of the games Configuration Settings at this time.</b><br><br>";
		// Get the config_values from the DB - silently.
	if($command == "save"){
		echo "<table><tr><td>";
		for($i = 0; $i < $count; $i++){
			echo "Updating config variable: <b><i>$name[$i]</i></b> = <b>$value[$i]&nbsp;</b>";
			if($name[$i] != "silent")
		 		 $$name[$i] = $value[$i];	
			$debug_query = $db->Execute("UPDATE $dbtables[config_values] SET value='$value[$i]' WHERE name='$name[$i]'");
			db_op_result($debug_query,__LINE__,__FILE__);
		}

		echo "<br><br><b>Variable Update Complete - <i>$game_name</i></b><br><br></td><tr></table>";
		unset($command);
	}

		$silent = 1;

		if((empty($section) or !isset($section)) and $command != "save"){
			echo "Select the Configuration Section you would like to edit:<BR><BR>\n";
			echo "<form action=\"create_universe.php\" method=\"post\">\n";
			echo "	<select name=\"section\">\n";
			$debug_query = $db->Execute("SELECT distinct section FROM $dbtables[config_values]");
			db_op_result($debug_query,__LINE__,__FILE__);
			while (!$debug_query->EOF && $debug_query)
			{
				$row = $debug_query->fields;
				echo "	<option value=\"$row[section]\">$row[section]</option>\n";
				$debug_query->MoveNext();
			}
			echo "	</select>\n";
			echo "<input type=hidden name=sektors value={$sektors}>";
			echo "<input type=hidden name=step value=2.1>";
			echo "	<input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
			echo "	&nbsp;<input type=\"submit\" value=\"Select\">\n";
			echo "</form><br><br><hr><br>\n";
			echo "<form action=create_universe.php method=post>
				<input type=hidden name=step value=3>
				<input type=hidden name=sektors value=$sektors>
				<input type=hidden name=swordfish value=$swordfish>
				<INPUT TYPE=SUBMIT NAME=command VALUE=\"Continue Universe Creation\">
				</form>";
		}else{
			// Get the config_values from the DB - silently.
			$silent = 1;
			$debug_query = $db->Execute("SELECT * FROM $dbtables[config_values] where section='$section'");
			db_op_result($debug_query,__LINE__,__FILE__);
			$count = 0;
			while (!$debug_query->EOF && $debug_query)
			{
				$row = $debug_query->fields;
				$db_config_name[$count] = $row['name'];
				$db_config_value[$count] = $row['value'];
				$db_config_info[$count] = $row['description'];
				$count++;
				$debug_query->MoveNext();
			}
			$smarty->assign('sektors', "$_POST[sektors]");
			$smarty->assign('swordfish', "$_POST[swordfish]");
			$smarty->assign('count', $count);
			$smarty->assign('db_config_name', $db_config_name);
			$smarty->assign('db_config_value', $db_config_value);
			$smarty->assign('db_config_info', $db_config_info);
			$smarty->display("admin/setedit_universe.tpl");
		}
		break;

 // Stage 3, Configuration
	case "3":
		echo "<br><br><b>Creating IP/EMail Ban List<b><br><br>";
		$fs = fopen($gameroot."config/banned_ip.ini", "r");
		while(!feof($fs)){
			$items = fgets($fs);
			$ipaddress = trim($items);
			if(strlen($ipaddress) > 8){
				echo "Banned IP Address: $ipaddress ";
				$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] VALUES('', '$ipaddress', '')");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
		fclose($fs);

		$fs = fopen($gameroot."config/banned_email.ini", "r");
		while(!feof($fs)){
			$items = fgets($fs);
			$emailname = trim($items);
			if(strlen($emailname) > 5){
				echo "Banned Email: $emailname ";
				$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] VALUES('', '', '$emailname')");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
		fclose($fs);

		echo "<br><br>Inserting Shoutbox Welcome message from Webmaster.<br><br>";
		$res = $db->Execute("INSERT INTO $dbtables[shoutbox] (player_id,player_name,sb_date,sb_text,sb_alli) VALUES (1,'Webmaster'," . time() . ",'Welcome to $game_name',0) ");
		db_op_result($debug_query,__LINE__,__FILE__);
		echo "<td><tr></table><table>";

		@unlink($gameroot."config/banner_top.inc");
		@unlink($gameroot."config/banner_bottom.inc");

		echo "<form action=create_universe.php method=post>";
		echo "<TR nowrap><TD width=\"250\"><b>Insert Top Banner Code:</b><br>This is just raw banner code.	Do not include any table, tr or td tags.<br><br><font color=#00ff00><i>Leave this blank if you do not want to place a banner at the top of the page.</i></font></TD>";
		echo "<TD width=\"800\"><TEXTAREA NAME=top_banner ROWS=10 COLS=60></TEXTAREA></TD></tr>";
		echo "<TR nowrap><TD width=\"250\"><b>Insert Bottom Banner Code:</b><br>This is just raw banner code.	Do not include any table, tr or td tags.<<br><br><font color=#00ff00><i>Leave this blank if you do not want to place a banner at the bottom of the page.</i></font></TD>";
		echo "<TD width=\"800\"><TEXTAREA NAME=bottom_banner ROWS=10 COLS=60></TEXTAREA></TD></tr>";

		echo "<tr><td></td><td><input type=hidden name=step value=3.2><input type=hidden name=swordfish value=$_POST[swordfish]></td><td></td></tr>";
		echo "</table>";
		echo "<input type=hidden name=sektors value=$_POST[sektors]>";
		echo "<input type=submit value=Submit><input type=reset value=Reset>";
		echo "</form>";
		break;

 // Stage 3.2, Configuration
	case "3.2":

		if($top_banner != ''){
			$fs = @fopen($gameroot.'config/banner_top.inc', 'w');
			@fwrite($fs, "<table border='0' align='center'><tr><td>". stripslashes($top_banner) . "</td></tr></table>");
			@fclose($fs);
		}

		if($bottom_banner != ''){
			$fs = @fopen($gameroot.'config/banner_bottom.inc', 'w');
			@fwrite($fs, "<table border='0' align='center'><tr><td>" . stripslashes($bottom_banner) . "</td></tr></table>");
			@fclose($fs);
		}

		echo "<form action=create_universe.php method=post>";
		echo "<table>";
		echo "<tr><td><b><u>Base/Planet Setup</u></b></td><td>Suggested value</td><td>Safe field range</td></tr>";
		echo "<tr><td>Percent Upgrade</td><td><input type=text name=upgrades size=5 maxlength=5 value=1></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Device</td><td><input type=text name=devices size=5 maxlength=5 value=1></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Spacedock</td><td><input type=text name=docks size=5 maxlength=5 value=1></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Ore</td><td><input type=text name=ore size=5 maxlength=5 value=15></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Organics</td><td><input type=text name=organics size=5 maxlength=5 value=10></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Goods</td><td><input type=text name=goods size=5 maxlength=5 value=15></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Energy</td><td><input type=text name=energy size=5 maxlength=5 value=10></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Upgrade/Device ports Alliance Owned</td><td><input type=text name=percentkabal size=5 maxlength=5 value=10></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Alliance Sectors with Casino Ports</td><td><input type=text name=casinos size=5 maxlength=5 value=25></td><td>[1-95]</td></tr>";
		echo "<tr><td>Percent Commodities ports with fixed max prices</td><td><input type=text name=percentfixed size=5 maxlength=5 value=10></td><td>[1-95]</td></tr>";
		echo "<tr><td>Initial Commodities to Sell<br><td><input type=text name=initscommod size=6 maxlength=6 value=100.00> % of max&nbsp;&nbsp;</td><td></td></tr>";
		echo "<tr><td>Initial Commodities to Buy<br><td><input type=text name=initbcommod size=6 maxlength=6 value=100.00> % of max&nbsp;</td><td></td></tr>";
		echo "<tr><td><b><u>Sector/Link Setup</u></b></td><td></td><td></td></tr>";
		$fedsecs = intval($_POST['sektors'] / 200);
		echo "<input type=hidden name=sektors value=$_POST[sektors]>";
		echo "<tr><td>Number of Mornoc Alliance sectors</TD><TD><INPUT TYPE=TEXT NAME=fedsecs SIZE=6 MAXLENGTH=6 VALUE=$fedsecs></TD><td></td></TR>";
		echo "<tr><td>Average # of links per sector &nbsp;</td><td><input type=text name=linksper size=2 maxlength=2 value=5></td><td>[1-10]</td></tr>";
		echo "<tr><td>% of sectors with two-way links &nbsp;</td><td><input type=text name=twoways size=3 maxlength=3 value=40></td><td>[1-100]</td></tr>";
		echo "<tr><td>% of sectors with unowned planets &nbsp;</td><td><input type=text name=planets size=3 maxlength=3 value=10></td><td>[1-100]</td></tr>";
		echo "<tr><td>% of unowned planets that will be based independant planets&nbsp;</td><td><input type=text name=basedplanets size=3 maxlength=3 value=99></td><td>[1-100]</td></tr>";
		echo "<tr><td>% of max colonists to place on based planets&nbsp;</td><td><input type=text name=basedcolonists size=3 maxlength=3 value=75></td><td>[1-100]</td></tr>";
		echo "<tr><td>Min tech level on based planets&nbsp;</td><td><input type=text name=minbasedlevel size=3 maxlength=3 value=15></td><td>[1-30]</td></tr>";
		echo "<tr><td>Max tech level on based planets&nbsp;</td><td><input type=text name=maxbasedlevel size=3 maxlength=3 value=25></td><td>[1-54]</td></tr>";
		echo "<tr><td></td><td><input type=hidden name=step value=4><input type=hidden name=swordfish value=$_POST[swordfish]></td><td></td></tr>";
		echo "</table>";
		echo "<input type=submit value=Submit><input type=reset value=Reset>";
		echo "</form>";
		break;

// Stage 4, Out with the old and in with the new
	case "4":

		if ($_POST['fedsecs'] > $_POST['sektors']) 
		{
			echo "The number of Mornoc Alliance sectors must be smaller than the size of the universe!";
			break;
		}

		if ($_POST['linksper'] > $link_max)
		{
			echo "The average number of links per sector must not be more than the max number of links per sector!<br>";
			echo "We have lowered it to the max number of links per sector.";
			$_POST['linksper'] = $link_max;
		}

		$upp = round($_POST['sektors'] * $_POST['devices']/100);
		$spp = round($_POST['sektors'] * $_POST['upgrades']/100);
		$dpp = round($_POST['sektors'] * $_POST['docks']/100);
		$cpp = round($_POST['sektors'] * $_POST['casinos']/100);
		$oep = round($_POST['sektors'] * $_POST['ore']/100);
		$ogp = round($_POST['sektors'] * $_POST['organics']/100);
		$gop = round($_POST['sektors'] * $_POST['goods']/100);
		$enp = round($_POST['sektors'] * $_POST['energy']/100);
		$kpp = round(($upp + $spp + $dpp) * $_POST['percentkabal']/100);
		$cpp = round($kpp * $_POST['casinos']/100);
		$fixedtotal = round(($oep + $ogp + $gop + $enp) * $_POST['percentkabal']/100);
		$empty = $_POST['sektors']-$upp-$spp-$dpp-$oep-$ogp-$gop-$enp;
		$nump = round ($_POST['sektors'] * $_POST['planets']/100);
		$numbp = round ($nump * $_POST['basedplanets']/100);
		$nump = $nump - $numbp;
		echo "So you would like your ".($_POST['sektors'])." sector universe to have:<BR><BR>";
		echo "$upp device ports<BR>";
		echo "$spp upgrade ports<BR>";
		echo "$dpp spacedock ports<BR>";
		echo "$oep ore ports<BR>";
		echo "$ogp organics ports<BR>";
		echo "$gop goods ports<BR>";
		echo "$enp energy ports<BR>";
		echo "$kpp Alliance owned Device/Upgrade ports<BR>";
		echo "$cpp Alliance owned Casino ports<BR>";
		echo "$fixedtotal Commodity ports with fixed prices<BR>";
		echo "$_POST[initscommod]% initial commodities to sell<BR>";
		echo "$_POST[initbcommod]% initial commodities to buy<BR>";
		echo "$empty empty sectors<BR>";
		echo "$_POST[fedsecs] Mornoc Alliance sectors<BR>";
		echo "$nump planets will be unowned<BR>";
		echo "$numbp planets will be based and independant<BR>";
		echo "Initial based and independant planets will have from 1 - ".NUMBER(floor(($basedcolonists/100) * $colonist_limit))." colonists<BR>";
	if($minbasedlevel < 1)
		$minbasedlevel = 1;
	if($minbasedlevel > 30)
		$minbasedlevel = 30;
	if($maxbasedlevel < $minbasedlevel)
		$maxbasedlevel = $minbasedlevel;
	if($maxbasedlevel > 54)
		$maxbasedlevel = 54;
		echo "Initial based and independant planets will have random levels from $minbasedlevel to $maxbasedlevel<BR><BR>";
		echo "Roughly " . floor($_POST['linksper'] * $_POST['sektors']) . " links<br>";
		echo "Roughly " . floor(($_POST['twoways']/100) * $_POST['sektors']) . " two-way links<br>";
		echo "Roughly " . floor(((100-$_POST['twoways'])/100) * $_POST['sektors']) . " one-way links<br>";
		echo "<br><br>\n";
		echo "<form action=create_universe.php method=post>";
		echo "<input type=hidden name=step value=6>";
		echo "<input type=hidden name=basedcolonists value=$basedcolonists>";
		echo "<input type=hidden name=minbasedlevel value=$minbasedlevel>";
		echo "<input type=hidden name=maxbasedlevel value=$maxbasedlevel>";
		echo "<input type=hidden name=numbp value=$numbp>";
		echo "<input type=hidden name=upp value=$upp>";
		echo "<input type=hidden name=spp value=$spp>";
		echo "<input type=hidden name=dpp value=$dpp>";
		echo "<input type=hidden name=cpp value=$cpp>";
		echo "<input type=hidden name=oep value=$oep>";
		echo "<input type=hidden name=ogp value=$ogp>";
		echo "<input type=hidden name=gop value=$gop>";
		echo "<input type=hidden name=enp value=$enp>";
		echo "<input type=hidden name=kpp value=$kpp>";
		echo "<input type=hidden name=fixedtotal value=$fixedtotal>";
		echo "<input type=hidden name=initscommod value=$_POST[initscommod]>";
		echo "<input type=hidden name=initbcommod value=$_POST[initbcommod]>";
		echo "<input type=hidden name=nump value=$nump>";
		echo "<input type=hidden name=linksper value=$_POST[linksper]>";
		echo "<input type=hidden name=twoways value=$_POST[twoways]>";
		echo "<input type=hidden name=fedsecs value=$_POST[fedsecs]>";
		echo "<input type=hidden name=swordfish value=$_POST[swordfish]>";
		echo "<input type=hidden name=sektors value=$_POST[sektors]>";
		echo "<input type=submit value=Continue>";
		echo "</form>";
		echo "<BR><BR><FONT COLOR=yellow>";
		echo "WARNING: GO GET A DRINK OR SOME FRESH AIR...THIS MAY TAKE A FEW MINUTES DEPENDING ON UNIVERSE SIZE<br>PLEASE ONE CLICK AND ONE CLICK ONLY OR YOU MAY CAUSE PROBLEMS BUILDING THE DATABASE!</FONT><br><br>";
		break;

// Stage 6, Galaxies-R-Us
	case "6":
		// Build the zones table. Only four zones here. The rest are named after players for
		// when they manage to dominate a sector.
		TextFlush("Building zone descriptions:\n<br>");

		// It should be noted: I do not like this if/then specific to the sql types. What we should be doing is having the sql
		// call itself in the sql-common file, and call it from there. The sql-common file would contain the if/thens specific to
		// different databases. But for the time being this is a non-elegant solution to a rather difficult problem, so its a
		// great deal better than doing nothing.

		sql_insert_identity_on($dbtables['zones']);

		TextFlush("Setting zone 1: Unchartered Space ");
		$debug_query = $db->Replace($dbtables['zones'], array('zone_id'=> 1, 'zone_name'=>'Unchartered space', 'owner'=>0, 'allow_beacon'=>'Y', 'team_zone'=>'N', 'allow_attack'=>'Y', 'allow_planetattack'=>'Y', 'allow_warpedit'=>'Y', 'allow_planet'=>'Y', 'allow_trade'=>'Y', 'allow_defenses'=>'Y', 'max_hull'=>'0', 'zone_color'=>'#000000'), 'zone_id', $autoquote = true);
		db_op_result($debug_query,__LINE__,__FILE__);

		TextFlush("\nSetting zone 2: Alliance Space ");
		$debug_query = $db->Replace($dbtables['zones'], array('zone_id'=> 2, 'zone_name'=>'Mornoc Alliance space', 'owner'=>3, 'allow_beacon'=>'N', 'team_zone'=>'N', 'allow_attack'=>'N', 'allow_planetattack'=>'N', 'allow_warpedit'=>'N', 'allow_planet'=>'N', 'allow_trade'=>'Y', 'allow_defenses'=>'N', 'max_hull'=>"$fed_max_avg_tech", 'zone_color'=>'#ffffff'), 'zone_id', $autoquote = true);
		db_op_result($debug_query,__LINE__,__FILE__);

		TextFlush("\nSetting zone 3: Mornoc Alliance Space");
		$debug_query = $db->Replace($dbtables['zones'], array('zone_id'=> 3, 'zone_name'=>'Alliance space', 'owner'=>1, 'allow_beacon'=>'N', 'team_zone'=>'N', 'allow_attack'=>'Y', 'allow_planetattack'=>'N', 'allow_warpedit'=>'N', 'allow_planet'=>'N', 'allow_trade'=>'Y', 'allow_defenses'=>'N', 'max_hull'=>'0', 'zone_color'=>'#ffff00'), 'zone_id', $autoquote = true);
		db_op_result($debug_query,__LINE__,__FILE__);

		TextFlush("\nSetting zone 4: War Zone Space");
		$debug_query = $db->Replace($dbtables['zones'], array('zone_id'=> 4, 'zone_name'=>'War Zone', 'owner'=>0, 'allow_beacon'=>'N', 'team_zone'=>'N', 'allow_attack'=>'Y', 'allow_planetattack'=>'Y', 'allow_warpedit'=>'Y', 'allow_planet'=>'Y', 'allow_trade'=>'N', 'allow_defenses'=>'Y', 'max_hull'=>'0', 'zone_color'=>'#ff0000'), 'zone_id', $autoquote = true);
		db_op_result($debug_query,__LINE__,__FILE__);
		TextFlush("");

		sql_insert_identity_off($dbtables['zones']);

		// Setup some need values for product amounts
		$initsore = $ore_limit * $_POST['initscommod'] / 100.0;
		$initsorganics = $organics_limit * $_POST['initscommod'] / 100.0;
		$initsgoods = $goods_limit * $_POST['initscommod'] / 100.0;
		$initsenergy = $energy_limit * $_POST['initscommod'] / 100.0;
		$initbore = $ore_limit * $_POST['initbcommod'] / 100.0;
		$initborganics = $organics_limit * $_POST['initbcommod'] / 100.0;
		$initbgoods = $goods_limit * $_POST['initbcommod'] / 100.0;
		$initbenergy = $energy_limit * $_POST['initbcommod'] / 100.0;

		sql_insert_identity_on($dbtables['universe']);

		// Build Sector 1, Sol
		$sector = array();
		$sector = array('sector_id' => '1',
						'sector_name' => 'Mornoc Prime',
						'zone_id' => '2',
						'star_size' => '1',
						'port_type' => 'upgrades',
						'port_organics' => '0',
						'port_ore' => '0',
						'port_goods' => '0',
						'port_energy' => '0',
						'beacon' => 'Mornoc Prime: Hub of the Universe',
						'x' => '0',
						'y' => '0',
						'z' => '0');
		sector_todb($sector,"Insert",'-1',1);

// Build Sector 2, Proxima Centauri
		$sector = array('sector_id' => '2',
						'sector_name' => 'Beta Proxima',
						'zone_id' => '2',
						'star_size' => '0',
						'port_type' => 'devices',
						'port_organics' => '0',
						'port_ore' => '0',
						'port_goods' => '0',
						'port_energy' => '0',
						'beacon' => 'Beta Proxima: Gateway to the Galaxy',
						'x' => '0',
						'y' => '0',
						'z' => '1');
		sector_todb($sector,"Insert",2,1);

// Build Sector 3, Wolf-359
		$sector = array('sector_id' => '3',
						'sector_name' => 'Targh Delta',
						'zone_id' => '2',
						'star_size' => '0',
						'port_type' => 'energy',
						'port_organics' => $initborganics,
						'port_ore' => $initbore,
						'port_goods' => $initbgoods,
						'port_energy' => $initsenergy,
						'beacon' => 'Targh Delta',
						'x' => '0',
						'y' => '0',
						'z' => '2');
		sector_todb($sector,"Insert",3,1);

// Build Sector 4, Andromeda Base
		$sector = array('sector_id' => '4',
						'sector_name' => 'Sygnus 4',
						'zone_id' => '2',
						'star_size' => '0',
						'port_type' => 'spacedock',
						'port_organics' => $initborganics,
						'port_ore' => $initbore,
						'port_goods' => $initbgoods,
						'port_energy' => $initsenergy,
						'beacon' => 'Sygnus 4: Mornoc Alliance Starship Repair Base',
						'x' => '0',
						'y' => '0',
						'z' => '3');
		sector_todb($sector,"Insert",4,1);

// Here's where the remaining sectors get built

		TextFlush("Creating remaining ".($_POST['sektors']-4)." sectors <br>\n");
		$collisions = 0;
		# calculate the scale to use such that 
		# the max distance between 2 points will be
		# approx $universe_size.
		$scale = $universe_size / (4.0*pi());

		# compute the angle between arms
		$angle = deg2rad(360/$spiral_galaxy_arms);

		if (!check_php_version ())
		{
			$enable_spiral_galaxy = 0;
		}

		TextFlush("<br>Creating sectors 5 to 499<br>\n");
		for ($i=5; $i<=$_POST['sektors']; $i++) 
		{

			if(!($i % 500)){
				if($i + 499 > $_POST['sektors'])
					$end = $_POST['sektors'] - $i;
				else
					$end = 499;
				TextFlush("Creating sectors $i to " . ($i + $end) . "<br>\n");
			}

			$sector = '';

			$random_star = mt_rand(0,$max_star_size);
			$sector['star_size'] = $random_star;
			$sector['sector_id'] = $i;
			$sector['sector_name'] = '';
//			$sector['port_type'] = 'none'; // This needs to be here to prevent warnings, but it causes it to loop infinitely?! Why?
			$sector['port_organics'] = '';
			$sector['port_ore'] = '';
			$sector['port_goods'] = '';
			$sector['port_energy'] = '';
			$sector['beacon'] = '';

			$collision = FALSE;
			while (TRUE) 
			{
				// Lot of shortcuts here. Basically we generate a spherical coordinate and convert it to cartesian.
				// Why? Cause random spherical coordinates tend to be denser towards the center.
				// Should really be like a spiral arm galaxy but this'll do for now.
			if($enable_spiral_galaxy != 1){
				$radius = mt_rand(100,$universe_size*100)/100;

				$temp_a = deg2rad(mt_rand(0,36000)/100-180);
				$temp_b = deg2rad(mt_rand(0,18000)/100-90);
				$temp_c = $radius*sin($temp_b);

				$sector['x'] = round(cos($temp_a)*$temp_c);
				$sector['y'] = round(sin($temp_a)*$temp_c);
				$sector['z'] = round($radius*cos($temp_b));

				// Collision check
				if (isset($index[$sector['x'].','.$sector['y'].','.$sector['z']])) 
				{
					$collisions++;
				} 
				else 
				{
					break;
				}
			}
			else
			{
				//The Spiral Galaxy Code was proviced by "Kelly Shane Harrelson" <shane@mo-ware.com> 
				# need to randomly assign this point to an arm.
				$arm = mt_rand(0,$spiral_galaxy_arms-1);
				$arm_offset = $arm * $angle;

				# generate the logical position on the spiral (0 being closer to the center).
				# the double rand puts more towards the center.
				$u = deg2rad(mt_rand(0, mt_rand(0, 360)));

				# generate the base x,y,z location in cartesian form
				$bx = $u*cos($u+$arm_offset);
				$by = $u*sin($u+$arm_offset);
				$bz = 0.0;

				# generate a max delta from the base x, y, z.
				# this will be larger closer to the center,
				# tapering off the further out you are. 
				# this will create the bulge like effect in 
				# the center.	this is just a rough function
				# and there are probably better ones out there.
				$d = ($u<0.3) ? 1.5 : (log($u,10)*-1.0)+1.0;	# log base 10

				# generate random angles and distance for offsets from base x,y,z
				$dt = deg2rad(mt_rand(0, 360)); # angle theta 0-360
				$dp = deg2rad(mt_rand(0, 360)); # angle phi	0-360
				$dd = $d*rand(1,100)/100;	 # distance	 0-$d

				# based on random angles and distance, generate cartesian offsets for base x,y,z
				$dx = $dd*sin($dt)*cos($dp);
				$dy = $dd*sin($dt)*sin($dp);
				$dz = $dd*cos($dt);

				# we want the arms to flatten out away from center
				$dz *= ($d/1.5);	

				# calcuate final cartesian coordinate 
				$x = $bx + $dx;
				$y = $by + $dy;
				$z = $bz + $dz;

				# now scale them to fit $universe_size
				$x *= $scale;
				$y *= $scale;
				$z *= $scale;

				$sector['x'] = $x;
				$sector['y'] = $y;
				$sector['z'] = $z;
				$sector['spiral_arm'] = $arm;

				// Collision check
				if (isset($index[$sector['x'].','.$sector['y'].','.$sector['z']])) 
				{
					$collisions++;
				} 
				else 
				{
					break;
				}
			}
			}
			$index[$sector['x'].','.$sector['y'].','.$sector['z']]=&$sector;

			// The Mornoc Alliance owns the first series of sectors. Logical because they
			// probably numbered them as they were found.
			if ($i<$_POST['fedsecs']) 
			{
				$sector['zone_id'] = '2'; // Mornoc Alliance space
			} 
			else 
			{
				$sector['zone_id'] = '1'; // Uncharted
			}

			// Insert the dump of the sector here, remove the $i notes above.
			sector_todb($sector,"Insert",'-1',1);
		}

		if ($collisions) 
		{
			echo("<font color=\"yellow\">- $collisions sector collisions repaired</font> ");
		} 
		else 
		{
			echo("- no sector collisions detected ");
		}

		TextFlush("<font color=\"lime\">- operation completed successfully.</font><br>");

// Locations are mapped out so now we need ports.

		TextFlush("Preparing for port placement:\n<br>");

		// Device port placement
		TextFlush("\n<br>Placing $_POST[upp] device ports<br>\n");

		$totaluports = 0;
		while ($_POST['upp']-1 > 0) // subtract one for the existing device port in fed space.
		{
			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$upp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($upp_sector,__LINE__,__FILE__);

			if (!$upp_sector->EOF)
			{
				$sector['zone_id'] = '1';
				$sector['port_type'] = 'devices';
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['upp']--;
				$portslist[$totaluports] = $random_sector;
				$totaluports++;
			}
		}

		// Upgrade port placement
		TextFlush("\n<br>Placing $_POST[spp] upgrade ports <br>\n");

		$totalsports = 0;
		while ($_POST['spp']-1 > 0) // subtract one for the existing device port in fed space.
		{
			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$spp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($spp_sector,__LINE__,__FILE__);

			if (!$spp_sector->EOF)
			{
				$sector['zone_id'] = '1';
				$sector['port_type'] = 'upgrades';
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['spp']--;
				$portslist2[$totalsports] = $random_sector;
				$totalsports++;
			}
		}

		// Spacedock port placement
		TextFlush("\n<br>Placing $_POST[dpp] spacedock ports <br>\n");

		$totalsports = 0;
		while ($_POST['dpp']-1 > 0) // subtract one for the existing device port in fed space.
		{
			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$dpp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($dpp_sector,__LINE__,__FILE__);

			if (!$dpp_sector->EOF)
			{
				$sector['zone_id'] = '1';
				$sector['port_type'] = 'spacedock';
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['dpp']--;
				$portslist3[$totalsports] = $random_sector;
				$totalsports++;
			}
		}

		// Alliance Device/Upgrade port zone placement
		TextFlush("\n<br>Placing $_POST[kpp] Mornoc Alliance device/upgrade zones <br>\n");
		$totalkpp = $_POST['kpp'];
		while ($_POST['kpp'] > 0) // subtract one for the existing device port in fed space.
		{
			if($_POST['kpp'] <= floor($totalkpp / 2)){
				$random_selection = mt_rand(0, $totaluports - 1);
				$random_sector = $portslist[$random_selection];
				$sector['port_type'] = 'devices';
			}else{
				$random_selection = mt_rand(0, $totalsports - 1);
				$random_sector = $portslist2[$random_selection];
				$sector['port_type'] = 'upgrades';
			}
			$sector['zone_id'] = '3';
			$sector['star_size'] = '0';
			sector_todb($sector,"Updat",$random_sector,1);
			$_POST['kpp']--;
		}

		// Alliance Casino port placement
		TextFlush("\n<br>Placing $_POST[cpp] Mornoc Alliance Casino ports <br>\n");

		$totalsports = 0;
		while ($_POST['cpp'] > 0)
		{
			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$cpp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($cpp_sector,__LINE__,__FILE__);

			if (!$cpp_sector->EOF)
			{
				$sector['zone_id'] = '3';
				$sector['port_type'] = 'casino';
				$sector['star_size'] = '0';
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['cpp']--;
				$portslist4[$totalsports] = $random_sector;
				$debug_query = $db->Execute("INSERT INTO $dbtables[casino_forums] (forum_name, forum_desc, private, casino_sector) values ('Smugglers Den Bar #$random_sector', 'Welcome and have fun!', 0, $random_sector)");
	 			db_op_result($debug_query,__LINE__,__FILE__);
				$totalsports++;
			}
		}

		// Ore port placement
		$fixedore = floor($fixedtotal / 4);
		$oreleft = $_POST['oep'] - $fixedore;
		TextFlush("\n<br>Placing $fixedore Fixed Price ore ports <br>\n");

		while ($_POST['oep'] > 0)
		{

			if($fixedore == 0){
				TextFlush("\n<br>Placing $oreleft Non-Fixed Price ore ports <br>\n");
				$fixedore--;
			}

			$oep_sector='';
			$sector='';
			$random_sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$oep_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($oep_sector,__LINE__,__FILE__);

			if (!$oep_sector->EOF)
			{
				$sector['port_type'] = 'ore';
				$sector['port_organics'] = $initborganics;
				$sector['port_ore'] = $initsore;
				$sector['port_goods'] = $initbgoods;
				$sector['port_energy'] = $initbenergy;
				$sector['trade_date'] = date("Y-m-d H:i:s");
				if($fixedore > 0){
					$sector['fixed_price'] = 1;
					$sector['fixed_ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['fixed_organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['fixed_goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['fixed_energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
					$sector['ore_price'] = $sector['fixed_ore_price'];
					$sector['organics_price'] = $sector['fixed_organics_price'];
					$sector['goods_price'] = $sector['fixed_goods_price'];
					$sector['energy_price'] = $sector['fixed_energy_price'];
				}else{
					$sector['fixed_price'] = 0;
					$sector['fixed_ore_price'] = 0;
					$sector['fixed_organics_price'] = 0;
					$sector['fixed_goods_price'] = 0;
					$sector['fixed_energy_price'] = 0;
					$sector['ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
				}
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['oep']--;
				$fixedore--;
			}
		}

		// Organics port placement
		$fixedorganics = floor($fixedtotal / 4);
		$organicsleft = $_POST['ogp'] - $fixedorganics;
		TextFlush("\n<br>Placing $fixedorganics Fixed Price organics ports <br>\n");

		while ($_POST['ogp'] > 0)
		{
			if($fixedorganics == 0){
				TextFlush("\n<br>Placing $organicsleft Non-Fixed Price organics ports <br>\n");
				$fixedorganics--;
			}

			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$ogp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($ogp_sector,__LINE__,__FILE__);

			if (!$ogp_sector->EOF)
			{
				$sector['port_type'] = 'organics';
				$sector['port_organics'] = $initsorganics;
				$sector['port_ore'] = $initbore;
				$sector['port_goods'] = $initbgoods;
				$sector['port_energy'] = $initbenergy;
				$sector['ore_price'] = 0;
				$sector['organics_price'] = 0;
				$sector['goods_price'] = 0;
				$sector['energy_price'] = 0;
				$sector['trade_date'] = date("Y-m-d H:i:s");
				if($fixedorganics > 0){
					$sector['fixed_price'] = 1;
					$sector['fixed_organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['fixed_ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['fixed_goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['fixed_energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
					$sector['ore_price'] = $sector['fixed_ore_price'];
					$sector['organics_price'] = $sector['fixed_organics_price'];
					$sector['goods_price'] = $sector['fixed_goods_price'];
					$sector['energy_price'] = $sector['fixed_energy_price'];
				}else{
					$sector['fixed_price'] = 0;
					$sector['fixed_ore_price'] = 0;
					$sector['fixed_organics_price'] = 0;
					$sector['fixed_goods_price'] = 0;
					$sector['fixed_energy_price'] = 0;
					$sector['ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
				}
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['ogp']--;
				$fixedorganics--;
			}
		}

		// Goods port placement
		$fixedgoods = floor($fixedtotal / 4);
		$goodsleft = $_POST['gop'] - $fixedgoods;
		TextFlush("\n<br>Placing $fixedgoods Fixed Price goods ports <br>\n");

		while ($_POST['gop'] > 0)
		{
			if($fixedgoods == 0){
				TextFlush("\n<br>Placing $goodsleft Non-Fixed Price goods ports <br>\n");
				$fixedgoods--;
			}

			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$gop_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($gop_sector,__LINE__,__FILE__);

			if (!$gop_sector->EOF)
			{
				$sector['port_type'] = 'goods';
				$sector['port_organics'] = $initborganics;
				$sector['port_ore'] = $initbore;
				$sector['port_goods'] = $initsgoods;
				$sector['port_energy'] = $initbenergy;
				$sector['organics_price'] = 0;
				$sector['ore_price'] = 0;
				$sector['energy_price'] = 0;
				$sector['goods_price'] = 0;
				$sector['trade_date'] = date("Y-m-d H:i:s");
				if($fixedgoods > 0){
					$sector['fixed_price'] = 1;
					$sector['fixed_goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['fixed_ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['fixed_organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['fixed_energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
					$sector['ore_price'] = $sector['fixed_ore_price'];
					$sector['organics_price'] = $sector['fixed_organics_price'];
					$sector['goods_price'] = $sector['fixed_goods_price'];
					$sector['energy_price'] = $sector['fixed_energy_price'];
			}else{
					$sector['fixed_price'] = 0;
					$sector['fixed_ore_price'] = 0;
					$sector['fixed_organics_price'] = 0;
					$sector['fixed_goods_price'] = 0;
					$sector['fixed_energy_price'] = 0;
					$sector['ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
				}
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['gop']--;
				$fixedgoods--;
			}
		}
	
		// Energy port placement
		$fixedenergy = floor($fixedtotal / 4);
		$energyleft = $_POST['enp'] - $fixedenergy;
		TextFlush("\n<br>Placing $fixedenergy Fixed Price energy ports <br>\n");

		while ($_POST['enp']-1 > 0) // Because Wolf-359 is an energy port and counts towards the total.
		{
			if($fixedenergy == 0){
				TextFlush("\n<br>Placing $energyleft Non-Fixed Price energy ports <br>\n");
				$fixedenergy--;
			}

			$sector='';
			$random_sector = mt_rand(5, $_POST['sektors']); // 3 because you cant include the first three sectors.
			$enp_sector = $db->SelectLimit("SELECT port_type, sector_id ".
										 "FROM $dbtables[universe] ".
										 "WHERE $dbtables[universe].port_type='none' ".
										 "AND $dbtables[universe].sector_id=$random_sector",1);
			db_op_result($enp_sector,__LINE__,__FILE__);

			if (!$enp_sector->EOF)
			{
				$sector['port_type'] = 'energy';
				$sector['port_organics'] = $initborganics;
				$sector['port_ore'] = $initbore;
				$sector['port_goods'] = $initbgoods;
				$sector['port_energy'] = $initsenergy;
				$sector['organics_price'] = 0;
				$sector['ore_price'] = 0;
				$sector['goods_price'] = 0;
				$sector['energy_price'] = 0;
				$sector['trade_date'] = date("Y-m-d H:i:s");
				if($fixedenergy > 0){
					$sector['fixed_price'] = 1;
					$sector['fixed_energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
					$sector['fixed_ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['fixed_organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['fixed_goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['ore_price'] = $sector['fixed_ore_price'];
					$sector['organics_price'] = $sector['fixed_organics_price'];
					$sector['goods_price'] = $sector['fixed_goods_price'];
					$sector['energy_price'] = $sector['fixed_energy_price'];
				}else{
					$sector['fixed_price'] = 0;
					$sector['fixed_ore_price'] = 0;
					$sector['fixed_organics_price'] = 0;
					$sector['fixed_goods_price'] = 0;
					$sector['fixed_energy_price'] = 0;
					$sector['ore_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 1.33);
					$sector['organics_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 3);
					$sector['goods_price'] = mt_rand(0, $fixed_start_pricerange);
					$sector['energy_price'] = floor(mt_rand(0, $fixed_start_pricerange) / 5);
				}
				sector_todb($sector,"Updat",$random_sector,1);
				$_POST['enp']--;
				$fixedenergy--;
			}
		}

		sql_insert_identity_off($dbtables['universe']);

		// build a form for the next stage
		echo "<br><br>\n";
		echo "<form action=create_universe.php method=post>";
		echo "<input type=hidden name=step value=7>";
		echo "<input type=hidden name=nump value=$_POST[nump]>";
		echo "<input type=hidden name=basedcolonists value=$basedcolonists>";
		echo "<input type=hidden name=minbasedlevel value=$minbasedlevel>";
		echo "<input type=hidden name=maxbasedlevel value=$maxbasedlevel>";
		echo "<input type=hidden name=numbp value=$_POST[numbp]>";
		echo "<input type=hidden name=linksper value=$_POST[linksper]>";
		echo "<input type=hidden name=twoways value=$_POST[twoways]>";
		echo "<input type=hidden name=fedsecs value=$_POST[fedsecs]>";
		echo "<input type=hidden name=swordfish value=$_POST[swordfish]>";
		echo "<input type=hidden name=sektors value=$_POST[sektors]>";
		echo "<input type=submit value=Continue>";
		echo "</form>";
		break;

// Stage 7, Planets-R-Us
	case "7":

		$debug_query = $db->Execute("INSERT INTO $dbtables[config_values] (name, value, description, section) VALUES ('totalfedsectors','$_POST[fedsecs]', 'Total number of Mornoc Alliance Sectors', 'totalfedsectors');");

		$max_credits = ((phpChangeDelta(90, 0) * 7) * $planet_credit_multi) + $base_credits;

		TextFlush("Inserting Alliance homeworld: Mornoc Prime "); //Fed homeworld - Earth
		$debug_query = $db->Execute("INSERT INTO $dbtables[planets] 
				(planet_id, 
				sector_id, 
				name, 
				organics,
				ore, 
				goods,
				energy,
				colonists,
				credits,
				computer,
				sensors,
				beams,
				torp_launchers,
				torps,
				shields,
				jammer,
				armour,
				armour_pts,
				cloak,
				fighters,
				owner,
				team,
				base,
				team_cash,
				defeated,
				prod_organics,
				prod_ore,
				prod_goods,
				prod_energy,
				prod_fighters,
				prod_torp,
				cargo_power,
				cargo_hull,
				computer_normal,
				sensors_normal,
				beams_normal,
				torp_launchers_normal,
				shields_normal,
				jammer_normal,
				armour_normal,
				cloak_normal,
				max_credits
				)
				VALUES (" .
				"1, " .				//planet_id
				"1, " .				//sector_id
				"'Mornoc Prime', " .			//name
				"10000000, " .				//organics
				"0," .				 //ore
				"0, " .				//goods
				"10000000, " .				//energy
				"10000000, " .				//colonists
				"0, " .				//credits
				"90, " .				//computer
				"90, " .				//sensors
				"90, " .				//beams
				"90, " .				//torp_launchers
				"10000000, " .				//torps
				"90, " .				//shields
				"90, " .				//jammer
				"90, " .				//armour
				"10000000, " .				//armour_pts
				"0, " .				//cloak
				"10000000, " .				//fighters
				"3, " .				//owner
				"0, " .				//team
				"'Y', " .				//base
				"'Y', " .				//team cash
				"'N', " .				//defeated
				"25, " .				//prod_organics
				"0, " .				//prod_ore
				"0, " .				//prod_goods
				"25, " .				//prod_energy
				"25, " .				//prod_fighters
				"25, " .				//prod_torp
				"0,0, " .				//cargo stuff
				"90, " .				//computer
				"90, " .				//sensors
				"90, " .				//beams
				"90, " .				//torp_launchers
				"90, " .				//shields
				"90, " .				//jammer
				"90, " .				//armour
				"127, " .				//cloak
				"$max_credits " .				//max_credits
				")");
		db_op_result($debug_query,__LINE__,__FILE__);

		TextFlush("Creating $_POST[nump] planets <br>");

		while ($_POST['nump'] >0)
		{
			$random_sector = mt_rand(2, $_POST['sektors']);
			$planetary_sector = $db->SelectLimit("SELECT $dbtables[universe].sector_id, $dbtables[universe].star_size, ".
												"$dbtables[universe].zone_id, $dbtables[zones].allow_planet ".
												"FROM $dbtables[universe], $dbtables[zones] ".
												"WHERE $dbtables[universe].sector_id=$random_sector AND ".
												"$dbtables[zones].allow_planet='Y' AND $dbtables[universe].zone_id!='2' AND $dbtables[universe].zone_id!='3' AND ".
												"$dbtables[universe].star_size!='0'",1);
			$silent=1;
			db_op_result($planetary_sector,__LINE__,__FILE__);
			if (!$planetary_sector->EOF)
			{
				$debug_query = $db->Execute("SELECT * from $dbtables[planets] where sector_id=$random_sector");
				db_op_result($debug_query,__LINE__,__FILE__);

				$num_planets_in_sector = $debug_query->RecordCount();
				$num_ok_planets = $planetary_sector->fields['star_size'] - $num_planets_in_sector;
			 
				if ($num_ok_planets > $_POST['nump'])
				{
					$num_ok_planets = $_POST['nump'];
				}

				if ($num_ok_planets > 0)
				{
					$random_num_planets = mt_rand(0, $num_ok_planets);

					while ($random_num_planets > 0)
					{
						// Select an empty record from the database
						$sql = "SELECT * FROM $dbtables[planets] WHERE planet_id = -1";

						// Execute the query and get the empty recordset
						$rs = $db->Execute($sql);

						// Initialize an array to hold the record data to insert
						$record = array();

						// Set the values for the fields in the record
						$record["colonists"] = 0;
						$record["owner"] = 0;
						$record["team"] = 0;
						$record["prod_ore"] = $default_prod_ore;
						$record["prod_organics"] = $default_prod_organics;
						$record["prod_goods"] = $default_prod_goods;
						$record["prod_energy"] = $default_prod_energy;
						$record["prod_fighters"] = $default_prod_fighters;
						$record["prod_torp"] = $default_prod_torp;
						$record["sector_id"] = $random_sector;
						$record["max_credits"] = $base_credits;

						// Pass the empty recordset and the array containing the data to insert
						// into the GetInsertSQL function. The function will process the data and return
						// a fully formatted insert sql statement.
						$insertSQL	= $db->GetInsertSQL($rs, $record);
						$debug_query = $db->Execute($insertSQL);

						if ($_POST['nump'] > 1)
						{
							$silent = 1;
						}
						else
						{
							$silent = 0;
						}

						db_op_result($debug_query,__LINE__,__FILE__);
						$_POST['nump']--;
						$random_num_planets--;
					}
				}
			}
		}

		$password = substr($adminpass, 0, $maxlen_password);
		$silent = 1;

		echo "<BR>Creating Alliance Leader Player ";
		newplayer($admin_mail, 'Alliance Leader', $password, "The Scourge", 1);
		$query = "UPDATE $dbtables[ships] SET class=99, hull=10, engines=99, power=99, computer=99,
			  sensors=99, beams=99, armour=99, cloak=99, torp_launchers=99, shields=99, ecm=99,
			  hull_normal=10, engines_normal=99, power_normal=99, computer_normal=99, ecm_normal=99,
			  sensors_normal=99, beams_normal=99, armour_normal=99, cloak_normal=99, torp_launchers_normal=99, shields_normal=99, fighters=7050392822843068800,
			  torps=7050392822843068800, armour_pts=7050392822843068800 , dev_emerwarp=999, dev_minedeflector=0, dev_escapepod='Y',
			  dev_fuelscoop='Y', dev_nova='Y', energy=35251964114215347200  WHERE ship_id=1";
		$debug_query = $db->Execute("$query");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "<BR>Creating Independent Player ";
		newplayer('Independent', 'Independent', $password, "Independent", 2);
		$debug_query = $db->Execute("INSERT INTO $dbtables[zones] (zone_name, ".
									"owner, team_zone, allow_attack, ".
									"allow_planetattack, allow_warpedit, ".
									"allow_planet, allow_trade, allow_defenses, ".
									"max_hull, zone_color) VALUES(" .
//									"''," .			 //zone_id		-	not needed
									"'Independent Territory'," .	//zone_name
									"'2'," .	 //owner
									"'N'," .			//team_zone
									"'Y'," .			//allow_attack
									"'Y'," .			//allow_planetattack
									"'Y'," .			//allow_warpedit
									"'Y'," .			//allow_planet
									"'Y'," .			//allow_trade
									"'L'," .			//allow_defenses
									"0," .				//max_hull
									"'#ff00ff'" .				//indy color
									")");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "<BR>Creating Mornoc Alliance Player <BR></B></center>";
		newplayer('Mornoc Alliance', 'Mornoc Alliance', $password, "Mornoc Alliance", 3);
		$query = "UPDATE $dbtables[ships] SET class=100, hull=10, engines=99, power=99, computer=99,
			  sensors=99, beams=99, armour=99, cloak=0, torp_launchers=99, shields=99, ecm=99,
			  hull_normal=10, engines_normal=99, power_normal=99, computer_normal=99, ecm_normal=99,
			  sensors_normal=99, beams_normal=99, armour_normal=99, cloak_normal=0, torp_launchers_normal=99, shields_normal=99, fighters=1000000000000000,
			  torps=1000000000000000, armour_pts=1000000000000000 , dev_emerwarp=999, dev_minedeflector=0, dev_escapepod='Y',
	dev_fuelscoop='Y', dev_nova='Y', energy=1000000000000000000 WHERE ship_id=3";
		$silent = 0;
	$debug_query = $db->Execute("$query");
		db_op_result($debug_query,__LINE__,__FILE__);


		TextFlush("<br>Creating $_POST[numbp] based and independant planets <br>");
	$silent = 1;
		while ($_POST['numbp'] >0)
		{
			$random_sector = mt_rand(2, $_POST['sektors']);
			$planetary_sector = $db->SelectLimit("SELECT $dbtables[universe].sector_id, $dbtables[universe].star_size, ".
												"$dbtables[universe].zone_id, $dbtables[zones].allow_planet ".
												"FROM $dbtables[universe], $dbtables[zones] ".
												"WHERE $dbtables[universe].sector_id=$random_sector AND ".
												"$dbtables[zones].allow_planet='Y' AND $dbtables[universe].zone_id!='2' AND $dbtables[universe].zone_id!='3' AND ".
												"$dbtables[universe].star_size!='0'",1);
			$silent=1;
			db_op_result($planetary_sector,__LINE__,__FILE__);
			if (!$planetary_sector->EOF)
			{
				$debug_query = $db->Execute("SELECT * from $dbtables[planets] where sector_id=$random_sector");
				db_op_result($debug_query,__LINE__,__FILE__);

				$num_planets_in_sector = $debug_query->RecordCount();
				$num_ok_planets = $planetary_sector->fields['star_size'] - $num_planets_in_sector;
			 
				if ($num_ok_planets > $_POST['numbp'])
				{
					$num_ok_planets = $_POST['numbp'];
				}

				if ($num_ok_planets > 0)
				{
					$random_num_planets = mt_rand(0, $num_ok_planets);

					while ($random_num_planets > 0)
					{
						// Select an empty record from the database
						$sql = "SELECT * FROM $dbtables[planets] WHERE planet_id = -1";

						// Execute the query and get the empty recordset
						$rs = $db->Execute($sql);

						// Initialize an array to hold the record data to insert
						$record = array();

						// Set the values for the fields in the record

					// Create Planet Name
					$Sylable1 = array("Ak","Al","Ar","B","Br","D","F","Fr","G","Gr","Gv","K","Kr","N","Ol","Om","P","Qu","R","S","Z","Ah","At","As","Bh","Bl","Dh","Fl","Fh","Gh","Gl","Kl","Ks","Nr","Oh","Ok","Pu","Qh","Rl","Ss","Zl","C","Cr","Ch","Cl","E","El","Er","H","I","J","L","M","T","Tl","Th","Ts","U","Ur","V","Vl","W","Wl","Wr","X","Y");
					$Sylable2 = array("a","ar","aka","aza","e","el","i","in","int","ili","ish","ido","ir","o","oi","or","os","ov","u","un");
					$Sylable3 = array("ag","al","ak","ba","dar","g","ga","k","ka","kar","kil","l","n","nt","ol","r","s","ta","til","x");
					$Sylable4 = array("a","ar","aka","aza","e","el","i","in","int","ili","ish","ido","ir","o","oi","or","os","ov","u","un");
					$sy1roll = mt_rand(0,63);
					$sy2roll = mt_rand(0,19);
					$sy3roll = mt_rand(0,19);
					$sy4roll = mt_rand(0,19);

					$record["name"] = $Sylable1[$sy1roll] . $Sylable2[$sy2roll] . $Sylable3[$sy3roll] . $Sylable4[$sy4roll];
					$record["colonists"] = floor((($basedcolonists/100) * $colonist_limit) * 0.75);
					$record["owner"] = 2;
					$record["team"] = 0;
					$record["base"] = "Y";
					$record["prod_ore"] = min(10, floor($default_prod_ore / 2));
					$record["prod_organics"] = min(13, floor($default_prod_organics / 1.5));
					$record["prod_goods"] = min(10, floor($default_prod_goods / 2));
					$record["prod_energy"] = $default_prod_energy;
					$record["prod_fighters"] = $default_prod_fighters;
					$record["prod_torp"] = $default_prod_torp;
					$record["sector_id"] = $random_sector;

					$record["computer"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["sensors"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["beams"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["torp_launchers"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["shields"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["jammer"] = mt_rand($minbasedlevel, $maxbasedlevel);
					$record["cloak"] = mt_rand($minbasedlevel, $maxbasedlevel);

					$record["computer_normal"] = $record["computer"];
					$record["sensors_normal"] = $record["sensors"];
					$record["beams_normal"] = $record["beams"];
					$record["torp_launchers_normal"] = $record["torp_launchers"];
					$record["shields_normal"] = $record["shields"];
					$record["jammer_normal"] = $record["jammer"];
					$record["cloak_normal"] = $record["cloak"];

					$record["torps"] = NUM_TORPEDOES($record["torp_launchers"]) + mt_rand(1, NUM_TORPEDOES($record["torp_launchers"]));
					$record["fighters"] = NUM_FIGHTERS($record["computer"]) + mt_rand(1, NUM_FIGHTERS($record["computer"]));
					$record["armour_pts"] = NUM_ARMOUR(floor(($record["computer"] + $record["sensors"] + $record["beams"] + $record["torp_launchers"] + $record["shields"] + $record["jammer"] + $record["cloak"]) / 7));
					$record["armour"] = floor(($record["computer"] + $record["sensors"] + $record["beams"] + $record["torp_launchers"] + $record["shields"] + $record["jammer"] + $record["cloak"]) / 7);
					$record["energy"] = NUM_BEAMS($record["beams"]) + NUM_SHIELDS($record["shields"]) + mt_rand(1, NUM_BEAMS($record["beams"]) + NUM_SHIELDS($record["shields"]));

					$max_credits = phpChangeDelta($record['computer'], 0) + phpChangeDelta($record['sensors'], 0) + phpChangeDelta($record['beams'], 0) + phpChangeDelta($record['torp_launchers'], 0) + phpChangeDelta($record['shields'], 0) + phpChangeDelta($record['jammer'], 0) + phpChangeDelta($record['cloak'], 0);
					$record["max_credits"] = ($max_credits * $planet_credit_multi) + $base_credits;

					if(mt_rand(1, 10000) < 5000){
						$fighter_query = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$random_sector and defence_type='F'");
						$fighter_id = $fighter_query->fields['defence_id'];

						$numfighters = mt_rand(1, NUM_FIGHTERS($record["computer"]));
						if ($fighter_id > 0)
						{
							$fighter_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $numfighters " .
														"where defence_id = $fighter_id");
							db_op_result($fighter_query,__LINE__,__FILE__);
						}
						else
						{
							$fighter_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
														"(player_id, sector_id, defence_type, quantity) values " .
														"(2, $random_sector, 'F', $numfighters)");
							db_op_result($fighter_query,__LINE__,__FILE__);
						}

						if(mt_rand(1, 10000) < 5000){
							$mine_query = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$random_sector and defence_type = 'M'");
							$mine_id = $mine_query->fields['defence_id'];

							$nummines = mt_rand(1, NUM_TORPEDOES($record["torp_launchers"]));
							if ($mine_id > 0)
							{
								$mine_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $nummines " .
															"where defence_id = $mine_id");
								db_op_result($mine_query,__LINE__,__FILE__);
							}
							else
							{
								$mine_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
															"(player_id,sector_id,defence_type,quantity) values " .
															"(2, $random_sector, 'M', $nummines)");
								db_op_result($mine_query,__LINE__,__FILE__);
							}
						}
					}

					// Pass the empty recordset and the array containing the data to insert
					// into the GetInsertSQL function. The function will process the data and return
					// a fully formatted insert sql statement.
					$insertSQL	= $db->GetInsertSQL($rs, $record);
					$debug_query = $db->Execute($insertSQL);
					$planet_id = $db->Insert_ID();

					$stamp = "2100-01-01 10:00:00";
					$embezzel_delay = "2200-01-01 10:00:00";
					$new_percet = (mt_rand(1, 100) / 100) * $dig_birthinc_max;
					$debug_query = $db->Execute("INSERT INTO $dbtables[dignitary] (dig_id, active, owner_id, planet_id, ship_id, job_id, percent, active_date, reactive_date) values ('','Y',2,'$planet_id','2','4','$new_percet', '$stamp', '$embezzel_delay')");
					db_op_result($debug_query,__LINE__,__FILE__);
					calc_ownership($random_sector);

					if ($_POST['numbp'] > 1)
					{
						$silent = 1;
					}
					else
					{
						$silent = 0;
					}

					db_op_result($debug_query,__LINE__,__FILE__);
					$_POST['numbp']--;
					$random_num_planets--;
				}
			}
		}
	}

		TextFlush("<br>Generating warp links for sector 1 to 499<br>\n");
	$silent = 1;
		for ($i=1; $i<=$_POST['sektors']; $i++)
		{
			if(!($i % 500)){
				if($i + 499 > $_POST['sektors'])
					$end = $_POST['sektors'] - $i;
				else
					$end = 499;
				TextFlush("Generating warp links for sector $i to " . ($i + $end) . "<br>\n");
			}
			$numlinks = mt_rand(0,$_POST['linksper']);
			for ($j=0; $j<$numlinks; $j++)
			{
				$destination = mt_rand(2,$_POST['sektors']);
				$result4 = $db->Execute("SELECT * FROM $dbtables[links] where link_start=$i and link_dest=$destination");
				db_op_result($result4,__LINE__,__FILE__);

				$totalcount = $result4->RecordCount();
				if($totalcount == 0)
					$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES ($i,$destination);");
				$link_odds = mt_rand(0,100);
				if ($link_odds < $_POST['twoways'])
				{
					$result4 = $db->Execute(" SELECT * FROM $dbtables[links] where link_start=$destination and link_dest=$i");
					db_op_result($result4,__LINE__,__FILE__);

					$totalcount = $result4->RecordCount();
					if($totalcount == 0)
						$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES ($destination,$i);");
				}
			}
		}

		// Put in the sector 1, 2, 3 and 4 warp loop.
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (1,2);");
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (2,3);");
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (3,2);");
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (3,4);");
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (4,3);");
	$silent = 0;
		$debug_query = $db->Execute("INSERT INTO $dbtables[links] (link_start, link_dest) VALUES (2,1);");
	db_op_result($debug_query,__LINE__,__FILE__);

		echo "<br><br>\n";
		echo "<form action=create_universe.php method=post>";
		echo "<input type=hidden name=step value=8>";
		echo "<input type=hidden name=swordfish value=$_POST[swordfish]>";
		echo "<input type=submit value=Continue>";
		echo "</form>";
		break;

// Stage 8, Let there be life
	case "8":
		TextFlush ("<B><BR>Configuring game scheduler<BR></B>");

		echo "<BR>Update ticks will occur every $sched_ticks minutes<BR>";
		$filevar = "sched_file";

		$stamp = date("Y-m-d H:i:s");

		echo "Repair/Optimize Database every $sched_repair minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_repair, 0, 'sched_repair.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Turns will occur every 1 minute ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, 1, 0, 'sched_turns.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "The NPC will play every $sched_npc minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_npc, 0, 'sched_npc.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Interests on igb accounts will be accumulated every $sched_igb minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_igb, 0, 'sched_igb.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "News will be generated every $sched_news minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_news, 0, 'sched_news.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Planets will generate production every $sched_planets minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_planets, 0, 'sched_planets.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Spies will act every $sched_spies minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_spies, 0, 'sched_spies.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Ports will regenerate every $sched_ports minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_ports, 0, 'sched_ports.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Ships will be towed from fed sectors every $sched_tow minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_tow, 0, 'sched_tow.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Rankings will be generated every $sched_ranking minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_ranking, 0, 'sched_ranking.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Sector Defences will degrade every $sched_degrade minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_degrade, 0, 'sched_degrade.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "The planetary apocalypse will occur every $sched_apocalypse minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		// AATrade master server list
		echo "The public list updater will occur every 60 minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, 60, 0, 'aatrade_ls_client.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Dignataries will act every $sched_dig minutes ";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_dig, 0, 'sched_dig.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Planetary independance will occur every $sched_independance minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_independance, 0, 'sched_independance.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Planetary will trade every $sched_trade minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_trade, 0, 'sched_trade.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Ship storage fees calculated every $sched_shipstorage minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_shipstorage, 0, 'sched_shipstorage.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);
		
		echo "Probes Move every $sched_probe minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_probe, 0, 'sched_probe.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Planetary Auto Trades every $sched_autotrade minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_autotrade, 0, 'sched_autotrade.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Prune log, news and dead players every $sched_prune minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_prune, 0, 'sched_prune.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Move Mornoc Alliance ship every $sched_federation minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_federation, 0, 'sched_federation.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Expand Universe every $sched_expansion minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_expansion, 0, 'sched_expanding.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Move Debris every $sched_debris minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_debris, 0, 'sched_debris.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Upgrade Independent planets every $sched_indy_upgrade minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_indy_upgrade, 0, 'sched_indy_upgrade.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		echo "Backup Database every $sched_backup minutes";
		$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('Y', 0, $sched_backup, 0, 'sched_backup.php', '', '$stamp')");
		db_op_result($debug_query,__LINE__,__FILE__);

		$creating = 1;
		$reset_date = date("Y-m-d");
		$debug_query = $db->Execute("INSERT INTO $dbtables[config_values] (name, value, description, section) VALUES ('reset_date','$reset_date', 'Date the server was last reset', 'Reset Date')");

		include ("aatrade_ls_client.php");

		echo "<B><BR>Configuring ship types<p></B>";

		$shipdata = file("config/shiptypes.ini");

		for($i = 0; $i < count($shipdata); $i += 35){
			$fields = "";
			$fielddata = "";
			for($element = 0; $element < 34; $element++){
				$variable = explode("=", $shipdata[$i + $element], 2);
				$variable[0] = trim($variable[0]);
				$variable[1] = trim($variable[1]);
				$$variable[0] = $variable[1];
				$fields .= $variable[0];
				$fielddata .= $variable[1];
				if($element != 33){
					$fields .= ", ";
					$fielddata .= ", ";
				}
//				echo $variable[0] . " = " . $variable[1] . "<br>";
			}
//			echo "<br>";

			echo "Inserting ship type: $name <br>";
			$silent = 0;
			$server_query = $db->Execute("INSERT INTO $dbtables[ship_types] ($fields) VALUES ($fielddata)");
			db_op_result($server_query,__LINE__,__FILE__);
			$silent = 1;
		}

		echo "<br>Inserting news of Universe Creation ";
		$stamp = date("Y-m-d H:i:s");
		insert_news("", 1, "creation");

		$password = substr($adminpass, 0, $maxlen_password);
		echo "<BR><BR><center><B>Your admin login Password: $password<BR>";

		TextFlush("<BR>Enter this URL to access Admin Functions <a href=\"http://" . $_SERVER['HTTP_HOST'] . $gamepath . "/admin.php\">http://" . $_SERVER['HTTP_HOST'] . $gamepath . "/admin.php</a>");
		TextFlush("<br><BR><center><BR><B>Congratulations! Universe created successfully.<BR>");
		TextFlush("Click <A HREF=index.php>here</A> to return to the login screen.</B></center>");
		include ("footer.php");
		break;
// Pre-stage, What's the password?
	default:
		echo "<font face=\"verdana\" size=\"2\">\n";
		echo "Welcome to the Rogue Assault Traders Universe creation tool!<br>\n";
		echo "This tool will assist in the creation of a Universe suitable for players.<br>\n";
		echo "Suitable and reasonable defaults have been inserted as suggestions, however, feel free to override them!<br>\n";
		echo "<br>\n";
		echo "In some cases, a field will cause problems/issues if beyond or below a certain range.<br>\n";
		echo "In those cases, we have included what we think the safe range is next to the fields.\n";
		echo "It *IS* possible to ignore those ranges and insert values above or below it.<br><br>\n";
		echo "<font color=yellow>You do so at your own risk - please understand that we will not support your configuration should you do so.</font><br>\n";
		echo "<br><br>\n";
		echo "<br>\n";
		echo "<form action=create_universe.php method=post>";
		echo "Enter the admin password to begin: <input type=password name=swordfish size=20 maxlength=20>&nbsp;&nbsp;";
		echo "<input type=submit value=Submit><input type=hidden name=step value=1>";
		echo "<input type=reset value=Reset>";
		echo "</form>";
		break;
}

$StopTime = $BenchmarkTimer->stop();
$Elapsed = $BenchmarkTimer->elapsed();
TextFlush("<br><br>\nElapsed Time - $Elapsed");

?>
