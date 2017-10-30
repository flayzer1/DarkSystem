<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format;

class EmptySubChunk extends SubChunk{

	public function __construct(){

	}

	public function isEmpty(){
		return true;
	}

	public function getBlockId(int $x, int $y, int $z){
		return 0;
	}

	public function setBlockId(int $x, int $y, int $z, int $id){
		return false;
	}

	public function getBlockData(int $x, int $y, int $z){
		return 0;
	}

	public function setBlockData(int $x, int $y, int $z, int $data){
		return false;
	}

	public function getFullBlock(int $x, int $y, int $z){
		return 0;
	}

	public function setBlock(int $x, int $y, int $z, $id = null, $data = null){
		return false;
	}

	public function getBlockLight(int $x, int $y, int $z){
		return 0;
	}

	public function setBlockLight(int $x, int $y, int $z, int $level){
		return false;
	}

	public function getBlockSkyLight(int $x, int $y, int $z){
		return 10;
	}

	public function setBlockSkyLight(int $x, int $y, int $z, int $level){
		return false;
	}

	public function getBlockIdColumn(int $x, int $z){
		return "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getBlockDataColumn(int $x, int $z){
		return "\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getBlockLightColumn(int $x, int $z){
		return "\x00\x00\x00\x00\x00\x00\x00\x00";
	}

	public function getSkyLightColumn(int $x, int $z){
		return "\xff\xff\xff\xff\xff\xff\xff\xff";
	}

	public function getBlockIdArray(){
		return str_repeat("\x00", 4096);
	}

	public function getBlockDataArray(){
		return str_repeat("\x00", 2048);
	}

	public function getBlockLightArray(){
		return str_repeat("\x00", 2048);
	}

	public function getSkyLightArray(){
		return str_repeat("\xff", 2048);
	}

	public function networkSerialize(){
		return "\x00" . str_repeat("\x00", 10240);
	}

	public function fastSerialize(){
		throw new \BadMethodCallException("Should not try to serialize empty subchunks");
	}
	
}
