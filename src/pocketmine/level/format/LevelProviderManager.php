<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format;

use pocketmine\Server;
use pocketmine\utils\LevelException;

abstract class LevelProviderManager{
	
	protected static $providers = [];

	/**
	 * @param Server $server
	 * @param string $class
	 *
	 * @throws LevelException
	 */
	public static function addProvider(Server $server, $class){
		if(!is_subclass_of($class, LevelProvider::class)){
			throw new LevelException("Class is not a subclass of LevelProvider");
		}
		
		LevelProviderManager::$providers[strtolower($class::getProviderName())] = $class;
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function getProvider($path){
		foreach(LevelProviderManager::$providers as $provider){
			if($provider::isValid($path)){
				return $provider;
			}
		}

		return null;
	}

	public static function getProviderByName($name){
		$name = trim(strtolower($name));
		
		return isset(LevelProviderManager::$providers[$name]) ? LevelProviderManager::$providers[$name] : null;
	}
}