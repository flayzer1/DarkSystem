<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\utils;

use LogLevel;
use pocketmine\Thread;
use pocketmine\Worker;
use pocketmine\Translate;

class MainLogger extends \AttachableThreadedLogger{
	
	protected $logFile;
	protected $logStream;
	protected $shutdown;
	protected $logDebug;
	
	public static $logger = null;
	
	public $shouldSendMsg = "";
	public $shouldRecordMsg = false;
	
	private $logResource;
	private $lastGet = 0;
	
	public function setSendMsg($b){
		$this->shouldRecordMsg = $b;
		$this->lastGet = time();
	}

	public function getMessages(){
		$msg = $this->shouldSendMsg;
		$this->shouldSendMsg = "";
		$this->lastGet = time();
		return $msg;
	}
	
	public function __construct($logFile, $logDebug = false){
		if(static::$logger instanceof MainLogger){
			throw new \RuntimeException("Sunucu Konsolu Zaten Oluşturulmuş!");
		}
		static::$logger = $this;
		$this->logStream = new \Threaded;
		$this->start();
	}
	
	public static function getLogger(){
		return static::$logger;
	}

	public function emergency($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::EMERGENCY, "ACIL", TextFormat::RED);
		}else{
			$this->send($message, \LogLevel::EMERGENCY, "EMERGENCY", TextFormat::RED);
		}
	}

	public function alert($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::ALERT, "IKAZ", TextFormat::RED);
		}else{
			$this->send($message, \LogLevel::ALERT, "ALERT", TextFormat::RED);
		}
	}

	public function critical($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::CRITICAL, "KRITIK", TextFormat::RED);
		}else{
			$this->send($message, \LogLevel::CRITICAL, "CRITICAL", TextFormat::RED);
		}
	}

	public function error($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::ERROR, "HATA", TextFormat::RED);
		}else{
			$this->send($message, \LogLevel::ERROR, "ERROR", TextFormat::RED);
		}
	}

	public function warning($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::WARNING, "UYARI", TextFormat::GOLD);
		}else{
			$this->send($message, \LogLevel::WARNING, "WARNING", TextFormat::GOLD);
		}
	}

	public function notice($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::NOTICE, "BILDIRIM", TextFormat::GRAY);
		}else{
			$this->send($message, \LogLevel::NOTICE, "NOTICE", TextFormat::GRAY);
		}
	}

	public function info($message){
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::INFO, "BILGI", TextFormat::YELLOW);
		}else{
			$this->send($message, \LogLevel::INFO, "INFO", TextFormat::YELLOW);
		}
	}

	public function debug($message, $name = "ONARIM"){
		if($this->logDebug === false){
			return false;
		}
		if(Translate::checkTurkish() === "yes"){
			$this->send($message, \LogLevel::DEBUG, $name, TextFormat::GRAY);
		}else{
			$this->send($message, \LogLevel::DEBUG, "DEBUG", TextFormat::GRAY);
		}
	}
	
	public function setLogDebug($logDebug){
		$this->logDebug = (bool) $logDebug;
	}

	public function logException(\Throwable $e, $trace = null){
		if($trace === null){
			$trace = $e->getTrace();
		}
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();
		if(Translate::checkTurkish() === "yes"){
		$errorConversion = [
			0 => "EXCEPTION",
			E_ERROR => "E_HATA",
			E_WARNING => "E_UYARI",
			E_PARSE => "E_OKUMA",
			E_NOTICE => "E_BILDIRIM",
			E_CORE_ERROR => "E_CORE_HATASI",
			E_CORE_WARNING => "E_CORE_UYARISI",
			E_COMPILE_ERROR => "E_COMPILE_HATASI",
			E_COMPILE_WARNING => "E_COMPILE_UYARISI",
			E_USER_ERROR => "E_KULLANICI_HATASI",
			E_USER_WARNING => "E_KULLANICI_UYARISI",
			E_USER_NOTICE => "E_KULLANCI_BILDIRIMI",
			E_STRICT => "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_HATA",
			E_DEPRECATED => "E_DEPRECATED",
			E_USER_DEPRECATED => "E_KULLANICI_DEPRECATED",
		];
		}else{
		$errorConversion = [
			0 => "EXCEPTION",
			E_ERROR => "E_ERROR",
			E_WARNING => "E_WARNING",
			E_PARSE => "E_PARSE",
			E_NOTICE => "E_NOTICE",
			E_CORE_ERROR => "E_CORE_ERROR",
			E_CORE_WARNING => "E_CORE_WARNING",
			E_COMPILE_ERROR => "E_COMPILE_ERROR",
			E_COMPILE_WARNING => "E_COMPILE_WARNING",
			E_USER_ERROR => "E_USER_ERROR",
			E_USER_WARNING => "E_USER_WARNING",
			E_USER_NOTICE => "E_USER_NOTICE",
			E_STRICT => "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
			E_DEPRECATED => "E_DEPRECATED",
			E_USER_DEPRECATED => "E_USER_DEPRECATED",
		];
		}
		if($errno === 0){
			$type = LogLevel::CRITICAL;
		}else{
			$type = ($errno === E_ERROR || $errno === E_USER_ERROR) ? LogLevel::ERROR : (($errno === E_USER_WARNING || $errno === E_WARNING) ? LogLevel::WARNING : LogLevel::NOTICE);
		}
		$errno = isset($errorConversion[$errno]) ? $errorConversion[$errno] : $errno;
		if(($pos = strpos($errstr, "\n")) !== false){
			$errstr = substr($errstr, 0, $pos);
		}
		$errfile = \pocketmine\cleanPath($errfile);
		$this->log($type, get_class($e) . ": \"$errstr\" ($errno) in \"$errfile\" at line $errline");
		foreach(@\pocketmine\getTrace(1, $trace) as $i => $line){
			$this->debug($line);
		}
	}

	public function log($level, $message){
		switch($level){
			case LogLevel::EMERGENCY:
				$this->emergency($message);
				break;
			case LogLevel::ALERT:
				$this->alert($message);
				break;
			case LogLevel::CRITICAL:
				$this->critical($message);
				break;
			case LogLevel::ERROR:
				$this->error($message);
				break;
			case LogLevel::WARNING:
				$this->warning($message);
				break;
			case LogLevel::NOTICE:
				$this->notice($message);
				break;
			case LogLevel::INFO:
				$this->info($message);
				break;
			case LogLevel::DEBUG:
				$this->debug($message);
				break;
		}
	}

	public function shutdown(){
		$this->shutdown = true;
	}

	protected function send($message, $level, $prefix, $color){
		$now = time();
		$thread = \Thread::getCurrentThread();
		if($thread === null){
			$threadName = "Sunucu Görevi";
		}elseif($thread instanceof Thread || $thread instanceof Worker){
			$threadName = $thread->getThreadName() . " Görevi";
		}else{
			$threadName = (new \ReflectionClass($thread))->getShortName() . " Görevi";
		}
		if($this->shouldRecordMsg){
			if((time() - $this->lastGet) >= 10) $this->shouldRecordMsg = false;
			else{
				if(strlen($this->shouldSendMsg) >= 10000) $this->shouldSendMsg = "";
				$this->shouldSendMsg .= $color . "|" . $prefix . "|" . trim($message, "\r\n") . "\n";
			}
		}
		$message = TextFormat::toANSI(TextFormat::AQUA . "<" . date("H:i:s", $now) . "> " . TextFormat::BLUE . "DarkSystem §l§6》§r§9 " . $color . $prefix . ":" . " " . $message . TextFormat::RESET);
		$cleanMessage = TextFormat::clean($message);
		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}
		if($this->attachment instanceof \ThreadedLoggerAttachment){
			$this->attachment->call($level, $message);
		}
		$this->logStream[] = date("Y-m-d", $now) . " " . $cleanMessage . "\n";
		if($this->logStream->count() === 1){
			$this->synchronized(function(){
				$this->notify();
			});
		}
	}
	
	public function directSend($message){
		$message = TextFormat::toANSI($message);
		$cleanMessage = TextFormat::clean($message);
		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}
	}
	
	public static function clear(){
		echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
	}
	
	public function run(){
		$this->shutdown = false;
	}
}
