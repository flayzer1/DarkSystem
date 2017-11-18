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

class WoodenShovel extends Tool{
	
	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::WOODEN_SHOVEL, $meta, $count, "Wooden Shovel");
	}

	public function isShovel(){
		return Tool::TIER_WOODEN;
	}
}