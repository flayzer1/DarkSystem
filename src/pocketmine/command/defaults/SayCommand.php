<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use pocketmine\Translate;
use pocketmine\Player;

class SayCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.say.description",
			"%commands.say.usage"
		);
		$this->setPermission("pocketmine.command.say");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
		
		if(Translate::checkTurkish() === "yes"){
			$sender->getServer()->broadcastMessage(new TranslationContainer(TextFormat::YELLOW . "%chat.type.announcement", [$sender instanceof Player ? $sender->getDisplayName() : ($sender instanceof ConsoleCommandSender ? "Sunucu" : $sender->getName()), TextFormat::YELLOW . implode(" ", $args)]));
		}else{
			$sender->getServer()->broadcastMessage(new TranslationContainer(TextFormat::YELLOW . "%chat.type.announcement", [$sender instanceof Player ? $sender->getDisplayName() : ($sender instanceof ConsoleCommandSender ? "Server" : $sender->getName()), TextFormat::YELLOW . implode(" ", $args)]));
		}
		
		return true;
	}
}