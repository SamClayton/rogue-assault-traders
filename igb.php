<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: igb.php

include ("config/config.php");
include ("languages/$langdir/lang_igb.inc");

$title=$l_igb_title;

if (checklogin() or $tournament_setup_access == 1) {
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

$result = $db->Execute("SELECT * FROM $dbtables[ibank_accounts] WHERE player_id=$playerinfo[player_id]");
$account = $result->fields;

if ((!isset($command)) || ($command == ''))
{
$command='';
}

$how_many2 = 0;
$result3 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE sector_id=$shipinfo[sector_id]");
if ($result3)
	$how_many2 = $result3->RecordCount();

if($sectorinfo['port_type'] == "casino")
{
	$how_many2 = 0;
}
else
{
	$how_many2 = 1;
}

if (!$allow_ibank || $how_many2 == 0)
  igb_error($l_igb_malfunction, "main.php");

if ($command == 'withdraw'){ //withdraw menu
  igb_withdraw();
  include ("footer.php");
  die();
}
elseif ($command == 'withdraw2'){ //withdraw operation
  igb_withdraw2();
  include ("footer.php");
  die();
}
elseif ($command == 'deposit'){ //deposit menu
  igb_deposit();
  include ("footer.php");
  die();
}
elseif ($command == 'deposit2'){ //deposit operation
  igb_deposit2();
  include ("footer.php");
  die();
}
elseif ($command == 'transfer'){ //main transfer menu
  igb_transfer();
  include ("footer.php");
  die();
}
elseif ($command == 'transfer2'){ //specific transfer menu (ship or planet)
  igb_transfer2();
  include ("footer.php");
  die();
}
elseif ($command == 'transfer3'){ //transfer operation
  igb_transfer3();
  include ("footer.php");
  die();
}
elseif ($command == 'loans'){ //loans menu
  igb_loans();
  include ("footer.php");
  die();
}
elseif ($command == 'borrow'){ //borrow operation
  igb_borrow();
  include ("footer.php");
  die();
}
elseif ($command == 'repay'){ //repay operation
  igb_repay();
  include ("footer.php");
  die();
}
elseif ($command == 'consolidate'){ //consolidate menu
  igb_consolidate();
  include ("footer.php");
  die();
}
elseif ($command == 'consolidate2'){ //consolidate compute
  igb_consolidate2();
  include ("footer.php");
  die();
}
elseif ($command == 'consolidate3'){ //consolidate operation
  igb_consolidate3();
  include ("footer.php");
  die();
}
else
{

$smarty->assign("l_igb_welcometoigb", $l_igb_welcometoigb);
$smarty->assign("l_igb_accountholder", $l_igb_accountholder);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("playername", $playerinfo['character_name']);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("l_igb_credit_symbol", $l_igb_credit_symbol);
$smarty->assign("l_igb_operations", $l_igb_operations);
$smarty->assign("l_igb_withdraw", $l_igb_withdraw);
$smarty->assign("l_igb_deposit", $l_igb_deposit);
$smarty->assign("l_igb_transfer", $l_igb_transfer);
$smarty->assign("l_igb_loans", $l_igb_loans);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);
$smarty->assign("accountbalance", NUMBER($account['balance']));

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_main.tpl");
include ("footer.php");
die();
}

function igb_withdraw()
{
  global $playerinfo;
  global $account,$smarty;
  global $l_igb_withdrawfunds, $l_igb_fundsavailable, $l_igb_selwithdrawamount;
  global $l_igb_withdraw, $l_igb_back, $l_igb_logout;
  global $l_igb_welcometoigb, $l_igb_accountholder, $l_igb_back, $l_igb_logout;
  global $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_withdraw, $l_igb_transfer;
  global $l_igb_deposit, $l_igb_credit_symbol, $l_igb_operations, $l_igb_loans;
  global $templatename;

$smarty->assign("l_igb_withdrawfunds", $l_igb_withdrawfunds);
$smarty->assign("l_igb_accountholder", $l_igb_accountholder);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("playername", $playerinfo['character_name']);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("l_igb_credit_symbol", $l_igb_credit_symbol);
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("l_igb_selwithdrawamount", $l_igb_selwithdrawamount);
$smarty->assign("l_igb_withdraw", $l_igb_withdraw);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_withdraw.tpl");
}

function igb_deposit()
{
  global $playerinfo;
  global $account,$smarty;
  global $l_igb_depositfunds, $l_igb_fundsavailable, $l_igb_seldepositamount;
  global $l_igb_deposit, $l_igb_back, $l_igb_logout,$l_igb_maximum,$l_igb_igbaccount;
  global $l_igb_welcometoigb, $l_igb_accountholder, $l_igb_back, $l_igb_logout, $l_credits;
  global $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_withdraw, $l_igb_transfer;
  global $l_igb_deposit, $l_igb_credit_symbol, $l_igb_operations, $l_igb_loans, $max_igb_storage;
  global $templatename;

$smarty->assign("l_igb_depositfunds", $l_igb_depositfunds);
$smarty->assign("l_igb_accountholder", $l_igb_accountholder);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("playername", $playerinfo['character_name']);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("l_igb_credit_symbol", $l_igb_credit_symbol);
$smarty->assign("l_igb_seldepositamount", $l_igb_seldepositamount);
$smarty->assign("max_igb_storage", $max_igb_storage);
$smarty->assign("l_igb_deposit", $l_igb_deposit);
$smarty->assign("l_igb_maximum", $l_igb_maximum);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("max_igb_storage", NUMBER($max_igb_storage));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_deposit.tpl");
}

