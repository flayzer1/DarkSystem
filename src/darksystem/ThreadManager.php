<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem;

class ThreadManager extends \Volatile{
	
	private static $instance = null;

	public static function init(){
		ThreadManager::$instance = new ThreadManager();
	}
	
	public static function getInstance(){
		return ThreadManager::$instance;
	}
	
	public function add($thread){
		if($thread instanceof Thread || $thread instanceof Worker){
			$this->{spl_object_hash($thread)} = $thread;
		}
	}
	
	public function remove($thread){
		if($thread instanceof Thread || $thread instanceof Worker){
			unset($this->{spl_object_hash($thread)});
		}
	}
	
	public function getAll(){
		$array = [];
		foreach($this as $key => $thread){
			$array[$key] = $thread;
		}

		return $array;
	}
	
}
