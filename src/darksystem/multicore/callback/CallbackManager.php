<?php

namespace darksystem\multicore\callback;

use pocketmine\Server;

class CallbackManager{
	
	/** @var Server */
	private $server;
	
	public $authenticate;
	
	public function __construct(Server $server){
		$this->server = $server;
		
		$this->init();
	}
	
	public function init(){
		//Other tasks here
		//$this->register($this->authenticate = new AuthenticateCallback());
	}
	
	public function register($listener){
		//Not works
		//$this->server->getPluginManager()->registerEvents($listener);
	}
	
}
