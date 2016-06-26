<?php
$l_log_months 		 = array('January','February','March','April','May','June','July','August','September','October','November','December');
$l_log_months_short 	 = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
$l_log_pod 		 = "Luckily, you had an escape pod!";
$l_log_nopod 		 = "<font color=yellow><b>You did not have an escape pod, you died.</b></font>";
$l_log_log 		 = "[player]'s Log";
$l_log_start 		 = "Log entry for";
$l_log_end 		 = "End of log entry for";
$l_log_click 		 = "Click <A HREF=main.php><font color=#00ff00>here</font></A> to return to the main menu.";

// Log entry data

$l_log_text[LOG_LOGIN] 	 = "You have logged in, from [ip].";
$l_log_title[LOG_LOGIN]  = "Logged in";
$l_log_text[LOG_LOGOUT]  = "You have logged out, from [ip].";
$l_log_title[LOG_LOGOUT] = "Logged out";
$l_log_text[LOG_ATTACK_OUTMAN] 		= "[player] was spotted on an attack vector, but your great pilot skills and good engines allowed you to outmaneuver the enemy.";
$l_log_title[LOG_ATTACK_OUTMAN] 	= "Evasive action!";
$l_log_text[LOG_ATTACK_OUTSCAN] 	= "You detected [player]'s weapons trying to fix on your ship, but your cloak generators managed to fool the enemy's primitive sensor circuits.";
$l_log_title[LOG_ATTACK_OUTSCAN] 	= "Cloak successful!";
$l_log_text[LOG_ATTACK_EWD] 		= "You are surprised by [player]'s treacherous attack. You can see the incoming warheads as you reach frantically for the emergency warp button. The world blurs around you and, for a milisecond, you think you are dead. When you finally open your eyes, you see that you were safely transported elsewhere. But where exactly, you do not know...";
$l_log_title[LOG_ATTACK_EWD] 		= "Emergency warp!";
$l_log_text[LOG_ATTACK_EWDFAIL] 	= "Your sensors detect [player]'s ship on an attack vector. \"Better be safe than sorry\", you think, hitting the emergency warp button. You hear a strange choking sound coming from the engines room and smell burned circuitry. \"Stupid piece of crap!\", you yell, kicking at the computer. The computer emits beeps of protestation as you prepare for the incoming battle...";
$l_log_title[LOG_ATTACK_EWDFAIL] 	= "Emergency warp <font color=red>failed!</font>";
$l_log_text[LOG_ATTACK_LOSE] 		= "You ship is being attacked by [player]. Try as you might, you cannot defend yourself against [player]'s superior weaponry. Your ship was destroyed. ";
$l_log_title[LOG_ATTACK_LOSE] 		= "<font color=red>Combat lost!</font>";
$l_log_text[LOG_ATTACKED_WIN] 		= "You ship was attacked by [player]. You battle for a while, then the fight breaks down. You lost [armor] armor points and [fighters] fighters in the skirmish.";
$l_log_title[LOG_ATTACKED_WIN] 		= "<font color=yellow>Skirmish!</font>";
$l_log_text[LOG_HIT_MINES] 		= "You hit [mines] mines when travelling in sector [sector].";
$l_log_title[LOG_HIT_MINES] 		= "<font color=yellow>Mines alert!</font>";
$l_log_text[LOG_SHIP_DESTROYED_MINES] 	= "Your ship was destroyed by a minefield in sector [sector]. ";
$l_log_title[LOG_SHIP_DESTROYED_MINES] 	= "<font color=red>Mines alert!</font>";
$l_log_text[LOG_PLANET_DEFEATED_D] 	= "Your planet [planet_name] in sector [sector] was defeated in battle by [name]. Fortunately, the strong loyalty of your citizens played in your favor. They have bravely chosen to destroy the planet rather than have it fall into enemy hands. You organize superb funerals in the memory of your faithful people.";
$l_log_title[LOG_PLANET_DEFEATED_D] 	= "<font color=yellow>Planet destroyed</font>";
$l_log_text[LOG_PLANET_DEFEATED] 	= "Your planet [planet_name] in sector [sector] was defeated in battle by the wretched [name]. The people of the planet welcome their new ruler. You hear a testimony on FNN : \"We're glad to be rid of that old fart! We don't want no stinking loser who can't protect us!\". You swear to make them pay for their arrogance.";
$l_log_title[LOG_PLANET_DEFEATED] 	= "<font color=red>Planet defeated!</font>";
$l_log_text[LOG_PLANET_NOT_DEFEATED] 	= "Your planet [planet_name] in sector [sector] was attacked by [name]. The citizens organized a valliant defense however, and managed to destroy the opponent. They salvage some resources from the space debris : [goods] goods, [ore] ore, [organics] organics and [salvage]% of the ship's worth of [credits] credits.";
$l_log_title[LOG_PLANET_NOT_DEFEATED] 	= "Victory!";
$l_log_title[LOG_RAW] 			= "Notice";
$l_log_text[LOG_DEFS_DESTROYED] 	= "[quantity] [type] were destroyed in sector [sector].";
$l_log_title[LOG_DEFS_DESTROYED] 	= "Sector defenses destroyed";
$l_log_text[LOG_PLANET_EJECT] 		= "You have been ejected from the planet in sector [sector] because [name] left the team.";
$l_log_title[LOG_PLANET_EJECT] 		= "Ejected!";
$l_log_text[LOG_BADLOGIN] 		= "There has been a bad login attempt from [ip].";
$l_log_title[LOG_BADLOGIN] 		= "Bad login";
$l_log_text[LOG_PLANET_SCAN] 		= "[name] has scanned your planet [planet_name] in sector [sector].";
$l_log_title[LOG_PLANET_SCAN] 		= "Planet scan";
$l_log_text[LOG_PLANET_SCAN_FAIL] 	= "[name] has attempted to scan your planet [planet_name] in sector [sector], but failed.";
$l_log_title[LOG_PLANET_SCAN_FAIL] 	= "Planet scan failed";
$l_log_text[LOG_SHIP_SCAN] 		= "[player] has scanned your ship.";
$l_log_title[LOG_SHIP_SCAN] 		= "Ship scan";
$l_log_text[LOG_SHIP_SCAN_FAIL] 	= "[player] has attempted to scan your ship, but failed.";
$l_log_title[LOG_SHIP_SCAN_FAIL] 	= "Ship scan failed";
$l_log_text[LOG_KABAL_ATTACK] 		= "Attacking [player]...";
$l_log_title[LOG_KABAL_ATTACK] 		= "Attack";
$l_log_text[LOG_STARVATION] 		= "One of your planet in [sector] cannot sustain its population! More organics is needed. [starvation] colonists died of hunger.";
$l_log_title[LOG_STARVATION] 		= "<font color=yellow>Starvation!</font>";
$l_log_title[LOG_TOW] 			= "Towed!";
$l_log_text[LOG_DEFS_DESTROYED_F] 	= "You have destroyed [fighters] patrolling fighters in sector [sector].";
$l_log_title[LOG_DEFS_DESTROYED_F] 	= "Fighters destroyed";
$l_log_text[LOG_DEFS_KABOOM] 		= "You were destroyed by a fighters force in sector [sector]. ";
$l_log_title[LOG_DEFS_KABOOM] 		= "<font color=red>Ship destroyed!</font>";
$l_log_text[LOG_HARAKIRI] 		= "You were tired of captaining this space barge, so you decided the universe would be better without it. Authorized by [ip].";
$l_log_title[LOG_HARAKIRI] 		= "<font color=red>Self-destructed!</font>";
$l_log_text[LOG_TEAM_REJECT] 		= "[player] has rejected an invitation to join your team, [teamname].";
$l_log_title[LOG_TEAM_REJECT] 		= "Invitation rejected";
$l_log_text[LOG_TEAM_RENAME] 		= "You have renamed your team to [team].";
$l_log_title[LOG_TEAM_RENAME] 		= "Team renamed";
$l_log_text[LOG_TEAM_M_RENAME] 		= "Your team coordinator renamed the team to [team].";
$l_log_title[LOG_TEAM_M_RENAME] 	= "Team renamed";
$l_log_text[LOG_TEAM_KICK] 		= "You have been ejected from the team [team].";
$l_log_title[LOG_TEAM_KICK] 		= "Ejected from team!";
$l_log_text[LOG_TEAM_CREATE] 		= "You have created a new team, [team].";
$l_log_title[LOG_TEAM_CREATE] 		= "Created new team";
$l_log_text[LOG_TEAM_LEAVE] 		= "You have left team [team].";
$l_log_title[LOG_TEAM_LEAVE] 		= "Left team";
$l_log_text[LOG_TEAM_NEWLEAD] 		= "You have left the team [team]. You relinquished the team leadership to [name].";
$l_log_title[LOG_TEAM_NEWLEAD] 		= "Left team";
$l_log_text[LOG_TEAM_LEAD] 		= "Your team coordinator has decided to leave [team]. You have been appointed to take its place.";
$l_log_title[LOG_TEAM_LEAD] 		= "Promotion!";
$l_log_text[LOG_TEAM_JOIN] 		= "You have joined the team [team].";
$l_log_title[LOG_TEAM_JOIN] 		= "Joined team";
$l_log_text[LOG_TEAM_NEWMEMBER] 	= "[name] has accepted to join [team]. This brings you one step further to galactic domination.";
$l_log_title[LOG_TEAM_NEWMEMBER] 	= "New team member";
$l_log_text[LOG_TEAM_INVITE] 		= "You have been invited to be part of [team].";
$l_log_title[LOG_TEAM_INVITE] 		= "Invitation";
$l_log_text[LOG_TEAM_NOT_LEAVE] 	= "[player] has left your team.";
$l_log_title[LOG_TEAM_NOT_LEAVE] 	= "Team loses a member";
$l_log_text[LOG_ADMIN_HARAKIRI] 	= "[player] has self-destructed from ip [ip].";
$l_log_title[LOG_ADMIN_HARAKIRI] 	= "Self-destruct";
$l_log_text[LOG_ADMIN_ILLEGVALUE] 	= "[player] had [quantity] [type], with max holds of [holds]";
$l_log_title[LOG_ADMIN_ILLEGVALUE] 	= "Illegal ship value";
$l_log_text[LOG_ADMIN_PLANETDEL] 	= "[attacker] has attempted to capture [defender]'s planet in sector [sector]. Planet was destroyed instead.";
$l_log_title[LOG_ADMIN_PLANETDEL] 	= "Planet destroyed";
$l_log_text[LOG_DEFENCE_DEGRADE] 	= "Sector Defences Fighters in sector [sector] degraded by [degrade] percent due to insufficient energy.";
$l_log_test[LOG_DEFENCE_DEGRADE] 	= "Sector Defences degraded.";
$l_log_text[LOG_PLANET_CAPTURED] 	= "You captured a planet belonging to [owner]. It had [cols] colonists and [credits] credits on it.";
$l_log_title[LOG_PLANET_CAPTURED] 	= "Planet captured!";
$l_log_text[LOG_CHEAT_TEAM] 		= "Player [player] at IP address [ip] tried to exploit the team kick anyone cheat. His ship was destroyed.";
$l_log_text[LOG_BOUNTY_CLAIMED] = "You claimed a bounty of [amount] credits for the destruction of the ship belonging to [bounty_on]. The bounty was placed by [placed_by].";
$l_log_title[LOG_BOUNTY_CLAIMED] = "Bounty Claimed.";
$l_log_text[LOG_BOUNTY_PAID] = "Your bounty on [bounty_on] has been paid for the amount of [amount] credits.";
$l_log_text[LOG_BOUNTY_TAX_PAID] = "The Fedration bounty on [bounty_on] has been taxed for the amount of [amount] credits.";
$l_log_title[LOG_BOUNTY_PAID] = "Bounty Paid.";
$l_log_text[LOG_BOUNTY_CANCELLED] = "Your bounty on [bounty_on] for the amount of [amount] credits has been refunded.";
$l_log_title[LOG_BOUNTY_CANCELLED] = "Bounty Refunded.";
$l_log_text[LOG_SPACE_PLAGUE] = "Your planet [name] in sector [sector] has been hit by Space Plague. [percentage] percent of the colonists were killed.";
$l_log_title[LOG_SPACE_PLAGUE] = "Space Plague!";
$l_log_text[LOG_PLASMA_STORM] = "Your planet [name] in sector [sector] has been hit by a Plasma Storm. All stored Energy has been lost.";
$l_log_title[LOG_PLASMA_STORM] = "Plasma Storm!";
$l_log_text[LOG_BOUNTY_FEDBOUNTY] = "The Federation has placed a bounty on you for the amount of [amount] credits.";
$l_log_title[LOG_BOUNTY_FEDBOUNTY] = "Federation Bounty!";
$l_log_text[LOG_IGB_TRANSFER1] = "Player [name] transferred [sum] credits to your IGB account.";
$l_log_title[LOG_IGB_TRANSFER1] = "Credits transferred";
$l_log_text[LOG_IGB_TRANSFER2] = "You transferred [sum] credits to player [name]'s IGB account.";
$l_log_title[LOG_IGB_TRANSFER2] = "Credits transferred";
//Planetary Revolt Text
$l_log_title[LOG_PLANET_REVOLT] = "Planetary Revolt";
$l_log_text[LOG_PLANET_REVOLT] = "Too many people on your planet [name] in sector [sector] and they have revolted!!! Damages: [credits]% credits lost, [colonists]% colonists killed, [organics]% organics destroyed, [goods]% goods destroyed, [ore]% ore destroyed, [torps]% torps destroyed, [fighters]% fighters destroyed and [energy]% energy lost.";
// End Planetary Revolt
$l_log_text[LOG_PLANET_novaED_D] 	= "Your planet [planet_name] in sector [sector] was destroyed with a Nova Bomb by [name]. Absolutely nothing remains to mark the planets former existance.";
$l_log_title[LOG_PLANET_novaED_D] 	= "<font color=yellow>Planet destroyed with a Nova Bomb</font>";
$l_log_text[LOG_SHIP_novaED_D] 	= "Your ship was destroyed with a Nova Bombed by [name].";
$l_log_title[LOG_SHIP_novaED_D] 	= "<font color=yellow>Ship destroyed with a Nova Bomb</font>";
$l_log_text[LOG_SPY_FOUND_EMBEZZLER] 	= "Your dignitary number [DIG] on planet [PLANET] has been caught embezzling.  You might want to dismiss them.";
$l_log_title[LOG_SPY_FOUND_EMBEZZLER] 	= "<font color=yellow>Embezzler found!</font>";
$l_log_text[LOG_PROBE_DETECTED_SHIP]="<font color=yellow>Your probe id [id] detected [ship_name] entering sector [sector]</font>";
$l_log_text[LOG_PROBE_SCAN_SHIP]="<font color=yellow>Your probe id [id] scanned [ship_name] entering sector [sector]<br>Hull: [hull] Engines: [engines] Power: [power] Fighter Bay: [computer] Sensors: [sensors] Beams: [beams] Torp Launchers: [torps] Armor: [armor] Shields: [shields] Cloak: [cloak]<br>Armor Points: [armor_pts] Fighters: [fighters] Torpedoes: [avail_torps] Ship Energy: [energy] Credits: [credits] <br>Mine Deflectors: [deflectors] Emergency Warp: [ewd] Escape Pod: [pod] ECM: [ecm]</font>";
$l_log_text[LOG_ADMIN_PLANETIND] 	= "[sector] planet [attacker] ([defender]) became independant.";
$l_log_title[LOG_ADMIN_PLANETIND] 	= "Planet went independant.";
$l_log_text[LOG_TEAM_CANCEL] 		= "You're invitation to be part of [team] has been canceled.";
$l_log_title[LOG_TEAM_CANCEL] 		= "Invitation Canceled";
$l_log_text[LOG_AUTOTRADE]="<font color=yellow>Auto Trade for [planetname] in sector [sector] for [totalcost] credits.<br>Goods: [goodsamount] @ [goodsprice] credits - Ore: [oreamount] @ [oreprice] credits - Organics: [organicsamount] @ [organicsprice] credits - Energy: [energyamount] @ [energyprice] credits<br></font>";
$l_log_title[LOG_AUTOTRADE] 		= "Auto Trade Report";
$l_log_text[LOG_AUTOTRADE_ABORTED]="<font color='#00ff00'>Auto Trade for [planetname] in sector [sector] was aborted due to enemy sector defence in sector [destsector].<br></font>";
$l_log_title[LOG_AUTOTRADE_ABORTED] 		= "Auto Trade Report";

