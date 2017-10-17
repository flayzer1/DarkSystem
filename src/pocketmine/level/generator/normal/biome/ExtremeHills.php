<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\generator\normal\biome;

use pocketmine\block\Block;

abstract class ExtremeHillsBiome extends NormalBiome{
	
	public function __construct(){
		$this->setGroundCover([
			Block::get(Block::GRASS, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
			Block::get(Block::DIRT, 0),
		]);
	}
}