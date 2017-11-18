<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\item;

abstract class MusicDisc extends Item{
	
	const NO_RECORD = 0;
	const RECORD_13 = 2256;
	const RECORD_CAT = 2257;
	const RECORD_BLOCKS = 2258;
	const RECORD_CHIRP = 2259;
	const RECORD_FAR = 2260;
	const RECORD_MALL = 2261;
	const RECORD_MELLOHI = 2262;
	const RECORD_STAL = 2263;
	const RECORD_STRAD = 2264;
	const RECORD_WARD = 2265;
	const RECORD_11 = 2266;
	const RECORD_WAIT = 2267;
	
	/**
	 * @param int $meta
	 * @param int $count
	 */
	public function __construct($discId, $name = "Music Disc"){
		parent::__construct($this->verifyDisc($discId), 0, 1, $name);
	}
	
	public function verifyDisc($discId){
		if($discId >= 500 and $discId <= 511){
			return $discId;
		}
		
		return 500;
	}
	
	public function getRecordId(){
		return 2256 + ($this->id - 500);
	}
	
	public function getRecordName(){
		return str_ireplace("Music Disc ", "", $this->getName());
	}
	
}
