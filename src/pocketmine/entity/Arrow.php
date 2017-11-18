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

use pocketmine\level\Level;
use pocketmine\level\format\FullChunk;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class Arrow extends Projectile{
	
	const NETWORK_ID = self::ARROW;
	
	public $width = 0.4; //Default: 0.5
	public $length = 0.4; //This
	public $height = 0.4; //and This
	
	protected $gravity = 0.03;
	protected $drag = 0.01;
	protected $damage = 2;
	protected $isCritical;
	
	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, $critical = false){
		$this->isCritical = (bool) $critical;
		
		parent::__construct($level, $nbt, $shootingEntity);
	}
	
	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}
		
		$hasUpdate = parent::onUpdate($currentTick);
		if(!$this->hadCollision and $this->isCritical){
			/*$this->level->addParticle(new CriticalParticle($this->add(
				$this->width / 2 + mt_rand(-100, 100) / 500,
				$this->height / 2 + mt_rand(-100, 100) / 500,
				$this->width / 2 + mt_rand(-100, 100) / 500)));*/
		}elseif($this->onGround){
			$this->isCritical = false;
		}
		
		if($this->age > 1200){
			$this->kill();
			$hasUpdate = true;
		}elseif($this->y < 1){
			$this->kill();
			$hasUpdate = true;
		}
		
		return $hasUpdate;
	}
	
	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = Arrow::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$player->dataPacket($pk);
		
		parent::spawnTo($player);
	}
	
	public function getBoundingBox(){
		$bb = clone parent::getBoundingBox();
		
		return $bb->expand(1, 1, 1);
	}
	
}
