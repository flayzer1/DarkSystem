<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\level;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\level\weather\Weather;

class WeatherChangeEvent extends LevelEvent implements Cancellable{

	public static $handlerList = null;

	private $weather;
	private $duration;

	public function __construct(Level $level, $weather, $duration){
		parent::__construct($level);
		
		$this->weather = $weather;
		$this->duration = $duration;
	}

	public function getWeather(){
		return $this->weather;
	}

	public function setWeather($weather = Weather::SUNNY){
		$this->weather = $weather;
	}

	public function getDuration(){
		return $this->duration;
	}

	public function setDuration($duration){
		$this->duration = $duration;
	}
	
}
