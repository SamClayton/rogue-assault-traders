<?php
include("config/config.php");
include("languages/$langdir/lang_teams.inc");
include("languages/$langdir/lang_report.inc");

$title="Team Member Ship Levels";

if (checklogin()) {
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

if ((!isset($orderby)) || ($orderby == ''))
{
	$orderby = 'p.character_name';
}

if ((!isset($direction)) || ($direction == ''))
{
	$direction = '';
}

if ((!isset($whichteam)) || ($whichteam == ''))
{
	$whichteam = '';
}

/* Get user info */
$result		= $db->Execute("SELECT $dbtables[players].*, $dbtables[teams].team_name, $dbtables[teams].description, $dbtables[teams].creator, $dbtables[teams].id
						FROM $dbtables[players]
						LEFT JOIN $dbtables[teams] ON $dbtables[players].team = $dbtables[teams].id
						WHERE $dbtables[players].email='$username'");
$playerinfo	= $result->fields;

/*
   Get Team Info
*/
$whichteam = stripnum($whichteam);
if ($whichteam)
{
	$result_team   = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$whichteam");
	$team		  = $result_team->fields;
} else {
	$result_team   = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
	$team		  = $result_team->fields;
}

if($playerinfo[team] != 0){
	$result = $db->Execute("SELECT * FROM $dbtables[teams] WHERE id=$playerinfo[team]");
	$whichteam = $result->fields;
	$isowner = ($playerinfo['player_id'] == $whichteam['creator']);
	$whichteam = $playerinfo['team'];

	for($iz=0; $iz<50; $iz++){
		if($iz<10)
			$colorarray[$iz] = "#FFADAD";
		if($iz>9 and $iz<20)
			$colorarray[$iz] = "#FFFF00";
		if($iz>19 and $iz<30)
			$colorarray[$iz] = "#0CD616";
		if($iz>29)
			$colorarray[$iz] = "#ffffff";
	}

	$result  = $db->Execute("SELECT * FROM $dbtables[players] as p, $dbtables[ships] as s WHERE p.team=$whichteam and s.player_id=p.player_id AND s.ship_id=p.currentship order by ".$orderby." ".$direction);
	$shipcount = 0;
	while (!$result->EOF) {
		$member = $result->fields;

		$debug_query = $db->Execute("SELECT * FROM $dbtables[ship_types] WHERE type_id=$member[class]");
		db_op_result($debug_query,__LINE__,__FILE__);
		$classstuff = $debug_query->fields;

		$hull[$shipcount] = $member['hull'];
		$engines[$shipcount] = $member['engines'];
		$power[$shipcount] = $member['power'];
		$computer[$shipcount] = $member['computer'];
		$sensors[$shipcount] = $member['sensors'];
		$armour[$shipcount] = $member['armour'];
		$shields[$shipcount] = $member['shields'];
		$beams[$shipcount] = $member['beams'];
		$torps[$shipcount] = $member['torp_launchers'];
		$cloak[$shipcount] = $member['cloak'];
		$ecm[$shipcount] = $member['ecm'];
		$shipname[$shipcount] = $member['name'];
		$playername[$shipcount] = $member['character_name'];
		$playeravatar[$shipcount] = $member['avatar'];
		$shipclassname[$shipcount] = $classstuff['name'];
		$memberclass[$shipcount] = $member['class'];
		$colorhull[$shipcount] = $colorarray[$hull[$shipcount]];
		$colorengines[$shipcount] = $colorarray[$engines[$shipcount]];
		$colorpower[$shipcount] = $colorarray[$power[$shipcount]];
		$colorcomputer[$shipcount] = $colorarray[$computer[$shipcount]];
		$colorsensors[$shipcount] = $colorarray[$sensors[$shipcount]];
		$colorarmour[$shipcount] = $colorarray[$armour[$shipcount]];
		$colorshields[$shipcount] = $colorarray[$shields[$shipcount]];
		$colorbeams[$shipcount] = $colorarray[$beams[$shipcount]];
		$colortorps[$shipcount] = $colorarray[$torps[$shipcount]];
		$colorcloak[$shipcount] = $colorarray[$cloak[$shipcount]];
		$colorecm[$shipcount] = $colorarray[$ecm[$shipcount]];
		$score[$shipcount] = NUMBER($member['score']);
		$linecolor[$shipcount] = $color_line2;
		$coordinator[$shipcount] = "";

		if ($member['player_id'] == $team['creator'])
		{
			$coordinator[$shipcount] = $l_team_coord;
		}

		$shipcount++;
		$result->MoveNext();
	}

	$smarty->assign("l_avatar", $l_avatar);
	$smarty->assign("l_team_members", $l_team_members);
	$smarty->assign("color_line2", $color_line2);
	$smarty->assign("l_hull", $l_hull);
	$smarty->assign("l_engines", $l_engines);
	$smarty->assign("l_power", $l_power);
	$smarty->assign("l_computer", $l_computer);
	$smarty->assign("l_sensors", $l_sensors);
	$smarty->assign("l_armour", $l_armour);
	$smarty->assign("l_shields", $l_shields);
	$smarty->assign("l_beams", $l_beams);
	$smarty->assign("l_torp_launch", $l_torp_launch);
	$smarty->assign("l_cloak", $l_cloak);
	$smarty->assign("l_ecm", $l_ecm);
	$smarty->assign("l_team_score", $l_team_score);
	$smarty->assign("teamname", $team['team_name']);
	$smarty->assign("description", $team['description']);
	$smarty->assign("shipcount", $shipcount);
	$smarty->assign("main_table_heading", $main_table_heading);
	$smarty->assign("l_team_class", $l_team_class);
	$smarty->assign("hull", $hull);
	$smarty->assign("engines", $engines);
	$smarty->assign("power", $power);
	$smarty->assign("computer", $computer);
	$smarty->assign("sensors", $sensors);
	$smarty->assign("armour", $armour);
	$smarty->assign("shields", $shields);
	$smarty->assign("beams", $beams);
	$smarty->assign("torps", $torps);
	$smarty->assign("cloak", $cloak);
	$smarty->assign("ecm", $ecm);
	$smarty->assign("shipname", $shipname);
	$smarty->assign("playername", $playername);
	$smarty->assign("playeravatar", $playeravatar);
	$smarty->assign("shipclassname", $shipclassname);
	$smarty->assign("memberclass", $memberclass);
	$smarty->assign("colorhull", $colorhull);
	$smarty->assign("colorengines", $colorengines);
	$smarty->assign("colorpower", $colorpower);
	$smarty->assign("colorcomputer", $colorcomputer);
	$smarty->assign("colorsensors", $colorsensors);
	$smarty->assign("colorarmour", $colorarmour);
	$smarty->assign("colorshields", $colorshields);
	$smarty->assign("colorbeams", $colorbeams);
	$smarty->assign("colortorps", $colortorps);
	$smarty->assign("colorcloak", $colorcloak);
	$smarty->assign("colorecm", $colorecm);
	$smarty->assign("score", $score);
	$smarty->assign("linecolor", $linecolor);
	$smarty->assign("coordinator", $coordinator);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team-report.tpl");
	include ("footer.php");
	die();
}else{
	$smarty->assign("error_msg", $l_team_notmember);
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."team-reportdie.tpl");
	include ("footer.php");
	die();
}

close_database();
?>

