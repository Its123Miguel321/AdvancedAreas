<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\api\AreasAPI;
use Its123Miguel321\AdvancedAreas\area\Area;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class EditOptionsForm{

	public static function form(Player $player, Area $area, string $label = '') : void{
		$label .= TF::GRAY . 'There are a few options below...';

		$form = new CustomForm(function(Player $player, ?array $data) use($area){
			if(is_null($data)) return;

			$hasUpdated = false;
			$priority = $data[1];

			if(empty($priority)) return self::form($player, $area, TF::RED . 'You must enter the area priority!' . "\n\n");
			if(!(is_numeric($priority)) && is_int(intval($priority))) return self::form($player, $area, TF::RED . 'You must enter a number for the priority!' . "\n\n");

			if($priority != $area->getPriority()){
				$area->setPriority($priority);
				$hasUpdated = true;
			}

			$items = explode(';', $data[3]);

			if(!(empty($items))){
				foreach($items as $itemData){
					$itemData = explode("|", $itemData);
					$item = strtolower($itemData[0]);

					if($item === 'clear'){
						$area->resetItems();
						break;
					}

					$type = strtolower(($itemData[1] ?? 'a'));

					if($type === 'r'){
						$area->removeItem($item);
					}else{
						$area->addItem($item);
					}
				}
				
				$hasUpdated = true;
			}

			if((bool) $data[4] != $area->getItemsApplyToWhitelist()){
				$area->setItemsApplyToWhitelist((bool) $data[4]);
				$hasUpdated = true;
			}

			$effects = explode(';', $data[6]);

			if(!(empty($effects))){
				foreach($effects as $effectsData){
					$effectsData = explode("|", $effectsData);
					$effect = strtolower($effectsData[0]);

					if($effect === 'clear'){
						$area->resetEffects();
						break;
					}

					$type = strtolower(($effectsData[1] ?? 'a'));

					if($type === 'r'){
						$area->removeEffect($effect);
					}else{
						$area->addEffect($effect);
					}
				}

				$hasUpdated = true;
			}

			if((bool) $data[7] != $area->getEffectsApplyToWhitelist()){
				$area->getEffectsApplyToWhitelist((bool) $data[7]);
				$hasUpdated = true;
			}

			$whitelist = explode(';', $data[9]);

			if(!(empty($whitelist))){
				foreach($whitelist as $whitelistData){
					$whitelistData = explode("|", $whitelistData);
					$whitelistPlayer = strtolower($whitelistData[0]);

					if($whitelistPlayer === 'clear'){
						$area->resetWhitelist();
						break;
					}

					$type = strtolower(($whitelistData[1] ?? 'a'));

					if($type === 'r'){
						$area->removeFromWhitelist($whitelistPlayer);
					}else{
						$area->addToWhitelist($whitelistPlayer);
					}
				}

				$hasUpdated = true;
			}

			if($data[11]){
				$selections = AreasAPI::getSelections($player);

				if(is_null($selections) || is_null($selections->getPosA()) || is_null($selections->getPosB())) return self::form($player, $area, TF::RED . 'You can not change the positions, you must have two positions selected!' . "\n\n");
				if($selections->getPosA()->getWorld()->getFolderName() !== $selections->getPosB()->getWorld()->getFolderName()) return self::form($player, $area, TF::RED . 'Selected positions are not in the same world!' . "\n\n");

				$area->setPosA($selections->getPosA());
				$area->setPosB($selections->getPosB());

				$hasUpdated = true;
			}	

			return EditAreaForm::form($player, $area, ($hasUpdated ? TF::GREEN . 'Updated Area Options!' . "\n\n" : ""));
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Area ' . TF::WHITE . 'Options');
		$form->addLabel($label);
		$form->addInput('Priority', '-1', (string)$area->getPriority());

		$itemsLabel = TF::GRAY . "Items listed below are banned in this area!" . "\n";
		$itemsLabel .= (empty($area->getItems()) ? TF::AQUA . '(None)' : TF::RED . implode(TF::WHITE . ', ' . TF::RED, $area->getItems())) . "\n\n";
		$itemsLabel .= TF::GRAY . "You can add or remove items in the input below!" . "\n";
		$itemsLabel .= TF::YELLOW . "(Add an " . TF::GRAY . "|r" . TF::YELLOW . " after the item name, if you want to remove the item from the list!)" . "\n";

		$form->addLabel($itemsLabel);
		$form->addInput('Items', 'diamond_sword;wooden_shovel|r;etc...');
		$form->addToggle('Apply Items To Whitelist', $area->getItemsApplyToWhitelist());

		$effectsLabel = TF::GRAY . 'Effects listed here would be given upon entering the area!' . "\n";
		$effectsLabel .= (empty($area->getEffects()) ? TF::AQUA . '(None)' : TF::RED . implode(TF::WHITE . ', ' . TF::RED, $area->getEffects())) . "\n\n";
		$effectsLabel .= TF::GRAY . "You can add or remove effects in the input below!" . "\n";
		$effectsLabel .= TF::YELLOW . "(Add an " . TF::GRAY . "|r" . TF::YELLOW . " after the effect name, if you want to remove the effect from the list!)" . "\n";

		$form->addLabel($effectsLabel);
		$form->addInput('Effects', 'night_vison-5;jump_boost|r;etc...');
		$form->addToggle('Apply Effects To Whitelist', $area->getEffectsApplyToWhitelist());

		$whitelistLabel = TF::GRAY . 'These are the whitelisted players of this area!' . "\n";
		$whitelistLabel .= (empty($area->getWhitelist()) ? TF::AQUA . '(None)' : TF::RED . implode(TF::WHITE . ', ' . TF::RED, $area->getWhitelist())) . "\n\n";
		$whitelistLabel .= TF::GRAY . "You can add or remove players in the input below!" . "\n";
		$whitelistLabel .= TF::YELLOW . "(Add an " . TF::GRAY . "|r" . TF::YELLOW . " after the player name, if you want to remove the player from the list!)" . "\n";

		$form->addLabel($whitelistLabel);
		$form->addInput('Whitelist', 'player_one;player_two|r;etc...');
		$form->addLabel(TF::GRAY . 'This last option will change the areas positions to your current selected postions! ' . TF::RED . '(Be Careful!)');
		$form->addToggle('Change Positions', false);
		$player->sendForm($form);
	}
}