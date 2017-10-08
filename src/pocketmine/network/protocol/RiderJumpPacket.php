<?php

namespace pocketmine\network\protocol;

class RiderJumpPacket extends PEPacket{
	
	const NETWORK_ID = Info::RIDER_JUMP_PACKET;

	public $power;

	public function decode(){
		$this->getHeader($playerProtocol);
		$this->power = $this->getVarInt();
	}

	public function encode(){
		$this->reset();
		$this->putVarInt($this->power);
	}

}