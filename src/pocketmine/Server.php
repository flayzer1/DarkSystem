<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine;

use darksystem\Registerer;
use darksystem\CrashReport;
use darksystem\DarkSystem;
use pocketmine\block\Block;
use darksystem\PacketManager;
use pocketmine\inventory\customUI\CustomUI;
use darksystem\ThemeManager;
use darksystem\darkbot\DarkBot;
use darksystem\multicore\CoreStarter;
use darksystem\darkbot\command\SpawnDarkBotCommand;
use pocketmine\command\CommandReader;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\command\SimpleCommandMap;
use pocketmine\entity\{Entity, Attribute, Effect, Arrow, Item as DroppedItem};
use pocketmine\event\HandlerList;
use pocketmine\event\level\LevelInitEvent;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\event\Timings;
use pocketmine\event\TimingsHandler;
use pocketmine\inventory\CraftingManager;
use pocketmine\inventory\InventoryType;
use pocketmine\inventory\Recipe;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;
use pocketmine\item\enchantment\{Enchantment, EnchantmentLevelTable};
use pocketmine\item\Item;
use darksystem\language\Language;
use pocketmine\level\format\anvil\Anvil;
use pocketmine\level\format\mcregion\McRegion;
use pocketmine\level\format\LevelProviderManager;
use pocketmine\level\Level;
use darksystem\metadata\EntityMetadataStore;
use darksystem\metadata\LevelMetadataStore;
use darksystem\metadata\PlayerMetadataStore;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\Network;
use pocketmine\network\CompressBatchedTask;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\network\protocol\BatchPacket;
use pocketmine\network\protocol\CraftingDataPacket;
use pocketmine\network\protocol\DataPacket;
use pocketmine\network\protocol\PlayerListPacket;
use pocketmine\network\query\QueryHandler;
use pocketmine\network\RakLibInterface;
use pocketmine\network\rcon\RCON;
use pocketmine\network\SourceInterface;
use pocketmine\permission\BanList;
use pocketmine\permission\DefaultPermissions;
use pocketmine\plugin\PharPluginLoader;
use pocketmine\plugin\ScriptPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\plugin\PluginManager;
use darksystem\resourcepacks\ResourcePackManager;
use darksystem\behaviorpacks\BehaviorPackManager;
use pocketmine\scheduler\ServerScheduler;
use pocketmine\scheduler\FileWriteTask;
use pocketmine\tile\Tile;
use pocketmine\utils\Binary;
use pocketmine\utils\Cache;
use pocketmine\utils\Config;
use pocketmine\utils\LevelException;
use pocketmine\utils\MainLogger;
use pocketmine\utils\MetadataConvertor;
use pocketmine\utils\MySQLManager;
use pocketmine\utils\ServerException;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\TextWrapper;
use pocketmine\utils\Utils;
use pocketmine\utils\UUID;
use pocketmine\utils\VersionString;
use pocketmine\level\generator\biome\Biome;
use pocketmine\level\generator\nether\Nether;
use pocketmine\level\generator\ender\Ender;
use pocketmine\level\generator\normal\Normal;
use pocketmine\entity\animal\walking\{Chicken, Cow, Mooshroom, Ocelot, Pig, Rabbit, Sheep};
use pocketmine\entity\monster\flying\{Blaze, Ghast};
use pocketmine\entity\monster\walking\{CaveSpider, Creeper, Enderman, IronGolem, PigZombie, Silverfish, Skeleton, SnowGolem, Spider, Wolf, Zombie, ZombieVillager};
use pocketmine\entity\projectile\FireBall;

class Server extends DarkSystem{
	
	/** @var Server */
	private static $instance = null;
	
	/** @var BanList */
	private $banByName = null;

	/** @var BanList */
	private $banByIP = null;
	
	/** @var BanList */
	private $banByCID = null;
	
	/** @var Config */
	private $operators = null;

	/** @var Config */
	private $whitelist = null;

	/** @var bool */
	private $isRunning = true;

	private $hasStopped = false;
	
	private $pluginMgr = null;
	
	private $scheduler = null;
	
	private $generationMgr = null;
	
	private $tickCounter;
	private $nextTick = 0;
	private $tickAverage = [20, 20, 20, 20, 20];
	private $useAverage = [20, 20, 20, 20, 20];
	
	private $knsol;
	
	private $console = null;
	private $consoleThreaded;
	
	private $cmdMap = null;
	
	private $craftingMgr;

	/** @var ConsoleCommandSender */
	private $consoleSender;

	/** @var int */
	private $maxPlayers = 25;

	/** @var bool */
	private $autoSave;
	
	/** @var bool */
	private $autoGenerate;
	
	/** @var bool */
	private $savePlayerData;

	/** @var RCON */
	private $rcon;

	/** @var EntityMetadataStore */
	private $entityMetadata;

	/** @var PlayerMetadataStore */
	private $playerMetadata;

	/** @var LevelMetadataStore */
	private $levelMetadata;

	/** @var Network */
	private $network;

	private $networkCompressionAsync = true;
	public $networkCompressionLevel = 7;
	
	private $autoSaveTicker = 0;
	private $autoSaveTicks = 6000;
	
	private $language;
	
	private $forceLanguage = true;
	
	private $serverID;
	
	private $autoloader;
	private $filePath;
	private $dataPath;
	private $pluginPath;
	
	/** @var QueryHandler */
	private $queryHandler;

	/** @var QueryRegenerateEvent */
	private $queryRegenerateTask = null;

	/** @var Config */
	private $properties;
	
	private $propertyCache = [];
	
	/** @var Config */
	private $config;

	/** @var Config */
	private $softConfig;

	/** @var Player[] */
	private $players = [];

	/** @var Player[] */
	private $playerList = [];

	private $identifiers = [];

	/** @var Level[] */
	private $levels = [];

	/** @var Level */
	private $levelDefault = null;
	
	private $useAnimal;
	private $animalLimit;
	private $useMonster;
	private $monsterLimit;
	
	public $packetMgr = null;
	
	private $spawnedEntity = [];
	
	private $unloadLevelQueue = [];
	
	private $serverPublicKey = "";
	private $serverPrivateKey = "";
	private $serverToken = "hksdYI3has";
	
	private $isUseEncrypt = false;
	
	private $craftList = [];
	
	public $keepInventory = false;
	public $netherEnabled = false;
	public $netherName = "nether";
	public $weatherEnabled = false;
	public $weatherRandomDurationMin = 6000;
	public $weatherRandomDurationMax = 12000;
	public $lightningTime = 200;
	public $lightningFire = false;
	public $endEnabled = false;
    public $endName = "end";
    public $redstoneEnabled = false;
	public $checkMovement = true;
	public $antiFly = true;
	public $allowInstabreak = false;
	public $dbotBroadcast = false;
	public $isCmdBlockEnable = true;
	public $advancedFlyCheck = false;
	public $forceResources = false;
	public $resourceStack = [];
	public $forceBehavior = false;
	public $behaviorStack = [];
	
	function unlink(){
		return true;
	}
	
	public function addSpawnedEntity($entity){
		if($entity instanceof Player){
			return false;
		}
		
		$this->spawnedEntity[$entity->getId()] = $entity;
	}

	public function removeSpawnedEntity($entity){
		unset($this->spawnedEntity[$entity->getId()]);
	}
	
	public function isUseAnimal(){
		return $this->useAnimal;
	}

	public function getAnimalLimit(){
		return $this->animalLimit;
	}

	public function isUseMonster(){
		return $this->useMonster;
	}

	public function getMonsterLimit(){
		return $this->monsterLimit;
	}
	
	public function getName(){
		$class = $this->getCallingClass();
		if(strchr($class, "SpoonDetector")){
			if(Translate::checkTurkish() === "yes"){
				$this->konsol->debug($class . " İsimli Spoon Detektörü Engellendi!");
			}else{
				$this->konsol->debug("A Spoon Detector Blocked Called as (!): " . $class);
			}
			
			return "PocketMine-MP";
		}
		
		return "DarkSystem";
	}
	
	private function getCallingClass(){
		$trace = debug_backtrace();
		$class = $trace[1]["class"];
		for($i=1; $i < count($trace); $i++){
			if(isset($trace[$i])){
				if($class != $trace[$i]["class"]){
					return $trace[$i]["class"];
				}
			}
		}
		
		return "null";
	}
	
	public function isRunning(){
		return $this->isRunning === true;
	}
	
	public function getDarkSystemVersion(){
		return \pocketmine\VERSION;
	}
	
	public function getPocketMineVersion(){
		return \pocketmine\VERSION;
	}
	
	public function getFormattedVersion($prefix = ""){
		return (\pocketmine\VERSION !== "" ? $prefix . \pocketmine\VERSION : "");
	}
	
	public function getCodename(){
		return \pocketmine\CODENAME;
	}
	
	public function getCurrentStatus(){
		return \pocketmine\CURRENT_STATUS;
	}
	
	public function getCreator(){
		return \pocketmine\CREATOR;
	}
	
	public function getTag(){
		return \pocketmine\TAG;
	}
	
	public function getVersion(){
		return ProtocolInfo::MINECRAFT_VERSION;
	}
	
	public function getApiVersion(){
		return \pocketmine\API_VERSION;
	}
	
	public function getDarkBotVersion(){
		return \pocketmine\DARKBOT_VERSION;
	}
	
	public function getDarkBotAI(){
		return SpawnDarkBotCommand::$darkbot;
	}
	
	public function getFilePath(){
		return $this->filePath;
	}
	
	public function getDataPath(){
		return $this->dataPath;
	}
	
	public function getPluginPath(){
		return $this->pluginPath;
	}
	
	public function getMaxPlayers(){
		return $this->maxPlayers;
	}
	
	public function getPort(){
		return $this->getConfigInt("server-port", 19132);
	}
	
	public function getViewDistance(){
		return 72;
	}
	
	public function getIp(){
		return $this->getConfigString("server-ip", "0.0.0.0");
	}
	
	public function getServerUniqueId(){
		return $this->serverID;
	}
	
	public function getServerLanguage(){
		if(!file_exists($this->getDataPath() . "sunucu.properties") && !file_exists($this->getDataPath() . "yoneticiler.json") && !file_exists($this->getDataPath() . "beyaz-liste.json")){
			return Translate::ENG;
		}else{
			return Translate::TUR;
		}
	}
	
