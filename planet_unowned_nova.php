<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: planet_unowned_nova.php

include ("config/config.php");
include ("languages/$langdir/lang_attack.inc");
include ("languages/$langdir/lang_planet.inc");
include ("languages/$langdir/lang_planets.inc");
include ("languages/$langdir/lang_combat.inc");
include ("languages/$langdir/lang_report.inc");
include ("languages/$langdir/lang_ports.inc");
include ("languages/$langdir/lang_bounty.inc");
include ("languages/$langdir/lang_shipyard.inc");
include ("languages/$langdir/lang_traderoute.inc");
include ("combat_functions.php");
$no_gzip = 1;

if (isset($_GET['planet_id']))
{
	$planet_id = $_GET['planet_id'];
}

$title = $l_planet_title;

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

$planet_id = stripnum($planet_id);
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet_id");
if ($result3)
	$planetinfo=$result3->fields;

bigtitle();

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

// No planet

if (empty($planetinfo))
{
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");

	die();
}

if ($shipinfo['sector_id'] != $planetinfo['sector_id'])
{
	if ($shipinfo['on_planet'] == 'Y')
	{
	  $debug_query = $db->Execute("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$shipinfo[ship_id]");
	  db_op_result($debug_query,__LINE__,__FILE__);
	}
		$smarty->assign("error_msg", $l_planet_none);
		$smarty->assign("error_msg2", "");
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}

if (($planetinfo['owner'] == 0  || $planetinfo['defeated'] == 'Y') && $command != "capture")
{
	if ($planetinfo['owner'] == 0)
		$smarty->assign("error_msg", $l_planet_unowned);
	$capture_link="<a href='planet_unowned_capture.php?planet_id=$planet_id'>$l_planet_capture1</a>";
	$l_planet_capture2=str_replace("[capture]",$capture_link,$l_planet_capture2);
		$smarty->assign("error_msg2", $l_planet_capture2);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."genericdie.tpl");
		include ("footer.php");
	die();
}

if ($planetinfo['owner'] != 0)
{
	if ($spy_success_factor)
	{
	  spy_detect_planet($shipinfo['ship_id'], $planetinfo['planet_id'],$planet_detect_success1);
	}
	$result3 = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$planetinfo[owner]");
	$ownerinfo = $result3->fields;

	$res = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$planetinfo[owner] AND ship_id=$ownerinfo[currentship]");
	$ownershipinfo = $res->fields;
}