function igb_transfer()
{
  global $playerinfo;
  global $account,$smarty;
  global $l_igb_transfertype, $l_igb_toanothership, $l_igb_shiptransfer, $l_igb_fromplanet, $l_igb_source, $l_igb_consolidate;
  global $l_igb_unnamed, $l_igb_in, $l_igb_none, $l_igb_planettransfer, $l_igb_back, $l_igb_logout, $l_igb_destination, $l_igb_conspl;
  global $db, $dbtables, $max_igb_storage;
  global $templatename;

  $res = $db->Execute("SELECT character_name, player_id FROM $dbtables[players] ORDER BY character_name ASC");
  while (!$res->EOF)
  {
	$ships[]=$res->fields;
	$res->MoveNext();
  }

  $res = $db->Execute("SELECT name, planet_id, sector_id FROM $dbtables[planets] WHERE owner=$playerinfo[player_id] ORDER BY sector_id ASC");
  while (!$res->EOF)
  {
	$planets[]=$res->fields;
	$res->MoveNext();
  }

$smarty->assign("l_igb_transfertype", $l_igb_transfertype);
$smarty->assign("l_igb_toanothership", $l_igb_toanothership);

	$shipcount = 0;
  foreach($ships as $ship)
  {
	$shipid[$shipcount] = $ship['player_id'];
	$playername[$shipcount] = $ship['character_name'];
	$shipcount++;
  }

$smarty->assign("shipcount", $shipcount);
$smarty->assign("shipid", $shipid);
$smarty->assign("playername", $playername);

$smarty->assign("l_igb_shiptransfer", $l_igb_shiptransfer);
$smarty->assign("l_igb_fromplanet", $l_igb_fromplanet);
$smarty->assign("l_igb_source", $l_igb_source);

	$planetcount = 0;
$smarty->assign("isplanets", isset($planets));
  if (isset($planets))
  {
	foreach($planets as $planet)
	{
	  if (empty($planet['name']))
		$planet['name'] = $l_igb_unnamed;
		$planetid[$planetcount] = $planet['planet_id'];
		$planetname[$planetcount] = $planet['name'];
		$planetsector[$planetcount] = $planet['sector_id'];
	$planetcount++;
	}
  }
  else
  {
	$smarty->assign("l_igb_none", $l_igb_none);
  }

$smarty->assign("planetcount", $planetcount);
$smarty->assign("l_igb_in", $l_igb_in);
$smarty->assign("planetid", $planetid);
$smarty->assign("planetname", $planetname);
$smarty->assign("planetsector", $planetsector);
$smarty->assign("l_igb_destination", $l_igb_destination);

$smarty->assign("l_igb_planettransfer", $l_igb_planettransfer);

$smarty->assign("l_igb_conspl", $l_igb_conspl);
$smarty->assign("l_igb_consolidate", $l_igb_consolidate);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_transfer.tpl");
}

function igb_transfer2()
{
  global $playerinfo;
  global $account,$smarty;
  global $player_id;
  global $splanet_id;
  global $dplanet_id;
  global $igb_min_turns;
  global $igb_svalue;
  global $ibank_paymentfee;
  global $igb_trate, $max_igb_storage;
  global $l_igb_sendyourself, $l_igb_unknowntargetship, $l_igb_min_turns, $l_igb_min_turns2;
  global $l_igb_mustwait, $l_igb_shiptransfer, $l_igb_igbaccount, $l_igb_maxtransfer;
  global $l_igb_unlimited, $l_igb_maxtransferpercent, $l_igb_transferrate, $l_igb_recipient;
  global $l_igb_seltransferamount, $l_igb_transfer, $l_igb_back, $l_igb_logout, $l_igb_in;
  global $l_igb_errplanetsrcanddest, $l_igb_errunknownplanet, $l_igb_unnamed;
  global $l_igb_errnotyourplanet, $l_igb_planettransfer, $l_igb_srcplanet, $l_igb_destplanet;
  global $l_igb_transferrate2, $l_igb_seltransferamount, $l_igb_errnobase;
  global $db, $dbtables;
  global $templatename;

$smarty->assign("isplayer", isset($player_id));

  if (isset($player_id)) //ship transfer
  {
	$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$player_id");

	if ($playerinfo['player_id'] == $player_id)
	  igb_error($l_igb_sendyourself, "igb.php?command=transfer");

	if (!$res || $res->EOF)
	  igb_error($l_igb_unknowntargetship, "igb.php?command=transfer");

	$target = $res->fields;

	if ($target['turns_used'] < $igb_min_turns)
	{
	  $l_igb_min_turns = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns);
	  $l_igb_min_turns = str_replace("[igb_target_char_name]", $target['character_name'], $l_igb_min_turns);
	  igb_error($l_igb_min_turns, "igb.php?command=transfer");
	}

	if ($playerinfo['turns_used'] < $igb_min_turns)
	{
	  $l_igb_min_turns2 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns2);
	  igb_error($l_igb_min_turns2, "igb.php?command=transfer");
	}

	if ($igb_trate > 0)
	{
	  $curtime = time();
	  $curtime -= $igb_trate * 60;
	  $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM $dbtables[igb_transfers] WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[player_id] AND dest_id=$target[player_id]");
	  if (!$res->EOF)
	  {
		$time = $res->fields;
		$difftime = ($time['time'] - $curtime) / 60;
		$l_igb_mustwait = str_replace("[igb_target_char_name]", $target['character_name'], $l_igb_mustwait);
		$l_igb_mustwait = str_replace("[igb_trate]", NUMBER($igb_trate), $l_igb_mustwait);
		$l_igb_mustwait = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait);
		igb_error($l_igb_mustwait, "igb.php?command=transfer");
	  }
	}

