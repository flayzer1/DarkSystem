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

class StartGamePacket extends PEPacket{
	
	const NETWORK_ID = Info::START_GAME_PACKET;
	const PACKET_NAME = "START_GAME_PACKET";

	public $seed;
	public $dimension;
	public $generator = 1;
	public $gamemode;
	public $eid;
	public $spawnX;
	public $spawnY;
	public $spawnZ;
	public $x;
	public $y;
	public $z;
	
	public $defaultPlayerPermission = PlayerPermissions::MEMBER;
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt($this->eid);
		$this->putVarInt($this->eid);
		
		if($playerProtocol >= Info::PROTOCOL_110){
 			$this->putSignedVarInt($this->gamemode);
 		}
		
		$this->putLFloat($this->x);
		$this->putLFloat($this->y);
		$this->putLFloat($this->z);
		
		$this->putLFloat(0);
		$this->putLFloat(0);
		
		$this->putSignedVarInt($this->seed);
		
		$this->putSignedVarInt($this->dimension);
		
		$this->putSignedVarInt($this->generator);
		
		$this->putSignedVarInt($this->gamemode);
		
		$this->putSignedVarInt(0);
		
		$this->putSignedVarInt($this->spawnX);
		$this->putSignedVarInt($this->spawnY);
		$this->putSignedVarInt($this->spawnZ);
		
		$this->putByte(1);
		
		$this->putSignedVarInt(0);
		
		$this->putByte(0);

		$this->putLFloat(0);

		$this->putLFloat(0);
		
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->putByte(1);
			$this->putByte(1);
			$this->putByte(1);
		}
		
		$this->putByte(1);
		
		$this->putByte(0);
		
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->putVarInt(0);
			$this->putByte(0);
			$this->putByte(0);
			$this->putByte(0);
			$this->putSignedVarInt($this->defaultPlayerPermission);
			$this->putSignedVarInt(4);
			$this->putString("3138ee93-4a4a-479b-8dca-65ca5399e075");
			$this->putString("");
			$this->putString("");
			$this->putByte(0);
			$this->putLong(0);
			$this->putSignedVarInt(0);
		}
	}
}
