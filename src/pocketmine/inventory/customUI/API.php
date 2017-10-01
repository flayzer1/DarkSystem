<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

namespace pocketmine\inventory\customUI;

use pocketmine\OfflinePlayer;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Utils;
use pocketmine\network\protocol\v120\ModalFormRequestPacket;

class API{
	
	/** @var array(CustomUI[]) */
	private static $UIs = [];

	/**
	 * @param Plugin $plugin
	 * @param CustomUI $ui
	 * @return int id
	 */
	public static function addUI($plugin, &$ui){
		$ui->setID(count(API::$UIs[$plugin->getName()] ?? []));
		$id = $ui->getID();
		API::$UIs[$plugin->getName()][$id] = $ui;
		$ui = null;
		return $id;
	}

	public static function resetUIs($plugin){
		API::$UIs[$plugin->getName()] = [];
	}

	/**
	 * @return array(CustomUI[])
	 */
	public static function getAllUIs(){
		return API::$UIs;
	}

	/**
	 * @param Plugin $plugin
	 * @return CustomUI[]
	 */
	public static function getPluginUIs($plugin){
		return API::$UIs[$plugin->getName()];
	}

	/**
	 * @param Plugin $plugin
	 * @param int $id
	 * @return CustomUI
	 */
	public static function getPluginUI($plugin, $id){
		return API::$UIs[$plugin->getName()][$id];
	}

	public static function handle($plugin, $id, $response, $player){
		$ui = API::getPluginUIs($plugin)[$id];
		var_dump($ui);
		return $ui->handle($response, $player) ?? "";
	}

	public static function showUI($ui, $player){
		$pk = new ModalFormRequestPacket();
		$pk->formData = json_encode($ui);
		$pk->formId = Utils::javaStringHash($ui->getTitle());
		$player->dataPacket($pk);
	}

	public static function showUIbyID($plugin, $id, $player){
		$ui = API::getPluginUIs($plugin)[$id];
		$pk = new ModalFormRequestPacket();
		$pk->formData = json_encode($ui);
		$pk->formId = $id;
		$player->dataPacket($pk);
	}

	/**
	 * @param Player[]|OfflinePlayer[] $players
	 * @return array
	 */
	public static function playerArrayToNameArray($players){
		$return = [];
		foreach($players as $player){
			$return[] = $player->getName();
		}
		
		sort($return, SORT_NATURAL);
		return $return;
	}
}