if ($planetinfo['owner'] == $playerinfo['player_id'] || ($planetinfo['team'] == $playerinfo['team'] && $playerinfo['team'] > 0 && $planetinfo[owner] > 0))
{
	if ($command != "")
	{
		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";
	}

	if ($allow_ibank)
	{
		echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
	}

	echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";

	TEXT_GOTOMAIN();
	include ("footer.php");
	die();
}
else
{

		if($shipinfo['class'] >= $dev_nova_shiplimit){
			if($shipinfo['dev_nova'] == "Y"){
				if($planetinfo['owner'] != 3){
					if($playerinfo[turns] > 50){
						$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns_used=turns_used+50, turns=turns-50 WHERE player_id=$playerinfo[player_id]");
						db_op_result($debug_query,__LINE__,__FILE__);

				   		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET dev_nova='N' WHERE ship_id=$shipinfo[ship_id]");
						db_op_result($debug_query,__LINE__,__FILE__);
						$novarand = rand(1, 100);

						if(rand(1, 100) > $dev_nova_explode){

							$isfedbounty = planet_bounty_check($playerinfo, $shipinfo['sector_id'], $ownerinfo, 1);

							if($isfedbounty > 0)
							{
								echo $l_by_fedbounty2 . "<BR><BR>";
							}
// end<br>
							$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id = $planetinfo[owner]");
							$last_login = $res->fields['last_login'];
							send_system_im($planetinfo['owner'], $l_planet_imtitleattack, $playerinfo['character_name'] . " $l_planet_imnova $planetinfo[name] $l_planet_iminsector $planetinfo[sector_id].", $last_login);

							if($novarand <= $dev_nova_percent){
								 if($spy_success_factor)
								 {
								   spy_planet_destroyed($planet_id);
								 }

								 $debug_query = $db->Execute("DELETE from $dbtables[planets] where planet_id=$planet_id");
								 db_op_result($debug_query,__LINE__,__FILE__);

// check for ships and destroy them
								$result4 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE planet_id=$planet_id AND on_planet='Y'");
								$shipsonplanet = $result4->RecordCount();

								if ($shipsonplanet > 0)
								{
									$l_cmb_shipdock2 = str_replace("[cmb_shipsonplanet]", $shipsonplanet, $l_cmb_shipdock2);
									echo "<BR><BR><CENTER>$l_cmb_shipdock2</CENTER><BR><BR>\n";
									while (!$result4->EOF)
									{
										$onplanet = $result4->fields;

										$ship_id = stripnum($onplanet['ship_id']);

										$result2 = $db->Execute ("SELECT * FROM $dbtables[players] WHERE currentship=$onplanet[ship_id]");
										$targetinfo = $result2->fields;

										$result = $db->Execute ("SELECT * FROM $dbtables[ships] WHERE ship_id='$ship_id'");
										$targetship = $result->fields;

										/* determine percent chance of success in detecting target ship - based on player's sensors and opponent's cloak */
										$targetcloak = floor($targetship['cloak'] * 0.75);

										$success = (10 - $targetcloak + $shipinfo['sensors']) * 5;
										if ($success < 5)
										{
											$success = 5;
										}

										if ($success > 95)
										{
											$success = 95;
										}

										$targetengines = floor($targetship['engines'] * 0.50);

										$flee = (10 - $targetengines + $shipinfo['engines']) * 5;
										$roll = mt_rand(1, 100);
										$roll2 = mt_rand(1, 100);

										if ($flee < $roll2)
										{
											echo "$l_att_flee<BR><BR>";
											$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
											db_op_result($debug_query,__LINE__,__FILE__);
											playerlog($targetinfo['player_id'], LOG_ATTACK_OUTMAN, "$playerinfo[character_name]");
											$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$ship_id");
											db_op_result($debug_query,__LINE__,__FILE__);
										}else if ($roll > $success)
										{
											/* if scan fails - inform both player and target. */
											echo "$l_planet_noscan<BR><BR>";
											$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
											db_op_result($debug_query,__LINE__,__FILE__);
											playerlog($targetinfo['player_id'], LOG_ATTACK_OUTSCAN, "$playerinfo[character_name]");
											$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET on_planet='N' WHERE ship_id=$ship_id");
											db_op_result($debug_query,__LINE__,__FILE__);
										}
										else
										{
											/* if scan succeeds, show results and inform target. */
											$shipavg = ($targetship['hull'] + $targetship['engines'] + $targetship['power'] + $targetship['computer'] + $targetship['sensors'] + $targetship['beams'] + $targetship['torp_launchers'] + $targetship['shields'] + $targetship['cloak'] + $targetship['armour'] + $targetship['ecm']) / 11;
											if ($shipavg > $ewd_maxavgtechlevel)
											{
												$chance = round($shipavg / 40) * 100;
											}
											else
											{
												$chance = 0;
											}

											$random_value = mt_rand(1,100);
											if ($targetship['dev_emerwarp'] > 0 && $random_value > $chance)
											{
												/* need to change warp destination to random sector in universe */
												$rating_change=round($targetinfo['rating']*.1);
												$source_sector = $shipinfo['sector_id'];
												$findem = $db->Execute("SELECT sector_id FROM $dbtables[universe] where sg_sector != 1 and sector_id > 3");
												$totrecs=$findem->RecordCount(); 
												$getit=$findem->GetArray();
												$randplay=mt_rand(0,($totrecs-1));
												$dest_sector = $getit[$randplay]['sector_id'];

												$debug_query = $db->Execute("SELECT zone_id FROM $dbtables[universe] WHERE sector_id=$source_sector");
												db_op_result($debug_query,__LINE__,__FILE__);
												$zones = $debug_query->fields;

												$debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns-1,turns_used=turns_used+1,rating=rating-$rating_change WHERE player_id=$playerinfo[player_id]");
												db_op_result($debug_query,__LINE__,__FILE__);

												playerlog($targetinfo['player_id'], LOG_ATTACK_EWD, "$playerinfo[character_name]");

												$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET sector_id=$dest_sector, dev_emerwarp=dev_emerwarp-1,cleared_defences=' ', on_planet='N' WHERE ship_id=$ship_id");
												db_op_result($debug_query,__LINE__,__FILE__);

												log_move($targetinfo['player_id'],$targetship['ship_id'],$source_sector,$dest_sector,$shipinfo['class'],$shipinfo['cloak'],$zones['zone_id']);
												echo "$l_att_ewd<BR><BR>";
											}
											else
											{
												echo "<BR>$targetinfo[character_name]". $l_att_sdest ."<BR>";
												if ($targetship['dev_escapepod'] == "Y")
												{
													$rating=round($targetinfo['rating']/2);
													echo "$l_att_espod<BR><BR>";

													player_ship_destroyed($ship_id, $targetinfo['player_id'], $rating, $playerinfo['player_id'], $playerinfo['rating']);

													playerlog($targetinfo['player_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|Y");
													///
													if ($spy_success_factor)
													{
														spy_ship_destroyed($targetship['ship_id'], $playerinfo['player_id']);
													}

												   if ($dig_success_factor)
												   {
													   dig_ship_destroyed($targetship['ship_id'], $playerinfo['player_id']);
												   }

													$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $targetship[ship_id] and active='P'"); 
													db_op_result($debug_query,__LINE__,__FILE__);

													collect_bounty($playerinfo['player_id'],$targetinfo['player_id']);
												}
												else
												{
													playerlog($targetinfo['player_id'], LOG_ATTACK_LOSE, "$playerinfo[character_name]|N");
													db_kill_player($targetinfo['player_id'], $playerinfo['player_id'], $playerinfo['rating']);
													collect_bounty($playerinfo['player_id'],$targetinfo['player_id']);
												}
											}
										}
										$result4->MoveNext();
									}
// end
								}
								calc_ownership($shipinfo[sector_id]);
								playerlog($ownerinfo['player_id'], LOG_PLANET_novaED_D, "$planetinfo[name]|$shipinfo[sector_id]|$playerinfo[character_name]");
								gen_score($ownerinfo['player_id']);

								$playernames = $playerinfo['character_name'];
								insert_news($playernames, 1, "nova");

								close_database();
								echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=main.php\">";
							}else{
								$planetinfo['computer'] = floor($planetinfo['computer'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['sensors'] = floor($planetinfo['sensors'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['beams'] = floor($planetinfo['beams'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['torp_launchers'] = floor($planetinfo['torp_launchers'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['shields'] = floor($planetinfo['shields'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['jammer'] = floor($planetinfo['jammer'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['cloak'] = floor($planetinfo['cloak'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['torps'] = floor($planetinfo['torps'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['fighters'] = floor($planetinfo['fighters'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['colonists'] = floor($planetinfo['colonists'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['credits'] = floor($planetinfo['credits'] * (rand($dev_nova_damage, 100) * 0.01));
								$planetinfo['energy'] = floor($planetinfo['energy'] * (rand($dev_nova_damage, 100) * 0.01));

								$debug_query = $db->Execute("UPDATE $dbtables[planets] SET torps=$planetinfo[torps], fighters=$planetinfo[fighters], colonists=$planetinfo[colonists], credits=$planetinfo[credits], energy=$planetinfo[energy], computer=$planetinfo[computer], sensors=$planetinfo[sensors], beams=$planetinfo[beams], torp_launchers=$planetinfo[torp_launchers], shields=$planetinfo[shields], jammer=$planetinfo[jammer], cloak=$planetinfo[cloak] WHERE planet_id=$planet_id");
								db_op_result($debug_query,__LINE__,__FILE__);

								set_max_credits($planet_id);

								echo "$l_planet_novamiss<BR>";
								$playernames = $playerinfo['character_name'];
								insert_news($playernames, 1, "novamiss");

								if(rand(1, 100) <= $dev_nova_explode){
									$averagetechlvl = ($shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['power'] + $shipinfo['computer'] + $shipinfo['sensors'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['cloak'] + $shipinfo['armour'] + $shipinfo['ecm']) / 11;
									if($averagetechlvl > $dev_nova_destroylevel){
										$shipinfo['hull'] = floor($shipinfo['hull'] * (rand(50, 75) * 0.01));
										$shipinfo['engines'] = floor($shipinfo['engines'] * (rand(50, 75) * 0.01));
										$shipinfo['power'] = floor($shipinfo['power'] * (rand(50, 75) * 0.01));
										$shipinfo['computer'] = floor($shipinfo['computer'] * (rand(50, 75) * 0.01));
										$shipinfo['sensors'] = floor($shipinfo['sensors'] * (rand(50, 75) * 0.01));
										$shipinfo['beams'] = floor($shipinfo['beams'] * (rand(50, 75) * 0.01));
										$shipinfo['torp_launchers'] = floor($shipinfo['torp_launchers'] * (rand(50, 75) * 0.01));
										$shipinfo['shields'] = floor($shipinfo['shields'] * (rand(50, 75) * 0.01));
										$shipinfo['cloak'] = floor($shipinfo['cloak'] * (rand(50, 75) * 0.01));
										$shipinfo['armour'] = floor($shipinfo['armour'] * (rand(50, 75) * 0.01));
										$shipinfo['armour_pts'] = floor($shipinfo['armour_pts'] * (rand(50, 75) * 0.01));
										$shipinfo['torps'] = floor($shipinfo['torps'] * (rand(50, 75) * 0.01));
										$shipinfo['fighters'] = floor($shipinfo['fighters'] * (rand(50, 75) * 0.01));
										$shipinfo['credits'] = floor($shipinfo['credits'] * (rand(50, 75) * 0.01));
										$shipinfo['energy'] = floor($shipinfo['energy'] * (rand(50, 75) * 0.01));
										$shipinfo['ecm'] = floor($shipinfo['ecm'] * (rand(50, 75) * 0.01));

										echo "<font color='#ff0000' size='4'><B>$l_planet_novaexplode</B></font>";

										$debug_query = $db->Execute("UPDATE $dbtables[ships] SET engines=$shipinfo[engines], power=$shipinfo[power], armour=$shipinfo[armour], armour_pts=$shipinfo[armour_pts], torps=$shipinfo[torps], fighters=$shipinfo[fighters], hull=$shipinfo[hull], energy=$shipinfo[energy], computer=$shipinfo[computer], sensors=$shipinfo[sensors], beams=$shipinfo[beams], torp_launchers=$shipinfo[torp_launchers], shields=$shipinfo[shields], cloak=$shipinfo[cloak], ecm=$shipinfo[ecm] WHERE ship_id=$shipinfo[ship_id]");
										db_op_result($debug_query,__LINE__,__FILE__);
									}else{
										if ($shipinfo['dev_escapepod'] == "Y")
										{
											echo "$l_cmb_escapepodlaunched<BR><BR>";
//											echo "<BR><BR>player_id=$onplanet[player_id]<BR><BR>";
											player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $playerinfo['rating'], 0, 0);

											playerlog($shipinfo['player_id'],LOG_SHIP_novaED_D, "$playerinfo[character_name]|Y");

											if ($spy_success_factor)
											{
											  spy_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
											}
											if ($dig_success_factor)
											{
												dig_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
											}

											$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
											db_op_result($debug_query,__LINE__,__FILE__);

// AATrade
											$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
											insert_news($playernames, 1, "targetepod");
// end
										}else{
											playerlog($playerinfo['player_id'], LOG_SHIP_novaED_D, "$playerinfo[character_name]|N");
											db_kill_player($playerinfo['player_id'], 0, 0);
// AATrade
											$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
											insert_news($playernames, 1, "targetdies");
//end
										}
										echo "<font color='#ff0000' size='4'><B>$l_planet_novaexplode2</B></font>";
									}
								}
							}
						}else{
							$averagetechlvl = ($shipinfo['hull'] + $shipinfo['engines'] + $shipinfo['power'] + $shipinfo['computer'] + $shipinfo['sensors'] + $shipinfo['beams'] + $shipinfo['torp_launchers'] + $shipinfo['shields'] + $shipinfo['cloak'] + $shipinfo['armour'] + $shipinfo['ecm']) / 11;
							if($averagetechlvl > $dev_nova_destroylevel){
								$shipinfo['hull'] = floor($shipinfo['hull'] * (rand(50, 75) * 0.01));
								$shipinfo['engines'] = floor($shipinfo['engines'] * (rand(50, 75) * 0.01));
								$shipinfo['power'] = floor($shipinfo['power'] * (rand(50, 75) * 0.01));
								$shipinfo['computer'] = floor($shipinfo['computer'] * (rand(50, 75) * 0.01));
								$shipinfo['sensors'] = floor($shipinfo['sensors'] * (rand(50, 75) * 0.01));
								$shipinfo['beams'] = floor($shipinfo['beams'] * (rand(50, 75) * 0.01));
								$shipinfo['torp_launchers'] = floor($shipinfo['torp_launchers'] * (rand(50, 75) * 0.01));
								$shipinfo['shields'] = floor($shipinfo['shields'] * (rand(50, 75) * 0.01));
								$shipinfo['cloak'] = floor($shipinfo['cloak'] * (rand(50, 75) * 0.01));
								$shipinfo['armour'] = floor($shipinfo['armour'] * (rand(50, 75) * 0.01));
								$shipinfo['armour_pts'] = floor($shipinfo['armour_pts'] * (rand(50, 75) * 0.01));
								$shipinfo['torps'] = floor($shipinfo['torps'] * (rand(50, 75) * 0.01));
								$shipinfo['fighters'] = floor($shipinfo['fighters'] * (rand(50, 75) * 0.01));
								$shipinfo['credits'] = floor($shipinfo['credits'] * (rand(50, 75) * 0.01));
								$shipinfo['energy'] = floor($shipinfo['energy'] * (rand(50, 75) * 0.01));
								$shipinfo['ecm'] = floor($shipinfo['ecm'] * (rand(50, 75) * 0.01));

								echo "<font color='#ff0000' size='4'><B>$l_planet_novaexplode</B></font>";

								$debug_query = $db->Execute("UPDATE $dbtables[ships] SET engines=$shipinfo[engines], power=$shipinfo[power], armour=$shipinfo[armour], armour_pts=$shipinfo[armour_pts], torps=$shipinfo[torps], fighters=$shipinfo[fighters], hull=$shipinfo[hull], energy=$shipinfo[energy], computer=$shipinfo[computer], sensors=$shipinfo[sensors], beams=$shipinfo[beams], torp_launchers=$shipinfo[torp_launchers], shields=$shipinfo[shields], cloak=$shipinfo[cloak], ecm=$shipinfo[ecm] WHERE ship_id=$shipinfo[ship_id]");
								db_op_result($debug_query,__LINE__,__FILE__);
							}else{
								if ($shipinfo['dev_escapepod'] == "Y")
								{
									echo "$l_cmb_escapepodlaunched<BR><BR>";
//									echo "<BR><BR>player_id=$onplanet[player_id]<BR><BR>";
									player_ship_destroyed($shipinfo['ship_id'], $playerinfo['player_id'], $playerinfo['rating'], 0, 0);

									playerlog($shipinfo['player_id'],LOG_SHIP_novaED_D, "$playerinfo[character_name]|Y");

									if ($spy_success_factor)
									{
									  spy_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
									}
									if ($dig_success_factor)
									{
										dig_ship_destroyed($shipinfo['ship_id'],$playerinfo['player_id']);
									}

									$debug_query = $db->Execute("DELETE FROM $dbtables[probe] WHERE ship_id = $shipinfo[ship_id] and active='P'"); 
									db_op_result($debug_query,__LINE__,__FILE__);

// AATrade
									$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
									insert_news($playernames, 1, "targetepod");
// end
								}else{
									playerlog($playerinfo['player_id'], LOG_SHIP_novaED_D, "$playerinfo[character_name]|N");
									db_kill_player($playerinfo['player_id'], 0, 0);
// AATrade
									$playernames = $playerinfo['character_name']."|".$playerinfo['character_name'];
									insert_news($playernames, 1, "targetdies");
//end
								}
								echo "<font color='#ff0000' size='4'><B>$l_planet_novaexplode2</B></font>";
							}
						}
					}else{
						echo "$l_planet_novaturns<BR>";
					}
				}else{
					echo "$l_planet_novafed<BR>";
				}
			}else{
					echo "$l_planet_nonova<BR>";
			}
		}else{
				echo "$l_planet_wrongclass<BR>";
		}
		echo "<BR><a href='planet.php?planet_id=$planet_id'>$l_clickme</a> $l_toplanetmenu<BR><BR>";

		if ($allow_ibank)
		{
			echo "$l_ifyouneedplan <A HREF=\"igb.php?planet_id=$planet_id\">$l_igb_term</A>.<BR><BR>";
		}

		echo "<A HREF =\"bounty.php\">$l_by_placebounty</A><p>";
		TEXT_GOTOMAIN();
		include ("footer.php");
		die();

}

close_database();
?>