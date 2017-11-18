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

class Beetroot extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::BEETROOT, $meta, $count, "Beetroot");
    }

    public function getFoodRestore()
    {
        return 1;
    }

    public function getSaturationRestore()
    {
        return 1.2;
    }
}
