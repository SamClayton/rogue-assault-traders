<?
// Sector Notes

include ("config/config.php");
include("languages/$langdir/lang_sector_notes.inc");

if (checklogin() or $tournament_setup_access == 1)
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

if ((!isset($sectorid)) || ($sectorid == ''))
{
	 $sectorid = $shipinfo['sector_id'];
}

$title = "$l_sn_title $sectorid";

if($base_template[basename($_SERVER['PHP_SELF'])] == 1){
	base_template_data();
}
else
{
	$smarty->assign("title", $title);
	$smarty->assign("templatename", $templatename);
}

$time = date("Y-m-d H:i:s");

if ($command=="showpersonal")
{
	$result = $db->Execute("SELECT distinct note_sector_id FROM $dbtables[sector_notes] WHERE note_player_id=$playerinfo[player_id] order by note_sector_id ASC");
	$count = 0;
	while (!$result->EOF && $result)
	{
		$sectorlist[$count] = $result->fields['note_sector_id'];
		$break = $count % 20;
		$count++;
		$result->MoveNext();
	}
}

if ($command=="showteam")
{
	$result = $db->Execute("SELECT distinct note_sector_id FROM $dbtables[sector_notes] WHERE note_team_id=$playerinfo[team] order by note_sector_id ASC");
	$count = 0;
	while (!$result->EOF && $result)
	{
		$sectorlist[$count] = $result->fields['note_sector_id'];
		$break = $count % 20;
		$count++;
		$result->MoveNext();
	}
}

if ($command==$l_sn_deleteteam and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "DELETE FROM $dbtables[sector_notes] WHERE note_sector_id=$shipinfo[sector_id] and note_id = $note_id";
	$result = $db->Execute($xsql);
	db_op_result($result,__LINE__,__FILE__);
}

