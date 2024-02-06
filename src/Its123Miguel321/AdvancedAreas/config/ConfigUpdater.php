<?php

namespace Its123Miguel321\AdvancedAreas\config;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;

class ConfigUpdater{

	private const LATEST_VERSION = 1.0;



	public function __construct(
		private AdvancedAreas $plugin
	){}

	public function needUpdate() : bool{
		return $this->plugin->getSettings()->getConfigVersion() !== self::LATEST_VERSION;
	}

	public function update() : bool{
		$date = date("Y_m_d-H_i_s");

		if(!(rename($this->plugin->getConfig()->getPath(), $this->plugin->getDataFolder() . 'config-' . $date . '.yml'))) return false;

		$this->plugin->reloadConfig();
		return true;
	}
}