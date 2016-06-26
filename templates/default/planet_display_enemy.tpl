<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>{if $planetowner != 3}
			{$l_planet_scn} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$l_planet_att}<p></p>
		{/if}
		
		{if $novaavailible == 1}
			<a href="planet_unowned_nova.php?planet_id={$planet_id}">{$l_planet_firenova}</a><BR>
		{/if}

		{if $sofaavailible == 1} 
			<a href="planet_unowned_sofa.php?planet_id={$planet_id}">{$l_sofa}</a><BR>
		{/if}
		
		{if $spy_success_factor == 1}
			{if $numspies != 0}
				<BR><table border=1 cellspacing=1 cellpadding=2 width="100%">
				<TR BGCOLOR="{$color_header}"><TD colspan=99 align=center><font color=white><B>{$l_spy_yourspies} </font> ({$numspies})
				{if $addaspy == 1}
					<a href="spy.php?command=send&planet_id={$planet_id}">{$l_spy_sendnew}</a>
				{/if}
				</B></TD></TR>
				<TR BGCOLOR="{$color_line2}">
				<TD><B><A HREF="planet.php?planet_id={$planet_id}">{$ID}</A></B></TD>
				<TD><B><A HREF="planet.php?planet_id={$planet_id}&by=job_id">{$l_spy_job}</A></B></TD>
				<TD><B><A HREF="planet.php?planet_id={$planet_id}&by=percent">{$l_spy_percent}</A></B></TD>
				<TD><B><A HREF="planet.php?planet_id={$planet_id}&by=move_type">{$l_spy_move}</A></B></TD>
				<TD><font color=white><B>{$l_spy_action}</B></font></TD>
				</TR>
		
				{php}
				for($i = 0; $i < $numspies; $i++){
					echo "<TR BGCOLOR=" . $color[$i] ."><TD><font size=2 color=white>$spyid[$i]</font></TD><TD><font size=2 color=white>$job[$i]</font></TD><TD><font size=2 color=white>$spypercent[$i]</font></TD><TD><font size=2><a href=spy.php?command=change&spy_id=$spyid[$i]&planet_id=$planet_id>$spymove[$i]</a></font></TD><TD><font size=2><a href=spy.php?command=comeback&spy_id=$spyid[$i]&planet_id=$planet_id>$l_spy_comeback</a></font></TD></TR>";
				}
				{/php}
				</TABLE><BR>

				<BR><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>
				<TR BGCOLOR="{$color_header}"><TD></TD><TD><B>{$l_base}</B></TD><TD><B>{$l_planetary_computer}</B></TD><TD><B>{$l_planetary_sensors}</B></TD><TD><B>{$l_planetary_beams}</B></TD><TD><B>{$l_planetary_torp_launch}</B></TD><TD><B>{$l_planetary_shields}</B></TD><TD><B>{$l_planetary_jammer}</B></TD>
				<TD><B>{$l_planetary_cloak}</B></TD></TR>
				<TR BGCOLOR="{$color_line2}"><TD>{$l_planetary_defense_levels}&nbsp;</TD>
				<TD>{$planetbased}</TD>
				<TD>{$planetcomputer}</TD>
				<TD>{$planetsensors}</TD>
				<TD>{$planetbeams}</TD>
				<TD>{$planetlaunchers}</TD>
				<TD>{$planetshields}</TD>
				<TD>{$planetjammer}</TD>	  
				<TD>{$planetcloak}</TD>
				</TR>
				</TABLE><BR><BR>
				<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=2>
				<TR BGCOLOR="{$color_header}"><TD></TD><TD><B>{$l_ore}</B></TD><TD><B>{$l_organics}</B></TD><TD><B>{$l_goods}</B></TD><TD><B>{$l_energy}</B></TD><TD><B>{$l_colonists}</B></TD><TD><B>{$l_credits}</B></TD><TD><B>{$l_fighters}</B></TD><TD><B>{$l_torps}</B></TD>
				</TR><TR BGCOLOR="{$color_line1}">
				<TD>{$l_current_qty}</TD>
				<TD>{$planetore}</TD>
				<TD>{$planetorganics}</TD>
				<TD>{$planetgoods}</TD>
				<TD>{$planetenergy}</TD>
				<TD>{$planetcolonists}</TD>
				<TD>{$planetcredits}</TD>
				<TD>{$planetfighters}</TD>
				<TD>{$planettorps}</TD>
				</TR>
				<TR BGCOLOR="{$color_line2}"><TD>{$l_planet_perc}</TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodore}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodorganics}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodgoods}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodenergy}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				<TD>{$na}</TD><TD>*</TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodfighters}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				<TD><INPUT TYPE=TEXT VALUE="{$prodtorp}" SIZE=3 MAXLENGTH=3 DISABLED></TD>
				</TABLE>{$l_planet_interest}<BR><BR>
			{else}
				{if $planetowner != 3}
					{$l_spy_nospieshere}. 
					<a href="spy.php?command=send&planet_id={$planet_id}">{$l_spy_sendnew}</a><BR>
				{/if}
			{/if}  
		{/if}  

		<BR><a href="planet.php?planet_id={$planet_id}">{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>

		{if $allow_ibank == 1}
			{$l_ifyouneedplan} <A HREF="igb.php?planet_id={$planet_id}">{$l_igb_term}</A>.<BR><BR>
		{/if}

		<A HREF ="bounty.php">{$l_by_placebounty}</A><p>
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
