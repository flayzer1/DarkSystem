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

use pocketmine\inventory\customUI\elements\Button;
use pocketmine\inventory\customUI\elements\Label;
use pocketmine\inventory\customUI\windows\CustomForm;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use pocketmine\Translate;
use pocketmine\Player;
use pocketmine\Server;

class ServerInfoCommand extends VanillaCommand{

    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.serverinfo.description",
            "%commands.serverinfo.usage"
        );
        $this->setPermission("pocketmine.command.serverinfo");
    }

    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return true;
        }
        
        $this->server = Server::getInstance();
        
        if(count($args) > 0){
        	$sender->sendMessage(new TranslationContainer("commands.generic.usage", [$this->usageMessage]));
            return false;
        }
       
        if(Translate::checkTurkish() === "yes"){
            $ui = new CustomForm(TextFormat::AQUA . "DarkSystem");
		    $button = new Button("DarkSystem");
		    $button->setImage(Button::IMAGE_TYPE_URL, "");
		    //$ui->addButton($button);
		    $ui->addElement(new Label(TextFormat::GREEN . "DarkSystem, PocketMine-MP'nin Yeniden Yazılması Adına Yapılmış Bir Projedir."));
		    $ui->addElement(new Label(TextFormat::GREEN . "DarkSystem'i Buradan İndirebilirsiniz: " . TextFormat::GOLD . "https://github.com/DarkYusuf13/DarkSystem"));
        }else{
			$ui = new CustomForm(TextFormat::AQUA . "DarkSystem");
		    $button = new Button("DarkSystem");
		    $button->setImage(Button::IMAGE_TYPE_URL, "");
		    //$ui->addButton($button);
		    $ui->addElement(new Label(TextFormat::GREEN . "DarkSystem is rewritation project of PocketMine-MP"));
		    $ui->addElement(new Label(TextFormat::GREEN . "You can download from " . TextFormat::GOLD . "https://github.com/DarkYusuf13/DarkSystem"));
        }
        
		$sender->showModal($ui);
		
        return true;
    }
}