<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity;

use pocketmine\Server;

class Attribute
{
    const ABSORPTION = 0;
    const SATURATION = 1;
    const EXHAUSTION = 2;
    const KNOCKBACK_RESISTANCE = 3;
    const HEALTH = 4;
    const MOVEMENT_SPEED = 5;
    const FOLLOW_RANGE = 6;
    const HUNGER = 7;
    const FOOD = 7;
    const ATTACK_DAMAGE = 8;
    const EXPERIENCE_LEVEL = 9;
    const EXPERIENCE = 10;

    private $id;
    
    protected $minValue;
    protected $maxValue;
    protected $defaultValue;
    protected $currentValue;
    protected $name;
    protected $shouldSend;

    protected $desynchronized = true;
    
    protected static $attributes = [];

    public static function init()
    {
        Attribute::addAttribute(Attribute::ABSORPTION, "minecraft:absorption", 0.00, 340282346638528859811704183484516925440.00, 0.00);
        Attribute::addAttribute(Attribute::SATURATION, "minecraft:player.saturation", 0.00, 20.00, 5.00);
        Attribute::addAttribute(Attribute::EXHAUSTION, "minecraft:player.exhaustion", 0.00, 5.00, 0.41);
        Attribute::addAttribute(Attribute::KNOCKBACK_RESISTANCE, "minecraft:knockback_resistance", 0.00, 1.00, 0.00);
        Attribute::addAttribute(Attribute::HEALTH, "minecraft:health", 0.00, 20.00, 20.00);
        Attribute::addAttribute(Attribute::MOVEMENT_SPEED, "minecraft:movement", 0.00, 340282346638528859811704183484516925440.00, 0.10);
        Attribute::addAttribute(Attribute::FOLLOW_RANGE, "minecraft:follow_range", 0.00, 2048.00, 16.00, false);
        Attribute::addAttribute(Attribute::HUNGER, "minecraft:player.hunger", 0.00, 20.00, 20.00);
        Attribute::addAttribute(Attribute::ATTACK_DAMAGE, "minecraft:attack_damage", 0.00, 340282346638528859811704183484516925440.00, 1.00, false);
        Attribute::addAttribute(Attribute::EXPERIENCE_LEVEL, "minecraft:player.level", 0.00, 24791.00, 0.00);
        Attribute::addAttribute(Attribute::EXPERIENCE, "minecraft:player.experience", 0.00, 1.00, 0.00);
    }

    /**
     * @param int $id
     * @param string $name
     * @param float $minValue
     * @param float $maxValue
     * @param float $defaultValue
     * @param bool $shouldSend
     *
     * @return Attribute
     */
    public static function addAttribute($id, $name, $minValue, $maxValue, $defaultValue, $shouldSend = true)
    {
        if($minValue > $maxValue or $defaultValue > $maxValue or $defaultValue < $minValue){
            throw new \InvalidArgumentException("Invalid ranges: min value: $minValue, max value: $maxValue, $defaultValue: $defaultValue");
        }

        return Attribute::$attributes[(int)$id] = new Attribute($id, $name, $minValue, $maxValue, $defaultValue, $shouldSend);
    }

    /**
     * @param $id
     *
     * @return null|Attribute
     */
    public static function getAttribute($id)
    {
        return isset(Attribute::$attributes[$id]) ? clone Attribute::$attributes[$id] : null;
    }

    /**
     * @param $name
     *
     * @return null|Attribute
     */
    public static function getAttributeByName($name)
    {
        foreach (Attribute::$attributes as $a){
            if($a->getName() === $name){
                return clone $a;
            }
        }

        return null;
    }

    private function __construct($id, $name, $minValue, $maxValue, $defaultValue, $shouldSend = true)
    {
        $this->id = (int)$id;
        $this->name = (string)$name;
        $this->minValue = (float)$minValue;
        $this->maxValue = (float)$maxValue;
        $this->defaultValue = (float)$defaultValue;
        $this->shouldSend = (bool)$shouldSend;

        $this->currentValue = $this->defaultValue;
    }

    public function getMinValue()
    {
        return $this->minValue;
    }

    public function setMinValue($minValue)
    {
        if($minValue > $this->getMaxValue()){
            throw new \InvalidArgumentException("Value $minValue is bigger than the maxValue!");
        }

        if($this->minValue != $minValue){
            $this->desynchronized = true;
            $this->minValue = $minValue;
        }
        
        return $this;
    }

    public function getMaxValue()
    {
        return $this->maxValue;
    }

    public function setMaxValue($maxValue)
    {
        if($maxValue < $this->getMinValue()){
            throw new \InvalidArgumentException("Value $maxValue is bigger than the minValue!");
        }

        if($this->maxValue != $maxValue){
            $this->desynchronized = true;
            $this->maxValue = $maxValue;
        }
        
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setDefaultValue($defaultValue)
    {
        if($defaultValue > $this->getMaxValue() or $defaultValue < $this->getMinValue()){
            throw new \InvalidArgumentException("Value $defaultValue exceeds the range!");
        }

        if($this->defaultValue !== $defaultValue){
            $this->desynchronized = true;
            $this->defaultValue = $defaultValue;
        }
        
        return $this;
    }

    public function getValue()
    {
        return $this->currentValue;
    }

    public function setValue($value, $fit = true, $shouldSend = false)
    {
        if($value > $this->getMaxValue() or $value < $this->getMinValue()){
            if(!$fit){
                Server::getInstance()->getLogger()->error("[Attribute / {$this->getName()}] Value $value exceeds the range!");
            }
            
            $value = min(max($value, $this->getMinValue()), $this->getMaxValue());
        }

        if($this->currentValue != $value){
            $this->desynchronized = true;
            $this->currentValue = $value;
        }

        if($shouldSend){
            $this->desynchronized = true;
        }
        
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function isSyncable()
    {
        return $this->shouldSend;
    }

    public function isDesynchronized()
    {
        return $this->shouldSend and $this->desynchronized;
    }

    public function markSynchronized($synced = true)
    {
        $this->desynchronized = !$synced;
    }
}
