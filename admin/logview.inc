<?
// Admin and Player Log Viewer

			echo "<form action=log.php method=POST>" .
				 "<INPUT TYPE=HIDDEN NAME=md5swordfish VALUE=".md5($_POST['swordfish']).">" .
				 "<INPUT TYPE=HIDDEN NAME=player VALUE=0>" .
				 "<INPUT TYPE=SUBMIT VALUE=\"View admin log\">" .
				 "</form>" .
				 "<form action=log.php method=POST>" .
				 "<INPUT TYPE=HIDDEN NAME=md5swordfish VALUE=".md5($_POST['swordfish']).">" .
				 "<SELECT name=player>";

			$res = $db->execute("SELECT player_id, character_name FROM $dbtables[players] ORDER BY character_name ASC");
			while (!$res->EOF)
			{
				$players[] = $res->fields;
				$res->MoveNext();
			}

			foreach ($players as $player)
			{
				echo "<OPTION value=$player[player_id]>$player[character_name]</OPTION>";
			}

			echo "</SELECT>&nbsp;&nbsp;" .
				 "<INPUT TYPE=SUBMIT VALUE=\"View player log\">" .
				 "</form><HR size=1 width=80%>";
?>
