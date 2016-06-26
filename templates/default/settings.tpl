<center>
<h1>{$title}</h1>
<table width="650" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$version}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$release_version}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_time_since_reset}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$totaltime}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_allowpl}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_allowplresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_allownewpl}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_allownewplresponse}</font></td>
  </tr>
		</table>
	</td>
 
  </tr>
  
</table>


<h1>{$title2}</h1>
<table width="650" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_allowteamplcreds}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_allowteamplcredsresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_allowfullscan}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_allowfullscanresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_sofa}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_sofaresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_showpassword}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_showpasswordresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_genesisdestroy}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_genesisdestroyresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_igb}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_igbresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_ksm}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_ksmresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_navcomp}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_navcompresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_newbienice}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_newbieniceresponse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spies}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_spiesresponse}</font></td>
  </tr>
  {if $spy_success_factor}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spycapture}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$l_s_spycaptureresponse}</font></td>
  </tr>
  {/if}
		</table>
	</td>
    
  </tr>

</table>


<h1>{$title3}</h1>
<table width="650" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
    
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_gameversion}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$game_name}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_averagetechewd}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$ewd_maxavgtechlevel}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_numsectors}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sector_max}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_maxwarpspersector}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$link_max}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_averagetechfed}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$fed_max_avg_tech}</font></td>
  </tr>
  {if $allow_ibank}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_igbirateperupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$bankinterest}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_igblrateperupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$loaninterest}</font></td>
  </tr>
  {/if}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_techupgradebase}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$basedefense}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_collimit}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$colonist_limit}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_maxturns}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$max_turns}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_maxplanetssector}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$max_planets_sector}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_maxtraderoutes}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$max_traderoutes_player}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colreprodrate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$colonist_reproduction_rate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_energyperfighter}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$energy_per_fighter}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_secfighterdegrade}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$defence_degrade_rate}</font></td>
  </tr>
  {if $spy_success_factor}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spiesperplanet}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$max_spies_per_planet}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spysuccessfactor}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$spy_success_factor2}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spykillfactor}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$spy_kill_factor}</font></td>
  </tr>
  {/if}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colsperfighter}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$fighter_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colspertorp}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$torpedo_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colsperore}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$ore_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colsperorganics}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$organics_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colspergoods}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$goods_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colsperenergy}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$energy_prate}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_colspercreds}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$credits_prate}</font></td>
  </tr>
		</table>
	</td>
   
  </tr>

</table>


<h1>{$title4}</h1>
<table width="650" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_ticksupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_ticks}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_turnsupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$updateticks}</font></td>
  </tr>

  {if $allow_ibank}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_igbturnsupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_igb}</font></td>
  </tr>
  {/if}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_newsupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_news}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_planetupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_planets}</font></td>
  </tr>
  {if $spy_success_factor}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_spyupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_spies}</font></td>
  </tr>
  {/if}
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_portsupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_ports}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_towupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_tow}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_scoreupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_ranking}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_secdefdegrupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_degrade}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_apocalypseupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_apocalypse}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_independence}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_independance}</font></td>
  </tr>
  <tr bgcolor="#000000">
	<td width="450"><font size="2" color="#FF0000">{$l_s_dignitaryupdate}</font></td>
	<td align="right" width="200"><font size="2" color="#00ff00">{$sched_dig}</font></td>
  </tr>

<tr><td><br><br>{$l_global_mlogin}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>
