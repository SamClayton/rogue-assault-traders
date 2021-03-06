<?
// Selective Database Restore

function dec($data) 
{
	global $iv;
	$dectext = mcrypt_ecb (MCRYPT_TripleDES,(Keysize), hex2bin($data), MCRYPT_DECRYPT,$iv); 
	return $dectext; 
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

if($db_type == "mysql"){
	if(empty($tablename)){
		echo "Select the table you would like to restore:<BR><BR>\n";
		echo "<form action=\"admin.php\" method=\"post\">\n";
		echo "  <select name=\"tablename\">\n";
		foreach($dbtables as $tablename){
			echo "	<option value=\"$tablename\">$tablename</option>\n";
		}
		echo "  </select>\n";
		echo "  <input type=\"hidden\" name=\"swordfish\" value=\"$_POST[swordfish]\">\n";
		echo "  <input type=\"hidden\" name=\"menu\" value=\"selectiverestore\">\n";
		echo "  &nbsp;<input type=\"submit\" value=\"Submit\">\n";
		echo "</form>\n";
	}else{
		set_time_limit(0);

		$path = $gameroot . "backup/";
		echo "<b>Starting Database Restore</b><br><br>";
		flush();
		echo "<b><i>Restoring table: $tablename</b></i><br>";
		flush();

		if($backup_encryption_type == 1)
			$encrypt =& new encrypt( $ADODB_CRYPT_KEY ); 

		if($backup_encryption_type == 2){
			DEFINE ("Keysize", $ADODB_CRYPT_KEY); 
			$td = mcrypt_module_open (MCRYPT_TripleDES, "", MCRYPT_MODE_ECB, ""); 
			$iv = mcrypt_create_iv (mcrypt_enc_get_iv_size ($td), MCRYPT_RAND); 
		}

		if(file_exists($path . $tablename . "_table.sql"))
		{
			$fp = fopen ($path.$tablename . "_table.sql","r");
			$delete = fgets($fp);
			$debug_query = $db->Execute($delete);
			db_op_result($debug_query,__LINE__,__FILE__);
			$table = fread($fp, 8192);
			$debug_query = $db->Execute($table);
			db_op_result($debug_query,__LINE__,__FILE__);
			fclose ($fp);

			$fp = fopen ($path.$tablename . "_data.sql","r");
			while(!feof($fp)){
				$insert = str_replace("\n", "", trim(fgets($fp)));
				if(!empty($insert)){

					if($backup_encryption_type == 1)
						$insert = $encrypt->cipher_string( $insert ); 
					if($backup_encryption_type == 2)
						$insert = dec( $insert ); 

					echo "$insert<br>";
					$debug_query = $db->Execute($insert);
					db_op_result($debug_query,__LINE__,__FILE__);
				}
			}
			fclose ($fp);
		}
	}
}
else
{
	echo "<br><b>Restore option can only be used for MySql databases</b><br>";
}
?>