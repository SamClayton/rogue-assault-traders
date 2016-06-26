<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: navcomp.php

include ("config/config.php");
include ("languages/$langdir/lang_navcomp.inc");
include ("languages/$langdir/lang_check_fighters.inc");
include ("languages/$langdir/lang_check_mines.inc");
include ("languages/$langdir/lang_autoroutes.inc");
$no_gzip = 1;

$title = $l_nav_title;

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

if ((!isset($state)) || ($state == ''))
{
	$state = 0;
}

if (checklogin() or $tournament_setup_access == 1)
{
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

$smarty->assign("templatename", $templatename);

$line_color = $color_line2;
function linecolor()
{
  global $line_color, $color_line1, $color_line2;

  if($line_color == $color_line1)   
   $line_color = $color_line2; 
  else   
   $line_color = $color_line1; 

  return $line_color;
}


function autowarpmove($targetlink)
{
	// *********************************
	// *** SETUP GENERAL VARIABLES  ****
	// *********************************
	global $db, $dbtables, $playerinfo, $shipinfo,$level_factor;
	global $l_chm_youhitsomemines, $l_chm_hehitminesinsector, $l_chm_youlostminedeflectors, $l_chm_youlostallminedeflectors;
	global $l_chm_youhadnominedeflectors, $l_chm_yourshieldshitforminesdmg, $l_chm_yourshieldsaredown, $l_chm_youlostallyourshields;
	global $l_chm_yourarmorhitforminesdmg, $l_chm_yourhullisbreached, $l_chm_hewasdestroyedbyyourmines, $l_chm_luckescapepod;
	global $spy_success_factor;
	global $l_autoroute_missingwarp, $l_autoroute_fighterabort, $l_autoroute_turns, $l_autoroute_noturns;

	$rswarp=0;
	$linkres = $db->Execute ("SELECT * FROM $dbtables[links] WHERE link_start='$shipinfo[sector_id]'");
	if ($linkres > 0)
	{
		while (!$linkres->EOF)
		{
			$row = $linkres->fields;
//echo "Link: $row[link_dest] - Target: $targetlink<br>";
			if($row['link_dest'] == $targetlink){
				// *** OBTAIN SECTOR INFORMATION ***
				$sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$row[link_dest]'");
				$sectrow = $sectres->fields;
				$zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
				$zonerow = $zoneres->fields;
				$rswarp = $targetlink;
			}
			$linkres->MoveNext();
		}
	}

	// *********************************
	// ***** IF NO ACCEPTABLE LINK *****
	// *********************************

	if($rswarp == 0){
		echo " - ".$l_autoroute_missingwarp;
		return "abort";
	}

	if ($targetlink>0)
	{
	$resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' and player_id != '$playerinfo[player_id]' ORDER BY quantity DESC");
		$i = 0;
		$total_sector_fighters = 0;
		$highsensors=0;
		if ($resultf > 0)
		{
			while (!$resultf->EOF)
			{
				$defences[$i] = $resultf->fields;
				$fmowners = $defences[$i]['player_id'];
				
				
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
				{
					$total_sector_fighters += $defences[$i]['quantity'];
					// Get Players ship sensors
					$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
					$ship_owner = $result3->fields;
					if ($ship_owner['sensors'] > $highsensors){
						$highsensors=$ship_owner['sensors'];
					}
					// get planet sensors
					$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors DESC",1);
					$planets = $result4->fields;
					if ($planets['sensors'] > $highsensors){
						$highsensors=$planets['sensors'];
					}
				}
				$i++;
				$resultf->MoveNext();
			}
		}

		$resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M' and player_id != '$playerinfo[player_id]' ");
		$i = 0;
		$total_sector_mines = 0;
		$highsensors=0;
		if ($resultm > 0)
		{
			while (!$resultm->EOF)
			{
				$defences[$i] = $resultm->fields;
				$fmowners = $defences[$i]['player_id'];

				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$mine_owner = $result2->fields;

				if ($mine_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) // Are the mine owner and player are on the same team?
				{
					$total_sector_mines += $defences[$i]['quantity'];
					// Get Players ship sensors
					$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$mine_owner[player_id] and ship_id=$mine_owner[currentship]");
					$ship_owner = $result3->fields;
					if ($ship_owner['sensors'] > $highsensors){
						$highsensors=$ship_owner['sensors'];
					}
					// get planet sensors
					$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$mine_owner[player_id] or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors DESC",1);
					$planets = $result4->fields;
					if ($planets['sensors'] > $highsensors){
						$highsensors=$planets['sensors'];
					}
				}
				$i++;
				$resultm->MoveNext();
			}
		}

		if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
		// ********************************
		// **** DEST LINK HAS DEFENCES ****
		// ********************************
		{
			$success = SCAN_SUCCESS($highsensors, $probeinfo['cloak']);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}
			$roll = mt_rand(1, 100);
			if (($roll < $success)and ($total_sector_fighters>0)) 
			{
				echo $l_autoroute_fighterabort;
				return "abort";
			}

			if ($total_sector_mines>0)
			{
				include("combat_sector_mines.php");
				return "abort";
			}

			$triptime = 1;

		   	if ($triptime == 0 && $targetlink != $shipinfo['sector_id'])
		   	{
				$triptime = 1;
		   	}

			if ($playerinfo['turns'] >= $triptime)
			{
		   		$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
		   		$query="UPDATE $dbtables[ships] SET sector_id=$targetlink " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
				$shipinfo['sector_id'] = $targetlink;
				$l_autoroute_turns2 = str_replace("[triptime]", $triptime, $l_autoroute_turns);
				$l_autoroute_turns2 = str_replace("[targetlink]", $targetlink, $l_autoroute_turns2);
				echo $l_autoroute_turns2;
				return "ok";
   			}else{
				echo $l_autoroute_noturns;
				return "abort";
			}
		}	
		else
		// ********************************
		// **** Safe Move ***
		// ********************************
		{
			$triptime = 1;
			if ($triptime == 0 && $targetlink != $probeinfo['sector_id'])
			{
				$triptime = 1;
			}
			if ($playerinfo['turns'] >= $triptime)
			{
   				$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
	   				   "WHERE player_id=$playerinfo[player_id]";
				$move_result = $db->Execute ("$query");
					$query="UPDATE $dbtables[ships] SET sector_id=$targetlink " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
				$shipinfo['sector_id'] = $targetlink;
				$l_autoroute_turns2 = str_replace("[triptime]", $triptime, $l_autoroute_turns);
				$l_autoroute_turns2 = str_replace("[targetlink]", $targetlink, $l_autoroute_turns2);
				echo $l_autoroute_turns2;
				return "ok";
			}else{
				echo $l_autoroute_noturns;
				return "abort";
			}
		}
	}
}


