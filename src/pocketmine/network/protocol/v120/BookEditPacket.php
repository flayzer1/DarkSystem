<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol\v120;

use pocketmine\network\protocol\Info120;
use pocketmine\network\protocol\PEPacket;

class BookEditPacket extends PEPacket{
	
	const NETWORK_ID = Info120::BOOK_EDIT_PACKET;
	const PACKET_NAME = "BOOK_EDIT_PACKET";
	
	const TYPE_REPLACE_PAGE = 0;
	const TYPE_ADD_PAGE = 1;
	const TYPE_DELETE_PAGE = 2;
	const TYPE_SWAP_PAGES = 3;
	const TYPE_SIGN_BOOK = 4;

	/** @var int */
	public $type;
	/** @var int */
	public $inventorySlot;
	/** @var int */
	public $pageNumber;
	/** @var int */
	public $secondaryPageNumber;

	/** @var string */
	public $text;
	/** @var string */
	public $photoName;

	/** @var string */
	public $title;
	/** @var string */
	public $author;

	public function decode($playerProtocol){
		$this->type = $this->getByte();
		$this->inventorySlot = $this->getByte();

		switch($this->type){
			case self::TYPE_REPLACE_PAGE:
			case self::TYPE_ADD_PAGE:
				$this->pageNumber = $this->getByte();
				$this->text = $this->getString();
				$this->photoName = $this->getString();
				break;
			case self::TYPE_DELETE_PAGE:
				$this->pageNumber = $this->getByte();
				break;
			case self::TYPE_SWAP_PAGES:
				$this->pageNumber = $this->getByte();
				$this->secondaryPageNumber = $this->getByte();
				break;
			case self::TYPE_SIGN_BOOK:
				$this->title = $this->getString();
				$this->author = $this->getString();
				break;
			default:
				throw new \UnexpectedValueException("Unknown book edit type $this->type!");
		}
	}

	public function encode($playerProtocol){
		$this->putByte($this->type);
		$this->putByte($this->inventorySlot);

		switch($this->type){
			case self::TYPE_REPLACE_PAGE:
			case self::TYPE_ADD_PAGE:
				$this->putByte($this->pageNumber);
				$this->putString($this->text);
				$this->putString($this->photoName);
				break;
			case self::TYPE_DELETE_PAGE:
				$this->putByte($this->pageNumber);
				break;
			case self::TYPE_SWAP_PAGES:
				$this->putByte($this->pageNumber);
				$this->putByte($this->secondaryPageNumber);
				break;
			case self::TYPE_SIGN_BOOK:
				$this->putString($this->title);
				$this->putString($this->author);
				break;
			default:
				throw new \UnexpectedValueException("Unknown book edit type $this->type!");
		}
	}
}
