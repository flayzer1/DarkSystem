<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity\morph;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\Player;

class MorphCommand extends VanillaCommand{
	
    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.morph.description",
            "%commands.morph.usage"
        );
        $this->setPermission("pocketmine.command.morph");
        
        $this->morphManager = new MorphManager(Server::getInstance());
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if($sender instanceof Player){
            $mobs = [
                "Creeper"
            ];
            if(isset($args[0]) && $args[0] == "add"){
                if(isset($args[1])){
                    if(in_array($args[1], $mobs)){
                        $this->morphManager->removeMob($sender);
                        $this->morphManager->spawn($sender, "Morph" . $args[1]);
                    }else{
                        $sender->sendMessage(TextFormat::GREEN . "Available mobs: " . TextFormat::GOLD . implode(", ", $mobs));
                    }
                }
            }
            if(isset($args[0]) && $args[0] == "del"){
                $this->morphManager->removeMob($sender);
            }    
            if(isset($args[0]) && $args[0] == "list") {
                $sender->sendMessage(TextFormat::GREEN . "Available mobs: " . TextFormat::GOLD . implode(", ", $mobs));
            }
            if(!isset($args[0])){
                $sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
            }
            return true;
        }else{
            $sender->sendMessage(TextFormat::RED . "Please use command in-game.");
            return false;
        }
    }
}