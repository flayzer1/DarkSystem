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

use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\Player;

class CustomChestInventory extends ChestInventory{
	
    public function __construct(CustomChest $tile){
        parent::__construct($tile);
    }
    
    public function onOpen(Player $who){
        parent::onOpen($who);
    }
    
    public function onClose(Player $who){
        $this->holder->sendReplacement($who);
        
        parent::onClose($who);
        
        $this->holder->close();
    }

}
