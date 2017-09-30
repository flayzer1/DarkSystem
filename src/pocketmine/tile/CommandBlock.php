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
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class CommandBlock extends Spawnable{
	
	public function __construct(Level $level, Compound $nbt){
		if(!isset($nbt->command) or !($nbt->command instanceof IntTag)){
			$nbt->command = new IntTag("command", null);
		}
		
		parent::__construct($level, $nbt);
	}
	
	public function getCommand(){
		return $this->namedtag->command;
	}
	
	public function setCommand($command){
		$this->namedtag["command"] = $command;
		$this->onChanged();
	}
	
	public function getSpawnCompound(){
		return new Compound("", [
			new StringTag("id", Tile::COMMAND_BLOCK),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new IntTag("command", (int) $this->namedtag["command"])
		]);
	}
}
