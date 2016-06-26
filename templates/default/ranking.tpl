<H1>{$title}</H1>
<table width="195" border="0" cellspacing="0" cellpadding="0" align="center">
  
  <tr>

    <td bgcolor="#000000" valign="top" align="center" colspan=2>
		<table cellspacing = "0" cellpadding = "0" border = "0">
			<TR align="center">
				<TD NOWRAP>
{if $multiplepages != 0}
	<TABLE border=0 cellpadding=2 cellspacing=1 width=725>
	<form action="ranking.php" method="post">
	<TR><TD align='right'>
	{$l_ranks_select}:
	</td><td align='left'><select name="page">
	{php}
	for($i = 0; $i <= $multiplepages; $i++){
		if(($i * $max_rank) == ($page * $max_rank))
			$selected = "selected";
		else $selected = "";
		
		echo "<option value=\"". $i."\"$selected> $l_ranks_page ".  $i ."</option>\n";
	}
	{/php}
	<option value="-1" {$allselected}>{$l_all}</option>
	</select>
	&nbsp;<input type="submit" value="{$l_submit}">
	<input type="hidden" name="sort" value="{$sort}">
	</TD></tr>
	</form>
	</table>
{/if}

{php}
if($page != 0)
	$prevlink = "<a href=\"ranking.php?page=".($page - 1) ."&sort={$sort}\">$l_ranks_prev</a>";
else $prevlink = "&nbsp;";

if(($page + 1) * $max_rank < $num_players )
	$nextlink = "<a href=\"ranking.php?page=".($page + 1) ."&sort={$sort}\">$l_ranks_next</a>";
else $nextlink = "&nbsp;";

echo "<TABLE border=0 cellpadding=2 cellspacing=1 width=935>\n";
echo "<TR><TD align='left'>$prevlink</td>\n";
echo "<TD align='right'>$nextlink</td></tr>\n";
echo "</table>";
{/php}

{if !$res}
	{$l_ranks_none}<br>
{else}
	<br>{$l_ranks_pnum}: {$num_players}
	<br>{$l_ranks_show} {$rankfrom} {$l_ranks_to} {$rankto}
	<br>{$l_ranks_dships}

	<br><br>

	<table border=0 cellspacing=0 cellpadding=4>
		<tr bgcolor="{$color_header}">
		<td align=center><b>{$l_ranks_standing}</b></td>
		<td align=center><b><a href="ranking.php?page={$page}">{$l_score}</a></b></td>
<td align=center><b>{$l_ranks_rank}</b></td>
		<td colspan=2 align=center><b><a href="ranking.php?sort=name&page={$page}">{$l_player}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=login&page={$page}">{$l_ranks_online}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=team&page={$page}">{$l_team}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=kills&page={$page}">{$l_ranks_kills}</a>/<a href="ranking.php?sort=deaths&page={$page}">{$l_ranks_deaths}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=captures&page={$page}">{$l_ranks_captures}</a></b></td>		
		<td align=center><b><a href="ranking.php?sort=lost&page={$page}">{$l_ranks_lost}</a></b></td>		
		<td align=center><b><a href="ranking.php?sort=built&page={$page}">{$l_ranks_built}</a></b></td>		
		<td align=center><b><a href="ranking.php?sort=good&page={$page}">{$l_ranks_good}</a>/<a href="ranking.php?sort=bad&page={$page}">{$l_ranks_evil}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=experience&page={$page}">{$l_ranks_experience}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=efficiency&page={$page}">{$l_ranks_rating}</a></b></td>
<td align=center><b><a href="ranking.php?sort=turns&page={$page}">{$l_turns_used}</a></b></td>
		<td align=center><b><a href="ranking.php?sort=login&page={$page}">{$l_ranks_lastlog}</a></b></td>
		</tr>

{php}
		for($i = 0; $i < $rankcount; $i++){
			if($username == $email[$i])
				$newbgcolor = "#454560";
			else $newbgcolor = $color;

			echo "  <tr bgcolor=\"$newbgcolor\">\n";
			echo "	<td align=center>" . $ranknumber[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankscore[$i] . "</td>\n";
			echo "	<td nowrap align=center><img border=\"0\" src=\"templates/$templatename/images/rank/" . $rankimage[$i] . "\" align=\"absmiddle\"></td>";
			echo "  <td align=center valign=middle width=32 height=32><img src='images/$publicavatar[$i]' width=32 height=32 border=1></td>";
			echo "	<td>";
			if($rankprofileid[$i] != 0){
				echo "<a href=\"http://profiles.aatraders.com?player_id=" . $rankprofileid[$i] . "\" target=\"_blank\"><b>" . $rankname[$i] . "</b></a>";
			}else{
				echo "<b>" . $rankname[$i] . "</b>";
			}
			echo "</td>\n";
			echo "	<td align=center>" . $rankonline[$i] . "</td>\n";
			echo "	<td align=center>" . $rankteam[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankkills[$i] . "/" . $rankdeaths[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankcaptures[$i] . "</td>\n";
			echo "	<td align='center'>" . $ranklost[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankbuilt[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankrating[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankexperience[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankeff[$i] . "</td>\n";
			echo "	<td align='center'>" . $rankturns[$i] . "</td>\n";
			echo "	<td>" . $ranklastlogin[$i] . "</td>\n";

			echo"</tr>\n";

			if ($color == $color_line1)
			{
				$color = $color_line2;
			}
			else
			{
				$color = $color_line1;
			}
		}
{/php}
	</table>
{/if}
</td></tr>
										<tr><td width="100%" colspan=3><br><br>{$gotomain}<br><br></td></tr>
				</td>
			</tr>
		</table>
	</td>
   
  </tr>

</table>

