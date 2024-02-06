<?php

namespace Its123Miguel321\AdvancedAreas\forms;

use Its123Miguel321\AdvancedAreas\area\Area;
use Its123Miguel321\AdvancedAreas\menus\EditAreaSettingsMenu;

use jojoe77777\FormAPI\SimpleForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class EditAreaForm{

	public static function form(Player $player, Area $area, string $label = '') : void{
		$label .= TF::GRAY . 'Click on what you would like to change about this area!' . "\n\n";

		$form = new SimpleForm(function(Player $player, ?int $data) use($area){
			if(is_null($data)) return;

			switch($data){
				case 0:
					return;
					break;
				
				case 1:
					EditSelectAreaForm::form($player);
					break;

				case 2:
					EditAreaSettingsMenu::menu($player, $area);
					break;

				case 3:
					EditOptionsForm::form($player, $area);
					break;
			}
		});
		$form->setTitle(TF::BOLD . TF::GOLD . 'Edit ' . TF::WHITE . 'Area');
		$form->setContent($label);
		$form->addButton(TF::RED . 'Close');
		$form->addButton(TF::GOLD . 'Back');
		$form->addButton(TF::DARK_GRAY . 'Edit Events');
		$form->addButton(TF::DARK_GRAY . 'Edit Options');
		$player->sendForm($form);
	}
}