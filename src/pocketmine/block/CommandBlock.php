<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

use pocketmine\inventory\CommandBlockInventory;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;

class CommandBlock extends Solid{
	
    protected $id = self::COMMAND_BLOCK;

    public function __construct($meta = 0){
        $this->meta = $meta;
    }
    
    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null) {
		$this->getLevel()->setBlock($block, $this, true, true);
		$nbt = new CompoundTag("", [
			new StringTag("id", Tile::COMMAND_BLOCK),
			new IntTag("x", $this->x),
			new IntTag("y", $this->y),
			new IntTag("z", $this->z),
			new IntTag("command", $this->getCommand())
		]);

		if ($item->hasCustomName()) {
			$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
		}

		if ($item->hasCustomBlockData()) {
			foreach ($item->getCustomBlockData() as $key => $v) {
				$nbt->{$key} = $v;
			}
		}

		Tile::createTile(Tile::COMMAND_BLOCK, $this->getLevel(), $nbt);

		return true;
	}
	
	public function onActivate(Item $item, Player $player = null) {
		if ($player instanceof Player) {
			$tile = $this->getLevel()->getTile($this);
			$enchantTable = null;
			if ($tile instanceof CommandBlock) {
				$enchantTable = $tile;
			} else {
				$this->getLevel()->setBlock($this, $this, true, true);
				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::COMMAND_BLOCK),
					new IntTag("x", $this->x),
					new IntTag("y", $this->y),
					new IntTag("z", $this->z),
					new IntTag("command", $this->getCommand())
				]);
				if ($item->hasCustomName()) {
					$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
				}
				if ($item->hasCustomBlockData()) {
					foreach ($item->getCustomBlockData() as $key => $v) {
						$nbt->{$key} = $v;
					}
				}
				$commandBlock = Tile::createTile(Tile::COMMAND_BLOCK, $this->getLevel(), $nbt);
			}
			$player->addWindow(new CommandBlockInventory($this));
		}
		return true;
	}
	
	/*public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$this->getServer()->dispatchCommand(""); //TODO
		}

		return false;
	}*/
	
	public function getCommand(){
        return null; //TODO
    }
    
    public function canBeActivated(){
        return true;
    }

    public function getName(){
        return "Command Block";
    }

    public function getHardness(){
        return -1;
    }

}
