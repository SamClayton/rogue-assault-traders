<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: settings.php

include ("config/config.php");
include ("languages/$langdir/lang_settings.inc");

$title = $l_s_gamesettings;
if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

function TRUEFALSE ($truefalse,$Stat,$True,$False)
{
	return(($truefalse == $Stat) ? $True : $False);
}

$smarty->assign("templatename", $templatename);

$num = 0;

// Game Status
$smarty->assign("title", $l_s_gamestatus);

$smarty->assign("version", "Game release version");
$smarty->assign("release_version", $release_version);
$smarty->assign("l_s_time_since_reset", $l_s_time_since_reset);
$smarty->assign("totaltime", time_since_reset());
$smarty->assign("l_s_allowpl", $l_s_allowpl);
$smarty->assign("l_s_allowplresponse", TRUEFALSE($server_closed,False,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_allownewpl", $l_s_allownewpl);
$smarty->assign("l_s_allownewplresponse", TRUEFALSE($account_creation_closed,False,$l_s_yes,"<font color=red>$l_s_no</font>"));

// Game Options

$smarty->assign("title2", $l_s_gameoptions);

$smarty->assign("l_s_allowteamplcreds", $l_s_allowteamplcreds);
$smarty->assign("l_s_allowteamplcredsresponse", TRUEFALSE($team_planet_transfers,1,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_allowfullscan", $l_s_allowfullscan);
$smarty->assign("l_s_allowfullscanresponse", TRUEFALSE($allow_fullscan,True,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_sofa", $l_s_sofa);
$smarty->assign("l_s_sofaresponse", TRUEFALSE($sofa_on,True,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_showpassword", $l_s_showpassword);
$smarty->assign("l_s_showpasswordresponse", TRUEFALSE($display_password,True,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_genesisdestroy", $l_s_genesisdestroy);
$smarty->assign("l_s_genesisdestroyresponse", TRUEFALSE($allow_genesis_destroy,True,$l_s_yes,"<font color=red>$l_s_no</font>"));
$smarty->assign("l_s_igb", $l_s_igb);
$smarty->assign("l_s_igbresponse", TRUEFALSE($allow_ibank,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>"));
$smarty->assign("l_s_ksm", $l_s_ksm);
$smarty->assign("l_s_ksmresponse", TRUEFALSE($ksm_allowed,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>"));
$smarty->assign("l_s_navcomp", $l_s_navcomp);
$smarty->assign("l_s_navcompresponse", TRUEFALSE($allow_navcomp,True,$l_s_enabled,"<font color=red>$l_s_disabled</font>"));
$smarty->assign("l_s_newbienice", $l_s_newbienice);
$smarty->assign("l_s_newbieniceresponse", TRUEFALSE($newbie_nice,"YES",$l_s_enabled,"<font color=red>$l_s_disabled</font>"));
$temp = ($spy_success_factor) ? "YES": "NO";
$smarty->assign("l_s_spies", $l_s_spies);
$smarty->assign("l_s_spiesresponse", TRUEFALSE($temp,"YES",$l_s_enabled,"<font color=red>$l_s_disabled</font>"));

$smarty->assign("spy_success_factor", $spy_success_factor);
if ($spy_success_factor)
{
	$temp = ($allow_spy_capture_planets) ? "YES": "NO";
	$smarty->assign("l_s_spycapture", $l_s_spycapture);
	$smarty->assign("l_s_spycaptureresponse", TRUEFALSE($temp,"YES",$l_s_yes,"<font color=red>$l_s_no</font>"));
}

// Game Settings

$smarty->assign("title3", $l_s_gamesettings);

$smarty->assign("l_s_gameversion", $l_s_gameversion);
$smarty->assign("game_name", $game_name);
$smarty->assign("l_s_minhullmines", $l_s_minhullmines);
$smarty->assign("l_s_averagetechewd", $l_s_averagetechewd);
$smarty->assign("ewd_maxavgtechlevel", $ewd_maxavgtechlevel);

$sector_res = $db->Execute("SELECT COUNT(sector_id) AS n FROM $dbtables[universe]");
$sector_max = $sector_res->fields['n'];

$smarty->assign("l_s_numsectors", $l_s_numsectors);
$smarty->assign("sector_max", NUMBER($sector_max));
$smarty->assign("l_s_maxwarpspersector", $l_s_maxwarpspersector);
$smarty->assign("link_max", $link_max);
$smarty->assign("l_s_averagetechfed", $l_s_averagetechfed);
$smarty->assign("fed_max_avg_tech", $fed_max_avg_tech);

$smarty->assign("allow_ibank", $allow_ibank);
if ($allow_ibank)
{
	$smarty->assign("l_s_igbirateperupdate", $l_s_igbirateperupdate);
	$smarty->assign("bankinterest", $ibank_interest * 100);
	$smarty->assign("l_s_igblrateperupdate", $l_s_igblrateperupdate);
	$smarty->assign("loaninterest", $ibank_loaninterest * 100);
}  

$smarty->assign("l_s_techupgradebase", $l_s_techupgradebase);
$smarty->assign("basedefense", $basedefense);

$smarty->assign("l_s_collimit", $l_s_collimit);
$smarty->assign("colonist_limit", NUMBER($colonist_limit));

$smarty->assign("l_s_maxturns", $l_s_maxturns);
$smarty->assign("max_turns", NUMBER($max_turns));
$smarty->assign("l_s_maxplanetssector", $l_s_maxplanetssector);
$smarty->assign("max_planets_sector", $max_planets_sector);
$smarty->assign("l_s_maxtraderoutes", $l_s_maxtraderoutes);
$smarty->assign("max_traderoutes_player", $max_traderoutes_player);
$smarty->assign("l_s_colreprodrate", $l_s_colreprodrate);
$smarty->assign("colonist_reproduction_rate", $colonist_reproduction_rate);
$smarty->assign("l_s_energyperfighter", $l_s_energyperfighter);
$smarty->assign("energy_per_fighter", $energy_per_fighter);

$smarty->assign("l_s_secfighterdegrade", $l_s_secfighterdegrade);
$smarty->assign("defence_degrade_rate", $defence_degrade_rate * 100);

$smarty->assign("spy_success_factor", $spy_success_factor);
if ($spy_success_factor)
{
	$smarty->assign("l_s_spiesperplanet", $l_s_spiesperplanet);
	$smarty->assign("max_spies_per_planet", $max_spies_per_planet);
	$smarty->assign("l_s_spysuccessfactor", $l_s_spysuccessfactor);
	$smarty->assign("spy_success_factor2", NUMBER($spy_success_factor,1));
	$smarty->assign("l_s_spykillfactor", $l_s_spykillfactor);
	$smarty->assign("spy_kill_factor", NUMBER($spy_kill_factor,1));
}

$rate = 1 / $colonist_production_rate;

$smarty->assign("l_s_colsperfighter", $l_s_colsperfighter);
$smarty->assign("fighter_prate", NUMBER($rate/$fighter_prate));

$smarty->assign("l_s_colspertorp", $l_s_colspertorp);
$smarty->assign("torpedo_prate", NUMBER($rate/$torpedo_prate));

$smarty->assign("l_s_colsperore", $l_s_colsperore);
$smarty->assign("ore_prate", NUMBER($rate/$ore_prate));

$smarty->assign("l_s_colsperorganics", $l_s_colsperorganics);
$smarty->assign("organics_prate", NUMBER($rate/$organics_prate));

$smarty->assign("l_s_colspergoods", $l_s_colspergoods);
$smarty->assign("goods_prate", NUMBER($rate/$goods_prate));

$smarty->assign("l_s_colsperenergy", $l_s_colsperenergy);
$smarty->assign("energy_prate", NUMBER($rate/$energy_prate));

$smarty->assign("l_s_colspercreds", $l_s_colspercreds);
$smarty->assign("credits_prate", NUMBER($rate/$credits_prate));

//Scheduler Settings

$smarty->assign("title4", $l_s_gameschedsettings);

$smarty->assign("l_s_ticksupdate", $l_s_ticksupdate);
$smarty->assign("sched_ticks", $sched_ticks . $l_s_minutes);
$smarty->assign("l_s_turnsupdate", $l_s_turnsupdate);
$smarty->assign("updateticks", round($turn_rate * $sched_ticks) . " $l_s_turnsupdate $sched_ticks " . $l_s_minutes);
$smarty->assign("l_s_npcupdate", $l_s_npcupdate);
$smarty->assign("sched_npc", $sched_npc . $l_s_minutes);

if ($allow_ibank)
{
	$smarty->assign("l_s_igbturnsupdate", $l_s_igbturnsupdate);
	$smarty->assign("sched_igb", $sched_igb . $l_s_minutes);
}

$smarty->assign("l_s_newsupdate", $l_s_newsupdate);
$smarty->assign("sched_news", $sched_news . $l_s_minutes);
$smarty->assign("l_s_planetupdate", $l_s_planetupdate);
$smarty->assign("sched_planets", $sched_planets . $l_s_minutes);

if ($spy_success_factor)
{
	$smarty->assign("l_s_spyupdate", $l_s_spyupdate);
	$smarty->assign("sched_spies", $sched_spies . $l_s_minutes);
}

$smarty->assign("l_s_portsupdate", $l_s_portsupdate);
$smarty->assign("sched_ports", $sched_ports . $l_s_minutes);
$smarty->assign("l_s_towupdate", $l_s_towupdate);
$smarty->assign("sched_tow", $sched_tow . $l_s_minutes);
$smarty->assign("l_s_scoreupdate", $l_s_scoreupdate);
$smarty->assign("sched_ranking", $sched_ranking . $l_s_minutes);
$smarty->assign("l_s_secdefdegrupdate", $l_s_secdefdegrupdate);
$smarty->assign("sched_degrade", $sched_degrade . $l_s_minutes);
$smarty->assign("l_s_apocalypseupdate", $l_s_apocalypseupdate);
$smarty->assign("sched_apocalypse", $sched_apocalypse . $l_s_minutes);
$smarty->assign("l_s_independence", $l_s_independence);
$smarty->assign("sched_independance", $sched_independance . $l_s_minutes);
$smarty->assign("l_s_dignitaryupdate", $l_s_dignitaryupdate);
$smarty->assign("sched_dig", $sched_dig . $l_s_minutes);

$smarty->assign("l_global_mlogin", $l_global_mlogin);
$smarty->display($templatename."settings.tpl");
include ("footer.php");

?>
