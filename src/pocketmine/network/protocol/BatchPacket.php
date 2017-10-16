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

class BatchPacket extends PEPacket{
	
	const NETWORK_ID = Info::BATCH_PACKET;
	const PACKET_NAME = "BATCH_PACKET";

	public $payload;
	public $is110 = false;

	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		if($this->is110){
			$playerProtocol = Info::PROTOCOL_110;
		}
		switch($playerProtocol){
			case Info::PROTOCOL_120:
			case Info::PROTOCOL_110:
				$this->payload = $this->get(true);
				break;
				default;
				$this->payload = $this->getString();
				break;
		}
	}

	public function encode($playerProtocol){
		switch($playerProtocol){
			case Info::PROTOCOL_120:
			case Info::PROTOCOL_110:
				$this->buffer = $this->payload;
				break;
				default;
				$this->reset($playerProtocol);
				$this->putString($this->payload);
				break;
		}
	}
}
