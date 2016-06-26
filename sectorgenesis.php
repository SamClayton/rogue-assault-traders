<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: genesis.php

include ("config/config.php");
include ("languages/$langdir/lang_genesis.inc");


$title = $l_sgns_title;

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

$result3 = $db->Execute("SELECT planet_id FROM $dbtables[planets] WHERE sector_id='$shipinfo[sector_id]'");
$num_planets = $result3->RecordCount();

$res = $db->Execute("SELECT $dbtables[universe].zone_id, $dbtables[zones].allow_planet, $dbtables[zones].team_zone, " .
					"$dbtables[zones].owner FROM $dbtables[zones],$dbtables[universe] WHERE " .
					"$dbtables[zones].zone_id=$sectorinfo[zone_id] AND $dbtables[universe].sector_id = $shipinfo[sector_id]");
$query97 = $res->fields;

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$max_query = $db->Execute("SELECT * from $dbtables[universe] order by sector_id DESC");
db_op_result($max_query,__LINE__,__FILE__);

$sector_max = $max_query->fields['sector_id'];

function getsgcost($sector_id){

	global $db, $dbtables, $playerinfo, $shipinfo, $sector_max,$level_factor, $dev_sectorgenesis_price, $max_sglinks;

	$search_results_echo = NULL;
	$links = NULL;
	$search_depth = NULL;
	$sgcost = $dev_sectorgenesis_price * 2;
	$searching = 1;
	$count = 0;

	$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$sector_id");
	$sector_type = $sector_res->fields['sg_sector'];

	if($sector_type != 1)
		return $dev_sectorgenesis_price;

	$link_old = 0;

	while($searching == 1){
		$search_query = "SELECT distinct link_dest FROM $dbtables[links] WHERE link_start = $sector_id AND link_dest != link_start and ( link_dest != $link_old and  link_dest != $link_old )\n";
   		$debug_query = $db->Execute ($search_query) or die ("Invalid Query");
	   	$found = $debug_query->RecordCount();

		if ($found < $max_sglinks)
		{
			$links = $debug_query->fields;
			$count++;

			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$links[link_dest]");
			$sector_type = $sector_res->fields['sg_sector'];
			if($count == 50 or $sector_type != 1){
				$searching = 0;
				break;
			}
			
  			$sgcost = $sgcost * 2;
			$link_old = $sector_id;
			$sector_id = $links['link_dest'];
		}else{
			$searching = 0;
			$sgcost = 0;
			break;
		}
	}
	return $sgcost;
}

if ($playerinfo['turns'] < 1)
{
	$smarty->assign("error_msg", $l_gns_turn);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."sectorgenesisdie.tpl");
	include ("footer.php");
	die();
}

if ($shipinfo['on_planet'] == 'Y')
{
	$smarty->assign("error_msg", $l_gns_onplanet);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."sectorgenesisdie.tpl");
	include ("footer.php");
	die();
}

if ($shipinfo['dev_sectorgenesis'] < 1)
{
	$smarty->assign("error_msg", $l_sgns_nogenesis);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."sectorgenesisdie.tpl");
	include ("footer.php");
	die();
}

if ($query97['allow_planet'] == 'N')
{
	$smarty->assign("error_msg", $l_sgns_forbid);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."sectorgenesisdie.tpl");
	include ("footer.php");
	die();
}

mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);
//check sector and make sure its not past total sector total
$res = $db->Execute("SELECT * from $dbtables[links] where  link_start=$shipinfo[sector_id] ");
$tlinks = $res->RecordCount();

// if past sector total make sure there are less that 2 warp links
// select chain or select loop point.
	$sgcost = getsgcost($shipinfo['sector_id']);

