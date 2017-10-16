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

use pocketmine\nbt\tag\Compound;
use pocketmine\nbt\tag\Enum;
use pocketmine\nbt\tag\StringTag;

class WritableBook extends Item{

	public function __construct($meta = 0){
		parent::__construct(self::WRITABLE_BOOK, $meta, "Book & Quill");
	}

	/**
	 * Returns whether the given page exists in this book.
	 *
	 * @param int $pageId
	 *
	 * @return bool
	 */
	public function pageExists($pageId){
		return isset($this->getNamedTag()->pages->{$pageId});
	}

	/**
	 * Returns a string containing the content of a page (which could be empty), or null if the page doesn't exist.
	 *
	 * @param int $pageId
	 *
	 * @return string|null
	 */
	public function getPageText($pageId){
		if(!$this->pageExists($pageId)){
			return null;
		}
		
		return $this->getNamedTag()->pages->{$pageId}->text->getValue();
	}

	/**
	 * Sets the text of a page in the book. Adds the page if the page does not yet exist.
	 *
	 * @param int    $pageId
	 * @param string $pageText
	 *
	 * @return bool indicating whether the page was created or not.
	 */
	public function setPageText($pageId, $pageText){
		$created = false;
		if(!$this->pageExists($pageId)){
			$this->addPage($pageId);
			$created = true;
		}

		$namedTag = $this->getNamedTag();
		$namedTag->pages->{$pageId}->text->setValue($pageText);
		$this->setNamedTag($namedTag);

		return $created;
	}

	/**
	 * Adds a new page with the given page ID.
	 * Creates a new page for every page between the given ID and existing pages that doesn't yet exist.
	 *
	 * @param int $pageId
	 */
	public function addPage($pageId){
		if($pageId < 0){
			throw new \InvalidArgumentException("Page number \"$pageId\" is out of range");
		}
		$namedTag = $this->getCorrectedNamedTag();

		if(!isset($namedTag->pages) or !($namedTag->pages instanceof Enum)){
			$namedTag->pages = new Enum("pages", []);
		}

		for($id = 0; $id <= $pageId; $id++){
			if(!$this->pageExists($id)){
				$namedTag->pages->{$id} = new Compound("", [
					new StringTag("text", ""),
					new StringTag("photoname", "")
				]);
			}
		}

		$this->setNamedTag($namedTag);
	}

	/**
	 * Deletes an existing page with the given page ID.
	 *
	 * @param int $pageId
	 *
	 * @return bool indicating success
	 */
	public function deletePage($pageId){
		if(!$this->pageExists($pageId)){
			return false;
		}

		$namedTag = $this->getNamedTag();
		unset($namedTag->pages->{$pageId});
		$this->pushPages($pageId, $namedTag);
		$this->setNamedTag($namedTag);

		return true;
	}

	/**
	 * Inserts a new page with the given text and moves other pages upwards.
	 *
	 * @param int $pageId
	 * @param string $pageText
	 *
	 * @return bool indicating success
	 */
	public function insertPage($pageId, $pageText = ""){
		$namedTag = $this->getCorrectedNamedTag();
		if(!isset($namedTag->pages) or !($namedTag->pages instanceof Enum)){
			$namedTag->pages = new Enum("pages", []);
		}
		
		$this->pushPages($pageId, $namedTag, false);

		$namedTag->pages->{$pageId}->text->setValue($pageText);
		$this->setNamedTag($namedTag);
		
		return true;
	}

	/**
	 * Switches the text of two pages with each other.
	 *
	 * @param int $pageId1
	 * @param int $pageId2
	 *
	 * @return bool indicating success
	 */
	public function swapPages($pageId1, $pageId2){
		if(!$this->pageExists($pageId1) or !$this->pageExists($pageId2)){
			return false;
		}

		$pageContents1 = $this->getPageText($pageId1);
		$pageContents2 = $this->getPageText($pageId2);
		$this->setPageText($pageId1, $pageContents2);
		$this->setPageText($pageId2, $pageContents1);
		
		return true;
	}

	/**
	 * @return Compound
	 */
	protected function getCorrectedNamedTag(){
		return $this->getNamedTag() ?? new Compound();
	}

	public function getMaxStackSize(){
		return 1;
	}

	/**
	 * @param int         $pageId
	 * @param Compound $namedTag
	 * @param bool        $downwards
	 *
	 * @return bool
	 */
	private function pushPages($pageId, Compound $namedTag, $downwards = true){
		if(empty($this->getPages())){
			return false;
		}

		$pages = $this->getPages();
		$type = $downwards ? -1 : 1;
		foreach($pages as $key => $page){
			if(($key <= $pageId and $downwards) or ($key < $pageId and !$downwards)){
				continue;
			}

			if($downwards){
				unset($namedTag->pages->{$key});
			}
			$namedTag->pages->{$key + $type} = clone $page;
		}
		return true;
	}

	/**
	 * Returns an array containing all pages of this book.
	 *
	 * @return Compound[]
	 */
	public function getPages(){
		$namedTag = $this->getCorrectedNamedTag();
		if(!isset($namedTag->pages)){
			return [];
		}

		return array_filter((array) $namedTag->pages, function($key){
			return is_numeric($key);
		}, ARRAY_FILTER_USE_KEY);
	}
}
