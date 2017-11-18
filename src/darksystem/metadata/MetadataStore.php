<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem\metadata;

use pocketmine\plugin\Plugin;
use pocketmine\utils\PluginException;

abstract class MetadataStore{
	
	private $metadataMap = [];

	/**
	 * @param mixed         $subject
	 * @param string        $metadataKey
	 * @param MetadataValue $newMetadataValue
	 *
	 * @throws \Exception
	 */
	public function setMetadata($subject, $metadataKey, MetadataValue $newMetadataValue){
		$owningPlugin = $newMetadataValue->getOwningPlugin();
		if($owningPlugin === null){
			throw new PluginException("Plugin cannot be null");
		}
		$key = $this->disambiguate($subject, $metadataKey);
		if(!isset($this->metadataMap[$key])){
			$entry = new \WeakMap();
			$this->metadataMap[$key] = $entry;
		}else{
			$entry = $this->metadataMap[$key];
		}
		$entry[$owningPlugin] = $newMetadataValue;
	}

	/**
	 * @param mixed  $subject
	 * @param string $metadataKey
	 *
	 * @return MetadataValue[]
	 *
	 * @throws \Exception
	 */
	public function getMetadata($subject, $metadataKey){
		$key = $this->disambiguate($subject, $metadataKey);
		if(isset($this->metadataMap[$key])){
			return $this->metadataMap[$key];
		}else{
			return [];
		}
	}

	/**
	 * @param mixed  $subject
	 * @param string $metadataKey
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 */
	public function hasMetadata($subject, $metadataKey){
		return isset($this->metadataMap[$this->disambiguate($subject, $metadataKey)]);
	}

	/**
	 * @param mixed  $subject
	 * @param string $metadataKey
	 * @param Plugin $owningPlugin
	 *
	 * @throws \Exception
	 */
	public function removeMetadata($subject, $metadataKey, Plugin $owningPlugin){
		$key = $this->disambiguate($subject, $metadataKey);
		if(isset($this->metadataMap[$key])){
			unset($this->metadataMap[$key][$owningPlugin]);
			if($this->metadataMap[$key]->count() === 0){
				unset($this->metadataMap[$key]);
			}
		}
	}

	/**
	 * @param Plugin $owningPlugin
	 */
	public function invalidateAll(Plugin $owningPlugin){
		foreach($this->metadataMap as $values){
			if(isset($values[$owningPlugin])){
				$values[$owningPlugin]->invalidate();
			}
		}
	}

	/**
	 * @param Metadatable $subject
	 * @param string      $metadataKey
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public abstract function disambiguate(Metadatable $subject, $metadataKey);
	
}