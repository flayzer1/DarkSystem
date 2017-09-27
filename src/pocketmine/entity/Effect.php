<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity;

use pocketmine\entity\effects\InvisibilityEffect;
use pocketmine\entity\effects\HarmingEffect;
use pocketmine\entity\effects\HealingEffect;
use pocketmine\entity\effects\HungerEffect;
use pocketmine\entity\effects\PoisonEffect;
use pocketmine\entity\effects\RegenerationEffect;
use pocketmine\entity\effects\SaturationEffect;
use pocketmine\entity\effects\SlownessEffect;
use pocketmine\entity\effects\SpeedEffect;
use pocketmine\entity\effects\WitherEffect;
use pocketmine\network\protocol\MobEffectPacket;
use pocketmine\Player;

class Effect{
	
	const SPEED = 1;
	const SLOWNESS = 2;
	const HASTE = 3;
	const SWIFTNESS = 3;
	const FATIGUE = 4;
	const MINING_FATIGUE = 4;
	const STRENGTH = 5;
	const HEALING = 6;
	const HARMING = 7;
	const JUMP = 8;
	const NAUSEA = 9;
	const CONFUSION = 9;
	const REGENERATION = 10;
	const DAMAGE_RESISTANCE = 11;
	const FIRE_RESISTANCE = 12;
	const WATER_BREATHING = 13;
	const INVISIBILITY = 14;
	const BLINDNESS = 15;
	const NIGHT_VISION = 16;
	const HUNGER = 17;
	const WEAKNESS = 18;
	const POISON = 19;
	const WITHER = 20;
	const HEALTH_BOOST = 21;
	const ABSORPTION = 22;
	const SATURATION = 23;
	
	protected static $effects;
	
	public static final function init(){
		Effect::$effects = new \SplFixedArray(256);

		Effect::$effects[Effect::SPEED] = new SpeedEffect(Effect::SPEED, "%potion.moveSpeed", 124, 175, 198);
		Effect::$effects[Effect::SLOWNESS] = new SlownessEffect(Effect::SLOWNESS, "%potion.moveSlowdown", 90, 108, 129, true);
		Effect::$effects[Effect::SWIFTNESS] = new Effect(Effect::SWIFTNESS, "%potion.digSpeed", 217, 192, 67);
		Effect::$effects[Effect::FATIGUE] = new Effect(Effect::FATIGUE, "%potion.digSlowDown", 74, 66, 23, true);
		Effect::$effects[Effect::STRENGTH] = new Effect(Effect::STRENGTH, "%potion.damageBoost", 147, 36, 35);
		Effect::$effects[Effect::HEALING] = new HealingEffect(Effect::HEALING, "%potion.heal", 248, 36, 35);
		Effect::$effects[Effect::HARMING] = new HarmingEffect(Effect::HARMING, "%potion.harm", 67, 10, 9, true);
		Effect::$effects[Effect::NIGHT_VISION] = new Effect(Effect::NIGHT_VISION, "%potion.nightVision", 147, 36, 35);
		Effect::$effects[Effect::JUMP] = new Effect(Effect::JUMP, "%potion.jump", 34, 255, 76);
		Effect::$effects[Effect::NAUSEA] = new Effect(Effect::NAUSEA, "%potion.confusion", 85, 29, 74, true);
		Effect::$effects[Effect::REGENERATION] = new RegenerationEffect(Effect::REGENERATION, "%potion.regeneration", 205, 92, 171);
		Effect::$effects[Effect::DAMAGE_RESISTANCE] = new Effect(Effect::DAMAGE_RESISTANCE, "%potion.resistance", 153, 69, 58);
		Effect::$effects[Effect::FIRE_RESISTANCE] = new Effect(Effect::FIRE_RESISTANCE, "%potion.fireResistance", 228, 154, 58);
		Effect::$effects[Effect::WATER_BREATHING] = new Effect(Effect::WATER_BREATHING, "%potion.waterBreathing", 46, 82, 153);
		Effect::$effects[Effect::INVISIBILITY] = new InvisibilityEffect(Effect::INVISIBILITY, "%potion.invisibility", 127, 131, 146);
		//Hunger
		Effect::$effects[Effect::WEAKNESS] = new Effect(Effect::WEAKNESS, "%potion.weakness", 72, 77, 72 , true);
		Effect::$effects[Effect::POISON] = new PoisonEffect(Effect::POISON, "%potion.poison", 78, 147, 49, true);
		Effect::$effects[Effect::WITHER] = new WitherEffect(Effect::WITHER, "%potion.wither", 53, 42, 39, true);
		Effect::$effects[Effect::HEALTH_BOOST] = new Effect(Effect::HEALTH_BOOST, "%potion.healthBoost", 248, 125, 35);
		//Absorption
		//Saturation
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public static final function getEffect($id){
		if(isset(Effect::$effects[$id])){
			return clone Effect::$effects[(int) $id];
		}
		return null;
	}

	public static final function getEffectByName($name){
		if(defined(Effect::class . "::" . strtoupper($name))){
			return Effect::getEffect(constant(Effect::class . "::" . strtoupper($name)));
		}
		return null;
	}
	
	protected $id;

	protected $name;

	protected $duration;

	protected $amplifier;

	protected $color;

	protected $show = true;

	protected $ambient = false;

	protected $bad;

	protected function __construct($id, $name, $r, $g, $b, $isBad = false){
		$this->id = $id;
		$this->name = $name;
		$this->bad = (bool) $isBad;
		$this->setColor($r, $g, $b);
	}

	public function getName(){
		return $this->name;
	}

	public function getId(){
		return $this->id;
	}

	public function setDuration($ticks){
		$this->duration = $ticks;
		return $this;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function isVisible(){
		return $this->show;
	}

	public function setVisible($bool){
		$this->show = (bool) $bool;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getAmplifier(){
		return $this->amplifier;
	}

	/**
	 * @param int $amplifier
	 *
	 * @return $this
	 */
	public function setAmplifier($amplifier){
		$this->amplifier = (int) $amplifier;
		return $this;
	}

	public function isAmbient(){
		return $this->ambient;
	}

	public function setAmbient($ambient = true){
		$this->ambient = (bool) $ambient;
		return $this;
	}

	public function isBad(){
		return $this->bad;
	}

	public function canTick(){
		return false;
	}

	public function applyEffect(Entity $entity) {
	}

	public function getColor(){
		return [$this->color >> 16, ($this->color >> 8) & 0xff, $this->color & 0xff];
	}

	public function setColor($r, $g, $b){
		$this->color = (($r & 0xff) << 16) + (($g & 0xff) << 8) + ($b & 0xff);
	}
	
	public function add(Entity $entity, $modify = false) {
		if ($entity instanceof Player) {
			$pk = new MobEffectPacket();
			$pk->eid = $entity->getId();
			$pk->effectId = $this->getId();
			$pk->amplifier = $this->getAmplifier();
			$pk->particles = $this->isVisible();
			$pk->duration = $this->getDuration();
			$pk->eventId = $modify ? MobEffectPacket::EVENT_MODIFY : MobEffectPacket::EVENT_ADD;
			$entity->dataPacket($pk);
		}
	}
	
	public function remove(Entity $entity) {
		if ($entity instanceof Player) {
			$pk = new MobEffectPacket();
			$pk->eid = $entity->getId();
			$pk->eventId = MobEffectPacket::EVENT_REMOVE;
			$pk->effectId = $this->getId();
			$entity->dataPacket($pk);
		}
	}
}
