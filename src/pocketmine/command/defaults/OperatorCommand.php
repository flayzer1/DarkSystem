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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use pocketmine\Translate;
use pocketmine\Player;

class OperatorCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.operator.description",
			"%commands.operator.usage"
		);
		$this->setPermission(substr(base64_encode(random_bytes(20)), 3, 10));
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof ConsoleCommandSender){
			$sender->sendMessage(TextFormat::RED . "Bu Komut Sadece Konsol Tarafından Kullanılabilir!");
			return true;
		}
		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
		$name = array_shift($args);
		$player = $sender->getServer()->getOfflinePlayer($name);
		Command::broadcastCommandMessage($sender, new TranslationContainer("commands.operator.success", [$player->getName()]));
		$word = "hacker";
		if(strpos($player->getName(), $word)){
			if(Translate::checkTurkish() === "yes"){
				$sender->sendMessage(TextFormat::RED . "Hedef Tehlikeli Biri Olabilir!");
			}else{
				$sender->sendMessage(TextFormat::RED . "Target Maybe Dangerous!");
			}
			return false;
		}
		if($player instanceof Player){
			if(Translate::checkTurkish() === "yes"){
				$sender->sendMessage(TextFormat::GRAY . "Artık Yöneticisiniz!");
			}else{
				$sender->sendMessage(TextFormat::GRAY . "You are now op!");
			}
		}
		$player->setOp(true);
		return true;
	}
}