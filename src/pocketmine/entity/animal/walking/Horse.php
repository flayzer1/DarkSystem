<?php

namespace pocketmine\entity\animal\walking;

use pocketmine\entity\animal\WalkingAnimal;
use pocketmine\entity\Rideable;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\entity\Creature;

class Horse extends WalkingAnimal implements Rideable{
	
    const NETWORK_ID = 23;

	const DATA_HORSE_TYPE = 19;

	const TYPE_NORMAL = -1;
	const TYPE_WHITE = 0;
	const TYPE_BROWN = 2;
	const TYPE_ZOMBIE = 3;
	const TYPE_SKELETON = 4;
	const TYPE_GOLD = 6;
	const TYPE_LIGHTBROWN = 7;
	const TYPE_DARKBROWN = 8;
	const TYPE_GRAY = 9;
	const TYPE_SILVER = 10;
	const TYPE_BLACK = 12;
	const TYPE_BLACKANDWHITE = 14;
	const TYPE_WHITEANDBLACK = 15;

	const TYPE_WEAR_LEATHER = 18;
	const TYPE_WEAR_IRON = 19;
	const TYPE_WEAR_GOLD = 20;
	const TYPE_WEAR_DIAMOND = 21;

	public $width = 0.6;
	public $length = 1.8;
	public $height = 1.8;
	public $maxhealth = 52;
	public $maxjump = 3;
    
    public function getName(){
        return "Horse";
    }
    
    public function initEntity(){
		parent::initEntity();
		
		@$flags |= 1 << Entity::DATA_FLAG_SADDLED;
		@$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		@$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;

		$pk->metadata = [
		Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
		Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_MAX_AIR => [Entity::DATA_TYPE_SHORT, 400],
		Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],
		Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
		Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 1],
		];

		$player->dataPacket($pk);

		$this->sendAttribute($player);
		
		$this->setChestPlate(419);
		
		$this->setMaxHealth(20);
	}
	
    public function getSpeed(){
        return 1;
    }
    
    public function setChestPlate($id = 419){
		$pk = new MobArmorEquipmentPacket();
		$pk->eid = $this->getId();
		$pk->slots = [
		ItemItem::get(0,0),
		ItemItem::get($id,0),
		ItemItem::get(0,0),
		ItemItem::get(0,0)
		];
		
		foreach($this->level->getPlayers() as $player){
			$player->dataPacket($pk);
		}
	}

	public function sendAttribute(Player $player){
		$entry = array();
		$entry[] = new Attribute($this->getId(), "minecraft:horse.jump_strength", 0, $this->maxjump, 0.6679779);
		$entry[] = new Attribute($this->getId(), "minecraft:fall_damage", 0, 3.402823, 1);
		$entry[] = new Attribute($this->getId(), "minecraft:luck", -1024, 1024, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:movement", 0, 3.402823, 0.223);
		$entry[] = new Attribute($this->getId(), "minecraft:absorption", 0, 3.402823, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:health", 0, 40, 40);

		$pk = new UpdateAttributesPacket();
		$pk->entries = $entry;
		$pk->entityId = $this->getId();
		$player->dataPacket($pk);
	}

	public function goBack(Player $player){
		$xz = $this->getXZ($this->yaw,$this->pitch);

		$movex = $xz[0];
		$movez = $xz[1];
		$newx = ($this->x - $movex/2);
		$newy = $this->y;
		$newz = ($this->z - $movez/2);

		if($this->isGoing(new Vector3($newx,$newy,$newz))){
			$this->x -= $movex/2;
			$this->z -= $movez/2;
		}
	}
	
	public function goStraight(Player $player){
		$xz = $this->getXZ($this->yaw,$this->pitch);

		$movex = $xz[0];
		$movez = $xz[1];
		$newx = $this->x + $movex;
		$newy = $this->y;
		$newz = $this->z + $movez;
		if($this->isGoing(new Vector3($newx,$newy,$newz))){
			$this->x += $movex;
			$this->z += $movez;
		}
	}

	public function getXZ($yaw,$pitch){
		$x = (-sin($yaw/180*M_PI))/2;
		$z = (cos($yaw/180*M_PI))/2;

		return array($x, $z);
	}

	public function isGoing($vector3){
		$level = $this->getLevel();
		$block = $level->getBlock($vector3);
		if($block->isTransparent()) return true;
		else return false;
	}

	public function jump($power){
		$this->move(0, $this->maxjump * ($power * 0.0001), 0);
		$this->updateMovement();
	}
	
	public function createChild($ageable){
		
	}
	
    public function targetOption(Creature $creature, float $distance){
        if($creature instanceof Player){
            return $creature->spawned && $creature->isAlive() && !$creature->closed && $creature->getInventory()->getItemInHand()->getId() == Item::APPLE && $distance <= 49;
        }
        return false;
    }

    public function getDrops(){
		$drops = [];
		if($this->lastDamageCause instanceof EntityDamageByEntityEvent){
			  $drops[] = Item::get(Item::LEATHER, 0, mt_rand(0, 2));
		}
		return $drops;
	}
    
    public function getKillExperience(){
        return mt_rand(1, 3);
    }
    
    public function getRidePosition(){
    	return [-0.02, 2.3, 0.19];
    }
    
}
