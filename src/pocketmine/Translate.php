<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine;

use pocketmine\language\Language;

class Translate{
	
	const CURRENT_LANG = "eng"; //
	
	const ENG = "eng";
	const TUR = "tur";
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
    public static function checkTurkish(){
    	$server = Server::getInstance();
    
    	$isTurkish = "no";
    	if(Translate::CURRENT_LANG == Translate::TUR){
    	    $isTurkish = "yes";
    	}
    
    	return $isTurkish;
    }
    
}
