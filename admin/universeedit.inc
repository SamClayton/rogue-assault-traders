<?
// Universe Lightyear Size Editor
			echo "<B>Universe editor</B>";
			$title = "Expand/Contract the Universe";
			echo "<BR>Expand or Contract the Universe <BR>";
			if (empty($action))
			{
				echo "<FORM ACTION=admin.php METHOD=POST>";
				echo "Universe Size: <INPUT TYPE=TEXT NAME=radius VALUE=\"$universe_size\">";
				echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
				echo "<INPUT TYPE=HIDDEN NAME=menu VALUE=universeedit>";
				echo "<INPUT TYPE=HIDDEN NAME=action VALUE=doexpand> ";
				echo "<INPUT TYPE=SUBMIT VALUE=\"Play God\">";
				echo "</FORM>";
			}
			elseif ($action == "doexpand")
			{
		   //	 echo "<BR><FONT SIZE='+2'>Be sure to update your config.php file with the new universe_size value</FONT><BR>";
				
				$radius = max( 1, abs(round($radius)) );
				$diff = $radius / $universe_size;
				
				$debug_query = $db->Execute("UPDATE $dbtables[universe] SET x=ROUND(x*$diff), y=ROUND(y*$diff), z=ROUND(z*$diff) WHERE zone_id <> 2 ");
				db_op_result($debug_query,__LINE__,__FILE__);
				
				$debug_query = $db->Execute("UPDATE $dbtables[config_values] SET value=$radius WHERE name='universe_size'");
				db_op_result($debug_query,__LINE__,__FILE__);
				
				if ($diff > 1)
				{
				  echo "<BR>Universe successfully Expanded. New universe radius is $radius.";
				}
				else
				{
				  echo "<BR>Universe successfully Contracted. New universe radius is $radius.";
				}
			}
?>