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

use pocketmine\event\Listener;
use pocketmine\event\TranslationContainer;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\Translate;
use pocketmine\math\Vector3;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TpAllCommand extends VanillaCommand{
	
	public function __construct($name){
		parent::__construct(
			$name,
			"%pocketmine.command.tpall.description",
			"%commands.tpall.usage"
		);
		$this->setPermission("pocketmine.command.tpall");
	}
	
	public function execute(CommandSender $sender, $currentAlias, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		
		if(!$sender instanceof Player){
			if(Translate::checkTurkish() === "yes"){
        	    $sender->sendMessage(TextFormat::RED . "Bu Komutu Sadece Oyuncular Kullanabilir!");
        	}else{
        	    $sender->sendMessage(TextFormat::RED . "Only Players Can Use This Command!");
        	}
        
			return false;
		}
		
		if(count($args) >= 1){
			if(Translate::checkTurkish() === "yes"){
        	    $sender->sendMessage(TextFormat::RED . "Yanlış Kullanış!");
        	}else{
        	    $sender->sendMessage(TextFormat::RED . "Wrong Usage!");
        	}
        
			return false;
		}
		
		$players = count($sender->getServer()->getOnlinePlayers());
		
        if($players <= 1){
        	if(Translate::checkTurkish() === "yes"){
        	    $sender->sendMessage(TextFormat::RED . "Hiçbir Oyuncu Aktif Değil!");
        	}else{
        	    $sender->sendMessage(TextFormat::RED . "No Players is Online!");
        	}
        
        	return false;
        }else{
        	foreach($sender->getServer()->getOnlinePlayers() as $p){
        	    $p->teleport($sender);
			}
		}
		
		if(Translate::checkTurkish() === "yes"){
        	$sender->sendMessage(TextFormat::GREEN . "Başarılı!");
        }else{
        	$sender->sendMessage(TextFormat::GREEN . "Success!");
        }
		
		return true;
	}
}