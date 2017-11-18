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

class FileWriteTask extends AsyncTask{

	private $path;
	private $contents;
	private $flags;

	public function __construct($path, $contents, $flags = 0){
		$this->path = $path;
		$this->contents = $contents;
		$this->flags = (int) $flags;
	}

	public function onRun(){
		try{
			file_put_contents($this->path, $this->contents, (int) $this->flags);
		}catch(\Throwable $e){
			
		}
	}
}
