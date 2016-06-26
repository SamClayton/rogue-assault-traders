<table border=0 cellspacing=0 cellpadding=5>
  <tr>
    <td>Player name: </td>
    <td><input type="text" name="character_name" value="{$character_name}" size="32" maxlength="25"></td>
  </tr>
  <tr>
    <td>Password: </td>
    <td><input type="text" name="password2" value="{$password}" size="32" maxlength="{$maxlen_password}"></td>
  </tr>
  <tr>
    <td>E-mail: </td>
    <td><input type="text" name="email" value="{$email}"></td>
  </tr>
  <tr>
    <td>ID: </td>
    <td>{$user}</td>
  </tr>
  <tr>
    <td>Ship ID: </td>
    <td><input type="hidden" name="currentship_id" value="{$currentship_id}">{$currentship_id}</td>
  </tr>
  <tr>
    <td>Ship: </td>
    <td><input type="text" name="ship_name" value="{$shipname}"></td>
  </tr>
  <tr>
    <td>Ship Class: </td>
    <td><input type="text" name="ship_class" value="{$ship_class}"></td>
  </tr>
  <tr>
    <td>Player Status? </td>
    <td>
	<input type="radio" name="destroyed" value="N"{if $destroyed == "N"} checked{/if}>Alive<br>
	<input type="radio" name="destroyed" value="K"{if $destroyed == "K"} checked{/if}>Killed with Escape Pod<br>
	<input type="radio" name="destroyed" value="Y"{if $destroyed == "Y"} checked{/if}>Killed without Escape Pod (out of game)
  	</td>
  </tr> 
  <tr>
    <td nowrap>Levels</td>
    <td nowrap colspan=3>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td>Hull: </td>
          <td><input type=text size=5 name="hull" value="{$hull}"></td>
          <td>Engines: </td>
          <td><input type=text size=5 name="engines" value="{$engines}"></td>
          <td>Power: </td>
          <td><input type=text size=5 name="power" value="{$power}"></td>
          <td>Computer: </td>
          <td><input type=text size=5 name="computer" value="{$computer}"></td>
        </tr>
        <tr>
          <td>Sensors: </td>
          <td><input type=text size=5 name="sensors" value="{$sensors}"></td>
          <td>Armour: </td>
          <td><input type=text size=5 name="armour" value="{$armour}"></td>
          <td>Shields: </td>
          <td><input type=text size=5 name="shields" value="{$shields}"></td>
          <td>Beams: </td>
          <td><input type=text size=5 name="beams" value="{$beams}"></td>
        </tr>
        <tr>
          <td>Torpedo Launchers: </td>
          <td><input type=text size=5 name="torp_launchers" value="{$torp_launchers}"></td>
          <td>Cloak: </td>
          <td><input type=text size=5 name="cloak" value="{$cloak}"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td nowrap>Holds</td>
    <td nowrap colspan=3>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td>Ore: </td>
          <td nowrap><input type="text" size=8 name="ship_ore" value="{$ore}"></td>
          <td>Organics: </td>
          <td nowrap><input type="text" size=8 name="ship_organics" value="{$organics}"></td>
          <td>Goods: </td>
          <td nowrap><input type="text" size=8 name="ship_goods" value="{$goods}"></td>
          <td>Energy: </td>
          <td nowrap><input type="text" size=8 name="ship_energy" value="{$energy}"></td>
        </tr>
        <tr>
          <td>Colonists: </td>
          <td nowrap><input type="text" size=8 name="ship_colonists" value="{$colonists}"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td nowrap>Combat</td>
    <td nowrap colspan=3>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td>Fighters: </td>
          <td nowrap><input type="text" size=8 name="ship_fighters" value="{$fighters}"></td>
          <td>Torpedoes: </td>
          <td nowrap><input type="text" size=8 name="torps" value="{$torps}"></td>
          <td>Armour Pts: </td>
          <td nowrap><input type="text" size=8 name="armour_pts" value="{$armour_pts}"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td nowrap>Devices</td>
    <td nowrap colspan=3>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td>Warp Editors: </td>
          <td nowrap><input type="text" size=8 name="dev_warpedit" value="{$dev_warpedit}"></td>
          <td>Genesis Torpedoes: </td>
          <td nowrap><input type="text" size=8 name="dev_genesis" value="{$dev_genesis}"></td>
          <td>Mine Deflectors: </td>
          <td nowrap><input type="text" size=8 name="dev_minedeflector" value="{$dev_minedeflector}"></td>
        </tr>
        <tr>
          <td>Emergency Warp: </td>
          <td nowrap><input type="text" size=8 name="dev_emerwarp" value="{$dev_emerwarp}"></td>
          <td>Escape Pod: </td>
          <td nowrap><input type="checkbox" name="dev_escapepod" value="on"{if $dev_escapepod == "Y"} checked{/if}></td>
          <td>Fuel scoop: </td>
          <td nowrap><input type="checkbox" name="dev_fuelscoop" value="on"{if $dev_fuelscoop == "Y"} checked{/if}></td>
          <td>Nova Bomb Device: </td>
          <td nowrap><input type="checkbox" name="dev_nova" value="on"{if $dev_nova == "Y"} checked{/if}></td>
        </tr>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td nowrap>Money and more</td>
    <td nowrap>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td nowrap>Credits: </td>
          <td nowrap colspan=3><input type="text" name="credits" value="{$credits}"></td>
        </tr>
        <tr>
          <td nowrap>Turns: </td>
          <td nowrap colspan=3><input type="text" name="turns" value="{$turns}"></td>
        </tr>
        <tr>
          <td nowrap>Turns Used: </td>
          <td nowrap colspan=3><input type="text" name="turns_used" value="{$turns_used}"></td>
        </tr>
        <tr>
          <td nowrap>Current Sector: </td>
          <td nowrap colspan=3><input type="text" name="sector" value="{$sector_id}"></td>
        </tr>
      </table>
    </td>
    <td nowrap>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td nowrap>Current balance: </td>
          <td nowrap colspan=3><input type="text" name="igb_balance" value="{$igb_balance}"></td>
        </tr>
        <tr>
          <td nowrap>Loan: </td>
          <td nowrap colspan=3><input type="text" name="igb_loan" value="{$igb_loan}"></td>
        </tr>
        <tr>
          <td nowrap>Loan Timestamp: </td>
          <td nowrap colspan=3><input type="text" name="igb_loantime" value="{$igb_loantime}"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td nowrap>Special Information</td>
    <td nowrap>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td nowrap>Federation Bounty Count: </td>
          <td nowrap colspan=3><input type="text" name="fed_bounty_count" value="{$fed_bounty_count}"></td>
        </tr>
        <tr>
          <td nowrap>Template: </td>
          <td nowrap colspan=3><input type="text" name="template" value="{$template}"></td>
        </tr>
        <tr>
          <td nowrap>Avatar: </td>
          <td nowrap colspan=3><img src="images/avatars/{$avatar}"><input type="text" name="avatar" value="{$avatar}"></td>
        </tr>
      </table>
    </td>
    <td nowrap>
      <table border=0 cellspacing=0 cellpadding=5>
        <tr>
          <td nowrap>The last team the player left: </td>
          <td nowrap colspan=3><input type="text" name="last_team" value="{$last_team}"></td>
        </tr>
        <tr>
          <td nowrap>Date player left team: </td>
          <td nowrap colspan=3><input type="text" name="left_team_time" value="{$left_team_time}"></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="user" value="{$user}">
      <input type="hidden" name="operation" value="save">
      <input type="submit" value="Save">
    </td>
  </tr>
</table>

