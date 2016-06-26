<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the     
// Free Software Foundation; either version 2 of the License, or (at your    
// option) any later version.                                                
// 
// File: newsservices.php


/*
Assumes $day is a valid formatted time
*/
function getpreviousday($day)
{
    //convert the formatted date into a timestamp
    $day = strtotime($day);

    //subtract one day in seconds from the timestamp
    $day = $day - 86400;

    //return the final amount formatted as YYYY-MM-DD
    return date("Y-m-d",$day);
}

function getnextday($day)
{
    //convert the formatted date into a timestamp
    $day = strtotime($day);

    //add one day in seconds to the timestamp
    $day = $day + 86400;

    //return the final amount formatted as YYYY-MM-DD
    return date("Y-m-d",$day);
}

function translate_news($entry)
{
    global $l_news_planets, $l_news_cols, $l_news_p_headline, $l_killheadline;
    global $l_news_killed;
    global $l_news_p_text5, $l_news_p_text10, $l_news_p_text25, $l_news_p_text50;
    global $l_news_c_text25, $l_news_c_text100, $l_news_c_text500, $l_news_c_text1000;
    global $l_created_universe, $l_created_universe_full;

    global $l_news_indi, $l_news_indi_short;
    global $l_news_nova_short, $l_news_nova;
    global $l_news_nova_m_short, $l_news_nova_m;
    global $l_news_bounty;
    global $l_news_attackerpod_p_short, $l_news_attackerpod_P;
    global $l_news_attackerdie_p_short, $l_news_attackerdie_p;
    global $l_news_destroyed_p_short, $l_news_destroyed_p;
    global $l_news_defeated_p_short, $l_news_defeated_p;
    global $l_news_notdefeated_p_short, $l_news_notdefeated_p;
    global $l_news_targetepod_short, $l_news_targetepod;
    global $l_news_targetdies_short, $l_news_targetdies,$l_news_fedcolbounty_short,$l_news_fedcolbounty;

	$entry['data'] = str_replace("\\", "", $entry['data']);

    switch($entry['news_type'])
    {
        case "creation":
            $retvalue['headline']  = $l_created_universe;
            $retvalue['newstext'] = $l_created_universe_full;
        break;

        case "planet50":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '50' . $l_news_planets;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_p_text50);
        break;

        case "planet25":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '25' . $l_news_planets;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_p_text25);
        break;

        case "planet10":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '10' . $l_news_planets;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_p_text10);
        break;
            
        case "planet5":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '5' . $l_news_planets;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_p_text5);
        break;

        case "col1000":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '1000' . $l_news_cols;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_c_text1000);
        break;

        case "col500":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '500' . $l_news_cols;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_c_text500);
        break;

        case "col100":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '100' . $l_news_cols;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_c_text100);
        break;

        case "col25":
            $l_news_p_headline2 = str_replace("[player]", $entry['data'], $l_news_p_headline);
            $retvalue['headline']  = $l_news_p_headline2 . '25' . $l_news_cols;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_c_text25);
        break;

        case "killed":
            $retvalue['headline']  = $entry['data'] . $l_killheadline;
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_killed);
        break;

        case "indi":
            $retvalue['headline']  = str_replace("[planets]", $entry['data'], $l_news_indi_short);
            $retvalue['newstext'] = str_replace("[planets]", $entry['data'], $l_news_indi);
        break;

        case "nova":
            $retvalue['headline']  = str_replace("[name]", $entry['data'], $l_news_nova_short);
			$total = str_replace("[total]", $entry['total'], $l_news_nova);
			if($entry['total'] == 1)
				$total = str_replace("(s)", "", $total);
			else $total = str_replace("(s)", "s", $total);
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $total);
        break;

        case "novamiss":
            $retvalue['headline']  = str_replace("[name]", $entry['data'], $l_news_nova_m_short);
			$total = str_replace("[total]", $entry['total'], $l_news_nova_m);
			if($entry['total'] == 1)
				$total = str_replace("(s)", "", $total);
			else $total = str_replace("(s)", "s", $total);
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $total);
        break;

        case "bounty":
			$playerdatas = explode("|", $entry['data']);
            $data  = str_replace("[name]", $playerdatas[0], $l_news_bounty);
            $data  = str_replace("[amount]", number($playerdatas[1]), $data);
            $retvalue['headline']  = str_replace("[name2]", $playerdatas[2], $data);
            $retvalue['newstext'] = str_replace("[name2]", $playerdatas[2], $data);
        break;

        case "pattackerpod":
            $retvalue['headline']  = str_replace("[name]", $entry['data'], $l_news_attackerpod_p_short);
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_attackerpod_P);
        break;

        case "pattackerdied":
            $retvalue['headline']  = str_replace("[name]", $entry['data'], $l_news_attackerdie_p_short);
            $retvalue['newstext'] = str_replace("[name]", $entry['data'], $l_news_attackerdie_p);
        break;

        case "planetdestroyed":
			$playerdatas = explode("|", $entry['data']);
            $retvalue['headline']  = str_replace("[name]", $playerdatas[0], $l_news_destroyed_p_short);
            $data = str_replace("[name]", $playerdatas[0], $l_news_destroyed_p);
			$total = str_replace("[total]", $entry['total'], $data);
			if($entry['total'] == 1)
				$total = str_replace("(s)", "", $total);
			else $total = str_replace("(s)", "s", $total);
            $retvalue['newstext'] = str_replace("[owner]", $playerdatas[1], $total);
        break;

        case "planetdefeated":
			$playerdatas = explode("|", $entry['data']);
            $retvalue['headline']  = str_replace("[name]", $playerdatas[0], $l_news_defeated_p_short);
            $data = str_replace("[name]", $playerdatas[0], $l_news_defeated_p);
			$total = str_replace("[total]", $entry['total'], $data);
			if($entry['total'] == 1)
				$total = str_replace("(s)", "", $total);
			else $total = str_replace("(s)", "s", $total);
            $retvalue['newstext'] = str_replace("[owner]", $playerdatas[1], $total);
        break;

        case "planetnotdefeated":
			$playerdatas = explode("|", $entry['data']);
            $retvalue['headline']  = str_replace("[name]", $playerdatas[0], $l_news_notdefeated_p_short);
            $data = str_replace("[name]", $playerdatas[0], $l_news_notdefeated_p);
			$total = str_replace("[total]", $entry['total'], $data);
			if($entry['total'] == 1)
				$total = str_replace("(s)", "", $total);
			else $total = str_replace("(s)", "s", $total);
            $retvalue['newstext'] = str_replace("[owner]", $playerdatas[1], $total);
        break;
		case "fedcolbounty":
	    $playerdatas = explode("|", $entry['data']);
            $data  = str_replace("[name]", $playerdatas[0], $l_news_fedcolbounty);
            $data  = str_replace("[amount]", number($playerdatas[1]), $data);
            $retvalue['headline']  = str_replace("[name]", $playerdatas[0], $l_news_fedcolbounty_short);
            $retvalue['newstext'] = $data;
        break;
        case "targetepod":
			$playerdatas = explode("|", $entry['data']);
            $data = str_replace("[name]", $playerdatas[0], $l_news_targetepod_short);
            $retvalue['headline']  = str_replace("[owner]", $playerdatas[1], $data);
            $data = str_replace("[name]", $playerdatas[0], $l_news_targetepod);
            $retvalue['newstext'] = str_replace("[owner]", $playerdatas[1], $data);
        break;

        case "targetdies":
			$playerdatas = explode("|", $entry['data']);
            $data = str_replace("[name]", $playerdatas[0], $l_news_targetdies_short);
            $retvalue['headline']  = str_replace("[owner]", $playerdatas[1], $data);
            $data = str_replace("[name]", $playerdatas[0], $l_news_targetdies);
            $retvalue['newstext'] = str_replace("[owner]", $playerdatas[1], $data);
        break;    }
    
    return $retvalue;
}
?>
