<?
include("config/config.php");

include("languages/$langdir/lang_planets.inc");

$title=$l_dig_title;

if(!$dig_success_factor)
{
	$smarty->assign("title", $title);
	$smarty->assign("error_msg", $l_dig_disabled);
	$smarty->assign("error_msg2", "");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."dig-die.tpl");
	include("footer.php");
	die();
}

if(checklogin() or $tournament_setup_access == 1)
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

if ((!isset($command)) || ($command == ''))
{
$command = '';
}
if ((!isset($by)) || ($by == ''))
{
$by = '';
}
if ((!isset($by1)) || ($by1 == ''))
{
$by1 = '';
}
if ((!isset($by2)) || ($by2 == ''))
{
$by2 = '';
}
if ((!isset($by3)) || ($by3 == ''))
{
$by3 = '';
}
if ((!isset($planet_id)) || ($planet_id == ''))
{
$planet_id = '-1';
}
if ((!isset($dig_id)) || ($dig_id == ''))
{
$dig_id = '-1';
}
if ((!isset($dismiss)) || ($dismiss == ''))
{
$dismiss = '';
}

$smarty->assign("command", $command);
$smarty->assign("color_header", $color_header);
$smarty->assign("color_line1", $color_line1);
$smarty->assign("color_line2", $color_line2);

switch ($command)
{
case "dismiss":   //CHANGING your dig settings on enemy planet

	$dismisstotal = 0;
	for($i = 0; $i <$digcount; $i++){
		if(isset($dismiss[$i])){
			$debug_query = $db->Execute("UPDATE $dbtables[dignitary] SET job_id='0', percent='0.0' WHERE dig_id=$dismiss[$i] ");
			db_op_result($debug_query,__LINE__,__FILE__);
			$dismisstotal++;
		}
	}
	$smarty->assign("dismisstotal", $dismisstotal);
	$smarty->assign("l_dig_dismiss2", $l_dig_dismiss2);
	$smarty->assign("l_dig_menu", $l_dig_menu);
	$smarty->assign("l_clickme", $l_clickme);

break;

default:	// SHOWING a summary table of all dignitary

  if($by2 == 'planet')		  $by22 = "$dbtables[planets].name asc, $dbtables[planets].sector_id asc, dig_id asc";
  elseif($by2 == 'id')			$by22 = "dig_id asc";
  elseif($by2 == 'job_id')	  $by22 = "job_id desc, percent desc, dig_id asc";
  else						  $by22 = "$dbtables[planets].sector_id asc, $dbtables[planets].name asc, dig_id asc";

	$res = $db->Execute("SELECT * FROM $dbtables[dignitary] WHERE $dbtables[dignitary].owner_id=$playerinfo[player_id] ");
	$smarty->assign("totaldigs", $res->RecordCount());
	if($res->RecordCount())
	{
		$line_color = $color_line2;
		$res = $db->Execute("SELECT $dbtables[dignitary].*, $dbtables[planets].name, $dbtables[planets].sector_id, $dbtables[players].character_name FROM $dbtables[dignitary] INNER JOIN $dbtables[planets] ON $dbtables[dignitary].planet_id=$dbtables[planets].planet_id LEFT JOIN $dbtables[players] ON $dbtables[players].player_id=$dbtables[planets].owner WHERE $dbtables[dignitary].owner_id=$playerinfo[player_id] AND $dbtables[dignitary].owner_id=$dbtables[planets].owner ORDER BY $by22 ");
		$smarty->assign("totaldigsbyplanet", $res->RecordCount());
		if($res->RecordCount())
		{
			$smarty->assign("l_dig_defaulttitle2", $l_dig_defaulttitle2);
			$smarty->assign("l_dig_codenumber", $l_dig_codenumber);
			$smarty->assign("l_dig_planetname", $l_dig_planetname);
			$smarty->assign("l_dig_sector", $l_dig_sector);
			$smarty->assign("l_dig_job", $l_dig_job);
			$smarty->assign("l_dig_dismiss", $l_dig_dismiss);

			$digcount = 0;
			while(!$res->EOF)
			{
				$dig = $res->fields;

				if($dig['job_id']==0)
					$job="$l_dig_jobs[0]";
				else
				{
					$temp = $dig['job_id'];
					$job = "<a href=dig.php?command=change&dig_id=$dig[dig_id]>$l_dig_jobs[$temp]</a>";
				}

				if(empty($dig['name']))
					$dig['name'] = $l_unnamed;

				$digid[$digcount] = $dig['dig_id'];
				$digname[$digcount] = $dig['name'];
				$digsector[$digcount] = $dig['sector_id'];
				$digjob[$digcount] = $job;
				$digcount++;
				$res->MoveNext();
			}

			$smarty->assign("l_dig_changebutton", $l_dig_changebutton);
			$smarty->assign("digcount", $digcount);
			$smarty->assign("digid", $digid);
			$smarty->assign("digname", $digname);
			$smarty->assign("digsector", $digsector);
			$smarty->assign("digjob", $digjob);
		}
		else
		{
			$smarty->assign("l_dig_no2", $l_dig_no2);
		}

		$res = $db->Execute("SELECT COUNT(dig_id) AS as_dig_id FROM $dbtables[dignitary] WHERE active='N' AND owner_id=$playerinfo[player_id] AND ship_id=$shipinfo[ship_id] AND planet_id='0'");
		$smarty->assign("digonship", $res->RecordCount());

		if ($res->RecordCount()) {
			$dig = $res->fields;
			$smarty->assign("l_dig_defaulttitle4", $l_dig_defaulttitle4);
			$smarty->assign("digshiptotal", $dig['as_dig_id']);
		} else { 
			$smarty->assign("l_dig_no4", $l_dig_no4);
		}
	}
	else
	{
		$smarty->assign("l_dig_nodignitaryatall", $l_dig_nodignitaryatall);
	}

break;

}   //swich

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."dig.tpl");

include("footer.php");
?>
