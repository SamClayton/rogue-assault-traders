<?
include("config/config.php");

include("languages/$langdir/lang_autotrade.inc");
include("languages/$langdir/lang_planets.inc");

$title=$l_autotrade_title;

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

if ((!isset($dismiss)) || ($dismiss == ''))
{
$dismiss = '';
}

$line_color = $color_line2;
function linecolor()
{
  global $line_color, $color_line1, $color_line2;

  if($line_color == $color_line1)   
   $line_color = $color_line2; 
  else   
   $line_color = $color_line1; 

  return $line_color;
}


switch ($command)
{

case "dismiss":

	$dismisstotal = 0;
	for($i = 0; $i <$tradecount; $i++){
		if(isset($dismiss[$i])){
			$debug_query = $db->Execute("delete from $dbtables[autotrades] WHERE traderoute_id=$dismiss[$i] ");
			db_op_result($debug_query,__LINE__,__FILE__);
			$dismisstotal++;
		}
	}
	$smarty->assign("error_msg", "$dismisstotal $l_autotrade_dismiss2");
	$smarty->assign("gotomain", $l_global_mmenu);
	$smarty->display($templatename."autotradedie.tpl");
	include ("footer.php");
	die();

break;

	default:

		$res = $db->Execute("SELECT * FROM $dbtables[autotrades] WHERE owner=$playerinfo[player_id] ");
		if($res->RecordCount())
		{
			$smarty->assign("color_header", $color_header);
			$smarty->assign("color_line2", $color_line2);
			$smarty->assign("color_line1", $color_line1);
			$smarty->assign("l_autotrade_report", $l_autotrade_report);
			$smarty->assign("l_autotrade_planet", $l_autotrade_planet);
			$smarty->assign("l_autotrade_hull", $l_autotrade_hull);
			$smarty->assign("l_autotrade_capacity", $l_autotrade_capacity);
			$smarty->assign("l_autotrade_energy", $l_autotrade_energy);
			$smarty->assign("l_autotrade_goods", $l_autotrade_goods);
			$smarty->assign("l_autotrade_ore", $l_autotrade_ore);
			$smarty->assign("l_autotrade_organics", $l_autotrade_organics);
			$smarty->assign("l_autotrade_energy", $l_autotrade_energy);
			$smarty->assign("l_autotrade_credits", $l_autotrade_credits);
			$smarty->assign("l_autotrade_delete", $l_autotrade_delete);

			$tradecount = 0;
			while(!$res->EOF)
			{
				$trade = $res->fields;
				$res2 = $db->Execute("SELECT * FROM $dbtables[planets] WHERE planet_id=$trade[planet_id] ");
				$tradeplanet = $res2->fields;

				if($tradeplanet['name'] == '')
					$tradeplanet['name'] = $l_autotrade_unnamed;

				$color[$tradecount] = linecolor();
				$tradesector[$tradecount] = $tradeplanet['sector_id'];
				$tradename[$tradecount] = $tradeplanet['name'];
				$tradehull[$tradecount] = $tradeplanet['cargo_hull'];
				$tradeholds[$tradecount] = number(NUM_HOLDS($tradeplanet['cargo_hull']));
				$tradepower[$tradecount] = $tradeplanet['cargo_power'];
				$tradeenergy[$tradecount] = number(NUM_ENERGY($tradeplanet['cargo_power']));
				if($trade['port_id_goods'] == 0){
					$tradegoodsprice[$tradecount] = 0;
				}else{
					$tradegoodsprice[$tradecount] = $trade['goods_price'];
					$tradegoodsport[$tradecount] = $trade['port_id_goods'];
				}
				if($trade['port_id_ore'] == 0){
					$tradeoreprice[$tradecount] = 0;
				}else{
					$tradeoreprice[$tradecount] = $trade['ore_price'];
					$tradeoreport[$tradecount] = $trade['port_id_ore'];
				}
				if($trade['port_id_organics'] == 0){
					$tradeorganicsprice[$tradecount] = 0;
				}else{
					$tradeorganicsprice[$tradecount] = $trade['organics_price'];
					$tradeorganicsport[$tradecount] = $trade['port_id_organics'];
				}
				if($trade['port_id_energy'] == 0){
					$tradeenergyprice[$tradecount] = 0;
				}else{
					$tradeenergyprice[$tradecount] = $trade['energy_price'];
					$tradeenergyport[$tradecount] = $trade['port_id_energy'];
				}
				$tradecredits[$tradecount] = number($trade['current_trade']);
				$tradedismiss[$tradecount] = $trade['traderoute_id'];
				$res->MoveNext();
				$tradecount++;
			}
			$smarty->assign("color", $color);
			$smarty->assign("tradesector", $tradesector);
			$smarty->assign("tradename", $tradename);
			$smarty->assign("tradehull", $tradehull);
			$smarty->assign("tradeholds", $tradeholds);
			$smarty->assign("tradepower", $tradepower);
			$smarty->assign("tradeenergy", $tradeenergy);
			$smarty->assign("l_autotrade_noroute", $l_autotrade_noroute);
			$smarty->assign("tradegoodsprice", $tradegoodsprice);
			$smarty->assign("tradeoreprice", $tradeoreprice);
			$smarty->assign("tradeorganicsprice", $tradeorganicsprice);
			$smarty->assign("tradeenergyprice", $tradeenergyprice);
			$smarty->assign("l_autotrade_credit2", $l_autotrade_credit2);
			$smarty->assign("l_autotrade_sector", $l_autotrade_sector);
			$smarty->assign("tradegoodsport", $tradegoodsport);
			$smarty->assign("tradeoreport", $tradeoreport);
			$smarty->assign("tradeorganicsport", $tradeorganicsport);
			$smarty->assign("tradeenergyport", $tradeenergyport);
			$smarty->assign("tradecredits", $tradecredits);
			$smarty->assign("tradedismiss", $tradedismiss);
			$smarty->assign("l_autotrade_deletebutton", $l_autotrade_deletebutton);
			$smarty->assign("tradecount", $tradecount);
		}
		else
		{
			$smarty->assign("error_msg", $l_autotrade_noroute2);
			$smarty->assign("gotomain", $l_global_mmenu);
			$smarty->display($templatename."autotradedie.tpl");
			include ("footer.php");
			die();
		}

break;

}   //swich

$smarty->assign("gotomain", $l_global_mmenu);
$smarty->display($templatename."autotrades.tpl");

include("footer.php");
?>
