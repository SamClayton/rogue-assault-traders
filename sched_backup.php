<?php
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or (at your
// option) any later version.
//
// File: sched_ports.php

if (preg_match("/sched_backup.php/i", $_SERVER['PHP_SELF'])) 
{
	echo "You can not access this file directly!";
	die();
}

function enc($data) 
{ 
	$encdata = mcrypt_ecb (MCRYPT_TripleDES,(Keysize), $data, MCRYPT_ENCRYPT, $iv); 
	$hextext=bin2hex($encdata); 
	return $hextext; 
} 

function hex2bin($data) 
{ 
$len = strlen($data); 
return pack("H" . $len, $data); 
} 

class encrypt { 

	var $intArray = array(); 
	var $byteArray = array(); 
	var $int1 = 0; 
	var $int2 = 0; 
	var $key = null; 
	var $keylen = 0; 

	function encrypt( $key ) { 
		$this->setKey( $key ); 
	} 

	function setKey( $key ) { 
		$this->key = null; 
		$key = md5( (string)$key ); 
		for( $idx = 0; $idx < 32; $idx += 4 ) { 
			$this->key .= md5( substr( $key, $idx, 4 ) ); 
		} 
		$this->keylen = strlen( $this->key ); 
		$bytes = $this->keylen / 2; 
		$bits = $bytes * 8; 
		return( $this->key ); 
	} 

	function init() { 
		// Save the key in a Byte Array 
		$this->intArray = array(); 
		$this->byteArray = array(); 
		$this->int1 = 0; 
		$this->int2 = 0; 
		if( ( strlen( $this->key ) % 2 ) > 0 ) { 
			// if the key is not an even number of characters, lose the last one 
			// this is supposed to be a string of bytes (hex pairs) 
			$this->key .= substr( $this->key, 0, -1 ); 
		} 
		$this->keylen = strlen( $this->key ); 

		for( $idx = 0, $idx2 = 0; $idx < 128; $idx++, $idx2+=2 ) { 
		if( $idx2 >= $this->keylen ) { 
				$idx2 = 0; 
			} 
			$this->byteArray[$idx] = hexdec( substr( $this->key, $idx2, 2 ) ); 
			$this->intArray[$idx] = $idx; 
		} 
		for( $idx = 0; $idx < 128; $idx++ ) { 
			$idx2 = ( $idx2 + $this->intArray[$idx] + $this->byteArray[$idx] ) % 128; 
			$temp = $this->intArray[$idx]; 
			$this->intArray[$idx] = $this->intArray[$idx2]; 
			$this->intArray[$idx2] = $temp; 
		} 
	} 

	function cipher_byte( $byte ) { 
		if( !is_int( $byte ) ) { 
			$byte = ord( $byte ); 
		} 
		$this->int1 = ( $this->int1 + 1 ) % 128; 
		$this->int2 = ( $this->int2 + $this->intArray[$this->int1] ) % 128; 

		$temp = $this->intArray[$this->int1]; 
		$this->intArray[$this->int1] = $this->intArray[$this->int2]; 
		$this->intArray[$this->int2] = $temp; 
		$intX = $this->intArray[($this->intArray[$this->int1] + $this->intArray[$this->int2]) % 128]; 
		return( $byte ^ $intX + 128); 
	} 

	function cipher_string( $string ) { 
		$this->init(); 
		for( $idx = 0, $len = strlen( $string ); $idx < $len; $idx++ ) { 
			$cipher .= chr( $this->cipher_byte( ord( $string{$idx} ) ) ); 
		} 
		return( $cipher ); 
	} 
} 

function get_def($dbname, $table) {
    global $conn, $fieldnames;
	$count = 0;
    $def = "";
    $def .= "DROP TABLE IF EXISTS $table;\n";
    $def .= "CREATE TABLE $table (\n";
    $result = mysql_db_query($dbname, "SHOW FIELDS FROM $table",$conn) or die("Table $table not existing in database");
    while($row = mysql_fetch_array($result)) {
        $def .= "    $row[Field] $row[Type]";
 		$fieldnames[$count] = $row['Field'];
		$count++;
        if ($row["Default"] != "") $def .= " DEFAULT '$row[Default]'";
        if ($row["Null"] != "YES") $def .= " NOT NULL";
       	if ($row[Extra] != "") $def .= " $row[Extra]";
        	$def .= ",\n";
     }
     $def = ereg_replace(",\n$","", $def);
     $result = mysql_db_query($dbname, "SHOW KEYS FROM $table",$conn);
     while($row = mysql_fetch_array($result)) {
          $kname=$row[Key_name];
          if(($kname != "PRIMARY") && ($row[Non_unique] == 0)) $kname="UNIQUE|$kname";
          if(!isset($index[$kname])) $index[$kname] = array();
          $index[$kname][] = $row[Column_name];
     }
     while(list($x, $columns) = @each($index)) {
          $def .= ",\n";
          if($x == "PRIMARY") $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
          else if (substr($x,0,6) == "UNIQUE") $def .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
          else $def .= "   KEY $x (" . implode($columns, ", ") . ")";
     }

     $def .= "\n);";
     return (stripslashes($def));
}

function get_content($dbname, $table, $namelist) {
    global $conn, $encrypt, $path, $backup_encryption_type;

	$fp = fopen ($path.$table . "_data.sql","w");

	$content="";
	$result = mysql_db_query($dbname, "SELECT * FROM $table",$conn);
	while($row = mysql_fetch_row($result)) {
		$insert = "INSERT INTO $table (" .$namelist . ") VALUES (";
		for($j=0; $j<mysql_num_fields($result);$j++) {
			if(!isset($row[$j])) $insert .= "NULL,";
			else if($row[$j] != "") $insert .= "'".addslashes($row[$j])."',";
			else $insert .= "'',";
		}
		$insert = ereg_replace(",$","",$insert);
		$insert .= ");";

		if($backup_encryption_type == 1)
			$insert = $encrypt->cipher_string( $insert ); 
		if($backup_encryption_type == 2)
			$insert = enc( $insert ); 

		$content .= $insert."\n";
		fwrite ($fp,$content);
		$content = "";
	}
	fclose ($fp);
}

if($enable_backup == 1 and $db_type == "mysql"){

	$sf = (bool) ini_get('safe_mode');
	if (!$sf)
	{
		set_time_limit(1200);
	}

	$path = $gameroot;

	flush();
	$conn = @mysql_connect($dbhost,$dbuname,$dbpass);
	if ($conn==false)  
		die("password / user or database name wrong");
	$path = $path . "backup/";

	TextFlush ( "<b>Starting Database Backup</b><br><br>");

	if($backup_encryption_type == 1)
		$encrypt =& new encrypt( $ADODB_CRYPT_KEY ); 

	if($backup_encryption_type == 2){
		DEFINE ("Keysize", $ADODB_CRYPT_KEY); 
		$td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, ""); 
		$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND); 
	}

	foreach($dbtables as $tablename){
		TextFlush ( "Backing up table: $tablename<br>");
		flush();
		if(!file_exists($path . $tablename . "_table.sql"))
		{
    		 @unlink($path.$tablename . "_table.sql");
	    	 @unlink($path.$tablename . "_data.sql");
		}

		$cur_time=date("Y-m-d H:i");

		unset($fieldnames);
		$newfile = get_def($dbname,$tablename);

		$fp = fopen ($path.$tablename . "_table.sql","w");
		fwrite ($fp,$newfile);
		fclose ($fp);

		$namelist = "";
		for($i = 0; $i < count($fieldnames); $i++){
			$namelist .= $fieldnames[$i];
			if($i != count($fieldnames) - 1)
				$namelist .= ",";
		}

		get_content($dbname,$tablename, $namelist);

	}
	TextFlush ( "<br><b>Database Backup Complete</b><br><br>");
}

$multiplier = 0;
?>

