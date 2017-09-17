<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\item;

use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\entity\Minecart as MinecartEntity;

class Minecart extends Item{
	
    public function __construct($meta = 0, $count = 1){
        parent::__construct(self::MINECART, $meta, $count, "Minecart");
    }

    public function canBeActivated(){
        return true;
    }

    public function onActivate(Level $level, Player $player, Block $block, Block $target, $face, $fx, $fy, $fz){
        $minecart = new MinecartEntity($player->getLevel(), new Compound("", [
            "Pos" => new Enum("Pos", [
                new DoubleTag("", $block->getX()),
                new DoubleTag("", $block->getY() + 0.8),
                new DoubleTag("", $block->getZ())
            ]),
            "Motion" => new Enum("Motion", [
                new DoubleTag("", 0),
                new DoubleTag("", 0),
                new DoubleTag("", 0)
            ]),
            "Rotation" => new Enum("Rotation", [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ]),
        ]));
        
        $minecart->spawnToAll();

        if($player->isSurvival() or $player->isAdventure()){
            $item = $player->getInventory()->getItemInHand();
            $count = $item->getCount();
            if(--$count <= 0){
                $player->getInventory()->setItemInHand(Item::get(Item::AIR));
                return true;
            }

            $item->setCount($count);
            $player->getInventory()->setItemInHand($item);
        }

        return true;
    }
}
