<h1>{$title}</h1>

<table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">


			<TR>
				<TD>
{if $newpass1 == "" && $newpass2 == ""}
{$l_opt2_passunchanged}
{elseif $password != $oldpass}
{$l_opt2_srcpassfalse}
{elseif $newpass1 != $newpass2}
{$l_opt2_newpassnomatch}
{elseif ($oldpass == $password) && $debug_query}
{$l_opt2_passchanged}
{else}
{$l_opt2_passchangeerr}
{/if}

{if $allow_shipnamechange == 1}
{$l_opt2_shipnamechanged}
{/if}

{if $l_opt2_chlang != ''}
{$l_opt2_chlang}
{/if}

{if $l_opt2_chtemplate != ''}
{$l_opt2_chtemplate}
{/if}

{$l_opt2_mapwidth} {$map_width}<br><br>
</td></tr>
<tr><td><br><br>{$gotomain}<br><br>				</td>
			</tr>
		</table>


</table>
