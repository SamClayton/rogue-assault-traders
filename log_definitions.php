<?
if (preg_match("/log_definitions.php/i", $_SERVER['PHP_SELF'])) {
	echo "You can not access this file directly!";
	die();
}

//Planet log constants
define('PLOG_GENESIS_CREATE',1);
define('PLOG_GENESIS_DESTROY',2);
define('PLOG_CAPTURE',3);
define('PLOG_ATTACKED',4);
define('PLOG_SCANNED',5);
define('PLOG_OWNER_DEAD',6);
define('PLOG_DEFEATED',7);
define('PLOG_SOFA',8);
define('PLOG_PLANET_DESTRUCT',9);

//Log constants
define('LOG_LOGIN', 1);
define('LOG_LOGOUT', 2);
define('LOG_ATTACK_OUTMAN', 3);		 // Sent to target when better engines
define('LOG_ATTACK_OUTSCAN', 4);		// Sent to target when better cloak
define('LOG_ATTACK_EWD', 5);			// Sent to target when EWD engaged
define('LOG_ATTACK_EWDFAIL', 6);		// Sent to target when EWD failed
define('LOG_ATTACK_LOSE', 7);		   // Sent to target when he lost
define('LOG_ATTACKED_WIN', 8);		  // Sent to target when he won
define('LOG_HIT_MINES', 10);			// Sent when hit mines
define('LOG_SHIP_DESTROYED_MINES', 11); // Sent when destroyed by mines
define('LOG_PLANET_DEFEATED_D', 12);	// Sent when one of your defeated planets is destroyed instead of captured
define('LOG_PLANET_DEFEATED', 13);	  // Sent when a planet is defeated
define('LOG_PLANET_NOT_DEFEATED', 14);  // Sent when a planet survives
define('LOG_RAW', 15);				  // Sent as-is
define('LOG_DEFS_DESTROYED', 17);	   // Sent for destroyed sector defenses
define('LOG_PLANET_EJECT', 18);		 // Sent when ejected from a planet due to team switch
define('LOG_BADLOGIN', 19);			 // Sent when bad login
define('LOG_PLANET_SCAN', 20);		  // Sent when a planet has been scanned
define('LOG_PLANET_SCAN_FAIL', 21);	 // Sent when a planet scan failed
define('LOG_PLANET_CAPTURE', 22);	   // Sent when a planet is captured
define('LOG_SHIP_SCAN', 23);			// Sent when a ship is scanned
define('LOG_SHIP_SCAN_FAIL', 24);	   // Sent when a ship scan fails
define('LOG_KABAL_ATTACK', 25);		 // Sent to the Alliance by the Alliance ** TODO - redo this.
define('LOG_STARVATION', 26);		   // Sent when colonists are starving... ** TODO - check to ensure its bein used
define('LOG_TOW', 27);				  // Sent when a player is towed
define('LOG_DEFS_DESTROYED_F', 28);	 // Sent when a player destroys fighters
define('LOG_DEFS_KABOOM', 29);		  // Sent when sector fighters destroy you
define('LOG_HARAKIRI', 30);			 // Sent when self-destructed
define('LOG_TEAM_REJECT', 31);		  // Sent when player refuses invitation
define('LOG_TEAM_RENAME', 32);		  // Sent when renaming a team
define('LOG_TEAM_M_RENAME', 33);		// Sent to members on team rename
define('LOG_TEAM_KICK', 34);			// Sent to booted player
define('LOG_TEAM_CREATE', 35);		  // Sent when created a team
define('LOG_TEAM_LEAVE', 36);		   // Sent when leaving a team
define('LOG_TEAM_NEWLEAD', 37);		 // Sent when leaving a team, appointing a new leader
define('LOG_TEAM_LEAD', 38);			// Sent to the new team leader
define('LOG_TEAM_JOIN', 39);			// Sent when joining a team
define('LOG_TEAM_NEWMEMBER', 40);	   // Sent to leader on join
define('LOG_TEAM_INVITE', 41);		  // Sent to invited player
define('LOG_TEAM_NOT_LEAVE', 42);	   // Sent to leader on leave
define('LOG_ADMIN_HARAKIRI', 43);	   // Sent to admin on self-destruct
define('LOG_ADMIN_PLANETDEL', 44);	  // Sent to admin on planet destruction instead of capture
define('LOG_DEFENCE_DEGRADE', 45);	  // Sent sector fighters have no supporting planet
define('LOG_PLANET_CAPTURED', 46);	  // Sent to player when he captures a planet
define('LOG_BOUNTY_CLAIMED',47);		// Sent to player when they claim a bounty
define('LOG_BOUNTY_PAID',48);		   // Sent to player when their bounty on someone is paid
define('LOG_BOUNTY_CANCELLED',49);	  // Sent to player when their bounty is refunded
define('LOG_SPACE_PLAGUE',50);		  // Sent when space plague attacks a planet
define('LOG_PLASMA_STORM',51);		  // Sent when a plasma storm attacks a planet
define('LOG_BOUNTY_FEDBOUNTY',52);	  // Sent when the federation places a bounty on a player
define('LOG_PLANET_BOMBED',53);		 // Sent after bombing a planet
define('LOG_ADMIN_ILLEGVALUE', 54);	 // Sent to admin on planet destruction instead of capture
define('LOG_CHEAT_TEAM',55);		// Sent when someone attempts the kick any team member cheat
define('LOG_PLANET_YOUR_CAPTURED',56);  // Sent when your planet is captured
define('LOG_IGB_TRANSFER1',57);		 // Sent when someone transferred money to your IGB account
define('LOG_IGB_TRANSFER2',58);		 // Sent when you transferred money to sb's IGB account
// AA Trade
define('LOG_ADMIN_PLANETIND', 59);	  // Sent to admin on planet independance
//end

