<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\api\AreasAPI;
use Its123Miguel321\AdvancedAreas\utils\Flags;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class OptionsForm{

	public static function form(Player $player, string $name, array $events, string $label = '') : void{
		$label .= TF::GRAY . 'One last thing, there are a few options below you can edit before you create the area!';

		$form = new CustomForm(function(Player $player, ?array $data) use($name, $events){
			if(is_null($data)) return;

			$priority = $data[1];

			if(empty($priority)) return self::form($player, $name, $events, TF::RED . 'You must enter the area priority!' . "\n\n");
			if(!(is_numeric($priority)) && is_int(intval($priority))) return self::form($player, $name, $events, TF::RED . 'You must enter a number for the priority!' . "\n\n");

			$items = explode(';', $data[3]);
			$itemsForWhitelist = (bool) $data[4];
			$effects = explode(';', $data[6]);
			$effectsForWhitelist = (bool) $data[7];
			$whitelist = explode(';', $data[8]);
			$selections = AreasAPI::getSelections($player);

			if($data[10]){
				$posA = $selections->getPosA();
				$posB = $selections->getPosB();
				$posA->y = 256;
				$posB->y = 0;
				$selections->setPosA($posA);
				$selections->setPosB($posB);
			}

			AdvancedAreas::getInstance()->getProvider()->create(
				$name, 
				$selections, 
				$priority, 
				$whitelist,
				$events,
				$effects,
				$items,
				$itemsForWhitelist,
				$effectsForWhitelist
			);

			$player->sendMessage(TF::GREEN . 'You created a new build protect area!');
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Area ' . TF::WHITE . 'Options');
		$form->addLabel($label);
		$form->addInput('Priority', '-1', (string)AdvancedAreas::getInstance()->getSettings()->getDefaultAreaSettings()[Flags::FLAG_AREA_PRIORITY]);
		$form->addLabel(TF::GRAY . 'Items listed here would not be allowed to use in this area!');
		$form->addInput('Items', 'diamond_sword;bow;etc...');
		$form->addToggle('Apply Items To Whitelist');
		$form->addLabel(TF::GRAY . 'Effects listed here would be given upon entering the area!');
		$form->addInput('Effects', 'night_vison-5;jump_boost-3;etc...');
		$form->addToggle('Apply Effects To Whitelist');
		$form->addInput('Whitelist', 'player_one;player_two;etc...');
		$form->addLabel(TF::GRAY . 'This last option makes the area cover Y axis 0-256!');
		$form->addToggle('Max Height', false);
		$player->sendForm($form);
	}
}