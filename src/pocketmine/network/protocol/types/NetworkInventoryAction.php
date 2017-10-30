<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol\types;

use pocketmine\inventory\transaction\action\CraftingTakeResultAction;
use pocketmine\inventory\transaction\action\CraftingTransferMaterialAction;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\network\protocol\InventoryTransactionPacket;
use pocketmine\Player;

class NetworkInventoryAction{
	
	const SOURCE_CONTAINER = 0;

	const SOURCE_WORLD = 2;
	const SOURCE_CREATIVE = 3;
	const SOURCE_TODO = 99999;
	
	const SOURCE_TYPE_CRAFTING_ADD_INGREDIENT = -2;
	const SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT = -3;
	const SOURCE_TYPE_CRAFTING_RESULT = -4;
	const SOURCE_TYPE_CRAFTING_USE_INGREDIENT = -5;

	const SOURCE_TYPE_ANVIL_INPUT = -10;
	const SOURCE_TYPE_ANVIL_MATERIAL = -11;
	const SOURCE_TYPE_ANVIL_RESULT = -12;
	const SOURCE_TYPE_ANVIL_OUTPUT = -13;

	const SOURCE_TYPE_ENCHANT_INPUT = -15;
	const SOURCE_TYPE_ENCHANT_MATERIAL = -16;
	const SOURCE_TYPE_ENCHANT_OUTPUT = -17;

	const SOURCE_TYPE_TRADING_INPUT_1 = -20;
	const SOURCE_TYPE_TRADING_INPUT_2 = -21;
	const SOURCE_TYPE_TRADING_USE_INPUTS = -22;
	const SOURCE_TYPE_TRADING_OUTPUT = -23;

	const SOURCE_TYPE_BEACON = -24;
	
	const SOURCE_TYPE_CONTAINER_DROP_CONTENTS = -100;

	const ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM = 0;
	const ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM = 1;

	const ACTION_MAGIC_SLOT_DROP_ITEM = 0;
	const ACTION_MAGIC_SLOT_PICKUP_ITEM = 1;

	/** @var int */
	public $sourceType;
	/** @var int */
	public $windowId = ContainerIds::NONE;
	/** @var int */
	public $unknown = 0;
	/** @var int */
	public $inventorySlot;
	/** @var Item */
	public $oldItem;
	/** @var Item */
	public $newItem;
	
	public function read(InventoryTransactionPacket $packet){
		$this->sourceType = $packet->getUnsignedVarInt();

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$this->windowId = $packet->getVarInt();
				break;
			case self::SOURCE_WORLD:
				$this->unknown = $packet->getUnsignedVarInt();
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$this->windowId = $packet->getVarInt();
				switch($this->windowId){
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
					case self::SOURCE_TYPE_CRAFTING_RESULT:
						$packet->isCraftingPart = true;
						break;
				}
				
				break;
		}

		$this->inventorySlot = $packet->getUnsignedVarInt();
		$this->oldItem = $packet->getSlot();
		$this->newItem = $packet->getSlot();

		return $this;
	}
	
	public function write(InventoryTransactionPacket $packet){
		$packet->putVarInt($this->sourceType);

		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				$packet->putSignedVarInt($this->windowId);
				break;
			case self::SOURCE_WORLD:
				$packet->putVarInt($this->unknown);
				break;
			case self::SOURCE_CREATIVE:
				break;
			case self::SOURCE_TODO:
				$packet->putVarInt($this->windowId);
				break;
		}

		$packet->putSignedVarInt($this->inventorySlot);
		$packet->putSlot($this->oldItem);
		$packet->putSlot($this->newItem);
	}
	
	public function createInventoryAction(Player $player){
		switch($this->sourceType){
			case self::SOURCE_CONTAINER:
				if($this->windowId === ContainerIds::ARMOR){
					$this->inventorySlot += 36;
					$this->windowId = ContainerIds::INVENTORY;
				}

				$window = $player->getWindow($this->windowId);
				if($window !== null){
					return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
				}

				return null;
			case self::SOURCE_WORLD:
				if($this->inventorySlot === self::ACTION_MAGIC_SLOT_DROP_ITEM){
					return new DropItemAction($this->oldItem, $this->newItem);
				}

				return null;
			case self::SOURCE_CREATIVE:
				switch($this->inventorySlot){
					case self::ACTION_MAGIC_SLOT_CREATIVE_DELETE_ITEM:
						$type = CreativeInventoryAction::TYPE_DELETE_ITEM;
						break;
					case self::ACTION_MAGIC_SLOT_CREATIVE_CREATE_ITEM:
						$type = CreativeInventoryAction::TYPE_CREATE_ITEM;
						break;
					default:
						return null;
				}

				return new CreativeInventoryAction($this->oldItem, $this->newItem, $type);
			case self::SOURCE_TODO:
				switch($this->windowId){
					case self::SOURCE_TYPE_CRAFTING_ADD_INGREDIENT:
					case self::SOURCE_TYPE_CRAFTING_REMOVE_INGREDIENT:
						$window = $player->getCraftingGrid();
						return new SlotChangeAction($window, $this->inventorySlot, $this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_RESULT:
						return new CraftingTakeResultAction($this->oldItem, $this->newItem);
					case self::SOURCE_TYPE_CRAFTING_USE_INGREDIENT:
						return new CraftingTransferMaterialAction($this->oldItem, $this->newItem, $this->inventorySlot);
					case self::SOURCE_TYPE_CONTAINER_DROP_CONTENTS:
						$window = $player->getCraftingGrid();
						
						$inventorySlot = $window->first($this->oldItem, true);
						if($inventorySlot === -1){
							return null;
						}
						
						return new SlotChangeAction($window, $inventorySlot, $this->oldItem, $this->newItem);
				}
				
				return null;
		}

		return null;
	}

}
