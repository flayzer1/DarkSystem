<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace pocketmine\network\protocol;

use pocketmine\network\protocol\types\PlayerPermissions;

class AdventureSettingsPacket extends PEPacket{
	
	const NETWORK_ID = Info::ADVENTURE_SETTINGS_PACKET;
	const PACKET_NAME = "ADVENTURE_SETTINGS_PACKET";
	
	const BITFLAG_SECOND_SET = 1 << 16;
	
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
	
	const WORLD_IMMUTABLE = 0x01;
	const NO_PVP = 0x02;

	const AUTO_JUMP = 0x20;
	const ALLOW_FLIGHT = 0x40;
	const NO_CLIP = 0x80;
	const WORLD_BUILDER = 0x100;
	const FLYING = 0x200;
	const MUTED = 0x400;

	const BUILD_AND_MINE = 0x01 | self::BITFLAG_SECOND_SET;
	const DOORS_AND_SWITCHES = 0x02 | self::BITFLAG_SECOND_SET;
	const OPEN_CONTAINERS = 0x04 | self::BITFLAG_SECOND_SET;
	const ATTACK_PLAYERS = 0x08 | self::BITFLAG_SECOND_SET;
	const ATTACK_MOBS = 0x10 | self::BITFLAG_SECOND_SET;
	const OPERATOR = 0x20 | self::BITFLAG_SECOND_SET;
	const TELEPORT = 0x80 | self::BITFLAG_SECOND_SET;
	
	const PERMISSION_LEVEL_VISITOR = 0;
	const PERMISSION_LEVEL_MEMBER = 1;
	const PERMISSION_LEVEL_OPERATOR = 2;
	const PERMISSION_LEVEL_CUSTOM = 3;
	
	const PERMISSION_NORMAL = 0;
	const PERMISSION_OPERATOR = 1;
	const PERMISSION_HOST = 2;
	const PERMISSION_AUTOMATION = 3;
	const PERMISSION_ADMIN = 4;
	
	public $flags = 0;
	public $commandPermission = self::PERMISSION_NORMAL;
	public $flags2 = -1;
	public $playerPermission = PlayerPermissions::MEMBER;
	public $actionPermissions = self::ACTION_FLAG_DEFAULT_LEVEL_PERMISSIONS;
	public $permissionLevel = self::PERMISSION_LEVEL_MEMBER;
	public $customFlags = 0;
	public $userId = 0;
	public $eid;
	
	public function decode($playerProtocol){
        $this->flags = $this->getVarInt();
        $this->commandPermission = $this->getVarInt();
		$this->flags2 = $this->getVarInt();
		$this->playerPermission = $this->getVarInt();
		$this->customFlags = $this->getVarInt();
		$this->eid = $this->getLLong();
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->flags);
		$this->putVarInt($this->commandPermission);
		$this->putVarInt($this->flags2);
		$this->putVarInt($this->playerPermission);
		$this->putVarInt($this->customFlags);
		$this->putLLong($this->eid);
		$this->putVarInt(0);
		switch($playerProtocol){
			case Info::PROTOCOL_120:
				$this->putVarInt($this->actionPermissions);
				$this->putVarInt($this->permissionLevel);
				if($this->userId & 1){
					$this->putLLong(-1 * (($this->userId + 1) >> 1));
				}else{
					$this->putLLong($this->userId >> 1);
				}
				break;
		}
	}
	
	public function getFlag($flag){
		if($flag & self::BITFLAG_SECOND_SET){
			return ($this->flags2 & $flag) !== 0;
		}

		return ($this->flags & $flag) !== 0;
	}

	public function setFlag($flag, $value){
		if($flag & self::BITFLAG_SECOND_SET){
			$flagSet =& $this->flags2;
		}else{
			$flagSet =& $this->flags;
		}

		if($value){
			$flagSet |= $flag;
		}else{
			$flagSet &= ~$flag;
		}
	}
}
