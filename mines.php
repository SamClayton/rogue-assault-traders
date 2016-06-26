<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: mines.php

include ("config/config.php");
include ("languages/$langdir/lang_mines.inc");

$title = $l_mines_title;

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

//-------------------------------------------------------------------------------------------------

$result3 = $db->Execute ("SELECT * FROM $dbtables[sector_defence] WHERE sector_id=$shipinfo[sector_id] ");
$defenseinfo = $result3->fields;

//Put the defence information into the array "defenceinfo"
$i = 0;
$total_sector_fighters = 0;
$total_sector_mines = 0;
$owns_all = true;
$fighter_id = 0;
$mine_id = 0;

if ($result3 > 0)
{
	while (!$result3->EOF)
	{
		$defences[$i] = $result3->fields;
		if ($defences[$i]['defence_type'] == 'F')
		{
			$total_sector_fighters += $defences[$i]['quantity'];
		}
		else
		{
			$total_sector_mines += $defences[$i]['quantity'];
		}

		if ($defences[$i]['player_id'] != $playerinfo['player_id'])
		{
			$owns_all = false;
		}
		else
		{
			if ($defences[$i]['defence_type'] == 'F')
			{
				$fighter_id = $defences[$i]['defence_id'];
			}
			else
			{
				$mine_id = $defences[$i]['defence_id'];
			}
		}
		$i++;
		$result3->MoveNext();
	}
}

$num_defencesm = $i;

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_mines_noturn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."minesdie.tpl");
	include ("footer.php");
	die();
}

$res = $db->Execute("SELECT allow_defenses, $dbtables[universe].zone_id, owner FROM $dbtables[zones],$dbtables[universe] " .
					"WHERE sector_id=$shipinfo[sector_id] AND $dbtables[zones].zone_id=$dbtables[universe].zone_id");
$query97 = $res->fields;

if ($query97['allow_defenses'] == 'N')
{
	$smarty->assign("error_msg", $l_mines_nopermit);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."minesdie.tpl");
	include ("footer.php");
	die();
}
else
{
	if ($num_defencesm > 0)
	{
		if (!$owns_all)
		{
			$defence_owner = $defences[0]['player_id'];
			$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$defence_owner");
			$fighters_owner = $result2->fields;

			if ($fighters_owner['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
			{
				$smarty->assign("error_msg", $l_mines_nodeploy);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."minesdie.tpl");
				include ("footer.php");
				die();
			}
		}
	}

	if ($query97['allow_defenses'] == 'L')
	{
		$zone_owner = $query97['owner'];
		$result2 = $db->Execute("SELECT * from $dbtables[players] where player_id=$zone_owner");
		$zoneowner_info = $result2->fields;

		if ($zone_owner <> $playerinfo['player_id'])
		{
			 if ($zoneowner_info['team'] != $playerinfo['team'] || $playerinfo['team'] == 0)
			 {
				$smarty->assign("error_msg", $l_mines_nopermit);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."minesdie.tpl");
				include ("footer.php");
				die();
			 }
		}
	}

	if (!isset($nummines) or !isset($numfighters))
	{
		$availmines = NUMBER($shipinfo['torps']);
		$availfighters = NUMBER($shipinfo['fighters']);
		$l_mines_info1=str_replace("[sector]",$shipinfo['sector_id'], $l_mines_info1);
		$l_mines_info1=str_replace("[mines]",NUMBER($total_sector_mines), $l_mines_info1);
		$l_mines_info1=str_replace("[fighters]",NUMBER($total_sector_fighters), $l_mines_info1);
		$l_mines_info2=str_replace("[mines]",$availmines, $l_mines_info2);
		$l_mines_info2=str_replace("[fighters]",$availfighters, $l_mines_info2);

		$smarty->assign("l_mines_info1", $l_mines_info1);
		$smarty->assign("l_mines_info2", $l_mines_info2);
		$smarty->assign("l_mines_deploy", $l_mines_deploy);
		$smarty->assign("l_submit", $l_submit);
		$smarty->assign("l_reset", $l_reset);
		$smarty->assign("l_mines_att", $l_mines_att);
		$smarty->assign("l_fighters", $l_fighters);
		$smarty->assign("l_mines", $l_mines);
		$smarty->assign("shiptorps", $shipinfo['torps']);
		$smarty->assign("shipfighters", $shipinfo['fighters']);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."mines.tpl");
		include ("footer.php");
		die();
	}
	else
	{
		$nummines = stripnum($nummines);
		$numfighters = stripnum($numfighters);
		if (empty($nummines)) 
		{
			$nummines = 0;
		}

		if (empty($numfighters))
		{
			$numfighters = 0;
		}

		if ($nummines < 0) 
		{
			$nummines = 0;
		}

		if ($numfighters < 0) 
		{
			$numfighters =0;
		}

		if ($nummines > $shipinfo['torps'])
		{
			$showmines = $l_mines_notorps;
			$nummines = 0;
		}
		else
		{
			$l_mines_dmines=str_replace("[mines]",$nummines, $l_mines_dmines);
			$showmines = $l_mines_dmines;
		}

		if ($numfighters > $shipinfo['fighters'])
		{
			$showfighters = $l_mines_nofighters;
			$numfighters = 0;
		}
		else
		{
			$l_mines_dfighter=str_replace("[fighters]",$numfighters, $l_mines_dfighter);
			$showfighters = $l_mines_dfighter;
		}

		$stamp = date("Y-m-d H:i:s");
		if ($numfighters > 0)
		{
			if ($fighter_id != 0)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $numfighters " .
											"where defence_id = $fighter_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				$debug_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
											"(player_id,sector_id,defence_type,quantity) values " .
											"($playerinfo[player_id],$shipinfo[sector_id],'F',$numfighters)");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}

		if ($nummines > 0)
		{
			if ($mine_id != 0)
			{
				$debug_query = $db->Execute("UPDATE $dbtables[sector_defence] set quantity=quantity + $nummines " .
											"where defence_id = $mine_id");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
			else
			{
				$debug_query = $db->Execute("INSERT INTO $dbtables[sector_defence] " .
											"(player_id,sector_id,defence_type,quantity) values " .
											"($playerinfo[player_id],$shipinfo[sector_id],'M',$nummines)");
				db_op_result($debug_query,__LINE__,__FILE__);
			}
		}

		$debug_query = $db->Execute("UPDATE $dbtables[players] SET last_login='$stamp', turns=turns-1, turns_used=turns_used+1 " .
									"WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute("UPDATE $dbtables[ships] SET fighters=fighters-$numfighters, torps=torps-$nummines WHERE " .
									"ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
	}
	$smarty->assign("showfighters", $showfighters);
	$smarty->assign("showmines", $showmines);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."minesdeploy.tpl");
	include ("footer.php");
	die();
}

close_database();
?>
