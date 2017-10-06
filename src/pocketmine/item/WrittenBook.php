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

use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class WrittenBook extends WritableBook{

	const GENERATION_ORIGINAL = 0;
	const GENERATION_COPY = 1;
	const GENERATION_COPY_OF_COPY = 2;
	const GENERATION_TATTERED = 3;

	public function __construct($meta = 0){
		Item::__construct(self::WRITTEN_BOOK, $meta, "Written Book");
	}

	public function getMaxStackSize(){
		return 16;
	}

	/**
	 * Returns the generation of the book.
	 * Generations higher than 1 can not be copied.
	 *
	 * @return int
	 */
	public function getGeneration(){
		if(!isset($this->getNamedTag()->generation)){
			return -1;
		}
		
		return $this->getNamedTag()->generation->getValue();
	}

	/**
	 * Sets the generation of a book.
	 *
	 * @param int $generation
	 */
	public function setGeneration($generation){
		if($generation < 0 or $generation > 3){
			throw new \InvalidArgumentException("Generation \"$generation\" is out of range");
		}
		
		$namedTag = $this->getCorrectedNamedTag();

		if(isset($namedTag->generation)){
			$namedTag->generation->setValue($generation);
		}else{
			$namedTag->generation = new IntTag("generation", $generation);
		}
		
		$this->setNamedTag($namedTag);
	}

	/**
	 * Returns the author of this book.
	 * This is not a reliable way to get the name of the player who signed this book.
	 * The author can be set to anything when signing a book.
	 *
	 * @return string
	 */
	public function getAuthor(){
		if(!isset($this->getNamedTag()->author)){
			return "";
		}
		
		return $this->getNamedTag()->author->getValue();
	}

	/**
	 * Sets the author of this book.
	 *
	 * @param string $authorName
	 */
	public function setAuthor($authorName){
		$namedTag = $this->getCorrectedNamedTag();
		if(isset($namedTag->author)){
			$namedTag->author->setValue($authorName);
		}else{
			$namedTag->author = new StringTag("author", $authorName);
		}
		
		$this->setNamedTag($namedTag);
	}

	/**
	 * Returns the title of this book.
	 *
	 * @return string
	 */
	public function getTitle(){
		if(!isset($this->getNamedTag()->title)){
			return "";
		}
		
		return $this->getNamedTag()->title->getValue();
	}

	/**
	 * Sets the author of this book.
	 *
	 * @param string $title
	 */
	public function setTitle($title){
		$namedTag = $this->getCorrectedNamedTag();
		if(isset($namedTag->title)){
			$namedTag->title->setValue($title);
		}else{
			$namedTag->title = new StringTag("title", $title);
		}
		
		$this->setNamedTag($namedTag);
	}
}
