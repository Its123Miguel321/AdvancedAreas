<?php

namespace Its123Miguel321\AdvancedAreas\subcommands;

use Its123Miguel321\AdvancedAreas\subcommands\SubCommand;

use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class WandCommand extends SubCommand{

	public function canUse(CommandSender $sender) : bool{
		return ($sender instanceof Player) && $sender->hasPermission('advancedareas.admin');
	}

	public function execute(CommandSender $sender, array $args) : bool{
		$item = $this->getPlugin()->getSettings()->getWandItem();
		$item->setCustomName(TF::BOLD . TF::GOLD . 'Advanced' . TF::WHITE . 'Areas' . TF::GOLD . ' Wand');
		$item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(750)));
		
		/** @var Player $sender */
		if($sender->getInventory()->canAddItem($item)){
			$sender->getInventory()->addItem($item);
			$sender->sendMessage(TF::GREEN . 'Added ' . TF::BOLD . TF::GOLD . 'Advanced' . TF::WHITE . 'Areas' . TF::GOLD . ' Wand' . TF::RESET . TF::GREEN . ' your inventory!');
		}else{
			$sender->sendMessage(TF::RED . 'Your inventory was full, could not add wand to inventory!');
		}

		return true;
	}
}