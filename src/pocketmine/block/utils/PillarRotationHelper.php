<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\block\utils;

use pocketmine\math\Vector3;

class PillarRotationHelper{

	public static function getMetaFromFace($meta, $face){
		$faces = [
			Vector3::SIDE_DOWN => 0,
			Vector3::SIDE_NORTH => 0x08,
			Vector3::SIDE_WEST => 0x04,
		];

		return ($meta & 0x03) | $faces[$face & ~0x01];
	}
	
}
