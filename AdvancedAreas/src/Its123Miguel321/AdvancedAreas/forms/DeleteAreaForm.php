<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class DeleteAreaForm{

	public static function form(Player $player, string $label = '') : void{
		$label .= TF::GRAY . 'Select the area you would like to delete!';

		$areas = [];

		foreach(AdvancedAreas::getInstance()->getProvider()->getAll() as $area) $areas[] = $area->getDisplayName();

		if(empty($areas)){
			$player->sendMessage(TF::RED . 'There are no areas you can delete!');
			return;
		}

		$form = new CustomForm(function(Player $player, ?array $data) use($areas){
			if(is_null($data)) return;

			$name = $areas[$data[1]];
			$area = AdvancedAreas::getInstance()->getProvider()->get($name);

			if(is_null($area)) return self::form($player, TF::RED . 'The area named ' . TF::WHITE . $name . TF::RED . ' has already been deleted!' . "\n\n");

			AdvancedAreas::getInstance()->getProvider()->delete($area);

			return self::form($player, TF::GREEN . 'You deleted the area named ' . TF::WHITE . $name . TF::GREEN . '!' . "\n\n");
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Delete ' . TF::WHITE . 'Area');
		$form->addLabel($label);
		$form->addDropdown('Select area', $areas);
		$player->sendForm($form);
	}
}