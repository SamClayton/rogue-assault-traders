<?
// Zone Editor
			echo "<B>Zone editor</B>";
			echo "<BR>";
			echo "<FORM ACTION=admin.php METHOD=POST>";
			if (empty($zone))
			{
				echo "<SELECT SIZE=20 NAME=zone>";
				$res = $db->Execute("SELECT zone_id,zone_name FROM $dbtables[zones] ORDER BY zone_name");
				while (!$res->EOF)
				{
					$row = $res->fields;
					echo "<OPTION VALUE=$row[zone_id]>$row[zone_name]</OPTION>";
					$res->MoveNext();
				}
	
				echo "</SELECT>";
				echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=editzone>";
				echo "&nbsp;<INPUT TYPE=SUBMIT VALUE=Edit>";
			}
			else
			{
				if ($operation == "editzone")
				{
					$res = $db->Execute("SELECT * FROM $dbtables[zones] WHERE zone_id=$zone");
					$row = $res->fields;
					echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=5>";
					echo "<TR><TD>Zone ID</TD><TD>$row[zone_id]</TD></TR>";
					echo "<TR><TD>Zone Name</TD><TD><INPUT TYPE=TEXT NAME=zone_name VALUE=\"$row[zone_name]\"></TD></TR>";
					echo "<TR><TD>Allow Attack</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_attack VALUE=ON " . CHECKED($row['allow_attack']) . "></TD>";
					echo "<TR><TD>Allow WarpEdit</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_warpedit VALUE=ON " . CHECKED($row['allow_warpedit']) . "></TD>";
					echo "<TR><TD>Allow Planet</TD><TD><INPUT TYPE=CHECKBOX NAME=zone_planet VALUE=ON " . CHECKED($row['allow_planet']) . "></TD>";
					echo "</TABLE>";
					echo "<TR><TD>Max Hull</TD><TD><INPUT TYPE=TEXT NAME=zone_hull VALUE=\"$row[max_hull]\"></TD></TR>";
					echo "<BR>";
					echo "<INPUT TYPE=HIDDEN NAME=zone VALUE=$zone>";
					echo "<INPUT TYPE=HIDDEN NAME=operation VALUE=savezone>";
					echo "<INPUT TYPE=SUBMIT VALUE=Save>";
				}
				elseif ($operation == "savezone")
				{
					// update database
					$_zone_attack = empty($zone_attack) ? "N" : "Y";
					$_zone_warpedit = empty($zone_warpedit) ? "N" : "Y";
					$_zone_planet = empty($zone_planet) ? "N" : "Y";
					$debug_query = $db->Execute("UPDATE $dbtables[zones] SET zone_name='$zone_name',allow_attack='$_zone_attack' ,allow_warpedit='$_zone_warpedit' ,allow_planet='$_zone_planet', max_hull='$zone_hull' WHERE zone_id=$zone");
					db_op_result($debug_query,__LINE__,__FILE__);
					echo "<INPUT TYPE=SUBMIT VALUE=\"Return to Zone Editor \">";
					$button_main = false;
				}
				else
				{
					echo "Invalid operation";
				}
			}
			echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=zoneedit>";
			echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
			echo "</FORM>";
?>
