<?
// Global Email Message to all Players

			if (empty($content)) 
			{ 
				$selfpath = basename($_SERVER['PHP_SELF']); 
				echo "<div align=\"left\">\n"; 
				echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n"; 
				echo "	<tr>\n"; 
				echo "	  <td width=\"50%\" nowrap><b>Global Email Message</b></td>\n"; 
				echo "	</tr>\n"; 
				echo "  </table>\n"; 
				echo "</div>\n"; 
				echo "<FORM ACTION=admin.php METHOD=POST>"; 
				echo "<input type=\"radio\" name=\"messagetype\" value=\"email\" checked>Email &nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"messagetype\" value=\"message\">Internal Message<BR><BR>"; 
				echo "<table>"; 
				echo "  <tr>"; 
				echo "	<td><font size=\"2\">TO:</font></td>"; 
				echo "	<td><input disabled maxLength=\"40\" size=\"40\" value=\"All Players\" name=\"dummy\"></td>"; 
				echo "  </tr>"; 
				echo "  <tr>"; 
				echo "	<td><font size=\"2\">FROM:</font></td>"; 
				echo "	<td><input disabled maxLength=\"40\" size=\"40\" value=\"GameAdmin\" name=\"dummy\"></td>"; 
				echo "  </tr>"; 
				echo "  <tr>"; 
				echo "	<td><font size=\"2\">SUBJECT:</font></td>"; 
				echo "	<td><input maxLength=\"40\" size=\"40\" name=\"subject\"></td>"; 
				echo "  </tr>"; 
				echo "  <tr>"; 
				echo "	<td valign=\"top\"><font size=\"2\">MESSAGE:</font></td>"; 
				echo "	<td><textarea name=\"content\" rows=\"5\" cols=\"40\"></textarea></td>"; 
				echo "  </tr>"; 
				echo "  <tr>"; 
				echo "	<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Submit\"><input type=\"reset\" value=\"Reset\"></td>"; 
				echo "  </tr>"; 
				echo "</table>"; 
				echo "<INPUT TYPE=HIDDEN NAME=\"swordfish\" VALUE=\"$_POST[swordfish]\">"; 
				echo "<INPUT TYPE=HIDDEN NAME=\"menu\" VALUE=\"globalmessage\">"; 
				echo "</FORM>";	 
			}
			else 
			{ 
				$res = $db->Execute("select * from $dbtables[players] LEFT JOIN $dbtables[ships] " .
									"ON $dbtables[players].player_id = $dbtables[ships].player_id " .
									"WHERE email != '$admin_mail' AND email NOT LIKE '%@npc' ORDER BY character_name ASC"); 
				$row = $res->fields; 
	
				if ($messagetype=='email')
				{
					$headers = "From: GameAdmin <$admin_mail>\r\n"; 
					$content .= "\r\n\r\nhttp://$SERVER_NAME$gamepath\r\n"; 
					$content = ereg_replace("\r\n.\r\n","\r\n. \r\n",$content);
				}
				elseif ($messagetype=='message')
				{
					$timestamp = date("Y-m-d H:i:s");
					$r2 = $db->Execute("SELECT player_id FROM $dbtables[players]  WHERE email = '$admin_mail'");
					$admin_id = $r2->fields["player_id"];
				}

				// New lines to prevent SQL injection. Bad stuff.
				$content = htmlspecialchars($content);
				$subject = htmlspecialchars($subject);

				while (!$res->EOF) 
				{
					if ($messagetype=='email')
					{ 
						$e_response = mail($res->fields["email"],$subject,$content,$headers); 
						if ($e_response===TRUE) 
						{ 
							echo "<font color=\"lime\">Global Message sent to ".$res->fields["email"]."</font> - \n"; 
							AddELog($res->fields["email"],5,'Y',$subject,$e_response); 
						} 
						else 
						{ 
							echo "<font color=\"Red\">Message failed to send to ".$res->fields["email"]."</font> - \n"; 
							AddELog($res->fields["email"],5,'N',$subject,$e_response); 
						} 
					}
					elseif ($messagetype=='message')
					{
						$temp = $silent;
						$silent = 0;
						echo "Sending Global Message to <B>". $res->fields["character_name"] . "</B> ";
						$debug_query = $db->Execute("INSERT INTO $dbtables[messages] (sender_id, recp_id, subject, sent, message) VALUES ('$admin_id', '" . $res->fields["player_id"] . "', '$subject', '$timestamp','$content')");
						db_op_result($debug_query,__LINE__,__FILE__);
						$silent = $temp;
					}
				$res->MoveNext(); 
				} 
			echo "<br><font color=\"lime\">Messages sent</font><BR>\n"; 
			} 
?>
