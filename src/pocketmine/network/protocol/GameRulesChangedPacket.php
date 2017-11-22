<?php

namespace pocketmine\network\protocol;

class GameRulesChangedPacket extends PEPacket{
	
	const NETWORK_ID = Info::GAME_RULES_CHANGED_PACKET;

	public $rules = [];

	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
		$count = $this->getVarInt();
		for ($i = 0; $i < $count; $i++){
			$this->rules[$i] = [];
			$this->rules[$i]["NAME"] = $this->getString();
			$this->rules[$i]["BOOL1"] = $this->getBool();
			$this->rules[$i]["BOOL2"] = $this->getBool();
		}
	}

	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		$this->putVarInt(count($this->rules));
		foreach($this->rules as $rule){
			$this->putString($rule["NAME"]);
			$this->putBool($rule["BOOL1"]);
			$this->putBool($rule["BOOL2"]);
		}
	}
}
