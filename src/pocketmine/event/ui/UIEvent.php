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

use pocketmine\event\plugin\PluginEvent;
use pocketmine\network\protocol\DataPacket;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\network\protocol\v120\ModalFormResponsePacket;

abstract class UIEvent extends PluginEvent{

	public static $handlerList = null;

	/** @var DataPacket|ModalFormResponsePacket $packet */
	protected $packet;
	/** @var Player */
	protected $player;

	public function __construct(Plugin $plugin, DataPacket $packet, Player $player){
		$this->packet = $packet;
		$this->player = $player;
		
		parent::__construct($plugin);
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

}
