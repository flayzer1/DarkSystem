<?php

namespace darksystem\multicore;

use pocketmine\Server;
use pocketmine\utils\Utils;
use darksystem\multicore\callback\CallbackManager;

class CoreStarter{
	
	/** @var CallbackManager */
	private $callback;
	
	public function __construct(Server $server){
		$this->server = $server;
		$this->callback = new CallbackManager($this->server, $this);
		$this->write();
	}
	
	public function getCallback(){
		return $this->callback;
	}
	
	private function write(){
		$multiCore = new MultiCore($this->server, Utils::getCoreCount());
		//$this->setPrivateVariableData($this->server->getScheduler(), "asyncPool", $multiCore);
		foreach($this->server->getLevels() as $l){
			$l->registerGenerator();
		}
	}
	
	private function prove(){
		$prove = new Prove();
		
		$prove->useMultiCore1();
		//$prove->useMultiCore2();
		//$prove->useSingleCore();
	}
	
	private function setPrivateVariableData($object, $variableName, $set){
		$property = (new \ReflectionClass($object))->getProperty($variableName);
		$property->setAccessible(true);
		$property->setValue($object, $set);
	}
	
}
