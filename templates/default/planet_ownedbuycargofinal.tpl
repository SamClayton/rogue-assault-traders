<H1>{$title}</H1>
	<TABLE width="80%" border="0" cellspacing="0" cellpadding="0" align="center">

		<TR>
			
			<TD bgcolor="#000000" valign="top" align="center" colspan="2">
				<TABLE cellspacing="0" cellpadding="0" border="0" width="100%">
					<TR>
						<TD>	{if $isowner == 1}
				{if $nomoney == 1}
					{$l_planet_nocredits1} {$cargoshipcost} {$l_planet_nocredits2} {$playercredits} {$l_credits}.
				{else}
							<TABLE cellspacing="0" cellpadding="0" border="0" width="100%">
								<TR>
									<TD><IMG src="templates/{$templatename}images/spacer.gif" width="10" height="1">
									</TD>
									<TD><TABLE cellspacing="0" cellpadding="0" border="0" width="99%">
											<TR>
												<TD colspan="2"><IMG src="templates/{$templatename}images/spacer.gif" width="1" height="10">
												</TD>
											</TR>
											<TR>
												<TD colspan="99" align="center" bgcolor="#300030"><FONT size="3" color="white"><B>{$l_trade_result}</B></FONT>
												</TD>
											</TR>
											<TR>
												<TD colspan="99" align="center"><B><FONT color="red">{$l_cost}: {$trade_credits} {$l_credits}</FONT></B>
												</TD>
											</TR>	{if $tempvar != 0}
											<TR>
												<TD colspan="99" align="center"><B>{$l_hull} {$l_trade_upgraded} {$cargoshiphull}</B>
												</TD>
											</TR>	{/if}
					{if $tempvar2 != 0}
											<TR>
												<TD colspan="99" align="center"><B>{$l_power} {$l_trade_upgraded} {$cargoshippower}</B>
												</TD>
											</TR>	{/if}
										</TABLE>	{/if}
										</td></tr></table>
			{/if}
			
									</TD>
									<TR>
										<TR>
											<TD colspan="2"><BR><A href='planet.php?planet_id={$planet_id}'>{$l_clickme}</A> {$l_toplanetmenu}<BR><BR>{if $allow_ibank}
		{$l_ifyouneedplan} <A href="igb.php?planet_id={$planet_id}">{$l_igb_term}</A>.<BR><BR>{/if}

	<A href="bounty.php">{$l_by_placebounty}</A>
<P>
											</TD>
										</TR>
										<TR>
											<TD><BR><BR>{$gotomain}<BR><BR>
											</TD>
										</TR>
							</TABLE>
						</TD>
							&nbsp;
						</TD>
					</TR>
				
				</TABLE>