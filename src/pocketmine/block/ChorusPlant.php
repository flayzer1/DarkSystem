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

use pocketmine\item\Tool;
use pocketmine\item\Item;

class ChorusPlant extends Solid{
    
    protected $id = self::CHORUS_PLANT;
    
    public function __construct($meta = 0){
		$this->meta = $meta;
	}
    
    public function getName(){
		return "Chrorus Plant";
	}
    
    public function getHardness(){
        return 0.4;
    }
    
    public function getToolType(){
        return Tool::TYPE_AXE;
    }
    
    public function getDrops(Item $item){
        return [
            [Item::CHORUS_FRUIT, 0, 2]
        ];
    }
}
