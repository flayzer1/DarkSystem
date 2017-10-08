<?php

namespace pocketmine\network\protocol;

class ChunkRadiusUpdatedPacket extends PEPacket{

	const NETWORK_ID = Info::CHUNK_RADIUS_UPDATED_PACKET;

	public $radius;

	public function decode(){
		$this->getHeader($playerProtocol);
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->radius);
	}
	
}