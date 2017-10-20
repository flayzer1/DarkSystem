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
use pocketmine\utils\TextFormat;
use pocketmine\Translate;
use pocketmine\Player;

class WorldCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"Teleport to a world",
			"/world [target player] <world name>"
		);
		$this->setPermission("pocketmine.command.world");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if($sender instanceof Player){
			if(count($args) == 1){
				$sender->getServer()->loadLevel($args[0]);
				if(($level = $sender->getServer()->getLevelByName($args[0])) !== null){
					$sender->teleport($level->getSafeSpawn());
					$sender->sendMessage("Teleported to Level: " . $level->getName());
					return true;
				}else{
					$sender->sendMessage(TextFormat::RED . "World: \"" . $args[0] . "\" does not exist");
					return false;
				}
			}elseif(count($args) > 1 && count($args) < 3){
				$sender->getServer()->loadLevel($args[1]);
				if(($level = $sender->getServer()->getLevelByName($args[1])) !== null){
					$player = $sender->getServer()->getPlayer($args[0]);
					$player->teleport($level->getSafeSpawn());
					$player->sendMessage("Teleported to Level: " . $level->getName());
					return true;
				}else{
					$sender->sendMessage(TextFormat::RED . "World: \"" . $args[1] . "\" does not exist");
					return false;
				}
			}else{
				$sender->sendMessage("Usage: /world [target player] <world name>");
				return false;
			}
		}else{
			if(Translate::checkTurkish() === "yes"){
        	    $sender->sendMessage(TextFormat::RED . "Bu Komutu Sadece Oyuncular Kullanabilir!");
        	}else{
        	    $sender->sendMessage(TextFormat::RED . "Only Players Can Use This Command!");
        	}
			return false;
		}
	}
}