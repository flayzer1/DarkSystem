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

class Random{

	protected $seed;

	/**
	 * @param int $seed
	 */
	public function __construct($seed = -1){
		if($seed == -1){
			$seed = time();
		}

		$this->setSeed($seed);
	}

	/**
	 * @param int $seed
	 */
	public function setSeed($seed){
		$this->seed = crc32(pack("N", $seed));
	}

	/**
	 * @return int
	 */
	public function nextInt(){
		return $this->nextSignedInt() & 0x7fffffff;
	}

	/**
	 * @return int
	 */
	public function nextSignedInt(){
		$t = crc32(pack("N", $this->seed));
		$this->seed ^= $t;

		if(PHP_INT_SIZE === 8){
			return $t << 32 >> 32;
		}else{
			return $t;
		}
	}

	/**
	 * @return float
	 */
	public function nextFloat(){
		return $this->nextInt() / 0x7fffffff;
	}

	/**
	 * @return float
	 */
	public function nextSignedFloat(){
		return $this->nextSignedInt() / 0x7fffffff;
	}

	/**
	 * @return bool
	 */
	public function nextBoolean(){
		return ($this->nextSignedInt() & 0x01) === 0;
	}

	/**
	 * @param int $start default 0
	 * @param int $end   default 0x7fffffff
	 *
	 * @return int
	 */
	public function nextRange($start = 0, $end = 0x7fffffff){
		return $start + ($this->nextInt() % ($end + 1 - $start));
	}

	public function nextBoundedInt($bound){
		return $this->nextInt() % $bound;
	}

}
