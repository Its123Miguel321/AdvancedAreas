<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;

use pocketmine\command\CommandSender;

abstract class SubCommand{
	
	public function __construct(
		private AdvancedAreas $plugin, 
		private string $name, 
		private string $description
	){}
	
	abstract public function canUse(CommandSender $sender) : bool;
	
	abstract public function execute(CommandSender $sender, array $args) : bool;

	final public function getPlugin() : AdvancedAreas{
		return $this->plugin;
	}

	final public function getName() : string{
		return $this->name;
	}

	final public function getDescription() : string{
		return $this->description;
	}
}