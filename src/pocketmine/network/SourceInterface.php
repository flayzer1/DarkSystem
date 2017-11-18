<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network;

use pocketmine\network\protocol\DataPacket;
use pocketmine\Player;

interface SourceInterface{
	
	public function putPacket(Player $player, DataPacket $packet, $immediate = true);
	
	public function close(Player $player, $reason = "Unknown Reason");
	
	public function setName($name);
	
	public function process();

	public function shutdown();

	public function emergencyShutdown();

}
