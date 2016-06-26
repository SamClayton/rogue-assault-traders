<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: scheduler.php

/******************************************************************
* Explanation of the scheduler									*
*																 *
* Here are the scheduler DB fields, and what they are used for :  *
*  - sched_id : Unique ID. Before calling the file responsible	*
*	for the event, the variable $sched_var_id will be set to	 *
*	this value, so the called file can modify the triggering	 *
*	scheduler entry if it needs to.							  *
*																 *
*  - loop : Set this to 'Y' if you want the event to be looped	*
*	endlessly. If this value is set to 'Y', the 'spawn' field is *
*	not used.													*
*																 *
*  - ticks_left : Used internally by the scheduler. It represents *
*	the number of mins elapsed since the last call. ALWAYS set   *
*	this to 0 when scheduling a new event.					   *
*																 *
*  - ticks_full : This is the interval in minutes between		 *
*	different runs of your event. Set this to the frenquency	 *
*	you wish the event to happen. For example, if you want your  *
*	event to be run every three minutes, set this to 3.		  *
*																 *
*  - spawn : If you want your event to be run a certain number of *
*	times only, set this to the number of times. For this to	 *
*	work, loop must be set to 'N'. When the event has been run   *
*	spawn number of times, it is deleted from the scheduler.	 *
*																 *
*  - file : This is the file that will be called when an event	*
*	has been trigerred.										  *
*																 *
*  - extra_info : This is a text variable that can be used to	 *
*	store any extra information concerning the event triggered.  *
*	It will be made available to the called file through the	 *
*	variable $sched_var_extrainfo.							   *
*																 *
* If you are including files in your trigger file, it is important*
* to use include_once() instead of include(), as your file might  *
* be called multiple times in a single execution. If you need to  *
* define functions, you can put them in your own				  *
* include file, with an include statement. THEY CANNOT BE		 *
* DEFINED IN YOUR MAIN FILE BODY. This would cause PHP to issue a *
* multiple function declaration error.							*
*																 *
* End of scheduler explanation									*
******************************************************************/

require_once ("config/config_sched.php");

function scheduler_log($data, $lf){
	global $enable_schedule_log;
	if($enable_schedule_log){
		$stamp = date("Y-m-d H:i:s");
		$filename = $gameroot . "config/scheduler.log";
		$file = fopen($filename,"a") or die ("Failed opening file: enable write permissions for '$filename'");
		fwrite($file,$data . " = " . $stamp . "\n" . $lf); 
		fclose($file);
	}
}

scheduler_log("Scheduler Started","\n");

include ("includes/$db_type-common.php"); // This is where all mysql calls that are common should be moved.
include ("log_definitions.php");
$no_gzip = 1;

$langdir = $default_lang;

function TextFlush($Text="") 
{
	global $adminexecuted;
	if($adminexecuted == 1){
		echo "$Text";
		flush();
	}
}

$title = "System Update";

