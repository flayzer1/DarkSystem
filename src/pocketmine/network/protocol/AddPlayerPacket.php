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

use pocketmine\utils\Binary;

class AddPlayerPacket extends PEPacket{
	
	const NETWORK_ID = Info::ADD_PLAYER_PACKET;
	const PACKET_NAME = "ADD_PLAYER_PACKET";

	public $uuid;
	public $username;
	public $eid;
	public $x;
	public $y;
	public $z;
	public $speedX;
	public $speedY;
	public $speedZ;
	public $pitch = 0.0;
	public $yaw = 0.0;
	public $item;
	public $metadata;
	public $links = [];
	
	/*public $flags = 0;
	public $commandPermission = 0;
	public $actionPermissions = AdventureSettingsPacket::ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS;
	public $permissionLevel = AdventureSettingsPacket::PERMISSION_LEVEL_MEMBER;
	public $storedCustomPermissions = 0;*/
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->putVarInt($this->eid);
		$this->putVarInt($this->eid);
		$this->putLFloat($this->x);
		$this->putLFloat($this->y);
		$this->putLFloat($this->z);
		$this->putLFloat($this->speedX);
		$this->putLFloat($this->speedY);
		$this->putLFloat($this->speedZ);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->yaw);
		$this->putSignedVarInt(0);

		$meta = Binary::writeMetadata($this->metadata, $playerProtocol);
		$this->put($meta);
		if($playerProtocol >= Info::PROTOCOL_120){
			/*$this->putVarInt($this->flags);
			$this->putVarInt($this->commandPermission);
			$this->putVarInt($this->actionPermissions);
			$this->putVarInt($this->permissionLevel);
			$this->putVarInt($this->storedCustomPermissions);
			if($this->eid & 1){
				$this->putLLong(-1 * (($this->eid + 1) >> 1));
			}else{
				$this->putLLong($this->eid >> 1);
			}*/
			$this->putVarInt(count($this->links));
			foreach($this->links as $link){
				$this->putVarInt($link["from"]);
				$this->putVarInt($link["to"]);
				$this->putByte($link["type"]);
				$this->putByte(0);
			}
		}
	}
}
