<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\level\format;

use pocketmine\level\Level;
use pocketmine\math\Vector3;

interface LevelProvider{
	
	const ORDER_YZX = 0;
	const ORDER_ZXY = 1;
	
	/**
	 * @param Level  $level
	 * @param string $path
	 */
	public function __construct(Level $level, $path);

	/**
	 * @return string
	 */
	public static function getProviderName();
	
	/**
	 * @return int
	 */
	public static function getProviderOrder();
	
	/**
	 * @return bool
	 */
	public static function usesChunkSection();

	/**
	 * @param int $x
	 * @param int $z
	 *
	 * @return \pocketmine\scheduler\AsyncTask|null
	 */
	public function requestChunkTask($x, $z);

	/** @return string */
	public function getPath();
	
	/**
	 * @param string $path
	 *
	 * @return true
	 */
	public static function isValid($path);
	
	/**
	 * @param string  $path
	 * @param string  $name
	 * @param int     $seed
	 * @param array[] $options
	 */
	public static function generate($path, $name, $seed, array $options = []);

	/**
	 * @param int  $X      absolute Chunk X value
	 * @param int  $Z      absolute Chunk Z value
	 * @param bool $create
	 *
	 * @return FullChunk|Chunk
	 */
	public function getChunk($X, $Z, $create = false);

	/**
	 * @param $Y 0-7
	 *
	 * @return ChunkSection
	 */
	public static function createChunkSection($Y);

	public function saveChunks();

	/**
	 * @param int $X
	 * @param int $Z
	 */
	public function saveChunk($X, $Z);

	public function unloadChunks();

	/**
	 * @param int  $X
	 * @param int  $Z
	 * @param bool $create
	 *
	 * @return bool
	 */
	public function loadChunk($X, $Z, $create = false);

	/**
	 * @param int  $X
	 * @param int  $Z
	 * @param bool $safe
	 *
	 * @return bool
	 */
	public function unloadChunk($X, $Z, $safe = true);

	/**
	 * @param int $X
	 * @param int $Z
	 *
	 * @return bool
	 */
	public function isChunkGenerated($X, $Z);

	/**
	 * @param int $X
	 * @param int $Z
	 *
	 * @return bool
	 */
	public function isChunkPopulated($X, $Z);

	/**
	 * @param int $X
	 * @param int $Z
	 *
	 * @return bool
	 */
	public function isChunkLoaded($X, $Z);

	/**
	 * @param int       $chunkX
	 * @param int       $chunkZ
	 * @param FullChunk $chunk
	 *
	 * @return mixed
	 */
	public function setChunk($chunkX, $chunkZ, FullChunk $chunk);

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return int
	 */
	public function getTime();

	/**
	 * @param int $value
	 */
	public function setTime($value);

	/**
	 * @return int
	 */
	public function getSeed();

	/**
	 * @param int $value
	 */
	public function setSeed($value);

	/**
	 * @return Vector3
	 */
	public function getSpawn();

	/**
	 * @param Vector3 $pos
	 */
	public function setSpawn(Vector3 $pos);

	/**
	 * @return FullChunk|Chunk[]
	 */
	public function getLoadedChunks();

	public function doGarbageCollection();

	/**
	 * @return Level
	 */
	public function getLevel();

	public function close();
	
	public static function getMaxY();
	
	public static function getYMask();

}
