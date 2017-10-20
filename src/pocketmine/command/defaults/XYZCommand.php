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
use pocketmine\Server;
use pocketmine\Player;

class XYZCommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.xyz.description",
            "%commands.xyz.usage"
        );
        $this->setPermission("pocketmine.command.xyz");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        if(Translate::checkTurkish() === "yes"){
			$sender->sendMessage("Sizin Konumunuz: ({$sender->getX()}, {$sender->getY()}, {$sender->getZ()}, {$sender->getLevel()->getFolderName()})");
        }else{
        	$sender->sendMessage("Your Position: ({$sender->getX()}, {$sender->getY()}, {$sender->getZ()}, {$sender->getLevel()->getFolderName()})");
        }
        
        return true;
    }
}