	public function getServerName(){
		if(Translate::checkTurkish() === "yes"){
			return $this->getConfigString("motd", "DarkSystem Sunucusu");
		}else{
			return $this->getConfigString("motd", "DarkSystem Server");
		}
	}
	
	public function getAutoSave(){
		return $this->autoSave;
	}
	
	public function setAutoSave($value){
		$this->autoSave = (bool) $value;
		foreach($this->levels as $l){
			$l->setAutoSave($this->autoSave);
		}
	}
	
	public function getAutoGenerate(){
		return $this->autoGenerate;
	}
	
	public function setAutoGenerate($value){
		$this->autoGenerate = (bool) $value;
	}
	
	public function getSavePlayerData(){
		return $this->savePlayerData;
	}
	
	public function setSavePlayerData($value){
		$this->savePlayerData = (bool) $value;
	}
	
	public function getOps(){
		return $this->operators;
	}
	
	public function getLevelType(){
		return $this->getConfigString("level-type", "DEFAULT");
	}
	
	public function getGenerateStructures(){
		return $this->getConfigBoolean("generate-structures", true);
	}
	
	public function getGamemode(){
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	
	public function getForceGamemode(){
		return $this->getConfigBoolean("force-gamemode", true);
	}
	
	public static function getGamemodeString($mode){
		switch((int) $mode){
			case Player::SURVIVAL:
				return "%gameMode.survival";
			case Player::CREATIVE:
				return "%gameMode.creative";
			case Player::ADVENTURE:
				return "%gameMode.adventure";
			case Player::SPECTATOR:
				return "%gameMode.spectator";
		}

		return "BILINMEYEN";
	}
	
	public static function getGamemodeFromString($str){
		switch(strtolower(trim($str))){
			case (string) Player::SURVIVAL:
			case "survival":
			case "s":
				return Player::SURVIVAL;
			case (string) Player::CREATIVE:
			case "creative":
			case "c":
				return Player::CREATIVE;
			case (string) Player::ADVENTURE:
			case "adventure":
			case "a":
				return Player::ADVENTURE;
			case (string) Player::SPECTATOR:
			case "spectator":
			case "view":
			case "v":
				return Player::SPECTATOR;
		}
		
		return -1;
	}
	
	public static function getDifficultyFromString($str){
		switch(strtolower(trim($str))){
			case "0":
			case "peaceful":
			case "p":
				return 0;
			case "1":
			case "easy":
			case "e":
				return 1;
			case "2":
			case "normal":
			case "n":
				return 2;
			case "3":
			case "hard":
			case "h":
				return 3;
		}
		
		return -1;
	}
	
	public function getDifficulty(){
		return $this->getConfigInt("difficulty", 1);
	}
	
	public function hasWhitelist(){
		return $this->getConfigBoolean("white-list", false);
	}
	
	public function getSpawnRadius(){
		return $this->getConfigInt("spawn-protection", 16);
	}
	
	public function getAllowFlight(){
		return $this->getConfigBoolean("allow-flight", false);
	}
	
	public function isHardcore(){
		return $this->getConfigBoolean("hardcore", false);
	}
	
	public function getDefaultGamemode(){
		return $this->getConfigInt("gamemode", 0) & 0b11;
	}
	
	public function getMotd(){
		if(Translate::checkTurkish() === "yes"){
			return $this->getConfigString("motd", "DarkSystem Sunucusu");
		}else{
			return $this->getConfigString("motd", "DarkSystem Server");
		}
	}
	
	public function getDarkBot(){
		return $this->dbot;
	}
	
	public function getDarkBotPrefix(){
		return DarkBot::PREFIX;
	}
	
	public function getLoader(){
		return $this->autoloader;
	}
	
	public function getLogger(){
		return $this->konsol;
	}
	
	public function getEntityMetadata(){
		return $this->entityMetadata;
	}
	
	public function getPlayerMetadata(){
		return $this->playerMetadata;
	}
	
	public function getLevelMetadata(){
		return $this->levelMetadata;
	}
	
	public function getPluginManager(){
		return $this->pluginMgr;
	}
	
	public function getCraftingManager(){
		return $this->craftingMgr;
	}
	
	public function getResourcePackManager(){
		return $this->resourceMgr;
	}
	
	public function getBehaviorPackManager(){
		return $this->behaviorMgr;
	}
	
	public function getQueryInformation(){
		return $this->queryRegenerator;
	}
	
	public function getScheduler(){
		return $this->scheduler;
	}
	
	public function getTick(){
		return $this->tickCounter;
	}
	
	public function getTicksPerSecond(){
		return round(array_sum($this->tickAverage) / count($this->tickAverage), 2);
	}
	
	public function getTickUsage(){
		return round((array_sum($this->useAverage) / count($this->useAverage)) * 100, 2);
	}
	
	public function blockAddress($address, $timeout = 300){
		$this->network->blockAddress($address, $timeout);
	}
	
	public function sendPacket($address, $port, $payload){
		$this->network->sendPacket($address, $port, $payload);
	}
	
	public function getInterfaces(){
		return $this->network->getInterfaces();
	}
	
	public function addInterface(SourceInterface $interface){
		$this->network->registerInterface($interface);
	}
	
	public function removeInterface(SourceInterface $interface){
		$interface->shutdown();
		$this->network->unregisterInterface($interface);
	}
	
	public function getCommandMap(){
		return $this->cmdMap;
	}
	
	public function getOnlinePlayers(){
		return $this->playerList;
	}

	public function addRecipe(Recipe $recipe){
		$this->craftingMgr->registerRecipe($recipe);
	}
	
	public function getLanguage(){
		return $this->language;
	}
	
	public function isLanguageForced(){
		return $this->forceLanguage;
	}
	
	public function getNetwork(){
		return $this->network;
	}
	
	public function isCommandBlockEnable(){
		return $this->isCmdBlockEnable;
	}
	
	public function isUseEncrypt(){
		return $this->isUseEncrypt;
	}
	
	public function getTheme(){
		return $this->getThemeManager()->getTheme();
	}
	
	public function getThemeManager(){
		return $this->themeManager;
	}
	
	public function getServerPublicKey(){
		return $this->serverPublicKey;
	}
	
	public function getServerPrivateKey(){
		return $this->serverPrivateKey;
	}
	
	public function getServerToken(){
		return $this->serverToken;
	}
	
	public function isCreditsEnable(){
		$isEnable = false; //TODO: Add config
		switch($isEnable){
			case true:
			$result = 1;
			break;
			case false:
			$result = 0;
			break;
		}
		
		return $result;
	}
	
	public function isSupportProtocol($protocol){
		if(in_array($protocol, ProtocolInfo::ACCEPTED_PROTOCOLS) || $protocol == ProtocolInfo::CURRENT_PROTOCOL){
			return true;
		}
		
		return false;
	}
	
	public function clearChat(){
		foreach($this->getOnlinePlayers() as $p){
			$p->sendMessage(str_repeat(" \n", 60));
		}
	}
	
	public function getOfflinePlayer($name){
		$name = strtolower($name);
		$result = $this->getPlayerExact($name);
		if($result === null){
			$result = new OfflinePlayer($this, $name);
		}

		return $result;
	}
	
	public function getOfflinePlayerData($name){
		$name = strtolower($name);
		
		if(Translate::checkTurkish() === "yes"){
		    $path = $this->getDataPath() . "oyuncular/";
		}else{
			$path = $this->getDataPath() . "players/";
		}
		
		if(file_exists($path . "$name.dat")){
			try{
				$nbt = new NBT(NBT::BIG_ENDIAN);
				$nbt->readCompressed(file_get_contents($path . "$name.dat"));
				
				return $nbt->getData();
			}catch(\Exception $e){
				rename($path . "$name.dat", $path . "$name.dat.bak");
				$this->konsol->notice($this->getLanguage()->translateString("pocketmine.data.playerCorrupted", [$name]));
			}
		}else{
			$this->konsol->notice($this->getLanguage()->translateString("pocketmine.data.playerNotFound", [$name]));
		}
		
		$spawn = $this->getDefaultLevel()->getSafeSpawn();
		$nbt = new CompoundTag("", [
			new LongTag("firstPlayed", floor(microtime(true) * 1000)),
			new LongTag("lastPlayed", floor(microtime(true) * 1000)),
			new ListTag("Pos", [
				new DoubleTag(0, $spawn->x),
				new DoubleTag(1, $spawn->y),
				new DoubleTag(2, $spawn->z)
			]),
			new StringTag("Level", $this->getDefaultLevel()->getName()),
			new ListTag("Inventory", []),
			new ListTag("EnderChestInventory", []),
			new ListTag("recipeBook", []),
			new CompoundTag("Achievements", []),
			new CompoundTag("EnderItems", []),
			new IntTag("Score", 0),
			new IntTag("ShoulderEntityLeft", 0),
			new IntTag("ShoulderEntityRight", 0),
			new IntTag("seenCredits", $this->isCreditsEnable()),
			new IntTag("playerGameType", $this->getGamemode()),
			new ListTag("Motion", [
				new DoubleTag(0, 0.0),
				new DoubleTag(1, 0.0),
				new DoubleTag(2, 0.0)
			]),
			new ListTag("Rotation", [
				new FloatTag(0, 0.0),
				new FloatTag(1, 0.0)
			]),
			new FloatTag("FallDistance", 0.0),
			new ShortTag("Fire", 0),
			new ShortTag("Air", 300),
			new ByteTag("OnGround", 1),
			new ByteTag("Invulnerable", 0),
			new StringTag("NameTag", $name),
		]);
		
		$nbt->Pos->setTagType(NBT::TAG_Double);
		$nbt->Inventory->setTagType(NBT::TAG_Compound);
		$nbt->EnderChestInventory->setTagType(NBT::TAG_Compound);
		$nbt->Motion->setTagType(NBT::TAG_Double);
		$nbt->Rotation->setTagType(NBT::TAG_Float);
		
		return $nbt;
	}
	
	public function saveOfflinePlayerData($name, CompoundTag $nbtTag, $async = false){
		$ev = new PlayerDataSaveEvent($nbtTag, $name);
		$this->getPluginManager()->callEvent($ev);
		
		if($ev->isCancelled()){
			if(Translate::checkTurkish() === "yes"){
				$this->konsol->emergency("Oyuncu Bilgileri Kaydedilemedi!");
			}else{
				$this->konsol->emergency("Player Data Save Error!");
			}
		}
		
		$nbt = new NBT(NBT::BIG_ENDIAN);
		
		try{
			$nbt->setData($nbtTag);
			
			if(Translate::checkTurkish() === "yes"){
			    if($async){
				    $this->scheduler->scheduleAsyncTask(new FileWriteTask($this->getDataPath() . "oyuncular/" . strtolower($name) . ".dat", $nbt->writeCompressed()));
			    }else{
				    file_put_contents($this->getDataPath() . "oyuncular/" . strtolower($name) . ".dat", $nbt->writeCompressed());
			    }
			}else{
			    if($async){
				    $this->scheduler->scheduleAsyncTask(new FileWriteTask($this->getDataPath() . "players/" . strtolower($name) . ".dat", $nbt->writeCompressed()));
			    }else{
				    file_put_contents($this->getDataPath() . "players/" . strtolower($name) . ".dat", $nbt->writeCompressed());
			    }
			}
		}catch(\Exception $e){
			$this->konsol->critical($this->getLanguage()->translateString("pocketmine.data.saveError", [$name, $e->getMessage()]));
			if(\pocketmine\DEBUG > 1 && $this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
		}
	}
	
	public function getPlayer($name){
		$found = null;
		$name = strtolower($name);
		$delta = PHP_INT_MAX;
		foreach($this->getOnlinePlayers() as $p){
			$playerName = strtolower($p->getName());
			if(strpos($playerName, $name) === 0){
				$curDelta = strlen($playerName) - strlen($name);
				if($curDelta < $delta){
					$found = $p;
					$delta = $curDelta;
				}
				
				if($curDelta == 0){
					break;
				}
			}
		}

		return $found;
	}
	
	public function getPlayerExact($name){
		$name = strtolower($name);
		foreach($this->getOnlinePlayers() as $p){
			if(strtolower($p->getName()) === $name){
				return $p;
			}
		}

		return null;
	}
	
	public function matchPlayer($partialName){
		$partialName = strtolower($partialName);
		$matchedPlayers = [];
		foreach($this->getOnlinePlayers() as $p){
			$playerName = strtolower($p->getName());
			if($playerName === $partialName){
				$matchedPlayers = [$p];
				break;
			}elseif(strpos($playerName, $partialName) !== false){
				$matchedPlayers[] = $p;
			}
		}

		return $matchedPlayers;
	}
	
	public function removePlayer(Player $player){
		if(isset($this->identifiers[$hash = spl_object_hash($player)])){
			$identifier = $this->identifiers[$hash];
			unset($this->players[$identifier]);
			unset($this->identifiers[$hash]);
			return;
		}

		foreach($this->players as $identifier => $p){
			if($player === $p){
				unset($this->players[$identifier]);
				unset($this->identifiers[spl_object_hash($player)]);
				break;
			}
		}
	}
	
	public function getLevels(){
		return $this->levels;
	}
	
	public function getDefaultLevel(){
		return $this->levelDefault;
	}
	
	public function setDefaultLevel($level){
		if($level === null || ($this->isLevelLoaded($level->getFolderName()) && $level !== $this->levelDefault)){
			$this->levelDefault = $level;
		}
	}
	
	public function isLevelLoaded($name){
		return $this->getLevelByName($name) instanceof Level;
	}
	
	public function getLevel($levelId){
		if(isset($this->levels[$levelId])){
			return $this->levels[$levelId];
		}

		return null;
	}
	
	public function getLevelByName($name){
		foreach($this->levels as $l){
			if($l->getFolderName() === $name){
				return $l;
			}
		}

		return null;
	}
	
	public function unloadLevel(Level $level, $forceUnload = false, $direct = false){
		if($direct){
			if($level->unload($forceUnload) === true){
				unset($this->levels[$level->getId()]);
				return true;
			}
		}else{
			$this->unloadLevelQueue[$level->getId()] = ["level" => $level, "force" => $forceUnload];
		}

		return false;
	}
	
	public function loadLevel($name){
		if(trim($name) === ""){
			throw new LevelException("Geçersiz Dünya İsmi!");
		}
		
		if($this->isLevelLoaded($name)){
			return true;
		}elseif(!$this->isLevelGenerated($name)){
			$this->konsol->notice($this->getLanguage()->translateString("pocketmine.level.notFound", [$name]));
			return false;
		}
		
		if(Translate::checkTurkish() === "yes"){
		    $path = $this->getDataPath() . "dunyalar/" . $name . "/";
		}else{
			$path = $this->getDataPath() . "worlds/" . $name . "/";
		}
		
		$provider = LevelProviderManager::getProvider($path);

		if($provider === null){
			$this->konsol->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, "Bilinmeyen Harita Yükleyici"]));
			return false;
		}
		
