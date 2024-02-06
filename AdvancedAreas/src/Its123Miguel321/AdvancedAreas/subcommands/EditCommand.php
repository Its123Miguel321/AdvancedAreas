<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\forms\EditSelectAreaForm;
use Its123Miguel321\AdvancedAreas\subcommands\SubCommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EditCommand extends SubCommand{

	public function canUse(CommandSender $sender) : bool{
		return ($sender instanceof Player) && $sender->hasPermission('advancedareas.admin');
	}

	public function execute(CommandSender $sender, array $args) : bool{
		EditSelectAreaForm::form($sender);
		return true;
	}
}