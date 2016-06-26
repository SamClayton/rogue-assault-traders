<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_federation.php

if (preg_match("/sched_federation.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}
TextFlush ( "<B>Mornoc Alliance Ship</B><BR>");

if (!function_exists('phpChangeDelta')) {
	function phpChangeDelta($desiredvalue,$currentvalue)
	{
		global $upgrade_cost, $upgrade_factor;

		$Delta=0; $DeltaCost=0;
		$Delta = $desiredvalue - $currentvalue;

		while ($Delta>0)
		{
			$DeltaCost=$DeltaCost + mypw($upgrade_factor,$desiredvalue-$Delta);
			$Delta=$Delta-1;
		}
		$DeltaCost=$DeltaCost * $upgrade_cost;

		return $DeltaCost;
	}
}

if (!function_exists('playerlog')) {
	function playerlog($sid, $log_type, $data = '')
	{
		global $db, $dbtables;

		// write log_entry to the player's log - identified by player's player_id - sid.
		if ($sid != '' && !empty($log_type))
		{
			$stamp = date("Y-m-d H:i:s");
			$data = addslashes($data);
			$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES($sid, $log_type, '$stamp', '$data')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

if (!function_exists('adminlog')) {
	function adminlog($log_type, $data = '')
	{
		global $db, $dbtables;

		// Failures should be silent, since its the admin log.
		$silent = 1;

		// write log_entry to the admin log
		if (!empty($log_type))
		{
			$stamp = date("Y-m-d H:i:s");
			$data = addslashes($data);
			$debug_query = $db->Execute("INSERT INTO $dbtables[logs] (player_id, type, time, data) VALUES(0, $log_type, '$stamp', '$data')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

if (!function_exists('send_system_im')) {
	function send_system_im($target_id, $subject, $content, $targetonline)
	{
		global $db, $dbtables;

		$difftime = (TIME() - strtotime($targetonline)) / 60;

		if ($difftime <= 5) 
		{
			$result2 = $db->Execute("SELECT * from $dbtables[messages] where recp_id = $target_id order by ID DESC");
			$iminfo = $result2->fields;

			$timestamp = date("Y-m-d H:i:s");
			if($iminfo['subject'] != $subject and $iminfo['message'] != $content){
				$debug_query = $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, sent, subject, message) VALUES " .
											"('0', '" . $target_id . "', '" . $timestamp . "', " .
											"'" . $subject . "', '" . $content . "')");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}
	}
}

include ("combat_functions.php");

$curtime = TIME();

$res5 = $db->Execute("SELECT count(*) as bounty_count,bounty_on  FROM $dbtables[bounty] WHERE placed_by = 0 group by bounty_on order by bounty_count desc");
db_op_result($res5,__LINE__,__FILE__);
$j = 0;
$attackflag=0;
if ($res5->RecordCount() > 0)
{
	while (!$res5->EOF)
	{
		$bounty_details = $res5->fields;
		if ($bounty_details['bounty_count'] >= $fed_collection_start)
		{
			// bounty limit reached see if online.
			if ($attackflag ==0)
			{
				$res_play = $db->Execute("SELECT * FROM $dbtables[players],$dbtables[ships] WHERE currentship =ship_id and on_planet='N' and  $dbtables[players].player_id=$bounty_details[bounty_on] and ((($curtime - UNIX_TIMESTAMP($dbtables[players].last_login) )/60 ) < 5 ) and ((fed_attack_date= '00-00-0000 00:00:00') or ((TO_DAYS(NOW()) - TO_DAYS(fed_attack_date))   > 0 ))");
				db_op_result($res_play,__LINE__,__FILE__);
				if ($res_play->RecordCount() > 0)
				{
					while (!$res_play->EOF)
					{
						$player_details = $res_play->fields;

						// You have a candidate
						// Move to sector
						$query = "UPDATE $dbtables[ships] SET  sector_id=$player_details[sector_id]  WHERE ship_id=3";
						$debug_query = $db->Execute("$query");
						db_op_result($debug_query,__LINE__,__FILE__);
						// execute an armor attack and do damage
						$perc_damage=mt_rand(10, 30)/100;
						calc_internal_damage($player_details['ship_id'], 0, $perc_damage);
						// esitmate level costs
						// reduce fed bounty by cost
						$query = "select * from $dbtables[ships] WHERE ship_id=$player_details[currentship]";
						$debug_query = $db->Execute("$query");
						db_op_result($debug_query,__LINE__,__FILE__);
						if ($debug_query->RecordCount() > 0)
						{
							$bounty_ship = $debug_query->fields;
							$total_cost=phpchangeDelta($bounty_ship['hull_normal'],$bounty_ship['hull'])+
								phpchangeDelta($bounty_ship['engines_normal'],$bounty_ship['engines'])+
								phpchangeDelta($bounty_ship['power_normal'],$bounty_ship['power'])+
								phpchangeDelta($bounty_ship['computer_normal'],$bounty_ship['computer'])+
								phpchangeDelta($bounty_ship['sensors_normal'],$bounty_ship['sensors'])+
								phpchangeDelta($bounty_ship['beams_normal'],$bounty_ship['beams'])+
								phpchangeDelta($bounty_ship['armour_normal'],$bounty_ship['armour'])+
								phpchangeDelta($bounty_ship['torp_launchers_normal'],$bounty_ship['torp_launchers'])+
								phpchangeDelta($bounty_ship['shields_normal'],$bounty_ship['shields'])+
								phpchangeDelta($bounty_ship['cloak_normal'],$bounty_ship['cloak'])+
								phpchangeDelta($bounty_ship['ecm_normal'],$bounty_ship['ecm']);
							$getbountyid = $db->Execute("SELECT * FROM $dbtables[bounty] WHERE placed_by = 0  and bounty_on=$player_details[player_id] order by bounty_id ");
							db_op_result($getbountyid,__LINE__,__FILE__);
						$temptotal=$total_cost;	
						if ($getbountyid->RecordCount() > 0)
						{
							while (!$getbountyid->EOF)
							{
								$bounty = $getbountyid->fields;
								if ($bounty['amount']<= $temptotal){
								$bountyupdate = $db->Execute("delete from  $dbtables[bounty]  where bounty_id=$bounty[bounty_id]");
								db_op_result($bountyupdate,__LINE__,__FILE__);
								$temptotal=$temptotal-$bounty['amount'];
								}else{
								
								$bountyupdate = $db->Execute("update $dbtables[bounty] set amount=amount-$temptotal where bounty_id=$bounty[bounty_id]");
								$temptotal=0;
								db_op_result($bountyupdate,__LINE__,__FILE__);
								}
							
							$getbountyid->MoveNext();	
							}
						}
					}
						// update attack date
						$query = "UPDATE $dbtables[players] SET  fed_attack_date=now()  WHERE player_id=$player_details[player_id]";
						$debug_query = $db->Execute("$query");
						db_op_result($debug_query,__LINE__,__FILE__);
						$query = "UPDATE $dbtables[ships] SET  armour_pts=armour_pts*(1-$perc_damage),fighters=fighters*(1-$perc_damage)  WHERE ship_id=$player_details[currentship]";
						$debug_query = $db->Execute("$query");
						db_op_result($debug_query,__LINE__,__FILE__);
						//put attack message in log
						$playername=get_player($player_details['player_id']);
						send_system_im($player_details['player_id'], "Mornoc Alliance Attack!", "You ship was attacked by Mornoc Alliance. You battle for a while, then the fight breaks down. You lost armor points and fighters in the skirmish.",$player_details['last_login']);
						playerlog($player_details['player_id'], LOG_ATTACKED_WIN, "Mornoc Alliance");
						adminlog(LOG_RAW,"<font color=yellow><B>FED Bounty Attack:</B></font>Bountied Player " . get_player($player_details[player_id]) . " -  Bounty Reduced by:<B> ".$total_cost. "</B><br>Tech Damage Percentage:<B>".$perc_damage."</b>");
						TextFlush ( "<B>Mornoc Alliance Bounty attack on $playername  </B><BR>");
						$res = $db->Execute("INSERT INTO $dbtables[shoutbox] (player_id,player_name,sb_date,sb_text,sb_alli) VALUES (3,'Mornoc Alliance'," . time() . ",'Fed bounty player $playername in sector $player_details[sector_id]',0) ");

						insert_news($playername."|".$total_cost, $player_details['player_id'], "fedcolbounty");
						$attackflag++;
						$res_play->MoveNext();
					}//end while
				}
			}
		}
		$j++;
		
		$res5->MoveNext();
	}
}else{
	//else regular move
	if(mt_rand(1, 10000) < 2500)
	{
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe]");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		if ($totrecs > 0)
		{
			$randplay=mt_rand(0,($totrecs-1));
			$sector_id = $getit[$randplay]['sector_id'];
		}
	}else{
		$findem = $db->Execute("SELECT sector_id FROM $dbtables[planets]");
		$totrecs=$findem->RecordCount(); 
		$getit=$findem->GetArray();
		if ($totrecs > 0)
		{
			$randplay=mt_rand(0,($totrecs-1));
			$sector_id = $getit[$randplay]['sector_id'];
		}
	}

	$query = "UPDATE $dbtables[ships] SET class=100, hull=90, engines=90, power=90, computer=90,
	  sensors=90, beams=90, armour=90, cloak=0, torp_launchers=90, shields=90, ecm=90,
	  hull_normal=90, engines_normal=90, power_normal=90, computer_normal=90, ecm_normal=90,
	  sensors_normal=90, beams_normal=90, armour_normal=90, cloak_normal=0, torp_launchers_normal=90, shields_normal=90, fighters=70503928228430688,
	  torps=70503928228430688, armour_pts=70503928228430688 , dev_emerwarp=1, dev_minedeflector=200000000000, dev_escapepod='Y',
	  dev_fuelscoop='Y', dev_nova='Y', sector_id=$sector_id, energy=352519641142153472  WHERE ship_id=3";
	$debug_query = $db->Execute("$query");
	db_op_result($debug_query,__LINE__,__FILE__);
}// end checks

$debug_query = $db->Execute("UPDATE $dbtables[players] SET fed_bounty_count=GREATEST(fed_bounty_count-$fed_bounty_delay, 0)");
db_op_result($debug_query,__LINE__,__FILE__);

TextFlush ( "<br>Mornoc Alliance Ship Moved<BR>");

$multiplier = 0;
TextFlush ( "<BR>\n");
?>