$l_log_text[LOG_PROBE_DESTROYED]="<font color='#00ff00'>SECTOR DEFENCES in [sector] destroy your probe.</font>";
$l_log_title[LOG_PROBE_DESTROYED] 		= "Probe Destroyed";
$l_log_text[LOG_PROBE_NOTURNS]="<font color='#00ff00'>You do not have enough turns for probe [probe_id] to move to [sector]</font>";
$l_log_title[LOG_PROBE_NOTURNS] 		= "Probe Report";
$l_log_text[LOG_PROBE_INVALIDSECTOR] = "<font color='#00ff00'>Moving to sector [target_sector] is not allowed</font>";
$l_log_title[LOG_PROBE_INVALIDSECTOR] 		= "Probe Report";
$l_log_text[LOG_PROBE_DETECTPROBE]="<font color='#00ff00'>Probe [probe_id] in sector [sector] detected [probe_detect]</font>";
$l_log_title[LOG_PROBE_DETECTPROBE] 		= "[probe_type] <font color='yellow'>Probe Report</font>";

// added
$l_log_text[LOG_PLANET_BOMBED] = "Your planet [planet_name] in sector [sector] was bombed by [name]. You used [beams] energy and [torps] torpedoes in your defense.  You lost [figs] fighters.";
$l_log_title[LOG_PLANET_BOMBED] = "<font color=red>Planet bombed!</font>";

