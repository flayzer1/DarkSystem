<?php

namespace pocketmine\inventory\customUI;

use pocketmine\Player;

interface CustomUI{

	public function handle($response, Player $player);

	public function jsonSerialize();
	
	public function close(Player $player);

	public function getTitle();

	public function getContent();
	
	//public function setID($id);

	//public function getID();
	
}