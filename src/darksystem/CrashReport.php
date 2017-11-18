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

use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\VersionString;
use pocketmine\utils\Utils;
use pocketmine\Translate;
use pocketmine\Server;
use raklib\RakLib;

class CrashReport{
	
	private $server;
	private $fp;
	private $time;
	private $data = [];
	private $encodedData = null;
	private $path;

	public function __construct(Server $server){
		$this->time = time();
		$this->server = $server;
		
		if(Translate::checkTurkish() === "yes"){
		    if(!is_dir($this->server->getDataPath() . "cokme-arsivleri")){
			    mkdir($this->server->getDataPath() . "cokme-arsivleri");
			}
		}else{
			if(!is_dir($this->server->getDataPath() . "crashdumps")){
			    mkdir($this->server->getDataPath() . "crashdumps");
			}
		}
		
		if(Translate::checkTurkish() === "yes"){
		    $this->path = $this->server->getCrashPath() . "CokmeArsivi_" . date("D_M_j-H.i.s-T_Y", $this->time) . ".log";
		}else{
			$this->path = $this->server->getCrashPath() . "CrashDump_" . date("D_M_j-H.i.s-T_Y", $this->time) . ".log";
		}
		
		$this->fp = @fopen($this->path, "wb");
		if(!is_resource($this->fp)){
			if(Translate::checkTurkish() === "yes"){
				throw new \RuntimeException("Çökme Arşivi Oluşturulamadı!");
			}else{
				throw new \RuntimeException("Could not Create Crash Report!");
			}
		}
		
		$this->data["time"] = $this->time;
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine($this->server->getName() . " Çökme Arşivi " . date("D M j H:i:s T Y", $this->time));
		}else{
			$this->addLine($this->server->getName() . " Crash Report " . date("D M j H:i:s T Y", $this->time));
		}
		
		$this->addLine();
		try{
			$this->baseCrash();
		}catch(\Exception $e){
			if(Translate::checkTurkish() === "yes"){
				$this->addLine("Çökme Arşivi Oluşturulurken Bir Hata Oluştu ve İşlem Durduruldu!");
			}else{
				$this->addLine("Crash Report Crashed While Generating Base Crash Data!");
			}
			
			$this->addLine();
		}
		
