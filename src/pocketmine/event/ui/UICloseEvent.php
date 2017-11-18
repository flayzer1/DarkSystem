<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\ui;

use pocketmine\network\protocol\DataPacket;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class UICloseEvent extends UIEvent implements Cancellable{

	public static $handlerList = null;

	public function __construct(Player $player, DataPacket $packet){
		parent::__construct($player, $packet);
	}
	
}