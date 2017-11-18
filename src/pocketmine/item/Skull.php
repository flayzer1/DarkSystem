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

class Skull extends Item
{
    const SKELETON = 0;
    const WITHER_SKELETON = 1;
    const ZOMBIE = 2;
    const STEVE = 3;
    const CREEPER = 4;
    const DRAGON = 5;
    
    public function __construct($meta = 0, $count = 1)
    {
        $this->block = Block::get(Block::SKULL_BLOCK);
        
        parent::__construct(self::SKULL, $meta, $count, "Skull");
    }

    public function getMaxStackSize()
    {
        return 64;
    }
    
}
