<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\nbt;

use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\EndTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\utils\Binary;
use pocketmine\utils\Utils;

class NBT{

	const LITTLE_ENDIAN = 0;
	const BIG_ENDIAN = 1;
	
	const TAG_End = 0;
	const TAG_Byte = 1;
	const TAG_Short = 2;
	const TAG_Int = 3;
	const TAG_Long = 4;
	const TAG_Float = 5;
	const TAG_Double = 6;
	const TAG_ByteArray = 7;
	const TAG_String = 8;
	const TAG_List = 9, TAG_Enum = 9;
	const TAG_Compound = 10;
	const TAG_IntArray = 11;

	public $buffer;
	public $endianness;
	
	private $data;
	private $offset;
	
	/**
	 * @param Item $item
	 * @param int  $slot
	 * @return CompoundTag
	 */
	public static function putItemHelper(Item $item, $slot = null){
		$tag = new CompoundTag("Item", [
			"id" => new ShortTag("id", $item->getId()),
			"Count" => new ByteTag("Count", $item->getCount()),
			"Damage" => new ShortTag("Damage", $item->getDamage())
		]);

		if($slot !== null){
			$tag->Slot = new ByteTag("Slot", (int) $slot);
		}

		if($item->hasCompoundTag()){
			$tag->tag = clone $item->getNamedTag();
			//$tag->tag->setName("tag");
		}

		return $tag;
	}

	/**
	 * @param CompoundTag $tag
	 * @return Item
	 */
	public static function getItemHelper(CompoundTag $tag){
		if(!isset($tag->id) || !isset($tag->Count)){
			return Item::get(0);
		}

		$item = Item::get($tag->id->getValue(), !isset($tag->Damage) ? 0 : $tag->Damage->getValue(), $tag->Count->getValue());
		
		if(isset($tag->tag) && $tag->tag instanceof CompoundTag){
			$item->setNamedTag($tag->tag);
		}

		return $item;
	}

	public static function matchList(ListTag $tag1, ListTag $tag2){
		if($tag1->getName() !== $tag2->getName() || $tag1->getCount() !== $tag2->getCount()){
			return false;
		}

		foreach($tag1 as $k => $v){
			if(!($v instanceof Tag)){
				continue;
			}

			if(!isset($tag2->{$k}) || !($tag2->{$k} instanceof $v)){
				return false;
			}

			if($v instanceof CompoundTag){
				if(!NBT::matchTree($v, $tag2->{$k})){
					return false;
				}
			}elseif($v instanceof ListTag){
				if(!NBT::matchList($v, $tag2->{$k})){
					return false;
				}
			}else{
				if($v->getValue() !== $tag2->{$k}->getValue()){
					return false;
				}
			}
		}

		return true;
	}

	public static function matchTree(CompoundTag $tag1, CompoundTag $tag2){
		if($tag1->getCount() !== $tag2->getCount()){
			return false;
		}

		foreach($tag1 as $k => $v){
			if(!($v instanceof Tag)){
				continue;
			}

			if(!isset($tag2->{$k}) || !($tag2->{$k} instanceof $v)){
				return false;
			}

			if($v instanceof CompoundTag){
				if(!NBT::matchTree($v, $tag2->{$k})){
					return false;
				}
			}elseif($v instanceof ListTag){
				if(!NBT::matchList($v, $tag2->{$k})){
					return false;
				}
			}else{
				if($v->getValue() !== $tag2->{$k}->getValue()){
					return false;
				}
			}
		}

		return true;
	}

	public static function parseJSON($data, &$offset = 0){
		$len = strlen($data);
		for(; $offset < $len; ++$offset){
			$c = $data{$offset};
			if($c === "{"){
				++$offset;
				$data = NBT::parseCompoundTag($data, $offset);
				return new CompoundTag("", $data);
			}elseif($c !== " " && $c !== "\r" && $c !== "\n" && $c !== "\t"){
				throw new \Exception("Sözdizimi Hatası: unexpected '$c' at offset $offset");
			}
		}

		return null;
	}

