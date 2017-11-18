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

use pocketmine\item\Item;

class DaylightDetector extends Solid
{
    protected $id = self::DAYLIGHT_SENSOR;

    public function __construct($meta = 0)
    {
        $this->meta = $meta;
    }

    public function getName()
    {
        return "Daylight Sensor";
    }

    public function getBoundingBox()
    {
        if($this->boundingBox === null){
            $this->boundingBox = $this->recalculateBoundingBox();
        }
        
        return $this->boundingBox;
    }

    public function canBeFlowedInto()
    {
        return false;
    }

    public function canBeActivated()
    {
        return true;
    }

    public function getHardness()
    {
        return 0.2;
    }

    public function getResistance()
    {
        return 1;
    }

    public function getDrops(Item $item)
    {
        return [
            [self::DAYLIGHT_SENSOR, 0, 1]
        ];
    }
}
