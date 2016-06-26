<?
// Planet Editor

			cleanjs('name');
			echo $cleanjs;
			echo "<H3>Planet editor</H3>";
			echo "<FORM ACTION=admin.php METHOD=POST>";
			if (empty($planet))
			{
				echo "<SELECT SIZE=15 NAME=planet>";
				$res = $db->Execute("SELECT planet_id, name, sector_id, character_name FROM $dbtables[planets] LEFT JOIN $dbtables[players] ON $dbtables[planets].owner = $dbtables[players].player_id ORDER BY name, sector_id");
				while (!$res->EOF)
				{
					$row = $res->fields;
					if ($row['name'] == "")
					{
						$row['name'] = "Unnamed";
					}

					if ($row['character_name'] == NULL)
					{
						$row['character_name'] = "No one";
					}

					echo "<OPTION VALUE=$row[planet_id]> $row[name] in sector $row[sector_id], owned by $row[character_name]</OPTION>\n";
					$res->MoveNext();
				}

				echo "</SELECT>";
				echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
			}
			else
			{
				if (empty($operation))
				{
					$res = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$planet");
					$row = $res->fields;

					echo "<TABLE BORDER=1 CELLSPACING=2 CELLPADDING=2>";
					echo "<TR><TD><tt>		  Planet ID  </tt></TD><TD><FONT COLOR=#66FF00>$planet</FONT></TD>";
					echo "<TD ALIGN=Right><tt>  Sector ID  </tt><INPUT TYPE=TEXT SIZE=5 NAME=sector_id VALUE=\"$row[sector_id]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Defeated   </tt><INPUT TYPE=CHECKBOX NAME=defeated VALUE=ON " . CHECKED($row['defeated']) . "></TD></TR>";
					echo "<TR><TD><tt>		  Planet Name</tt></TD><TD><INPUT TYPE=TEXT SIZE=15 NAME=name VALUE=\"$row[name]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Base	   </tt><INPUT TYPE=CHECKBOX NAME=base VALUE=ON " . CHECKED($row['base']) . "></TD>";
					echo "<TR><TD COLSPAN=4>	<HR>	   </TD></TR>";
					echo "</TABLE>";
					echo "<TABLE BORDER=1 CELLSPACING=2 CELLPADDING=2>";
					echo "<TR><TD><tt>		  Planet Owner</tt></TD><TD>";
					echo "<SELECT SIZE=1 NAME=owner>";
					$ressuba = $db->Execute("SELECT player_id,character_name FROM $dbtables[players] ORDER BY character_name");
					echo "<OPTION VALUE=0>No One</OPTION>";
					while (!$ressuba->EOF)
					{
						$rowsuba = $ressuba->fields;
						if ($rowsuba[player_id] == $row[owner])
						{ 
							echo "<OPTION SELECTED=$rowsuba[player_id] VALUE=$rowsuba[player_id]>$rowsuba[character_name]</OPTION>";
						} 
						else 
						{  
							echo "<OPTION VALUE=$rowsuba[player_id]>$rowsuba[character_name]</OPTION>";
						}

						$ressuba->MoveNext();
					}

					echo "</SELECT></TD>";
					echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=organics VALUE=\"$row[organics]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Ore		</tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=ore VALUE=\"$row[ore]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Goods	  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=goods VALUE=\"$row[goods]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Energy	 </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=energy VALUE=\"$row[energy]\"></TD></TR>";
					echo "<TR><TD><tt>		  Planet Team</tt></TD><TD><INPUT TYPE=TEXT SIZE=5 NAME=team VALUE=\"$row[team]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Colonists  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=colonists VALUE=\"$row[colonists]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Credits	</tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=credits VALUE=\"$row[credits]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Fighters   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=fighters VALUE=\"$row[fighters]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Torpedoes  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=torps VALUE=\"$row[torps]\"></TD></TR>";
					echo "<TR><TD COLSPAN=2><tt>Planet Production</tt></TD>";
					echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_organics VALUE=\"$row[prod_organics]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Ore		</tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_ore VALUE=\"$row[prod_ore]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Goods	  </tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_goods VALUE=\"$row[prod_goods]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Energy	 </tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_energy VALUE=\"$row[prod_energy]\"></TD></TR>";
					echo "<TR><TD COLSPAN=6><tt>Planet Production</tt></TD>";
					echo "<TD ALIGN=Right><tt>  Fighters   </tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_fighters VALUE=\"$row[prod_fighters]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Torpedoes  </tt></TD><TD><INPUT TYPE=TEXT SIZE=3 NAME=prod_torp VALUE=\"$row[prod_torp]\"></TD></TR>";
					echo "<TR><TD COLSPAN=10>   <HR>	   </TD></TR>";
					echo "<TD ALIGN=Right><tt>  Computer   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=computer VALUE=\"$row[computer]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Sensors   </tt></TD> <TD><INPUT TYPE=TEXT SIZE=9 NAME=sensors VALUE=\"$row[sensors]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Beams   </tt></TD>   <TD><INPUT TYPE=TEXT SIZE=9 NAME=beams VALUE=\"$row[beams]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Torp Launchers   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=torp_launchers VALUE=\"$row[torp_launchers]\"></TD>";
					echo "<TR><TD COLSPAN=10>   <HR>	   </TD></TR>";
					echo "<TD ALIGN=Right><tt>  Shields   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=shields VALUE=\"$row[shields]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Armour   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=armour VALUE=\"$row[armour]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Armour Points   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=armour_pts VALUE=\"$row[armour_pts]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Cloak   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=cloak VALUE=\"$row[cloak]\"></TD>";
					echo "</TABLE>";
					echo "<BR>";
					echo "<INPUT TYPE=HIDDEN NAME=planet VALUE=$planet>";
					echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
					echo "<INPUT TYPE=SUBMIT SIZE=1 VALUE=Save ONCLICK=\"clean_js()\">";
				}
				elseif ($operation == "save")
				{
					// update database
					$_defeated = empty($defeated) ? "N" : "Y";
					$_base = empty($base) ? "N" : "Y";
					$debug_query = $db->Execute("UPDATE $dbtables[planets] SET sector_id='$sector_id',defeated='$_defeated',name='$name',base='$_base',owner='$owner',organics='$organics',ore='$ore',goods='$goods',energy='$energy',team='$team',colonists='$colonists',credits='$credits',computer='$computer',sensors='$sensors',beams='$beams',torp_launchers='$torp_launchers',shields='$shields',armour='$armour',armour_pts='$armour_pts',cloak='$cloak',fighters='$fighters',torps='$torps',prod_organics='$prod_organics',prod_ore='$prod_ore',prod_goods='$prod_goods',prod_energy='$prod_energy',prod_fighters='$prod_fighters',prod_torp='$prod_torp' WHERE planet_id=$planet");
					db_op_result($debug_query,__LINE__,__FILE__);
					echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Planet editor\">";
					$button_main = false;
				}
				else
				{
					echo "Invalid operation";
				}
			}
			echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=planetedit>";
			echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
			echo "</FORM>";
?>