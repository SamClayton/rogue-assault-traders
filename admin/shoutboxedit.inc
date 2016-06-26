<?
// Shoutbox Editor

			if ($command=="DEL")
			{
				$xsql = "DELETE FROM $dbtables[shoutbox] WHERE sb_id = $sb_id";
				$result = $db->Execute($xsql);
			}

			if ($command=="ADD")
			{
				$xsql = "INSERT INTO $dbtables[shoutbox] (player_id, sb_text, player_name, sb_date) VALUES (1, '". rawurlencode(addslashes($sb_text)) . "', 'Webmaster'," . time() . ")";
				$debug_query = $db->Execute($xsql);
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			if ($command=="SAVE")
			{
				$xsql = "UPDATE $dbtables[shoutbox] SET sb_text='". rawurlencode(addslashes($sb_text)) . "' WHERE sb_id=$sb_id";
				$debug_query = $db->Execute($xsql);
				db_op_result($debug_query,__LINE__,__FILE__);
			}

			echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=3>";
			if ($command=="EDIT")
			{
				$result = $db->Execute("SELECT * FROM $dbtables[shoutbox] where sb_id=$sb_id");
				db_op_result($debug_query,__LINE__,__FILE__);
				$row = $result->fields;

				echo "<FORM ACTION=admin.php METHOD=POST>";
				echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
				echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=shoutboxedit>";
				echo "<TR nowrap><TD>Edit Shoutbox Message:</TD>";
				echo "<TD><TEXTAREA NAME=sb_text ROWS=10 COLS=60>". stripslashes(stripslashes(rawurldecode($row['sb_text']))) ."</TEXTAREA></TD>";
				echo "<INPUT TYPE=HIDDEN NAME=sb_id VALUE=$row[sb_id]>";
				echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"SAVE\"></TD></TR>";
				echo "</form>";
			}

			echo "<FORM ACTION=admin.php METHOD=POST>";
			echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
			echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=shoutboxedit>";
			echo "<TR nowrap><TD>Add Shoutbox Message:</TD>";
			echo "<TD><TEXTAREA NAME=sb_text ROWS=10 COLS=60></TEXTAREA></TD>";
			echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"ADD\"></TD></TR>";
			echo "</form>";
				$result = $db->Execute("SELECT * FROM $dbtables[shoutbox] WHERE sb_alli = 0 ORDER BY sb_date desc LIMIT 0,20");
				while (!$result->EOF && $result)
				{
					$row = $result->fields;
					echo "<FORM ACTION=admin.php METHOD=POST>";
					echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
					echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=shoutboxedit>";
					echo "<TR nowrap><TD>Delete or Edit:</TD>";
					echo "<TD width=450>". stripslashes(stripslashes(rawurldecode($row['sb_text']))) ."</TD>";
					echo "<TD width=150>". $row['player_name'] ."</TD>";
					echo "<INPUT TYPE=HIDDEN NAME=sb_id VALUE=$row[sb_id]>";
					echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"DEL\">&nbsp;&nbsp;&nbsp;<INPUT TYPE=SUBMIT NAME=command VALUE=\"EDIT\"></TD></TR>";
					echo "</FORM>";
					$result->MoveNext();
				}
			echo "</TABLE>";
?>