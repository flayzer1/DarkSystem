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

class RedstoneTorchActive extends RedstoneTorch{
	
	protected $id = self::REDSTONE_TORCH_ACTIVE;
	
	public function __construct($meta = 0){
		$this->meta = $meta;
	}
	
	public function getName(){
		return "Glowing Redstone Torch";
	}
	
}