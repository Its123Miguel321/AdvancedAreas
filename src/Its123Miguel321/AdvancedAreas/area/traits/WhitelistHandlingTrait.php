<?php

namespace Its123Miguel321\AdvancedAreas\area\traits;

use pocketmine\player\Player;

trait WhitelistHandlingTrait{

	protected array $whitelist = [];

	public function inWhitelist(Player|string $player) : bool{
		if($player instanceof Player) $player = $player->getName();

		$player = strtolower(str_replace(' ', '_', $player));

		return in_array($player, $this->whitelist);
	}

	public function addToWhitelist(Player|string $player) : self{
		if($player instanceof Player) $player = $player->getName();

		$player = strtolower(str_replace(' ', '_', $player));

		$this->whitelist[] = $player;
		return $this;
	}

	public function setWhitelist(array $whitelist) : self{
		$this->whitelist = $whitelist;
		return $this;
	}

	public function removeFromWhitelist(Player|string $player) : self{
		if($player instanceof Player) $player = $player->getName();

		$player = strtolower(str_replace(' ', '_', $player));

		unset($this->whitelist[array_search($player, $this->whitelist)]);
		return $this;
	}

	public function resetWhitelist() : self{
		$this->whitelist = [];
		return $this;
	}

	public function getWhitelist() : array{
		return $this->whitelist;
	}
}