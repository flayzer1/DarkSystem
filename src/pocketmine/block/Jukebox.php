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

use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\item\MusicDisc;
use pocketmine\tile\Jukebox as JukeboxTile;
use pocketmine\network\protocol\LevelSoundEventPacket;

class Jukebox extends Solid{

	protected $id = self::JUKEBOX;

	/**
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return float
	 */
	public function getHardness(){
		return 2;
	}

	/**
	 * @return string
	 */
	public function getName(){
		return "Jukebox";
	}

	/**
	 * @return int
	 */
	public function getToolType(){
		return Tool::TYPE_AXE;
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
		$faces = [
			0 => 4,
			1 => 2,
			2 => 5,
			3 => 3,
		];
		
		$this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];
		$this->getLevel()->setBlock($block, $this, true, true);
		
		$tile = JukeboxTile::createTileFromPosition("jukebox", $this);

		return true;
	}
	
	public function playMusicDisc(Player $p = null){
		$record = $this->getRecord();
		if($record == 0) return false;
		
		$soundId = 90 + ($record - 2256);
		$this->level->broadcastLevelSoundEvent($this, $soundId);
		
		if($p instanceof Player){
			$name = $this->getMusicDisc()->getRecordName();
			$p->sendTip("§6Now Playing: §3C418 {$name}");
		}
		
		return true;
	}
	
	public function onActivate(Item $item, Player $p = null){
		if($item instanceof MusicDisc){
			$this->setRecord($item->getRecordId());
			$this->setRecordItem($item);
			$this->playMusicDisc($p);
		}else{
			$this->setRecord(0);
			$this->setRecordItem(null);
		}
	}
	
	public function setRecord($record){
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			if($tile->getRecord() > 0){
				$this->level->dropItem($this->add(0,1,0), $tile->getRecordItem());
			}
			
			$tile->setRecord($record);
		}
	}
	
	public function getRecord(){
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			return $tile->getRecord();
		}
		
		return 0;
	}
	
	public function getMusicDisc(){
		$tile = $this->level->getTile($this);
		if($tile instanceof JukeboxTile){
			return $tile->getRecordItem();
		}
		
		return null;
	}
}
