<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\menus\CreateAreaSettingsMenu;

use jojoe77777\FormAPI\CustomForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;


class CreateAreaNameForm{

	public static function form(Player $player, string $label = '') : void{
		$label .= TF::GRAY . 'You are creating a new protected area!';

		$form = new CustomForm(function(Player $player, ?array $data){
			if(is_null($data)) return;

			$name = trim($data[1], ' §');

			if(empty($name)) return self::form($player, TF::RED . 'You must enter a name for this area!' . "\n\n");
			if(!(is_null(AdvancedAreas::getInstance()->getProvider()->get($name)))) return self::form($player, TF::RED . 'The name ' . TF::DARK_GRAY . $name . TF::RED . ' is already taken, please use another name for this area!' . "\n\n");
		
			return CreateAreaSettingsMenu::menu($player, $name);
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Create ' . TF::WHITE . 'Area' . TF::GOLD . ' Name');
		$form->addLabel($label);
		$form->addInput('Enter Area Name', 'area1');
		$player->sendForm($form);
	}
}