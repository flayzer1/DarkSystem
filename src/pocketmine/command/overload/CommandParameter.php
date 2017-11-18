<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\command\overload;

class CommandParameter{
	
	const FLAG_VALID = 0x100000;
	const FLAG_ENUM = 0x200000;
	const FLAG_POSTFIX = 0x1000000;
	
	const TYPE_INT = 0x01;
	const TYPE_FLOAT = 0x02;
	const TYPE_VALUE = 0x03;
	const TYPE_TARGET = 0x04;
	const TYPE_STRING = 0x0d;
	const TYPE_POSITION = 0x0e;
	const TYPE_RAWTEXT = 0x11;
	const TYPE_TEXT = 0x13;
	const TYPE_JSON = 0x16;
	const TYPE_COMMAND = 0x1d;
	
	protected $name;
	protected $type;
	protected $optional;
	protected $enum;
	protected $flag;
	protected $postfix;
	
	public function __construct($name, $type = self::TYPE_STRING, $optional = true, $flag = self::FLAG_VALID, CommandEnum $enum = null, $postfix = ""){
		$this->name = $name;
		$this->type = $type;
		$this->enum = $enum;
		$this->optional = $optional;
		$this->flag = $flag;
		$this->postfix = $postfix;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getEnum(){
		return $this->enum;
	}
	
	public function getPostfix(){
		return $this->postfix;
	}
	
	public function isOptional(){
		return $this->optional;
	}
	
	public function getFlag(){
		return $this->flag;
	}
	
	public static function getTypeFromString($str){
		switch(strtolower($str)){
			case "int":
			 return self::TYPE_INT;
			case "float":
			 return self::TYPE_FLOAT;
			case "mixed":
			 return self::TYPE_MIXED;
			case "target":
			 return self::TYPE_TARGET;
			case "string":
			 return self::TYPE_STRING;
			case "pos":
			case "position":
			 return self::TYPE_POSITION;
			case "rawtext":
			case "raw_text":
			 return self::TYPE_RAWTEXT;
			case "text":
			 return self::TYPE_TEXT;
			case "json":
			 return self::TYPE_JSON;
			case "command":
			 return self::TYPE_COMMAND;
		}
		return self::TYPE_UNKNOWN;
	}
	
	public static function getFlagFromString($str){
		switch(strtolower($str)){
			case "valid":
			 return self::FLAG_VALID;
			case "enum":
			case "list":
			 return self::FLAG_ENUM;
			case "postfix":
			 return self::FLAG_POSTFIX;
		}
		return self::FLAG_VALID;
	}
}
