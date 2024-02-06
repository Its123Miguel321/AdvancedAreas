<?php

namespace Its123Miguel321\AdvancedAreas;

use Its123Miguel321\AdvancedAreas\api\AreasAPI;
use Its123Miguel321\AdvancedAreas\utils\{
	Flags,
	Selections
};

use pocketmine\entity\effect\{
	EffectInstance,
	StringToEffectParser
};
use pocketmine\event\block\{
	BlockBreakEvent,
	BlockGrowEvent,
	BlockPlaceEvent,
	BlockUpdateEvent,
	LeavesDecayEvent
};
use pocketmine\event\entity\{
	EntityDamageByEntityEvent,
    EntityDamageEvent,
    EntityExplodeEvent,
	EntityItemPickupEvent,
	EntityRegainHealthEvent,
	EntityTeleportEvent
};
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerDropItemEvent,
	PlayerExhaustEvent,
	PlayerInteractEvent,
	PlayerItemUseEvent,
	PlayerMoveEvent,
	PlayerToggleFlightEvent,
	PlayerToggleSprintEvent
};
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class EventListener implements Listener{
	
	private array $wandClicks = [];



	public function __construct(
		private AdvancedAreas $plugin
	){}

	public function onBreak(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();

		if($item->equals($this->plugin->getSettings()->getWandItem(), false, false)){
			if(isset($this->wandClicks[$player->getXuid()]) && microtime(true) - $this->wandClicks[$player->getXuid()] < 0.5) return;

			$this->wandClicks[$player->getXuid()] = microtime(true);

			$event->cancel();

			if(is_null(AreasAPI::getSelections($player))){
				AreasAPI::addSelections($player, new Selections($block->getPosition(), null));
			}else{
				AreasAPI::getSelections($player)->setPosA($block->getPosition());
			}

			$player->sendMessage(TF::GREEN . 'Selected first position ' . str_replace('Position', TF::GRAY, $block->getPosition()->__toString()));
		}

		if($player->getGamemode()->equals(GameMode::CREATIVE())) return;

		if(!$this->onBlockEvent($event, Flags::FLAG_EVENT_BLOCK_BREAK)) return;

		$player->sendMessage(TF::RED . 'You can not break blocks in this area!');
	}

	public function onInteract(PlayerInteractEvent $event) : void{
		if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK) return;

		$player = $event->getPlayer();
		$item = $event->getItem();
		$block = $event->getBlock();

		if($item->equals($this->plugin->getSettings()->getWandItem(), false, false)){
			if(isset($this->wandClicks[$player->getXuid()]) && microtime(true) - $this->wandClicks[$player->getXuid()] < 0.5) return;

			$this->wandClicks[$player->getXuid()] = microtime(true);

			$event->cancel();

			if(is_null(AreasAPI::getSelections($player))){
				AreasAPI::addSelections($player, new Selections($block->getPosition()));
			}else{
				AreasAPI::getSelections($player)->setPosB($block->getPosition());
			}

			$player->sendMessage(TF::GREEN . 'Selected second position ' . str_replace('Position', TF::GRAY, $block->getPosition()->__toString()));
		}

		if($player->getGamemode()->equals(GameMode::CREATIVE())) return;

		if(!$this->onBlockEvent($event, Flags::FLAG_EVENT_PLAYER_INTERACT)) return;

		$player->sendMessage(TF::RED . 'You can not interact with blocks in this area!');
	}

	public function onPlace(BlockPlaceEvent $event) : void{
		$player = $event->getPlayer();

		if($player->getGamemode()->equals(GameMode::CREATIVE())) return;

		if(!$this->onBlockEvent($event, Flags::FLAG_EVENT_BLOCK_PLACE)) return;

		$player->sendMessage(TF::RED . 'You can not place blocks in this area!');
	}

	public function onDecay(LeavesDecayEvent $event) : void{
		$this->onBlockEvent($event, Flags::FLAG_EVENT_BLOCK_DECAY);
	}

	public function onGrow(BlockGrowEvent $event) : void{
		$this->onBlockEvent($event, Flags::FLAG_EVENT_BLOCK_GROW);
	}

	public function onUpdate(BlockUpdateEvent $event) : void{
		$this->onBlockEvent($event, Flags::FLAG_EVENT_BLOCK_UPDATE);
	}

	/**
	 * Returns true if event is cancelled, returns false if not!
	 */
	public function onBlockEvent(PlayerInteractEvent|BlockPlaceEvent|BlockBreakEvent|LeavesDecayEvent|BlockGrowEvent|BlockUpdateEvent $event, string $flag) : bool{
		if($event->isCancelled()) return true;

		$block = ($event instanceof BlockPlaceEvent ? $event->getBlockAgainst() : $event->getBlock());
		$allow = true;
		$priority = -100;

		foreach(AreasAPI::getAreasIn($block->getPosition()) as $area){
			if($area->getPriority() <= $priority) continue;

			$allow = $area->getEventValue($flag);
			$priority = $area->getPriority();
		}

		if(!$allow){
			$event->cancel();
			return true;
		}else{
			return false;
		}
	}

	public function onAttack(EntityDamageEvent $event) : void{
		if($event->isCancelled()) return;

		$victim = $event->getEntity();

		if($event instanceof EntityDamageByEntityEvent){
			$attacker = $event->getDamager();

			if(!($victim instanceof Player && $attacker instanceof Player)) return;
			if($attacker->getGamemode()->equals(GameMode::CREATIVE())) return;
			if(empty(($victimAreas = AreasAPI::getAreasIn($victim->getLocation()))) && empty(($attackerAreas = AreasAPI::getAreasIn($attacker->getLocation())))) return;

			$attackerArea = null;
			$attackerPriority = -100;

			foreach($attackerAreas as $area){
				if($area->getPriority() <= $attackerPriority) continue;

				$attackerArea = $area;
				$attackerPriority = $area->getPriority();
			}

			$victimArea = null;
			$victimPriority = -100;

			foreach($victimAreas as $area){
				if($area->getPriority() <= $victimPriority) continue;

				$victimArea = $area;
				$victimPriority = $area->getPriority();
			}

			if(is_null($victimArea) || is_null($attackerArea)) return;

			$allow = ($victimArea->getEventValue(Flags::FLAG_EVENT_ENTITY_DAMAGE) && $attackerArea->getEventValue(Flags::FLAG_EVENT_ENTITY_DAMAGE) ? true : false);

			if(!$allow){
				$event->cancel();
				$attacker->sendMessage(TF::RED . 'You can not attack others in this area!');
			}
		}else{
			$this->onEntityEvent($event, Flags::FLAG_EVENT_ENTITY_DAMAGE);
		}
	}

	public function onExplode(EntityExplodeEvent $event) : void{
		$this->onEntityEvent($event, Flags::FLAG_EVENT_ENTITY_EXPLOSION);
	}

	public function onRegainHealth(EntityRegainHealthEvent $event) : void{
		$this->onEntityEvent($event, Flags::FLAG_EVENT_ENTITY_REGAIN_HEALTH);
	}

	public function onTeleport(EntityTeleportEvent $event) : void{
		if(!$this->onEntityEvent($event, Flags::FLAG_EVENT_ENTITY_TELEPORT)) return;

		$entity = $event->getEntity();
		
		if($entity instanceof Player) $entity->sendMessage(TF::RED . 'You can not teleport in this area!');
	}

	public function onPickup(EntityItemPickupEvent $event) : void{
		$this->onEntityEvent($event, Flags::FLAG_EVENT_PLAYER_ITEM_PICKUP);
	}

	/**
	 * Returns true if event is cancelled, returns false if not!
	 */
	public function onEntityEvent(EntityExplodeEvent|EntityRegainHealthEvent|EntityTeleportEvent|EntityItemPickupEvent|EntityDamageEvent $event, string $flag) : bool{
		if($event->isCancelled()) return true;

		$entity = $event->getEntity();
		$allow = true;
		$priority = -100;

		foreach(AreasAPI::getAreasIn($entity->getPosition()) as $area){
			if($area->getPriority() <= $priority) continue;

			$allow = $area->getEventValue($flag);
			$priority = $area->getPriority();
		}

		if(!$allow){
			$event->cancel();
			return true;
		}else{
			return false;
		}
	}

	public function onCraft(CraftItemEvent $event) : void{
		$player = $event->getPlayer();

		if(!$this->onPlayerEvent($event, Flags::FLAG_EVENT_PLAYER_CRAFT)) return;

		$player->sendMessage(TF::RED . 'You can not craft items in this area!');

		if(!is_null($player->getCurrentWindow())) $player->removeCurrentWindow();
	}

	public function onDrop(PlayerDropItemEvent $event) : void{
		$player = $event->getPlayer();

		if(!$this->onPlayerEvent($event, Flags::FLAG_EVENT_PLAYER_ITEM_DROP)) return;

		$player->sendMessage(TF::RED . 'You can not drop items in this area!');
	}

	public function onFly(PlayerToggleFlightEvent $event) : void{
		$player = $event->getPlayer();

		if(!$event->isFlying()) return;
		if(!$this->onPlayerEvent($event, Flags::FLAG_EVENT_PLAYER_FLIGHT)) return;

		$player->sendMessage(TF::RED . 'You can not fly in this area!');
	}

	public function onSprint(PlayerToggleSprintEvent $event) : void{
		$player = $event->getPlayer();

		if(!$event->isSprinting()) return;
		if(!$this->onPlayerEvent($event, Flags::FLAG_EVENT_PLAYER_SPRINT)) return;

		$player->sendMessage(TF::RED . 'You can not sprint in this area!');
	}

	public function onHunger(PlayerExhaustEvent $event) : void{
		$this->onPlayerEvent($event, Flags::FLAG_EVENT_PLAYER_SPRINT);
	}

	/**
	 * Returns true if event is cancelled, returns false if not!
	 */
	public function onPlayerEvent(CraftItemEvent|PlayerDropItemEvent|PlayerToggleSprintEvent|PlayerToggleFlightEvent|PlayerExhaustEvent $event, string $flag) : bool{
		if($event->isCancelled()) return true;

		$player = $event->getPlayer();

		if($player->getGamemode()->equals(GameMode::CREATIVE())) return false;

		$allow = true;
		$priority = -100;

		foreach(AreasAPI::getAreasIn($player->getPosition()) as $area){
			if($area->getPriority() <= $priority) continue;

			$allow = $area->getEventValue($flag);
			$priority = $area->getPriority();
		}

		if(!$allow){
			$event->cancel();
			return true;
		}else{
			return false;
		}
	}

	public function onItemUse(PlayerItemUseEvent $event) : void{
		if($event->isCancelled()) return;

		$player = $event->getPlayer();
		$item = $event->getItem();

		if($player->getGamemode()->equals(GameMode::CREATIVE())) return;

		$allow = true;
		$priority = -100;

		foreach(AreasAPI::getAreasIn($player->getPosition()) as $area){
			if($area->getPriority() <= $priority) continue;
			
			$allow = !($area->inWhitelist($player) ? $area->getItemsApplyToWhitelist() && $area->hasItem($item) : $area->hasItem($item));
			$priority = $area->getPriority();
		}

		if(!$allow) $event->cancel();
		
	}

	public function onMove(PlayerMoveEvent $event) : void{
		if($event->isCancelled()) return;

		$player = $event->getPlayer();
		$from = $event->getFrom();
		$areasFrom = AreasAPI::getAreasIn($from);
		$to = $event->getTo();
		$areasTo = AreasAPI::getAreasIn($to);
		$enteringArea = (AreasAPI::isInside($to) && !AreasAPI::isInside($from));
		$enteringNewArea = (count($areasTo) > count($areasFrom));
		$leavingArea = (count($areasTo) < count($areasFrom));

		if(
			AdvancedAreas::getInstance()->getSettings()->showAreaNames() && 
			!AreasAPI::inSameAreas($to, $from, false) && 
			(AreasAPI::isInside($from) || AreasAPI::isInside($to))
		){
			$areaNames = [];

			foreach((($enteringArea || $enteringNewArea) ? $areasTo : $areasFrom) as $area){
				$areas = (($enteringArea || $enteringNewArea) ? $areasFrom : $areasTo);

				if(in_array($area->getIdentifier(), array_keys($areas))) continue;

				$areaNames[] = $area->getDisplayName();
			}

			if(!empty($areaNames)){
				$type = AdvancedAreas::getInstance()->getSettings()->getAreaNameType();
				$msg = (($enteringArea || $enteringNewArea) ? TF::GREEN . 'Entering' : TF::RED . 'Leaving') . ' Area' . (count($areaNames) > 1 ? 's' : '');
				
				switch(strtolower($type)){
					case 'title':
						$player->sendTitle(
							$msg,
							TF::GRAY . implode(TF::WHITE . ', ' . TF::GRAY, $areaNames)
						);
						break;

					case 'message':
						$msg .= ": " . TF::GRAY . implode(TF::WHITE . ', ' . TF::GRAY, $areaNames);

						$player->sendMessage($msg);
						break;

					case 'popup':
					default:
						$msg .= "\n" . TF::GRAY . implode(TF::WHITE . ', ' . TF::GRAY, $areaNames);

						$player->sendPopup($msg);	
				}
			}
		}

		if($leavingArea){
			foreach($areasFrom as $area){
				if(AreasAPI::inSameArea($to, $area)) continue;

				foreach($area->getEffects() as $effectData){
					$effectData = explode('-', $effectData);
					$effect = $effectData[0];
					$level = $effectData[1] ?? 1;

					$effect = StringToEffectParser::getInstance()->parse($effect);

					if(is_null($effect)) continue;

					$effectInstance = new EffectInstance($effect, null, $level, false);
					$allow = !($area->inWhitelist($player) ? $area->getEffectsApplyToWhitelist() && $area->hasEffect($effectInstance) : $area->hasEffect($effectInstance));

					if($allow) $player->getEffects()->remove($effect);
				}
			}
			return;
		}

		if($enteringArea || $enteringNewArea){
			foreach(AreasAPI::getAreasIn($to) as $area){
				foreach($area->getEffects() as $effectData){
					$effectData = explode('-', $effectData);
					$effect = $effectData[0];
					$level = $effectData[1] ?? 1;

					$effect = StringToEffectParser::getInstance()->parse($effect);

					if(is_null($effect)) continue;

					$effectInstance = new EffectInstance($effect, null, $level, false);
					$allow = !($area->inWhitelist($player) ? $area->getEffectsApplyToWhitelist() && $area->hasEffect($effectInstance) : $area->hasEffect($effectInstance));

					if($allow) $player->getEffects()->add($effectInstance);
				}
			}
		}
	}
}
