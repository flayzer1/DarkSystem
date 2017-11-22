<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\inventory;

use pocketmine\block\Block;
use pocketmine\inventory\customInventory\CustomChestInventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Tile;
use pocketmine\Player;

class InventoryAPI{
	
    /**
     * @param Player $player
     * @param bool $autoOpen
     * @return CustomChestInventory
     */
    public function createInventory(Player $player, $name, $autoOpen = false){
        $tile = Tile::createTile("CustomChest", $level = $player->getLevel(), new CompoundTag("", [
            new StringTag("id", Tile::CHEST),
            new StringTag("CustomName", $name),
            new IntTag("x", (int) $player->x),
            new IntTag("y", (int) $player->y + 3),
            new IntTag("z", (int) $player->z),
        ]));
        
        $block = Block::get(Block::CHEST);
        $block->x = (int) $tile->x;
        $block->y = (int) $tile->y;
        $block->z = (int) $tile->z;
        $block->level = $level;
        $block->level->sendBlocks([$player], [$block]);
        $inventory = new CustomChestInventory($tile);
        $tile->spawnTo($player);
        
        if($autoOpen){
            $player->addWindow($inventory, 15);
        }
        
        return $inventory;
    }

    public function getInventory(Inventory $inventory){
        if($inventory instanceof CustomChestInventory) return $inventory;
        return null;
    }
    
}