$smarty->assign("l_igb_shiptransfer", $l_igb_shiptransfer);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("igb_svalue", $igb_svalue);

	if ($igb_svalue == 0){
		$smarty->assign("l_igb_maxtransfer", $l_igb_maxtransfer);
		$smarty->assign("l_igb_unlimited", $l_igb_unlimited);
	}
	else
	{
	  $percent = $igb_svalue * 100;
	  $score = gen_score($playerinfo['player_id']);
	  $maxtrans = $score * $score * $igb_svalue;

	  $l_igb_maxtransferpercent = str_replace("[igb_percent]", $percent, $l_igb_maxtransferpercent);
		$smarty->assign("l_igb_maxtransferpercent", $l_igb_maxtransferpercent);
		$smarty->assign("maxtrans", NUMBER($maxtrans));
	}

	$percent = $ibank_paymentfee * 100;

	$l_igb_transferrate = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate);

	$smarty->assign("l_igb_recipient", $l_igb_recipient);
	$smarty->assign("targetname", $target['character_name']);
	$smarty->assign("l_igb_seltransferamount", $l_igb_seltransferamount);
	$smarty->assign("l_igb_transfer", $l_igb_transfer);
	$smarty->assign("player_id", $player_id);
	$smarty->assign("l_igb_transferrate", $l_igb_transferrate);
	$smarty->assign("l_igb_back", $l_igb_back);
	$smarty->assign("l_igb_logout", $l_igb_logout);
  }
  else
  {
	if ($splanet_id == $dplanet_id)
	  igb_error($l_igb_errplanetsrcanddest, "igb.php?command=transfer");

	$res = $db->Execute("SELECT name, credits, owner, sector_id FROM $dbtables[planets] WHERE planet_id=$splanet_id");
	if (!$res || $res->EOF)
	  igb_error($l_igb_errunknownplanet, "igb.php?command=transfer");
	$source = $res->fields;

	if (empty($source['name']))
	  $source[name]=$l_igb_unnamed;

	$res = $db->Execute("SELECT name, credits, owner, sector_id, base FROM $dbtables[planets] WHERE planet_id=$dplanet_id");
	if (!$res || $res->EOF)
	  igb_error($l_igb_errunknownplanet, "igb.php?command=transfer");
	$dest = $res->fields;

	if (empty($dest['name']))
	  $dest[name]=$l_igb_unnamed;
	if ($dest['base'] == 'N')
	  igb_error($l_igb_errnobase, "igb.php?command=transfer");

	if ($source['owner'] != $playerinfo['player_id'] || $dest['owner'] != $playerinfo['player_id'])
	  igb_error($l_igb_errnotyourplanet, "igb.php?command=transfer");

	$percent = $ibank_paymentfee * 100;

	$l_igb_transferrate2 = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate2);
	$smarty->assign("l_igb_planettransfer", $l_igb_planettransfer);
	$smarty->assign("l_igb_srcplanet", $l_igb_srcplanet);
	$smarty->assign("sourcename", $source['name']);
	$smarty->assign("l_igb_in", $l_igb_in);
	$smarty->assign("sourcesector", $source['sector_id']);
	$smarty->assign("sourcecredits", NUMBER($source['credits']));
	$smarty->assign("l_igb_destplanet", $l_igb_destplanet);
	$smarty->assign("destname", $dest['name']);
	$smarty->assign("destsector", $dest['sector_id']);
	$smarty->assign("destcredits", NUMBER($dest['credits']));
	$smarty->assign("l_igb_seltransferamount", $l_igb_seltransferamount);
	$smarty->assign("l_igb_transfer", $l_igb_transfer);
	$smarty->assign("splanet_id", $splanet_id);
	$smarty->assign("dplanet_id", $dplanet_id);
	$smarty->assign("l_igb_transferrate2", $l_igb_transferrate2);
	$smarty->assign("l_igb_maxtransfer", $l_igb_maxtransfer);
	$smarty->assign("transfercredits", ($dest['max_credits'] - $dest['credits']));
	$smarty->assign("l_igb_back", $l_igb_back);
	$smarty->assign("l_igb_logout", $l_igb_logout);
  }

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_transfer2.tpl");
}

