<?
// Sector Editor

			echo "<H2>Sector editor</H2>";
			echo "<FORM ACTION=admin.php METHOD=POST>";
			if (empty($sector))
			{
				echo "<SELECT SIZE=20 NAME=sector>";
				$res = $db->Execute("SELECT sector_id FROM $dbtables[universe] ORDER BY sector_id");
				while (!$res->EOF)
				{
					$row = $res->fields;
					if ($row[sector_id] > 2)
					{
						echo "<OPTION VALUE=$row[sector_id]> $row[sector_id] </OPTION>";
					}
					$res->MoveNext();
				}
				echo "</SELECT>";
				echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
			}
			else
			{
				if (empty($operation))
				{
					$res = $db->Execute("SELECT * FROM $dbtables[universe] WHERE sector_id=$sector");
					$row = $res->fields;
					echo "<TABLE BORDER=1 CELLSPACING=2 CELLPADDING=2>";
					echo "<TR><TD><tt>		  Sector ID  </tt></TD><TD><FONT COLOR=#66FF00>$sector</FONT></TD>";
					echo "<TD ALIGN=Right><tt>  Sector Name</tt></TD><TD><INPUT TYPE=TEXT SIZE=15 NAME=sector_name VALUE=\"$row[sector_name]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Zone ID	</tt></TD><TD>";
					echo "<SELECT SIZE=1 NAME=zone_id>";
					$ressubb = $db->Execute("SELECT zone_id,zone_name FROM $dbtables[zones] ORDER BY zone_name");
					while (!$ressubb->EOF)
					{
						$rowsubb = $ressubb->fields;
						if ($rowsubb[zone_id] == $row[zone_id])
						{ 
							echo "<OPTION SELECTED=$rowsubb[zone_id] VALUE=$rowsubb[zone_id]>$rowsubb[zone_name]</OPTION>";
						} 
						else 
						{ 
							echo "<OPTION VALUE=$rowsubb[zone_id]>$rowsubb[zone_name]</OPTION>";
						}

						$ressubb->MoveNext();
					}

					echo "</SELECT></TD></TR>";
					echo "<TR><TD><tt>		  Beacon	 </tt></TD><TD COLSPAN=5><INPUT TYPE=TEXT SIZE=70 NAME=beacon VALUE=\"$row[beacon]\"></TD></TR>";
					echo "<TR><TD><tt>		  X   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=x VALUE=\"$row[x]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Y	 </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=y VALUE=\"$row[y]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Z	 </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=z VALUE=\"$row[z]\"></TD></TR>";
					echo "<TR><TD COLSPAN=6>	<HR>	   </TD></TR>";
					echo "</TABLE>";

					echo "<TABLE BORDER=5 CELLSPACING=2 CELLPADDING=2>";
					echo "<TR><TD><tt>		  Port Type  </tt></TD><TD>";
					echo "<SELECT SIZE=1 NAME=port_type>";
					$oportnon = $oportorg = $oportore = $oportgoo = $oportene = "VALUE"; 

					if ($row[port_type] == "none") $oportnon = "SELECTED=none VALUE";
					if ($row[port_type] == "organics") $oportorg = "SELECTED=organics VALUE";
					if ($row[port_type] == "ore") $oportore = "SELECTED=ore VALUE";
					if ($row[port_type] == "goods") $oportgoo = "SELECTED=goods VALUE";
					if ($row[port_type] == "energy") $oportene = "SELECTED=energy VALUE";

					echo "<OPTION $oportnon=none>none</OPTION>";
					echo "<OPTION $oportorg=organics>organics</OPTION>";
					echo "<OPTION $oportore=ore>ore</OPTION>";
					echo "<OPTION $oportgoo=goods>goods</OPTION>";
					echo "<OPTION $oportene=energy>energy</OPTION>";
					echo "</SELECT></TD>";
					echo "<TD ALIGN=Right><tt>  Organics   </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_organics VALUE=\"$row[port_organics]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Ore		</tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_ore VALUE=\"$row[port_ore]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Goods	  </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_goods VALUE=\"$row[port_goods]\"></TD>";
					echo "<TD ALIGN=Right><tt>  Energy	 </tt></TD><TD><INPUT TYPE=TEXT SIZE=9 NAME=port_energy VALUE=\"$row[port_energy]\"></TD></TR>";
					echo "<TR><TD COLSPAN=10>   <HR>	   </TD></TR>";
					echo "</TABLE>";
					echo "<BR>";
					echo "<INPUT TYPE=HIDDEN NAME=sector VALUE=$sector>";
					echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=save>";
					echo "<INPUT TYPE=SUBMIT SIZE=1 VALUE=Save>";
				}
				elseif ($operation == "save")
				{
					// update database
					$debug_query = $db->Execute("UPDATE $dbtables[universe] SET sector_name='$sector_name',zone_id='$zone_id',beacon='$beacon',port_type='$port_type',port_organics='$port_organics',port_ore='$port_ore',port_goods='$port_goods',port_energy='$port_energy',z='$z',x='$x',y='$y' WHERE sector_id=$sector");
					db_op_result($debug_query,__LINE__,__FILE__);
					echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Sector editor\">";
					$button_main = false;
				}
				else
				{
					echo "Invalid operation";
				}
			}
			echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=sectoredit>";
			echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
			echo "</FORM>";
?>
