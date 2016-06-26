<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_news.php

if (preg_match("/sched_news.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

if (!function_exists('insert_news')) {
	function insert_news($data, $user_id, $news_type)
	{
		global $db, $dbtables;
		$total = 1;

		$result2 = $db->Execute("SELECT * from $dbtables[news] order by news_id DESC");
		$newsinfo = $result2->fields;
//print "$newsinfo[data] - $data<br>$newsinfo[news_type]<br>$newsinfo[user_id]<br>";
		if($newsinfo['data'] == $data and $newsinfo['news_type'] == $news_type and $newsinfo['user_id'] == $user_id){
			$total = $newsinfo['total'] + 1;
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("UPDATE $dbtables[news] set total='$total', date='$stamp' where news_id=$newsinfo[news_id]");
			db_op_result($debug_query,__LINE__,__FILE__);
		}else{
			$stamp = date("Y-m-d H:i:s");
			$debug_query = $db->Execute("INSERT INTO $dbtables[news] (data, total, user_id, date, news_type) VALUES ('$data', '$total', '$user_id', '$stamp', '$news_type')");
			db_op_result($debug_query,__LINE__,__FILE__);
		}
	}
}

if (!function_exists('get_player')) {
	function get_player($player_id)
	{
		global $db, $dbtables;
		$res = $db->Execute("SELECT character_name from $dbtables[players] where player_id = $player_id");
		db_op_result($res,__LINE__,__FILE__);
		if ($res)
		{
			$row = $res->fields;
			$character_name = $row['character_name'];
			return $character_name;
		}
		else
		{
			return "Unknown";
		}
	}
}

global $default_lang;
TextFlush ( "<b>POSTING NEWS</b><br>\n");

// generation of planet amount
$sql = $db->Execute("SELECT count(owner) as amount, owner from $dbtables[planets] where owner !='0' group by owner order by amount ASC");
db_op_result($sql,__LINE__,__FILE__);

if($sql){
	while (!$sql->EOF)
	{
		$row = $sql->fields;
		if ($row['amount'] >= 50) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='planet50'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "planet50");
			}
		}
		elseif ($row['amount'] >= 25) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='planet25'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "planet25");
			}
		}
		elseif ($row['amount'] >= 10) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='planet10'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "planet10");
			}
		}
		elseif ($row['amount'] >= 5) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='planet5'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "planet5");
			}
		}
		$sql->MoveNext();
	} // while
}
// end generation of planet amount

// generation of colonist amount
$sql = $db->Execute("select sum(colonists) as amount, owner from $dbtables[planets] where owner !='0' group by owner order by amount ASC");

if($sql){
	while (!$sql->EOF)
	{
		$row = $sql->fields;
		if ($row['amount'] >= 1000000000) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='col1000'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "col1000");
			}
		}
		elseif ($row['amount'] >= 500000000) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='col500'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "col500");
			}
		}
		elseif ($row['amount'] >= 100000000) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='col100'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "col100");
			}
		}
		elseif ($row['amount'] >= 25000000) 
		{
			$sql2 = $db->Execute("select news_type from $dbtables[news] where user_id='$row[owner]' and news_type='col25'");
			if ($sql2->EOF) 
			{
				$name = get_player($row['owner']);
				insert_news($name, $row['owner'], "col25");
			}
		}
		$sql->MoveNext();
	} // while
}
// end generation of colonist amount

$multiplier = 0; //no use to run this more than once per tick
TextFlush ( "<br>\n");
?>
