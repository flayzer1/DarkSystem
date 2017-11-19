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
use pocketmine\level\Explosion;
use pocketmine\level\Level;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Bed as TileBed;
use pocketmine\tile\Tile;
use pocketmine\Translate;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Bed extends Transparent{

	/**
	 * @var int
	 */
	protected $id = self::BED_BLOCK;

	/**
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return bool
	 */
	public function canBeActivated(){
		return true;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 0.3;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Bed Block";
	}

	/**
	 * @return AxisAlignedBB
	 */
	protected function recalculateBoundingBox(){
		return new AxisAlignedBB(
			$this->x,
			$this->y,
			$this->z,
			$this->x + 1,
			$this->y + 0.5625,
			$this->z + 1
		);
	}

	/**
	 * @param Item        $item
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function onActivate(Item $item, Player $player = null){
		$time = $this->getLevel()->getTime() % Level::TIME_FULL;

		$isNight = ($time >= Level::TIME_NIGHT and $time < Level::TIME_SUNRISE);

		if($player instanceof Player and !$isNight){
			if(Translate::checkTurkish() === "yes"){
				$player->sendMessage(TextFormat::GRAY . "Sadece Geceleri Uyuyabilirsiniz");
			}else{
				$player->sendMessage(TextFormat::GRAY . "You can only sleep at night");
			}
			
			return true;
		}

		$blockNorth = $this->getSide(2);
		$blockSouth = $this->getSide(3);
		$blockEast = $this->getSide(5);
		$blockWest = $this->getSide(4);
		if(($this->meta & 0x08) === 0x08){
			$b = $this;
		}else{
			if($blockNorth->getId() === $this->id and ($blockNorth->meta & 0x08) === 0x08){
				$b = $blockNorth;
			}elseif($blockSouth->getId() === $this->id and ($blockSouth->meta & 0x08) === 0x08){
				$b = $blockSouth;
			}elseif($blockEast->getId() === $this->id and ($blockEast->meta & 0x08) === 0x08){
				$b = $blockEast;
			}elseif($blockWest->getId() === $this->id and ($blockWest->meta & 0x08) === 0x08){
				$b = $blockWest;
			}else{
				if($player instanceof Player){
					if(Translate::checkTurkish() === "yes"){
						$player->sendMessage(TextFormat::GRAY . "Yatak Tam Değil");
					}else{
						$player->sendMessage(TextFormat::GRAY . "This bed is incomplete");
					}
				}

				return true;
			}
		}

		if($player instanceof Player and $player->sleepOn($b) === false){
			if(Translate::checkTurkish() === "yes"){
				$player->sendMessage(TextFormat::GRAY . "Yatak Dolu");
			}else{
				$player->sendMessage(TextFormat::GRAY . "This bed is occupied");
			}
		}

		return true;
	}

	/**
	 * @param Item        $item
	 * @param Block       $block
	 * @param Block       $target
	 * @param int         $face
	 * @param float       $fx
	 * @param float       $fy
	 * @param float       $fz
	 * @param Player|null $player
	 *
	 * @return bool
	 */
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(0);
		if($down->isTransparent() === false){
			$faces = [
				0 => 3,
				1 => 4,
				2 => 2,
				3 => 5,
			];
			
			$d = $player instanceof Player ? $player->getDirection() : 0;
			$next = $this->getSide($faces[(($d + 3) % 4)]);
			$downNext = $this->getSide(0);
			if($next->canBeReplaced() === true and $downNext->isTransparent() === false){
				$meta = (($d + 3) % 4) & 0x03;
				$this->getLevel()->setBlock($block, Block::get($this->id, $meta), true, true);
				$this->getLevel()->setBlock($next, Block::get($this->id, $meta | 0x08), true, true);

				$nbt = new CompoundTag("", [
					new StringTag("id", Tile::BED),
					new ByteTag("color", $item->getDamage() & 0x0f),
					new IntTag("x", $block->x),
					new IntTag("y", $block->y),
					new IntTag("z", $block->z),
				]);
				
				$nbt2 = clone $nbt;
				$nbt2["x"] = $next->x;
				$nbt2["z"] = $next->z;
				Tile::createTile(Tile::BED, $this->getLevel(), $nbt);
				Tile::createTile(Tile::BED, $this->getLevel(), $nbt2);

				return true;
			}
		}

		return false;
	}

	/**
	 * @param Item $item
	 *
	 * @return bool
	 */
	public function onBreak(Item $item){
		$sides = [
			0 => 3,
			1 => 4,
			2 => 2,
			3 => 5,
			8 => 2,
			9 => 5,
			10 => 3,
			11 => 4,
		];

		if(($this->meta & 0x08) === 0x08){
			$next = $this->getSide($sides[$this->meta]);
			if($next->getId() === $this->id and ($next->meta | 0x08) === $this->meta){
				$this->getLevel()->setBlock($next, new Air(), true, true);
			}
		}else{
			$next = $this->getSide($sides[$this->meta]);
			if($next->getId() === $this->id and $next->meta === ($this->meta | 0x08)){
				$this->getLevel()->setBlock($next, new Air(), true, true);
			}
		}
		
		$this->getLevel()->setBlock($this, new Air(), true, true);

		return true;
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item) : array{
		$tile = $this->getLevel()->getTile($this);
		if($tile instanceof TileBed){
			return [
				[Item::BED, $tile->getColor(), 1]
			];
		}else{
			return [
				[Item::BED, 14, 1]
			];
		}
	}

	/**
	 * @return int
	 */
	public function getVariantBitmask(){
		return 0x08;
	}

}
