<?php

namespace pocketmine\network\protocol;

abstract class PEPacket extends DataPacket{
	
	const CLIENT_ID_MAIN_PLAYER = 0;
	const CLIENT_ID_SERVER = 0;
	
	public $senderSubClientID = PEPacket::CLIENT_ID_SERVER;
	
	public $targetSubClientID = PEPacket::CLIENT_ID_MAIN_PLAYER;
	
	abstract public function encode($playerProtocol);

	abstract public function decode($playerProtocol);
	
	protected function checkLength($len){
		if($this->offset + $len > strlen($this->buffer)){
			throw new \Exception(get_class($this) . ": Try get {$len} bytes, offset = " . $this->offset . ", bufflen = " . strlen($this->buffer) . ", buffer = " . bin2hex(substr($this->buffer, 0, 250)));
		}
	}
	
	protected function getHeader($playerProtocol = 0){
		if($playerProtocol >= Info::PROTOCOL_120) {
			$this->senderSubClientID = $this->getByte();
			$this->targetSubClientID = $this->getByte();
			if($this->senderSubClientID > 4 || $this->targetSubClientID > 4){
				throw new \Exception(get_class($this) . ": Packet decode headers error");
			}
		}
	}
	
	public function reset($playerProtocol = 0){
		$this->buffer = chr(PEPacket::$packetsIds[$playerProtocol][$this::PACKET_NAME]);
		$this->offset = 0;
		if($playerProtocol >= Info::PROTOCOL_120){
			$this->putByte($this->senderSubClientID);
			$this->putByte($this->targetSubClientID);
			$this->offset = 2;
		}
	}
	
	public final static function convertProtocol($protocol){
		switch($protocol){
			case Info::PROTOCOL_120:
			case Info::PROTOCOL_121;
			case Info::PROTOCOL_130;
			case Info::PROTOCOL_131;
			case Info::PROTOCOL_132;
			case Info::PROTOCOL_133;
			case Info::PROTOCOL_134;
			case Info::PROTOCOL_135;
			case Info::PROTOCOL_136;
			case Info::PROTOCOL_137;
				return Info::PROTOCOL_120;
			case Info::PROTOCOL_110:
			case Info::PROTOCOL_111:
			case Info::PROTOCOL_112:
			case Info::PROTOCOL_113:
				return Info::PROTOCOL_110;
			case Info::PROTOCOL_105:
			case Info::PROTOCOL_106:
			case Info::PROTOCOL_107:
				return Info::PROTOCOL_105;
			default:
				return Info::BASE_PROTOCOL;
		}
	}
}