function igb_transfer3()
{
  global $playerinfo;
  global $account, $max_igb_storage;
  global $player_id,$smarty;
  global $splanet_id;
  global $dplanet_id;
  global $igb_min_turns;
  global $igb_svalue;
  global $ibank_paymentfee;
  global $amount;
  global $igb_trate;
  global $l_igb_errsendyourself, $l_igb_unknowntargetship, $l_igb_min_turns3, $l_igb_min_turns4, $l_igb_mustwait2;
  global $l_igb_invalidtransferinput, $l_igb_nozeroamount, $l_igb_notenoughcredits, $l_igb_notenoughcredits2, $l_igb_in, $l_igb_to;
  global $l_igb_amounttoogreat, $l_igb_transfersuccessful, $l_igb_creditsto, $l_igb_transferamount, $l_igb_amounttransferred;
  global $l_igb_transferfee, $l_igb_igbaccount, $l_igb_back, $l_igb_logout, $l_igb_errplanetsrcanddest, $l_igb_errnotyourplanet;
  global $l_igb_errunknownplanet, $l_igb_unnamed, $l_igb_ctransferred, $l_igb_srcplanet, $l_igb_destplanet, $l_igb_ctransferredfrom;
  global $db, $dbtables;
  global $templatename;

  $amount = StripNonNum($amount);

   if ($amount < 0)
	 $amount = 0;

$smarty->assign("isplayer", isset($player_id));

  if (isset($player_id)) //ship transfer
  {
	//Need to check again to prevent cheating by manual posts

	$res = $db->Execute("SELECT * FROM $dbtables[players] WHERE player_id=$player_id");

	if ($playerinfo['player_id'] == $player_id)
	  igb_error($l_igb_errsendyourself, "igb.php?command=transfer");

	if (!$res || $res->EOF)
	  igb_error($l_igb_unknowntargetship, "igb.php?command=transfer");

	$target = $res->fields;

	if ($target['turns_used'] < $igb_min_turns)
	{
	  $l_igb_min_turns3 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns3);
	  $l_igb_min_turns3 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_min_turns3);
	  igb_error($l_igb_min_turns3, "igb.php?command=transfer");
	}

	if ($playerinfo['turns_used'] < $igb_min_turns)
	{
	  $l_igb_min_turns4 = str_replace("[igb_min_turns]", $igb_min_turns, $l_igb_min_turns4);
	  igb_error($l_igb_min_turns4, "igb.php?command=transfer");
	}

	if ($igb_trate > 0)
	{
	  $curtime = time();
	  $curtime -= $igb_trate * 60;
	  $res = $db->Execute("SELECT UNIX_TIMESTAMP(time) as time FROM $dbtables[igb_transfers] WHERE UNIX_TIMESTAMP(time) > $curtime AND source_id=$playerinfo[player_id] AND dest_id=$target[player_id]");
	  if (!$res->EOF)
	  {
		$time = $res->fields;
		$difftime = ($time['time'] - $curtime) / 60;
		$l_igb_mustwait2 = str_replace("[igb_target_char_name]", $target[character_name], $l_igb_mustwait2);
		$l_igb_mustwait2 = str_replace("[igb_trate]", NUMBER($igb_trate), $l_igb_mustwait2);
		$l_igb_mustwait2 = str_replace("[igb_difftime]", NUMBER($difftime), $l_igb_mustwait2);
		igb_error($l_igb_mustwait2, "igb.php?command=transfer");
	  }
	}


	if (($amount * 1) != $amount)
	  igb_error($l_igb_invalidtransferinput, "igb.php?command=transfer");

	if ($amount == 0)
	  igb_error($l_igb_nozeroamount, "igb.php?command=transfer");

	if ($amount > $account['balance'])
	  igb_error($l_igb_notenoughcredits, "igb.php?command=transfer");

	if ($igb_svalue != 0)
	{
	  $percent = $igb_svalue * 100;
	  $score = gen_score($playerinfo['player_id']);
	  $maxtrans = $score * $score * $igb_svalue;

	  if ($amount > $maxtrans)
		igb_error($l_igb_amounttoogreat, "igb.php?command=transfer");
	}

	$account['balance'] -= $amount;
	$amount2 = $amount * $ibank_paymentfee;
	$transfer = $amount - $amount2;

