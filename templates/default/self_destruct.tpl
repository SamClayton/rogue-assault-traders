<H1>{$title}</H1>

<table  cellspacing = "0" cellpadding = "0" border = "0" align="center" width="400">
	
	<tr>
		<td colspan=4>
			<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
				<tr>
					<td>	
						<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
							<tr bgcolor="black">
						
								<td valign="top">
									<table cellspacing = "0" cellpadding = "0" border = "0" width="100%">
<tr><td>

{if $sure == 0}
	<FONT COLOR=RED><B>{$l_die_rusure}</B></FONT><BR><BR>
	<A HREF=main.php>{$l_die_nonono}</A> {$l_die_what}<BR><BR>
	<A HREF=self-destruct.php?sure=1>{$l_yes}!</A> {$l_die_goodbye}<BR><BR>
{/if}

{if $sure == 1}
	<FONT COLOR=RED><B>{$l_die_check}</B></FONT><BR><BR>
	<A HREF=main.php>{$l_die_nonono}</A> {$l_die_what}<BR><BR>
	<A HREF=self-destruct.php?sure=2>{$l_yes}!</A> {$l_die_goodbye}<BR><BR>
{/if}

{if $sure == 2}
	{$l_die_count}<BR>
	{$l_die_vapor}<BR><BR>
	{$l_die_please2}<BR>
{/if}

</td></tr>
										<tr><td width="100%" colspan=3><br><br>{$gotomain}<br><br></td></tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					
				</tr>
			</table>
		</td>
	</tr>
	
</table>