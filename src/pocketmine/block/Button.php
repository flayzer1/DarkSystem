<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block;

abstract class Button extends Transparent{

	public function canBeFlowedInto(){
		return true;
	}

	public function getHardness(){
		return 1;
	}
	
	public function canBeActivated(){
		return true;
	}

	public function getResistance(){
		return 0;
	}

	public function isSolid(){
		return false;
	}

	public function getBoundingBox(){
		return null;
	}
}
