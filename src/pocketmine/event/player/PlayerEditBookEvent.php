<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\item\WritableBook;
use pocketmine\Player;

class PlayerEditBookEvent extends PlayerEvent implements Cancellable{
	
	public static $handlerList = null;

	const ACTION_REPLACE_PAGE = 0;
	const ACTION_ADD_PAGE = 1;
	const ACTION_DELETE_PAGE = 2;
	const ACTION_SWAP_PAGES = 3;
	const ACTION_SIGN_BOOK = 4;

	/** @var WritableBook */
	private $oldBook;
	/** @var int */
	private $action;
	/** @var WritableBook */
	private $newBook;
	/** @var int[] */
	private $modifiedPages;

	public function __construct(Player $player, WritableBook $oldBook, WritableBook $newBook, $action, $modifiedPages){
		$this->player = $player;
		$this->oldBook = $oldBook;
		$this->newBook = $newBook;
		$this->action = $action;
		$this->modifiedPages = $modifiedPages;
	}

	/**
	 * Returns the action of the event.
	 *
	 * @return int
	 */
	public function getAction(){
		return $this->action;
	}

	/**
	 * Returns the book before it was modified.
	 *
	 * @return WritableBook
	 */
	public function getOldBook(){
		return $this->oldBook;
	}

	/**
	 * Returns the book after it was modified.
	 * The new book may be a written book, if the book was signed.
	 *
	 * @return WritableBook
	 */
	public function getNewBook(){
		return $this->newBook;
	}

	/**
	 * Sets the new book as the given instance.
	 *
	 * @param WritableBook $book
	 */
	public function setNewBook(WritableBook $book){
		$this->newBook = $book;
	}

	/**
	 * Returns an array containing the page IDs of modified pages.
	 *
	 * @return int[]
	 */
	public function getModifiedPages(){
		return $this->modifiedPages;
	}
	
}
