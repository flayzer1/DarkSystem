<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\multiversion;

use pocketmine\Player;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\PlayerInventory120;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\network\protocol\ContainerSetSlotPacket;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\v120\InventoryContentPacket;
use pocketmine\network\protocol\v120\InventorySlotPacket;

abstract class Multiversion{
	
	public static function getPlayerInventory($player){
		if($player->getPlayerProtocol() >= ProtocolInfo::PROTOCOL_120){
			return new PlayerInventory120($player);
		}else{
			return new PlayerInventory($player);
		}
	}
	
	public static function sendContainer($player, $windowId, $items){
		if($player->getPlayerProtocol() >= ProtocolInfo::PROTOCOL_120){
			$pk = new InventoryContentPacket();
			$pk->inventoryID = $windowId;
			$pk->items = $items;
		}else{
			$pk = new ContainerSetContentPacket();
			$pk->windowid = $windowId;
			$pk->slots = $items;
			$pk->eid = $player->getId();
		}
		
		$player->dataPacket($pk);
	}
	
	public static function sendContainerSlot($player, $windowId, $item, $slot){
		if($player->getPlayerProtocol() >= ProtocolInfo::PROTOCOL_120){
			$pk = new InventorySlotPacket();
			$pk->containerId = $windowId;
			$pk->item = $item;
			$pk->slot = $slot;
		}else{
			$pk = new ContainerSetSlotPacket();
			$pk->windowid = $windowId;
			$pk->item = $item;
			$pk->slot = $slot;
		}
		
		$player->dataPacket($pk);
	}
	
}
