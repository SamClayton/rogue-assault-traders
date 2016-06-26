<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: new.php

include ("config/config.php");
include ("languages/$langdir/lang_new.inc");

$title = $l_new_title;
// Skinning stuff
if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");
$smarty->assign("templatename", $templatename);

$smarty->assign("title", $title);
$smarty->assign("l_new_closed_message", $l_new_closed_message);
$smarty->assign("account_creation_closed", $account_creation_closed);
$smarty->assign("l_login_email", $l_login_email);
$smarty->assign("l_new_shipname", $l_new_shipname);
$smarty->assign("l_new_pname", $l_new_pname);
$smarty->assign("l_submit", $l_submit);
$smarty->assign("l_reset", $l_reset);
$smarty->assign("l_new_info", $l_new_info);
$smarty->display($default_template."new.tpl");

include ("footer.php"); 

?>
