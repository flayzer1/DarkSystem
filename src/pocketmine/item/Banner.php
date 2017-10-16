<?php

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\StringTag;

class Banner extends Item{

	public function __construct($meta = 0){
		$this->block = Block::get(Block::STANDING_BANNER);
		
		parent::__construct(self::BANNER, $meta, "Banner");
	}

	public function getMaxStackSize(){
		return 16;
	}
	
	public function getBaseColor(){
		return $this->getNamedTag()->Base->getValue();
	}
	
	public function setBaseColor($color){
		$namedTag = $this->getNamedTag();
		$namedTag->Base->setValue($color & 0x0f);
		$this->setNamedTag($namedTag);
	}
	
	public function addPattern($pattern, $color){
		$patternId = 0;
		if($this->getPatternCount() !== 0){
			$patternId = max($this->getPatternIds()) + 1;
		}

		$namedTag = $this->getNamedTag();
		$namedTag->Patterns->{$patternId} = new Compound("", [
			new IntTag("Color", $color & 0x0f),
			new StringTag("Pattern", $pattern)
		]);

		$this->setNamedTag($namedTag);
		return $patternId;
	}
	
	public function patternExists($patternId){
		$this->correctNBT();
		return isset($this->getNamedTag()->Patterns->{$patternId});
	}
	
	public function getPatternData($patternId){
		if(!$this->patternExists($patternId)){
			return [];
		}

		return [
			"Color" => $this->getNamedTag()->Patterns->{$patternId}->Color->getValue(),
			"Pattern" => $this->getNamedTag()->Patterns->{$patternId}->Pattern->getValue()
		];
	}
	
	public function changePattern($patternId, $pattern, $color){
		if(!$this->patternExists($patternId)){
			return true;
		}

		$namedTag = $this->getNamedTag();
		$namedTag->Patterns->{$patternId}->setValue([
			new IntTag("Color", $color & 0x0f),
			new StringTag("Pattern", $pattern)
		]);

		$this->setNamedTag($namedTag);
		return true;
	}
	
	public function deletePattern($patternId){
		if(!$this->patternExists($patternId)){
			return true;
		}

		$namedTag = $this->getNamedTag();
		unset($namedTag->Patterns->{$patternId});
		$this->setNamedTag($namedTag);

		return true;
	}
	
	public function deleteTopPattern(){
		$keys = $this->getPatternIds();
		if(empty($keys)){
			return true;
		}

		$index = max($keys);
		$namedTag = $this->getNamedTag();
		unset($namedTag->Patterns->{$index});
		$this->setNamedTag($namedTag);
		return true;
	}
	
	public function getPatternIds(){
		$this->correctNBT();

		$keys = array_keys((array) $this->getNamedTag()->Patterns);
		foreach($keys as $key => $index){
			if(!is_numeric($index)){
				unset($keys[$key]);
			}
		}

		return $keys;
	}
	
	public function deleteBottomPattern(){
		$keys = $this->getPatternIds();
		if(empty($keys)){
			return true;
		}

		$namedTag = $this->getNamedTag();
		$index = min($keys);
		unset($namedTag->Patterns->{$index});
		$this->setNamedTag($namedTag);
		return true;
	}
	
	public function getPatternCount(){
		return count($this->getPatternIds());
	}

	public function correctNBT(){
		$tag = $this->getNamedTag() ?? new Compound();
		if(!isset($tag->Base) or !($tag->Base instanceof IntTag)){
			$tag->Base = new IntTag("Base", $this->meta);
		}

		if(!isset($tag->Patterns) or !($tag->Patterns instanceof Enum)){
			$tag->Patterns = new Enum("Patterns");
		}
		
		$this->setNamedTag($tag);
	}
}
