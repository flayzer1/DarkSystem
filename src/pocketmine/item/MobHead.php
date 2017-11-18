<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\item;

use pocketmine\block\Block;

class MobHead extends Item{
	
	const SLOT_NUMBER = 0;
	
	const SKELETON = 0;
	const WITHER_SKELETON = 1;
	const ZOMBIE = 2;
	const STEVE = 3;
	const CREEPER = 4;
	const DRAGON = 5;
	
	static $names = [
		self::SKELETON => "Skeleton Head",
		self::WITHER_SKELETON => "Wither Skeleton Head",
		self::ZOMBIE => "Zombie Head",
		self::STEVE => "Steve Head",
		self::CREEPER => "Creeper Head",
		self::DRAGON => "Dragon Head",
	];
	
	public function __construct($meta = 0, $count = 1){
		$this->block = Block::get(Block::MOB_HEAD_BLOCK);
		
		parent::__construct(self::MOB_HEAD, $meta, $count, self::$names[$meta]);
	}
	
	public function getMaxStackSize(){
		return 64;
	}
	
}
