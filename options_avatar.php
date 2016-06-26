<?php
// This program is free software; you can redistribute it and/or modify it   
// under the terms of the GNU General Public License as published by the	 
// Free Software Foundation; either version 2 of the License, or (at your	
// option) any later version.												
// 
// File: options.php

include ("config/config.php");
include ("languages/$langdir/lang_options_avatar.inc");

$title = $l_opt_title;

if ((!isset($i)) || ($i == ''))
{
	$i = 0;
}

if (checklogin())
{
	include ("footer.php");
	die();
}

if($playerinfo['template'] == '' or !isset($playerinfo['template'])){
	$templatename = $default_template;
}else{
	$templatename = $playerinfo['template'];
}
include ("templates/".$templatename."/skin_config.inc");
include ("header.php");

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

function RecurseDir($basedir, $AllDirectories=array()) { 
	#Create array for current directories contents 
	$ThisDir=array(); 
	if ($handle = @opendir($basedir)) {
    	while (false !== ($file = readdir($handle))) { 
			if ($file != "." && $file != "..") { 
				if (is_dir($basedir."/".$file)) {
					array_push($ThisDir,$file);     
				}
			}
		}
		closedir($handle); 
	}
	#Loop through each directory,  run RecurseDir function on each one 
	foreach ($ThisDir as $key=>$var) { 
		array_push($AllDirectories, $var); 
		$AllDirectories=RecurseDir($var, $AllDirectories); 
	} 
	return $AllDirectories; 
}

if($action == "selectgraphic" and ($galleryimage != '' or isset($galleryimage))){

	if($playerinfo['avatar'] != "default_avatar.gif"){
		$junkold = explode("/", $playerinfo['avatar']);
		$galleryold = $junkold[0];
		$pictureold = $junkold[1];
		if($galleryold == "uploads"){
			@unlink($gameroot."images/avatars/uploads/$pictureold");
		}
	}
	$playerinfo['avatar'] = $gallery."/$galleryimage";
	$debug_query = $db->Execute("UPDATE $dbtables[players] SET avatar='$playerinfo[avatar]' WHERE player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
}

if($action == "uploadgraphic" and $allow_avatar_upload == 1){

	if($playerinfo['avatar'] != "default_avatar.gif"){
		$junkold = explode("/", $playerinfo['avatar']);
		$galleryold = $junkold[0];
		$pictureold = $junkold[1];
		if($galleryold == "uploads"){
			@unlink($gameroot."images/avatars/uploads/$pictureold");
		}
	}

	$img_src=$_FILES['img_src']['tmp_name'];
	if($img_src != '' and isset($img_src))	   
	{
		$checkit = GetImageSize($img_src); 
		if($checkit[2] > 0 and $checkit[2] < 4){

			$imagewidth = $checkit[0];
			$imageheight = $checkit[1];

			if($imagewidth < 65 and $imageheight < 65){
				if ($checkit[2] == 1)
				{
					copy($img_src,$gameroot."images/avatars/uploads/$playerinfo[player_id].gif");
					$galleryimage="$playerinfo[player_id].gif";
				}

				if ($checkit[2] == 2)		  	
				{
					copy($img_src,$gameroot."images/avatars/uploads/$playerinfo[player_id].jpg");
					$galleryimage="$playerinfo[player_id].jpg";	
				}

				if ($checkit[2] == 3)		  	
				{
					copy($img_src,$gameroot."images/avatars/uploads/$playerinfo[player_id].png");
					$galleryimage="$playerinfo[player_id].png";	
				}

				$playerinfo['avatar'] = "uploads/$galleryimage";
				$debug_query = $db->Execute("UPDATE $dbtables[players] SET avatar='$playerinfo[avatar]' WHERE player_id=$playerinfo[player_id]");
				db_op_result($debug_query,__LINE__,__FILE__);
			}else{
				$smarty->assign("error_msg", $l_opt_errorsize);
				$smarty->assign("gotomain", $l_global_mmenu);
				$smarty->display($templatename."options_avatardie.tpl");
				include ("footer.php");
				die();
			}
		}else{
			$smarty->assign("error_msg", $l_opt_errortype);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."options_avatardie.tpl");
			include ("footer.php");
			die();
		}
	}else{
		$smarty->assign("error_msg", $l_opt_errornographic);
		$smarty->assign("gotomain", $l_global_mmenu);
		$smarty->display($templatename."options_avatardie.tpl");
		include ("footer.php");
		die();
	}
}

