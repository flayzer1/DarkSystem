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

class AdventureSettingsPacket extends PEPacket{
	
	const NETWORK_ID = Info::ADVENTURE_SETTINGS_PACKET;
	const PACKET_NAME = "ADVENTURE_SETTINGS_PACKET";

	const ACTION_FLAG_PROHIBIT_ALL = 0;
	const ACTION_FLAG_BUILD_AND_MINE = 1;
	const ACTION_FLAG_DOORS_AND_SWITCHES = 2;
	const ACTION_FLAG_OPEN_CONTAINERS = 4;
	const ACTION_FLAG_ATTACK_PLAYERS = 8;
	const ACTION_FLAG_ATTACK_MOBS = 16;
	const ACTION_FLAG_OP = 32;
	const ACTION_FLAG_TELEPORT = 64;
	const ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS = 128;
	const ACTION_FLAG_ALLOW_ALL = 511;
	
	const PERMISSION_LEVEL_VISITOR = 0;
	const PERMISSION_LEVEL_MEMBER = 1;
	const PERMISSION_LEVEL_OPERATOR = 2;
	const PERMISSION_LEVEL_CUSTOM = 3;
	
	public $flags = 0;
	public $actionPermissions = self::ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS;
	public $permissionLevel = self::PERMISSION_LEVEL_MEMBER;
	public $customStoredPermissions = 0;
	public $userId = 0;
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
        $this->flags = $this->getVarInt();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->flags);
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
		}
	}
}