$l_log_text[LOG_DIG_PRODUCTION] = "Your dignitary [id] on planet [planet_name] in sector [sector] starts building production.";
$l_log_title[LOG_DIG_PRODUCTION] = "<font color=#00FF00>Production Building  Started!</font>";
$l_log_text[LOG_DIG_BIRTHDEC] = "Your dignitary [id] on planet [planet_name] in sector [sector] decreasing birthrate.";
$l_log_title[LOG_DIG_BIRTHDEC] = "<font color=#00FF00>Birthrate Decreased!</font>";
$l_log_text[LOG_DIG_BIRTHINC] = "Your dignitary [id] on planet [planet_name] in sector [sector] increasing birthrate.";
$l_log_title[LOG_DIG_BIRTHINC] = "<font color=#00FF00>Birthrate Increased!</font>";
$l_log_text[LOG_DIG_SPYHUNT] = "Your dignitary [id] on planet [planet_name] in sector [sector] began searching for spys on that planet.";
$l_log_title[LOG_DIG_SPYHUNT] = "<font color=#00FF00>Spy Hunter!</font>";
$l_log_text[LOG_DIG_INTEREST] = "Your dignitary [id] on planet [planet_name] in sector [sector] starts boosting credit production.";
$l_log_title[LOG_DIG_INTEREST] = "<font color=#00FF00>Interest Building Started!</font>";
$l_log_text[LOG_DIG_MONEY] = "Dignitary [id] on planet [planet_name] embezzled [data] credits and donated to you!";
$l_log_title[LOG_DIG_MONEY] = "<font color=#00FF00>You Recieved Embezzled  Credits!</font>";
$l_log_text[LOG_DIG_KILLED_SPY] = "Your dignitary detected player [name]'s spy on your planet [planet_name] in sector [sector]. The spy is killed now.";
$l_log_title[LOG_DIG_KILLED_SPY] = "<font color=yellow>Enemy Spy Killed!</font>";
$l_log_text[LOG_DIG_CATACLYSM] = "Your dig [id] on planet [planet_name] in sector [sector] perished in a terrible cataclysm with that planet.";
$l_log_title[LOG_DIG_CATACLYSM] = "<font color=yellow>dig Lost!</font>";
$l_log_text[LOG_SHIPDIG_CATACLYSM] = "Your dig [id] on player [name]'s ship [shipname] perished in a terrible cataclysm with that ship.";
$l_log_title[LOG_SHIPDIG_CATACLYSM] = "<font color=yellow>dig Lost!</font>";
$l_log_text[LOG_DIG_TORPS] = "Your dignitary [id] on planet [planet_name] in sector [sector] built [data] planetary torpedoes!";
$l_log_title[LOG_DIG_TORPS] = "<font color=#00FF00>Torpedoes Built!</font>";
$l_log_text[LOG_DIG_FITS] = "Your dignitary [id] on planet [planet_name] in sector [sector] built [data] planetary fighters!";
$l_log_title[LOG_DIG_FITS] = "<font color=#00FF00>Fighters Built!</font>";

