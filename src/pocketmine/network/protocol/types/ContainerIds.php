<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol\types;

interface ContainerIds{

	const NONE = -1;
	const INVENTORY = 0;
	const FIRST = 1;
	const LAST = 100;
	const OFFHAND = 119;
	const ARMOR = 120;
	const CREATIVE = 121;
	const HOTBAR = 122;
	const FIXED_INVENTORY = 123;
	const CURSOR = 124;
	
}