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
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

class TellCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.tell.description",
			"%commands.message.usage",
			["msg"]
		);
		$this->setPermission("pocketmine.command.tell");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) < 2){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}

		$name = strtolower(array_shift($args));

		$player = $sender->getServer()->getPlayer($name);

		if($player === $sender){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.message.sameTarget"));
			return true;
		}

		if($player instanceof Player){
			$sender->sendMessage("§3[§9".$sender->getName()." §b> §9" . $player->getDisplayName() . "§3]§7 " . implode(" ", $args));
			$player->sendMessage("§3[§9" . ($sender instanceof Player ? $sender->getDisplayName() : $sender->getName()) . " §b> §9".$player->getName()."§3]§7 " . implode(" ", $args));
		}else{
			$sender->sendMessage(new TranslationContainer("commands.generic.player.notFound"));
		}

		return true;
	}
}
