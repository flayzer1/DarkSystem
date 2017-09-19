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
use pocketmine\entity\Entity;
use pocketmine\entity\Human;

class GoldenApple extends Food
{
    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::GOLDEN_APPLE, $meta, $count, "Golden Apple");
    }

    public function canBeConsumedBy(Entity $entity)
    {
        return $entity instanceof Human and $this->canBeConsumed();
    }

    public function getFoodRestore()
    {
        return 4;
    }

    public function getSaturationRestore()
    {
        return 9.6;
    }

    public function getAdditionalEffects()
    {
        return [
            Effect::getEffect(Effect::REGENERATION)->setDuration(100)->setAmplifier(1),
            Effect::getEffect(Effect::ABSORPTION)->setDuration(2400)->setAmplifier(0)
        ];
    }
}

