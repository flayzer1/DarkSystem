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

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\event\Cancellable;

class PlayerEnchantEvent extends PlayerEvent{
	
	public static $handlerList = null;
    
	private $enchantment;
	
	/**
	 * @param Item $item
	 */
	public function __construct(Item $item, Enchantment $enchantment){
		$this->item = $item;
        $this->enchantment = $enchantment;
		$this->player = null;
		
	}
	
	/**
	 * @return Enchantment
	 */
	public function getEnchantment(){
	return $this->enchantment;
	}
    
	public function getItem(){
		return $this->item;
	}
}
