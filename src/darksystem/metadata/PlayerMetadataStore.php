<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem\metadata;

use darksystem\IPlayer;

class PlayerMetadataStore extends MetadataStore{

	public function disambiguate(Metadatable $player, $metadataKey){
		if(!($player instanceof IPlayer)){
			throw new \InvalidArgumentException("Argument must be an IPlayer instance");
		}

		return strtolower($player->getName()) . ":" . $metadataKey;
	}
}
