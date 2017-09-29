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

use pocketmine\utils\BinaryStream;

abstract class DataPacket extends BinaryStream{

	const NETWORK_ID = 0;
	const PACKET_NAME = "";

	public $isEncoded = false;
	private $channel = 0;
	
	protected static $packetsIds = [];

	public function pid(){
		return DataPacket::NETWORK_ID;
	}
	
	public function pname(){
		return DataPacket::PACKET_NAME;
	}
	
	public function setChannel($channel){
		$this->channel = (int) $channel;
		return $this;
	}

	public function getChannel(){
		return $this->channel;
	}

	public function clean(){
		$this->buffer = null;
		$this->isEncoded = false;
		$this->offset = 0;
		return $this;
	}
	
	public static function initializePackets(){
		$oClass = new \ReflectionClass('pocketmine\network\protocol\Info');
		DataPacket::$packetsIds[Info::BASE_PROTOCOL] = $oClass->getConstants();
		$oClass = new \ReflectionClass('pocketmine\network\protocol\Info105');
		DataPacket::$packetsIds[Info::PROTOCOL_105] = $oClass->getConstants();
		$oClass = new \ReflectionClass('pocketmine\network\protocol\Info110');
		DataPacket::$packetsIds[Info::PROTOCOL_110] = $oClass->getConstants();
		$oClass = new \ReflectionClass('pocketmine\network\protocol\Info120');
		DataPacket::$packetsIds[Info::PROTOCOL_120] = $oClass->getConstants();
	}
	
}
