<?php

namespace pocketmine\multi-core\callback;

use pocketmine\Server;
use pocketmine\plugin\Plugin;
use pocketmine\multi-core\Main;

class CallbackManager {
	/** @var Server */
	private $server;
	/** @var Main */
	private $plugin;
	public $authenticate;
	public function __construct(Server $server, Plugin $plugin) {
		$this->server = $server;
		$this->plugin = $plugin;
		
		$this->init ();
	}
	public function init() {
		$this->register ( $this->authenticate = new AuthenticateCallback () );
	}
	public function register($listener) {
		$this->server->getPluginManager ()->registerEvents ( $listener, $this->plugin );
	}
}
