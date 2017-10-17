<?php

namespace pocketmine\multicore;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Utils;
use pocketmine\multicore\callback\CallbackManager;

class Main extends PluginBase{
	
	/** @var CallbackManager */
	private $callback;
	
	public function onLoad() {
		$this->write ();
		// $this->prove();
	}
	
	public function onEnable() {
		$this->callback = new CallbackManager ( $this->getServer (), $this );
	}
	
	public function getCallback() {
		return $this->callback;
	}
	
	private function write() {
		$multicore = new MultiCore ( $this->getServer (), Utils::getCoreCount () );
		$this->setPrivateVariableData ( $this->getServer ()->getScheduler (), "asyncPool", $multicore );
		foreach ( $this->getServer ()->getLevels () as $level )
			$level->registerGenerator ();
	}
	
	private function prove() {
		$prove = new Prove ();
		
		/* It Works */
		$prove->useMultiCore1 ();
		
		/* Not Works */
		// $prove->useMultiCore2();
		// $prove->useSingleCore();
	}
	
	private function setPrivateVariableData($object, $variableName, $set) {
		$property = (new \ReflectionClass ( $object ))->getProperty ( $variableName );
		$property->setAccessible ( true );
		$property->setValue ( $object, $set );
	}
	
}