$l_log_text[LOG_PLANET_YOUR_CAPTURED]   = "Your planet [planet_name] in sector [sector] was defeated in captured by the wretched [name].";
$l_log_title[LOG_PLANET_YOUR_CAPTURED]  = "<font color=red>Planet captured!</font>";

$l_log_text[LOG_SPY_NEWSHIP] = "You lost your spy [id] on [name]'s ship [shipname], because that player bought a new ship.";
$l_log_title[LOG_SPY_NEWSHIP] = "<font color=yellow>Spy Lost!</font>";

$l_log_text[LOG_SPY_SEND_FAIL] = "[name] tried to send a spy to your planet [planet_name] in sector [sector], but your Sensors detected the spy and he is killed now.";
$l_log_title[LOG_SPY_SEND_FAIL] = "<font color=yellow>Enemy Spy Killed!</font>";

$l_log_text[LOG_SPY_SABOTAGE] = "Your spy [id] on planet [planet_name] in sector [sector] starts sabotage. Production is decreased by [data] on that planet.";
$l_log_title[LOG_SPY_SABOTAGE] = "<font color=#00FF00>Sabotage Started!</font>";

$l_log_text[LOG_SPY_BIRTH] = "Your spy [id] on planet [planet_name] in sector [sector] decreased birthrate by [data] on that planet.";
$l_log_title[LOG_SPY_BIRTH] = "<font color=#00FF00>Birthrate Decreased!</font>";

