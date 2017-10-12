<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\item\Item;
use pocketmine\Player;

class PlayerGlassBottleEvent extends PlayerEvent implements Cancellable{

    public static $handlerList = null;

    /** @var Block */
    private $target;
    /** @var Item */
    private $item;

    /**
     * @param Player $player
     * @param Block  $target
     * @param Item   $itemInHand
     */
    public function __construct(Player $player, Block $target, Item $itemInHand){
        $this->player = $player;
        $this->target = $target;
        $this->item = $itemInHand;
    }
    
    /**
     * @return Item
     */
    public function getItem(){
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item){
        $this->item = $item;
    }

    /**
     * @return Block
     */
    public function getBlock(){
        return $this->target;
    }
    
}