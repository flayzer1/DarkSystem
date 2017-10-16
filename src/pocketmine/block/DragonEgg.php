<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\event\block\BlockTeleportEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\sound\GenericSound;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\Server;
use pocketmine\Player;

class DragonEgg extends Fallable{
	
	protected $id = self::DRAGON_EGG;

	const RAND_VERTICAL = [-7,-6,-5,-4,-3,-2,-1,0,1,2,3,4,5,6,7];
	const RAND_HORIZONTAL = [-15,-14,-13,-12,-11,-10,-9,-8,-7,-6,-5,-4,-3,-2,-1,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];
	
	/**
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Dragon Egg";
	}

	/**
	 * @return int
	 */
	public function getHardness(){
		return 4.5;
	}

	/**
	 * @return int
	 */
	public function getResistance(){
		return 45;
	}

	/**
	 * @return int
	 */
	public function getLightLevel(){
		return 1;
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function isBreakable(Item $item){
		return false;
	}

	public function canBeActivated(){
		return true;
	}

	public function onActivate(Item $item, Player $player = null){
		$safe = false;
		while(!$safe){
			$level = $this->getLevel();
			$x = $this->getX() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
			$y = $this->getY() + self::RAND_VERTICAL[array_rand(self::RAND_VERTICAL)];
			$z = $this->getZ() + self::RAND_HORIZONTAL[array_rand(self::RAND_HORIZONTAL)];
			if($level->getBlockIdAt($x,$y,$z) == 0){
				$safe = true;
				break;
			}
		}
		
		$level->setBlock($this, new Air(), true, true);
		$oldpos = clone $this;
		$pos = new Position($x, $y, $z, $level);
		$newpos = clone $pos;

		$ev = new BlockTeleportEvent($this, $oldpos, $newpos);
		Server::getInstance()->getPluginManager()->callEvent($ev);
		if(!$ev->isCancelled()){
			$level->setBlock($pos, $this, true, true);
			$posdistance = new Position($newpos->x - $oldpos->x, $newpos->y - $oldpos->y, $newpos->z - $oldpos->z, $this->getLevel());
			$intdistance = $oldpos->distance($newpos);
			for($c = 0; $c <= $intdistance; $c++){
				$progress = $c / $intdistance;
				$this->level->addSound(new GenericSound(new Position($oldpos->x + $posdistance->x * $progress, 1.62 + $oldpos->y + $posdistance->y * $progress, $oldpos->z + $posdistance->z * $progress, $this->getLevel()), LevelEventPacket::EVENT_PARTICLE_PORTAL_1));
			}
		}
		
		return $safe;
	}
}
