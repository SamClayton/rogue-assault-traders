<?
include ("../config/config_local.php");
include ("../languages/$default_lang/regional_settings.php");
if (!empty($_GET)) {
	extract($_GET);
} else if (!empty($HTTP_GET_VARS)) {
	extract($HTTP_GET_VARS);
}
// Define the adodb directory:
define ('ADODB_DIR',"$ADOdbpath");

// Define the smarty directory:
define ('SMARTY_DIR',"$gameroot" . "backends/smarty2/libs/");

// Define the smarty class location:
define('SMARTY_CLASS', SMARTY_DIR . "Smarty.class.php");   
include ("faqheader.php");

$section=ucwords(str_replace("_"," ",$section));
if ($section==""){

$section="Overview";
}

$smarty->assign("gameroot", $gameroot);
$smarty->assign("section", $section);
$smarty->display("faq/faqindex.tpl");
include ("faqfooter.php");
?>