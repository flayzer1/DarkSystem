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

class CommandEnum{
	
	protected $name;
	protected $values = [];
	
	public function __construct($name, $values = []){
		$this->name = $name;
		$this->values = $values;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getValues(){
		return $this->values;
	}
}
