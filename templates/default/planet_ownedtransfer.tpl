<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">

	<FORM ACTION='planet_transfer.php?planet_id={$planet_id}' METHOD=POST>

	<TR BGCOLOR="{$color_header}"><TD><B>{$l_commodity}</B></TD><TD><B>{$l_planet}</B></TD><TD><B>{$l_ship}</B></TD><TD><B>{$l_planet_transfer_link}</B></TD><TD><B>{$l_planet_toplanet}</B></TD><TD><B>{$l_all}?</B></TD></TR>
	<TR BGCOLOR="{$color_line1}"><TD>{$l_ore}</TD><TD>{$planetore}</TD><TD>{$shipore}</TD><TD><INPUT TYPE=TEXT NAME=transfer_ore SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpore VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allore VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line2}"><TD>{$l_organics}</TD><TD>{$planetorganics}</TD><TD>{$shiporganics}</TD><TD><INPUT TYPE=TEXT NAME=transfer_organics SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tporganics VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allorganics VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line1}"><TD>{$l_goods}</TD><TD>{$planetgoods}</TD><TD>{$shipgoods}</TD><TD><INPUT TYPE=TEXT NAME=transfer_goods SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpgoods VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allgoods VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line2}"><TD>{$l_energy}</TD><TD>{$planetenergy}</TD><TD>{$shipenergy}</TD><TD><INPUT TYPE=TEXT NAME=transfer_energy SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpenergy VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allenergy VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line1}"><TD>{$l_colonists}</TD><TD>{$planetcolonists}</TD><TD>{$shipcolonists}</TD><TD><INPUT TYPE=TEXT NAME=transfer_colonists SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcolonists VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcolonists VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line2}"><TD>{$l_fighters}</TD><TD>{$planetfighters}</TD><TD>{$shipfighters}</TD><TD><INPUT TYPE=TEXT NAME=transfer_fighters SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpfighters VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allfighters VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line1}"><TD>{$l_torps}</TD><TD>{$planettorps}</TD><TD>{$shiptorps}</TD><TD><INPUT TYPE=TEXT NAME=transfer_torps SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tptorps VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=alltorps VALUE=-1></TD></TR>
	<TR BGCOLOR="{$color_line2}"><TD>{$l_credits} {$planetmaxcredit} {$l_max}</TD><TD>{$planetcredits}</TD><TD>{$playercredits}</TD><TD><INPUT TYPE=TEXT NAME=transfer_credits SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpcredits VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allcredits VALUE=-1></TD></TR>

	{if $spytransfer == 1}
		<TR BGCOLOR="{$color_line1}"><TD>{$l_spy}</TD><TD>{$n_pl}</TD><TD>{$n_sh}</TD><TD><INPUT TYPE=TEXT NAME=transfer_spies SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpspies VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=allspies VALUE=-1></TD></TR>
	{/if}
	{if $digtransfer == 1}
		<TR BGCOLOR="{$color_line1}"><TD>{$l_dig}</TD><TD>{$n_pld}</TD><TD>{$n_shd}</TD><TD><INPUT TYPE=TEXT NAME=transfer_dignitary SIZE=10 MAXLENGTH=20></TD><TD><INPUT TYPE=CHECKBOX NAME=tpdigs VALUE=-1></TD><TD><INPUT TYPE=CHECKBOX NAME=alldigs VALUE=-1></TD></TR>
	{/if}

</tr><td colspan=6>
								
								<BR>
	<INPUT TYPE=SUBMIT VALUE={$l_planet_transfer_link}>&nbsp;<INPUT TYPE=RESET VALUE={$l_reset}>
	</FORM>	

	<BR><br>{$l_planet_cinfo}<BR>
	<BR><a href='planet.php?planet_id={$planet_id}'>{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>
	{if $allow_ibank}
		{$l_ifyouneedplan} <A HREF="igb.php?planet_id={$planet_id}">{$l_igb_term}</A>.<BR><BR>
	{/if}

	<A HREF ="bounty.php">{$l_by_placebounty}</A><p>

</td></tr>

<tr><td colspan=6><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
 
</table>

