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
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class UIDataReceiveEvent extends UIEvent{

	public static $handlerList = null;

	public function __construct(Plugin $plugin, DataPacket $packet, Player $player){
		parent::__construct($plugin, $packet, $player);
	}

	public function getData(){
		return json_decode($this->packet->formData);
	}

	public function getDataEncoded(){
		return $this->packet->formData;
	}
}