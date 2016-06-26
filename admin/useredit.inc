<?
// Player and Ship Editor
			$fun_temp = 0;
			$option_echo = '';
			echo "<b>User editor</b>\n";
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
				echo "  &nbsp;\n  <input type=\"submit\" value=\"Edit\">\n";
			}
			else
			{
				if (empty($operation))
				{
					$res = $db->Execute("SELECT * from $dbtables[players] LEFT JOIN $dbtables[ships] " .
										"ON $dbtables[players].player_id = $dbtables[ships].player_id and  $dbtables[players].currentship =$dbtables[ships].ship_id " .
										"WHERE $dbtables[players].player_id=$user");
					$row = $res->fields;

					$res = $db->Execute("SELECT * from $dbtables[ship_types] WHERE type_id=$row[class]");
					$shiptypeinfo = $res->fields;

					$res2 = $db->Execute("SELECT * FROM $dbtables[ships] WHERE player_id=$user and ship_id=$row[currentship]");
					$row2 = $res2->fields;

					// Template stuff
					$smarty->assign("character_name", $row['character_name']);
					$smarty->assign("password", $row['password']);
					$smarty->assign("email", $row['email']);
					$smarty->assign("user", $user);
					$smarty->assign("shipname", $row2['name']);
					$smarty->assign("ship_class", $shiptypeinfo['type_id']);
					$smarty->assign("destroyed", $row2['destroyed']);
					$smarty->assign("hull", $row2['hull']);
					$smarty->assign("engines", $row2['engines']);
					$smarty->assign("power", $row2['power']);
					$smarty->assign("computer", $row2['computer']);
					$smarty->assign("sensors", $row2['sensors']);
					$smarty->assign("armour", $row2['armour']);
					$smarty->assign("shields", $row2['shields']);
					$smarty->assign("beams", $row2['beams']);
					$smarty->assign("torp_launchers", $row2['torp_launchers']);
					$smarty->assign("cloak", $row2['cloak']);
					$smarty->assign("ore", $row2['ore']);
					$smarty->assign("organics", $row2['organics']);
					$smarty->assign("goods", $row2['goods']);
					$smarty->assign("energy", $row2['energy']);
					$smarty->assign("colonists", $row2['colonists']);
					$smarty->assign("fighters", $row2['fighters']);
					$smarty->assign("torps", $row2['torps']);
					$smarty->assign("armour_pts", $row2['armour_pts']);
					$smarty->assign("dev_warpedit", $row2['dev_warpedit']);
					$smarty->assign("dev_genesis", $row2['dev_genesis']);
					$smarty->assign("dev_minedeflector", $row2['dev_minedeflector']);
					$smarty->assign("dev_emerwarp", $row2['dev_emerwarp']);
					$smarty->assign("dev_escapepod", $row2['dev_escapepod']);
					$smarty->assign("dev_fuelscoop", $row2['dev_fuelscoop']);
					$smarty->assign("credits", $row['credits']);
					$smarty->assign("turns", $row['turns']);
					$smarty->assign("turns_used", $row['turns_used']);
					$smarty->assign("sector_id", $row['sector_id']);
					$smarty->assign("currentship_id", $row['currentship']);
					$smarty->assign("dev_nova", $row2['dev_nova']);

					$smarty->assign("fed_bounty_count", $row['fed_bounty_count']);
					$smarty->assign("template", $row['template']);
					$smarty->assign("avatar", $row['avatar']);
					$smarty->assign("last_team", $row['last_team']);
					$smarty->assign("left_team_time", $row['left_team_time']);

					$res3 = $db->Execute("SELECT * FROM $dbtables[ibank_accounts] WHERE player_id=$user");
					$row3 = $res3->fields;

					$smarty->assign("igb_balance", $row3['balance']);
					$smarty->assign("igb_loan", $row3['loan']);
					$smarty->assign("igb_loantime", $row3['loantime']);

					$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE owner=$user");

					$stuff = array();

					while (!$res->EOF)
					{
						$row = $res->fields;

						$name = ($row['name']=="")?("UnNamed"):($row['name']);
						// $id[] = $row[planet_id];
						$sector = $row['sector_id'];
						$stuff[] = "'$name' in sector $sector";

						$res->MoveNext();
					}

					$smarty->assign("stuff", $stuff);

					$smarty->display("admin/useredit.tpl");
				}
				elseif ($operation == "save")
				{
					if ((!isset($dev_escapepod)) || ($dev_escapepod == ''))
					{
						$dev_escapepod = '';
					}
					
					if ((!isset($dev_fuelscoop)) || ($dev_fuelscoop == ''))
					{
						$dev_fuelscoop = '';
					}
					
					if ((!isset($turns_used)) || ($turns_used == ''))
					{
						$turns_used = '';
					}
					
					if ((!isset($ship_class)) || ($ship_class == ''))
					{
						$ship_class = '';
					}
					
					if ((!isset($igb_balance)) || ($igb_balance == ''))
					{
						$igb_balance = '';
					}
					
					if ((!isset($igb_loan)) || ($igb_loan == ''))
					{
						$igb_loan = '';
					}
					
					if ((!isset($igb_loantime)) || ($igb_loantime == ''))
					{
						$igb_loantime = '';
					}
					
					//intercept checkbox values
					$dev_escapepod = ($dev_escapepod == "on") ? "Y" : "N";
					$dev_fuelscoop = ($dev_fuelscoop == "on") ? "Y" : "N";
					$dev_nova = ($dev_nova == "on") ? "Y" : "N";

					$character_name = preg_replace ("/[^\w\d\s\.\'\@]/","",$character_name);
					$ship_name = preg_replace ("/[^\w\d\s\.\'\@]/","",$ship_name);

					$character_name = htmlspecialchars($character_name,ENT_QUOTES);
					$ship_name = htmlspecialchars($ship_name,ENT_QUOTES);

					if (!get_magic_quotes_gpc())
					{
						$character_name = addslashes($character_name);
						$ship_name = addslashes($ship_name);
					}

					$silent = 0;
					echo "Updating PLAYERS table "; 
					$query = "UPDATE $dbtables[players] SET last_login=last_login,character_name='$character_name',email='$email',credits='$credits',turns='$turns',turns_used='$turns_used',password='$password2',fed_bounty_count='$fed_bounty_count',template='$template',avatar='$avatar',last_team='$last_team',left_team_time='$left_team_time' WHERE player_id=$user";
					$debug_query = $db->Execute($query); 
					db_op_result($debug_query,__LINE__,__FILE__);
					$query = "SELECT rating FROM $dbtables[players] WHERE player_id=$user";
					$debug_query = $db->Execute($query); 
					db_op_result($debug_query,__LINE__,__FILE__);
					$rating = $debug_query->fields['rating'];

					echo "Updating SHIPS table "; 

					$query = "UPDATE $dbtables[ships] SET ";
					$query .= "class='$ship_class',name='$ship_name',destroyed='$destroyed',hull='$hull',engines='$engines',power='$power',computer='$computer',sensors='$sensors',beams='$beams',torp_launchers='$torp_launchers',torps='$torps',shields='$shields',armour='$armour',armour_pts='$armour_pts',cloak='$cloak',sector_id='$sector',ore='$ship_ore',organics='$ship_organics',goods='$ship_goods',energy='$ship_energy',colonists='$ship_colonists',fighters='$ship_fighters',dev_warpedit='$dev_warpedit',dev_genesis='$dev_genesis',dev_emerwarp='$dev_emerwarp',dev_escapepod='$dev_escapepod',dev_fuelscoop='$dev_fuelscoop',dev_minedeflector='$dev_minedeflector',dev_nova='$dev_nova'";
					$query .= " WHERE ship_id=$currentship_id"; 
					$debug_query = $db->Execute($query); 
					db_op_result($debug_query,__LINE__,__FILE__);

					$query = "UPDATE $dbtables[ships] SET destroyed='$destroyed' WHERE player_id=$user"; 
					$debug_query = $db->Execute($query); 
					db_op_result($debug_query,__LINE__,__FILE__);

					//store igb data in db
					echo "Updating IBANK_ACCOUNTS table "; 
					$query = "UPDATE $dbtables[ibank_accounts] SET balance='$igb_balance',loan='$igb_loan',loantime='$igb_loantime' WHERE player_id='$user'"; 
					$debug_query = $db->Execute($query); 
					db_op_result($debug_query,__LINE__,__FILE__);

					if($destroyed == "Y")
						db_kill_player($user, 0, 0);

					if($destroyed == "K")
						player_ship_destroyed($currentship_id, $user, $rating, 3, 0);

					echo "<br><INPUT TYPE=SUBMIT VALUE=\"Return to User editor\">";
					$silent = 0;
					$button_main = false;
				}
				else
				{
					echo "Invalid operation";
				}
			}
			echo "  <input type=\"hidden\" name=\"menu\" value=\"useredit\">\n";
			echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
			echo "</form>\n";
?>