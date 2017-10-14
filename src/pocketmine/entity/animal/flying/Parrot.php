<?php

namespace pocketmine\entity\animal\flying;

use pocketmine\entity\animal\FlyingAnimal;
use pocketmine\entity\Creature;

class Parrot extends FlyingAnimal{
	
    const NETWORK_ID = 30;

    public $width = 0.5;
    public $length = 0.484;
    public $height = 0.9;
    
    public function getSpeed(){
        return 1.1;
    }

    public function getName(){
        return "Parrot";
    }

    public function targetOption(Creature $creature, $distance){
        return false;
    }

    public function getDrops(){
        return [];
    }
    
    public function getMaxHealth(){
        return 6;
    }
    
    public function dance(){
    	
    }
    
}
