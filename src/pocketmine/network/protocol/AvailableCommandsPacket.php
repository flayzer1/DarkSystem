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

class AvailableCommandsPacket extends PEPacket{

	const NETWORK_ID = Info::AVAILABLE_COMMANDS_PACKET;
	const PACKET_NAME = "AVAILABLE_COMMANDS_PACKET";
	
	const ARG_FLAG_VALID = 0x100000;
	const ARG_FLAG_ENUM = 0x200000;
	const ARG_TYPE_INT = 0x01;
	const ARG_TYPE_FLOAT = 0x02;
	const ARG_TYPE_VALUE = 0x03;
	const ARG_TYPE_TARGET = 0x04;
	const ARG_TYPE_STRING = 0x0c;
	const ARG_TYPE_POSITION = 0x0d;
	const ARG_TYPE_RAWTEXT = 0x10;
	const ARG_TYPE_TEXT = 0x12;
	const ARG_TYPE_JSON = 0x15;
	const ARG_TYPE_COMMAND = 0x1c;
	
	static private $commandsBuffer = [];
	
	public $commands;
	
	public function decode($playerProtocol){
		$this->getHeader($playerProtocol);
	}
	
	public function encode($playerProtocol){
		$this->reset($playerProtocol);
		if(isset(self::$commandsBuffer[$playerProtocol])){
			$this->put(self::$commandsBuffer[$playerProtocol]);
		}else{
			//$this->putString(self::$commandsBuffer['default']);
		}
	}
	
	public static function prepareCommands($commands){
		self::$commandsBuffer['default'] = json_encode($commands);
		
		$enumValues = [];
		$enumValuesCount = 0;
		$enumAdditional = [];
		$enums = [];
		$commandsStream = new BinaryStream();
		foreach($commands as $commandName => &$commandData){
			if($commandName == "help"){
				continue;
			}
			$commandsStream->putString($commandName);
			$commandsStream->putString($commandData['versions'][0]['description']);
			$commandsStream->putByte(0);
			$commandsStream->putByte(0);
			if(isset($commandData['versions'][0]['aliases']) && !empty($commandData['versions'][0]['aliases'])){
				foreach($commandData['versions'][0]['aliases'] as $alias){
					$aliasAsCommand = $commandData;
					$aliasAsCommand['versions'][0]['aliases'] = [];
					$commands[$alias] = $aliasAsCommand;
				}
				$commandData['versions'][0]['aliases'] = [];
			}
			$aliasesEnumId = -1;
			$commandsStream->putLInt($aliasesEnumId);
			$commandsStream->putVarInt(count($commandData['versions'][0]['overloads']));
			foreach($commandData['versions'][0]['overloads'] as $overloadData){
				$commandsStream->putVarInt(count($overloadData['input']['parameters']));
				$paramNum = count($overloadData['input']['parameters']);
				foreach($overloadData['input']['parameters'] as $paramData){
					$commandsStream->putString($paramData['name']);
					$isParamOneAndOptional = ($paramNum == 1 && isset($paramData['optional']) && $paramData['optional']);
					if($paramData['type'] == "rawtext" && ($paramNum > 1 || $isParamOneAndOptional)){
						$commandsStream->putLInt(self::ARG_FLAG_VALID | self::getFlag('string'));
					}else{
						$commandsStream->putLInt(self::ARG_FLAG_VALID | self::getFlag($paramData['type']));
					}
					$commandsStream->putByte(isset($paramData['optional']) && $paramData['optional']);
				}
			}
		}
		
		$additionalDataStream = new BinaryStream();
		$additionalDataStream->putVarInt($enumValuesCount);
		for($i = 0; $i < $enumValuesCount; $i++){
			$additionalDataStream->putString($enumValues[$i]);
		}
		$additionalDataStream->putVarInt(0);
		$enumsCount = count($enums);
		$additionalDataStream->putVarInt($enumsCount);
		for($i = 0; $i < $enumsCount; $i++){
			$additionalDataStream->putString($enums[$i]['name']);
			$dataCount = count($enums[$i]['data']);
			$additionalDataStream->putVarInt($dataCount);
			for($j = 0; $j < $dataCount; $j++){
				if($enumValuesCount < 256){
					$additionalDataStream->putByte($enums[$i]['data'][$j]);
				}elseif($enumValuesCount < 65536){
					$additionalDataStream->putLShort($enums[$i]['data'][$j]);
				}else{
					$additionalDataStream->putLInt($enums[$i]['data'][$j]);
				}	
			}
		}
		
		$additionalDataStream->putVarInt(count($commands));
		$additionalDataStream->put($commandsStream->buffer);
		self::$commandsBuffer[Info::PROTOCOL_120] = $additionalDataStream->buffer;
	}
	
    private static function getFlag($paramName){
            switch ($paramName){
            	case "int":
			    return self::ARG_TYPE_INT;
            	case "float":
                	return self::ARG_TYPE_FLOAT;
            	case "mixed":
                	return self::ARG_TYPE_VALUE;
            	case "target":
               		return self::ARG_TYPE_TARGET;
            	case "string":
                	return self::ARG_TYPE_STRING;
            	case "xyz":
                	return self::ARG_TYPE_POSITION;
            	case "rawtext":
                	return self::ARG_TYPE_RAWTEXT;
            	case "text":
                	return self::ARG_TYPE_TEXT;
            	case "json":
                	return self::ARG_TYPE_JSON;
            	case "command":
                	return self::ARG_TYPE_COMMAND;
            }
            
            return 0;
    }
}