		$this->generalData();
		$this->pluginsData();
		$this->extraData();
		$this->encodeData();
	}

	public function getPath(){
		return $this->path;
	}

	public function getEncodedData(){
		return $this->encodedData;
	}

	public function getData(){
		return $this->data;
	}

	private function encodeData(){
		$this->addLine();
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine("----------------------RAPOR-----------------------");
			$this->addLine();
			$this->addLine("===ÇÖKME ARŞİVİ===");
		}else{
			$this->addLine("----------------------REPORT THE DATA BELOW THIS LINE-----------------------");
			$this->addLine();
			$this->addLine("===BEGIN CRASH REPORT===");
		}
		
		$this->encodedData = zlib_encode(json_encode($this->data, JSON_UNESCAPED_SLASHES), ZLIB_ENCODING_DEFLATE, 9);
		
		foreach(str_split(base64_encode($this->encodedData), 76) as $line){
			$this->addLine($line);
		}
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine("===SON===");
		}else{
			$this->addLine("===END CRASH REPORT===");
		}
	}

	private function pluginsData(){
		if($this->server->getPluginManager() instanceof PluginManager){
			$this->addLine();
			
			if(Translate::checkTurkish() === "yes"){
				$this->addLine("Yüklenmiş Eklentiler:");
			}else{
				$this->addLine("Loaded Plugins:");
			}
			
			$this->data["plugins"] = [];
			foreach($this->server->getPluginManager()->getPlugins() as $p){
				$d = $p->getDescription();
				$this->data["plugins"][$d->getName()] = [
					"name" => $d->getName(),
					"version" => $d->getVersion(),
					"authors" => $d->getAuthors(),
					"api" => $d->getCompatibleApis(),
					"enabled" => $p->isEnabled(),
					"depends" => $d->getDepend(),
					"softDepends" => $d->getSoftDepend(),
					"main" => $d->getMain(),
					"load" => $d->getOrder() === PluginLoadOrder::POSTWORLD ? "POSTWORLD" : "STARTUP",
					"website" => $d->getWebsite()
				];
				
				if(Translate::checkTurkish() === "yes"){
					$this->addLine($d->getName() . " " . $d->getVersion() . " Yapan " . implode(", ", $d->getAuthors()) . " API(ler) " . implode(", ", $d->getCompatibleApis()));
				}else{
					$this->addLine($d->getName() . " " . $d->getVersion() . " by " . implode(", ", $d->getAuthors()) . " API(s) " . implode(", ", $d->getCompatibleApis()));
				}
			}
		}
	}

	private function extraData(){
		global $arguments;
		
		if(Translate::checkTurkish() === "yes"){
		if($this->server->getProperty("auto-report.send-settings", true) !== false){
			$this->data["parameters"] = (array) $arguments;
			$this->data["sunucu.properties"] = @file_get_contents($this->server->getDataPath() . "sunucu.properties");
			$this->data["sunucu.properties"] = preg_replace("#^rcon\\.password=(.*)$#m", "rcon.password=******", $this->data["sunucu.properties"]);
			$this->data["pocketmine.yml"] = @file_get_contents($this->server->getDataPath() . "pocketmine.yml");
		}else{
			$this->data["pocketmine.yml"] = "";
			$this->data["sunucu.properties"] = "";
			$this->data["parameters"] = [];
		}
		}else{
		if($this->server->getProperty("auto-report.send-settings", true) !== false){
			$this->data["parameters"] = (array) $arguments;
			$this->data["server.properties"] = @file_get_contents($this->server->getDataPath() . "server.properties");
			$this->data["server.properties"] = preg_replace("#^rcon\\.password=(.*)$#m", "rcon.password=******", $this->data["server.properties"]);
			$this->data["pocketmine.yml"] = @file_get_contents($this->server->getDataPath() . "pocketmine.yml");
		}else{
			$this->data["pocketmine.yml"] = "";
			$this->data["server.properties"] = "";
			$this->data["parameters"] = [];
		}
		}
		
		$extensions = [];
		foreach(get_loaded_extensions() as $ext){
			$extensions[$ext] = phpversion($ext);
		}
		
		$this->data["extensions"] = $extensions;
		
		if($this->server->getProperty("auto-report.send-phpinfo", false) !== false){
			ob_start();
			phpinfo();
			$this->data["phpinfo"] = ob_get_contents();
			ob_end_clean();
		}
	}

	private function baseCrash(){
		global $lastExceptionError, $lastError;
		
		if(isset($lastExceptionError)){
			$error = $lastExceptionError;
		}else{
			$error = (array) error_get_last();
			$error["trace"] = @getTrace(3);
			
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
			
			$error["fullFile"] = $error["file"];
			$error["file"] = cleanPath($error["file"]);
			$error["type"] = $errorConversion[$error["type"]] ?? $error["type"];
			if(($pos = strpos($error["message"], "\n")) !== false){
				$error["message"] = substr($error["message"], 0, $pos);
			}
		}

		if(isset($lastError)){
			$this->data["lastError"] = $lastError;
		}

		$this->data["error"] = $error;
		
		unset($this->data["error"]["fullFile"]);
		unset($this->data["error"]["trace"]);
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine("Hata: " . $error["message"]);
			$this->addLine("Dosya: " . $error["file"]);
			$this->addLine("Satır: " . $error["line"]);
			$this->addLine("Tür: " . $error["type"]);
		}else{
			$this->addLine("Error: " . $error["message"]);
			$this->addLine("File: " . $error["file"]);
			$this->addLine("Line: " . $error["line"]);
			$this->addLine("Type: " . $error["type"]);
		}
		
		if(strpos($error["file"], "src/pocketmine/") === false and strpos($error["file"], "src/raklib/") === false and file_exists($error["fullFile"])){
			$this->addLine();
			
			if(Translate::checkTurkish() === "yes"){
				$this->addLine("SUNUCU BİR EKLENTİ YÜZÜNDEN ÇÖKTÜ");
			}else{
				$this->addLine("THIS CRASH WAS CAUSED BY A PLUGIN");
			}
			
			$this->data["plugin"] = true;

			$reflection = new \ReflectionClass(PluginBase::class);
			$file = $reflection->getProperty("file");
			$file->setAccessible(true);
			foreach($this->server->getPluginManager()->getPlugins() as $plugin){
				$filePath = \pocketmine\cleanPath($file->getValue($plugin));
				if(strpos($error["file"], $filePath) === 0){
					$this->data["plugin"] = $plugin->getName();
					if(Translate::checkTurkish() === "yes"){
						$this->addLine("KÖTÜ EKLENTİ: " . $plugin->getDescription()->getFullName());
					}else{
						$this->addLine("BAD PLUGIN: " . $plugin->getDescription()->getFullName());
					}
					break;
				}
			}
		}else{
			$this->data["plugin"] = false;
		}

		$this->addLine();
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine("Kod:");
		}else{
			$this->addLine("Code:");
		}
		
		$this->data["code"] = [];

		if($this->server->getProperty("auto-report.send-code", true) !== false){
			$file = @file($error["fullFile"], FILE_IGNORE_NEW_LINES);
			for($l = max(0, $error["line"] - 10); $l < $error["line"] + 10; ++$l){
				$this->addLine("[" . ($l + 1) . "] " . @$file[$l]);
				$this->data["code"][$l + 1] = @$file[$l];
			}
		}
		
		$this->addLine();
		
		if(Translate::checkTurkish() === "yes"){
			$this->addLine("Ayrıntı:");
		}else{
			$this->addLine("Backtrace:");
		}
		
		foreach(($this->data["trace"] = $error["trace"]) as $line){
			$this->addLine($line);
		}
		
		$this->addLine();
	}
	
	private function generalData(){
		$version = new VersionString();
		$this->data["general"] = [];
		$this->data["general"]["name"] = $this->server->getName();
		$this->data["general"]["version"] = $version->get(false);
		$this->data["general"]["build"] = $version->getBuild();
		$this->data["general"]["protocol"] = ProtocolInfo::CURRENT_PROTOCOL;
		$this->data["general"]["api"] = \pocketmine\API_VERSION;
		$this->data["general"]["raklib"] = RakLib::VERSION;
		$this->data["general"]["uname"] = php_uname("a");
		$this->data["general"]["php"] = phpversion();
		$this->data["general"]["zend"] = zend_version();
		$this->data["general"]["php_os"] = PHP_OS;
		$this->data["general"]["os"] = Utils::getOS();
		if(Translate::checkTurkish() === "yes"){
			$this->addLine($this->server->getName() . " Sürüm: " . $version->get(false) . " #" . $version->getBuild() . " [Protokol " . ProtocolInfo::CURRENT_PROTOCOL . "; API " . API_VERSION . "]");
			$this->addLine("PHP Sürümü: " . phpversion());
			$this->addLine("Zend Sürümü: " . zend_version());
			$this->addLine("İşletim Sistemi: " . PHP_OS . ", " . Utils::getOS());
			$this->addLine();
			$this->addLine("Yüklenmiş Dünya Sayısı: " . count($this->server->getLevels()));
			$this->addLine("Aktif Oyuncular: " . count($this->server->getOnlinePlayers()) . "/" . $this->server->getMaxPlayers());
		}else{
			$this->addLine($this->server->getName() . " Version: " . $version->get(false) . " #" . $version->getBuild() . " [Protocol " . ProtocolInfo::CURRENT_PROTOCOL . "; API " . API_VERSION . "]");
			$this->addLine("PHP Version: " . phpversion());
			$this->addLine("Zend Version: " . zend_version());
			$this->addLine("Operating System: " . PHP_OS . ", " . Utils::getOS());
			$this->addLine();
			$this->addLine("Number of Loaded Worlds: " . count($this->server->getLevels()));
			$this->addLine("Players Online: " . count($this->server->getOnlinePlayers()) . "/" . $this->server->getMaxPlayers());
		}
	}

	public function addLine($line = ""){
		fwrite($this->fp, $line . PHP_EOL);
	}

	public function add($str){
		fwrite($this->fp, $str);
	}

}
