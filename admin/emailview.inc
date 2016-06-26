<?
// System Email Log Viewer

			$selfpath = basename($_SERVER['PHP_SELF']);
			echo "<div align=\"left\">\n";
			echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n";
			echo "	<tr>\n";
			echo "	  <td width=\"50%\" nowrap><b>Email Log Viewer</b></td>\n";
			echo "	</tr>\n";
			echo "  </table>\n";
			echo "</div>\n";
			echo "<form action=$selfpath method=post>";
			echo "<div align=\"center\">\n";
			echo "  <center>\n";
			echo "  <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n";
			echo "	<tr>\n";
			echo "	  <td width=\"50%\"></td>\n";
			echo "	  <td width=\"50%\" align=\"right\"><input type=\"submit\" value=\"Clear E-Logs\" name=\"cmd\"><input type=\"submit\" value=\"Refresh\" name=\"cmd\"></td>\n";
			echo "			<input type=\"hidden\" name=\"swordfish\" value=$_POST[swordfish]>\n";
			echo "			<input type=\"hidden\" name=\"menu\" value=\"emailview\">\n";
			echo "	</tr>\n";
			echo "  </table>\n";
			echo "  </center>\n";
			echo "</div>\n";
			echo "</form>\n";
			if ($cmd == "Clear E-Logs")
			{
				$db->Execute("DROP TABLE $dbtables[email_log]");
				$db->Execute("CREATE TABLE $dbtables[email_log](" .
							 "log_id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment," .
							 "sp_name varchar(50) NOT NULL," .
							 "sp_IP tinytext NOT NULL," .
							 "dp_name varchar(50) NOT NULL," .
							 "e_subject varchar(250)," .
							 "e_status enum('Y','N') DEFAULT 'N' NOT NULL," .
							 "e_type tinyint(3) unsigned DEFAULT '0' NOT NULL," .
							 "e_stamp char(20)," .
							 "e_response varchar(250)," .
							 "PRIMARY KEY (log_id)" .
							 ")");
				$cmd="Refresh";
			}

			if (empty($cmd)|| $cmd =="Refresh")
			{
				echo "<br>\n";
				$res =  $db->Execute ("SELECT * FROM $dbtables[email_log] order by log_id DESC");
				echo "<body bgcolor=\"#000040\" text=\"#FFFFFF\">\n";
				echo "<div align=\"center\">\n";
				echo "  <center>\n";
				echo "  <table cellSpacing=\"1\" cellPadding=\"2\" width=\"100%\" bgColor=\"#e7c03d\" border=\"0\">\n";
				echo "	<tbody>\n";
				echo "	  <tr>\n";
				echo "		<td noWrap width=\"50\" bgColor=\"#800000\" align=\"left\"><font size=\"2\" color=\"#FFFFFF\"><i><b>ID NO</b></i></font></td>\n"; 
				echo "		<td noWrap width=\"150\" bgColor=\"#800000\" align=\"left\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Source</b></i></font></td>\n"; 
				echo "		<td noWrap width=\"100\" bgColor=\"#800000\" align=\"center\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Source IP</b></i></font></td>\n"; 
				echo "		<td noWrap width=\"150\" bgColor=\"#800000\" align=\"left\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Destination</b></i></font></td>\n"; 
				echo "		<td noWrap bgColor=\"#800000\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Topic&nbsp;</b></i></font></td>\n"; 
				echo "		<td noWrap width=\"100\" bgColor=\"#800000\" align=\"center\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Delivery</b></i></font></td>\n"; 
				echo "		<td noWrap bgColor=\"#800000\" align=\"center\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Log Type</b></i></font></td>\n"; 
				echo "		<td noWrap bgColor=\"#800000\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Date of Log</b></i></font></td>\n"; 
				echo "		<td noWrap bgColor=\"#800000\"><font size=\"2\" color=\"#FFFFFF\"><i><b>Response</b></i></font></td>\n"; 
				echo "	  </tr>\n"; 
				while (!$res->EOF)
				{
					$row = $res->fields;
					if ($row['e_type']==MiscEmail)$LogType = "MiscEmail";
					if ($row['e_type']==Registering)$LogType = "Registeration";
					if ($row['e_type']==Feedback)$LogType = "Feedback";
					if ($row['e_type']==ReqPassword)$LogType = "PW Request";
					if ($row['e_type']==DebugInfo)$LogType = "Debug Info";
					if ($row['e_type']==GlobalEmail)$LogType = "Global Email";
					if ($row['e_status']=='Y') $Delivery = "<font size=\"1\" color=\"lime\">Successful</font>";else $Delivery = "<font size=\"1\" color=\"red\">Fail</font>"; 
					echo "	  <tr>\n"; 
					echo "		<td noWrap width=\"50\" bgColor=\"#000080\" align=\"center\"><font size=\"1\" color=\"#FFFF00\">$row[log_id]</font></td>\n"; 
					echo "		<td noWrap width=\"150\" bgColor=\"#000080\" align=\"left\"><font size=\"1\" color=\"#FFFF00\">$row[sp_name]</font></td>\n"; 
					echo "		<td noWrap width=\"100\" bgColor=\"#000080\" align=\"center\"><font size=\"1\" color=\"#FFFF00\">$row[sp_IP]</font></td>\n"; 
					echo "		<td noWrap width=\"150\" bgColor=\"#000080\" align=\"left\"><font size=\"1\" color=\"#FFFF00\">$row[dp_name]</font></td>\n"; 
					echo "		<td noWrap bgColor=\"#000080\"><font size=\"1\" color=\"#FFFF00\">$row[e_subject]</font></td>\n"; 
					echo "		<td noWrap width=\"100\" bgColor=\"#000080\" align=\"center\"><font size=\"1\">$Delivery</font></td>\n"; 
					echo "		<td noWrap bgColor=\"#000080\" align=\"center\"><font size=\"1\" color=\"#FFFF00\">$LogType</font></td>\n"; 
					echo "		<td noWrap bgColor=\"#000080\"><font size=\"1\" color=\"#FFFFFF\">$row[e_stamp]</font></td>\n"; 
					echo "		<td noWrap bgColor=\"#000080\"><font size=\"1\" color=\"#FFFFFF\">$row[e_response]</font></td>\n"; 
					echo "	  </tr>\n"; 
					$res->MoveNext(); 
				} 
			echo "</table>\n</center></div>\n"; 
			echo "</body>\n"; 
			$res = ''; 
			} 
?>
