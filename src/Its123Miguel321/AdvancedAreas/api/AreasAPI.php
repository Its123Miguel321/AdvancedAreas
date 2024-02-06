<?php

declare(strict_types=1);

namespace Its123Miguel321\AdvancedAreas\api;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\area\Area;
use Its123Miguel321\AdvancedAreas\utils\Selections;

use pocketmine\player\Player;
use pocketmine\world\Position;

class AreasAPI{

	/** @var Selection[] $selections */
	private static array $selections = [];



	public static function getSelections(Player|string $player) : ?Selections{
		if($player instanceof Player) $player = $player->getName();

		return self::$selections[$player] ?? null;
	}

	public static function addSelections(Player|string $player, Selections $selections) : void{
		if($player instanceof Player) $player = $player->getName();

		self::$selections[$player] = $selections;
	}

	public static function removeSelections(Player|string $player) : void{
		if($player instanceof Player) $player = $player->getName();

		unset(self::$selections[$player]);
	}

	public static function isInside(Position $pos) : bool{
		foreach(AdvancedAreas::getInstance()->getProvider()->getAll() as $area){
			if(strtolower($pos->getWorld()->getFolderName()) !== strtolower($area->getWorld()->getFolderName())) continue;

			$minX = min($area->getPositionA()->getX(), $area->getPositionB()->getX());
			$maxX = max($area->getPositionA()->getX(), $area->getPositionB()->getX());
			$minY = min($area->getPositionA()->getY(), $area->getPositionB()->getY());
			$maxY = max($area->getPositionA()->getY(), $area->getPositionB()->getY());
			$minZ = min($area->getPositionA()->getZ(), $area->getPositionB()->getZ());
			$maxZ = max($area->getPositionA()->getZ(), $area->getPositionB()->getZ());

			if($pos->getFloorX() >= $minX && $pos->getFloorX() <= $maxX){
				if($pos->getFloorY() >= $minY && $pos->getFloorY() <= $maxY){
					if($pos->getFloorZ() >= $minZ && $pos->getFloorZ() <= $maxZ){
						return true;
					}
				}
			}
		}

		return false;
	}

	public static function inAreaByName(Position $pos, string $name) : bool{
		foreach(self::getAreasIn($pos) as $area){
			if(strtolower($area->getDisplayName()) === strtolower($name)) return true;
		}

		return false;
	}

	public static function inSameArea(Position $current, Area $previous) : bool{
		foreach(self::getAreasIn($current) as $area){
			if($area->getIdentifier() === $previous->getIdentifier()) return true;
		}

		return false;
	}

	public static function inSameAreas(Position $current, Position $previous, bool $checkSpecific = true) : bool{
		$sameAreas = false;
		$currentAreas = array_keys(self::getAreasIn($current));
		$previousAreas = array_keys(self::getAreasIn($previous));

		if($checkSpecific){
			foreach($currentAreas as $currentArea){
				if(in_array($currentArea, $previousAreas)){
					$sameAreas = true;
				}else{
					$sameAreas = false;
					break;
				}
			}
		}else{
			$sameAreas = (count($currentAreas) == count($previousAreas));
		}

		return $sameAreas;
	}

	/** @return Area[] */
	public static function getAreasIn(Position $pos) : array{
		$areas = [];

		foreach(AdvancedAreas::getInstance()->getProvider()->getAll() as $area){
			if(strtolower($pos->getWorld()->getFolderName()) !== strtolower($area->getWorld()->getFolderName())) continue;

			$minX = min($area->getPositionA()->getX(), $area->getPositionB()->getX());
			$maxX = max($area->getPositionA()->getX(), $area->getPositionB()->getX());
			$minY = min($area->getPositionA()->getY(), $area->getPositionB()->getY());
			$maxY = max($area->getPositionA()->getY(), $area->getPositionB()->getY());
			$minZ = min($area->getPositionA()->getZ(), $area->getPositionB()->getZ());
			$maxZ = max($area->getPositionA()->getZ(), $area->getPositionB()->getZ());

			if($pos->getFloorX() >= $minX && $pos->getFloorX() <= $maxX){
				if($pos->getFloorY() >= $minY && $pos->getFloorY() <= $maxY){
					if($pos->getFloorZ() >= $minZ && $pos->getFloorZ() <= $maxZ){
						$areas[$area->getIdentifier()] = $area;
					}
				}
			}
		}

		return $areas;
	}
}
