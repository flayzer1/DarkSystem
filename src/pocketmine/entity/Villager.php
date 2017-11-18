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

use pocketmine\nbt\tag\IntTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class Villager extends Creature implements NPC, Ageable{
	
	const NETWORK_ID = self::VILLAGER;
	
	const PROFESSION_FARMER = 0;
	const PROFESSION_LIBRARIAN = 1;
	const PROFESSION_PRIEST = 2;
	const PROFESSION_BLACKSMITH = 3;
	const PROFESSION_BUTCHER = 4;
	const PROFESSION_GENERIC = 5;
	
	public $width = 0.938;
	public $length = 0.609;
	public $height = 2;

	public function getName(){
		return "Villager";
	}

	protected function initEntity(){
		parent::initEntity();
		
		$this->setMaxHealth(10);
		
		if(!isset($this->namedtag->Profession)){
			$this->setProfession(self::PROFESSION_GENERIC);
		}
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = Villager::NETWORK_ID;
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}
	
	public function setProfession($profession){
		$this->namedtag->Profession = new IntTag("Profession", $profession);
	}

	public function getProfession(){
		return $this->namedtag["Profession"];
	}

	public function isBaby(){
		return $this->getDataFlag(self::DATA_AGEABLE_FLAGS, self::DATA_FLAG_BABY);
	}
	
	public function getSpeed(){
        return 1.1;
    }
    
    public function getDrops(){
        return [];
    }
    
    public function getKillExperience(){
        return mt_rand(3, 6);
    }
    
}