$l_log_text[LOG_SPY_INTEREST] = "Your spy [id] on planet [planet_name] in sector [sector] starts collecting planet interest by [data] to you.";
$l_log_title[LOG_SPY_INTEREST] = "<font color=#00FF00>Interest Stealing Started!</font>";

$l_log_text[LOG_SPY_MONEY] = "Your spy [id] on planet [planet_name] in sector [sector] stole [data] credits to you!";
$l_log_title[LOG_SPY_MONEY] = "<font color=#00FF00>Credits Have Stolen!</font>";

$l_log_text[LOG_SPY_TORPS] = "Your spy [id] on planet [planet_name] in sector [sector] destroyed [data] planetary torpedoes!";
$l_log_title[LOG_SPY_TORPS] = "<font color=#00FF00>Torpedoes Destroyed!</font>";

$l_log_text[LOG_SPY_FITS] = "Your spy [id] on planet [planet_name] in sector [sector] destroyed [data] planetary fighters!";
$l_log_title[LOG_SPY_FITS] = "<font color=#00FF00>Fighters Destroyed!</font>";

$l_log_text[LOG_SPY_CPTURE] = "Your spy [id] on planet [planet_name] in sector [sector] captured that planet!! You are a lucky guy...";
$l_log_title[LOG_SPY_CPTURE] = "<font color=#00FF00>PLANET CAPTURED!</font>";

