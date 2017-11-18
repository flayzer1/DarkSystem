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

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\level\Explosion;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\LevelSoundEventPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\Player;
use pocketmine\Server;

class PrimedTNT extends Entity implements Explosive{
	
	const NETWORK_ID = self::PRIMED_TNT;

	public $width = 0.98;
	public $length = 0.98;
	public $height = 0.98;

	protected $gravity = 0.04;
	protected $drag = 0.02;

	protected $fuse;

	public $canCollide = false;

	protected $owner = null;
	
	public function attack($damage, EntityDamageEvent $source){

	}

	protected function initEntity(){
		parent::initEntity();

		if(isset($this->namedtag->Fuse)){
			$this->fuse = $this->namedtag["Fuse"];
		}else{
			$this->fuse = 80;
		}
	}
	
	public function canCollideWith(Entity $entity){
		return false;
	}

	public function saveNBT(){
		parent::saveNBT();
		
		$this->namedtag->Fuse = new ByteTag("Fuse", $this->fuse);
	}

	public function onUpdate($currentTick){
		if($this->closed){
			return false;
		}
		$tickDiff = max(1, $currentTick - $this->lastUpdate);
		$this->lastUpdate = $currentTick;
		$hasUpdate = $this->entityBaseTick($tickDiff);
		if(!$this->dead){
			if(!$this->onGround){
				$this->motionY -= $this->gravity;
				$this->move($this->motionX, $this->motionY, $this->motionZ);
				$this->updateMovement();
			}
			$this->fuse -= $tickDiff;
			if($this->fuse % 2 == 0){
				$time = (int) $this->fuse / 2;
				$pk = new SetEntityDataPacket();
				$pk->eid = $this->getId();
				$pk->metadata = [self::DATA_EXPLODE_TIMER => [self::DATA_TYPE_INT, $time]];
				Server::broadcastPacket($this->hasSpawned, $pk);
			}
			if($this->fuse <= 0){
				$this->kill();
				$this->explode();
			}
		}
		return $hasUpdate or $this->fuse >= 0 or $this->motionX != 0 or $this->motionY != 0 or $this->motionZ != 0;
	}
	
	public function move($dx, $dy, $dz){
		if($dx == 0 && $dz == 0 && $dy == 0){
			return true;
		}
		$this->boundingBox->offset($dx, $dy, $dz);
		$block = $this->level->getBlock(new Vector3($this->x, $this->y + $dy, $this->z));
		if($dy < 0 && $block->isSolid()){
			$newY = (int) $this->y;
			for ($tempY = (int) $this->y; $tempY > (int) ($this->y + $dy); $tempY--){
				$block = $this->level->getBlock(new Vector3($this->x, $tempY, $this->z));
				if(!$block->isSolid()){
					$newY = $tempY;
				}
			}
			$this->onGround = true;
			$this->motionY = 0;
			$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_NO_AI, true);
			$addY = $this->boundingBox->maxY - $this->boundingBox->minY - 1;
			$this->setComponents($this->x + $dx, $newY + $addY, $this->z + $dz);
		}else{
			$this->setComponents($this->x + $dx, $this->y + $dy, $this->z + $dz);
		}
	}

	public function explode(){
		$this->server->getPluginManager()->callEvent($ev = new ExplosionPrimeEvent($this, 3.6));

		if(!$ev->isCancelled()){
			$explosion = new Explosion($this, $ev->getForce(), $this->owner);
			if($ev->isBlockBreaking()){
				$explosion->explodeB(); //A
			}else{
				$explosion->explodeB();
			}
			
			$pk = new LevelSoundEventPacket();
			$pk->eventId = LevelSoundEventPacket::SOUND_EXPLODE;
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->blockId = -1;
			$pk->entityType = 1;
			foreach ($this->getViewers() as $player){
				$player->dataPacket($pk);
			}
		}
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = PrimedTNT::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);

		parent::spawnTo($player);
	}

	public function setOwner($owner){
		$this->owner = $owner;
	}
	
}
