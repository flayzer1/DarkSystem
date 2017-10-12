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

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PlayerChangeSkinEvent extends PlayerEvent implements Cancellable{
	
	public static $handlerList = null;
	
	private $oldSkinName;
	private $newSkinName;
	
	public function __construct(Player $player, $oldSkinName, $newSkinName){
		$this->player = $player;
		$this->oldSkinName = $oldSkinName;
		$this->newSkinName = $newSkinName;
	}
	
	public function getOldSkinName(){
		return $this->oldSkinName;
	}
	
	public function getNewSkinName(){
		return $this->newSkinName;
	}
	
	public function setNewSkin($newSkinName){
		if(!$newSkinName->isValid()){
			throw new \InvalidArgumentException("Skin format is invalid");
		}

		$this->newSkin = $newSkinName;
	}

}