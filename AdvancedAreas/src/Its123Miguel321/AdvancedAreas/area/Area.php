<?php

namespace Its123Miguel321\AdvancedAreas\area;

use Its123Miguel321\AdvancedAreas\area\traits\{
	EffectsHandlingTrait,
	EventsHandlingTrait,
	ItemsHandlingTrait,
	WhitelistHandlingTrait
};

use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;

class Area{

	use EffectsHandlingTrait;
	use EventsHandlingTrait;
	use ItemsHandlingTrait;
	use WhitelistHandlingTrait;



	public function __construct(
		private string $identifier,
		private string $displayName,
		private Vector3 $posA,
		private Vector3 $posB,
		private string $worldName,
		private int $priority = -100,
		array $whitelist = [],
		array $events = [],
		array $effects = [],
		array $items = [],
		bool $itemsForWhitelist = false,
		bool $effectsForWhitelist = false
	){
		$this->events = $events;
		$this->effects = $effects;
		$this->effectsForWhitelist = $effectsForWhitelist;
		$this->items = $items;
		$this->itemsForWhitelist = $itemsForWhitelist;
		$this->whitelist = $whitelist;
	}

	public function getIdentifier() : string{ return $this->identifier; }

	public function getDisplayName() : string{ return $this->displayName; }

	public function setDisplayName(string $name): void{
		$this->displayName = $name;
	}


	public function getPositionA() : Vector3{ return $this->posA; }


	public function setPosA(Vector3 $pos) : void{
		$this->posA = $pos;
	}


	public function getPositionB() : Vector3{ return $this->posB; }


	public function setPosB(Vector3 $pos) : void{
		$this->posB = $pos;
	}


	public function getWorld() : ?World{
		return Server::getInstance()->getWorldManager()->getWorldByName($this->worldName);;
	}


	public function getPriority() : int{ return $this->priority; }


	public function setPriority(int $priority) : void{
		$this->priority = $priority;
	}
}
