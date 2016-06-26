# rogue-assault-traders
Rogue Assault Traders 0.01 is a fork of the Alien Assault Traders .21 codebase by user gvar of SourceForge. It contains many fixes &amp; mods, in many areas.
https://sourceforge.net/projects/rat10/

## Last project news (2007-05-22)
I have been bugged enough by unhappy AAT players, that i've decided to reopen the RAT Project.
Over the past few years i've fixed little things here there, added some new features, replaced a few graphics, tuned some of the scripts & schedulars, editted the english language files, and much more.
Some of the changes:
* Decided to drop the 2 original template sets (default, default_rick). my lower bandwidth dropdown version of the default_rick template is now default, and my lower bandwidth menu version is the 2nd default (in template settings). i choose to keep the bnt/ngs classic as i like connection to this forks roots. whether we keep all 3 or add 10 or drop 1 or 2, remains to be seen.
* Scabbed together a passible image for the front page, using a base graphic texture, and some images from various sources: Suns (TKI), Ships, front page buttons (BNT/NGS Template set for aat.21). Any graphic artists interested in showcasing their skills?
http://rogue-assault-traders.com/rat01/templates/gv_dropdown/images/logo_bg.jpg
* Added Debris news messeges, Now when a player finds a debris that "affects" them it broadcasts to the news. this way when a player finds a good debris and their score goes up, other players can see that they did indeed get a good debris, no more guessing, or rumors.... "Player Name Found Turns" "Player Name Lost Credits", ect. This has the added benefit of motivating players to roam around, as the rewards (and penalties) of the "Debris Lotto" can be seen in the news.
* Entropia Universe inspired me here, they have "Globals" "HoFs" (Hall Of Fame) "ATHs" (All Time Highs), a Global is any looted, crafted or mined item worth more then 50 PED (Project Entropia Dollars). When a player "gets" a global its posted to the games general chat, so all players see it.
* It makes a player feel good when they find a big one, and everyone likes to make the news... of course sometimes the news is less then good.... "Player Name Was Killed By Debris....."
* Sun code replaced, so they are displayed in all browsers, next to the port name. no longer uses absolute-positioning, so all suns are always in the same place reguardless of browser or screen res.
* Probes sometimes bite back when attacked (similar to damage from a nova bomb misfire, infact its the nova code). This one came to me after losing 20 or so very expensive probes to other players. So in a fit of outrageousness i gave them some teeth. "Be warned ye who kill probes... no more shall we tread upon me, with impunity!"
* Lower bandwidth versions of the 2 template sets. these are the original templates, with some graphics removed or replaced. A set of templates for dialup users with NO GRAPHICS, other then the login page. Once in game, its all links.
* Independent player ship roams like the alliance leader (same code). So its more or less a "stupid" AI.
* Derelict Ship, a bonus target for players, there is currently 1 more or less defenceless Derelict roaming at any given time (same code as sched_npc, create universe makes the 1st one, then the sched takes over to move it every 5 mins, settable in admin, it does not fix the ship as the original code did.) Recently updated, added a switch to check if the derelict has been killed, if so make a whole new random derelict. also added alliance leader places bounty, and posts news of it. (so players know a new derelict is out there)
* Pods, when a player is "podded" they find themselves in sector 1 in of all things a pod. Pods have 0 lvl stats, and may only move effectively via warp links. a futrue mod will have pods go to a nearby upgrades/ship port, with a small chance of partial (slim chance), or total (even slimmer) failure. a partial failure will result in a random warp, a total failure will put the player out of the game as if they lost a ship without a pod. (yeah i know, i suck, but its still going in).
* Debris, some minor mods here, most notable is the add/remove pod debris. its self explainitory, if you have a pod it takes it, if you dont, it gives you one, all SILENTLY, with the standard debris is trash messege.
* New Independent planets in the expanding universe (1 check per expansion, and a % chance when making a new sg (added to sector genesis code). The addition to the sg torps, surprises some folks, while pissing off others. its a balance to the mad sg creation. and a way for smart players to cash in on. ok so why? it always balffed me that the new sectors created in sched_expanding were devoid of indies. why make new spce thats empty? sure theres genesis torps, but just because the "KNOWN" universe has gotten bigger, it doesnt have to mean its "New growth" or however you choose to call it, it can simply mean that we can see futher over the horizen. therefore i decided that they shouldnt be empty, so i modified the planet code from the create_universe script and added it in to sched_expanding. then i thought, well new sg's could simply be in "subspace pockets", and sg torps simply rip a hole between real space and subspace. so ok, why is it all empty? why isnt there life there? so ok, i added the same code in.
now players can expect a lil excitement in an otherwise boring aspect of the game.
I noticed that this has been added to AAT .30 at long last. Good job guys!
* Last game stats, i started this a while back, its all manual at this point but i plan to automate it soon (while mine was 1st, the AAT folks seem to have it automated in .30, though thats a guess as i havent dug in to the .30 code, just at a live game)
* Email phishing exploit, a simple fix to change the email password request langauge, so it stated "to registered email" instead of "to players_email_name_here email". Simply put anyone could try to login with your name, and do a lost password request, to get your email address, allowing them to attempt to hijack your mail, or browser via url to there site, ect.
* Added/Increased turns cost for nova bombs, sg torps & genesis torps. Players like Nambia (love ya bro!), build like crazy! so in an effort to help the database & server load, i added a fairly steep turns cost to the genesis script. other players like to build huge subspace dens, never to return to realspace. (part of the reason for the debris adds)

These are the ones i can think of off the top of my head, theres been a lot of mods.
And theres lots planned for the future... so check in now & then.
At this point the code is not released in general.


there are 5 registered sites

###Active
* http://www.Rogue-Assault-Traders.com (of course)
* http://www.FunDMental.com

###Inactive (waiting for php 5 / mysql 4.2+ fix)
* http://www.CrazyBri.net 
* http://www.Rogue-Radio.com
* http://www.CrimsonBladeOnline.com

If you'd like your site to be considered for a game, or have some skill in php, mysql, html, css or would like to submit ideas for future ships, technologies, questions or comments, ect, please contact a project member.
