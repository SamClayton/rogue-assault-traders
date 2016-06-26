<?
class Basic_Torpedo {

	var $mine_damage_shields = 1500; // amount of damage to enemy shield hit points per torp
	var $mine_damage_all = 3000; // amount of damage to all enemy items except shields per torp
	var $torp_damage_shields = 1500; // amount of damage to enemy shield hit points per torp
	var $torp_damage_all = 2500; // amount of damage to all enemy items except shields per torp
	var $mine_deflector_damage = 150; // amound of damage to mines when each mine deflector hits

	var $mine_hit_pts = 100; // the amount of damage a mine take before it is destroyed
	var $torp_hit_pts = 100; // the amount of damage a torp can take before it is destroyed

	var $torp_price_modifier = 1; // The cost of each torp is multiplied by this number
}

?>