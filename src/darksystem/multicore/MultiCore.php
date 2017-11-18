<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace darksystem\multicore;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use darksystem\multicore\task\InitialTask;

class MultiCore{
	
	/** @var Server */
	private $server;
	
	/** @var \Pool */
	private $pool;
	
	/** @var AsyncTask[] */
	private $tasks = [];
	
	protected $size;
	
	public function __construct(Server $server, $size){
		$this->server = $server;
		$this->size = (int) $size;
		$this->pool = new \Pool($size, CoreWorker::class, [
			$this->server->getLogger()
		]);
		
		for($i = 0; $i < $size; $i ++){
			$this->pool->submit(new InitialTask());
		}
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function increaseSize($newSize){
		$this->size = $newSize;
		$this->pool->resize($newSize);
	}
	
	public function submitTaskToWorker(AsyncTask $task, $worker){
		if($task->isGarbage()){
			return;
		}
		
		$worker = (int) $worker;
		if($worker < 0 || $worker >= $this->size){
			throw new \InvalidArgumentException("Invalid worker $worker");
		}
		
		$this->tasks[$task->getTaskId()] = $task;
		$this->pool->submitTo((int) $worker, $task);
	}
	
	public function submitTask(AsyncTask $task){
		if($task->isGarbage()){
			return;
		}
		
		$this->tasks[$task->getTaskId()] = $task;
		$this->pool->submit($task);
	}
	
	private function removeTask(AsyncTask $task, $force = false){
		unset($this->tasks[$task->getTaskId()]);
	}
	
	public function removeTasks(){
		$this->pool->shutdown();
	}
	
	public function collectTasks(){
		for($i = 0; $i < 2; $i ++){
			if(!$this->pool->collect(function (AsyncTask $task){
				if($task->isGarbage() && !$task->isRunning() && !$task->isCrashed()){
					if(!$task->hasCancelledRun()){
						$task->onCompletion($this->server);
					}
					
					$this->removeTask($task);
				}elseif($task->isTerminated() || $task->isCrashed()){
					$this->server->getLogger()->critical("Could not execute asynchronous task " . (new \ReflectionClass($task))->getShortName() . ": Task crashed");
					$this->removeTask($task);
				}
				
				return $task->isGarbage();
			} ))
				break;
		}
	}
}
