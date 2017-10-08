<?php

namespace pocketmine\network\protocol;

class CameraPacket extends DataPacket{
	
	const NETWORK_ID = Info::CAMERA_PACKET;
	public $eid;
	
	public function decode(){
		$this->getHeader($playerProtocol);
	}
	
	public function encode(){
		$this->reset();
		$this->putVarInt($this->eid);
		$this->putVarInt($this->eid);
	}
	
}