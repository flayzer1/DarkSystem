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

class ItemBlock extends Item{
	
	public function __construct(Block $block, $meta = 0, $count = 1){
		$this->block = $block;
		
		parent::__construct($block->getId(), $block->getDamage(), $count, $block->getName());
	}

	public function setDamage($meta){
		$this->meta = $meta !== null ? $meta & 0xf : null;
		$this->block->setDamage($this->meta);
	}

	public function __clone(){
		$this->block = clone $this->block;
	}

	public function getBlock(){
		return $this->block;
	}
	
}
