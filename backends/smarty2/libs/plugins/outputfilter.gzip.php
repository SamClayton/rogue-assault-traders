<?php
/*
 * Author: Mark Dickenson, akapanamajack@wildmail.com
 * You can stack multiple template display commands to have the entire page output as a compressed file.
 * A global variable $no_gzip was added to allow the compression to be turned off in the middle
 * of a multi-template output.
 *
 * This output filter was specifically modified to work only with Alien Assault Traders
 */


function smarty_outputfilter_gzip($tpl_source, &$smarty)
{
	global $no_gzip, $send_now, $tpl_saved, $playerinfo, $force_compession, $compression_level;

	if(!$no_gzip){
		if(extension_loaded("zlib") && !$smarty->caching && (strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") || $force_compession)){
				$tpl_saved[$playerinfo['player_id']] .= $tpl_source."\n<!-- zlib compression level ".$compression_level." -->\n\n";
				$tpl_source = "";
		}

		if($send_now == 1){
			$tpl_source = gzencode($tpl_saved[$playerinfo['player_id']],$compression_level);
			$tpl_saved[$playerinfo['player_id']] = "";
			header("Content-Encoding: gzip");
			header("Vary: Accept-Encoding");
			header("Content-Length: ".strlen($tpl_source));
		}
	}

	return $tpl_source;
}
?>