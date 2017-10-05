<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/   Unleash Your Power Turkey!

namespace{
	function safe_var_dump(){
		static $cnt = 0;
		foreach(func_get_args() as $var){
			switch(true){
				case is_array($var):
					echo str_repeat("  ", $cnt) . "array(" . count($var) . ") {" . PHP_EOL;
					foreach($var as $key => $value){
						echo str_repeat("  ", $cnt + 1) . "[" . (is_integer($key) ? $key : '"' . $key . '"') . "]=>" . PHP_EOL;
						++$cnt;
						safe_var_dump($value);
						--$cnt;
					}
					echo str_repeat("  ", $cnt) . "}" . PHP_EOL;
					break;
				case is_int($var):
					echo str_repeat("  ", $cnt) . "int(" . $var . ")" . PHP_EOL;
					break;
				case is_float($var):
					echo str_repeat("  ", $cnt) . "float(" . $var . ")" . PHP_EOL;
					break;
				case is_bool($var):
					echo str_repeat("  ", $cnt) . "bool(" . ($var === true ? "true" : "false") . ")" . PHP_EOL;
					break;
				case is_string($var):
					echo str_repeat("  ", $cnt) . "string(" . strlen($var) . ") \"$var\"" . PHP_EOL;
					break;
				case is_resource($var):
					echo str_repeat("  ", $cnt) . "resource() of type (" . get_resource_type($var) . ")" . PHP_EOL;
					break;
				case is_object($var):
					echo str_repeat("  ", $cnt) . "object(" . get_class($var) . ")" . PHP_EOL;
					break;
				case is_null($var):
					echo str_repeat("  ", $cnt) . "NULL" . PHP_EOL;
					break;
			}
		}
	}
}

namespace pocketmine{
	
	use darksystem\DarkSystem;
	use pocketmine\darkbot\DarkBot;
	use pocketmine\utils\MainLogger;
	use pocketmine\utils\Terminal;
	use pocketmine\utils\Utils;
	use pocketmine\setup\Setup;

	const VERSION = "2.1.7";
	const DARKBOT_VERSION = "1.0.0";
	const API_VERSION = "3.0.1";
	const TAG = "dev";
	const CURRENT_STATUS = "beta";
	const CODENAME = "DarkSystem";
	const CREATOR = "DarkYusuf13";
	
	if(\Phar::running(true) !== ""){
		@define("pocketmine\\PATH", \Phar::running(true) . "/");
	}else{
		@define("pocketmine\\PATH", getcwd() . DIRECTORY_SEPARATOR);
	}
	
	if(!strpos(VERSION, ".")){
		echo "[HATA] Geçersiz DarkSystem Sürümü!" . PHP_EOL;
		exit(1);
	}
	
	if(!strpos(DARKBOT_VERSION, ".")){
		echo "[HATA] Geçersiz DarkBot Sürümü!" . PHP_EOL;
		exit(1);
	}
	
	if(!strpos(API_VERSION, ".")){
		echo "[HATA] Geçersiz API Sürümü!" . PHP_EOL;
		exit(1);
	}
	
	$codename = "DarkSystem"; //WARNING: Do not change!
	if(CODENAME !== $codename){
		echo "[HATA] Orjinal Olmayan DarkSystem Yüklemesi Bulundu!" . PHP_EOL;
		exit(1);
	}
	
	$creator = "DarkYusuf13"; //WARNING: Do not change!
	if(CREATOR !== $creator){
		echo "[HATA] Orjinal Olmayan DarkSystem Yüklemesi Bulundu!" . PHP_EOL;
		exit(1);
	}
	
	if(version_compare("7.0", PHP_VERSION) > 0){
		echo "[HATA] PHP 7.0 Kullanmalısınız!" . PHP_EOL;
		echo "[HATA] Yükleyici Kullanarak İndiriniz!" . PHP_EOL;
		exit(1);
	}

	if(!extension_loaded("pthreads")){
		echo "[HATA] pthreads Bulunamadı!" . PHP_EOL;
		echo "[HATA] Yükleyici Kullanarak İndiriniz!" . PHP_EOL;
		exit(1);
	}
	
	if(!class_exists("ClassLoader", false)){
		require_once(\pocketmine\PATH . "src/spl/ClassLoader.php");
		require_once(\pocketmine\PATH . "src/spl/BaseClassLoader.php");
	}

	$autoloader = new \BaseClassLoader();
	$autoloader->addPath(\pocketmine\PATH . "src");
	$autoloader->addPath(\pocketmine\PATH . "src" . DIRECTORY_SEPARATOR . "spl");
	$autoloader->register(true);
	
	set_time_limit(0);

	gc_enable();
	
	error_reporting(-1);
	
	ini_set("allow_url_fopen", 1);
	ini_set("display_errors", 1);
	ini_set("display_startup_errors", 1);
	ini_set("default_charset", "UTF-8");

	ini_set("memory_limit", -1);
	
	define('pocketmine\START_TIME', microtime(true));

	$opts = getopt("", ["data:", "eklentiler:", "no-setup", "enable-profiler"]);
	
	define('pocketmine\DATA', isset($opts["data"]) ? $opts["data"] . DIRECTORY_SEPARATOR : \getcwd() . DIRECTORY_SEPARATOR);
	
	$lang = "Bilinmeyen";
	if(!file_exists(\pocketmine\DATA . "sunucu.properties") && !file_exists(\pocketmine\DATA . "server.properties") && !isset($opts["no-setup"])){
		$setup = new Setup();
		$lang = $setup->getDefaultLang();
	}
	