	private static function parseList($str, &$offset = 0){
		$len = strlen($str);
		
		$key = 0;
		$value = null;

		$data = [];

		for(; $offset < $len; ++$offset){
			if($str{$offset - 1} === "]"){
				break;
			}elseif($str{$offset} === "]"){
				++$offset;
				break;
			}

			$value = NBT::readValue($str, $offset, $type);

			switch($type){
				case NBT::TAG_Byte:
					$data[$key] = new ByteTag($key, $value);
					break;
				case NBT::TAG_Short:
					$data[$key] = new ShortTag($key, $value);
					break;
				case NBT::TAG_Int:
					$data[$key] = new IntTag($key, $value);
					break;
				case NBT::TAG_Long:
					$data[$key] = new LongTag($key, $value);
					break;
				case NBT::TAG_Float:
					$data[$key] = new FloatTag($key, $value);
					break;
				case NBT::TAG_Double:
					$data[$key] = new DoubleTag($key, $value);
					break;
				case NBT::TAG_ByteArray:
					$data[$key] = new ByteArrayTag($key, $value);
					break;
				case NBT::TAG_String:
					$data[$key] = new ByteTag($key, $value);
					break;
				case NBT::TAG_List:
					$data[$key] = new ListTag($key, $value);
					break;
				case NBT::TAG_Compound:
					$data[$key] = new CompoundTag($key, $value);
					break;
				case NBT::TAG_IntArray:
					$data[$key] = new IntArrayTag($key, $value);
					break;
			}

			$key++;
		}

		return $data;
	}

	private static function parseCompoundTag($str, &$offset = 0){
		$len = strlen($str);

		$data = [];

		for(; $offset < $len; ++$offset){
			if($str{$offset - 1} === "}"){
				break;
			}elseif($str{$offset} === "}"){
				++$offset;
				break;
			}

			$key = NBT::readKey($str, $offset);
			$value = NBT::readValue($str, $offset, $type);

			switch($type){
				case NBT::TAG_Byte:
					$data[$key] = new ByteTag($key, $value);
					break;
				case NBT::TAG_Short:
					$data[$key] = new ShortTag($key, $value);
					break;
				case NBT::TAG_Int:
					$data[$key] = new IntTag($key, $value);
					break;
				case NBT::TAG_Long:
					$data[$key] = new LongTag($key, $value);
					break;
				case NBT::TAG_Float:
					$data[$key] = new FloatTag($key, $value);
					break;
				case NBT::TAG_Double:
					$data[$key] = new DoubleTag($key, $value);
					break;
				case NBT::TAG_ByteArray:
					$data[$key] = new ByteArrayTag($key, $value);
					break;
				case NBT::TAG_String:
					$data[$key] = new StringTag($key, $value);
					break;
				case NBT::TAG_List:
					$data[$key] = new ListTag($key, $value);
					break;
				case NBT::TAG_Compound:
					$data[$key] = new CompoundTag($key, $value);
					break;
				case NBT::TAG_IntArray:
					$data[$key] = new IntArrayTag($key, $value);
					break;
			}
		}

		return $data;
	}

