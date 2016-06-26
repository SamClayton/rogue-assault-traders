<?php
// Save Configuration

if ($command=="SAVE")
{
	$filename = $gameroot . "config/setup_" . $setup_file . ".ini";
	$file = fopen($filename,"w") or die ("Failed opening file: enable write permissions for '$filename'");
	echo "<b>Saving setup_" . $setup_file . ".ini</b><br><br>";
	
	$debug_query = $db->Execute("SELECT * FROM $dbtables[config_values]");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF && $debug_query)
	{
		$row = $debug_query->fields;
		$db_config_name = $row['name'];
		$db_config_value = $row['value'];
		$db_config_info = $row['description'];
		if($db_config_section != trim($row['section']) and trim($row['section']) != ""){
			$db_config_section = trim($row['section']);
			if($db_config_section != "totalfedsectors" && $db_config_section != "Reset Date"){
				echo "Writing data: [" . $db_config_section . "]<br>"; 
				fwrite($file,"\n[". $db_config_section . "]\n"); 
			}
		}
		if($db_config_name != "totalfedsectors" && $db_config_name != "scheduled_reset" && $db_config_name != "reset_date")
		{
			echo "Writing data: " . $db_config_name . "=\"" . $db_config_value . "\"; ;" . $db_config_info . "<br>"; 
			fwrite($file,$db_config_name . "=\"" . $db_config_value . "\"; ;" . $db_config_info . "\n"); 
		}
		$debug_query->MoveNext();
	}

	fclose($file);

	echo "Success.";
}
elseif ($command=="OVERWRITE")
{
	$filename = $gameroot . "config/$setup_file";
	$file = fopen($filename,"w") or die ("Failed opening file: enable write permissions for '$filename'");
	echo "<b>Overwriting " . $setup_file . "</b><br><br>";

	$debug_query = $db->Execute("SELECT * FROM $dbtables[config_values]");
	db_op_result($debug_query,__LINE__,__FILE__);

	while (!$debug_query->EOF && $debug_query)
	{
		$row = $debug_query->fields;
		$db_config_name = $row['name'];
		$db_config_value = $row['value'];
		$db_config_info = $row['description'];
		if($db_config_section != trim($row['section']) and trim($row['section']) != ""){
			$db_config_section = trim($row['section']);
			if($db_config_section != "totalfedsectors" && $db_config_section != "Reset Date"){
				echo "Writing data: [" . $db_config_section . "]<br>"; 
				fwrite($file,"\n[". $db_config_section . "]\n"); 
			}
		}
		if($db_config_name != "totalfedsectors" && $db_config_name != "scheduled_reset" && $db_config_name != "reset_date")
		{
			echo "Writing data: " . $db_config_name . "=\"" . $db_config_value . "\"; ;" . $db_config_info . "<br>"; 
			fwrite($file,$db_config_name . "=\"" . $db_config_value . "\"; ;" . $db_config_info . "\n"); 
		}
		$debug_query->MoveNext();
	}

	fclose($file);

	echo "Success.";
}
else
{
?>
	<FORM ACTION=admin.php METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=swordfish VALUE=<?=$_POST['swordfish'];?>>
	<INPUT TYPE=HIDDEN NAME=menu VALUE=saveconfig>
	<table>
	<TR><TD>Enter new setup filename&nbsp;<input type="text" name="setup_file" size="15" maxlength="15">&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE="SAVE"></TD></TR>
	</table></form>

	<FORM ACTION=admin.php METHOD=POST>
	<INPUT TYPE=HIDDEN NAME=swordfish VALUE=<?=$_POST['swordfish'];?>>
	<INPUT TYPE=HIDDEN NAME=menu VALUE=saveconfig>
<table>
		<tr><td>Overwrite Setup File: <select name="setup_file">
<?
	$filelist = get_dirlist($gameroot."config/");
	$newcommands = 0;
	for ($c=0; $c<count($filelist); $c++) { 
		$filenameroot =  str_replace(".ini", "", $filelist[$c]); 
		if(strstr($filelist[$c], "setup_")){
		?>
		<option value="<?=$filelist[$c];?>"><?=str_replace("setup_", "", $filenameroot);?>
		<?
		}
	}?>
		</select>&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE="OVERWRITE">
	</td></tr></table></form>
<?
}
?>