$smarty->assign("l_igb_transfersuccessful", $l_igb_transfersuccessful);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("l_igb_creditsto", $l_igb_creditsto);
$smarty->assign("targetname", $target['character_name']);
$smarty->assign("l_igb_transferamount", $l_igb_transferamount);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_transferfee", $l_igb_transferfee);
$smarty->assign("amount2", NUMBER($amount2));
$smarty->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

	$debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime,balance=balance-$amount WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime,balance=balance+$transfer WHERE player_id=$target[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);

	$stamp = date("Y-m-d H:i:s");
	$debug_query = $db->Execute("INSERT INTO $dbtables[igb_transfers] VALUES('', $playerinfo[player_id], $target[player_id], '$stamp')");
	db_op_result($debug_query,__LINE__,__FILE__);

	$temp = NUMBER($transfer);
	playerlog($target['player_id'], LOG_IGB_TRANSFER1, "$playerinfo[character_name]|$temp");
	playerlog($playerinfo['player_id'], LOG_IGB_TRANSFER2, "$target[character_name]|$temp");
  }
  else
  {
	if ($splanet_id == $dplanet_id)
	  igb_error($l_igb_errplanetsrcanddest, "igb.php?command=transfer");

	$res = $db->Execute("SELECT name, credits, owner, sector_id FROM $dbtables[planets] WHERE planet_id=$splanet_id");
	if (!$res || $res->EOF)
	  igb_error($l_igb_errunknownplanet, "igb.php?command=transfer");
	$source = $res->fields;

	if (empty($source['name']))
	  $source[name]=$l_igb_unnamed;

	$res = $db->Execute("SELECT name, credits, owner, sector_id FROM $dbtables[planets] WHERE planet_id=$dplanet_id");
	if (!$res || $res->EOF)
	  igb_error($l_igb_errunknownplanet, "igb.php?command=transfer");
	$dest = $res->fields;

	if (empty($dest['name']))
	  $dest[name]=$l_igb_unnamed;

	if ($source['owner'] != $playerinfo['player_id'] || $dest['owner'] != $playerinfo['player_id'])
	  igb_error($l_igb_errnotyourplanet, "igb.php?command=transfer");

	if ($amount > $source['credits'])
	  igb_error($l_igb_notenoughcredits2, "igb.php?command=transfer");

	$percent = $ibank_paymentfee * 100;

	if($dest['credits'] + $amount > $dest['max_credits']){
		$amount = $dest['max_credits'] - $dest['credits'];
	}

	if($amount < 0)
		$amount = 0;

	$source['credits'] -= $amount;
	$amount2 = $amount * $ibank_paymentfee;
	$transfer = $amount - $amount2;
	$dest['credits'] += $transfer;

$smarty->assign("l_igb_transfersuccessful", $l_igb_transfersuccessful);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("l_igb_ctransferredfrom", $l_igb_ctransferredfrom);
$smarty->assign("sourcename", $source['name']);
$smarty->assign("l_igb_to", $l_igb_to);
$smarty->assign("destname", $dest['name']);
$smarty->assign("l_igb_transferamount", $l_igb_transferamount);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_transferfee", $l_igb_transferfee);
$smarty->assign("amount2", NUMBER($amount2));
$smarty->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("l_igb_srcplanet", $l_igb_srcplanet);
$smarty->assign("sourcename", $source['name']);
$smarty->assign("l_igb_in", $l_igb_in);
$smarty->assign("sourcesector", $source['sector_id']);
$smarty->assign("sourcecredits", NUMBER($source['credits']));
$smarty->assign("l_igb_destplanet", $l_igb_destplanet);
$smarty->assign("destname", $dest['name']);
$smarty->assign("destsector", $dest['sector_id']);
$smarty->assign("destcredits", NUMBER($dest['credits']));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

	$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits-$amount WHERE planet_id=$splanet_id");
	db_op_result($debug_query,__LINE__,__FILE__);

	$debug_query = $db->Execute("UPDATE $dbtables[planets] SET credits=credits+$transfer WHERE planet_id=$dplanet_id");
	db_op_result($debug_query,__LINE__,__FILE__);
  }

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_transfer3.tpl");
}

function igb_deposit2()
{
  global $playerinfo;
  global $amount, $max_igb_storage;
  global $account,$smarty;
  global $l_igb_invaliddepositinput, $l_igb_nozeroamount2, $l_igb_notenoughcredits, $l_igb_accounts, $l_igb_logout;
  global $l_igb_operationsuccessful, $l_igb_creditstoyou, $l_igb_igbaccount, $l_igb_shipaccount, $l_igb_back;
  global $db, $dbtables;
  global $templatename;

  $amount = StripNonNum($amount);
  if (($amount * 1) != $amount)
	igb_error($l_igb_invaliddepositinput, "igb.php?command=deposit");

  if ($amount == 0)
	igb_error($l_igb_nozeroamount2, "igb.php?command=deposit");

  if ($amount > $playerinfo['credits'])
	igb_error($l_igb_notenoughcredits, "igb.php?command=deposit");

  if ($account['balance'] + $amount > $max_igb_storage and $max_igb_storage != 0)
	igb_error($l_igb_invaliddepositinput, "igb.php?command=deposit");

  $account['balance'] += $amount;
  $playerinfo['credits'] -= $amount;

  $debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime,balance=$account[balance] WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

  $debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=$playerinfo[credits] WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);


$smarty->assign("l_igb_operationsuccessful", $l_igb_operationsuccessful);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_creditstoyou", $l_igb_creditstoyou);
$smarty->assign("l_igb_accounts", $l_igb_accounts);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_deposit2.tpl");
}

function igb_withdraw2()
{
  global $playerinfo;
  global $amount;
  global $account,$smarty;
  global $l_igb_invalidwithdrawinput, $l_igb_nozeroamount3, $l_igb_notenoughcredits, $l_igb_accounts, $l_igb_shipaccount;
  global $l_igb_operationsuccessful, $l_igb_creditstoyourship, $l_igb_igbaccount, $l_igb_back, $l_igb_logout;
  global $db, $dbtables;
  global $templatename;

  $amount = StripNonNum($amount);
  if (($amount * 1) != $amount)
	igb_error($l_igb_invalidwithdrawinput, "igb.php?command=withdraw");

  if ($amount == 0)
	igb_error($l_igb_nozeroamount3, "igb.php?command=withdraw");

  if ($amount > $account['balance'])
	igb_error($l_igb_notenoughcredits, "igb.php?command=withdraw");

  $account['balance'] -= $amount;
  $playerinfo['credits'] += $amount;

$smarty->assign("l_igb_operationsuccessful", $l_igb_operationsuccessful);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_creditstoyourship", $l_igb_creditstoyourship);
$smarty->assign("l_igb_accounts", $l_igb_accounts);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_igbaccount", $l_igb_igbaccount);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("accountbalance", NUMBER($account['balance']));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

  $debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loantime=loantime,balance=balance-$amount WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

  $debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$amount WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_withdraw2.tpl");
}

