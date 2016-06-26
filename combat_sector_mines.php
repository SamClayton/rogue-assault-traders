<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: combat_sector_mines.php

if (preg_match("/combat_sector_mines.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

include ("languages/$langdir/lang_check_mines.inc");
// Lets blow up some mines!
$ok = 0;
$totalmines = $total_sector_mines;
if ($totalmines > 1)
{
	$roll = mt_rand(1,$totalmines);
	if($roll < 0)
		$roll = floor($totalmines * (rand(1, 10000) * 0.0001));
}
else
{
	$roll = 1;
}

if($shipinfo['hull'] > $mine_hullsize)
{

echo "<table align=\"center\" bgcolor=#000000 border=1><tr><td align=center><font color=\"#ff0000\"><b>";
$totalmines = $totalmines - $roll;
$l_chm_youhitsomemines = str_replace("[chm_roll]", $roll, $l_chm_youhitsomemines);
echo "$l_chm_youhitsomemines<br>";
playerlog($playerinfo['player_id'], LOG_HIT_MINES, "$roll|$destination");

$l_chm_hehitminesinsector = str_replace("[chm_playerinfo_character_name]", $playerinfo['character_name'], $l_chm_hehitminesinsector);
$l_chm_hehitminesinsector = str_replace("[chm_roll]", $roll, $l_chm_hehitminesinsector);
$l_chm_hehitminesinsector = str_replace("[chm_sector]", $destination, $l_chm_hehitminesinsector);
message_defence_owner($destination,"$l_chm_hehitminesinsector");

if ($shipinfo['dev_minedeflector'] >= $roll)
{
	$l_chm_youlostminedeflectors = str_replace("[chm_roll]", $roll, $l_chm_youlostminedeflectors);
	echo "$l_chm_youlostminedeflectors<br>";
	$debug_query = $db->Execute("UPDATE $dbtables[ships] set dev_minedeflector=dev_minedeflector-$roll where ship_id=$shipinfo[ship_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
}
else
{
	if ($shipinfo['dev_minedeflector'] > 0)
	{
		echo "$l_chm_youlostallminedeflectors<br>";
	}
	else
	{
		echo "$l_chm_youhadnominedeflectors<br>";
	}

	$mines_left = $roll - $shipinfo['dev_minedeflector'];
	$playershields = NUM_SHIELDS($shipinfo['shields']);

	if ($playershields > $shipinfo['energy'])
	{
		$playershields = $shipinfo['energy'];
	}

   if ($playershields >= $mines_left)
   {
	   $l_chm_yourshieldshitforminesdmg = str_replace("[chm_mines_left]", $mines_left, $l_chm_yourshieldshitforminesdmg);
	   echo "$l_chm_yourshieldshitforminesdmg<br>";
	   $debug_query = $db->Execute("UPDATE $dbtables[ships] set energy=energy-$mines_left,dev_minedeflector=0 WHERE ship_id=$shipinfo[ship_id]");
	   db_op_result($debug_query,__LINE__,__FILE__);
	   if ($playershields == $mines_left) 
	   {
		   echo "$l_chm_yourshieldsaredown<br>";
	   }
   }
   else
   {
	   echo "$l_chm_youlostallyourshields<br>";
	   $mines_left = $mines_left - $playershields;
	   if ($shipinfo['armour_pts'] >= $mines_left)
	   {
		   $l_chm_yourarmorhitforminesdmg = str_replace("[chm_mines_left]", $mines_left,$l_chm_yourarmorhitforminesdmg);
		   echo "$l_chm_yourarmorhitforminesdmg<br>";
		   $debug_query = $db->Execute("UPDATE $dbtables[ships] SET armour_pts=armour_pts-$mines_left,energy=0,dev_minedeflector=0 WHERE ship_id=$shipinfo[ship_id]");
		   db_op_result($debug_query,__LINE__,__FILE__);
		   if ($shipinfo['armour_pts'] == $mines_left) 
		   {
			   echo "$l_chm_yourhullisbreached<br>";
		   }
	   }
	   else
	   {
		   $pod = $shipinfo['dev_escapepod'];
		   playerlog($playerinfo['player_id'], LOG_SHIP_DESTROYED_MINES, "$destination|$pod");
		   $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_playerinfo_character_name]",$playerinfo['character_name'], $l_chm_hewasdestroyedbyyourmines);
		   $l_chm_hewasdestroyedbyyourmines = str_replace("[chm_sector]", "<a href=move.php?move_method=real&engage=1&destination=$destination>$destination</a>",$l_chm_hewasdestroyedbyyourmines);
		   message_defence_owner($destination,"$l_chm_hewasdestroyedbyyourmines");
		   echo "$l_chm_yourshiphasbeendestroyed<br><br>";

			$move_failed = 1;

		   if ($shipinfo['dev_escapepod'] == "Y")
		   {
			   $rating = round($playerinfo['rating']/2);
			   echo "$l_chm_luckescapepod<br><br>";
			   player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $rating, 0, 0);

			   if ($spy_success_factor)
			   {
				   spy_ship_destroyed($shipinfo['ship_id'],0);
			   }

			   if ($dig_success_factor)
			   {
				   dig_ship_destroyed($shipinfo['ship_id'],0);
			   }

				$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
				db_op_result($debug_query,__LINE__,__FILE__);

			   cancel_bounty($playerinfo['player_id']);
		   }
		   else
		   {
			   cancel_bounty($playerinfo['player_id']);
			   db_kill_player($playerinfo['player_id'], 0, 0);
		   }
	   }
   }
}
echo "</b></font></td></tr></table>";
explode_mines($destination,$roll);
}
else
{
	$minesfound = 0;
}
?>