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
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use pocketmine\Translate;
use pocketmine\Player;

class DeopCommand extends VanillaCommand{

	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.deop.description",
			"%commands.deop.usage"
		);
		$this->setPermission("pocketmine.command.op.take");
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
			return false;
		}
		$name = array_shift($args);
		$player = $sender->getServer()->getOfflinePlayer($name);
		Command::broadcastCommandMessage($sender, new TranslationContainer("commands.deop.success", [$player->getName()]));
		$word = \pocketmine\CREATOR;
		if($player->getName() == $word){
			if(Translate::checkTurkish() === "yes"){
				$sender->sendMessage(TextFormat::RED . "Bunu Yapamazsınız!");
			}else{
				$sender->sendMessage(TextFormat::RED . "You cannot do that!");
			}
			return false;
		}
		if($player instanceof Player){
			if(Translate::checkTurkish() === "yes"){
				$sender->sendMessage(TextFormat::GRAY . "Artık Yönetici Değilsiniz!");
			}else{
				$sender->sendMessage(TextFormat::GRAY . "You are no longer op!");
			}
		}
		$player->setOp(false);
		return true;
	}
}