function igb_loans()
{
  global $playerinfo, $account;
  global $ibank_loanlimit, $ibank_loanfactor, $ibank_loaninterest,$ibank_collateral_level,$l_igb_nocollateral;
  global $l_igb_loanstatus,$l_igb_shipaccount, $l_igb_currentloan, $l_igb_repay, $l_igb_loanrepaytime;
  global $l_igb_maxloanpercent, $l_igb_loanamount, $l_igb_borrow, $l_igb_loanrates;
  global $l_igb_back, $l_igb_logout, $igb_lrate, $l_igb_loantimeleft, $l_igb_loanlate, $l_igb_repayamount;
  global $db, $dbtables;
  global $templatename, $smarty;

$smarty->assign("l_igb_loanstatus", $l_igb_loanstatus);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("l_igb_currentloan", $l_igb_currentloan);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("accountloan", NUMBER($account['loan']));

  if ($account['loan'] != 0)
  {
	$curtime = time();
	$res = $db->Execute("SELECT UNIX_TIMESTAMP(loantime) as time FROM $dbtables[ibank_accounts] WHERE player_id=$playerinfo[player_id]");
	if (!$res->EOF)
	{
	  $time = $res->fields;
	}

	$difftime = ($curtime - $time['time']) / 60;

	$smarty->assign("l_igb_loantimeleft", $l_igb_loantimeleft);
	$smarty->assign("isloanlate", ($difftime > $igb_lrate));
	$smarty->assign("l_igb_loanlate", $l_igb_loanlate);

	if ($difftime <= $igb_lrate)
	{
	  $difftime=$igb_lrate - $difftime;
	  $hours = $difftime / 60;
	  $hours = (int) $hours;
	  $mins = $difftime % 60;
		$smarty->assign("hours", $hours);
		$smarty->assign("mins", $mins);
	}

	  $hours2 = $igb_lrate / 60;
	  $hours2 = (int) $hours2;
	  $mins2 = $igb_lrate % 60;

	$factor = $ibank_loanfactor *=100;
	$interest = $ibank_loaninterest *=100;

	$l_igb_loanrates = str_replace("[factor]", $factor, $l_igb_loanrates);
	$l_igb_loanrates = str_replace("[interest]", $interest, $l_igb_loanrates);

	$smarty->assign("l_igb_repayamount", $l_igb_repayamount);
	$smarty->assign("amount", NUMBER(MIN($playerinfo['credits'],$account['loan'])));
	$smarty->assign("l_igb_repay", $l_igb_repay);
	$smarty->assign("l_igb_loanrates", $l_igb_loanrates);
  }
  else
  {
	$percent = $ibank_loanlimit * 100;
	$score = gen_score($playerinfo['player_id']);
	$maxloan = $score * $score * $ibank_loanlimit;

	$l_igb_maxloanpercent = str_replace("[igb_percent]", $percent, $l_igb_maxloanpercent);
	$smarty->assign("l_igb_maxloanpercent", $l_igb_maxloanpercent);
	$smarty->assign("maxloan", NUMBER($maxloan));

	$hours2 = $igb_lrate / 60;
	$hours2 = (int) $hours2;
	$mins2 = $igb_lrate % 60;

	$l_igb_loanrepaytime = str_replace("[hours]", $hours2, $l_igb_loanrepaytime);
	$l_igb_loanrepaytime = str_replace("[mins]", $mins2, $l_igb_loanrepaytime);

	$factor = $ibank_loanfactor *=100;
	$interest = $ibank_loaninterest *=100;

	$l_igb_loanrates = str_replace("[factor]", $factor, $l_igb_loanrates);
	$l_igb_loanrates = str_replace("[interest]", $interest, $l_igb_loanrates);

	$debug_query = $db->Execute("SELECT * from $dbtables[planets] WHERE owner=$playerinfo[player_id] and base='Y'");
	db_op_result($debug_query,__LINE__,__FILE__);

	$reccount = $debug_query->RecordCount();

	$smarty->assign("iscollateral", ($reccount >= $ibank_collateral_level));
	$smarty->assign("l_igb_loanamount", $l_igb_loanamount);
	$smarty->assign("l_igb_borrow", $l_igb_borrow);
	$smarty->assign("l_igb_loanrates", $l_igb_loanrates);
	$smarty->assign("l_igb_loanrepaytime", $l_igb_loanrepaytime);
	$smarty->assign("l_igb_nocollateral", $l_igb_nocollateral);
}

$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_loans.tpl");
}

function igb_borrow()
{
  global $playerinfo, $account, $amount, $ibank_loanlimit, $ibank_loanfactor;
  global $l_igb_invalidamount,$l_igb_notwoloans, $l_igb_loantoobig;
  global $l_igb_takenaloan, $l_igb_loancongrats, $l_igb_loantransferred;
  global $l_igb_loanfee, $l_igb_amountowned, $igb_lrate, $l_igb_loanreminder;
  global $db, $dbtables, $l_igb_back, $l_igb_logout,$smarty;
  global $templatename;

  $amount = StripNonNum($amount);
  if (($amount * 1) != $amount)
	igb_error($l_igb_invalidamount, "igb.php?command=loans");

  if ($amount <= 0)
	igb_error($l_igb_invalidamount, "igb.php?command=loans");

  if ($account['loan'] != 0)
	igb_error($l_igb_notwoloans, "igb.php?command=loans");

  $score = gen_score($playerinfo['player_id']);
  $maxtrans = $score * $score * $ibank_loanlimit;

  if ($amount > $maxtrans)
	igb_error($l_igb_loantoobig, "igb.php?command=loans");

  $amount2 = $amount * $ibank_loanfactor;
  $amount3= $amount + $amount2;

  $hours = $igb_lrate / 60;
  $mins = $igb_lrate % 60;

  $l_igb_loanreminder = str_replace("[hours]", $hours, $l_igb_loanreminder);
  $l_igb_loanreminder = str_replace("[mins]", $mins, $l_igb_loanreminder);

$smarty->assign("l_igb_takenaloan", $l_igb_takenaloan);
$smarty->assign("l_igb_loancongrats", $l_igb_loancongrats);
$smarty->assign("l_igb_loantransferred", $l_igb_loantransferred);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_loanfee", $l_igb_loanfee);
$smarty->assign("amount2", NUMBER($amount2));
$smarty->assign("l_igb_amountowned", $l_igb_amountowned);
$smarty->assign("amount3", NUMBER($amount3));
$smarty->assign("l_igb_loanreminder", $l_igb_loanreminder);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

  $stamp = date("Y-m-d H:i:s");
  $debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loan=$amount3, loantime='$stamp' WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

  $debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits+$amount WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_borrow.tpl");
}

