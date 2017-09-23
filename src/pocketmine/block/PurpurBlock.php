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

class PurpurBlock extends Solid{
    
    const META_TYPE_PILLAR = 2;

    protected $id = self::PURPUR_BLOCK;
    
    public function __construct($meta = 0){
		$this->meta = $meta;
	}
    
    public function getName(){
        return 'Purpur Block';
    }
    
    public function getHardness(){
        return 1.5;
    }
    
    public function getToolType(){
        return Tool::TYPE_PICKAXE;
    }
    
    public function getDrops(Item $item){
        if($item->isPickaxe()){
            return [
                [$this->id, $this->meta, 1]
            ];
        }
        return [];
    }
}
