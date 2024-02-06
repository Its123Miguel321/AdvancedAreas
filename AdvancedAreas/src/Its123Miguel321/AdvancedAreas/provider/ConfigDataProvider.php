<?php

namespace Its123Miguel321\AdvancedAreas\provider;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\area\Area;
use Its123Miguel321\AdvancedAreas\utils\{
	Flags,
	Selections
};

use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class ConfigDataProvider extends DataProvider{

	private Config $config;

	public function __construct(
		private AdvancedAreas $plugin,
		bool $yaml = false
	){
		@mkdir($plugin->getDataFolder() . 'data');

		$this->config = new Config($plugin->getDataFolder() . 'data' . DIRECTORY_SEPARATOR . 'area.' . ($yaml ? 'yml' : 'json'), ($yaml ? Config::YAML : Config::JSON), [
			'areas' => []
		]);
	}

	public function create(string $displayName, Selections $selections, int $priority = -100, array $whitelist = [], array $events = [], array $effects = [], array $items = [], bool $itemsForWhitelist = false, bool $effectsForWhitelist = false) : void{
		$identifier = 'Area' . mt_rand(1, 5000) . '-' . date("Y_m_d-H_i_s");

		$this->areas[$identifier] = new Area(
			$identifier, 
			$displayName, 
			$selections->getPosA(), 
			$selections->getPosB(), 
			$selections->getPosA()->getWorld()->getFolderName(), 
			$priority,
			$whitelist,
			$events, 
			$effects, 
			$items,
			$itemsForWhitelist,
			$effectsForWhitelist
		);
	}

	public function delete(Area $area) : void{
		unset($this->areas[$area->getIdentifier()]);
	}

	public function loadAll() : void{
		foreach($this->config->get('areas', []) as $identifier => $data){
			$displayName = $data['displayname'];
			$posA = new Vector3(($posA = $data['posA'])[0], $posA[1], $posA[2]);
			$posB = new Vector3(($posB = $data['posB'])[0], $posB[1], $posB[2]);
			$worldName = $data['world'];

			$this->areas[$identifier] = new Area(
				$identifier,
				$displayName,
				$posA,
				$posB,
				$worldName,
				$data[Flags::FLAG_AREA_PRIORITY] ?? -100,
				$data[Flags::FLAG_AREA_WHITELIST] ?? [],
				$data[Flags::FLAG_AREA_EVENTS] ?? [],
				$data[Flags::FLAG_AREA_EFFECTS] ?? [],
				$data[Flags::FLAG_AREA_BANNED_ITEMS] ?? [],
				$data[Flags::FLAG_WHITELIST_APPLY_ITEMS] ?? false,
				$data[Flags::FLAG_WHITELIST_APPLY_EFFECTS] ?? false
			);
		}
	}

	public function saveAll() : void{
		$areas = [];

		foreach($this->areas as $area){
			$posA = $area->getPositionA();
			$posB = $area->getPositionB();
			$areas[$area->getIdentifier()] = [
				'displayname' => $area->getDisplayName(),
				'posA' => [$posA->getX(), $posA->getY(), $posA->getZ()],
				'posB' => [$posB->getX(), $posB->getY(), $posB->getZ()],
				'world' => $area->getWorld()->getFolderName(),
				Flags::FLAG_AREA_PRIORITY => $area->getPriority(),
				Flags::FLAG_AREA_WHITELIST => $area->getWhitelist(),
				Flags::FLAG_AREA_EVENTS => $area->getEvents(),
				Flags::FLAG_AREA_EFFECTS => $area->getEffects(),
				Flags::FLAG_AREA_BANNED_ITEMS => $area->getItems(),
				Flags::FLAG_WHITELIST_APPLY_ITEMS => $area->getItemsApplyToWhitelist(),
				Flags::FLAG_WHITELIST_APPLY_EFFECTS => $area->getEffectsApplyToWhitelist()
			];
		}

		$this->config->set('areas', $areas);
		$this->config->save();
	}
}