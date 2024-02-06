<?php

declare(strict_types=1);

namespace Its123Miguel321\AdvancedAreas;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\subcommands\{
	CreateCommand,
	DeleteCommand,
	EditCommand,
	HelpCommand,
	SubCommand,
	WandCommand
};

use pocketmine\command\{
	Command,
	CommandSender
};
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;

class Commands extends Command implements PluginOwned{

	/** @var SubCommand[] $subcommands */
	private static $subcommands = [];



	public function __construct(
		private AdvancedAreas $plugin
	){
		parent::__construct('advancedareas');
		$this->setDescription('AdvancedAreas Commands');
		$this->setUsage(TF::GRAY . 'Unknown command, try ' . TF::RED . '/advancedareas help' . TF::GRAY . ' for a list of all commands!');
		$this->setAliases(['areas', 'aa']);
		$this->setPermission('advancedareas.admin');
		
		self::loadSubCommand(new CreateCommand($plugin, 'create', 'Creates a new area'));
		self::loadSubCommand(new DeleteCommand($plugin, 'delete', 'Deletes an area'));
		self::loadSubCommand(new EditCommand($plugin, 'edit', 'Edits an area'));
		self::loadSubCommand(new HelpCommand($plugin, 'help', 'Shows help page'));
		self::loadSubCommand(new WandCommand($plugin, 'wand', 'Gets AdvancedAreas wand'));
	}

	public static function loadSubCommand(SubCommand $command) : void{
		self::$subcommands[$command->getName()] = $command;
	}

	public static function unloadSubCommand($command) : void{
		if($command instanceof SubCommand) $command = $command->getName();
		if(!(isset(self::$subcommands[$command]))) return;
		
		unset(self::$subcommands[$command]);
	}

	public static function getSubCommand(string $command) : ?SubCommand{
		if(!(isset(self::$subcommands[$command]))) return null;
		
		return self::$subcommands[$command];
	}

	/** @return SubCommand[] */
	public static function getSubCommands() : array{
		return self::$subcommands;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!(isset($args[0]))){
			$sender->sendMessage($this->getUsage());
			return false;
		}

		$command = strtolower(array_shift($args));

		if(isset(self::$subcommands[$command])){
			$command = self::$subcommands[$command];
		}else{
			$sender->sendMessage($this->getUsage());
			return false;
		}

		if(!($command->canUse($sender))){
			$sender->sendMessage(TF::RED . 'You can not use this command!');
			return false;
		}

		return $command->execute($sender, $args);
	}

	public function getOwningPlugin() : AdvancedAreas{
		return $this->plugin;
	}
}