// Spy Logs
define('LOG_SPY_SEND_FAIL',60);		 // Sent to player when another player failed to send a spy to his planet
define('LOG_SPY_SABOTAGE',61);		  // Sent to spy owner if his spy starts sabotage
define('LOG_SPY_BIRTH',62);			 // Sent to spy owner if his spy starts decreasing birth rate
define('LOG_SPY_INTEREST',63);		  // Sent to spy owner if his spy starts stealing planet interest
define('LOG_SPY_MONEY',64);			 // Sent to spy owner if his spy steals money
define('LOG_SPY_TORPS',65);			 // Sent to spy owner if his spy destroys torpedoes
define('LOG_SPY_FITS',66);			  // Sent to spy owner if his spy destroys fighters
define('LOG_SPY_CPTURE',67);			// Sent to spy owner if his spy captures a planet
define('LOG_SPY_CPTURE_OWNER',68);	  // Sent to planet owner if his planet is captured
define('LOG_SPY_KILLED_SPYOWNER',69);   // Sent to spy owner if his spy get killed on a planet
define('LOG_SPY_KILLED',70);			// Sent to planet owner if he killed an enemy spy
define('LOG_SHIPSPY_KILLED',71);		// Sent to spy owner if his spy get killed on a ship
define('LOG_SPY_CATACLYSM',72);		 // Sent to spy owner if his spy get killed with a planet
define('LOG_SHIPSPY_CATACLYSM',73);	 // Sent to spy owner if his spy get killed with a ship
define('LOG_SPY_TOSHIP',74);			// Sent to spy owner if his spy infiltrates an enemy ship
define('LOG_SPY_TOPLANET',75);		  // Sent to spy owner if his spy infiltrates an enemy planet
define('LOG_SPY_NEWSHIP',76);		   // Sent to spy owner if his spy get lost, because the ship owner bought a new ship

// AATrade
define('LOG_PLANET_REVOLT',77);		   // Sent to planet owner if planet revolts

define('LOG_PLANET_novaED_D',90);		   // Sent to planet owner if planet is novaed
define('LOG_SHIP_novaED_D',91);		   // Sent to planet owner if ship is novaed
// Dig defines
define('LOG_DIG_PRODUCTION',93);		  // Sent to dig owner if his dig starts production increasing
define('LOG_DIG_BIRTHDEC',94);			 // Sent to dig owner if his dig starts decreasing birth rate
define('LOG_DIG_INTEREST',89);		  // Sent to dig owner if his dig starts stealing planet interest
define('LOG_DIG_MONEY',95);			 // Sent to dig owner if his dig steals money
define('LOG_DIG_TORPS',96);			 // Sent to dig owner if his dig destroys torpedoes
define('LOG_DIG_FITS',97);			  // Sent to dig owner if his dig destroys fighters
define('LOG_DIG_KILLED_SPY',102);		// Sent to dig owner if his dig killed a spy
define('LOG_DIG_CATACLYSM',103);		 // Sent to dig owner if his dig get killed with a planet
define('LOG_SHIPDIG_CATACLYSM',104);	 // Sent to dig owner if his dig get killed with a ship
define('LOG_DIG_BIRTHINC',108);
define('LOG_SPY_FOUND_EMBEZZLER',109);		   // Sent to dig owner if his dig get lost, because the ship owner bought a new ship
define('LOG_BOUNTY_TAX_PAID',110);		   // Sent to player when their bounty on someone is paid
define('LOG_PROBE_DETECTED_SHIP',111);		   // Sent to player when their probe detects incoming ship
define('LOG_PROBE_SCAN_SHIP',113);		   // Sent to player when their probe scans incoming ship
define('LOG_DIG_SPYHUNT',114);
define('LOG_TEAM_CANCEL', 115);		  // Sent to initation cancel to player

define('LOG_AUTOTRADE', 200);		  // Log Auto Trades
define('LOG_AUTOTRADE_ABORTED', 201);		  // Log Auto Trades Failed due to enemy Sector Defense

define('LOG_PROBE_DETECTED_SHIP',111);		   // Sent to player when their probe detects incoming ship
define('LOG_PROBE_SCAN_SHIP',113);		   // Sent to player when their probe scans incoming ship

define('LOG_PROBE_DESTROYED',300);		   // Sent to player when their probe is destroyed
define('LOG_PROBE_NOTURNS',301); 				// Send to player when player is out of turns
define('LOG_PROBE_INVALIDSECTOR',302);		   // Sent to player when thier probe tried to move to an invalid sector
define('LOG_PROBE_DETECTPROBE',303);		   // Sent to player when their probe detects incoming probe
// end
?>