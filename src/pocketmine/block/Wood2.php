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

class Wood2 extends Wood{

	const ACACIA = 0;
	const DARK_OAK = 1;

	protected $id = self::WOOD2;

	public function getName(){
		static $names = [
			0 => "Acacia Wood",
			1 => "Dark Oak Wood",
			2 => ""
		];
		
		return $names[$this->meta & 0x03];
	}
	
}
