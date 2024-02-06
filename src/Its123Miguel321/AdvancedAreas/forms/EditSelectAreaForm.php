<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class EditSelectAreaForm{

	public static function form(Player $player, string $label = '') : void{
		$areas = [];

		foreach(AdvancedAreas::getInstance()->getProvider()->getAll() as $area) $areas[] = $area->getDisplayName();

		if(empty($areas)){
			$player->sendMessage(TF::RED . 'There are no areas you can delete!');
			return;
		}

		$label .= TF::GRAY . 'Select the area you would like to edit!';

		$form = new CustomForm(function(Player $player, ?array $data) use($areas){
			if(is_null($data)) return;

			$area = AdvancedAreas::getInstance()->getProvider()->get(($name = $areas[$data[1]]));

			if(is_null($area)) return self::form($player, TF::RED . 'The area named ' . TF::WHITE . $name . TF::RED . ' no longer exists!' . "\n\n");

			return EditAreaForm::form($player, $area, TF::GREEN . 'You selected the area named ' . TF::WHITE . $name . TF::GREEN . '!' . "\n\n");
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Select ' . TF::WHITE . 'Area');
		$form->addLabel($label);
		$form->addDropdown('Select area', $areas);
		$player->sendForm($form);
	}
}