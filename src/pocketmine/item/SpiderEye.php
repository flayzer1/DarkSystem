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

use pocketmine\entity\Effect;

class SpiderEye extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::SPIDER_EYE, $meta, $count, "Spider Eye");
    }

    public function getFoodRestore()
    {
        return 2;
    }

    public function getSaturationRestore()
    {
        return 3.2;
    }

    public function getAdditionalEffects()
    {
        return [Effect::getEffect(Effect::POISON)->setDuration(80)];
    }
}
