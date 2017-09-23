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

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Purpur extends Solid
{

    protected $id = self::PURPUR;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getHardness()
    {
        return 1.5;
    }

    public function getToolType()
    {
        return Tool::TYPE_PICKAXE;
    }

    public function getName()
    {
        static $names = [
            0 => "Purpur Block",
            2 => "Purpur Pillar",
        ];

        return $names[$this->meta & 0x0f] ?? "Purpur Block";
    }

    public function getDrops(Item $item)
    {
        if($item->isPickaxe() >= Tool::TIER_WOODEN){
            return [
                [$this->id, $this->meta & 0x0f, 1],
            ];
        } else {
            return [];
        }
    }
}