	private static function readValue($data, &$offset, &$type = null){
		$value = "";
		$type = null;
		$inQuotes = false;

		$len = strlen($data);
		for(; $offset < $len; ++$offset){
			$c = $data{$offset};

			if(!$inQuotes && ($c === " " || $c === "\r" || $c === "\n" || $c === "\t" || $c === "," || $c === "}" || $c === "]")){
				if($c === "," || $c === "}" || $c === "]"){
					break;
				}
			}elseif($c === '"'){
				$inQuotes = !$inQuotes;
				if($type === null){
					$type = NBT::TAG_String;
				}elseif($inQuotes){
					throw new \Exception("Sözdizimi Hatası: invalid quote at offset $offset");
				}
			}elseif($c === "\\"){
				$value .= isset($data{$offset + 1}) ? $data{$offset + 1} : "";
				++$offset;
			}elseif($c === "{" && !$inQuotes){
				if($value !== ""){
					throw new \Exception("Sözdizimi Hatası: invalid CompoundTag start at offset $offset");
				}
				++$offset;
				$value = NBT::parseCompoundTag($data, $offset);
				$type = NBT::TAG_CompoundTag;
				break;
			}elseif($c === "[" && !$inQuotes){
				if($value !== ""){
					throw new \Exception("Sözdizimi Hatası: invalid list start at offset $offset");
				}
				++$offset;
				$value = NBT::parseList($data, $offset);
				$type = NBT::TAG_ListTag;
				break;
			}else{
				$value .= $c;
			}
		}

		if($value === ""){
			throw new \Exception("Sözdizimi Hatası: invalid empty value at offset $offset");
		}

		if($type === null && strlen($value) > 0){
			$value = trim($value);
			$last = strtolower(substr($value, -1));
			$part = substr($value, 0, -1);

			if($last !== "b" && $last !== "s" && $last !== "l" && $last !== "f" && $last !== "d"){
				$part = $value;
				$last = null;
			}

			if($last !== "f" && $last !== "d" && ((string) ((int) $part)) === $part){
				if($last === "b"){
					$type = NBT::TAG_Byte;
				}elseif($last === "s"){
					$type = NBT::TAG_Short;
				}elseif($last === "l"){
					$type = NBT::TAG_Long;
				}else{
					$type = NBT::TAG_Int;
				}
				$value = (int) $part;
			}elseif(is_numeric($part)){
				if($last === "f" || $last === "d" || strpos($part, ".") !== false){
					if($last === "f"){
						$type = NBT::TAG_Float;
					}elseif($last === "d"){
						$type = NBT::TAG_Double;
					}else{
						$type = NBT::TAG_Float;
					}
					$value = (float) $part;
				}else{
					if($last === "l"){
						$type = NBT::TAG_Long;
					}else{
						$type = NBT::TAG_Int;
					}

					$value = $part;
				}
			}else{
				$type = NBT::TAG_String;
			}
		}

		return $value;
	}

	private static function readKey($data, &$offset){
		$key = "";

		$len = strlen($data);
		for(; $offset < $len; ++$offset){
			$c = $data{$offset};

			if($c === ":"){
				++$offset;
				break;
			}elseif($c !== " " && $c !== "\r" && $c !== "\n" && $c !== "\t"){
				$key .= $c;
			}
		}

		if($key === ""){
			throw new \Exception("Sözdizimi Hatası: invalid empty key at offset $offset");
		}

		return $key;
	}