if ($sglink==1){
	if ($playerinfo['credits'] < $sgcost){
		$smarty->assign("error_msg", $l_sgns_nocredits);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."sectorgenesisdie.tpl");
		include ("footer.php");
		die();
	}

	$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
	$sector_type = $sector_res->fields['sg_sector'];

	if (($sector_type != 1) or (($sector_type == 1) and ($tlinks < $max_sglinks))){
		$initsore = $ore_limit ;
		$initsorganics = $organics_limit ;
		$initsgoods = $goods_limit ;
		$initsenergy = $energy_limit ;
		$initbore = $ore_limit;
		$initborganics = $organics_limit ;
		$initbgoods = $goods_limit ;
		$initbenergy = $energy_limit ;
		$random_star = mt_rand(0,$max_star_size);

		$port_type= mt_rand(0,100);
		if ($port_type > 40){
			$port="none";
			$sgport_organics=0;
			$sgport_ore= 0;
			$sgport_goods= 0;
			$sgport_energy= 0;
			$sgorganics_price= 0;
			$sgore_price= 0;
			$sggoods_price=0;
			$sgenergy_price= 0;
		}elseif ($port_type >15){
			$random_port = mt_rand(1,4);
			if ($random_port==1){
				$port="goods";
			}elseif($random_port==2){
				$port="ore";
			}elseif($random_port==3){
				$port="organics";
			}else{
				$port="energy";
			}
			$sgport_organics=$initborganics;
			$sgport_ore= $initbore;
			$sgport_goods= $initsgoods;
			$sgport_energy= $initbenergy;
			$sgorganics_price= 0;
			$sgore_price= 0;
			$sggoods_price= 0;
			$sgenergy_price=0;
		}else{
			$random_port = mt_rand(1,3);
			if ($random_port==1){
				$port="upgrades";
			}elseif ($random_port==2){
				$port="devices";
			}else{
				$port="spacedock";
			}
			$sgport_organics=0;
			$sgport_ore= 0;
			$sgport_goods= 0;
			$sgport_energy= 0;
			$sgorganics_price= 0;
			$sgore_price= 0;
			$sggoods_price=0;
			$sgenergy_price= 0;
		}
		// Build Sector
		$debug_query = $db->Execute ("insert into $dbtables[universe] (sector_name,zone_id ,star_size,port_type,port_organics,port_ore ,port_goods ,port_energy ,organics_price,ore_price,goods_price,energy_price,x,y,z,beacon, sg_sector)values('',1,$random_star,'$port','$sgport_organics','$sgport_ore','$sgport_goods','$sgport_energy','$sgorganics_price','$sgore_price','$sggoods_price','$sgenergy_price',0,0,0,'', 1) ");
		db_op_result($debug_query,__LINE__,__FILE__);

		$res = $db->Execute("SELECT max(sector_id) as targetsector from $dbtables[universe] ");
		$target_sector = $res->fields['targetsector'];
		// Build warp link
		$debug_query = $db->Execute ("INSERT INTO $dbtables[links] SET   link_start=$target_sector, " .
									 "link_dest=$shipinfo[sector_id]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$debug_query = $db->Execute ("INSERT INTO $dbtables[links] SET link_start=$shipinfo[sector_id], link_dest=$target_sector");

		$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET dev_sectorgenesis =dev_sectorgenesis  - 1 WHERE ship_id=$shipinfo[ship_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$debug_query = $db->Execute ("UPDATE $dbtables[players] SET turns=turns-1, " .
									 "turns_used=turns_used+1, credits=credits-$sgcost WHERE player_id=$playerinfo[player_id]");
		db_op_result($debug_query,__LINE__,__FILE__);

		$smarty->assign("error_msg", $l_sgns_pcreate);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."sectorgenesisdie.tpl");
		include ("footer.php");
		die();
	}else{
		$smarty->assign("error_msg", str_replace("[limit]", "<font color=#00ff00><b>$max_sglinks</b></font>", $l_sgns_forbid2));
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."sectorgenesisdie.tpl");
		include ("footer.php");
		die();
	}
}else{
	$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
	$sector_type = $sector_res->fields['sg_sector'];
	if(($sector_type == 1)and ($rslink==1)){
		$res = $db->Execute("SELECT * from $dbtables[links] where  link_start=$shipinfo[sector_id] ");
		$tlinks = $res->RecordCount();
		$res1 = $db->Execute("SELECT * from $dbtables[universe] where  sector_id=$target_sector");
		db_op_result($debug_query,__LINE__,__FILE__);
		$row1 = $res1->fields;

		$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$target_sector");
		$sector_type = $sector_res->fields['sg_sector'];

		if (($tlinks < $max_sglinks) and ($row1['zone_id'] !=2) and ($sector_type != 1)){
		// Build warp link
			$debug_query = $db->Execute ("INSERT INTO $dbtables[links] SET   link_start=$target_sector, " .
										 "link_dest=$shipinfo[sector_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$debug_query = $db->Execute ("INSERT INTO $dbtables[links] SET link_start=$shipinfo[sector_id], link_dest=$target_sector");

			$debug_query = $db->Execute ("UPDATE $dbtables[ships] SET dev_sectorgenesis =dev_sectorgenesis  - 1 WHERE ship_id=$shipinfo[ship_id]");
			db_op_result($debug_query,__LINE__,__FILE__);

			$debug_query = $db->Execute ("UPDATE $dbtables[players] SET turns=turns-1, " .
										 "turns_used=turns_used+1 WHERE player_id=$playerinfo[player_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
			$smarty->assign("error_msg", $l_sgns_complete);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."sectorgenesisdie.tpl");
			include ("footer.php");
			die();
		}elseif($row1['zone_id'] ==2){
			$smarty->assign("error_msg", $l_sgns_forbid1);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."sectorgenesisdie.tpl");
			include ("footer.php");
			die();
		}else{
			$smarty->assign("error_msg", str_replace("[limit]", "<font color=#00ff00><b>$max_sglinks</b></font>", $l_sgns_forbid2));
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."sectorgenesisdie.tpl");
			include ("footer.php");
			die();
		}
	}else{
		if($sgcost != 0){
			$sector_res = $db->Execute("SELECT sg_sector FROM $dbtables[universe] WHERE sector_id=$shipinfo[sector_id]");
			$sector_type = $sector_res->fields['sg_sector'];
			$smarty->assign("sector_type", $sector_type);
			$smarty->assign("l_sgns_shipcredits", $l_sgns_shipcredits);
			$smarty->assign("credits", NUMBER($playerinfo['credits']));
			$smarty->assign("l_sgns_createcost", $l_sgns_createcost);
			$smarty->assign("sgcostnumber", NUMBER($sgcost));
			$smarty->assign("sgcost", $sgcost);
			$smarty->assign("l_sgcreate", $l_sgcreate);
			$smarty->assign("l_submit", $l_submit);
			$smarty->assign("l_reset", $l_reset);
			$smarty->assign("sector_max", $sector_max);
			$smarty->assign("shipsector", $shipinfo['sector_id']);
			$smarty->assign("l_sgcreatens", $l_sgcreatens);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."sectorgenesis.tpl");
			include ("footer.php");
			die();
		}else{
			$smarty->assign("error_msg", str_replace("[limit]", "<font color=#00ff00><b>$max_sglinks</b></font>", $l_sgns_forbid2));
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."sectorgenesisdie.tpl");
			include ("footer.php");
			die();
		}
	}
}

close_database();
?>
