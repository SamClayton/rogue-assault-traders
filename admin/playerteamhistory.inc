<?
// Player Team History
			$option_echo = '';
			echo "<b>Player Team History</b>\n";
			echo "<br>\n";
			echo "<form action=\"admin.php\" method=\"post\">\n";
			if (empty($user))
			{
				$res = $db->Execute("SELECT player_id,character_name FROM $dbtables[players] ORDER BY character_name");
				while (!$res->EOF)
				{
					$row = $res->fields;
					$option_echo .= "	<option value=\"$row[player_id]\">$row[character_name]</option>\n";
					$res->MoveNext();
					$fun_temp++;
				}

				if ($fun_temp > 20)
				{
					$fun_temp = 20;
				}

				echo "  <select size=\"$fun_temp\" name=\"user\">\n";
				echo $option_echo;
				echo "  </select>\n";
				echo "  &nbsp;\n  <input type=\"submit\" value=\"View\">\n";
			}
			else
			{
				$res = $db->Execute("SELECT * from $dbtables[players], $dbtables[player_team_history] LEFT JOIN $dbtables[ships] " .
									"ON $dbtables[players].player_id = $dbtables[ships].player_id and  $dbtables[players].currentship =$dbtables[ships].ship_id " .
									"WHERE $dbtables[players].player_id=$user and $dbtables[player_team_history].player_id=$user order by $dbtables[player_team_history].left_team ASC");
				if (!$res->EOF){
					$row = $res->fields;
					echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"4\"><tr><td colspan=4 align=\"center\"><font color=#00ff00 size=3><b>$row[character_name]</b></font></td></tr>";
					echo "<tr><td align=\"center\"><font color=cyan><b>Team ID</b></font></td><td align=\"center\"><b><font color=cyan>Team Name</b></font></td><td align=\"center\"><b><font color=cyan>Info</b></font></td><td align=\"center\"><b><font color=cyan>Date</b></font></td></tr>\n";
				}
				else
				{
					echo "<table border=\"1\" cellspacing=\"1\" cellpadding=\"4\"><tr><td colspan=4><font color=#00ff00 size=3>Player hasn't joined/created any teams.</font></td></tr>";
				}
				while (!$res->EOF)
				{
					$row = $res->fields;
					echo "<tr><td>$row[history_team_id]</td><td>$row[history_team_name]</td><td>$row[info]</td><td>" . date($local_date_full_format, strtotime($row['left_team'])) . "</td></tr>\n";
					$res->MoveNext();
				}
				echo "</table>";
				echo "<input type=\"submit\" value=\"Return to History List\">\n";
			}
			echo "  <input type=\"hidden\" name=\"menu\" value=\"playerteamhistory\">\n";
			echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
			echo "</form>\n";
?>