<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\subcommands\SubCommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class ListCommand extends SubCommand{

	public function canUse(CommandSender $sender) : bool{
		return ($sender instanceof Player) && $sender->hasPermission('advancedareas.admin');
	}

	public function execute(CommandSender $sender, array $args) : bool{
		$areas = AdvancedAreas::getInstance()->getProvider()->getAll();

		if(empty($areas)){
			$sender->sendMessage(TF::RED . "Currently there are no areas that exist.");
			return false;
		}

		if(count($args) === 0) {
			$pageNumber = 1;
		}elseif(is_numeric($args[0])) {
			$pageNumber = (int) array_shift($args);
			if ($pageNumber <= 0) {
				$pageNumber = 1;
			}
		}else{
			return false;
		}

		$areas = array_chunk($areas, (int) ($sender->getScreenLineHeight() / 2));
		$pageNumber = min(count($areas), $pageNumber);

		$list = TF::GRAY . str_repeat(' - ', 3) . TF::BOLD . TF::DARK_GREEN . 'Showing Areas list page ' . TF::WHITE . $pageNumber . TF::DARK_GREEN . " of " . TF::WHITE . count($areas) . TF::RESET . TF::GRAY . str_repeat(' -', 3) . "\n\n";
		
		foreach($areas[$pageNumber - 1] as $area){
			$list .= TF::DARK_GREEN . ucfirst($area->getDisplayName()) . TF::GRAY .  " (World: " . (is_null(($world = $area->getWorld())) ? "Unknown" : $world->getFolderName()) . ")" . TF::RESET ."\n";
		}
		
		$list .= TF::GRAY . str_repeat(' - ', 18);
		
		$sender->sendMessage($list);
		return true;
	}
}