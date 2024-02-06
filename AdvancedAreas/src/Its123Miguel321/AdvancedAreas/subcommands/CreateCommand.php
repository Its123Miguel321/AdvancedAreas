<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\API\AreasAPI;
use Its123Miguel321\AdvancedAreas\forms\CreateAreaNameForm;
use Its123Miguel321\AdvancedAreas\subcommands\SubCommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class CreateCommand extends SubCommand{

	public function canUse(CommandSender $sender) : bool{
		return ($sender instanceof Player) && $sender->hasPermission('advancedareas.admin');
	}

	public function execute(CommandSender $sender, array $args) : bool{
		$selections = AreasAPI::getSelections($sender);

		if(is_null($selections) || is_null($selections->getPosA()) || is_null($selections->getPosB())){
			$sender->sendMessage(TF::RED . 'You must have 2 selections!');
			return false;
		}
		
		if($selections->getPosA()->getWorld()->getFolderName() !== $selections->getPosB()->getWorld()->getFolderName()){
			$sender->sendMessage(TF::RED . 'Selections must be in the same world!');
			return false;
		}
		
		CreateAreaNameForm::form($sender);
		return true;
	}
}