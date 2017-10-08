<?php

namespace pocketmine\network\protocol;

class ResourcePackDataInfoPacket extends PEPacket{

	const NETWORK_ID = Info::RESOURCE_PACK_DATA_INFO_PACKET;
	const PACKET_NAME = "RESOURCE_PACK_DATA_INFO_PACKET";

	// read
	public function decode($playerProtocol) {
		$this->getHeader($playerProtocol);
	}
	
	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		
		$this->putString('53644fac-a276-42e5-843f-a3c6f169a9ab');
		$this->putInt(1);
		$this->putInt(0);
		$this->putLong(1);
		$this->putString('resources');
		
//		$this->putString('resourcePack.vanilla.name');
//		$this->putString('test');
//		$this->putVarInt(1);
//		$this->putVarInt(1);
//		$this->putVarInt(1);
//		
//		for ($i = 1; $i < 100; $i++) {
//			$this->buffer .= chr(1);
//			$this->buffer .= chr(0);
//		}
	}

}
