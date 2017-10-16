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
use pocketmine\Player;

class EndRod extends Transparent{
    
    const FACING_DOWN = 0;
    const FACING_UP = 1;
    const FACING_NORTH = 2;
    const FACING_SOUTH = 3;
    const FACING_WEST = 4;
    const FACING_EAST = 5;
    
    protected $id = self::END_ROD;
    
    public function __construct($meta = 0){
		$this->meta = $meta;
	}
    
    public function getName(){
        return 'End Rod';
    }
    
    public function getLightLevel(){
        return 9;
    }
    
    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
        if($target->isTransparent()){
            return false;
        }
        
        if($face < 2){
            $this->meta = $face;
        }else{
            $this->meta = $face + (($face % 2 == 0) ? 1 : -1);
        }
        
        $this->getLevel()->setBlock($block, $this, true, true);
        
        return true;
    }
    
    public function getDrops(Item $item){
        return [
            [ $this->id, 0, 1 ]
        ];
    }
}
