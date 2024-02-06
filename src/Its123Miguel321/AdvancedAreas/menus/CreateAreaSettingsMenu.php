<?php

namespace Its123Miguel321\AdvancedAreas\menus;

use Its123Miguel321\AdvancedAreas\AdvancedAreas;
use Its123Miguel321\AdvancedAreas\forms\{
	CreateAreaNameForm,
	OptionsForm
};
use Its123Miguel321\AdvancedAreas\utils\Flags;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\{
	InvMenuTransaction,
	InvMenuTransactionResult
};

use pocketmine\block\{
	utils\DyeColor,
	VanillaBlocks
};
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\inventory\Inventory;
use pocketmine\item\{
	enchantment\EnchantmentInstance,
	Item,
	VanillaItems
};
use pocketmine\network\mcpe\{
	NetworkBroadcastUtils,
	protocol\PlaySoundPacket
};
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class CreateAreaSettingsMenu{
	
	private static array $slots = [
		10 => Flags::FLAG_EVENT_BLOCK_BREAK,
		11 => Flags::FLAG_EVENT_BLOCK_DECAY,
		12 => Flags::FLAG_EVENT_BLOCK_GROW,

		14 => Flags::FLAG_EVENT_PLAYER_INTERACT,
		15 => Flags::FLAG_EVENT_BLOCK_PLACE,
		16 => Flags::FLAG_EVENT_BLOCK_UPDATE,

		19 => Flags::FLAG_EVENT_PLAYER_CRAFT,
		20 => Flags::FLAG_EVENT_PLAYER_FLIGHT,
		21 => Flags::FLAG_EVENT_PLAYER_HUNGER,
		22 => Flags::FLAG_EVENT_ENTITY_REGAIN_HEALTH,
		23 => Flags::FLAG_EVENT_PLAYER_ITEM_DROP,
		24 => Flags::FLAG_EVENT_PLAYER_ITEM_PICKUP,
		25 => Flags::FLAG_EVENT_PLAYER_SPRINT,

		30 => Flags::FLAG_EVENT_ENTITY_DAMAGE,
		31 => Flags::FLAG_EVENT_ENTITY_EXPLOSION,
		32 => Flags::FLAG_EVENT_ENTITY_TELEPORT
	];
	private static array $defaults = [];



	public static function setMenuItems(Player $player, Inventory $menu) : void{
		$items = [
			Flags::FLAG_EVENT_BLOCK_BREAK => VanillaItems::DIAMOND_PICKAXE()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Breaking'),
			Flags::FLAG_EVENT_BLOCK_DECAY => VanillaBlocks::OAK_LEAVES()->asItem()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Decay'),
			Flags::FLAG_EVENT_BLOCK_GROW => VanillaItems::WHEAT()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Growth'),
			Flags::FLAG_EVENT_PLAYER_INTERACT => VanillaBlocks::ITEM_FRAME()->asItem()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Interacting'),
			Flags::FLAG_EVENT_BLOCK_PLACE => VanillaBlocks::STONE()->asItem()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Placing'),
			Flags::FLAG_EVENT_BLOCK_UPDATE => VanillaItems::WATER_BUCKET()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Block Update'),
	
			Flags::FLAG_EVENT_PLAYER_CRAFT => VanillaBlocks::CRAFTING_TABLE()->asItem()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Player Craft'),
			Flags::FLAG_EVENT_PLAYER_FLIGHT => VanillaItems::FEATHER()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Player Flight'),
			Flags::FLAG_EVENT_PLAYER_HUNGER => VanillaItems::STEAK()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Player Hunger'),
			Flags::FLAG_EVENT_ENTITY_REGAIN_HEALTH => VanillaItems::ENCHANTED_GOLDEN_APPLE()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Entity Regain Health'),
			Flags::FLAG_EVENT_PLAYER_ITEM_DROP => VanillaItems::STICK()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Item Drop'),
			Flags::FLAG_EVENT_PLAYER_ITEM_PICKUP => VanillaItems::BLAZE_ROD()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Item Pickup'),
			Flags::FLAG_EVENT_PLAYER_SPRINT => VanillaItems::GOLDEN_BOOTS()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Player Sprint'),

			Flags::FLAG_EVENT_ENTITY_DAMAGE => VanillaItems::DIAMOND_SWORD()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Entity Damage'),
			Flags::FLAG_EVENT_ENTITY_EXPLOSION => VanillaBlocks::TNT()->asItem()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Entity Explosion'),
			Flags::FLAG_EVENT_ENTITY_TELEPORT => VanillaItems::ENDER_PEARL()->setCustomName(TF::BOLD . TF::DARK_PURPLE . 'Entity Teleport')
		];
		$tools = [
			45 => VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::RED())->asItem()->setCustomName(TF::BOLD . TF::RED . 'Back'),
			49 => VanillaBlocks::BARRIER()->asItem()->setCustomName(TF::BOLD . TF::DARK_RED . 'Cancel'),
			53 => VanillaBlocks::GLAZED_TERRACOTTA()->setColor(DyeColor::GREEN())->asItem()->setCustomName(TF::BOLD . TF::GREEN . 'Continue')
		];

		for($i = 0; $i < 54; $i++){
			/** @var Item $item */
			$item = (isset(self::$slots[$i]) ? $items[self::$slots[$i]] : (isset($tools[$i]) ? $tools[$i] : VanillaBlocks::IRON_BARS()->asItem()->setCustomName(' ')));

			if(isset(self::$slots[$i])){
				$item->setLore([
					'',
					TF::GRAY . 'Status: ' . (self::$defaults[$player->getXuid()][self::$slots[$i]] ? TF::GREEN . 'ENABLED' : TF::RED . 'DISABLED')
				]);
				$item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(750)));
			}

			$menu->setItem($i, $item);
		}
	}

	public static function menu(Player $player, string $name) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($name . ' Settings');
		$menu->setListener(function(InvMenuTransaction $transaction) use($name) : InvMenuTransactionResult{
			return self::listener($name, $transaction);
		});

		self::$defaults[$player->getXuid()] = AdvancedAreas::getInstance()->getSettings()->getDefaultAreaSettings()[Flags::FLAG_AREA_EVENTS];
		self::setMenuItems($player, $menu->getInventory());

		$menu->send($player);
	}

	public static function listener(string $name, InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$player = $transaction->getPlayer();
		$action = $transaction->getAction();
		$slot = $action->getSlot();
		$menu = $action->getInventory();

		if(isset(self::$slots[$slot])){
			$setting = self::$slots[$slot];
			
			self::$defaults[$player->getXuid()][$setting] = !(self::$defaults[$player->getXuid()][$setting]);
			self::setMenuItems($player, $menu);

			$sound = PlaySoundPacket::create(
				'note.pling',
				$player->getLocation()->getX(),
				$player->getLocation()->getY(),
				$player->getLocation()->getZ(),
				0.75,
				1.0
			);

			NetworkBroadcastUtils::broadcastPackets([$player], [$sound]);
		}

		if($slot === 45){
			$player->removeCurrentWindow();

			return $transaction->discard()->then(function() use($player){
				CreateAreaNameForm::form($player);
			});
		}elseif($slot === 49){
			$player->removeCurrentWindow();
		}elseif($slot === 53){
			$player->removeCurrentWindow();

			return $transaction->discard()->then(function() use($player, $name){
				OptionsForm::form($player, $name, self::$defaults[$player->getXuid()]);
			});
		}

		return $transaction->discard();
	}
}