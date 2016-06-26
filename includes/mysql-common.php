<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: mysql-common.php



function sql_insert_identity_on($table)
{
}

function sql_insert_identity_off($table)
{
}

function sql_time_since_login() 
{
    global $db, $dbtables;
    $debug_query = $db->Execute("SELECT * from $dbtables[players] WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP($dbtables[players].last_login)) / 60 < 5 and email NOT LIKE '%@npc'");
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function isLoanPending($player_id)
{
    global $db, $dbtables, $igb_lrate;

    $debug_query = $db->Execute("SELECT loan, loantime from $dbtables[ibank_accounts] WHERE player_id=$player_id and (loan != 0) and (((UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(loantime)) / 60) > $igb_lrate) ");
    db_op_result($debug_query,__LINE__,__FILE__);
    if (!$debug_query->EOF)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function sql_log_starvation()
{
  global $db, $dbtables, $starvation_death_rate, $expoprod;
  global $colonist_limit, $colonist_production_rate, $organics_prate;
  global $organics_consumption, $colonist_tech_add;

//	$doomsday = $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7));
    // LOGGING Starvation
    $starv_log = "SELECT owner, sector_id, ROUND(colonists * $starvation_death_rate * $expoprod) AS st_value FROM ".                
                 "$dbtables[planets] WHERE (organics + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) " .    
                 "* $colonist_production_rate * $organics_prate * prod_organics / 100.0 * $expoprod) - " .
                 "(LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * $organics_consumption * $expoprod) < 0)";
    $debug_query = $db->Execute($starv_log);
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_update_starvation()
{
    global $db, $dbtables, $starvation_death_rate, $expoprod;
    global $colonist_limit, $colonist_production_rate, $organics_prate;
    global $colonist_reproduction_rate, $organics_consumption;
    global $ore_prate, $goods_prate, $energy_prate, $credits_prate, $colonist_tech_add;

    $planetupdate = "UPDATE $dbtables[planets] SET organics=0, " .
                    "colonists = LEAST((colonists - (colonists * $starvation_death_rate * $expoprod) + " .
                    "(colonists * $colonist_reproduction_rate * $expoprod)), $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))),
                     ore=ore + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $ore_prate * prod_ore / 100.0 " .
                    "* $expoprod, goods=goods + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * " .
                    "$goods_prate * prod_goods / 100.0 * $expoprod, energy=energy + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * " .
                    "$colonist_production_rate) * $energy_prate * prod_energy / 100.0 * $expoprod
					 WHERE (organics + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * " .
                    "$colonist_production_rate * $organics_prate * prod_organics / 100.0 * $expoprod) - " .
                    "(LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * $organics_consumption * $expoprod) < 0)";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_production_update()
{
    global $db, $dbtables, $starvation_death_rate, $expoprod;
    global $colonist_limit, $colonist_production_rate, $organics_prate;
    global $colonist_reproduction_rate, $organics_consumption;
    global $ore_prate, $goods_prate, $energy_prate, $credits_prate, $colonist_tech_add, $production_multiplier;

    // If organics plus org production minus org consumption is greater then or equal to zero
    // Then all colonists are fed and life is happy
    $planetupdate = "UPDATE $dbtables[planets] SET 
	organics=GREATEST(organics + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * $organics_prate * prod_organics / 100.0 * $expoprod) - (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * $organics_consumption * $expoprod), 0), 
    ore=ore + ((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $ore_prate * prod_ore / 100.0 * $expoprod), 
	goods=goods + ((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $goods_prate * prod_goods / 100.0 * $expoprod), 
	energy=energy + ((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $energy_prate * prod_energy / 100.0 * $expoprod), 
    colonists= LEAST((colonists + (colonists * $colonist_reproduction_rate * $expoprod)), $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))), 
	credits=LEAST(credits + (((LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $credits_prate * ((100.0 - prod_organics - prod_ore - prod_goods - prod_energy - prod_fighters - prod_torp - prod_research - prod_build) / 100.0) * $expoprod) * (1.5 + ((credits / max_credits) * $production_multiplier))), max_credits) ";
					"WHERE " .
                    "(organics + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * $organics_prate * " .
                    "prod_organics / 100.0 * $expoprod) - (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate * " .
                    "$organics_consumption * $expoprod) >= 0)";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;

}

function sql_defense_update()
{
    global $db, $dbtables, $expoprod;
    global $colonist_limit, $colonist_production_rate;
    global $fighter_prate, $torpedo_prate, $colonist_tech_add;

    $planetupdate = "UPDATE $dbtables[planets] SET fighters=fighters + " .
                    "(LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $fighter_prate * prod_fighters / 100.0 * " .
                    "$expoprod, torps=torps + (LEAST(colonists, $colonist_limit + ($colonist_tech_add * ((computer + sensors + beams + torp_launchers + shields + jammer + cloak) / 7))) * $colonist_production_rate) * $torpedo_prate * " .
                    "prod_torp / 100.0 * $expoprod WHERE owner!=0";

    $debug_query = $db->Execute($planetupdate);
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_sched_degrade_defences($defence_id)
{
    global $db, $dbtables, $defence_degrade_rate;

    $debug_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity = quantity - " .
                                "GREATEST(ROUND(quantity * $defence_degrade_rate),1) where " .
                                "defence_id = $defence_id and quantity > 0");

    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_sched_degrade_energy($planet_id)
{
    global $db, $dbtables, $defence_degrade_rate, $energy_required, $energy_available;

    $debug_query = $db->Execute("UPDATE $dbtables[planets] set energy = energy - " .
                 "GREATEST(ROUND($energy_required * (energy / $energy_available)),1)  where planet_id = $planet_id ");

    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function time_since_reset()
{
    global $reset_date;
    $time_since = time() - strtotime($reset_date . " 00:00:00");
    $timestring = '';
    
    $weeks = $time_since/604800;
    $days = ($time_since%604800)/86400;

    if (round($weeks))
    {
        $timestring=floor($weeks)." weeks ";
    }

    if (round($days))
    {
        $timestring.=floor($days)." days ";
    }

    return $timestring;     
}

function sql_ranking()
{
    global $db, $dbtables, $query, $by, $page, $max_rank, $showzeroranking;

if($showzeroranking == 1)
	$showzero = "";
else $showzero = "$dbtables[players].turns_used != 0 and";

    $debug_query = $db->Execute("SELECT $dbtables[players].experience, $dbtables[players].email, $dbtables[players].score, $dbtables[players].player_id, " .
                        "$dbtables[players].character_name, $dbtables[players].avatar, $dbtables[players].kills, $dbtables[players].deaths, $dbtables[players].captures, $dbtables[players].planets_lost, $dbtables[players].planets_built, $dbtables[players].profile_id, " .
                        "$dbtables[players].turns_used, UNIX_TIMESTAMP($dbtables[players].last_login) as last_login, " .
                        "UNIX_TIMESTAMP($dbtables[players].last_login) as online, $dbtables[players].rating, " .
                        "$dbtables[teams].team_name, " .
                        "IF($dbtables[players].turns_used<150,0,ROUND($dbtables[players].score/$dbtables[players].turns_used)) " .
                        "AS efficiency FROM $dbtables[players] LEFT JOIN $dbtables[teams] ON $dbtables[players].team " .
                        "= $dbtables[teams].id LEFT JOIN $dbtables[ships] ON " .
                        "$dbtables[players].player_id=$dbtables[ships].player_id WHERE ".$showzero." $dbtables[players].currentship=$dbtables[ships].ship_id and destroyed!='Y' " .
                        "and email NOT LIKE '%@npc'".$query." ORDER BY $by  LIMIT ". $page * $max_rank .",$max_rank");
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

function sql_last_sched_run()
{
    global $db, $dbtables;
    $debug_query = $db->SelectLimit("SELECT UNIX_TIMESTAMP(last_run) as last_run FROM $dbtables[scheduler]",1);
    db_op_result($debug_query,__LINE__,__FILE__);
    return $debug_query;
}

?>
