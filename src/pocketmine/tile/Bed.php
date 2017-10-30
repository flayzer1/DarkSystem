<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\ByteTag;

class Bed extends Spawnable{
	
	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->color) or !($nbt->color instanceof ByteTag)){
			$nbt->color = new ByteTag("color", 14);
		}
		
		parent::__construct($level, $nbt);
	}
	
	public function getColor(){
		return $this->namedtag->color->getValue();
	}
	
	public function setColor($color){
		$this->namedtag["color"] = $color & 0x0f;
		$this->onChanged();
	}
	
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::BED),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new ByteTag("color", (int) $this->namedtag["color"]),
			new ByteTag("isMovable", (int) $this->namedtag["isMovable"])
		]);
	}
}
