<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\event;

abstract class Event{
	
	protected $eventName = null;
	
	private $isCancelled = false;
	
	final public function getEventName(){
		return $this->eventName === null ? get_class($this) : $this->eventName;
	}
	
	public function isCancelled(){
		if(!($this instanceof Cancellable)){
			throw new \BadMethodCallException("Event is not Cancellable");
		}
		
		return $this->isCancelled === true;
	}
	
	public function setCancelled($value = true){
		if(!($this instanceof Cancellable)){
			throw new \BadMethodCallException("Event is not Cancellable");
		}
		
		$this->isCancelled = (bool) $value;
	}
	
	public function getHandlers(){
		if(static::$handlerList === null){
			static::$handlerList = new HandlerList();
		}

		return static::$handlerList;
	}

}