$l_log_text[LOG_SPY_CPTURE_OWNER] = "[name]'s spy captured your planet [planet_name] in sector [sector]! It seems that it is wise idea to cleanup your planets more frequently...";
$l_log_title[LOG_SPY_CPTURE_OWNER] = "<font color=red>Planet Lost!</font>";

$l_log_text[LOG_SPY_KILLED_SPYOWNER] = "There are no messages from your spy [id] on planet [planet_name] in sector [sector] for a long time - it seems that your Cloak device couldn't cover him enough and he is killed.";
$l_log_title[LOG_SPY_KILLED_SPYOWNER] = "<font color=yellow>Spy Lost!</font>";

$l_log_text[LOG_SPY_KILLED] = "Your Sensors detected a [name]'s spy on your planet [planet_name] in sector [sector]. The spy is killed now.";
$l_log_title[LOG_SPY_KILLED] = "<font color=yellow>Enemy Spy Killed!</font>";

$l_log_text[LOG_SHIPSPY_KILLED] = "There are no messages from your spy [id] on player [name]'s ship [shipname] for a long time - it seems that your Cloak device couldn't cover him enough and he is killed.";
$l_log_title[LOG_SHIPSPY_KILLED] = "<font color=yellow>Spy Lost!</font>";

$l_log_text[LOG_SPY_CATACLYSM] = "Your spy [id] on planet [planet_name] in sector [sector] perished in a terrible cataclysm with that planet.";
$l_log_title[LOG_SPY_CATACLYSM] = "<font color=yellow>Spy Lost!</font>";