	if(Translate::checkTurkish() === "yes"){
		define('pocketmine\PLUGIN_PATH', isset($opts["eklentiler"]) ? $opts["eklentiler"] . DIRECTORY_SEPARATOR : \getcwd() . DIRECTORY_SEPARATOR . "eklentiler" . DIRECTORY_SEPARATOR);
	}else{
		define('pocketmine\PLUGIN_PATH', isset($opts["eklentiler"]) ? $opts["eklentiler"] . DIRECTORY_SEPARATOR : \getcwd() . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR);
	}

	Terminal::init();

	define('pocketmine\ANSI', Terminal::hasFormattingCodes());

	if(!file_exists(\pocketmine\DATA)){
		mkdir(\pocketmine\DATA, 0777, true);
	}
	
	date_default_timezone_set("UTC");
	
	function kill($pid){
		switch(Utils::getOS()){
			case "win":
				exec("taskkill.exe /F /PID " . ((int) $pid) . " > NUL");
				break;
			case "mac":
			case "linux":
			default:
				if(function_exists("posix_kill")){
					posix_kill($pid, SIGKILL);
				}else{
					exec("kill -9 " . ((int)$pid) . " > /dev/null 2>&1");
				}
		}
	}
	
	function getTrace($start = 1, $trace = null){
		if($trace === null){
			if(function_exists("xdebug_get_function_stack")){
				$trace = array_reverse(xdebug_get_function_stack());
			}else{
				$e = new \Exception();
				$trace = $e->getTrace();
			}
		}

		$messages = [];
		$j = 0;
		for($i = (int) $start; isset($trace[$i]); ++$i, ++$j){
			$params = "";
			if(isset($trace[$i]["args"]) || isset($trace[$i]["params"])){
				if(isset($trace[$i]["args"])){
					$args = $trace[$i]["args"];
				}else{
					$args = $trace[$i]["params"];
				}
				
				foreach($args as $name => $value){
					$params .= (is_object($value) ? get_class($value) . " " . (method_exists($value, "__toString") ? $value->__toString() : "object") : gettype($value) . " " . (is_array($value) ? "Array()" : Utils::printable(@strval($value)))) . ", ";
				}
			}
			
			$messages[] = "#$j " . (isset($trace[$i]["file"]) ? cleanPath($trace[$i]["file"]) : "") . "(" . (isset($trace[$i]["line"]) ? $trace[$i]["line"] : "") . "): " . (isset($trace[$i]["class"]) ? $trace[$i]["class"] . (($trace[$i]["type"] === "dynamic" || $trace[$i]["type"] === "->") ? "->" : "::") : "") . $trace[$i]["function"] . "(" . Utils::printable(substr($params, 0, -2)) . ")";
		}

		return $messages;
	}
	
	function cleanPath($path){
		return rtrim(str_replace(["\\", ".php", "phar://", rtrim(str_replace(["\\", "phar://"], ["/", ""], \pocketmine\PATH), "/"), rtrim(str_replace(["\\", "phar://"], ["/", ""], \pocketmine\PLUGIN_PATH), "/")], ["/", "", "", "", ""], $path), "/");
	}
	
	$konsol = new MainLogger(\pocketmine\ANSI);
	
	$errors = 0;

	if(php_sapi_name() !== "cli"){
		$konsol->critical("You must run DarkSystem using the CLI.");
		++$errors;
	}

	if(!extension_loaded("sockets")){
		$konsol->critical("Unable to find the Socket extension.");
		++$errors;
	}

	$pthreads_version = phpversion("pthreads");
	if(substr_count($pthreads_version, ".") < 2){
		$pthreads_version = "0.$pthreads_version";
	}
	
	if(version_compare($pthreads_version, "3.1.5") < 0){
		$konsol->critical("pthreads >= 3.1.5 is required, while you have $pthreads_version.");
		++$errors;
	}
	
	if(extension_loaded("pocketmine")){
		if(version_compare(phpversion("pocketmine"), "0.0.1") < 0){
			$konsol->critical("You have the native DarkSystem extension, but your version is lower than 0.0.1.");
			++$errors;
		}elseif(version_compare(phpversion("pocketmine"), "0.0.4") > 0){
			$konsol->critical("You have the native DarkSystem extension, but your version is higher than 0.0.4.");
			++$errors;
		}
	}
	
	if(!extension_loaded("curl")){
		$konsol->critical("Unable to find the cURL extension.");
		++$errors;
	}

	if(!extension_loaded("yaml")){
		$konsol->critical("Unable to find the YAML extension.");
		++$errors;
	}

	if(!extension_loaded("sqlite3")){
		$konsol->critical("Unable to find the SQLite3 extension.");
		++$errors;
	}

	if(!extension_loaded("zlib")){
		$konsol->critical("Unable to find the Zlib extension.");
		++$errors;
	}

	if($errors > 0){
		$konsol->critical("Lütfen PHP'yi Güncelleyiniz!");
		$konsol->shutdown();
		exit(1);
	}
	
	@define("ENDIANNESS", (pack("d", 1) === "\77\360\0\0\0\0\0\0" ? 0x00 : 0x01));
	@define("INT32_MASK", is_int(0xffffffff) ? 0xffffffff : -1);
	@ini_set("opcache.mmap_base", bin2hex(random_bytes(8)));
	
	ThreadManager::init();
	
	//new DarkSystem($autoloader, $konsol, \pocketmine\PATH, \pocketmine\DATA, \pocketmine\PLUGIN_PATH, $lang);
	new Server($autoloader, $konsol, \pocketmine\PATH, \pocketmine\DATA, \pocketmine\PLUGIN_PATH, $lang);
	
	foreach(ThreadManager::getInstance()->getAll() as $id => $thread){
		$konsol->debug("Durduruluyor: " . (new \ReflectionClass($thread))->getShortName());
		$thread->quit();
	}
	
	$konsol->info("§cSunucu Durduruldu!");
	$konsol->shutdown();
	exit(0);
}