if($gallery == '' or !isset($gallery)){
	if($playerinfo['avatar'] != "default_avatar.gif"){
		$junk = explode("/", $playerinfo['avatar']);
		$gallery = $junk[0];
	}
}

if($playerinfo['avatar'] != "default_avatar.gif"){
	$junk = explode("/", $playerinfo['avatar']);
	$picture = $junk[1];
}

$dirlist=RecurseDir($gameroot."images/avatars"); 
chdir($gameroot);
$directorycount = 0;
foreach ($dirlist as $key=>$val) { 
	$gallerybase = str_replace($gameroot."images/avatars/", "", $val);
	if($gallery == '' or !isset($gallery) or $gallery == "uploads")
		$gallery = $gallerybase;
	if($gallery == $gallerybase)
		$selected = "selected";
	else $selected = "";

	$galleryarray = explode("/", $gallerybase);

	if($gallerybase != "CVS" and $gallerybase != "uploads" and count($galleryarray) == 1){
		$directoryitem[$directorycount] =  $gallerybase;
		$directoryselect[$directorycount] = $selected;
		$directorycount++;
	} 
}

$start_dir = $gameroot."images/avatars/$gallery"; 
$filelist = get_dirlist($start_dir);
unset ($galleryimage);

$itemcount = 0;
for ($c=0; $c<count($filelist); $c++) { 
	$gallerypicture =  str_replace($gameroot."images/avatars/$gallery/", "", $filelist[$c]); 
	$validimage = 0;

	if(strstr(strtolower($gallerypicture), ".jpg") or strstr(strtolower($gallerypicture), ".jpeg") or strstr(strtolower($gallerypicture), ".gif") or strstr(strtolower($gallerypicture), ".png"))
		$validimage = 1;

	$result_avatar = $db->Execute("SELECT * FROM $dbtables[players] WHERE avatar='".str_replace($gameroot."images/avatars/", "", $gallery. "/". $filelist[$c])."' and player_id != $playerinfo[player_id]");
	if($result_avatar->RecordCount() == 0 and $validimage == 1){
		if($picture == $gallerypicture)
			$checked = "checked";
		else $checked = "";

		$galleryimage[$itemcount] = $gallerypicture;
		$gallerychecked[$itemcount] = $checked;
		$galleryfile[$itemcount] = str_replace($gameroot."images/avatars/$gallery/", "", $filelist[$c]);
		$itemcount++;
	}
}

$smarty->assign("l_opt_currentavatar", $l_opt_currentavatar);
$smarty->assign("currentavatar", $playerinfo['avatar']);
$smarty->assign("allow_avatar_upload", $allow_avatar_upload);
$smarty->assign("l_opt_uploadavatar", $l_opt_uploadavatar);
$smarty->assign("l_opt_uploadtypes", $l_opt_uploadtypes);
$smarty->assign("l_opt_uploadavatar", $l_opt_uploadavatar);
$smarty->assign("l_opt_directory", $l_opt_directory);
$smarty->assign("directorycount", $directorycount);
$smarty->assign("directoryitem", $directoryitem);
$smarty->assign("directoryselect", $directoryselect);
$smarty->assign("l_opt_change", $l_opt_change);
$smarty->assign("gallery", $gallery);
$smarty->assign("itemcount", $itemcount);
$smarty->assign("galleryimage", $galleryimage);
$smarty->assign("gallerychecked", $gallerychecked);
$smarty->assign("galleryfile", $galleryfile);
$smarty->assign("l_opt_select", $l_opt_select);
$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."options_avatar.tpl");
include ("footer.php");
?>
