<?php

namespace pocketmine\entity\effects;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;

class LevitationEffect extends Effect{

	public function add(Entity $entity, $modify = false){
		parent::add($entity, $modify);
		//TODO
	}

	public function remove(Entity $entity){
		parent::remove($entity);
		//TODO
	}

}