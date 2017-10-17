<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event\server;

use pocketmine\utils\Utils;

class LowMemoryEvent extends ServerEvent{
	
	public static $handlerList = null;

	private $memory;
	private $memoryLimit;
	private $triggerCount;
	private $global;

	public function __construct($memory, $memoryLimit, $isGlobal = false, $triggerCount = 0){
		$this->memory = $memory;
		$this->memoryLimit = $memoryLimit;
		$this->global = (bool) $isGlobal;
		$this->triggerCount = (int) $triggerCount;
	}

	/**
	 * Returns the memory usage at the time of the event call (in bytes)
	 *
	 * @return int
	 */
	public function getMemory(){
		return $this->memory;
	}

	/**
	 * Returns the memory limit defined (in bytes)
	 *
	 * @return int
	 */
	public function getMemoryLimit(){
		return $this->memory;
	}

	/**
	 * Returns the times this event has been called in the current low-memory state
	 *
	 * @return int
	 */
	public function getTriggerCount(){
		return $this->triggerCount;
	}

	/**
	 * @return bool
	 */
	public function isGlobal(){
		return $this->global;
	}

	/**
	 * Amount of memory already freed
	 *
	 * @return int
	 */
	public function getMemoryFreed(){
		return $this->getMemory() - ($this->isGlobal() ? Utils::getMemoryUsage(true)[1] : Utils::getMemoryUsage(true)[0]);
	}
	
}
