<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\inventory\EnderChestInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item as ItemItem;
use pocketmine\utils\UUID;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\protocol\RemoveEntityPacket;
use pocketmine\network\multiversion\Multiversion;
use pocketmine\level\Level;
use pocketmine\Player;

class Human extends Creature implements ProjectileSource, InventoryHolder{
	
	const INVENTORY_CLASSIC = 0;
    const INVENTORY_POCKET = 1;
    
	const SURVIVAL = 0;
	const CREATIVE = 1;
	const ADVENTURE = 2;
	const SPECTATOR = 3;
	const VIEW = 3;
	
	const CRAFTING_DEFAULT = 0;
	const CRAFTING_WORKBENCH = 1;
	const CRAFTING_ANVIL = 2;
	const CRAFTING_ENCHANT = 3;

	const SURVIVAL_SLOTS = 42;
	const CREATIVE_SLOTS = 112;
	
	const DEFAULT_SPEED = 0.1;
	const MAXIMUM_SPEED = 0.5;
	
	const MIN_WINDOW_ID = 2;
	
	const MAX_EXPERIENCE = 2147483648;
	const MAX_EXPERIENCE_LEVEL = 21863;
	
	protected $inventory;
	protected $uuid;
	protected $rawUUID;
	protected $nameTag = "TESTIFICATE";
	
	public $width = 0.5; //Default: 0.6
	public $length = 0.5; //and This
	public $height = 1.7; //Default: 1.8
	public $eyeHeight = 1.62;
	
	protected $skinName = "Standard_Custom";
	//protected $skinId;
	protected $skin;
	protected $skinGeometryName = "geometry.humanoid.custom";
	//protected $skinGeometryId = "";
	protected $skinGeometryData = "";
	protected $capeData = "";
	
	protected $totalXp = 0;
	protected $xpSeed;
	protected $xpCooldown = 0;
	
	public function getSkinName(){
		return $this->skinName;
	}
	
	/*public function getSkinId(){
		return $this->skinId;
	}*/
	
	public function getSkinData(){
		return $this->skin;
	}
	
	public function getSkinGeometryName(){
		return $this->skinGeometryName;
	}
	
	/*public function getSkinGeometryId(){
		return $this->skinGeometryId;
	}*/
	
	public function getSkinGeometryData(){
		return $this->skinGeometryData;
	}
	
	public function getCapeData(){
		return $this->capeData;
	}
	
	/**
	 * @return UUID|null
	 */
	public function getUniqueId(){
		return $this->uuid;
	}

	/**
	 * @return string
	 */
	public function getRawUniqueId(){
		return $this->rawUUID;
	}
	
	public function getLeftShoulderEntity(){
		return 0; //Parrot? (30)
	}
	
	public function getRightShoulderEntity(){
		return 0; //Parrot? (30)
	}
	
	/**
	 * @param string $str
	 * @param string $skinId
	 * @param bool   $skinName
	 */
	public function setSkin($str, /*$skinId, */$skinName, $skinGeometryName = "", /*$skinGeometryId = "", */$skinGeometryData = "", $capeData = ""){
		$this->skin = $str;
		//$this->skinId = $skinId;
		if(is_string($skinName)){
			$this->skinName = $skinName;
		}
		
		if(!empty($skinGeometryName)){
			$this->skinGeometryName = $skinGeometryName;
		}
		
		/*if(!empty($skinGeometryId)){
			$this->skinGeometryId = $skinGeometryId;
		}*/
		
		if(!empty($skinGeometryData)){
			$this->skinGeometryData = $skinGeometryData;
		}
		
		if(!empty($capeData)){
			$this->capeData = $capeData;
		}
	}
	
	public function getInventory(){
		return $this->inventory;
	}
	
	public function getEnderChestInventory(){
		return $this->enderChestInventory;
	}
	
	protected function initEntity(){
		$this->setDataFlag(Human::DATA_PLAYER_FLAGS, Human::DATA_PLAYER_FLAG_SLEEP, false);
		$this->setDataProperty(Human::DATA_PLAYER_BED_POSITION, Human::DATA_TYPE_POS, [0, 0, 0]);
		if($this instanceof Player){
			$this->inventory = Multiversion::getPlayerInventory($this);
			$this->addWindow($this->inventory, 0);
		}else{
			$this->inventory = new PlayerInventory($this);
		}
		$this->enderChestInventory = new EnderChestInventory($this, ($this->namedtag->EnderChestInventory ?? null));
		if(!($this instanceof Player)){
			if(isset($this->namedtag->NameTag)){
				$this->setNameTag($this->namedtag["NameTag"]);
			}
			if(isset($this->namedtag->Skin) && $this->namedtag->Skin instanceof CompoundTag){
				$this->setSkin($this->namedtag->Skin["Data"], /*$this->namedtag->Skin["Name"], */$this->namedtag->Skin["Slim"] > 0);
			}
			$this->uuid = UUID::fromData($this->getId(), $this->getSkinData(), $this->getNameTag());
		}
		if(isset($this->namedtag->Inventory) && $this->namedtag->Inventory instanceof ListTag){
			foreach($this->namedtag->Inventory as $item){
				if($item["Slot"] >= 0 && $item["Slot"] < 9){
					$this->inventory->setHotbarSlotIndex($item["Slot"], isset($item["TrueSlot"]) ? $item["TrueSlot"] : -1);
				}elseif($item["Slot"] >= 100 && $item["Slot"] < 104){
					$this->inventory->setItem($this->inventory->getSize() + $item["Slot"] - 100, NBT::getItemHelper($item));
				}else{
					$this->inventory->setItem($item["Slot"] - 9, NBT::getItemHelper($item));
				}
			}
		}
		parent::initEntity();
	}

