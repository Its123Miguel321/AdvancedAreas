<?php

namespace Its123Miguel321\AdvancedAreas\area\traits;

use pocketmine\item\Item;

trait ItemsHandlingTrait{

	protected array $items = [];
	protected bool $itemsForWhitelist = false;

	public function hasItem(Item|string $item) : bool{
		if($item instanceof Item) $item = $item->getVanillaName();

		$item = strtolower(str_replace(' ', '_', $item));

		return in_array($item, $this->items);
	}

	public function addItem(Item|string $item) : self{
		if($item instanceof Item) $item = $item->getVanillaName();

		$item = strtolower(str_replace(' ', '_', $item));

		$this->items[] = $item;
		return $this;
	}

	public function setItems(array $items) : self{
		$this->resetItems();

		foreach($items as $item){
			$this->addItem($item);
		}
		return $this;
	}

	public function removeItem(Item|string $item) : self{
		if($item instanceof Item) $item = $item->getVanillaName();

		$item = strtolower(str_replace(' ', '_', $item));

		unset($this->items[array_search($item, $this->items)]);
		return $this;
	}

	public function resetItems() : self{
		$this->items = [];
		return $this;
	}

	public function getItems() : array{
		return $this->items;
	}

	public function getItemsApplyToWhitelist() : bool{
		return $this->itemsForWhitelist;
	}

	public function setItemsApplyToWhitelist(bool $apply) : void{
		$this->itemsForWhitelist = $apply;
	}
}