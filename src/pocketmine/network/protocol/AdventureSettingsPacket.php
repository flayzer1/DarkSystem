<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\network\protocol;

use pocketmine\network\protocol\types\PlayerPermissions;

class AdventureSettingsPacket extends PEPacket{
	
	const NETWORK_ID = Info::ADVENTURE_SETTINGS_PACKET;
	const PACKET_NAME = "ADVENTURE_SETTINGS_PACKET";

	const PERMISSION_NORMAL = 0;
	const PERMISSION_OPERATOR = 1;
	const PERMISSION_HOST = 2;
	const PERMISSION_AUTOMATION = 3;
	const PERMISSION_ADMIN = 4;

	const WORLD_IMMUTABLE = 1;
	const NO_PVP = 2;
	const NO_PVM = 4;
	const NO_MVP = 8;
	const NO_EVP = 16;
	const AUTO_JUMP = 32;
	const ALLOW_FLIGHT = 64;
	const NO_CLIP = 128;
	const FLYING = 512;
	const MUTED = 1024;
	
	const WORLD_BUILDER = 0x100;

	const BUILD_AND_MINE = 1;
	const DOORS_AND_SWITCHES = 2;
	const OPEN_CONTAINERS = 4;
	const ATTACK_PLAYERS = 8;
	const ATTACK_MOBS = 16;
	const OPERATOR = 32;
	const TELEPORT = 64;
	
	/*public $flags = 0;
	public $actionPermissions = self::ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS;
	public $permissionLevel = self::PERMISSION_LEVEL_MEMBER;
	public $customStoredPermissions = 0;
	public $userId = 0;*/
	
	public $flags = 0;
	public $commandPermission = self::PERMISSION_NORMAL;
	public $abilities = -1;
	public $playerPermission = PlayerPermissions::MEMBER;
	public $customPermissions = 0;
	public $eid;
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		$this->flags = $this->getVarInt();
		$this->commandPermission = $this->getVarInt();
		$this->abilities = $this->getVarInt();
		$this->playerPermission = $this->getVarInt();
		$this->customPermissions = $this->getVarInt();
		$this->eid = $this->getLLong();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->flags);
		$this->putVarInt($this->commandPermission);
		$this->putVarInt($this->abilities);
		$this->putVarInt($this->playerPermission);
		$this->putVarInt($this->customPermissions);
		$this->putLLong($this->eid);
		/*$this->putVarInt($this->flags);
		$this->putVarInt(0);
		switch($playerProtocol){
			case Info::PROTOCOL_120:
				$this->putVarInt($this->actionPermissions);
				$this->putVarInt($this->permissionLevel);
				$this->putVarInt($this->customStoredPermissions);
				if($this->userId & 1){
					$this->putLLong(-1 * (($this->userId + 1) >> 1));
				}else{
					$this->putLLong($this->userId >> 1);
				}
				
				break;
		}*/
	}
	
	public function setPlayerFlag($flag, $value = true){
		if($value){
			$this->flags |= $flag;
		}
	}
	
	public function getPlayerFlag($flag){
		return ($this->flags & $flag) !== 0;
	}
	
	public function setAbility($flag, $value = true){
		if($value){
			$this->abilities |= $flag;
		}
	}
	
	public function getAbility($flag){
		return ($this->abilities & $flag) !== 0;
	}
	
	public function setCustomPermission($flag, $value = true){
		if($value){
			$this->customPermissions |= $flag;
		}
	}
	
	public function getCustomPermission($flag){
		return ($this->customPermissions & $flag) !== 0;
	}
}
