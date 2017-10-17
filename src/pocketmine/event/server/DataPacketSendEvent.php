<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\server;

use pocketmine\event;
use pocketmine\event\Cancellable;
use pocketmine\network\protocol\DataPacket;
use pocketmine\Player;

class DataPacketSendEvent extends ServerEvent implements Cancellable{
	
	public static $handlerList = null;

	private $packet;
	private $player;

	public function __construct(Player $player, DataPacket $packet){
		$this->packet = $packet;
		$this->player = $player;
	}

	public function getPacket(){
		return $this->packet;
	}

	public function getPlayer(){
		return $this->player;
	}
	
}
