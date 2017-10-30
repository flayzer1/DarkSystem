<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\entity\morph;

use pocketmine\entity\morph\entities\Morph;
use pocketmine\entity\morph\entities\MorphCreeper;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class MorphManager{
	
    public $id;
    public $eid;
    public $owner;

    public function __construct($server){
    	$this->server = $server;
        Entity::registerEntity(MorphCreeper::class);
    }

    public function createNBT($x, $y, $z, $yaw, $pitch){
        $nbt = new CompoundTag;
        $nbt->Pos = new ListTag("Pos", [
            new DoubleTag("", $x),
            new DoubleTag("", $y),
            new DoubleTag("", $z)
        ]);
        
        $nbt->Rotation = new ListTag("Rotation", [
            new FloatTag("", $yaw),
            new FloatTag("", $pitch)
        ]);
        
        $nbt->Health = new ShortTag("Health", 1);
        $nbt->Invulnerable = new ByteTag("Invulnerable", 1);
        
        return $nbt;
    }

    public function spawn(Player $player, $name){
        $entity = Entity::createEntity($name, $player->getLevel(), $this->createNBT($player->x, $player->y, $player->z, $player->yaw, $player->pitch));
        $entity->spawnToAll();
        $this->eid[$player->getName()] = $entity->getId();
        $entity->setNameTag($player->getName());
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTagVisible(true);
        foreach($this->server->getOnlinePlayers() as $p){
            $p->hidePlayer($player);
        }
        $player->addEffect(Effect::getEffect(14)->setDuration(9999999999)->setVisible(false));
    }

    public function moveEntity(Player $player, $entityId){
        $chunk = $player->getLevel()->getChunk($player->x >> 4, $player->z >> 4);
        $player->getLevel()->addEntityMovement(
            $chunk->getX(), $chunk->getZ(),
            $entityId,
            $player->x, $player->y, $player->z,
            $player->yaw, $player->pitch
        );
    }

    public function removeMob(Player $player){
        if(isset($this->eid[$player->getName()])){
            $player->getLevel()->getEntity($this->eid[$player->getName()])->kill();
            unset($this->eid[$player->getName()]);
            foreach($this->server->getOnlinePlayers() as $p){
                $p->showPlayer($player);
            }
        }
    }
}