	public function get($len){
		if($len < 0){
			$this->offset = strlen($this->buffer) - 1;
			return "";
		}elseif($len === true){
			return substr($this->buffer, $this->offset);
		}

		return $len === 1 ? $this->buffer{$this->offset++} : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	public function put($v){
		$this->buffer .= $v;
	}

	public function feof(){
		return !isset($this->buffer{$this->offset});
	}

	public function __construct($endianness = NBT::LITTLE_ENDIAN){
		$this->offset = 0;
		$this->endianness = $endianness & 0x01;
	}

	public function read($buffer, $doMultiple = false, $new = false){
		$this->offset = 0;
		$this->buffer = $buffer;
		$this->data = $this->readTag($new);
		if($doMultiple && $this->offset < strlen($this->buffer)){
			$this->data = [$this->data];
			do{
				$this->data[] = $this->readTag($new);
			}while($this->offset < strlen($this->buffer));
		}
		$this->buffer = "";
	}

	public function readCompressed($buffer, $compression = ZLIB_ENCODING_GZIP){
		$this->read(zlib_decode($buffer));
	}

	/**
	 * @return string|bool
	 */
	public function write($old = false){
		$this->offset = 0;
		$this->buffer = "";
		if($this->data instanceof CompoundTag){
			$this->writeTag($this->data, $old);
			return $this->buffer;
		}elseif(is_array($this->data)){
			foreach($this->data as $tag){
				$this->writeTag($tag, $old);
			}
			return $this->buffer;
		}
		return false;
	}

	public function writeCompressed($compression = ZLIB_ENCODING_GZIP, $level = 7){
		if(($write = $this->write(true)) !== false){
			return zlib_encode($write, $compression, $level);
		}
		return false;
	}
	
	private function checkGetString($new = false){
		if($new){
			$data = $this->getNewString();
		}else{
			$data = $this->getString();
		}
		return $data;
	}

	public function readTag($new = false){
		$tagType = $this->getByte();
		switch($tagType){
			case NBT::TAG_Byte:
				$tag = new ByteTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_Short:
				$tag = new ShortTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_Int:
				$tag = new IntTag($this->checkGetString($new));
				$tag->read($this, $new);
				break;
			case NBT::TAG_Long:
				$tag = new LongTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_Float:
				$tag = new FloatTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_Double:
				$tag = new DoubleTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_ByteArray:
				$tag = new ByteArrayTag($this->checkGetString($new));
				$tag->read($this);
				break;
			case NBT::TAG_String:
				$tag = new StringTag($this->checkGetString($new));
				$tag->read($this, $new);
				break;
			case NBT::TAG_List:
				$tag = new ListTag($this->checkGetString($new));
				$tag->read($this, $new);
				break;
			case NBT::TAG_Compound:
				$tag = new CompoundTag($this->checkGetString($new));
				$tag->read($this, $new);
				break;
			case NBT::TAG_IntArray:
				$tag = new IntArrayTag($this->checkGetString($new));
				$tag->read($this);
				break;

			case NBT::TAG_End:
				default;
				$tag = new EndTag;
				break;
		}
		return $tag;
	}

	public function writeTag(Tag $tag, $old = false){
		$this->buffer .= chr($tag->getType());
		if($tag instanceof NamedTag){
			if($old){
				$this->putOldString($tag->getName());
			}else{
				$this->putString($tag->getName());
			}
		}
		$tag->write($this, $old);
	}

	public function getByte(){
		return ord($this->get(1));
	}

	public function putByte($v){
		$this->buffer .= chr($v);
	}

	public function getShort(){
		return $this->endianness === NBT::BIG_ENDIAN ? unpack("n", $this->get(2))[1] : unpack("v", $this->get(2))[1];
	}

	public function putShort($v){
		$this->buffer .= $this->endianness === NBT::BIG_ENDIAN ? pack("n", $v) : pack("v", $v);
	}

	public function getInt(){
		return $this->endianness === NBT::BIG_ENDIAN ? Binary::readInt($this->get(4)) : Binary::readLInt($this->get(4));
	}
	
	public function getNewInt(){
		return $this->getSignedVarInt();
	}

	public function putOldInt($v){
		$this->buffer .= $this->endianness === NBT::BIG_ENDIAN ? pack("N", $v) : pack("V", $v);
	}
	
	public function putInt($v){
		$this->putSignedVarInt($v);
	}

	public function getLong(){
		return $this->endianness === NBT::BIG_ENDIAN ? Binary::readLong($this->get(8)) : Binary::readLLong($this->get(8));
	}

	public function putLong($v){
		$this->buffer .= $this->endianness === NBT::BIG_ENDIAN ? Binary::writeLong($v) : Binary::writeLLong($v);
	}

	public function getFloat(){
		return $this->endianness === NBT::BIG_ENDIAN ? (ENDIANNESS === 0 ? unpack("f", $this->get(4))[1] : unpack("f", strrev($this->get(4)))[1]) : (ENDIANNESS === 0 ? unpack("f", strrev($this->get(4)))[1] : unpack("f", $this->get(4))[1]);
	}

	public function putFloat($v){
		$this->buffer .= $this->endianness === NBT::BIG_ENDIAN ? (ENDIANNESS === 0 ? pack("f", $v) : strrev(pack("f", $v))) : (ENDIANNESS === 0 ? strrev(pack("f", $v)) : pack("f", $v));
	}

	public function getDouble(){
		return $this->endianness === NBT::BIG_ENDIAN ? (ENDIANNESS === 0 ? unpack("d", $this->get(8))[1] : unpack("d", strrev($this->get(8)))[1]) : (ENDIANNESS === 0 ? unpack("d", strrev($this->get(8)))[1] : unpack("d", $this->get(8))[1]);
	}

	public function putDouble($v){
		$this->buffer .= $this->endianness === NBT::BIG_ENDIAN ? (ENDIANNESS === 0 ? pack("d", $v) : strrev(pack("d", $v))) : (ENDIANNESS === 0 ? strrev(pack("d", $v)) : pack("d", $v));
	}

	public function getString(){
		return $this->get($this->endianness === 1 ? unpack("n", $this->get(2))[1] : unpack("v", $this->get(2))[1]);
	}
	
	public function getNewString(){
		$len = $this->getVarInt();
		return $this->get($len);
	}

	public function putOldString($v){
		$this->buffer .= $this->endianness === 1 ? pack("n", strlen($v)) : pack("v", strlen($v));
		$this->buffer .= $v;
	}
	
	public function putString($v){
		$this->putVarInt(strlen($v));
		$this->buffer .= $v;
	}
	
	public function getVarInt(){
		$result = $shift = 0;
		do {
			$byte = $this->getByte();
			$result |= ($byte & 0x7f) << $shift;
			$shift += 7;
		} while ($byte > 0x7f);
		return $result;
	}
	
	public function getSignedVarInt(){
		$result = $this->getVarInt();
		if($result % 2 == 0){
			$result = $result / 2;
		}else{
			$result = (-1) * ($result + 1) / 2;
		}
		return $result;
	}
	
	public function putSignedVarInt($v){
		$this->buffer .= Binary::writeSignedVarInt($v);
	}

	public function putVarInt($v){
		$this->buffer .= Binary::writeVarInt($v);
	}

	public function getArray(){
		$data = [];
		NBT::toArray($data, $this->data);
	}

	private static function toArray(array &$data, Tag $tag){
		/** @var CompoundTag[]|ListTag[]|IntArrayTag[] $tag */
		foreach($tag as $key => $value){
			if($value instanceof CompoundTag || $value instanceof ListTag || $value instanceof IntArrayTag){
				$data[$key] = [];
				NBT::toArray($data[$key], $value);
			}else{
				$data[$key] = $value->getValue();
			}
		}
	}

	public static function fromArrayGuesser($key, $value){
		if(is_int($value)){
			return new IntTag($key, $value);
		}elseif(is_float($value)){
			return new FloatTag($key, $value);
		}elseif(is_string($value)){
			return new StringTag($key, $value);
		}elseif(is_bool($value)){
			return new ByteTag($key, $value ? 1 : 0);
		}

		return null;
	}

	private static function fromArray(Tag $tag, array $data, callable $guesser){
		foreach($data as $key => $value){
			if(is_array($value)){
				$isNumeric = true;
				$isIntArray = true;
				foreach($value as $k => $v){
					if(!is_numeric($k)){
						$isNumeric = false;
						break;
					}elseif(!is_int($v)){
						$isIntArray = false;
					}
				}
				$tag{$key} = $isNumeric ? ($isIntArray ? new IntArrayTag($key, []) : new ListTag($key, [])) : new CompoundTag($key, []);
				NBT::fromArray($tag->{$key}, $value, $guesser);
			}else{
				$v = call_user_func($guesser, $key, $value);
				if($v instanceof Tag){
					$tag{$key} = $v;
				}
			}
		}
	}

	public function setArray(array $data, callable $guesser = null){
		$this->data = new CompoundTag("", []);
		NBT::fromArray($this->data, $data, $guesser === null ? [NBT::class, "fromArrayGuesser"] : $guesser);
	}

	/**
	 * @return CompoundTag|array
	 */
	public function getData(){
		return $this->data;
	}

	/**
	 * @param CompoundTag|array $data
	 */
	public function setData($data){
		$this->data = $data;
	}

}
