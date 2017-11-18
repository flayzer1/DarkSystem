<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\scheduler;

use pocketmine\Server;

class AsyncPool{

	/** @var Server */
	private $server;

	protected $size;

	/** @var AsyncTask[] */
	private $tasks = [];
	/** @var int[] */
	private $taskWorkers = [];

	/** @var AsyncWorker[] */
	private $workers = [];
	/** @var int[] */
	private $workerUsage = [];

	public function __construct(Server $server, $size){
		$this->server = $server;
		$this->size = (int) $size;

		for($i = 0; $i < $this->size; ++$i){
			$this->workerUsage[$i] = 0;
			$this->workers[$i] = new AsyncWorker();
			$this->workers[$i]->setClassLoader($this->server->getLoader());
			$this->workers[$i]->start();
		}
	}

	public function submitTask(AsyncTask $task){
		if(isset($this->tasks[$task->getTaskId()]) or $task->isFinished()){
			return;
		}

		$this->tasks[$task->getTaskId()] = $task;

		$selectedWorker = mt_rand(0, $this->size - 1);
		$selectedTasks = $this->workerUsage[$selectedWorker];
		for($i = 0; $i < $this->size; ++$i){
			if($this->workerUsage[$i] < $selectedTasks){
				$selectedWorker = $i;
				$selectedTasks = $this->workerUsage[$i];
			}
		}
		
		$this->workers[$selectedWorker]->stack($task);
		$this->workerUsage[$selectedWorker]++;
		$this->taskWorkers[$task->getTaskId()] = $selectedWorker;
	}

	private function removeTask(AsyncTask $task){
		if(!$task->isTerminated() and ($task->isRunning() or !$task->isFinished())){
			return;
		}

		if(isset($this->taskWorkers[$task->getTaskId()])){
			$this->workerUsage[$this->taskWorkers[$task->getTaskId()]]--;
		}

		unset($this->tasks[$task->getTaskId()]);
		unset($this->taskWorkers[$task->getTaskId()]);	
		$task->cleanObject();
	}

	public function removeTasks(){
		foreach($this->tasks as $task){
			$this->removeTask($task);
		}

		for($i = 0; $i < $this->size; ++$i){
			$this->workerUsage[$i] = 0;
		}

		$this->taskWorkers = [];
		$this->tasks = [];
	}

	public function collectTasks(){
		foreach($this->workers as $worker){
			$worker->collect();
		}
	}
	
	public function getSize(){
		return $this->size;
	}
	
	public function submitTaskToWorker(AsyncTask $task, $worker){
		if(isset($this->tasks[$task->getTaskId()]) or $task->isFinished()){
			return;
		}

		$worker = (int) $worker;
		if($worker < 0 or $worker >= $this->size){
			throw new \InvalidArgumentException("Invalid worker $worker");
		}

		$this->tasks[$task->getTaskId()] = $task;

		$this->workers[$worker]->stack($task);
		$this->workerUsage[$worker]++;
		$this->taskWorkers[$task->getTaskId()] = $worker;
	}
}
