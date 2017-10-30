<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\inventory\customInventory;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Chest;
use pocketmine\Player;

class CustomChest extends Chest{
	
    private $replacement;

    public function __construct(Level $level, CompoundTag $nbt){
        parent::__construct($level, $nbt);
        
        $this->inventory = new CustomChestInventory($this);
        $this->replacement = [$this->getBlock()->getId(), $this->getBlock()->getDamage()];
    }

    private function getReplacement(){
        return Block::get(...$this->replacement);
    }

    public function sendReplacement(Player $player){
        $block = $this->getReplacement();
        $block->x = (int) $this->x;
        $block->y = (int) $this->y;
        $block->z = (int) $this->z;
        $block->level = $this->getLevel();
        if($block->level !== null){
            $block->level->sendBlocks([$player], [$block]);
        }
    }

    public function spawnToAll(){
    	
    }

}
