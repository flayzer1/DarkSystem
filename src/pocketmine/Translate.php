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
	
	const ENG = "eng";
	const TUR = "tur";
	
	public static $result;
	
	public function __construct(Server $server){
		$this->server = $server;
	}
	
	public function prepareLang(){
		
	}
	
	public function prepareLan(){
		$isTurkish = "no";
		
    	if(!file_exists(\pocketmine\DATA . "sunucu.properties") && !file_exists(\pocketmine\DATA . "yoneticiler.json") && !file_exists(\pocketmine\DATA . "beyaz-liste.json")){
    	    $isTurkish = "no";
    	}else{
            $isTurkish = "yes";
    	}
    
    	Translate::$result = $isTurkish;
    
    	return true;
    }
    
    public static function checkTurkish(){
    	$isTurkish = "no";
    	if(!file_exists(\pocketmine\DATA . "sunucu.properties") && !file_exists(\pocketmine\DATA . "yoneticiler.json") && !file_exists(\pocketmine\DATA . "beyaz-liste.json")){
    	    $isTurkish = "no";
    	}else{
            $isTurkish = "yes";
    	}
    
    	return $isTurkish;
    }
    
    public static function checkTurkis(){
    	return Translate::$result;
    }
    
}
