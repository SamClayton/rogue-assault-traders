<?
// Configuration Editor

if($command == "save"){
	echo "<table><tr><td>";
	for($i = 0; $i < $count; $i++){
		echo "Updating config variable: <b><i>$name[$i]</i></b> = <b>$value[$i]&nbsp;</b><br>";
		$debug_query = $db->Execute("UPDATE $dbtables[config_values] SET value='$value[$i]' WHERE name='$name[$i]'");
		db_op_result($debug_query,__LINE__,__FILE__);
		$debug_query = $db->Execute("UPDATE $dbtables[scheduler] SET ticks_full='$value[$i]' WHERE sched_file='$name[$i].php'");
	}

	echo "<br><br><b>Variable Update Complete - <i>$game_name</i></b><br><br></td><tr></table>";
	unset($command);
}

if(empty($section) and $command != "save"){
	echo "Select the configuration group you would like to edit:<BR><BR>\n";
	echo "<form action=\"admin.php\" method=\"post\">\n";
	echo "  <select name=\"section\">\n";
	$debug_query = $db->Execute("SELECT distinct section FROM $dbtables[config_values]");
	db_op_result($debug_query,__LINE__,__FILE__);
	while (!$debug_query->EOF && $debug_query)
	{
		$row = $debug_query->fields;
		echo "	<option value=\"$row[section]\">$row[section]</option>\n";
		$debug_query->MoveNext();
	}
	echo "  </select>\n";
	echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
	echo "  <input type=\"hidden\" name=\"menu\" value=\"settingsedit\">\n";
	echo "  &nbsp;<input type=\"submit\" value=\"Select\">\n";
	echo "</form><br><br><hr><br>\n";
}else{
	if($command != "save"){
		// Get the config_values from the DB - silently.
		$silent = 1;
		$debug_query = $db->Execute("SELECT * FROM $dbtables[config_values] where section='$section'");
		db_op_result($debug_query,__LINE__,__FILE__);

		$count = 0;
		while (!$debug_query->EOF && $debug_query)
		{
			$row = $debug_query->fields;
			$db_config_name[$count] = $row['name'];
			$db_config_value[$count] = $row['value'];
			$db_config_info[$count] = $row['description'];
			$count++;
			$debug_query->MoveNext();
		}
		$smarty->assign('swordfish', "$_POST[swordfish]");
		$smarty->assign('returnlink', "admin.php");
		$smarty->assign('menu', $_POST['menu']);
		$smarty->assign('count', $count);
		$smarty->assign('db_config_name', $db_config_name);
		$smarty->assign('db_config_value', $db_config_value);
		$smarty->assign('db_config_info', $db_config_info);
		$smarty->display("admin/setedit.tpl");
	}
}
?>
