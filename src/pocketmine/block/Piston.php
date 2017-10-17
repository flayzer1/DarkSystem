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
use pocketmine\math\AxisAlignedBB;
use pocketmine\Player;

class Piston extends Solid{

	protected $id = self::PISTON;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}
	
	public function getName(){
        return "Piston";
	}
	
	protected function recalculateBoundingBox(){
		$damage = $this->getDamage();

		$f = 0.1875;

		if(($damage & 0x08) > 0){
			$bb = new AxisAlignedBB(
				$this->x,
				$this->y + 1 - $f,
				$this->z,
				$this->x + 1,
				$this->y + 1,
				$this->z + 1
			);
		}else{
			$bb = new AxisAlignedBB(
				$this->x,
				$this->y,
				$this->z,
				$this->x + 1,
				$this->y + $f,
				$this->z + 1
			);
		}

		if(($damage & 0x04) > 0){
			if(($damage & 0x03) === 0){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z + 1 - $f,
					$this->x + 1,
					$this->y + 1,
					$this->z + 1
				);
			}elseif(($damage & 0x03) === 1){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z,
					$this->x + 1,
					$this->y + 1,
					$this->z + $f
				);
			}
			if(($damage & 0x03) === 2){
				$bb->setBounds(
					$this->x + 1 - $f,
					$this->y,
					$this->z,
					$this->x + 1,
					$this->y + 1,
					$this->z + 1
				);
			}
			if(($damage & 0x03) === 3){
				$bb->setBounds(
					$this->x,
					$this->y,
					$this->z,
					$this->x + $f,
					$this->y + 1,
					$this->z + 1
				);
			}
		}

		return $bb;
	}
	
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$directions = [
			0 => 1,
			1 => 3,
			2 => 0,
			3 => 2
		];
		
		if($player !== null){
			$this->meta = $directions[$player->getDirection() & 0x03];
		}
		
		if(($fy > 0.5 and $face !== self::SIDE_UP) or $face === self::SIDE_DOWN){
			$this->meta |= 0b00000100;
		}
		
		$this->getLevel()->setBlock($block, $this, true, true);
		
		return true;
	}
	
	public function onActivate(Item $item, Player $player = null){
		$block = $this->y + 1;
		$newBlock = $block + 1;
		if($block instanceof Block and $newBlock instanceof Block){
			$this->getLevel()->setBlock($block, Item::get(0), true, true);
			$this->getLevel()->setBlock($newBlock, $block, true, true);
			return true;
		}
	}
	
	public function canBeActivated(){
		return true;
	}
	
	public function getToolType(){
		return Tool::TYPE_PICKAXE;
	}
	
}