	public function getName(){
		return $this->getNameTag();
	}

	public function getDrops(){
		return $this->inventory !== null ? array_values($this->inventory->getContents()) : [];
	}

	public function saveNBT(){
		parent::saveNBT();
		
		$this->namedtag->Inventory = new ListTag("Inventory", []);
		$this->namedtag->EnderChestInventory = new ListTag("EnderChestInventory", []);
		$this->namedtag->Inventory->setTagType(NBT::TAG_Compound);
		$this->namedtag->EnderChestInventory->setTagType(NBT::TAG_Compound);
		
		if($this->inventory !== null){
			for($slot = 0; $slot < 9; ++$slot){
				$hotbarSlot = $this->inventory->getHotbarSlotIndex($slot);
				if($hotbarSlot !== -1){
					$item = $this->inventory->getItem($hotbarSlot);
					if($item->getId() !== ItemItem::AIR && $item->getCount() > 0){
						$this->namedtag->Inventory[$slot] = NBT::putItemHelper($item, $slot);
						$this->namedtag->Inventory[$slot]->TrueSlot = new ByteTag("TrueSlot", $hotbarSlot);
						continue;
					}
				}
				
				$this->namedtag->Inventory[$slot] = NBT::putItemHelper(ItemItem::get(ItemItem::AIR), $slot);
				$this->namedtag->Inventory[$slot]->TrueSlot = new ByteTag("TrueSlot", -1);
			}
			
			$slotCount = Player::SURVIVAL_SLOTS + 9;
			for($slot = 9; $slot < $slotCount; ++$slot){
				$item = $this->inventory->getItem($slot - 9);
				$this->namedtag->Inventory[$slot] = NBT::putItemHelper($item, $slot);
			}
			
			for($slot = 100; $slot < 104; ++$slot){
				$item = $this->inventory->getItem($this->inventory->getSize() + $slot - 100);
				if($item instanceof ItemItem && $item->getId() !== ItemItem::AIR){
					$this->namedtag->Inventory[$slot] = NBT::putItemHelper($item, $slot);
				}
			}
			
			if($this->enderChestInventory !== null){
				for($slot = 0; $slot < $this->enderChestInventory->getSize(); $slot++){
					if(($item = $this->enderChestInventory->getItem($slot)) instanceof ItemItem){
						$this->namedtag->EnderChestInventory[$slot] = NBT::putItemHelper($item, $slot);
					}
				}
			}
		}
	}

	public function spawnTo(Player $player){
		if($player !== $this && !isset($this->hasSpawned[$player->getId()]) && isset($player->usedChunks[Level::chunkHash($this->chunk->getX(), $this->chunk->getZ())])){
			$this->hasSpawned[$player->getId()] = $player;
			
			//$name = ($this instanceof Player) ? $this->getDisplayName() : $this->getName();
			$xuid = ($this instanceof Player) ? $this->getXUID() : "";
			$this->server->updatePlayerListData($this->getUniqueId(), $this->getId(), /*$name, */$this->getName(), $this->skinName, /*$this->skinId, */$this->skin, $this->skinGeometryName, /*$this->skinGeometryId, */$this->skinGeometryData, $this->capeData, $xuid, [$player]);
			
			$pk = new AddPlayerPacket();
			$pk->uuid = $this->getUniqueId();
			$pk->username = $this->getName();
			$pk->eid = $this->getId();
			$pk->x = $this->x;
			$pk->y = $this->y;
			$pk->z = $this->z;
			$pk->speedX = $this->motionX;
			$pk->speedY = $this->motionY;
			$pk->speedZ = $this->motionZ;
			$pk->yaw = $this->yaw;
			$pk->pitch = $this->pitch;
			$pk->item = $this->inventory->getItemInHand();
			$pk->metadata = $this->dataProperties;
			$player->dataPacket($pk);

			$this->inventory->sendArmorContents($player);
			$this->level->addPlayerHandItem($this, $player);

			if(!($this instanceof Player)){
				$this->server->removePlayerListData($this->getUniqueId(), [$player]);
			}
		}
	}

	public function despawnFrom(Player $player){
		if(isset($this->hasSpawned[$player->getId()])){
			$pk = new RemoveEntityPacket();
			$pk->eid = $this->getId();
			$player->dataPacket($pk);
			unset($this->hasSpawned[$player->getId()]);
		}
	}

	public function close(){
		if(!$this->closed){
			if(!($this instanceof Player) || $this->loggedIn){
				foreach($this->inventory->getViewers() as $viewer){
					$viewer->removeWindow($this->inventory);
				}
			}
			
			parent::close();
		}
	}
	
	public function isNeedSaveOnChunkUnload(){
		return true;
	}
	
}
