<?php

namespace pocketmine\network\protocol;

class ResourcePackInfoPacket extends PEPacket{

	const NETWORK_ID = Info::RESOURCE_PACK_INFO_PACKET;
	const PACKET_NAME = "RESOURCE_PACK_INFO_PACKET";
	
	public function decode($playerProtocol) {
		$this->getHeader($playerProtocol);
	}
	
	public function encode($playerProtocol) {
		$this->reset($playerProtocol);
		
		$this->putByte(0);
		
		$this->putShort(0);
		
		$this->putShort(0);
	}

}