		try{
			$level = new Level($this, $name, $path, $provider);
		}catch(\Exception $e){
			$this->konsol->error($this->getLanguage()->translateString("pocketmine.level.loadError", [$name, $e->getMessage()]));
			if($this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
			
			return false;
		}

		$this->levels[$level->getId()] = $level;

		$level->initLevel();

		$this->pluginMgr->callEvent(new LevelLoadEvent($level));
		
		return true;
	}
	
	public function generateLevel($name, $seed = null, $options = []){
		if(trim($name) === "" || $this->isLevelGenerated($name)){
			return false;
		}

		$seed = $seed === null ? Binary::readInt(@Utils::getRandomBytes(4, false)) : (int) $seed;

		if(($provider = LevelProviderManager::getProviderByName($providerName = $this->getProperty("level-settings.default-format", "anvil"))) === null){
			$provider = LevelProviderManager::getProviderByName($providerName = "anvil");
		}

		try{
			if(Translate::checkTurkish() === "yes"){
			    $path = $this->getDataPath() . "dunyalar/" . $name . "/";
			}else{
				$path = $this->getDataPath() . "worlds/" . $name . "/";
			}
			
			$provider::generate($path, $name, $seed, $options);

			$level = new Level($this, $name, $path, $provider);
			$this->levels[$level->getId()] = $level;

			$level->initLevel();
		}catch(\Exception $e){
			$this->konsol->error("Could not generate level \"" . $name . "\": " . $e->getMessage());
			if($this->konsol instanceof MainLogger){
				$this->konsol->logException($e);
			}
			
			return false;
		}

		$this->pluginMgr->callEvent(new LevelInitEvent($level));
		$this->pluginMgr->callEvent(new LevelLoadEvent($level));

		if($this->getAutoGenerate()){
			$centerX = $level->getSpawnLocation()->getX() >> 4;
			$centerZ = $level->getSpawnLocation()->getZ() >> 4;

			$order = [];

			for($X = -3; $X <= 3; ++$X){
				for($Z = -3; $Z <= 3; ++$Z){
					$distance = $X ** 2 + $Z ** 2;
					$chunkX = $X + $centerX;
					$chunkZ = $Z + $centerZ;
					$index = Level::chunkHash($chunkX, $chunkZ);
					$order[$index] = $distance;
				}
			}

			asort($order);

			foreach($order as $index => $distance){
				Level::getXZ($index, $chunkX, $chunkZ);
				$level->generateChunk($chunkX, $chunkZ, true);
			}
		}

		return true;
	}
	
	public function isLevelGenerated($name){
		if(trim($name) === ""){
			return false;
		}
		
		if(Translate::checkTurkish() === "yes"){
		    $path = $this->getDataPath() . "dunyalar/" . $name . "/";
		}else{
			$path = $this->getDataPath() . "worlds/" . $name . "/";
		}
		
		if(!($this->getLevelByName($name) instanceof Level)){
			if(LevelProviderManager::getProvider($path) === null){
				return false;
			}
		}

		return true;
	}
	
	public function findEntity($entityId, Level $expectedLevel = null){
		$levels = $this->levels;
		if($expectedLevel !== null){
			array_unshift($levels, $expectedLevel);
		}

		foreach($levels as $l){
			assert(!$l->isClosed());
			if(($entity = $l->getEntity($entityId)) instanceof Entity){
				return $entity;
			}
		}

		return null;
	}
	
	public function getConfigString($variable, $defaultValue = ""){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (string) $v[$variable];
		}