if ($command==$l_sn_addteam and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "INSERT INTO $dbtables[sector_notes] (note_data, note_team_id, note_sector_id, note_date) VALUES ('$note_data', $playerinfo[team], $shipinfo[sector_id], '$time')";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

if ($command==$l_sn_saveteam and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "UPDATE $dbtables[sector_notes] SET note_data='$note_data' WHERE note_sector_id=$shipinfo[sector_id] and note_id=$note_id";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

if ($command==$l_sn_deletepersonal and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "DELETE FROM $dbtables[sector_notes] WHERE note_sector_id=$shipinfo[sector_id] and note_id = $note_id";
	$result = $db->Execute($xsql);
	db_op_result($result,__LINE__,__FILE__);
}

if ($command==$l_sn_addpersonal and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "INSERT INTO $dbtables[sector_notes] (note_data, note_player_id, note_sector_id, note_date) VALUES ('$note_data', $playerinfo[player_id], $shipinfo[sector_id], '$time')";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

if ($command==$l_sn_savepersonal and $sectorid == $shipinfo['sector_id'])
{
	$xsql = "UPDATE $dbtables[sector_notes] SET note_data='$note_data' WHERE note_id=$note_id";
	$debug_query = $db->Execute($xsql);
	db_op_result($debug_query,__LINE__,__FILE__);
}

$showlistcount = 0;
$showteamlistcount = 0;
$editid = 0;
$teameditid = 0;
$notelistcount = 0;
$teamnotelistcount = 0;

if ($command==$l_sn_editpersonal and $sectorid == $shipinfo['sector_id'])
{
	$result = $db->Execute("SELECT * FROM $dbtables[sector_notes] where note_sector_id=$shipinfo[sector_id] and note_id=$note_id and note_player_id=$playerinfo[player_id]");
	db_op_result($debug_query,__LINE__,__FILE__);
	$editid = $result->fields['note_id'];
	$editnoteid = $result->fields['note_data'];
}

$result = $db->Execute("SELECT * FROM $dbtables[sector_notes] WHERE note_sector_id=$sectorid and note_player_id=$playerinfo[player_id] ORDER BY note_date DESC");
while (!$result->EOF && $result)
{
	$row = $result->fields;

	$notelistid[$notelistcount] = $result->fields['note_id'];
	$notelistnote[$notelistcount] = $result->fields['note_data'];
	$notelistdate[$notelistcount] = $result->fields['note_date'];

	$notelistcount++;
	$result->MoveNext();
}

if($playerinfo['team'] > 0){

	if ($command==$l_sn_editteam and $sectorid == $shipinfo['sector_id'])
	{
		$result = $db->Execute("SELECT * FROM $dbtables[sector_notes] where note_sector_id=$shipinfo[sector_id] and note_id=$note_id and note_team_id=$playerinfo[team]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$teameditid = $result->fields['note_id'];
		$teameditnoteid = $result->fields['note_data'];
	}

	$result = $db->Execute("SELECT * FROM $dbtables[sector_notes] WHERE note_sector_id=$sectorid and note_team_id=$playerinfo[team] ORDER BY note_date DESC");
	while (!$result->EOF && $result)
	{
		$row = $result->fields;

		$teamnotelistid[$teamnotelistcount] = $result->fields['note_id'];
		$teamnotelistnote[$teamnotelistcount] = $result->fields['note_data'];
		$teamnotelistdate[$teamnotelistcount] = $result->fields['note_date'];

		$teamnotelistcount++;
		$result->MoveNext();
	}
}

$smarty->assign("l_sn_pntitle", $l_sn_pntitle);
$smarty->assign("l_sn_tntitle", $l_sn_tntitle);
$smarty->assign("l_sn_psntitle", $l_sn_psntitle);
$smarty->assign("l_sn_tsntitle", $l_sn_tsntitle);
$smarty->assign("l_sn_editnote", $l_sn_editnote);
$smarty->assign("l_sn_deleteedit", $l_sn_deleteedit);
$smarty->assign("l_sn_addnote", $l_sn_addnote);
$smarty->assign("l_sn_listps", $l_sn_listps);
$smarty->assign("l_sn_listts", $l_sn_listts);
$smarty->assign("l_sn_addteam", $l_sn_addteam);
$smarty->assign("l_sn_deleteteam", $l_sn_deleteteam);
$smarty->assign("l_sn_saveteam", $l_sn_saveteam);
$smarty->assign("l_sn_deletepersonal", $l_sn_deletepersonal);
$smarty->assign("l_sn_addpersonal", $l_sn_addpersonal);
$smarty->assign("l_sn_savepersonal", $l_sn_savepersonal);
$smarty->assign("l_sn_editpersonal", $l_sn_editpersonal);
$smarty->assign("l_sn_editteam", $l_sn_editteam);
$smarty->assign("command", $command);
$smarty->assign("count", $count);
$smarty->assign("sectorlist", $sectorlist);
$smarty->assign("playerteam", $playerinfo['team']);
$smarty->assign("showlistcount", $showlistcount);
$smarty->assign("showteamlistcount", $showteamlistcount);
$smarty->assign("editid", $editid);
$smarty->assign("editnoteid", $editnoteid);
$smarty->assign("teameditid", $teameditid);
$smarty->assign("teameditnoteid", $teameditnoteid);
$smarty->assign("sectorid", $sectorid);
$smarty->assign("shipsectorid", $shipinfo['sector_id']);

$smarty->assign("notelistcount", $notelistcount);
$smarty->assign("notelistid", $notelistid);
$smarty->assign("notelistnote", $notelistnote);
$smarty->assign("notelistdate", $notelistdate);
$smarty->assign("teamnotelistcount", $teamnotelistcount);
$smarty->assign("teamnotelistid", $teamnotelistid);
$smarty->assign("teamnotelistnote", $teamnotelistnote);
$smarty->assign("teamnotelistdate", $teamnotelistdate);

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."sector_notes.tpl");
include ("footer.php");

?>