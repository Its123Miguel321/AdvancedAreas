<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\Commands;
use Its123Miguel321\AdvancedAreas\subcommands\SubCommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class HelpCommand extends SubCommand{

	public function canUse(CommandSender $sender) : bool{
		return ($sender instanceof Player) && $sender->hasPermission('advancedareas.admin');
	}

	public function execute(CommandSender $sender, array $args) : bool{
		$help = TF::GRAY . str_repeat(' - ', 3) . TF::BOLD . TF::DARK_GREEN . 'Help Page' . TF::RESET . TF::GRAY . str_repeat(' -', 3) . "\n\n";
		
		foreach(Commands::getSubCommands() as $command){
			$help .= TF::DARK_GREEN . ucfirst($command->getName()) . TF::GRAY .  " - " . $command->getDescription() . TF::RESET ."\n";
		}
		
		$help .= TF::GRAY . str_repeat(' - ', 10);
		
		$sender->sendMessage($help);
		return true;
	}
}