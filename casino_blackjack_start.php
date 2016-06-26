<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: port_casino.php

include ("config/config.php");
$no_gzip = 1;

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

// CLear all current casino related session data
unset($_SESSION['newdeck'], $newdeck);
unset($_SESSION['dealer'], $dealer);
unset($_SESSION['bet'], $bet);
unset($_SESSION['hand'], $hand);
unset($_SESSION['handend'], $handend);
unset($_SESSION['player'], $player);
unset($_SESSION['player_split'], $player_split);
unset($_SESSION['count'], $count);
unset($_SESSION['status'], $status);
unset($_SESSION['split_flag'], $split_flag);
unset($_SESSION['playercards'], $playercards);
unset($_SESSION['playersplitcards'], $playersplitcards);
unset($_SESSION['dealercards'], $dealercards);
unset($_SESSION['old_bet'], $old_bet);
unset($_SESSION['completedhand'], $completedhand);
// End clear

close_database();
echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=casino_blackjack.php\">";
