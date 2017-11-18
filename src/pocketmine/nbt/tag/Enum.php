<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\nbt\tag;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Enum as TagEnum;
use pocketmine\utils\Binary;

class Enum extends NamedTag implements \ArrayAccess, \Countable{

	private $tagType;

	public function __construct($name = "", $value = []){
		$this->name = $name;
		foreach($value as $k => $v){
			$this->{$k} = $v;
		}
	}

	public function &getValue(){
		$value = [];
		foreach($this as $k => $v){
			if($v instanceof Tag){
				$value[$k] = $v;
			}
		}

		return $value;
	}

	public function getCount(){
		$count = 0;
		foreach($this as $tag){
			if($tag instanceof Tag){
				++$count;
			}
		}

		return $count;
	}

	public function offsetExists($offset){
		return isset($this->{$offset});
	}

	public function offsetGet($offset){
		if(isset($this->{$offset}) and $this->{$offset} instanceof Tag){
			if($this->{$offset} instanceof \ArrayAccess){
				return $this->{$offset};
			}else{
				return $this->{$offset}->getValue();
			}
		}

		return null;
	}

	public function offsetSet($offset, $value){
		if($value instanceof Tag){
			$this->{$offset} = $value;
		}elseif($this->{$offset} instanceof Tag){
			$this->{$offset}->setValue($value);
		}
	}

	public function offsetUnset($offset){
		unset($this->{$offset});
	}

	public function count($mode = COUNT_NORMAL){
		for($i = 0; true; $i++){
			if(!isset($this->{$i})){
				return $i;
			}
			
			if($mode === COUNT_RECURSIVE){
				if($this->{$i} instanceof \Countable){
					$i += count($this->{$i});
				}
			}
		}

		return $i;
	}

	public function getType(){
		return NBT::TAG_List;
	}

	public function setTagType($type){
		$this->tagType = $type;
	}

	public function getTagType(){
		return $this->tagType;
	}

	public function read(NBT $nbt, $new = false){
		$this->value = [];
		$this->tagType = ord($nbt->get(1));
		$size = $new ? $nbt->getVarInt() : $nbt->getInt();
		for($i = 0; $i < $size and !$nbt->feof(); ++$i){
			switch($this->tagType){
				case NBT::TAG_Byte:
					$tag = new ByteTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Short:
					$tag = new ShortTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Int:
					$tag = new IntTag("");
					$tag->read($nbt, $new);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Long:
					$tag = new LongTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Float:
					$tag = new FloatTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Double:
					$tag = new DoubleTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_ByteArray:
					$tag = new ByteArrayTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_String:
					$tag = new StringTag("");
					$tag->read($nbt, $new);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_List:
					$tag = new TagEnum("");
					$tag->read($nbt, $new);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_Compound:
					$tag = new CompoundTag("");
					$tag->read($nbt, $new);
					$this->{$i} = $tag;
					break;
				case NBT::TAG_IntArray:
					$tag = new IntArrayTag("");
					$tag->read($nbt);
					$this->{$i} = $tag;
					break;
			}
		}
	}

	public function write(NBT $nbt, $old = false){
		if(!isset($this->tagType)){
			$id = null;
			foreach($this as $tag){
				if($tag instanceof Tag){
					if(!isset($id)){
						$id = $tag->getType();
					}elseif($id !== $tag->getType()){
						return false;
					}
				}
			}
			
			$this->tagType = $id;
		}
		
		$nbt->buffer .= chr($this->tagType);
		
		$tags = [];
		foreach($this as $tag){
			if($tag instanceof Tag){
				$tags[] = $tag;
			}
		}
		
		if($old){
			$nbt->buffer .= $nbt->endianness === 1 ? pack("N", count($tags)) : pack("V", count($tags));
		}else{
			$nbt->putVarInt(count($tags));
		}
		
		foreach($tags as $tag){
			$tag->write($nbt, $old);
		}
	}

	public function __toString(){
		$str = get_class($this) . "{\n";
		foreach($this as $tag){
			if($tag instanceof Tag){
				$str .= get_class($tag) . ":" . $tag->__toString() . "\n";
			}
		}
		return $str . "}";
	}
}