$l_log_text[LOG_SHIPSPY_CATACLYSM] = "Your spy [id] on player [name]'s ship [shipname] perished in a terrible cataclysm with that ship.";
$l_log_title[LOG_SHIPSPY_CATACLYSM] = "<font color=yellow>Spy Lost!</font>";

$l_log_text[LOG_SPY_TOSHIP] = "Your spy [id] from planet [planet_name] in sector [sector] infiltrates a player [playername]'s ship [shipname] and acts now there.";
$l_log_title[LOG_SPY_TOSHIP] = "<font color=#00FF00>Spy Infiltrates Enemy Ship!</font>";

$l_log_text[LOG_SPY_TOPLANET] = "Your spy [id] from player [playername]'s ship [shipname] infiltrates a planet [planet_name] in sector [sector] and acts now there.";
$l_log_title[LOG_SPY_TOPLANET] = "<font color=#00FF00>Spy Infiltrates Enemy Planet!</font>";

$l_log_select 		 = "Select a Log:";
$l_log_general 		 = "General Log";
$l_log_dig 		 = "Dignitary Log";
$l_log_spy 		 = "Spy Log";
$l_log_disaster 		 = "Planet Disaster Log";
$l_log_nova 		 = "Nova Device Log";
$l_log_attack 		 = "Attack Log";
$l_log_scan 		 = "Scan Log";
$l_log_starv 		 = "Planet Starvation Log";
$l_log_probe 		 = "Probe Log";
$l_log_autotrade 		 = "Auto Trade Log";
$l_log_combined 		 = "Combined Log";

?>