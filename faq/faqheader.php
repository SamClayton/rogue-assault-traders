<?php
// File: header.php

header("Cache-Control: no-store, no-cache, must-revalidate");

if (preg_match("/faqheader.php/i", $_SERVER['PHP_SELF']))
{
	echo "You can not access this file directly!";
	die();
}

// Defines to avoid warnings
if ((!isset($no_body)) || ($no_body == ''))
{
	$no_body = '';
}

// Smarty Templates!
require_once (SMARTY_CLASS);
$smarty = new Smarty;
$smarty->template_dir = "../templates/";
$smarty->compile_dir = "../templates_c/";
$smarty->use_sub_dirs = 1;

// put this in your application
function extract_variables($tpl_source, &$smarty)
{
	return str_replace("{php}","{php}extract(\$this->get_template_vars());",$tpl_source);
}
$local_charset = "windows-1252";

// register the prefilter
$smarty->register_prefilter("extract_variables");

$smarty->display("faq/faqheader.tpl");

?>
