<?
// IP Ban Editor

			echo "<B>IP Bans editor</B><p>";
			if (empty($command))
			{
				echo "<FORM ACTION=admin.php METHOD=POST>";
				echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
				echo "<INPUT TYPE=HIDDEN NAME=command VALUE=showips>";
				echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>";
				echo "<INPUT TYPE=SUBMIT VALUE=\"Show player's ips\">";
				echo "</form>";

				echo "<FORM ACTION=admin.php METHOD=POST>";
				echo "Enter IP Address: <input type=text name=ipaddress value='' size=32 maxlength=25>&nbsp;&nbsp;";
				echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
				echo "<INPUT TYPE=HIDDEN NAME=command VALUE=banipaddress>";
				echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>";
				echo "<INPUT TYPE=SUBMIT VALUE=\"Ban this IP\">";
				echo "</form>";

				echo "<FORM ACTION=admin.php METHOD=POST>";
				echo "Enter Email Address: <input type=text name=emailaddress value='' size=32 maxlength=150>&nbsp;&nbsp;";
				echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
				echo "<INPUT TYPE=HIDDEN NAME=command VALUE=banemail>";
				echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>";
				echo "<INPUT TYPE=SUBMIT VALUE=\"Ban this Email\">";
				echo "</form><br>";

				$res = $db->Execute("SELECT * FROM $dbtables[ip_bans]");
				while (!$res->EOF)
				{
					$ipaddress=$res->fields[ban_mask];
					if($ipaddress != ''){
						$bans[]=$ipaddress;
					}
					else
					{
						$bansemail[]=$res->fields[email];
					}
					$res->MoveNext();
				}

				if (empty($bans) and empty($bansemail))
				{
					echo "<b>No IP bans are currently active.</b>";
				}
				else
				{
					echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
						 "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
						 "Active IP Bans" .
						 "</font></b>" .
						 "</td></tr>" .
						 "<tr align=center bgcolor=$color_line2>" .
						 "<td><font size=2 color=white><b>Ban Mask</b></font></td>" .
						 "<td><font size=2 color=white><b>Affected Players</b></font></td>" .
						 "<td><font size=2 color=white><b>E-mail</b></font></td>" .
						 "<td><font size=2 color=white><b>Operations</b></font></td>" .
						 "</tr>";

					$curcolor = $color_line1;
		
					foreach ($bans as $ban)
					{
						echo "<tr bgcolor=$curcolor>";
						if ($curcolor == $color_line1)
						{
							$curcolor = $color_line2; 
						}
						else
						{
							$curcolor = $color_line1;
						}

						$printban = str_replace("%", "*", $ban);

						echo "<td align=center><font size=2 color=white>$printban</td>" .
							 "<td align=center><font size=2 color=white>";

						$res = $db->Execute("SELECT character_name, player_id, email FROM $dbtables[players] WHERE ip_address LIKE '$ban'");
						unset($players);
						while (!$res->EOF)
						{
							$players[] = $res->fields;
							$res->MoveNext();
						}
			
						if (empty($players))
						{
							echo "None";
						}
						else
						{
							foreach ($players as $player)
							{
								echo "<b>$player[character_name]</b><br>";
							}
						}

						echo "<td align=center><font size=2 color=white>";
						if (empty($players))
						{
							echo "N/A";
						}
						else
						{
							foreach ($players as $player)
							{
								echo "$player[email]<br>";
							}
						}

						echo "<td align=center nowrap valign=center><font size=2 color=white>" .
							 "<form action=admin.php method=POST>" .
							 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
							 "<INPUT TYPE=HIDDEN NAME=command VALUE=unbanip>" .
							 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
							 "<INPUT TYPE=HIDDEN NAME=ban VALUE=$ban>" .
							 "<INPUT TYPE=SUBMIT VALUE=Remove>" .
							 "</form>";
					}
					foreach ($bansemail as $ban)
					{
						echo "<tr bgcolor=$curcolor>";
						if ($curcolor == $color_line1)
						{
							$curcolor = $color_line2; 
						}
						else
						{
							$curcolor = $color_line1;
						}

						$printban = $ban;

						echo "<td align=center><font size=2 color=white>N/A</td>" .
							 "<td align=center><font size=2 color=white>";

						echo "None</td>";

						echo "<td align=center><font size=2 color=white>";
						echo "$printban</td>";

						echo "<td align=center nowrap valign=center><font size=2 color=white>" .
							 "<form action=admin.php method=POST>" .
							 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
							 "<INPUT TYPE=HIDDEN NAME=command VALUE=unbanemail>" .
							 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
							 "<INPUT TYPE=HIDDEN NAME=ban VALUE=$ban>" .
							 "<INPUT TYPE=SUBMIT VALUE=Remove>" .
							 "</form>";
					}
				echo "</table><p>";
				}
			}
			elseif ($command== 'showips')
			{
				$res = $db->Execute("SELECT DISTINCT ip_address FROM $dbtables[players] order by ip_address");
				while (!$res->EOF)
				{
					$ips[]=$res->fields['ip_address'];
					$res->MoveNext();
				}
				echo "<table border=1 cellspacing=1 cellpadding=2 width=100% align=center>" .
					 "<tr bgcolor=$color_line2><td align=center colspan=7><b><font color=white>" .
					 "Players sorted by IP address" .
					 "</font></b>" .
					 "</td></tr>" .
					 "<tr align=center bgcolor=$color_line2>" .
					 "<td><font size=2 color=white><b>IP address</b></font></td>" .
					 "<td><font size=2 color=white><b>Players</b></font></td>" .
					 "<td><font size=2 color=white><b>E-mail</b></font></td>" .
					 "<td><font size=2 color=white><b>Operations</b></font></td>" .
					 "</tr>";

				$curcolor = $color_line1;
		
				foreach ($ips as $ip)
				{
					echo "<tr bgcolor=$curcolor>";
					if ($curcolor == $color_line1)
					{
						$curcolor = $color_line2; 
					}
					else
					{
						$curcolor = $color_line1;
					}

					echo "<td align=center><font size=2 color=white><a href=http://www.geektools.com/cgi-bin/proxy.cgi?query=$ip&targetnic=auto target=_blank class=mnu>$ip</a></td>" .
						 "<td align=center><font size=2 color=white>";

					$res = $db->Execute("SELECT character_name, player_id, email FROM $dbtables[players] WHERE ip_address='$ip'");
					unset($players);
					while (!$res->EOF)
					{
						$players[] = $res->fields;
						$res->MoveNext();
					}

					foreach ($players as $player)
					{
						echo "<b>$player[character_name]</b><br>";
					}

					echo "<td align=center><font size=2 color=white>";
		
					foreach($players as $player)
					{
						echo "$player[email]<br>";
					}

					echo "<td align=center nowrap valign=center><font size=2 color=white>" .
						 "<form action=admin.php method=POST>" .
						 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
						 "<INPUT TYPE=HIDDEN NAME=command VALUE=banip>" .
						 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
						 "<INPUT TYPE=HIDDEN NAME=ip VALUE=$ip>" .
						 "<INPUT TYPE=SUBMIT VALUE=Ban>" .
						 "</form>" .
						 "<form action=admin.php method=POST>" .
						 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
						 "<INPUT TYPE=HIDDEN NAME=command VALUE=unbanip>" .
						 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
						 "<INPUT TYPE=HIDDEN NAME=ip VALUE=$ip>" .
						 "<INPUT TYPE=SUBMIT VALUE=Unban>" .
						 "</form>";
				}

				echo "</table><p>" .
					 "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command == 'banip')
			{
				$ip = $_POST[ip];
				echo "<b>Banning ip : $ip<p>";
				echo "<font size=2 color=white>Please select ban type :<p>";
				$ipparts = explode(".", $ip);

				echo "<table border=0>" .
					 "<tr><td align=right>" .
					 "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=HIDDEN NAME=command VALUE=banip2>" .
					 "<INPUT TYPE=HIDDEN NAME=ip VALUE=$ip>" .
					 "<input type=radio name=class value=I checked>" .
					 "<td><font size=2 color=white>IP only : $ip</td>" .
					 "<tr><td>" .
					 "<input type=radio name=class value=A>" .
					 "<td><font size=2 color=white>Class A : $ipparts[0].$ipparts[1].$ipparts[2].*</td>" .
					 "<tr><td>" .
					 "<input type=radio name=class value=B>" .
					 "<td><font size=2 color=white>Class B : $ipparts[0].$ipparts[1].*</td>" .
					 "<tr><td><td><br><input type=submit value=Ban>" .
					 "</table>" .
					 "</form>";

				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command == 'banip2')
			{
				$ip = $_POST[ip];
				$ipparts = explode(".", $ip);
		
				if ($class == 'A')
				{
					$banmask = "$ipparts[0].$ipparts[1].$ipparts[2].%";
				}
				elseif ($class == 'B')
				{
					$banmask = "$ipparts[0].$ipparts[1].%";
				}
				else
				{
					$banmask = $ip;
				}

				$printban = str_replace("%", "*", $banmask);
				echo "<font size=2 color=white><b>Successfully banned $printban</b>.<p>";
		
				$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] (ban_mask, email)VALUES('$banmask', '')");
				db_op_result($debug_query,__LINE__,__FILE__);

				$res = $db->Execute("SELECT DISTINCT $dbtables[players].character_name, $dbtables[players].email FROM $dbtables[players], $dbtables[ip_bans] WHERE $dbtables[players].ip_address LIKE '$banmask'");
				echo "Affected players :<p>";
				while (!$res->EOF)
				{
					echo " - " . $res->fields['character_name'] . "<br>";
					$emailban = $res->fields['email'];
					$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] (ban_mask, email)VALUES('', '$emailban')");
					db_op_result($debug_query,__LINE__,__FILE__);
					$res->MoveNext();
				}
			   
				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command == 'unbanip')
			{
				$ip = $_POST[ip];

				if (!empty($ban))
				{
					$res = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE ban_mask='$ban'");
				}
				else
				{
					$res = $db->Execute("SELECT * FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask");
				}

				$nbbans = $res->RecordCount();
				while (!$res->EOF)
				{
					$res->fields[print_mask] = str_replace("%", "*", $res->fields[ban_mask]);
					$bans[]=$res->fields;
					$res->MoveNext();
				}

				if (!empty($ban))
				{
					$db->Execute("DELETE FROM $dbtables[ip_bans] WHERE ban_mask='$ban'");
				}
				else
				{
					$db->Execute("DELETE FROM $dbtables[ip_bans] WHERE '$ip' LIKE ban_mask");
				}

				$query_string = "ip_address LIKE '" . $bans[0][ban_mask] ."'";
				for ($i = 1; $i < $nbbans ; $i++)
				{
					$query_string = $query_string . " OR ip_address LIKE '" . $bans[$i][ban_mask] . "'";
				}

				$res = $db->Execute("SELECT DISTINCT character_name FROM $dbtables[players] WHERE $query_string");
				$nbplayers = $res->RecordCount();

				while (!$res->EOF)
				{
					$players[]=$res->fields[character_name];
					$res->MoveNext();
				}

				echo "<font size=2 color=white><b>Successfully removed $nbbans bans</b> :<p>";
				foreach ($bans as $ban)
				{
					echo " - $ban[print_mask]<br>";
				}

				echo "<p><b>Affected players :</b><p>";
				if (empty($players))
				{
					echo " - None<br>";
				}
				else
				{
					foreach($players as $player)
					{
						echo " - $player<br>";
					}
				}
		
				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command== 'banipaddress')
			{
				echo "Banned IP Address: $ipaddress<br>";
				$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] VALUES('', '$ipaddress', '')");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command== 'banemail')
			{
				echo "Banned Email Address: $emailaddress<br>";
				$debug_query = $db->Execute("INSERT INTO $dbtables[ip_bans] VALUES('', '', '$emailaddress')");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
			elseif ($command== 'unbanemail')
			{
				echo "<font size=2 color=white><b>Successfully removed ban for $ban</b><p>";
				$debug_query = $db->Execute("DELETE FROM $dbtables[ip_bans] WHERE email='$ban'");
				db_op_result($debug_query,__LINE__,__FILE__);
				echo "<form action=admin.php method=POST>" .
					 "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>" .
					 "<INPUT TYPE=HIDDEN NAME=menu VALUE=ipedit>" .
					 "<INPUT TYPE=SUBMIT VALUE=\"Return to IP bans menu\">" .
					 "</form>";
			}
?>