function igb_repay()
{
  global $playerinfo, $account, $amount;
  global $l_igb_notrepay, $l_igb_notenoughrepay,$l_igb_payloan;
  global $l_igb_shipaccount, $l_igb_currentloan, $l_igb_loanthanks, $l_igb_invalidamount;
  global $db, $dbtables, $l_igb_back, $l_igb_logout,$smarty;
  global $templatename;

  $amount = StripNonNum($amount);
  if (($amount * 1) != $amount)
	igb_error($l_igb_invalidamount, "igb.php?command=loans");

  if ($amount == 0)
	igb_error($l_igb_invalidamount, "igb.php?command=loans");

  if ($account['loan'] == 0)
	igb_error($l_igb_notrepay, "igb.php?command=loans");

  if ($amount > $account['loan'])
	$amount = $account['loan'];

  if ($amount > $playerinfo['credits'])
	igb_error($l_igb_notenoughrepay, "igb.php?command=loans");

  $playerinfo['credits']-=$amount;
  $account['loan']-=$amount;

$smarty->assign("l_igb_payloan", $l_igb_payloan);
$smarty->assign("l_igb_loanthanks", $l_igb_loanthanks);
$smarty->assign("l_igb_shipaccount", $l_igb_shipaccount);
$smarty->assign("playercredits", NUMBER($playerinfo['credits']));
$smarty->assign("l_igb_payloan", $l_igb_payloan);
$smarty->assign("amount", NUMBER($amount));
$smarty->assign("l_igb_currentloan", $l_igb_currentloan);
$smarty->assign("accountloan", NUMBER($account['loan']));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

  $debug_query = $db->Execute("UPDATE $dbtables[ibank_accounts] SET loan=loan-$amount WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

  $debug_query = $db->Execute("UPDATE $dbtables[players] SET credits=credits-$amount WHERE player_id=$playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_repay.tpl");
}

function igb_consolidate()
{
  global $playerinfo, $account, $dest;
  global $db, $dbtables,$smarty;
  global $l_igb_errunknownplanet, $l_igb_errnotyourplanet, $l_igb_transferrate3;
  global $l_igb_planettransfer, $l_igb_destplanet, $l_igb_in, $igb_tconsolidate;
  global $dplanet_id, $l_igb_unnamed, $l_igb_currentpl, $l_igb_consolrates;
  global $l_igb_minimum, $l_igb_maximum, $l_igb_back, $l_igb_logout;
  global $l_igb_planetconsolidate, $l_igb_compute, $ibank_paymentfee;
  global $templatename, $l_credits,$l_minplanetpercent;

  $percent = $ibank_paymentfee * 100;

  $l_igb_transferrate3 = str_replace("[igb_num_percent]", NUMBER($percent,1), $l_igb_transferrate3);
  $l_igb_transferrate3 = str_replace("[nbplanets]", $igb_tconsolidate, $l_igb_transferrate3);

  $destplanetcreds  = $dest['credits'];

$smarty->assign("l_igb_planetconsolidate", $l_igb_planetconsolidate);
$smarty->assign("l_igb_consolrates", $l_igb_consolrates);
$smarty->assign("l_igb_minimum", $l_igb_minimum);
$smarty->assign("l_igb_maximum", $l_igb_maximum);
$smarty->assign("l_igb_compute", $l_igb_compute);
$smarty->assign("l_igb_transferrate3", $l_igb_transferrate3);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);
$smarty->assign("l_credits", $l_credits);
$smarty->assign("l_minplanetpercent", $l_minplanetpercent);


$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_consolidate.tpl");
}

