<?
// Wordcensor Editor

if ($command=="DUMP")
{
	$filename = $gameroot . "config/wordcensor.ini";
	$file = fopen($filename,"w") or die ("Failed opening file: enable write permissions for '$filename' and then refresh the screen.");

	$debug_query = $db->Execute("SELECT * FROM $dbtables[wordcensor]");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF && $debug_query)
	{
		$row = $debug_query->fields;
		$db_config_name = $row['name'];
		$db_config_value = $row['value'];
		echo "Writing data: " . $db_config_name . "=\"" . $db_config_value . "\";<br>"; 
		fwrite($file,$db_config_name . "=\"" . $db_config_value . "\";\n"); 
		$debug_query->MoveNext();
	}

	fclose($file);

	echo "Success.";
}

if ($command=="DEL")
{
	$xsql = "DELETE FROM $dbtables[wordcensor] WHERE id = $id";
	$result = $db->Execute($xsql);
}

if ($command=="ADD")
{
	$name = strtolower($name);
	$value = strtolower($value);
	$xsql = "INSERT INTO $dbtables[wordcensor] (name, value) VALUES ('$name', '$value')";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

if ($command=="SAVE")
{
	$name = strtolower($name);
	$value = strtolower($value);
	$xsql = "UPDATE $dbtables[wordcensor] SET name='$name', value='$value' WHERE id=$id";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3>";
if ($command=="EDIT")
{
	$result = $db->Execute("SELECT * FROM $dbtables[wordcensor] where id=$id");
	db_op_result($debug_query,__LINE__,__FILE__);
	$row = $result->fields;

	echo "<FORM ACTION=admin.php METHOD=POST>";
	echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
	echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=wordcensoredit>";
	echo "<TR nowrap><TD>Edit word:</TD>";
	echo "<TD>Convert: <INPUT TYPE=TEXT NAME=name VALUE=\"$row[name]\"></TD>";
	echo "<TD>&nbsp;&nbsp;To: <INPUT TYPE=TEXT NAME=value VALUE=\"$row[value]\"></TD>";
	echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$row[id]>";
	echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"SAVE\"></TD></TR>";
	echo "</form>";
	echo "<tr><td colspan=4><br><hr><br></td></tr>";
}

echo "<FORM ACTION=admin.php METHOD=POST>";
echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=wordcensoredit>";
echo "<TR nowrap><TD>Add Word to Censor:</TD>";
echo "<TD>&nbsp;&nbsp;Convert: <INPUT TYPE=TEXT NAME=name VALUE=\"\"></TD>";
echo "<TD>&nbsp;&nbsp;To: <INPUT TYPE=TEXT NAME=value VALUE=\"\"></TD>";
echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"ADD\"></TD></TR>";
echo "</form>";
echo "<tr><td colspan=4><br><hr><br></td></tr>";
$result = $db->Execute("SELECT * FROM $dbtables[wordcensor] ORDER BY name");
while (!$result->EOF && $result)
{
	$row = $result->fields;
	echo "<FORM ACTION=admin.php METHOD=POST>";
	echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
	echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=wordcensoredit>";
	echo "<TR nowrap><TD>Delete or Edit:</TD>";
	echo "<TD>&nbsp;&nbsp;Convert: ". $row['name'] ."</TD>";
	echo "<TD>&nbsp;&nbsp;To: ". $row['value'] ."</TD>";
	echo "<INPUT TYPE=HIDDEN NAME=id VALUE=$row[id]>";
	echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"DEL\">&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE=\"EDIT\"></TD></TR>";
	echo "</FORM>";
	$result->MoveNext();
}

if($result)
	echo "<tr><td colspan=4><br><hr><br></td></tr>";

echo "<FORM ACTION=admin.php METHOD=POST>";
echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=wordcensoredit>";
echo "<TR><TD colspan=2>Dump the Wordcensor database to wordcensor.ini.<br>This will allow you to keep your updated bad word list when you reset the server.</TD>";
echo "<TD colspan=2 valign=middle align=center><INPUT TYPE=SUBMIT NAME=command VALUE=\"DUMP\"></TD></TR>";
echo "</form>";
echo "</TABLE>";
?>