if($adminexecuted == 1){
	TextFlush("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
		<html>
		 <head>
		  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=$local_charset\">
		  <meta http-equiv=\"Pragma\" content=\"no-cache\">
		  <META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">
		  <title>$title</title>
<style type=\"text/css\">
<!--
body             { font-family: Verdana, Arial, sans-serif; font-size: x-small;}
td                { font-size: 12px; color: #e0e0e0; font-family: verdana; }
-->
</style>
		 </head>
			<body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 background=\"templates/default/images/bgoutspace1.png\" bgcolor=\"#000000\" text=\"#c0c0c0\" link=\"#52ACEA\" vlink=\"#52ACEA\" alink=\"#52ACEA\">
		<table><tr><td>
	");

	TextFlush( "<H1>$title</H1>\n");
}

if ((!isset($swordfish)) || ($swordfish == ''))
{
	$swordfish = '';
}

function get_dirlist($dirPath)
{
	if ($handle = opendir($dirPath)) 
	{
		while (false !== ($file = readdir($handle))) 
			if ($file != "." && $file != "..") 
				$filesArr[] = trim($file);
			closedir($handle);
	}
	return $filesArr; 
}

function db_op_result($query,$served_line,$served_page)
{
	global $db, $dbtables, $silent, $_SERVER, $cumulative, $db_type;

	if (!(!$query->EOF && $query == ''))
	{
		if (!$silent)
		{
			echo "<font color=\"lime\">- operation completed successfully.</font><br>\n";
		}
	}
	else
	{
		$temp_error = $db->ErrorMsg();
		$dberror = "A Database error occurred in " . $served_page . " on line " . ($served_line-1) . " (called from: $_SERVER[PHP_SELF]): " . $temp_error . "";
		$dberror = ereg_replace("'","&#039;",$dberror); // Allows the use of apostrophes.
		adminlog(LOG_RAW, $dberror);
		$cumulative = 1; // For areas with multiple actions needing status - 0 is all good so far, 1 is at least one bad.

		if(strstr(strtolower($temp_error), "can't open file") and strstr(strtolower($temp_error), ".myi") and strstr($temp_error, "145")){
			$deoperror = 1;
			adminlog(LOG_RAW,"Running sched_repair.php to repair table.");
			include ("sched_repair.php");
		}

		if (!$silent)
		{
			echo "<font color=\"red\">- failed to complete database operation in $served_page on line " .($served_line-1). ". Error code follows:\n";
			echo "<hr>\n";
			echo $temp_error;
			echo "<hr>\n";
			echo "</font><br>\n";
		}
	}
}

function mypw($one,$two)
{
	return pow($one*1,$two*1);
}

function NUMBER($number, $decimals = 0)
{
	global $local_number_dec_point;
	global $local_number_thousands_sep;
	return number_format($number, $decimals, $local_number_dec_point, $local_number_thousands_sep);
}

$sf = (bool) ini_get('safe_mode');
if (!$sf)
{
	set_time_limit(600);
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

if ($swordfish == $adminpass)
{
	$filelist = get_dirlist($gameroot);

	for ($c=0; $c<count($filelist); $c++) { 
		$filenameroot =  str_replace(".php", "", $filelist[$c]); 
		if(strstr($filelist[$c], "sched_mod_")){
			$fs = fopen($gameroot.$filelist[$c], "r");
			$items = fgets($fs);
			$items = fgets($fs);
			$name = substr(trim($items), 3);
			$items = fgets($fs);
			$loop = substr(trim($items), 3);
			$items = fgets($fs);
			$ticks_full = substr(trim($items), 3);
			$items = fgets($fs);
			$spawn = substr(trim($items), 3);
			fclose($fs);
			$sched_check = $db->Execute("SELECT * FROM $dbtables[scheduler] where sched_file='$filenameroot.php'");
			if($sched_check->recordcount() == 0){
				TextFlush ("Found New Schedule File: ".$filenameroot."<br>");
				TextFlush ("Inserting Schedule $name every $ticks_full minutes");
				$stamp = date("Y-m-d H:i:s");
				$debug_query = $db->Execute("INSERT INTO $dbtables[scheduler] (loop, ticks_left, ticks_full, spawn, sched_file, extra_info, last_run) VALUES('$loop', 0, $ticks_full, $spawn, '$filenameroot.php', '$name', '$stamp')");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
	}

	$sched_res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] order by sched_id ASC");
	$startschedtime = $sched_res->fields['last_run'];
	$sched_res = $db->Execute("SELECT last_run FROM $dbtables[scheduler] order by sched_id DESC");
	$endschedtime = $sched_res->fields['last_run'];
	$unixdate = strtotime($startschedtime);
	$unixdatecheck = strtotime(date("Y-m-d H:i:s")) - 900;

	$startschedtime = $endschedtime;
	if($startschedtime == $endschedtime || $unixdate < $unixdatecheck)
	{
		$starttime = time();
//		May use these in the future..
//		$db->Execute("LOCK TABLES $dbtables[scheduler], $dbtables[players], $dbtables[adminnews], $dbtables[planets], $dbtables[sector_defence], $dbtables[players], $dbtables[ibank_accounts], $dbtables[ships], $dbtables[news], $dbtables[spies], $dbtables[universe] WRITE");
		$runstamp = date("Y-m-d H:i:s");
		$sched_res = $db->Execute("SELECT * FROM $dbtables[scheduler] order by sched_id ASC");
		if ($sched_res)
		{
			while (!$sched_res->EOF)
			{
				$event = $sched_res->fields;
				$multiplier = ($sched_ticks / $event['ticks_full']) + ($event['ticks_left'] / $event['ticks_full']);
				$multiplier = (int) $multiplier;
				$ticks_left = ($sched_ticks + $event['ticks_left']) % $event['ticks_full'];

				if ($event['loop'] == 'N')
				{
					if ($multiplier > $event[spawn])
					{
						$multiplier = $event[spawn];
					}

					if ($event[spawn] - $multiplier == 0)
					{
						$debug_query = $db->Execute("DELETE FROM $dbtables[scheduler] WHERE sched_id=$event[sched_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
					}
					else
					{
						$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET last_run=last_run, ticks_left=$ticks_left, spawn=spawn-$multiplier WHERE sched_id=$event[sched_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
					}
				}
				else
				{   
					$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET last_run=last_run, ticks_left=$ticks_left WHERE sched_id=$event[sched_id]");
					db_op_result($debug_query,__LINE__,__FILE__);
				}

				$sched_var_id = $event['sched_id'];
				$sched_var_extrainfo = $event['extra_info'];

				$sched_i = 0;
				while ($sched_i < $multiplier)
				{
					if($enable_scheduler == 1 or $event['sched_file'] == "aatrade_ls_client.php"){
						TextFlush();
						scheduler_log("Starting $event[sched_file]","");
						include ("$event[sched_file]");
						scheduler_log("Ending $event[sched_file]","\n");
					}
					else
					{
						TextFlush();
						TextFlush ("Scheduler Disabled $event[sched_file]<br><br>");
					}
					$sched_i++;
				}
				$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET last_run='$runstamp' where sched_id=$event[sched_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
				$sched_res->MoveNext();
			}
		}
	}
	else
	{
		TextFlush ("Previous schedule has not finished executing.<br><br>");
	}
	
	$runtime = time() - $starttime;
	TextFlush ("<p>The scheduler took $runtime seconds to execute.<p>");
	scheduler_log("The scheduler took $runtime seconds to execute.","\n");

	scheduler_log("Scheduler Ended","\n\n");
	if($adminexecuted == 1){
//		include ("footer.php");
		unset ($smarty);
		TextFlush ("<br><br></td></tr></table>");
		TextFlush( "</body></html>\n");
	}
//		May use these in the future..
//		$db->Execute("UNLOCK TABLES");
}
else
{
	scheduler_log("Scheduler Failed","\n");
}

$db->close();
?>
