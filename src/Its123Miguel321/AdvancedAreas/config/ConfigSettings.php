<?php

namespace Its123Miguel321\AdvancedAreas\config;

use Its123Miguel321\AdvancedAreas\utils\Flags;

use pocketmine\item\{
	Item,
	StringToItemParser
};
use pocketmine\utils\Config;

class ConfigSettings{

	public const DEFAULT_PROVIDER = 'json';
	public const DEFAULT_WAND_ITEM = 'feather';
	public const DEFAULT_AREA_NAME_TYPE = 'popup';
	public const DEFAULT_AREA_EVENTS = [
		Flags::FLAG_EVENT_BLOCK_BREAK => true,
		Flags::FLAG_EVENT_BLOCK_DECAY => true,
		Flags::FLAG_EVENT_BLOCK_GROW => true,
		Flags::FLAG_EVENT_BLOCK_PLACE => true,
		Flags::FLAG_EVENT_BLOCK_UPDATE => true,
		Flags::FLAG_EVENT_ENTITY_DAMAGE => true,
		Flags::FLAG_EVENT_ENTITY_EXPLOSION => true,
		Flags::FLAG_EVENT_ENTITY_REGAIN_HEALTH => true,
		Flags::FLAG_EVENT_ENTITY_TELEPORT => true,
		Flags::FLAG_EVENT_PLAYER_CRAFT => true,
		Flags::FLAG_EVENT_PLAYER_FLIGHT => true,
		Flags::FLAG_EVENT_PLAYER_INTERACT => true,
		Flags::FLAG_EVENT_PLAYER_ITEM_DROP => true,
		Flags::FLAG_EVENT_PLAYER_ITEM_PICKUP => true,
		Flags::FLAG_EVENT_PLAYER_SPRINT => true,
		Flags::FLAG_EVENT_PLAYER_HUNGER => true
	];
	public const DEFAULT_AREA_PRIORITY = 5;
	public const UNKNOWN_CONFIG_VERSION = -1;

	

	public function __construct(
		private Config $config
	){}

	public function getConfigVersion() : float{
		return $this->config->get('config-version', self::UNKNOWN_CONFIG_VERSION);
	}

	public function getDataProvider() : string{
		return strtolower($this->config->get('data-provider', self::DEFAULT_PROVIDER));
	}

	public function getWandItem() : ?Item{
		return StringToItemParser::getInstance()->parse($this->config->get('wand-item', self::DEFAULT_WAND_ITEM));
	}

	public function getAreaNameType() : string{
		return $this->config->get('area-name-type', self::DEFAULT_AREA_NAME_TYPE);
	}

	public function showAreaNames() : bool{
		return (bool)$this->config->get('show-area-name', true);
	}

	public function getDefaultAreaSettings() : array{
		$default_settings = [
			Flags::FLAG_AREA_PRIORITY => self::DEFAULT_AREA_PRIORITY,
			Flags::FLAG_AREA_BANNED_ITEMS => [],
			Flags::FLAG_AREA_EVENTS => self::DEFAULT_AREA_EVENTS,
			Flags::FLAG_AREA_EFFECTS => [],
			Flags::FLAG_AREA_WHITELIST => []
		];
		
		return $this->config->get('default-area-settings', $default_settings);
	}
}