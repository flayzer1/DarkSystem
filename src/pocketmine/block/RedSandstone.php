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

class RedSandstone extends Sandstone
{
    protected $id = Block::RED_SANDSTONE;

    public function getName()
    {
        static $names = [
            0 => "Red Sandstone",
            1 => "Chiseled Red Sandstone",
            2 => "Smooth Red Sandstone",
            3 => "",
        ];
        
        return $names[$this->meta & 0x03];
    }
}