<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\MusicDisc;

class Jukebox extends Spawnable{

    protected $record = MusicDisc::NO_RECORD;
    protected $recordItem;

    public function __construct(Level $level, CompoundTag $nbt){
        if(isset($nbt->record)){
            $this->record = $nbt->record->getValue();
        }
        
        if(isset($nbt->recordItem)){
            $this->recordItem = NBT::getItemHelper($nbt->recordItem->getValue());
        }

        parent::__construct($level, $nbt);
    }
    
    public function getRecord(){
    	return $this->record;
    }
    
    public function setRecord($record){
    	if($record > 511 or $record < 500){
    		return false;
    	}
    
    	$this->record = $record;
    }
    
    public function getRecordItem(){
    	return $this->recordItem;
    }
    
    public function setRecordItem($item = null){
    	$this->recordItem = $item;
    }

    public function saveNBT(){
        parent::saveNBT();
        
        $this->namedtag->record = new IntTag("record", $this->record);
        $this->namedtag->recordItem = ($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize() : (Item::get(0))->nbtSerialize());
    }
    
    public function updateCompound(CompoundTag $nbt, Player $player){
        if($nbt["id"] !== Tile::JUKEBOX){
            return false;
        }
        
        $this->namedtag = $nbt;
    }
    
	public function getSpawnCompound(){
		return new CompoundTag("", [
			new StringTag("id", Tile::JUKEBOX),
			new IntTag("x", (int) $this->x),
			new IntTag("y", (int) $this->y),
			new IntTag("z", (int) $this->z),
			new IntTag("record", $this->record),
			($this->recordItem instanceof MusicDisc ? $this->recordItem->nbtSerialize() : (Item::get(0))->nbtSerialize())
		]);
	}
}
