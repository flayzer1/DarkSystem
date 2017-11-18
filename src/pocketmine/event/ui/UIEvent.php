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

use pocketmine\event\Event;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\v120\ModalFormResponsePacket;
use pocketmine\Player;

abstract class UIEvent extends Event{

	public static $handlerList = null;

	/** @var DataPacket|ModalFormResponsePacket $packet */
	protected $packet;
	/** @var Player */
	protected $player;
	
	public function __construct(Player $player, DataPacket $packet){
		$this->player = $player;
		$this->packet = $packet;
	}

	public function getPacket(){
		return $this->packet;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getID(){
		return $this->packet->formId;
	}
	
	public function getFormId(){
		return $this->packet->formId;
	}
	
	public function getFormData(){
		return @json_decode($this->packet->data);
	}
	
}