function autorealspacemove($targetlink)
{
	// *********************************
	// *** SETUP GENERAL VARIABLES  ****
	// *********************************
	global $db, $dbtables, $playerinfo, $shipinfo,$level_factor;
	global $l_chm_youhitsomemines, $l_chm_hehitminesinsector, $l_chm_youlostminedeflectors, $l_chm_youlostallminedeflectors;
	global $l_chm_youhadnominedeflectors, $l_chm_yourshieldshitforminesdmg, $l_chm_yourshieldsaredown, $l_chm_youlostallyourshields;
	global $l_chm_yourarmorhitforminesdmg, $l_chm_yourhullisbreached, $l_chm_hewasdestroyedbyyourmines, $l_chm_luckescapepod;
	global $spy_success_factor;
	global $l_autoroute_missingwarp, $l_autoroute_fighterabort, $l_autoroute_turns, $l_autoroute_noturns;

	 // *** OBTAIN SECTOR INFORMATION ***
	$sectres = $db->Execute ("SELECT sector_id,zone_id FROM $dbtables[universe] WHERE sector_id='$targetlink'");
	$sectrow = $sectres->fields;
	$zoneres = $db->Execute("SELECT zone_id,allow_attack FROM $dbtables[zones] WHERE zone_id=$sectrow[zone_id]");
	$zonerow = $zoneres->fields;

	if ($targetlink>0)
	{
	$resultf = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='F' and player_id != '$playerinfo[player_id]' ORDER BY quantity DESC");
		$i = 0;
		$total_sector_fighters = 0;
		$highsensors=0;
		if ($resultf > 0)
		{
			while (!$resultf->EOF)
			{
				$defences[$i] = $resultf->fields;
				$fmowners = $defences[$i]['player_id'];
				
				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$fighters_owner = $result2->fields;
				if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
				{
					$total_sector_fighters += $defences[$i]['quantity'];
					// Get Players ship sensors
					$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$fighters_owner[player_id] and ship_id=$fighters_owner[currentship]");
					$ship_owner = $result3->fields;
					if ($ship_owner['sensors'] > $highsensors){
						$highsensors=$ship_owner['sensors'];
					}
					// get planet sensors
					$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$fighters_owner[player_id] or  (team > 0 and team=$fighters_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
					$planets = $result4->fields;
					if ($planets['sensors'] > $highsensors){
						$highsensors=$planets['sensors'];
					}
				}
				$i++;
				$resultf->MoveNext();
			}
		}

		$resultm = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id='$targetlink' and defence_type ='M' and player_id != '$playerinfo[player_id]' ");
		$i = 0;
		$total_sector_mines = 0;
		$highsensors=0;
		if ($resultm > 0)
		{
			while (!$resultm->EOF)
			{
				$defences[$i] = $resultm->fields;
				$fmowners = $defences[$i]['player_id'];

				$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$fmowners");
				$mine_owner = $result2->fields;

				if ($mine_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0) // Are the mine owner and player are on the same team?
				{
					$total_sector_mines += $defences[$i]['quantity'];
					// Get Players ship sensors
					$result3 = $db->Execute("SELECT * from $dbtables[ships] where player_id=$mine_owner[player_id] and ship_id=$mine_owner[currentship]");
					$ship_owner = $result3->fields;
					if ($ship_owner['sensors'] > $highsensors){
						$highsensors=$ship_owner['sensors'];
					}
					// get planet sensors
					$result4 = $db->SelectLimit("SELECT * from $dbtables[planets] where (owner=$mine_owner[player_id] or  (team > 0 and team=$mine_owner[team])) and base='Y' and sector_id='$targetlink' order by sensors",1);
					$planets = $result4->fields;
					if ($planets['sensors'] > $highsensors){
						$highsensors=$planets['sensors'];
					}
				}
				$i++;
				$resultm->MoveNext();
			}
		}

		if ($total_sector_fighters>0 || $total_sector_mines>0 || ($total_sector_fighters>0 && $total_sector_mines>0))
		// ********************************
		// **** DEST LINK HAS DEFENCES ****
		// ********************************
		{
			$success = SCAN_SUCCESS($highsensors, $shipinfo['cloak']);
			if ($success < 5)
			{
				$success = 5;
			}

			if ($success > 95)
			{
				$success = 95;
			}

			$roll = mt_rand(1, 100);
			if (($roll < $success)and ($total_sector_fighters>0)) 
			{
				echo $l_autoroute_fighterabort;
				return "abort";
			}

			if ($total_sector_mines>0)
			{
				include("combat_sector_mines.php");
				return "abort";
			}

			$distance = calc_dist($shipinfo['sector_id'],$targetlink);
	   		$shipspeed = mypw($level_factor, $shipinfo['engines']);
		   	$triptime = round($distance / $shipspeed);

		   	if ($triptime == 0 && $targetlink != $shipinfo['sector_id'])
		   	{
				$triptime = 1;
		   	}
			if ($playerinfo['turns'] >= $triptime)
			{
		   		$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
		   		$query="UPDATE $dbtables[ships] SET sector_id=$targetlink " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
				$shipinfo['sector_id'] = $targetlink;
				$l_autoroute_turns2 = str_replace("[triptime]", $triptime, $l_autoroute_turns);
				$l_autoroute_turns2 = str_replace("[targetlink]", $targetlink, $l_autoroute_turns2);
				echo $l_autoroute_turns2;
		   		return "ok";
   			}else{
				echo $l_autoroute_noturns;
				return "abort";
			}
		}
		else
		// ********************************
		// **** Safe Move ***
		// ********************************
		{
		//  Calculate number of turns for RS
			$distance = calc_dist($shipinfo['sector_id'],$targetlink);
			$shipspeed = mypw($level_factor, $shipinfo['engines']);
			$triptime = round($distance / $shipspeed);

			if ($triptime == 0 && $targetlink != $shipinfo['sector_id'])
			{
				$triptime = 1;
			}
			if ($playerinfo['turns'] >= $triptime)
			{
				$query="UPDATE $dbtables[players] SET  turns_used=turns_used+$triptime, turns=turns-$triptime " .
					   "WHERE player_id=$playerinfo[player_id]";
				$move_result = $db->Execute ("$query");
		   		$query="UPDATE $dbtables[ships] SET sector_id=$targetlink " .
					   "WHERE player_id=$playerinfo[player_id]";
		   		$move_result = $db->Execute ("$query");
				$shipinfo['sector_id'] = $targetlink;
				$l_autoroute_turns2 = str_replace("[triptime]", $triptime, $l_autoroute_turns);
				$l_autoroute_turns2 = str_replace("[targetlink]", $targetlink, $l_autoroute_turns2);
				echo $l_autoroute_turns2;
				return "ok";
			}else{
				echo $l_autoroute_noturns;
				return "abort";
			}
		}
	}
}


if ($allow_navcomp)
{
	$computer_tech  = $shipinfo['computer'];

	// Without these here.  You will receive warnings.
	$search_results_echo = NULL;
	$links = NULL;
	$search_depth = NULL;

	switch ($state)
	{
		case "0":
			$autocount = 0;

			$res = $db->Execute("SELECT * FROM $dbtables[autoroutes] WHERE player_id=$playerinfo[player_id] ");
			if($res->RecordCount())
			{
				while(!$res->EOF)
				{
					$autoroute = $res->fields;

					if($autoroute['warp_list'] == "")
						$autoroute['warp_list'] = "None";

					$autolinecolor[$autocount] = linecolor();
					$autorouteid[$autocount] = $autoroute['autoroute_id'];
					$autostart[$autocount] = $autoroute['start_sector'];
					$autoend[$autocount] = $autoroute['destination'];
					$warplist[$autocount] = $autoroute['warp_list'];
					$autodelete[$autocount] = "dismiss[$autocount]";
					$res->MoveNext();
					$autocount++;
				}
			}

			$smarty->assign("title", $title);
			$smarty->assign("warplist", $warplist);
			$smarty->assign("autorouteid", $autorouteid);
			$smarty->assign("autostart", $autostart);
			$smarty->assign("autoend", $autoend);
			$smarty->assign("autodelete", $autodelete);
			$smarty->assign("autolinecolor", $autolinecolor);
			$smarty->assign("autocount", $autocount);
			$smarty->assign("l_autoroute_id", $l_autoroute_id);
			$smarty->assign("l_autoroute_start", $l_autoroute_start);
			$smarty->assign("l_autoroute_destination", $l_autoroute_destination);
			$smarty->assign("l_autoroute_warps", $l_autoroute_warps);
			$smarty->assign("l_autoroute_deleteroute", $l_autoroute_deleteroute);
			$smarty->assign("l_autoroute_noroutes", $l_autoroute_noroutes);

			$smarty->assign("color_header", $color_header);
			$smarty->assign("color_line2", $color_line2);
			$smarty->assign("l_autoroute_title", $l_autoroute_title);
			$smarty->assign("color_line1", $color_line1);
			$smarty->assign("l_autoroute_delete2", $l_autoroute_delete2);
			$smarty->assign("l_autoroute_info", $l_autoroute_info);


			$smarty->assign("l_nav_nocomp", $l_nav_nocomp);
			$smarty->assign("allow_navcomp", $allow_navcomp);
			$smarty->assign("l_submit", $l_submit);
			$smarty->assign("state", $_POST['state']);
			$smarty->assign("search_results_echo", $search_results_echo);
			$smarty->assign("start_sector", $links[0]);
			$smarty->assign("found", $found);
			$smarty->assign("search_depth", $search_depth);
			$smarty->assign("l_nav_answ1", $l_nav_answ1);
			$smarty->assign("l_nav_answ2", $l_nav_answ2);
			$smarty->assign("l_nav_proper", $l_nav_proper);
			$smarty->assign("l_nav_query", $l_nav_query);
			$smarty->assign("l_autoroute_return", $l_autoroute_return);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."navcomp2.tpl");

			break;

		case "1":

			$max_search_depth = round($computer_tech / 5)+2;

			for ($search_depth = 1; $search_depth <= $max_search_depth; $search_depth++)
			{
				$search_query = "SELECT distinct a1.link_start , a1.link_dest \n";
				for ($i = 2; $i<=$search_depth; $i++)
				{
					$search_query = $search_query . " ,a". $i . ".link_dest \n";
				}

				$search_query = $search_query . "FROM $dbtables[links] AS a1 \n";
				for ($i = 2; $i<=$search_depth; $i++)
				{
					$search_query = $search_query . " ,$dbtables[links] AS a". $i . " \n";
				}

				$search_query = $search_query . "WHERE a1.link_start = $shipinfo[sector_id] \n";
				for ($i = 2; $i<=$search_depth; $i++)
				{
					$k = $i-1;
					$search_query = $search_query . " AND a" . $k . ".link_dest = a" . $i . ".link_start \n";
				}

				$search_query = $search_query . " AND a" . $search_depth . ".link_dest = $_POST[stop_sector] \n";
				$search_query = $search_query . " AND a1.link_dest != a1.link_start \n";
				for ($i=2; $i<=$search_depth; $i++)
				{
					$search_query = $search_query . " AND a" . $i . ".link_dest not in (a1.link_dest, a1.link_start ";
					for ($j=2; $j<$i; $j++)
					{
						$search_query = $search_query . ",a".$j.".link_dest ";
					}
					$search_query = $search_query . ")\n";
	 			}

				$search_query = $search_query . "ORDER BY a1.link_start, a1.link_dest ";
				for ($i=2; $i<=$search_depth; $i++)
				{
					$search_query = $search_query . ", a" . $i . ".link_dest";
				}

				//$search_query = $search_query . " \nLIMIT 1";

				// Okay, this is tricky. We need the db returns to be numeric, not associative, so that we 
				// can get a count from it. A good page on it is here: http://php.weblogs.com/adodb_tutorial .
				// We also dont need to set it BACK to the game default, because each page sets it again (by calling config).
				// If someone can think of a way to recode this to not need this line, I would deeply appreciate it!

				$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
				$debug_query = $db->SelectLimit($search_query) or die ("Invalid Query");
				//$debug_query = $db->Execute ($search_query) or die ("Invalid Query");
				$found = $debug_query->RecordCount();
				if ($found > 0)
				{
					break;
				}
			}

			if ($found > 0)
			{
				$links = $debug_query->fields;
				$search_results_echo = '';
				$warp_list = '';
				for ($i=1; $i<$search_depth+1; $i++)
				{
					if ($i==1)
					{
						$search_results_echo = $search_results_echo . " >> " . "<a href=move.php?move_method=warp&sector=$links[$i]>$links[$i]</a>";
					}
					else
					{
						$search_results_echo = $search_results_echo . " >> " . $links[$i];
					}
					if($i <= ($search_depth -1)){
						$warp_list = $warp_list . $links[$i];
						if($i != ($search_depth - 1))
							$warp_list = $warp_list . "|";
					}
				}
			}


			$smarty->assign("title", $title);
			$smarty->assign("l_nav_nocomp", $l_nav_nocomp);
			$smarty->assign("l_nav_pathfnd", $l_nav_pathfnd);
			$smarty->assign("allow_navcomp", $allow_navcomp);
			$smarty->assign("l_submit", $l_submit);
			$smarty->assign("state", $_POST['state']);
			$smarty->assign("search_results_echo", $search_results_echo);
			$smarty->assign("start_sector", $links[0]);
			$smarty->assign("found", $found);
			$smarty->assign("search_depth", $search_depth);
			$smarty->assign("l_nav_answ1", $l_nav_answ1);
			$smarty->assign("l_nav_answ2", $l_nav_answ2);
			$smarty->assign("l_nav_proper", $l_nav_proper);
			$smarty->assign("l_nav_query", $l_nav_query);
			$smarty->assign("destination", $links[$i - 1]);
			$smarty->assign("warp_list", $warp_list);
			$smarty->assign("l_autoroute_createroute", $l_autoroute_createroute);
			$smarty->assign("found", $found);
			$smarty->assign("l_autoroute_return", $l_autoroute_return);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."navcomp.tpl");

			
			break;

		case "dismiss":
			$dismisstotal = 0;
			for($i = 0; $i <$autocount; $i++){
				if(isset($dismiss[$i])){
					$debug_query = $db->Execute("delete from $dbtables[autoroutes] WHERE autoroute_id=$dismiss[$i] and player_id=$playerinfo[player_id] ");
					db_op_result($debug_query,__LINE__,__FILE__);
					$dismisstotal++;
				}
			}
			$smarty->assign("title", $title);
			$smarty->assign("dismisstotal", $dismisstotal);
			$smarty->assign("l_autoroute_deleted", $l_autoroute_deleted);
			$smarty->assign("l_autoroute_return", $l_autoroute_return);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."navcomp_delete.tpl");
			break;

		case "create":
			$debug_query = $db->Execute("INSERT INTO $dbtables[autoroutes] " .
										"(start_sector ,destination ,warp_list ,player_id ) values " .
										"($start_sector,$destination,'$warp_list',$playerinfo[player_id])");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("title", $title);
			$smarty->assign("l_autoroute_created", $l_autoroute_created);
			$smarty->assign("l_autoroute_return", $l_autoroute_return);
			$smarty->assign("l_clickme", $l_clickme);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."navcomp_create.tpl");
			break;

		case "start":

			bigtitle();
			$res = $db->Execute("SELECT * FROM $dbtables[autoroutes] WHERE autoroute_id=$autoroute_id and player_id=$playerinfo[player_id] ");
			$autoroute = $res->fields;

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
			$sector_type = $sector_res->fields['sg_sector'];

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$autoroute[start_sector]");
			$route_type = $sector_res->fields['sg_sector'];

			if($shipinfo['sector_id'] != $autoroute['start_sector']){
				if($sector_type != 1 and $route_type != 1){
					echo "$l_autoroute_realmove $autoroute[start_sector].";
					if(autorealspacemove($autoroute['start_sector']) != "ok")
						break;
				}else{
					echo "$l_autoroute_warpmove ".$autoroute['start_sector'];
					if(autowarpmove($autoroute['start_sector']) != "ok")
						break;
				}
			}

			if($autoroute['warp_list'] != ""){
				$warpsectors = explode("|", $autoroute['warp_list']);
				for($i = 0; $i <count($warpsectors); $i++){
					echo "<br>$l_autoroute_warpmove ".$warpsectors[$i];
					if(autowarpmove($warpsectors[$i]) != "ok")
						break;
				}
			}
			echo "<br>$l_autoroute_warpfinal $autoroute[destination].";
			autowarpmove($autoroute['destination']);
			echo "<br>";
			TEXT_GOTOMAIN();
			break;

		case "reverse":

			bigtitle();
			$res = $db->Execute("SELECT * FROM $dbtables[autoroutes] WHERE autoroute_id=$autoroute_id and player_id=$playerinfo[player_id] ");
			$autoroute = $res->fields;

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
			$sector_type = $sector_res->fields['sg_sector'];

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$autoroute[destination]");
			$route_type = $sector_res->fields['sg_sector'];

			if($shipinfo['sector_id'] != $autoroute['destination']){
				if($sector_type != 1 and $route_type != 1){
					echo "$l_autoroute_realmove $autoroute[destination].";
					if(autorealspacemove($autoroute['destination']) != "ok")
						break;
				}else{
					echo "$l_autoroute_warpmove ".$autoroute['destination'];
					if(autowarpmove($autoroute['destination']) != "ok")
						break;
				}
			}

			if($autoroute['warp_list'] != ""){
				$warpsectors = explode("|", $autoroute['warp_list']);
				for($i = (count($warpsectors) - 1); $i >= 0; $i--){
					echo "<br>$l_autoroute_warpmove ".$warpsectors[$i];
					if(autowarpmove($warpsectors[$i]) != "ok")
						break;
				}
			}
			echo "<br>$l_autoroute_warpfinal $autoroute[start_sector].";
			autowarpmove($autoroute['start_sector']);
			echo "<br>";
			TEXT_GOTOMAIN();
			break;
	}
}

include ("footer.php");

?>
