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
use pocketmine\Player;

class RedSandstoneSlab extends Slab
{
    protected $id = Block::RED_SANDSTONE_SLAB;

    public function getName()
    {
        return "Red Sandstone Slab";
    }

    public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null)
    {
        if ($face === 0) {
            if ($target->getId() === self::RED_SANDSTONE_SLAB and ($target->getDamage() & 0x08) === 0x08) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::RED_SANDSTONE_SLAB) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);

                return true;
            } else {
                $this->meta |= 0x08;
            }
        } elseif ($face === 1) {
            if ($target->getId() === self::RED_SANDSTONE_SLAB and ($target->getDamage() & 0x08) === 0) {
                $this->getLevel()->setBlock($target, Block::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);

                return true;
            } elseif ($block->getId() === self::RED_SANDSTONE_SLAB) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);

                return true;
            }
        } else {
            if ($block->getId() === self::RED_SANDSTONE_SLAB) {
                $this->getLevel()->setBlock($block, Block::get(Item::DOUBLE_RED_SANDSTONE_SLAB, $this->meta), true);
            } else {
                if ($fy > 0.5) {
                    $this->meta |= 0x08;
                }
            }
        }

        if ($block->getId() === self::RED_SANDSTONE_SLAB and ($target->getDamage() & 0x07) !== ($this->meta & 0x07)) {
            return false;
        }
        
        $this->getLevel()->setBlock($block, $this, true, true);
        
        return true;
    }
}