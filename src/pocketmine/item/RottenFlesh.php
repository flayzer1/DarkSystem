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

class RottenFlesh extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::ROTTEN_FLESH, 0, $count, "Rotten Flesh");
    }

    public function getFoodRestore()
    {
        return 4;
    }

    public function getSaturationRestore()
    {
        return 0.8;
    }

    public function getAdditionalEffects()
    {
        $chance = mt_rand(0, 100);
        if($chance >= 20){
            return [Effect::getEffect(Effect::HUNGER)->setDuration(30 * 20)];
        }else{
            return [];
        }
    }
}
