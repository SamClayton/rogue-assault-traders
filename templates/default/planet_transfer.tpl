<H1>{$title}</H1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>
	{if $spy_success_factor == 1 and $spytotal != 0}
		{$spytotal} {$l_spy_transferred}.<BR>
	{/if}
	{if $dig_success_factor == 1 and $digtotal != 0}
		{$digtotal} {$l_dig_transferred}.<BR>
	{/if}

	{if $transfer_ore1 != 0}
		{$l_planet2_noten} {$l_ore}. {$l_planet2_settr} {$transfer_ore1} {$l_units} {$l_ore}.<BR>
	{/if}
	{if $transfer_organics1 != 0}
		{$l_planet2_noten} {$l_organics}. {$l_planet2_settr} {$transfer_organics1} {$l_units}.<BR>
	{/if}
	{if $transfer_goods1 != 0}
		{$l_planet2_noten} {$l_goods}. {$l_planet2_settr} {$transfer_goods1} {$l_units}.<BR>
	{/if}
	{if $transfer_energy1 != 0}
		{$l_planet2_noten} {$l_energy}. {$l_planet2_settr} {$transfer_energy1} {$l_units}.<BR>
	{/if}
	{if $transfer_colonists1 != 0}
		{$l_planet2_noten} {$l_colonists}. {$l_planet2_settr} {$transfer_colonists1} {$l_colonists}.<BR>
	{/if}
	{if $transfer_credits1 != 0}
		{$l_planet2_noten} {$l_credits}. {$l_planet2_settr} {$transfer_credits1} {$l_credits}.<BR>
	{/if}
	{if $transfer_credits1a != 0}
		{$l_planet2_baseexceeded} {$l_planet2_settr} {$transfer_credits1a} {$l_credits}.<BR>
	{/if}
	{if $transfer_torps1 != 0}
		{$l_planet2_noten} {$l_torps}. {$l_planet2_settr} {$transfer_torps1} {$l_torps}.<BR>
	{/if}
	{if $transfer_fighters1 != 0}
		{$l_planet2_noten} {$l_fighters}. {$l_planet2_settr} {$transfer_fighters1} {$l_fighters}.<BR>
	{/if}
	
	{if $transfer_ore2 != 0}
		{$l_planet2_losup} {$transfer_ore2} {$l_units} {$l_ore}.<BR>
	{/if}
	{if $transfer_organics2 != 0}
		{$l_planet2_losup} {$transfer_organics2} {$l_units} {$l_organics}.<BR>
	{/if}
	{if $transfer_goods2 != 0}
		{$l_planet2_losup} {$transfer_goods2} {$l_units} {$l_goods}.<BR>
	{/if}
	{if $transfer_energy2 != 0}
		{$l_planet2_losup} {$transfer_energy2} {$l_units} {$l_energy}.<BR>
	{/if}
	{if $transfer_colonists2 != 0}
		{$l_planet2_losup} {$transfer_colonists2} {$l_colonists}.<BR>
	{/if}
	{if $transfer_credits2 != 0}
		{$l_planet2_losup} {$transfer_credits2} {$l_credits}.<BR>
	{/if}
	{if $transfer_torps2 != 0}
		{$l_planet2_losup} {$transfer_torps2} {$l_torps}.<BR>
	{/if}
	{if $transfer_fighters2 != 0}
		{$l_planet2_losup} {$transfer_fighters2} {$l_fighters}.<BR>
	{/if}

	{if $transfer_ore3 != 0}
		{$l_planet2_settr} {$transfer_ore3} {$l_ore}.<BR>
	{/if}
	{if $transfer_organics3 != 0}
		{$l_planet2_settr} {$transfer_organics3} {$l_organics}.<BR>
	{/if}
	{if $transfer_goods3 != 0}
		{$l_planet2_settr} {$transfer_goods3} {$l_goods}.<BR>
	{/if}
	{if $transfer_colonists3 != 0}
		{$l_planet2_settr} {$transfer_colonists3} {$l_colonists}.<BR>
	{/if}
	{if $transfer_energy3 != 0}
		{$l_planet2_settr} {$transfer_energy3} {$l_energy}.<BR>
	{/if}
	{if $transfer_torps3 != 0}
		{$l_planet2_settr} {$transfer_torps3} {$l_torps}.<BR>
	{/if}
	{if $transfer_fighters3 != 0}
		{$l_planet2_settr} {$transfer_fighters3} {$l_fighters}.<BR>
	{/if}


	{if $mineteam == 1}
		{if $transfer_ore4 == 1}
			{$l_planet2_noten} {$l_ore} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_organics4 == 1}
			{$l_planet2_noten} {$l_organics} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_goods4 == 1}
			{$l_planet2_noten} {$l_goods} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_energy4 == 1}
			{$l_planet2_noten} {$l_energy} {$l_planet2_fortr}<BR>
		{/if}
		
		{if $transfer_energy4 == 2}
			{$l_planet2_noten} {$l_planet2_power} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_colonists4 == 1}
			{$l_planet2_noten} {$l_colonists} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_fighters4 == 1}
			{$l_planet2_noten} {$l_fighters} {$l_planet2_fortr}<BR>
		{/if}
		
		{if $transfer_fighters4 == 2}
			{$l_planet2_noten} {$l_planet2_comp} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_torps4 == 1}
			{$l_planet2_noten} {$l_torpedoes} {$l_planet2_fortr}<BR>
		{/if}
		
		{if $transfer_torps4 == 2}
			{$l_planet2_noten} {$l_planet2_laun} {$l_planet2_fortr}<BR>
		{/if}

		{if $transfer_credits == 1}
			{$l_planet2_noten} {$l_credits} {$l_planet2_fortr}<BR>
		{/if}

		{$l_planet2_compl}<BR><a href=planet.php?planet_id={$planet_id}>{$l_clickme}</a> {$l_toplanetmenu}<BR><BR>
	{else}
		{$l_planet2_notowner}
	{/if}
</td></tr>

<tr><td><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>

</table>
