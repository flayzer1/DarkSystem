<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

class Cache{
	
	public static $cached = [];

	/**
	 * @param string    $identifier
	 * @param mixed     $blob
	 * @param float|int $minTTL
	 */
	public static function add($identifier, $blob, $minTTL = 30){
		self::$cached[$identifier] = [$blob, microtime(true) + $minTTL, $minTTL];
	}

	/**
	 * @param $identifier
	 *
	 * @return bool|mixed
	 */
	public static function get($identifier){
		if(isset(self::$cached[$identifier])){
			self::$cached[$identifier][1] = microtime(true) + self::$cached[$identifier][2];
			return self::$cached[$identifier][0];
		}

		return false;
	}

	/**
	 * @param $identifier
	 *
	 * @return bool
	 */
	public static function exists($identifier){
		return isset(self::$cached[$identifier]);
	}

	/**
	 * @param $identifier
	 */
	public static function remove($identifier){
		unset(self::$cached[$identifier]);
	}
	
	public static function cleanup(){
		$time = microtime(true);
		foreach(self::$cached as $index => $data){
			if($data[1] < $time){
				unset(self::$cached[$index]);
			}
		}
	}
}