function igb_consolidate2()
{
  global $playerinfo, $account;
  global $db, $dbtables;
  global $dplanet_id, $minimum, $maximum,$maxplanetpercent, $percentage,$igb_tconsolidate, $ibank_paymentfee;
  global $l_igb_planetconsolidate, $l_igb_back, $l_igb_logout;
  global $l_igb_errunknownplanet, $l_igb_unnamed, $l_igb_errnotyourplanet;
  global $l_igb_currentpl, $l_igb_in, $l_igb_transferamount, $l_igb_plaffected;
  global $l_igb_transferfee, $l_igb_turncost, $l_igb_amounttransferred;
  global $l_igb_consolidate,$smarty;
  global $templatename;
 if ($percentage=="")
 	$percentage=100;
 if ($maxplanetpercent=="")
 	$maxplanetpercent=0;	
  $maxplanetpercent = min(100,StripNonNum($maxplanetpercent));
  	
  $minimum = StripNonNum($minimum);
  $maximum = StripNonNum($maximum);
  $percentage = min(100,StripNonNum($percentage));
  
  
  

  $query = "SELECT SUM(credits*($percentage/100)) as total, COUNT(*) as count from $dbtables[planets] WHERE owner=$playerinfo[player_id] AND credits != 0";

  if ($minimum != 0)
	$query .= " AND credits >= $minimum";

  if ($maximum != 0)
	$query .= " AND credits <= $maximum";
	
  if ($maxplanetpercent != 0 )	
		$query .= " AND ((credits/max_credits)*100)  >= $maxplanetpercent";
		
		
  $res = $db->Execute($query);
  $amount = $res->fields;

  $fee = $ibank_paymentfee * $amount['total'];

  $tcost = ceil($amount['count'] / $igb_tconsolidate);
  $transfer = $amount['total'] - $fee;

$smarty->assign("l_igb_planetconsolidate", $l_igb_planetconsolidate);
$smarty->assign("l_igb_transferamount", $l_igb_transferamount);
$smarty->assign("total", NUMBER($amount['total']));
$smarty->assign("l_igb_transferfee", $l_igb_transferfee);
$smarty->assign("fee", NUMBER($fee));
$smarty->assign("l_igb_plaffected", $l_igb_plaffected);
$smarty->assign("count", NUMBER($amount['count']));
$smarty->assign("l_igb_turncost", $l_igb_turncost);
$smarty->assign("tcost", NUMBER($tcost));
$smarty->assign("l_igb_amounttransferred", $l_igb_amounttransferred);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("minimum", $minimum);
$smarty->assign("maximum", $maximum);
$smarty->assign("percentage", $percentage);
$smarty->assign("maxplanetpercent", $maxplanetpercent);
$smarty->assign("l_igb_consolidate", $l_igb_consolidate);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_consolidate2.tpl");
}

function igb_consolidate3()
{
  global $playerinfo;
  global $db, $dbtables;
  global $dplanet_id, $minimum, $maximum,$percentage,$maxplanetpercent, $igb_tconsolidate, $ibank_paymentfee;
  global $l_igb_notenturns, $l_igb_back, $l_igb_logout, $l_igb_transfersuccessful;
  global $l_igb_currentpl, $l_igb_in, $l_igb_turncost, $l_igb_unnamed,$smarty;
  global $templatename;

  $minimum = StripNonNum($minimum);
  $maximum = StripNonNum($maximum);
 if ($percentage=="")
 	$percentage=100;
  $percentage = min(100,StripNonNum($percentage));
  
  if ($maxplanetpercent=="")
 	$maxplanetpercent=0;	
  $maxplanetpercent = min(100,StripNonNum($maxplanetpercent)); 
  
  $query = "SELECT SUM(credits*($percentage/100)) as total, COUNT(*) as count from $dbtables[planets] WHERE owner=$playerinfo[player_id] AND credits != 0";

  if ($minimum != 0)
	$query .= " AND credits >= $minimum";

  if ($maximum != 0)
	$query .= " AND credits <= $maximum";
	
  if ($maxplanetpercent != 0 )	
		$query .= " AND ((credits/max_credits)*100)  >= $maxplanetpercent";	
	

  $res = $db->Execute($query);
  $amount = $res->fields;

  $fee = $ibank_paymentfee * $amount['total'];

  $tcost = ceil($amount['count'] / $igb_tconsolidate);
  $transfer = $amount['total'] - $fee;

  if ($tcost > $playerinfo['turns'])
	igb_error($l_igb_notenturns, "igb.php?command=transfer");

$smarty->assign("l_igb_transfersuccessful", $l_igb_transfersuccessful);
$smarty->assign("l_igb_currentpl", $l_igb_currentpl);
$smarty->assign("l_igb_turncost", $l_igb_turncost);
$smarty->assign("transfer", NUMBER($transfer));
$smarty->assign("tcost", NUMBER($tcost));
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

  $query = "UPDATE $dbtables[planets] SET credits=credits-(credits*($percentage/100)) WHERE owner=$playerinfo[player_id] AND credits != 0";

  if ($minimum != 0)
	$query .= " AND credits >= $minimum";

  if ($maximum != 0)
	$query .= " AND credits <= $maximum";
 if ($maxplanetpercent != 0 )	
		$query .= " AND ((credits/max_credits)*100)  >= $maxplanetpercent";	
	
  $debug_query = $db->Execute($query);
  db_op_result($debug_query,__LINE__,__FILE__);

  $debug_query = $db->Execute("UPDATE $dbtables[players] SET turns=turns - $tcost, credits=credits + $transfer WHERE player_id = $playerinfo[player_id]");
  db_op_result($debug_query,__LINE__,__FILE__);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_consolidate3.tpl");
}

function igb_error($errmsg, $backlink, $title="Error!")
{
  global $l_igb_igberrreport, $l_igb_back, $l_igb_logout,$smarty,$templatename;

  $title = $l_igb_igberrreport;
  global $templatename;

$smarty->assign("title", $title);
$smarty->assign("errmsg", $errmsg);
$smarty->assign("backlink", $backlink);
$smarty->assign("l_igb_back", $l_igb_back);
$smarty->assign("l_igb_logout", $l_igb_logout);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."igb_error.tpl");
include ("footer.php");
die();
}

close_database();
?>
