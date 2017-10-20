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

class ZoomCommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.zoom.description",
            "%commands.zoom.usage"
        );
        $this->setPermission("pocketmine.command.zoom");
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
		
        $sender->updateSpeed(0.1);
        
        if(Translate::checkTurkish() === "yes"){
        	$sender->sendMessage(TextFormat::GREEN . "Başarılı!");
        }else{
        	$sender->sendMessage(TextFormat::GREEN . "Success!");
        }
        
        return true;
    }
}