<?php

  if (preg_match("/dig_funcs.php/i", $_SERVER['PHP_SELF'])) {
	  echo "You can not access this file directly!";
	  die();
  }


function buy_them_dig($player_id, $how_many = 1)
{
  global $db;
  global $dbtables;
  global $shipinfo;

  for($i=1; $i<=$how_many; $i++)
  {
	$debug_query = $db->Execute("INSERT INTO $dbtables[dignitary] (dig_id, active, owner_id, planet_id, ship_id, job_id, percent) values ('','N',$player_id,'0','$shipinfo[ship_id]','0','0.0')");
	db_op_result($debug_query,__LINE__,__FILE__);
  }  
}


function transfer_to_planet_dig($player_id, $planet_id, $how_many = 1)
{
  global $db;
  global $dbtables;
  global $max_dignitary_per_planet;
  global $shipinfo;
  $res = $db->Execute("SELECT COUNT(dig_id) AS n FROM $dbtables[dignitary] WHERE owner_id = $player_id AND ship_id = '0' AND planet_id = $planet_id");
  $on_planet = $res->fields['n'];
  $can_transfer = min(($max_dignitary_per_planet - $on_planet), $how_many);
  if($can_transfer < 0)
	$can_transfer = 0;
  $res = $db->Execute("SELECT dig_id FROM $dbtables[dignitary] WHERE owner_id = $player_id AND ship_id = $shipinfo[ship_id] LIMIT $can_transfer");
  $how_many2 = $res->RecordCount();
  
  if(!$how_many2)
	return 0;
  else  
  {
	while(!$res->EOF)
	{
	  $spy = $res->fields['dig_id'];
	  $debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET planet_id = '$planet_id', ship_id = '0', active = 'Y', job_id = '0', percent = '0.0' WHERE dig_id = $spy");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $res->MoveNext();
	}
	return $how_many2;
  }   
}


function transfer_to_ship_dig($player_id, $planet_id, $how_many = 1)
{
  global $db;
  global $dbtables;
  global $shipinfo;

  $res=$db->Execute("SELECT dig_id FROM $dbtables[dignitary] WHERE owner_id = $player_id AND planet_id = $planet_id LIMIT $how_many");// AND active = 'N'
  $how_many2 = $res->RecordCount();
  
  if(!$how_many2)
	return 0;
  else  
  {
	while(!$res->EOF)
	{
	  $spy = $res->fields['dig_id'];
	  $debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET planet_id = 0, ship_id = $shipinfo[ship_id], active = 'N', job_id = '0', percent = '0.0' WHERE dig_id = $spy");
	  db_op_result($debug_query,__LINE__,__FILE__);

	  $res->MoveNext();
	}
	return $how_many2;
  }   
}

function dig_ship_destroyed($ship_id, $attacker_player_id = 0)
{
  global $db;
  global $dbtables;
  global $shipinfo;
  
  $debug_query = $db->Execute("DELETE FROM $dbtables[dignitary] WHERE ship_id = $ship_id "); 
  db_op_result($debug_query,__LINE__,__FILE__);
}

function dig_buy_new_ship($old_ship_id, $new_ship_id)
{
	global $db;
	global $dbtables;

	$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET ship_id = $new_ship_id WHERE ship_id = $old_ship_id");
	db_op_result($debug_query,__LINE__,__FILE__);
}

?>