		return $this->properties->exists($variable) ? $this->properties->get($variable): $defaultValue;
	}
	
	public function getProperty($variable, $defaultValue = null){
		if(!array_key_exists($variable, $this->propertyCache)){
			$v = getopt("", ["$variable::"]);
			if(isset($v[$variable])){
				$this->propertyCache[$variable] = $v[$variable];
			}else{
				$this->propertyCache[$variable] = $this->config->getNested($variable);
			}
		}

		return $this->propertyCache[$variable] === null ? $defaultValue : $this->propertyCache[$variable];
	}
	
	public function setConfigString($variable, $value){
		$this->properties->set($variable, $value);
	}
	
	public function getConfigInt($variable, $defaultValue = 0){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			return (int) $v[$variable];
		}

		return $this->properties->exists($variable) ? (int) $this->properties->get($variable): (int) $defaultValue;
	}
	
	public function setConfigInt($variable, $value){
		$this->properties->set($variable, (int) $value);
	}
	
	public function getConfigBoolean($variable, $defaultValue = false){
		$v = getopt("", ["$variable::"]);
		if(isset($v[$variable])){
			$value = $v[$variable];
		}else{
			$value = $this->properties->exists($variable) ? $this->properties->get($variable): $defaultValue;
		}

		if(is_bool($value)){
			return $value;
		}
		
		switch(strtolower($value)){
			case "on":
			case "true":
			case "1":
			case "yes":
				return true;
		}

		return false;
	}
	
	public function setConfigBool($variable, $value){
		$this->properties->set($variable, $value == true ? "1" : "0");
	}
	
	public function getPluginCommand($name){
		if(($command = $this->cmdMap->getCommand($name)) instanceof PluginIdentifiableCommand){
			return $command;
		}else{
			return null;
		}
	}
	
	public function getNameBans(){
		return $this->banByName;
	}
	
	public function getIPBans(){
		return $this->banByIP;
	}
	
	public function getCIDBans(){
		return $this->banByCID;
	}
	
	public function addOp($name){
		$this->operators->set(strtolower($name), true);
		if(($player = $this->getPlayerExact($name)) instanceof Player){
			$player->recalculatePermissions();
		}
		
		$this->operators->save();
	}
	
	public function removeOp($name){
		$this->operators->remove(strtolower($name));
		if(($player = $this->getPlayerExact($name)) instanceof Player){
			$player->recalculatePermissions();
		}
		
		$this->operators->save();
	}
	
	public function addWhitelist($name){
		$this->whitelist->set(strtolower($name), true);
		$this->whitelist->save();
	}
	
	public function removeWhitelist($name){
		$this->whitelist->remove(strtolower($name));
		$this->whitelist->save();
	}
	
	public function isWhitelisted($name){
		return !$this->hasWhitelist() || $this->operators->exists($name, true) || $this->whitelist->exists($name, true);
	}
	
	public function isOp($name){
		return $this->operators->exists($name, true);
	}
	
	public function getWhitelisted(){
		return $this->whitelist;
	}
	
	public function reloadWhitelist(){
		$this->whitelist->reload();
	}
	
	public function getCommandAliases(){
		$section = $this->getProperty("aliases");
		$result = [];
		if(is_array($section)){
			foreach($section as $key => $value){
				$commands = [];
				if(is_array($value)){
					$commands = $value;
				}else{
					$commands[] = $value;
				}

				$result[$key] = $commands;
			}
		}

		return $result;
	}
	
	public function createQuery($server, $host, $user, $pass, $dbname, $port = 19132, $connect){
		$this->query = new MySQLManager($server, $host, $user, $pass, $dbname, $port = 19132);
		
		if($connect){
			$this->query->Connect();
		}
	}
	
	public function connectQuery(){
		if($this->query !== null){
			$this->query->Connect();
		}
	}
	
	public function getCrashPath(){
		if(Translate::checkTurkish() === "yes"){
		    return $this->getDataPath() . "cokme-arsivleri/";
		}else{
			return $this->getDataPath() . "crashdumps/";
		}
	}
	
	public static function getInstance(){
		return Server::$instance;
	}
	
	public static function getServerId(){
		return Server::$serverId;
	}
	
	public static function microSleep($microseconds){
		Server::$sleeper->synchronized(function($ms){
			Server::$sleeper->wait($ms);
		}, $microseconds);
	}

	public function loadAdvancedConfig(){
		$this->weatherEnabled = $this->getAdvancedProperty("level.weather", false);
		$this->foodEnabled = $this->getAdvancedProperty("player.hunger", true);
		$this->expEnabled = $this->getAdvancedProperty("player.experience", true);
		$this->keepInventory = $this->getAdvancedProperty("player.keep-inventory", false);
		$this->keepExperience = $this->getAdvancedProperty("player.keep-experience", false);
		$this->netherEnabled = $this->getAdvancedProperty("level.allow-nether", false);
		$this->netherName = $this->getAdvancedProperty("level.level-name", "nether");
		$this->endEnabled = $this->getAdvancedProperty("level.allow-end", false);
        $this->endName = $this->getAdvancedProperty("level.end-level-name", "end");
        $this->redstoneEnabled = $this->getAdvancedProperty("redstone.enable", false);
		$this->weatherRandomDurationMin = $this->getAdvancedProperty("level.weather-random-duration-min", 6000);
		$this->weatherRandomDurationMax = $this->getAdvancedProperty("level.weather-random-duration-max", 12000);
		$this->lightningTime = $this->getAdvancedProperty("level.lightning-time", 200);
		$this->lightningFire = $this->getAdvancedProperty("level.lightning-fire", false);
		$this->autoClearInv = $this->getAdvancedProperty("player.auto-clear-inventory", true);
		$this->asyncChunkRequest = $this->getAdvancedProperty("server.async-chunk-request", true);
		$this->limitedCreative = $this->getAdvancedProperty("server.limited-creative", true);
		$this->chunkRadius = $this->getAdvancedProperty("player.chunk-radius", -1);
		$this->allowSplashPotion = $this->getAdvancedProperty("server.allow-splash-potion", true);
		$this->fireSpread = $this->getAdvancedProperty("level.fire-spread", false);
		$this->advancedCommandSelector = $this->getAdvancedProperty("server.advanced-command-selector", false);
		$this->anvilEnabled = $this->getAdvancedProperty("enchantment.enable-anvil", true);
		$this->enchantingTableEnabled = $this->getAdvancedProperty("enchantment.enable-enchanting-table", true);
		$this->countBookshelf = $this->getAdvancedProperty("enchantment.count-bookshelf", false);
		$this->allowInventoryCheats = $this->getAdvancedProperty("inventory.allow-cheats", false);
		$this->checkMovement = $this->getAdvancedProperty("anticheat.check-movement", true);
		$this->allowInstabreak = $this->getAdvancedProperty("anticheat.allow-instabreak", true);
		$this->antiFly = $this->getAdvancedProperty("anticheat.anti-fly", true);
		$this->folderpluginloader = $this->getAdvancedProperty("developer.folder-plugin-loader", false);
		$this->useAnimal = $this->getAdvancedProperty("spawn-animals", false);
		$this->animalLimit = $this->getAdvancedProperty("animals-limit", 0);
		$this->useMonster = $this->getAdvancedProperty("spawn-mobs", false);
		$this->monsterLimit = $this->getAdvancedProperty("mobs-limit", 0);
		$this->forceResources = $this->getAdvancedProperty("packs.force-resources", false);
		$this->resourceStack = $this->getAdvancedProperty("packs.resource-stack", []);
		$this->forceBehavior = $this->getAdvancedProperty("packs.force-behavior", false);
		$this->behaviorStack = $this->getAdvancedProperty("packs.behavior-stack", []);
	}
	
	public function getBuild(){
		return $this->version->getBuild();
	}

	public function getGameVersion(){
		return $this->version->getRelease();
	}
	
	public function __construct(\ClassLoader $autoloader, \ThreadedLogger $knsol, $filePath, $dataPath, $pluginPath, $defaultLang = "Bilinmeyen"){
		Server::$instance = $this;
		$this->autoloader = $autoloader;
		$this->konsol = $knsol;
		$this->filePath = $filePath;
		$this->translate = new Translate($this);
		$this->translate->prepareLang();
		$this->dbot = new DarkBot($this);
		$this->themeManager = new ThemeManager($this);
		$this->core = new CoreStarter($this);
		try{
			if(Translate::checkTurkish() === "yes"){
			if(!file_exists($dataPath . "dunyalar/")){
				mkdir($dataPath . "dunyalar/", 0777);
			}

			if(!file_exists($dataPath . "oyuncular/")){
				mkdir($dataPath . "oyuncular/", 0777);
			}
			
			if(!file_exists($dataPath . "cokme-arsivleri/")){
				mkdir($dataPath . "cokme-arsivleri/", 0777);
			}
			
			if(!file_exists($dataPath . "oyuncu-basarimlari/")){
				mkdir($dataPath . "oyuncu-basarimlari/", 0777);
			}
			
			if(!file_exists($pluginPath)){
				mkdir($pluginPath, 0777);
			}
			}else{
			if(!file_exists($dataPath . "worlds/")){
				mkdir($dataPath . "worlds/", 0777);
			}

			if(!file_exists($dataPath . "players/")){
				mkdir($dataPath . "players/", 0777);
			}
			
			if(!file_exists($dataPath . "crashdumps/")){
				mkdir($dataPath . "crashdumps/", 0777);
			}
			
			if(!file_exists($dataPath . "achievements/")){
				mkdir($dataPath . "achievements/", 0777);
			}
			
			if(!file_exists($pluginPath)){
				mkdir($pluginPath, 0777);
			}
			}
			
			if(\Phar::running(true) === ""){
			   $packages = "src";
			}else{
				$packages = "phar";
			}

			$this->dataPath = realpath($dataPath) . DIRECTORY_SEPARATOR;
			$this->pluginPath = realpath($pluginPath) . DIRECTORY_SEPARATOR;

			/*if(!file_exists($this->getDataPath() . "pocketmine.yml")){
				$content1 = file_get_contents($this->filePath . "src/darksystem/resources/pocketmine.yml");
				@file_put_contents($this->getDataPath() . "pocketmine.yml", $content1);
			}*/
			
			if(!file_exists($this->getDataPath() . "pocketmine.yml")){
				if(file_exists($this->getDataPath() . "lang_cache.txt")){
					$langFile = new Config($configPath = $this->getDataPath() . "lang_cache.txt", Config::ENUM, []);
                    $setupLang = null;
					foreach($langFile->getAll(true) as $langName){
						$setupLang = $langName;
						break;
					}
					
					if(file_exists($this->filePath . "src/darksystem/resources/pocketmine_$setupLang.yml")){
						$content1 = file_get_contents($file = $this->filePath . "src/darksystem/resources/pocketmine_$setupLang.yml");
					}else{
						$content1 = file_get_contents($file = $this->filePath . "src/darksystem/resources/pocketmine_eng.yml");
					}
				}else{
					$content1 = file_get_contents($file = $this->filePath . "src/darksystem/resources/pocketmine_eng.yml");
				}
				
				@file_put_contents($this->getDataPath() . "pocketmine.yml", $content1);
			}
			
			if(file_exists($this->getDataPath() . "lang_cache.txt")){
				unlink($this->getDataPath() . "lang_cache.txt");
			}
			
			if(!file_exists($this->getDataPath() . "pocketmine-advanced.yml")){
				$content2 = file_get_contents($this->filePath . "src/darksystem/resources/pocketmine-advanced.yml");
				@file_put_contents($this->getDataPath() . "pocketmine-advanced.yml", $content2);
			}
			
		    $this->softConfig = new Config($this->getDataPath() . "pocketmine-advanced.yml", Config::YAML, []);
		
			if(!is_dir($this->pluginPath . $this->getName())){
				mkdir($this->pluginPath . $this->getName());
			}
			
			$this->config = new Config($configPath = $this->getDataPath() . "pocketmine.yml", Config::YAML, []);
			$this->cmdReader = new CommandReader($knsol);
			
			//$this->loadAdvancedConfig();
			
			$this->weatherEnabled = false; //A quick fix for a crash
			
			if(Translate::checkTurkish() === "yes"){
			$this->properties = new Config($this->getDataPath() . "sunucu.properties", Config::PROPERTIES, [
				"motd" => "DarkSystem Sunucusu",
				"server-ip" => "0.0.0.0",
				"server-port" => 19132,
				"memory-limit" => "256M",
				"white-list" => false,
				"announce-player-achievements" => false,
				"spawn-protection" => 16,
				"max-players" => 25,
				"allow-flight" => false,
				"spawn-animals" => true,
				"animals-limit" => 0,
				"spawn-mobs" => true,
				"mobs-limit" => 0,
				"gamemode" => 0,
				"force-gamemode" => true,
				"hardcore" => false,
				"pvp" => true,
				"difficulty" => 1,
				"enable-command-block" => true,
				"generator-settings" => "",
				"level-name" => "world",
				"level-seed" => "",
				"level-type" => "DEFAULT",
				"enable-query" => true,
				"auto-query" => false,
				"enable-rcon" => false,
				"rcon.password" => substr(base64_encode(random_bytes(20)), 3, 10),
				"auto-save" => true,
				"auto-generate" => true,
				"save-player-data" => true,
				"time-update" => true,
				"online-mode" => false,
				"use-encrypt" => false,
				"theme" => "classic",
				"random-theme" => false,
				"colorful-theme" => false
			]);
			}else{
			$this->properties = new Config($this->getDataPath() . "server.properties", Config::PROPERTIES, [
				"motd" => "DarkSystem Server",
				"server-ip" => "0.0.0.0",
				"server-port" => 19132,
				"memory-limit" => "256M",
				"white-list" => false,
				"announce-player-achievements" => false,
				"spawn-protection" => 16,
				"max-players" => 25,
				"allow-flight" => false,
				"spawn-animals" => true,
				"animals-limit" => 0,
				"spawn-mobs" => true,
				"mobs-limit" => 0,
				"gamemode" => 0,
				"force-gamemode" => true,
				"hardcore" => false,
				"pvp" => true,
				"difficulty" => 1,
				"enable-command-block" => true,
				"generator-settings" => "",
				"level-name" => "world",
				"level-seed" => "",
				"level-type" => "DEFAULT",
				"enable-query" => true,
				"auto-query" => false,
				"enable-rcon" => false,
				"rcon.password" => substr(base64_encode(random_bytes(20)), 3, 10),
				"auto-save" => true,
				"auto-generate" => true,
				"save-player-data" => true,
				"time-update" => true,
				"online-mode" => false,
				"use-encrypt" => false,
				"theme" => "classic",
				"random-theme" => false,
				"colorful-theme" => false
			]);
			}
			
			if($this->getCurrentStatus() == "alpha" || $this->getCurrentStatus() == "beta"){
				$this->konsol->directSend("§e<?php>");
			}else{
				$this->konsol->directSend("§6》》》");
			}
			
			if($this->getMotd() == "schudoz" || $this->getMotd() == "devlrs"){ //Easter egg
				$random = substr(base64_encode(random_bytes(20)), 3, 10);
				$this->konsol->directSend("\n" . "§" . mt_rand(1, 9) . "" . $random);
			}
			
			$this->konsol->directSend($this->getLogo());
			
			if($this->dbot->check() == "✔"){
				$this->konsol->info($this->dbot->getStartupMessage());
			}
			
			//if(count($this->pluginMgr->getPlugins()) > 0){
				if(Translate::checkTurkish() === "yes"){
					$this->konsol->info("§aEklentiler Yükleniyor...");
				}else{
					$this->konsol->info("§aEnabling Plugins...");
				}
			//}
			
			if(Translate::checkTurkish() === "yes"){
				$lang = Translate::TUR;
			}elseif($this->getProperty("settings.language", Language::FALLBACK_LANGUAGE) !== Translate::TUR){
				$lang = $this->getProperty("settings.language", Language::FALLBACK_LANGUAGE);
			}
			
			if($defaultLang !== "Bilinmeyen" && $lang !== $defaultLang){
				@file_put_contents($configPath, str_replace('language: "' . $lang . '"', 'language: "' . $defaultLang . '"', file_get_contents($configPath)));
				$this->config->reload();
				unset($this->propertyCache["settings.language"]);
			}
			
			if(file_exists($this->filePath . "src/darksystem/resources/darksystem_$lang.yml")){
				$content3 = file_get_contents($file = $this->filePath . "src/darksystem/resources/darksystem_$lang.yml");
			}else{
				$content3 = file_get_contents($file = $this->filePath . "src/darksystem/resources/darksystem_eng.yml");
			}
			
			if(!file_exists($this->getDataPath() . "darksystem.yml")){
				@file_put_contents($this->getDataPath() . "darksystem.yml", $content3);
			}
			
			$internelConfig = new Config($file, Config::YAML, []);
			$this->advancedConfig = new Config($this->getDataPath() . "darksystem.yml", Config::YAML, []);
			
			$cfgVer = $this->getAdvancedProperty("config.version", 0, $internelConfig);
			$advVer = $this->getAdvancedProperty("config.version", 0);

			$this->loadAdvancedConfig();
			
			$this->forceLanguage = $this->getProperty("settings.force-language", true);
			$this->language = new Language($this->getProperty("settings.language", Language::FALLBACK_LANGUAGE));
			
			if(($poolSize = $this->getProperty("settings.async-workers", "auto")) === "auto"){
				$poolSize = ServerScheduler::$WORKERS;
				$processors = Utils::getCoreCount() - 2;
				if($processors > 0){
					$poolSize = max(1, $processors);
				}
			}

			ServerScheduler::$WORKERS = $poolSize;

			if($this->getProperty("network.batch-threshold", 256) >= 0){
				Network::$BATCH_THRESHOLD = (int) $this->getProperty("network.batch-threshold", 256);
			}else{
				Network::$BATCH_THRESHOLD = -1;
			}
			
			$this->networkCompressionLevel = $this->getProperty("network.compression-level", 7);
			$this->networkCompressionAsync = $this->getProperty("network.async-compression", true);

			$this->autoTickRate = (bool) $this->getProperty("level-settings.auto-tick-rate", true);
			$this->autoTickRateLimit = (int) $this->getProperty("level-settings.auto-tick-rate-limit", 20);
			$this->alwaysTickPlayers = (int) $this->getProperty("level-settings.always-tick-players", false);
			$this->baseTickRate = (int) $this->getProperty("level-settings.base-tick-rate", 1);

			$this->scheduler = new ServerScheduler();
			
			if($this->getConfigBoolean("enable-rcon", false)){
				$this->rcon = new RCON($this, $this->getConfigString("rcon.password", ""), $this->getConfigInt("rcon.port", $this->getPort()), ($ip = $this->getIp()) !== "" ? $ip : "0.0.0.0", $this->getConfigInt("rcon.threads", 1), $this->getConfigInt("rcon.clients-per-thread", 50));
			}
			
			if($this->getConfigBoolean("auto-query", false)){
				$data = [];
				$data[$server] = $this->getProperty("query.server", "test");
				$data[$host] = $this->getProperty("query.host", "0.0.0.0");
				$data[$user] = $this->getProperty("query.user", "root");
				$data[$pass] = $this->getProperty("query.pass", "admin");
				$data[$dbname] = $this->getProperty("query.dbname", "test");
				$data[$port] = $this->getProperty("query.port", "19132");
				
				$this->createQuery($data[$server], $data[$host], $data[$user], $data[$pass], $data[$dbname], $data[$port]);
			}
			
			$this->entityMetadata = new EntityMetadataStore();
			$this->playerMetadata = new PlayerMetadataStore();
			$this->levelMetadata = new LevelMetadataStore();
			
			if(Translate::checkTurkish() === "yes"){
			$this->operators = new Config($this->getDataPath() . "yoneticiler.json", Config::JSON);
			$this->whitelist = new Config($this->getDataPath() . "beyaz-liste.json", Config::JSON);
			if(file_exists($this->getDataPath() . "engelli.txt") && !file_exists($this->getDataPath() . "engelli-oyuncular.txt")){
				@rename($this->getDataPath() . "engelli.txt", $this->getDataPath() . "engelli-oyuncular.txt");
			}
			
			@touch($this->getDataPath() . "engelli-oyuncular.txt");
			$this->banByName = new BanList($this->getDataPath() . "engelli-oyuncular.txt");
			$this->banByName->load();
			@touch($this->getDataPath() . "engelli-IPler.txt");
			$this->banByIP = new BanList($this->getDataPath() . "engelli-IPler.txt");
			$this->banByIP->load();
			@touch($this->getDataPath() . "engelli-CIDler.txt");
			$this->banByCID = new BanList($this->getDataPath() . "engelli-CIDler.txt");
			$this->banByCID->load();
			}else{
			$this->operators = new Config($this->getDataPath() . "ops.json", Config::JSON);
			$this->whitelist = new Config($this->getDataPath() . "white-list.json", Config::JSON);
			if(file_exists($this->getDataPath() . "banned.txt") && !file_exists($this->getDataPath() . "banned-players.txt")){
				@rename($this->getDataPath() . "banned.txt", $this->getDataPath() . "banned-players.txt");
			}
			@touch($this->getDataPath() . "banned-players.txt");
			$this->banByName = new BanList($this->getDataPath() . "banned-players.txt");
			$this->banByName->load();
			@touch($this->getDataPath() . "banned-ips.txt");
			$this->banByIP = new BanList($this->getDataPath() . "banned-ips.txt");
			$this->banByIP->load();
			@touch($this->getDataPath() . "banned-cids.txt");
			$this->banByCID = new BanList($this->getDataPath() . "banned-cids.txt");
			$this->banByCID->load();
			}
			
			$this->maxPlayers = $this->getConfigInt("max-players", 25);
			$this->setAutoSave($this->getConfigBoolean("auto-save", true));
			$this->setAutoGenerate($this->getConfigBoolean("auto-generate", true));
			$this->setSavePlayerData($this->getConfigBoolean("save-player-data", true));
			
			$this->useAnimal = $this->getConfigBoolean("spawn-animals", false);
			$this->animalLimit = $this->getConfigInt("animals-limit", 0);
			$this->useMonster = $this->getConfigBoolean("spawn-mobs", false);
			$this->monsterLimit = $this->getConfigInt("mobs-limit", 0);
			$this->isUseEncrypt = $this->getConfigBoolean("use-encrypt", false);
			$this->isCmdBlockEnable = $this->getConfigBoolean("enable-command-block", true);
			
			//New properties here
			$this->advancedFlyCheck = $this->getProperty("player.advanced-fly-check", false);
			
			if($this->getConfigBoolean("hardcore", false) === true && $this->getDifficulty() < 3){
				$this->setConfigInt("difficulty", 3);
			}
			
			define("pocketmine\\DEBUG", (int) $this->getProperty("debug.level", 1));
			if($this->konsol instanceof MainLogger){
				$this->konsol->setLogDebug(\pocketmine\DEBUG > 1);
			}
			
			define("advanced_cache", $this->getProperty("settings.advanced-cache", true));
			
			if(\pocketmine\DEBUG >= 0){
				@cli_set_process_title($this->getName() . TF::SPACE . $this->getDarkSystemVersion());
			}
			
			define("BOOTUP_RANDOM", Utils::getRandomBytes(16));
			
			$this->serverID = Utils::getMachineUniqueId($this->getIp() . $this->getPort());
			
			if(Translate::checkTurkish() === "yes"){
				$this->konsol->debug("Sunucu ID: " . $this->getServerUniqueId());
				$this->konsol->debug("Makine ID: " . Utils::getMachineUniqueId());
			}else{
				$this->konsol->debug("Server ID: " . $this->getServerUniqueId());
				$this->konsol->debug("Machine ID: " . Utils::getMachineUniqueId());
			}
			
			$this->network = new Network($this);
			$this->network->setName($this->getMotd());
			
			Timings::init();

			$this->consoleSender = new ConsoleCommandSender();
			$this->cmdMap = new SimpleCommandMap($this);
			
			Registerer::registerAll();
			
			InventoryType::init();
			Block::init();
			Enchantment::init();
			Item::init();
			Biome::init();
			TextWrapper::init();
			MetadataConvertor::init();
			
			$this->craftingMgr = new CraftingManager();
			
			if(Translate::checkTurkish() === "yes"){
				$this->resourceMgr = new ResourcePackManager($this, $this->getDataPath() . "doku_paketleri" . DIRECTORY_SEPARATOR);
				$this->behaviorMgr = new BehaviorPackManager($this, $this->getDataPath() . "behavior_paketleri" . DIRECTORY_SEPARATOR);
			}else{
				$this->resourceMgr = new ResourcePackManager($this, $this->getDataPath() . "resource_packs" . DIRECTORY_SEPARATOR);
				$this->behaviorMgr = new BehaviorPackManager($this, $this->getDataPath() . "behavior_packs" . DIRECTORY_SEPARATOR);
			}
			
			$this->pluginMgr = new PluginManager($this, $this->cmdMap);
			$this->pluginMgr->subscribeToPermission(Server::BROADCAST_CHANNEL_ADMINISTRATIVE, $this->consoleSender);
			$this->pluginMgr->setUseTimings($this->getProperty("settings.enable-profiling", false));
			$this->profilingTickRate = (float) $this->getProperty("settings.profile-report-trigger", 20);
			$this->pluginMgr->registerInterface(PharPluginLoader::class);
            //$this->pluginMgr->registerInterface(FolderPluginLoader::class);
			$this->pluginMgr->registerInterface(ScriptPluginLoader::class);
			
			//\set_exception_handler([$this, "exceptionHandler"]);
			
			register_shutdown_function([$this, "crashReport"]);

			$this->queryRegenerator = new QueryRegenerateEvent($this, 7);
			$this->pluginMgr->loadPlugins($this->pluginPath);
			$this->enablePlugins(PluginLoadOrder::STARTUP);
			$this->network->registerInterface(new RakLibInterface($this));
			
			LevelProviderManager::addProvider($this, Anvil::class);
			//LevelProviderManager::addProvider($this, PMAnvil::class);
			LevelProviderManager::addProvider($this, McRegion::class);
			
			foreach((array) $this->getProperty("worlds", []) as $name => $worldSetting){
				if($this->loadLevel($name) === false){
					$seed = $this->getProperty("worlds.$name.seed", time());
					if(count($options) > 0){
						$options = [
							"preset" => implode(":", $options),
						];
					}else{
						$options = [];
					}
				}
				
				$this->generateLevel($name, $seed, $options);
			}
			
			if($this->getDefaultLevel() === null){
				$default = $this->getConfigString("level-name", "world");
				if(trim($default) == ""){
					if(Translate::checkTurkish() === "yes"){
						$this->konsol->warning("level-name Boş Olamaz!");
					}else{
						$this->konsol->warning("level-name Cannot be Null, Using Default");
					}
					
					$default = "world";
					$this->setConfigString("level-name", "world");
				}
				
				if($this->loadLevel($default) === false){
					$seed = $this->getConfigInt("level-seed", time());
					$this->generateLevel($default, $seed === 0 ? time() : $seed);
				}
				
				$this->setDefaultLevel($this->getLevelByName($default));
			}
			
			$this->properties->save();
			
			if(!$this->getDefaultLevel() instanceof Level){
				$this->konsol->emergency($this->getLanguage()->translateString("pocketmine.level.defaultError"));
				$this->forceShutdown();
				return false;
			}
		
			/*if($this->netherEnabled){
				if(!$this->loadLevel($this->netherName)){
					$this->generateLevel($this->netherName, time(), Generator::getGenerator("nether"));
				}
				
				$this->netherLevel = $this->getLevelByName($this->netherName);
			}*/
			
			/*if($this->endEnabled){
                if(!$this->loadLevel($this->endName)){
                    $this->generateLevel($this->endName, time(), Generator::getGenerator("ender"));
                }
                
                $this->endName = $this->getLevelByName($this->endName);
            }*/
            
			if($this->getProperty("ticks-per.autosave", 6000) > 0){
				$this->autoSaveTicks = $this->getProperty("ticks-per.autosave", 6000);
			}

			$this->enablePlugins(PluginLoadOrder::POSTWORLD);
			
			/*if($cfgVer > $advVer){
				$this->konsol->notice("darksystem.yml Dosyası Güncellenmeli!");
				$this->konsol->notice("Şimdiki Sürüm: $advVer | Güncel Sürüm: $cfgVer");
			}*/

			$this->run();
		}catch(\Throwable $e){
			$this->exceptionHandler($e);
		}
	}
	
	public function run(){
		DataPacket::initializePackets();
		
		if($this->isUseEncrypt){
			\McpeEncrypter::generateKeyPair($this->serverPrivateKey, $this->serverPublicKey);
		}
		
		if($this->getConfigBoolean("enable-query", true) === true){
			$this->queryHandler = new QueryHandler();
		}

		foreach($this->getIPBans()->getEntries() as $ent){
			$this->network->blockAddress($ent->getName(), -1);
		}
		
		$this->tickCounter = 0;
		
		Effect::init();
		
		switch(strtolower($this->getTag())){
			case "priv":
			case "private":
				if(Translate::checkTurkish() === "yes"){
					$this->konsol->notice("------------------ BILDIRIM ------------------");
					$this->konsol->notice("Şuanda DarkSystem'in GIZLI Bir Sürümünü Kullanıyorsunuz.");
					$this->konsol->notice("------------------ BILDIRIM ------------------");
				}else{
					$this->konsol->notice("------------------ NOTICE ------------------");
					$this->konsol->notice("You're running a PRIVATE version of DarkSystem.");
					$this->konsol->notice("------------------ NOTICE ------------------");
				}
				break;
			case "dev":
			case "developer":
				if(Translate::checkTurkish() === "yes"){
					$this->konsol->notice("--------------------- BILDIRIM ---------------------");
					$this->konsol->notice("Şuanda DarkSystem GELIŞTIRICI'sinin Sürümünü Kullanıyorsunuz.");
					$this->konsol->notice("            BU URUNU KULLANMAYINIZ              ");
					$this->konsol->notice("--------------------- BILDIRIM ---------------------");
				}else{
					$this->konsol->notice("--------------------- NOTICE ---------------------");
					$this->konsol->notice("You're running a DEVELOPER's version of DarkSystem.");
					$this->konsol->notice("            DO NOT use in production              ");
					$this->konsol->notice("--------------------- NOTICE ---------------------");
				}
				break;
			case "exp":
			case "experimental":
				if(Translate::checkTurkish() === "yes"){
					$this->konsol->notice("--------------------- BILDIRIM ---------------------");
					$this->konsol->notice("Şuanda DarkSystem'in DENEYSEL Sürümünü Kullanıyorsunuz.");
					$this->konsol->notice("            BU URUNU KULLANMAYINIZ              ");
					$this->konsol->notice("--------------------- BILDIRIM ---------------------");
				}else{
					$this->konsol->notice("--------------------- NOTICE ---------------------");
					$this->konsol->notice("You're running an EXPERIMENTAL version of DarkSystem.");
					$this->konsol->notice("            DO NOT use in production              ");
					$this->konsol->notice("--------------------- NOTICE ---------------------");
				}
				break;
				default;
				break;
		}
		
		$this->konsol->info($this->getLanguage()->translateString("pocketmine.server.startFinished", [round(microtime(true) - \pocketmine\START_TIME, 3)]));

		$this->packetMgr = new PacketManager($this->getLoader());
		
		$this->tickAverage = [];
		$this->useAverage = [];
		for($i = 0; $i < 1200; $i++){
			$this->tickAverage[] = 20;
			$this->useAverage[] = 0;
		}

		$this->tickProcessor();
		$this->forceShutdown();

		\gc_collect_cycles();
	}
	
	protected function getLogo(){
		$dbotcheck = $this->dbot->check();
		$dbotver = $this->getDarkBotVersion();
		
		$version = $this->getFormattedVersion();
		$this->version = $version;
		$mcpe = $this->getVersion();
		$protocol = ProtocolInfo::CURRENT_PROTOCOL;
		$build = ProtocolInfo::DARKSYSTEM_VERSION;
		$tag = $this->getTag();
		
		$splash = $this->getSplash();
		
		return $this->getThemeManager()->getLogoTheme($dbotcheck, $dbotver, $version, $mcpe, $protocol, $build, $tag, $splash);
	}
	
	protected function getSplash(){
		if(Translate::checkTurkish() === "yes"){
			$messages = [
				"Hızlı!",
				"Kod Düzenleyicisi Tarafından Yapıldı!",
				"MCPE İçin!",
				"DarkBot Tarafından Destekleniyor!",
				"Temiz!",
				"Güvenli!",
				"Lagsız!",
				"Türkiye'de Yapıldı!"
			];
		}else{
			$messages = [
				"Fast!",
				"Made by Code Editor!",
				"For MCPE!",
				"Sponsored by DarkBot!",
				"Clean!",
				"Safe!",
				"No-lag!",
				"Made in Turkey!"
			];
		}
		
		$result = "§" . mt_rand(1, 9) . $messages[array_rand($messages)];
		
		if(strlen($result) > 15){
			return str_repeat(TF::SPACE, 4) . "\n" . $result;
		}
		
		return $result;
	}
	
	public function broadcastMessage($message, $recipients = null){
		if(strpos($message, $this->getDarkBotPrefix())){
			foreach($this->getOnlinePlayers() as $p){
				$p->sendMessage($message);
				return true;
			}
		}
		
		if(!is_array($recipients)){
			return $this->broadcast($message, Server::BROADCAST_CHANNEL_USERS);
		}
		
		foreach($recipients as $r){
			$r->sendMessage($message);
		}

		return count($recipients);
	}
	
	public function broadcastTip($tip, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $r){
			$r->sendTip($tip);
		}

		return count($recipients);
	}
	
	public function broadcastPopup($popup, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $r){
			$r->sendPopup($popup);
		}

		return count($recipients);
	}
	
	public function broadcastTitle($title, $subtitle = "", $fadeIn = -1, $stay = -1, $fadeOut = -1, $recipients = null){
		if(!is_array($recipients)){
			$recipients = [];
			foreach($this->pluginMgr->getPermissionSubscriptions(Server::BROADCAST_CHANNEL_USERS) as $permissible){
				if($permissible instanceof Player && $permissible->hasPermission(Server::BROADCAST_CHANNEL_USERS)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}
		
		foreach($recipients as $r){
			$r->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
		}

		return count($recipients);
	}
	
	public function broadcast($message, $permissions){
		$recipients = [];
		foreach(explode(";", $permissions) as $permission){
			foreach($this->pluginMgr->getPermissionSubscriptions($permission) as $permissible){
				if($permissible instanceof CommandSender && $permissible->hasPermission($permission)){
					$recipients[spl_object_hash($permissible)] = $permissible;
				}
			}
		}

		foreach($recipients as $r){
			$r->sendMessage($message);
		}

		return count($recipients);
	}
	
	public static function broadcastPacket($players, DataPacket $packet){
		foreach($players as $p){
			$p->dataPacket($packet);
		}
		
		if(isset($packet->__encapsulatedPacket)){
			unset($packet->__encapsulatedPacket);
		}
	}
	
	public function batchPackets($players, $packets){
		$targets = [];
		$neededProtocol = [];
		$neededSubClientsId = [];
		foreach($players as $p){
			$protocol = $p->getPlayerProtocol();
			$subClientId = $p->getSubClientId();
			$playerIdentifier = $p->getIdentifier();
			if($subClientId > 0 && ($parent = $p->getParent()) !== null){
				$playerIdentifier = $parent->getIdentifier();
			}
			$targets[$playerIdentifier] = [$playerIdentifier, $protocol];
			$neededProtocol[$protocol] = $protocol;
			$neededSubClientsId[$subClientId] = $subClientId;
		}
		$protocolsCount = count($neededProtocol);
		$newPackets = [];
		foreach($packets as $p){
			foreach($neededProtocol as $protocol){
				if($p instanceof DataPacket){
					if($protocol >= ProtocolInfo::PROTOCOL_120){
						foreach($neededSubClientsId as $subClientId){
							$p->senderSubClientID = $subClientId;
							$p->encode($protocol);
							$newPackets[$protocol][] = $p->buffer;
						}
					}else{
						if(!$p->isEncoded || $protocolsCount > 1){
							$p->senderSubClientID = 0;
							$p->encode($protocol);
						}
						$newPackets[$protocol][] = $p->buffer;
					}
				}elseif($protocolsCount == 1){
					$newPackets[$protocol][] = $p;
				}
			}
		}
		
		$data = [];
		$data["packets"] = $newPackets;
		$data["targets"] = $targets;
		$data["networkCompressionLevel"] = $this->networkCompressionLevel;
		$data["isBatch"] = true;
		
		$this->packetMgr->pushMainToThreadPacket(serialize($data));
	}
	
	/*public function batchPackets($players, $packets){
		$targets = [];
		$neededProtocol = [];
		foreach($players as $p){
			$targets[] = array($p->getIdentifier(), $p->getPlayerProtocol());
			$neededProtocol[$p->getPlayerProtocol()] = $p->getPlayerProtocol();
		}
		
		$newPackets = [];
		foreach($packets as $p){
			foreach($neededProtocol as $protocol){
				if($p instanceof DataPacket){
					if(!$p->isEncoded || count($neededProtocol) > 1){
						$p->encode($protocol);
					}
					
					$newPackets[$protocol][] = $p->buffer;
				}elseif(count($neededProtocol) == 1){
					$newPackets[$protocol][] = $p;
				}
			}
		}
		
		$data = [];
		$data["packets"] = $newPackets;
		$data["targets"] = $targets;
		$data["networkCompressionLevel"] = $this->networkCompressionLevel;
		$data["isBatch"] = true;
		
		$this->packetMgr->pushMainToThreadPacket(serialize($data));
	}*/
	
	public function enablePlugins($type){
		foreach($this->pluginMgr->getPlugins() as $pl){
			if(!$pl->isEnabled() && $pl->getDescription()->getOrder() === $type){
				$this->enablePlugin($pl);
			}
		}
		if($type === PluginLoadOrder::POSTWORLD){
			$this->cmdMap->registerServerAliases();
			DefaultPermissions::registerCorePermissions();
		}
	}
	
	public function enablePlugin(Plugin $plugin){
		$this->pluginMgr->enablePlugin($plugin);
	}
	
	public function loadPlugin(Plugin $plugin){
		$this->enablePlugin($plugin);
	}

	public function disablePlugins(){
		$this->pluginMgr->disablePlugins();
	}

	public function checkConsole(){
		if(($line = $this->cmdReader->getLine()) !== null){
			$this->pluginMgr->callEvent($ev = new ServerCommandEvent($this->consoleSender, $line));
			if(!$ev->isCancelled()){
				$this->dispatchCommand($ev->getSender(), $ev->getCommand());
			}
		}
	}
	
	public function dispatchCommand(CommandSender $sender, $commandLine){
		if(!($sender instanceof CommandSender)){
			throw new ServerException("CommandSender Geçerli Değil!");
		}
		if($this->cmdMap->dispatch($sender, $commandLine)){
			return true;
		}
		if($sender instanceof Player){
			$message = $this->getSoftConfig("messages.unknown-command", "Unknown command!");
			if(is_string($message) && strlen($message) > 0){
				$sender->sendMessage(TF::RED . $message);
			}
		}else{
			$sender->sendMessage(TF::RED . "Unknown command!");
		}
		return false;
	}

	public function reload(){
		foreach($this->levels as $l){
			$l->save();
		}

		$this->pluginMgr->disablePlugins();
		$this->pluginMgr->clearPlugins();
		$this->cmdMap->clearCommands();
		
		if(Translate::checkTurkish() === "yes"){
			$this->konsol->info("Ayarlar Yeniden Yükleniyor...");
		}else{
			$this->konsol->info("Reloading Properties...");
		}
		
		$this->properties->reload();
		$this->advancedConfig->reload();
		$this->loadAdvancedConfig();
		$this->maxPlayers = $this->getConfigInt("max-players", 25);
		
		$this->banByName->load();
		$this->banByIP->load();
		$this->banByCID->load();
		$this->reloadWhitelist();
		$this->operators->reload();
		
		foreach($this->getIPBans()->getEntries() as $ent){
			$this->blockAddress($ent->getName(), -1);
		}

		$this->pluginMgr->registerInterface(PharPluginLoader::class);
		$this->pluginMgr->loadPlugins($this->pluginPath);
		$this->enablePlugins(PluginLoadOrder::STARTUP);
		$this->enablePlugins(PluginLoadOrder::POSTWORLD);
		TimingsHandler::reload();
	}
	
	public function shutdown($msg = ""){
		if($msg !== ""){
			$this->propertyCache["settings.shutdown-message"] = $msg;
		}
		
		$this->isRunning = false;
		
		\gc_collect_cycles();
	}
	
	public function forceShutdown(){
		if($this->hasStopped){
			return false;
		}
		
		try{
			$this->hasStopped = true;
			foreach($this->players as $p){
				$p->close($this->getProperty("settings.shutdown-message", "Sunucu Durduruldu"));
			}
			
			foreach($this->network->getInterfaces() as $int){
				$int->shutdown();
				$this->network->unregisterInterface($int);
			}
			
			$this->shutdown();
			$this->dbot->shutdown();
			
			//if(count($this->pluginMgr->getPlugins()) > 0){
				if(Translate::checkTurkish() === "yes"){
					$this->konsol->info("§cEklentiler Devre Dışı Bırakılıyor...");
				}else{
					$this->konsol->info("§cDisabling Plugins...");
				}
			//}
			
			if($this->rcon instanceof RCON){
				$this->rcon->stop();
			}
			
			$this->pluginMgr->disablePlugins();
			
			foreach($this->levels as $l){
				$l->save();
				$this->unloadLevel($l, true, true);
			}
			
			HandlerList::unregisterAll();
			$this->scheduler->cancelAllTasks();
			$this->scheduler->mainThreadHeartbeat(PHP_INT_MAX);
			$this->properties->save();
			$this->cmdReader->shutdown();
			$this->cmdReader->notify();
			
			\gc_collect_cycles();
		}catch(\Exception $e){
			if(Translate::checkTurkish() === "yes"){
				$this->konsol->emergency("Sunucu Çöktü, Tüm Görevler Durduruluyor!");
			}else{
				$this->konsol->emergency("Server Crashed, Stopping All Threads...");
			}
			
			@kill(getmypid());
			exit(1);
		}
	}
	
	public function handleSignal($signo){
		if($signo === SIGTERM || $signo === SIGINT || $signo === SIGHUP){
			$this->shutdown();
			return false;
		}
	}
	
	public function exceptionHandler(\Throwable $e, $trace = null){
		if($e === null){
			return false;
		}

		global $lastError;
		
		if($trace === null){
			$trace = $e->getTrace();
		}
		
		$errstr = $e->getMessage();
		$errfile = $e->getFile();
		$errno = $e->getCode();
		$errline = $e->getLine();

		$type = ($errno === E_ERROR || $errno === E_USER_ERROR) ? \LogLevel::ERROR : (($errno === E_USER_WARNING || $errno === E_WARNING) ? \LogLevel::WARNING : \LogLevel::NOTICE);
		if(($pos = strpos($errstr, "\n")) !== false){
			$errstr = substr($errstr, 0, $pos);
		}

		$errfile = cleanPath($errfile);

		if($this->konsol instanceof MainLogger){
			$this->konsol->logException($e, $trace);
		}

		$lastError = [
			"type" => $type,
			"message" => $errstr,
			"fullFile" => $e->getFile(),
			"file" => $errfile,
			"line" => $errline,
			"trace" => @getTrace(1, $trace)
		];

		global $lastExceptionError, $lastError;
		$lastExceptionError = $lastError;
		$this->crashReport();
	}

	public function crashReport(){
		if($this->isRunning === false){
			return false;
		}
		
		$this->isRunning = false;
		$this->hasStopped = false;

		ini_set("error_reporting", 0);
		ini_set("memory_limit", -1);
		
		$this->konsol->emergency($this->getLanguage()->translateString("pocketmine.crash.create"));
		
		try{
		    $report = new CrashReport($this);
		}catch(\Exception $e){
			$this->konsol->critical($this->getLanguage()->translateString("pocketmine.crash.error", $e->getMessage()));
			return false;
		}

		$this->konsol->emergency($this->getLanguage()->translateString("pocketmine.crash.submit", [$report->getPath()]));
		
		$this->saveEverything();
		
		//$this->shutdown();
		$this->forceShutdown();
		@kill(getmypid());
		exit(1);
	}
	
	private function tickProcessor(){
		$this->nextTick = microtime(true);
		while($this->isRunning){
			$this->tick();
			$next = $this->nextTick - 0.0001;
			if($next > microtime(true)){
				try{
					@time_sleep_until($next);
				}catch(\Throwable $e){
				}
			}
		}
	}

	public function addOnlinePlayer(Player $player){
		$this->playerList[$player->getRawUniqueId()] = $player;
	}

	public function removeOnlinePlayer(Player $player){
		if(isset($this->playerList[$player->getRawUniqueId()])){
			unset($this->playerList[$player->getRawUniqueId()]);
			$pk = new PlayerListPacket();
			$pk->type = PlayerListPacket::TYPE_REMOVE;
			$pk->entries[] = [$player->getUniqueId()];
			Server::broadcastPacket($this->playerList, $pk);
		}
	}
	
	public function updatePlayerListData(UUID $uuid, $entityId, $name, $skinName, /*$skinId, */$skinData, $skinGeometryName, /*$skinGeometryId, */$skinGeometryData, $capeData, $xuid, array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_ADD;
		$pk->entries[] = [$uuid, $entityId, $name, $skinName, /*$skinId, */$skinData, $skinGeometryName, /*$skinGeometryId, */$skinGeometryData, $capeData, $xuid];
		$readyPackets = [];
		foreach($players === null ? $this->playerList : $players as $p){
			$protocol = $p->getPlayerProtocol();
			if(!isset($readyPackets[$protocol])){
				$pk->encode($protocol, $p->getSubClientId());
				//$pk->encode($protocol);
				$batch = new BatchPacket();
				$batch->payload = zlib_encode(Binary::writeVarInt(strlen($pk->getBuffer())) . $pk->getBuffer(), ZLIB_ENCODING_DEFLATE, 7);
				$readyPackets[$protocol] = $batch;
			}
			$p->dataPacket($readyPackets[$protocol]);
		}
	}

	public function removePlayerListData(UUID $uuid, array $players = null){
		$pk = new PlayerListPacket();
		$pk->type = PlayerListPacket::TYPE_REMOVE;
		$pk->entries[] = [$uuid];
		foreach($players === null ? $this->playerList : $players as $p){
			$p->dataPacket($pk);
		}
	}
	
	public function sendRecipeList(Player $p){
		if(!isset($this->craftList[$p->getPlayerProtocol()])){
			$pk = new CraftingDataPacket();
			$pk->cleanRecipes = true;
			foreach($this->getCraftingManager()->getRecipes() as $r){
				if($r instanceof ShapedRecipe){
					$pk->addShapedRecipe($r);
				}elseif($r instanceof ShapelessRecipe){
					$pk->addShapelessRecipe($r);
				}
			}

			foreach($this->getCraftingManager()->getFurnaceRecipes() as $r){
				$pk->addFurnaceRecipe($r);
			}
			
			$pk->encode($p->getPlayerProtocol(), $p->getSubClientId());
			//$pk->encode($p->getPlayerProtocol());
			$pk->isEncoded = true;
			$this->craftList[$p->getPlayerProtocol()] = $pk;
		}
		
		$this->batchPackets([$p], [$this->craftList[$p->getPlayerProtocol()]]);
	}

	public function addPlayer($identifier, Player $player){
		$this->players[$identifier] = $player;
		$this->identifiers[spl_object_hash($player)] = $identifier;
	}
	
	public function saveEverything(){
		if($this->getSavePlayerData()){
			foreach($this->getOnlinePlayers() as $index => $p){
				if($p->isOnline()){
					$p->save();
				}elseif(!$p->isConnected()){
					$this->removePlayer($p);
				}
			}
		}
			
		foreach($this->levels as $l){
			$l->save();
		}
	}
	
	public function doAutoSave(){
		if($this->getAutoSave()){
			$this->saveEverything();
		}
	}

	public function doLevelGC(){
		foreach($this->levels as $l){
			$l->doChunkGarbageCollection();
		}
	}
	
	public function handlePacket($address, $port, $payload){
		try{
			if(strlen($payload) > 2 && substr($payload, 0, 2) === "\xfe\xfd" && $this->queryHandler instanceof QueryHandler){
				$this->queryHandler->handle($address, $port, $payload);
			}
		}catch(\Exception $e){
			if(\pocketmine\DEBUG > 1){
				if($this->konsol instanceof MainLogger){
					$this->konsol->logException($e);
				}
			}
			$this->getNetwork()->blockAddress($address, 600);
		}
	}
	
	public function getSoftConfig($variable, $defaultValue = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		if($this->softConfig->exists($base)){
			$base = $this->softConfig->get($base);
		}else{
			return $defaultValue;
		}

		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) && isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}

		return $base;
	}
	
	public function getAdvancedProperty($variable, $defaultValue = null, Config $cfg = null){
		$vars = explode(".", $variable);
		$base = array_shift($vars);
		if($cfg == null) $cfg = $this->advancedConfig;
		if($cfg->exists($base)){
			$base = $cfg->get($base);
		}else{
			return $defaultValue;
		}

		while(count($vars) > 0){
			$baseKey = array_shift($vars);
			if(is_array($base) && isset($base[$baseKey])){
				$base = $base[$baseKey];
			}else{
				return $defaultValue;
			}
		}

		return $base;
	}
	
	private function tick(){
		$tickTime = microtime(true);
		$dbotcheck = $this->dbot->check();
		//if(($tickTime - $this->nextTick) < -0.025){
		if($tickTime < $this->nextTick){
			return false;
		}
		++$this->tickCounter;
		$this->checkConsole();
		/*foreach($this->unloadLevelQueue as $levelForUnload){
			$this->unloadLevel($levelForUnload["level"], $levelForUnload["force"], true);
		}*/
		/*if(($this->tickCounter % 200) === 0){
			foreach($this->levels as $l){
				$l->clearCache();
			}
			$this->saveEverything();
		}*/
		if(($this->tickCounter % 1925) === 0){
			foreach($this->levels as $l){
				foreach($l->getEntities() as $e){
					if($e instanceof DroppedItem || $e instanceof Arrow){
						$e->close();
					}
				}
			}
		}
		if($this->autoSave && ++$this->autoSaveTicker >= $this->autoSaveTicks){
			$this->autoSaveTicker = 0;
			$this->doAutoSave();
		}
		$this->unloadLevelQueue = [];
		while(strlen($str = $this->packetMgr->readThreadToMainPacket()) > 0){
			$data = unserialize($str);
			if(isset($this->players[$data["identifier"]])){
				$player = $this->players[$data["identifier"]];
				$player->getInterface()->putReadyPacket($player, $data["buffer"]);
			}
		}
		$this->network->processInterfaces();
		$this->scheduler->mainThreadHeartbeat($this->tickCounter);
		foreach($this->levels as $l){
			$l->doTick($this->tickCounter);
		}
		if(($this->tickCounter & 0b1111) === 0){
			if($this->queryHandler !== null && ($this->tickCounter & 0b111111111) === 0){
				try{
					$this->queryHandler->regenerateInfo();
				}catch(\Exception $e){
					if($this->konsol instanceof MainLogger){
						$this->konsol->logException($e);
					}
				}
			}
		}
		if(($this->tickCounter % 2975) === 0 && $dbotcheck = "§aAktif" && $this->dbotBroadcast){
			if(Translate::checkTurkish() === "yes"){
				switch(mt_rand(1, 5)){
					case 1:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aSunucu Benimle Güvende!");
					break;
					case 2:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aBu Sunucu Güvencem Altındadır!");
					break;
					case 3:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aYakında Oyuna Bende Katılacağım!");
					break;
					case 4:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aBen Sadece Bir Robot Değilim!");
					break;
					case 5:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aGüvenlik Önemlidir!");
					break;
					default;
					break;
				}
			}else{
				switch(mt_rand(1, 5)){
					case 1:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aServer is safe with me!");
					break;
					case 2:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aThis server is in protection of me!");
					break;
					case 3:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aI will join game soon!");
					break;
					case 4:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aI am not just a robot!");
					break;
					case 5:
					$this->broadcastMessage($this->getDarkBotPrefix() . "§aProtection is important!");
					break;
					default;
					break;
				}
			}
		}
		/*if(($this->tickCounter % 6375) === 0){
			$this->clearChat();
			$this->broadcastMessage("§b[DarkSystem] §aSohbet Temizlendi!");
		}*/
		/*if(($this->tickCounter % 1275) === 0){ //16025
			$x = $this->getDefaultLevel()->getSafeSpawn()->getX();
			$y = $this->getDefaultLevel()->getSafeSpawn()->getY();
			$z = $this->getDefaultLevel()->getSafeSpawn()->getZ();
			//$skin;
			$eid = "114514";
			$item = Item::get(0);
			$this->dbot->spawn("DarkBot", $eid, $x, $y + 0.1, $z, $skin, $item);
			$this->broadcastPopup("§aDarkBot Oyuna Katıldı!");
		}*/
		$now = microtime(true);
		array_shift($this->tickAverage);
		$tickDiff = $now - $tickTime;
		$this->tickAverage[] = ($tickDiff <= 0.05) ? 20 : 1 / $tickDiff;
		array_shift($this->useAverage);
		$this->useAverage[] = min(1, $tickDiff * 20);
		if(($this->nextTick - $tickTime) < -1){
			$this->nextTick = $tickTime;
		}
		$this->nextTick += 0.05;
		return true;
	}
	
}
