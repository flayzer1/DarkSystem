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

use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerReceiptsReceivedEvent extends PlayerEvent implements Cancellable{
	
	public static $handlerList = null;
	
	/** @var string[] */
	protected $receipts = [];
	
	/**
	 * @param Player $player
	 * @param string[] $receipts
	 */
	public function __construct(Player $player, $receipts){
		if(!is_array($receipts)){
			throw new Exception("$receipts whould be is array type");
		}
		
		$this->player = $player;
		$this->receipts = $receipts;
	}
	
	public function getReceipts(){
		return $this->receipts;
	}
	
}
