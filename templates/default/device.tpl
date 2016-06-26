<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
   
    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
  <tr bgcolor="{$color_header}">
	<td colspan="3" align="center">{$l_device_expl}<br><br></td>
  </tr>
  <tr bgcolor="{$color_header}">
	<td><b>{$l_device}</b></td>
	<td><b>{$l_qty}</b></td>
	<td><b>{$l_usage}</b></td>
  </tr>
	
<!-- beacon section -->
  <tr bgcolor="{$color_line1}">
	<td><a href=beacon.php>{$l_beacons}</a></td>
	<td>{$dev_beacon}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- beacon section end -->
<!-- warpedit section -->
  <tr bgcolor="{$color_line2}">
	<td><a href=probemenu.php?command=drop>{$l_probe}</a></td>
	<td>{$ship_probe}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- warpedit section end -->
<!-- warpedit section -->
  <tr bgcolor="{$color_line1}">
	<td><a href=warpedit.php>{$l_warpedit}</a></td>
	<td>{$dev_warpedit}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- warpedit section end -->
<!-- sectorgenesis section -->
  <tr bgcolor="{$color_line2}">
	<td><a href=sectorgenesis.php>{$l_sectorgenesis}</a></td>
	<td>{$dev_sectorgenesis}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- genesis section end -->
<!-- genesis section -->
  <tr bgcolor="{$color_line1}">
	<td><a href=genesis.php>{$l_genesis}</a></td>
	<td>{$dev_genesis}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- genesis section end -->

<!-- mine deflector section -->
  <tr bgcolor="{$color_line2}">
	<td>{$l_deflect}</td>
	<td>{$dev_minedeflector}</td>
	<td>{$l_automatic}</td>
  </tr>
<!-- mine deflector section end -->

<!-- mines/torpedoes section -->
  <tr bgcolor="{$color_line1}">
	<td><a href=mines.php>{$l_mines}/ {$l_fighters}</a></td>
	<td>{$dev_torps} / {$dev_fighters}</td>
	<td>{$l_manual}</td>
  </tr>
<!-- mines/torpedoes section end -->

<!-- emergency warp section -->
  <tr bgcolor="{$color_line1}">
	<td><a href=emerwarp.php>{$l_ewd}</a></td>
	<td>{$dev_emerwarp}</td>
	<td>{$l_manual}/{$l_automatic}</td>
  </tr>
<!-- emergency warp section end -->

<!-- escapepod section -->
  <tr bgcolor="{$color_line2}">
	<td>{$l_escape_pod}</td>
	<td>
	 {if $dev_escapepod == 'Y'}
	 {$l_yes}
	 {else}
	 {$l_no}
	 {/if}
	</td>
	<td>{$l_automatic}</td>
  </tr>
<!-- escapepod section end -->

<!-- fuelscoop section end -->
  <tr bgcolor="{$color_line1}">
	<td>{$l_fuel_scoop}</td>
	<td>
	 {if $dev_fuelscoop == 'Y'}
	 {$l_yes} 
	 {else}
	 {$l_no}
	 {/if}
	</td>
	<td>{$l_automatic}</td>
  </tr>
<!-- fuelscoop section end -->

<!-- nova section -->
  <tr bgcolor="{$color_line2}">
	<td>{$l_nova}</td>
	<td>
	 {if $dev_nova == 'Y'}
	 {$l_yes} 
	 {else}
	 {$l_no}
	 {/if}
	</td>
	<td>{$l_manual}</td>
  </tr>
<!-- nova section end -->
										<tr><td colspan="3"><br><br>{$gotomain}<br><br></td></tr>
		</table>
	</td>
    
  </tr>
  
</table>
