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

use pocketmine\network\multiversion\MultiversionTags;

class TextPacket extends PEPacket{
	
	const NETWORK_ID = Info::TEXT_PACKET;
	const PACKET_NAME = "TEXT_PACKET";

	const TYPE_RAW = 0;
	const TYPE_CHAT = 1;
	const TYPE_TRANSLATION = 2;
	const TYPE_POPUP = 3;
	const TYPE_JUKEBOX_POPUP = 4;
	const TYPE_TIP = 5;
	const TYPE_SYSTEM = 6;
	const TYPE_WHISPER = 7;
	const TYPE_ANNOUNCEMENT = 8;
	
	/*const TYPE_RAW = "TYPE_RAW";
	const TYPE_CHAT = "TYPE_CHAT";
	const TYPE_TRANSLATION = "TYPE_TRANSLATION";
	const TYPE_POPUP = "TYPE_POPUP";
	const TYPE_JUKEBOX_POPUP = "TYPE_JUKEBOX_POPUP";
	const TYPE_TIP = "TYPE_TIP";
	const TYPE_SYSTEM = "TYPE_SYSTEM";
	const TYPE_WHISPER = "TYPE_WHISPER";
	const TYPE_ANNOUNCEMENT = "TYPE_ANNOUNCEMENT";*/
	
	public $type;
	public $source;
	public $message;
	public $parameters = [];
	public $isLocalize = false;
	public $xuid = "";
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		$this->type = $this->getByte();
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->isLocalize = $this->getByte();
		}
		//$this->type = MultiversionEnums::getMessageType($playerProtocol, $this->type);
		switch($this->type){
			case TextPacket::TYPE_POPUP:
			case TextPacket::TYPE_CHAT:
				$this->source = $this->getString();
			case TextPacket::TYPE_RAW:
			case TextPacket::TYPE_TIP:
			case TextPacket::TYPE_SYSTEM:
				$this->message = $this->getString();
				break;
			case TextPacket::TYPE_TRANSLATION:
				$this->message = $this->getString();
				$count = $this->getByte();
				for($i = 0; $i < $count; ++$i){
					$this->parameters[] = $this->getString();
				}
		}
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->xuid = $this->getString();
		}
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		//$typeId = MultiversionTags::getMessageTypeId($playerProtocol, $this->type);
		//$this->putByte($typeId);
		$this->putByte($this->type);
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->putByte($this->isLocalize);
		}
		switch($this->type){
			case TextPacket::TYPE_POPUP:
			case TextPacket::TYPE_CHAT:
            case TextPacket::TYPE_WHISPER:
				$this->putString($this->source);
			case TextPacket::TYPE_RAW:
			case TextPacket::TYPE_TIP:
			case TextPacket::TYPE_SYSTEM:
            case TextPacket::TYPE_WHISPER:
				$this->putString($this->message);
				break;
			case TextPacket::TYPE_TRANSLATION:
				$this->putString($this->message);
				$this->putByte(count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
			case TextPacket::TYPE_JUKEBOX_POPUP:
				$this->putString($this->message);
				$this->putVarInt(count($this->parameters));
				foreach($this->parameters as $p){
					$this->putString($p);
				}
		}
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->putString($this->xuid);
		}
	}
}
