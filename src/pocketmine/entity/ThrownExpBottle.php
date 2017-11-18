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

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\level\particle\SpellParticle;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;

class ThrownExpBottle extends Projectile
{
    const NETWORK_ID = self::THROWN_EXP_BOTTLE;

    public $width = 0.25;
    public $length = 0.25;
    public $height = 0.25;

    protected $gravity = 0.1;
    protected $drag = 0.15;

    private $hasSplashed = false;

    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null)
    {
        parent::__construct($level, $nbt, $shootingEntity);
    }

    public function splash()
    {
        if (!$this->hasSplashed) {
            $this->hasSplashed = true;
            //$this->getLevel()->addParticle(new SpellParticle($this, 46, 82, 153));
            if ($this->getLevel()->getServer()->expEnabled) {
                $this->getLevel()->spawnXPOrb($this->add(0, -0.2, 0), mt_rand(1, 4));
                $this->getLevel()->spawnXPOrb($this->add(-0.1, -0.2, 0), mt_rand(1, 4));
                $this->getLevel()->spawnXPOrb($this->add(0, -0.2, -0.1), mt_rand(1, 4));
            }

            $this->kill();
        }
    }

    public function onUpdate($currentTick)
    {
        if ($this->closed) {
            return false;
        }

        $this->timings->startTiming();

        $hasUpdate = parent::onUpdate($currentTick);

        $this->age++;

        if ($this->age > 1200 or $this->isCollided) {
            $this->splash();
            $hasUpdate = true;
        }

        $this->timings->stopTiming();

        return $hasUpdate;
    }

    public function spawnTo(Player $player)
    {
        $pk = new AddEntityPacket();
        $pk->type = ThrownExpBottle::NETWORK_ID;
        $pk->eid = $this->getId();
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = $this->motionX;
        $pk->speedY = $this->motionY;
        $pk->speedZ = $this->motionZ;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }
}
