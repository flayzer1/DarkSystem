<?php

namespace darksystem\multicore;

use darksystem\Worker;

class CoreWorker extends Worker{
	
	private $logger;
	
	public function __construct(\ThreadedLogger $logger){
		$this->logger = $logger;
	}
	
	public function __destruct(){
		
	}
	
	public function run(){
		$this->registerClassLoader();
		gc_enable();
		ini_set("memory_limit", - 1);
		
		global $store;
		$store = [];
	}
	
	public function handleException(\Throwable $e){
		$this->logger->logException($e);
	}
	
	public function getThreadName(){
		return "Asynchronous Worker";
	}
	
}
