<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: bounty.php

include ("config/config.php");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");

$title = $l_by_title;

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

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

if ((!isset($response)) || ($response == ''))
{
	$response = '';
}
//-------------------------------------------------------------------------------------------------


switch ($response) 
{
	case "display":
		$res5 = $db->Execute("SELECT * FROM $dbtables[players],$dbtables[bounty] WHERE bounty_on = player_id AND bounty_on = $bounty_on");
		$j = 0;
		if ($res5)
		{
			while (!$res5->EOF)
			{
				$bounty_details[$j] = $res5->fields;
				$j++;
				$res5->MoveNext();
			}
		}

		$num_details = $j;
		if ($num_details > 0)
		{
			$playername = $bounty_details[0]['character_name'];
			$color = $color_line1;
			for ($j=0; $j<$num_details; $j++)
			{
				$someres = $db->execute("SELECT character_name, fed_bounty_count FROM $dbtables[players] WHERE player_id = " . $bounty_details[$j]['placed_by']);
				$details = $someres->fields;
				$someres2 = $db->execute("SELECT character_name, fed_bounty_count FROM $dbtables[players] WHERE player_id = " . $bounty_details[$j]['bounty_on']);
				$moredetails = $someres2->fields;
				$bountyamount[$j] = number($bounty_details[$j]['amount']);
				$bountyby[$j] = $bounty_details[$j]['placed_by'];
				if ($bounty_details[$j]['placed_by'] == 0)
				{
					if ($fed_bounty_count <= $moredetails['fed_bounty_count'])
						$bountydetails[$j] = $moredetails['fed_bounty_count'];
				}
				else
				{
					$bountydetails[$j] = $details['character_name'];
				}
				if ($bounty_details[$j]['placed_by'] == $playerinfo['player_id'])
				{
					$bountyid[$j] = $bounty_details[$j]['bounty_id'];
				}
			}
		}

	$smarty->assign("l_none", $l_none);
	$smarty->assign("bountyid", $bountyid);
	$smarty->assign("l_by_cancel", $l_by_cancel);
	$smarty->assign("playername", $playername);
	$smarty->assign("playerid", $playerinfo['player_id']);
	$smarty->assign("bountydetails", $bountydetails);
	$smarty->assign("fed_bounty_count", $fed_bounty_count);
	$smarty->assign("l_by_fedcollectonly", $l_by_fedcollectonly);
	$smarty->assign("l_by_thefeds", $l_by_thefeds);
	$smarty->assign("bountyby", $bountyby);
	$smarty->assign("bountyamount", $bountyamount);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("num_details", $num_details);
	$smarty->assign("l_by_action", $l_by_action);
	$smarty->assign("l_by_nobounties", $l_by_nobounties);
	$smarty->assign("l_by_placedby", $l_by_placedby);
	$smarty->assign("l_by_amount", $l_by_amount);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_amount", $l_amount);
	$smarty->assign("l_by_bountyon", $l_by_bountyon);
	$smarty->assign("gotobounty", $l_gotobounty);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."bountydisplay.tpl");
	include ("footer.php");
	die();
		break;

	case "cancel":
		if ($playerinfo['turns'] <1 )
		{
			$smarty->assign("error_msg", $l_by_noturn);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		$res = $db->Execute("SELECT * from $dbtables[bounty] WHERE bounty_id = $bid");
		if (!$res)
		{
			$smarty->assign("error_msg", $l_by_nobounty);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}
		$bty = $res->fields;

		if ($bty['placed_by'] <> $playerinfo['player_id'])
		{
			$smarty->assign("error_msg", $l_by_notyours);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		$del = $db->Execute("DELETE FROM $dbtables[bounty] WHERE bounty_id = $bid");
		$stamp = date("Y-m-d H:i:s");
		$refund = $bty['amount'];
		$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits+$refund where player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("error_msg", $l_by_canceled);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;

		break;

	case "place":
		$bounty_on = stripnum($bounty_on);
		$ex = $db->Execute("SELECT * from $dbtables[players] LEFT JOIN $dbtables[ships] " .
						   "ON $dbtables[players].player_id = $dbtables[ships].player_id " .
						   "WHERE $dbtables[ships].destroyed='N' AND $dbtables[players].player_id = $bounty_on");
		if (!$ex)
		{
			$smarty->assign("error_msg", $l_by_notexists);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		$bty = $ex->fields;
		if ($bty['destroyed'] == "Y")
		{
			$smarty->assign("error_msg", $l_by_destroyed);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		if ($playerinfo['turns']<1 )
		{
			$smarty->assign("error_msg", $l_by_noturn);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		$amount = stripnum($amount);
		if ($amount <= 0)
		{
			$smarty->assign("error_msg", $l_by_zeroamount);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;

		}

		if ($bounty_on == $playerinfo['player_id'])
		{
			$smarty->assign("error_msg", $l_by_yourself);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

		if ($amount > $playerinfo['credits'])
		{
			$smarty->assign("error_msg", $l_by_notenough);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;
		}

	   if ($bounty_maxvalue != 0)
	   {
			$percent = $bounty_maxvalue * 100;
			$score = gen_score($playerinfo['player_id']);
			$maxtrans = floor($score * $score * $bounty_maxvalue);
			$l_by_placed = "Maximum bounty available to place would be: ". NUMBER($maxtrans) . "<br>".$l_by_placed;
			$previous_bounty = 0;
			$pb = $db->Execute("SELECT SUM(amount) AS totalbounty FROM $dbtables[players] WHERE bounty_on = $bounty_on AND placed_by = $playerinfo[player_id]");
			if ($pb)
			{
				$prev = $pb->fields;
				$previous_bounty = $prev[totalbounty];
			}
			if ($amount + $previous_bounty > $maxtrans)
			{
				$l_by_toomuch = str_replace("[percent]", $percent, $l_by_toomuch);
				$smarty->assign("error_msg", "Maximum bounty available to place would be: $maxtrans<br>".$l_by_toomuch);
				$smarty->assign("gotobounty", $l_gotobounty);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."bountydie.tpl");
				include ("footer.php");
				die();
				break;
			}

	  }

	  $mystuff = $playerinfo['character_name']."|".$amount."|";

	  $debug_query = $db->Execute("INSERT INTO $dbtables[bounty] (bounty_on,placed_by,amount) values ($bounty_on, $playerinfo[player_id] ,$amount)");
	  db_op_result($debug_query,__LINE__,__FILE__);
	  $stamp = date("Y-m-d H:i:s");
	  $debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp',turns=turns-1, turns_used=turns_used+1, credits=credits-$amount where player_id=$playerinfo[player_id]");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $res = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id = $bounty_on");
		$mystuff2 = $res->fields[character_name];
	  $mystuff = $mystuff.$mystuff2;

		insert_news($mystuff, 1, "bounty");

			$smarty->assign("error_msg", $l_by_placed);
			$smarty->assign("gotobounty", $l_gotobounty);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."bountydie.tpl");
			include ("footer.php");
			die();
			break;

	default:
		$debug_query = $db->Execute("SELECT DISTINCT $dbtables[players].* FROM $dbtables[ships] LEFT JOIN $dbtables[players] " .
									"ON $dbtables[players].player_id = $dbtables[ships].player_id WHERE destroyed='N' AND " .
									"$dbtables[players].player_id <> $playerinfo[player_id] ORDER BY character_name ASC");
		db_op_result($debug_query,__LINE__,__FILE__);

		$playerlist = 0;
		while (!$debug_query->EOF)
		{
			if (isset($bounty_on) && $bounty_on == $debug_query->fields[player_id])
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}

			$charname = $debug_query->fields[character_name];
			$player_id = $debug_query->fields[player_id];
			$playerid[$playerlist] = $player_id;
			$playerselect[$playerlist] = $selected;
			$playername[$playerlist] = $charname;
			$playerlist++;
			$debug_query->MoveNext();
		}

		$result3 = $db->Execute ("SELECT bounty_on, SUM(amount) as total_bounty FROM $dbtables[bounty] GROUP BY bounty_on");

		$i = 0;
		if ($result3)
		{
			while (!$result3->EOF)
			{
				$bounties[$i] = $result3->fields;
				$i++;
				$result3->MoveNext();
			}
		}

		$num_bounties = $i;
		if ($num_bounties > 0)
		{
			for ($i=0; $i<$num_bounties; $i++)
			{
				$someres = $db->execute("SELECT character_name FROM $dbtables[players] WHERE player_id = " . $bounties[$i]['bounty_on']);
				$details = $someres->fields;
				$bountyon[$i] = $bounties[$i]['bounty_on'];
				$bountyname[$i] = $details['character_name'];
				$bountyamount[$i] = number($bounties[$i]['total_bounty']);
			}
		}

	$smarty->assign("bountyon", $bountyon);
	$smarty->assign("bountyname", $bountyname);
	$smarty->assign("bountyamount", $bountyamount);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("color_line1", $color_line1);
	$smarty->assign("num_bounties", $num_bounties);
	$smarty->assign("playerid", $playerid);
	$smarty->assign("playerselect", $playerselect);
	$smarty->assign("playername", $playername);
	$smarty->assign("playerlist", $playerlist);
	$smarty->assign("l_amount", $l_amount);
	$smarty->assign("l_by_moredetails", $l_by_moredetails);
	$smarty->assign("color_header", $color_header);
	$smarty->assign("l_by_nobounties", $l_by_nobounties);
	$smarty->assign("l_by_place", $l_by_place);
	$smarty->assign("l_by_amount", $l_by_amount);
	$smarty->assign("l_by_bountyon", $l_by_bountyon);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."bountydefault.tpl");
	include ("footer.php");
	die();
	break;
}

close_database();
?>
