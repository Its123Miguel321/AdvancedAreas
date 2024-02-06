<?php

namespace Its123Miguel321\AdvancedAreas\provider;

use Its123Miguel321\AdvancedAreas\area\Area;
use Its123Miguel321\AdvancedAreas\utils\Selections;

abstract class DataProvider{

	/** @var Area[] $areas */
	protected array $areas = [];



	abstract public function create(string $displayName, Selections $selections, int $priority = -100, array $whitelist = [], array $events = [], array $effects = [], array $items = [], bool $itemsForWhitelist = false, bool $effectsForWhitelist = false) : void;

	abstract public function delete(Area $area) : void;

	abstract public function loadAll();

	public function exists(string $displayName) : bool{
		return !is_null($this->get($displayName));
	}

	public function get(string $displayName) : ?Area{
		foreach($this->areas as $area){
			if(strtolower($area->getDisplayName()) === strtolower($displayName)) return $area;
		}

		return null;
	}

	/** @return Area[] */
	public function getAll() : array{
		return $this->areas;
	}

	public function save(Area $area) : void{
		
	}

	public function saveAll() : void{
		foreach($this->areas as $area) $this->save($area); 
	}
}