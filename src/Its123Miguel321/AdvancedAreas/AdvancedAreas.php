<?php

namespace Its123Miguel321\AdvancedAreas;

use Its123Miguel321\AdvancedAreas\config\{
	ConfigSettings,
	ConfigUpdater
};
use Its123Miguel321\AdvancedAreas\provider\{
	ConfigDataProvider,
	DataProvider,
	SQLiteDataProvider
};

use muqsit\invmenu\InvMenuHandler;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\{
	Enchantment,
	ItemFlags
};
use pocketmine\plugin\PluginBase;

class AdvancedAreas extends PluginBase{

	private static self $instance;

	private ConfigSettings $settings;
	private DataProvider $provider;


	public function onLoad() : void{
		self::$instance = $this;

		@mkdir($this->getDataFolder());

		$this->settings = new ConfigSettings($this->getConfig());
		
		$updater = new ConfigUpdater($this);

		if($updater->needUpdate() && !$updater->update()){
			$this->getLogger()->critical('Could not create the new config file!');
		}
	}

	public function onEnable() : void{
		if(!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);

		$this->setProvider($this->settings->getDataProvider());

		$this->provider->loadAll();

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register($this->getName(), new Commands($this));

		EnchantmentIdMap::getInstance()->register(750, new Enchantment('advancedareas', 1, ItemFlags::ALL, ItemFlags::NONE, 1));
	}

	public function onDisable() : void{
		$this->provider->saveAll();
	}

	public static function getInstance() : self{
		return self::$instance;
	}

	public function getSettings() : ConfigSettings{
		return $this->settings;
	}

	public function setProvider(string $provider) : void{
		switch(strtolower($provider)){
			case 'yaml':
				$this->provider = new ConfigDataProvider($this, true);
				break;

			case 'sqlite':
			case 'sqlite3':
				$this->provider = new SQLiteDataProvider($this);
				break;

			case 'json':
			default:
				$this->provider = new ConfigDataProvider($this);
		}
	}

	public function getProvider() : DataProvider{
		return $this->provider;
	}
}
