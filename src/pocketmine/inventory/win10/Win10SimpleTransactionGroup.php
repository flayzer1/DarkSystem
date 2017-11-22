<?php

namespace pocketmine\inventory\win10;

use pocketmine\inventory\SimpleTransactionGroup;
use pocketmine\item\Item;
use pocketmine\inventory\Transaction;

class Win10SimpleTransactionGroup extends SimpleTransactionGroup{
	
	protected function matchItems(array &$needItems, array &$haveItems) {
		foreach ($this->transactions as $ts) {
			$newItem = $ts->getTargetItem();
			if ($newItem->getId() !== Item::AIR) {
				$needItems[] = $newItem;
			}
			$oldItem = $ts->getSourceItem();
			if ($oldItem->getId() !== Item::AIR) {
				$haveItems[] = $oldItem;
			}
		}

		foreach ($needItems as $i => $needItem) {
			foreach ($haveItems as $j => $haveItem) {
				if ($needItem->deepEquals($haveItem)) {
					$amount = min($needItem->getCount(), $haveItem->getCount());
					$needItem->setCount($needItem->getCount() - $amount);
					$haveItem->setCount($haveItem->getCount() - $amount);
					if ($haveItem->getCount() === 0) {
						unset($haveItems[$j]);
					}
					if ($needItem->getCount() === 0) {
						unset($needItems[$i]);
						break;
					}
				}
			}
		}

		return true;
	}
	
	
	public function addTransaction(Transaction $transaction) {
		if (isset($this->transactions[spl_object_hash($transaction)])) {
			return;
		}
		
		$this->transactions[spl_object_hash($transaction)] = $transaction;
		$this->inventories[spl_object_hash($transaction->getInventory())] = $transaction->getInventory();
	}
	
}
