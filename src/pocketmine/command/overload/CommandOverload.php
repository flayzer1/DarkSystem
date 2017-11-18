<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command\overload;

class CommandOverload{
	
	protected $name;
	protected $params = [];
	
	public function __construct($name, $params = []){
		$this->params = $params;
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getParameters(){
		return $this->params;
	}
	
	public function setParameter($index, CommandParameter $param){
		$this->params[$index] = $param;
	}
}
