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

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Stone extends Solid{
	
	const NORMAL = 0;
	const GRANITE = 1;
	const POLISHED_GRANITE = 2;
	const DIORITE = 3;
	const POLISHED_DIORITE = 4;
	const ANDESITE = 5;
	const POLISHED_ANDESITE = 6;
	const UNKNOWN_STONE = 7;

	protected $id = self::STONE;

	public function __construct($meta = 0){
		$this->meta = $meta;

	}

	public function getHardness(){
		return 1.5;
	}

	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}

	public function getName(){
		static $names = [
			self::NORMAL => "Stone",
			self::GRANITE => "Granite",
			self::POLISHED_GRANITE => "Polished Granite",
			self::DIORITE => "Diorite",
			self::POLISHED_DIORITE => "Polished Diorite",
			self::ANDESITE => "Andesite",
			self::POLISHED_ANDESITE => "Polished Andesite",
			self::UNKNOWN_STONE => "Unknown Stone",
		];
		
		return $names[$this->meta & 0x07];
	}

	public function getDrops(Item $item){
		if($item->isPickaxe() >= Tool::TIER_WOODEN){
			return [
				[$this->getDamage() === 0 ? Item::COBBLESTONE : Item::STONE, $this->getDamage(), 1],
			];
		}else{
			return [];
		}
	}
}
