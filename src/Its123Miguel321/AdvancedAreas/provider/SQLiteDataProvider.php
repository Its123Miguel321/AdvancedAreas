<?php

namespace Its123Miguel321\AdvancedAreas\provider;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\area\Area;
use Its123Miguel321\AdvancedAreas\utils\{
	Flags,
	Selections
};

use pocketmine\math\Vector3;

use Ramsey\Uuid\Uuid;

use SQLite3;
use SQLite3Result;

class SQLiteDataProvider extends DataProvider{

	private SQLite3 $database;

	public function __construct(
		private AdvancedAreas $plugin
	){
		@mkdir($plugin->getDataFolder() . 'data');

		$this->database = new SQLite3($plugin->getDataFolder() . 'data' . DIRECTORY_SEPARATOR . 'areas.db');
		$this->database->exec(
			"CREATE TABLE IF NOT EXISTS areas(
				identifier TEXT PRIMARY KEY, 
				displayname TEXT, 
				posA JSON, 
				posB JSON, 
				world TEXT, 
				priority INT, 
				whitelist JSON,
				events JSON,
				effects JSON,
				items JSON,
				effectsToWhitelist BOOL,
				itemsToWhitelist BOOL
			);"
		);
	}

	public function create(string $displayName, Selections $selections, int $priority = -100, array $whitelist = [], array $events = [], array $effects = [], array $items = [], bool $itemsForWhitelist = false, bool $effectsForWhitelist = false) : void{
		$identifier = Uuid::uuid4()->getBytes();

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

		$stmt = $this->database->prepare("DELETE FROM areas WHERE displayname = :name");
		$stmt->bindValue(':name', $area->getDisplayName());
		$stmt->reset();

		$result = $stmt->execute();

		if(!($result instanceof SQLite3Result)) return;

		$result->finalize();
	}

	public function loadAll() : void{
		$stmt = $this->database->prepare("SELECT * FROM areas");
		$result = $stmt->execute();

		if(!($result instanceof SQLite3Result)) return;

		while($result instanceof SQLite3Result && !is_bool($data = $result->fetchArray(SQLITE3_ASSOC))){
			$identifier = $data['identifier'] ?? Uuid::uuid4()->getBytes();
			
			if(
				!isset($data['displayname']) || 
				!isset($data['posA']) || 
				!isset($data['posB']) || 
				!isset($data['world'])
			) continue;

			$displayName = $data['displayname'];
			$posA = new Vector3(($posA = unserialize($data['posA']))[0], $posA[1], $posA[2]);
			$posB = new Vector3(($posB = unserialize($data['posB']))[0], $posB[1], $posB[2]);
			$worldName = $data['world'];

			$this->areas[$identifier] = new Area(
				$identifier,
				$displayName,
				$posA,
				$posB,
				$worldName,
				$data['priority'] ?? -100,
				(isset($data['whitelist']) ? unserialize($data['whitelist']) : []),
				(isset($data['events']) ? unserialize($data['events']) : []),
				(isset($data['effects']) ? unserialize($data['effects']) : []),
				(isset($data['items']) ? unserialize($data['items']) : []),
				$data[Flags::FLAG_WHITELIST_APPLY_ITEMS] ?? false,
				$data[Flags::FLAG_WHITELIST_APPLY_EFFECTS] ?? false
			);
		}
	}

	public function save(Area $area) : void{
		$stmt = $this->database->prepare(
			"INSERT OR REPLACE INTO areas(
				identifier, 
				displayname, 
				posA, 
				posB, 
				world, 
				priority, 
				whitelist,
				events,
				effects,
				items,
				effectsToWhitelist,
				itemsToWhitelist
			) VALUES (
				:identifier, 
				:displayname, 
				:posA, 
				:posB, 
				:world, 
				:priority, 
				:whitelist,
				:events,
				:effects,
				:items,
				:effectsToWhitelist,
				:itemsToWhitelist
			);"
		);
		$stmt->bindValue(':identifier', $area->getIdentifier());
		$stmt->bindValue(':displayname', $area->getDisplayName());

		$posA = $area->getPositionA();
		$stmt->bindValue(':posA', serialize([$posA->getX(), $posA->getY(), $posA->getZ()]));
		
		$posB = $area->getPositionB();
		$stmt->bindValue(':posB', serialize([$posB->getX(), $posB->getY(), $posB->getZ()]));

		$stmt->bindValue(':world', $area->getWorld()->getFolderName());
		$stmt->bindValue(':priority', $area->getPriority());
		$stmt->bindValue(':whitelist', serialize($area->getWhitelist()));
		$stmt->bindValue(':events', serialize($area->getEvents()));
		$stmt->bindValue(':effects', serialize($area->getEffects()));
		$stmt->bindValue(':items', serialize($area->getItems()));
		$stmt->bindValue(':effectsToWhitelist', $area->getEffectsApplyToWhitelist());
		$stmt->bindValue(':itemsToWhitelist', $area->getItemsApplyToWhitelist());
		$stmt->reset();

		$result = $stmt->execute();

		if(!($result instanceof SQLite3Result)) return;

		$result->finalize();
	}
}