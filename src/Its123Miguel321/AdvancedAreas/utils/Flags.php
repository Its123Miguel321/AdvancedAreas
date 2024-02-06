<?php

namespace Its123Miguel321\AdvancedAreas\utils;

class Flags{

	const FLAG_AREA_BANNED_ITEMS = 'banned-items';
	const FLAG_AREA_EFFECTS = 'effects';
	const FLAG_AREA_EVENTS = 'events';
	const FLAG_AREA_WHITELIST = 'whitelist';
	const FLAG_AREA_PRIORITY = 'priority';

	const FLAG_EVENT_BLOCK_BREAK = 'block.break';
	const FLAG_EVENT_BLOCK_DECAY = 'block.decay';
	const FLAG_EVENT_BLOCK_GROW = 'block.grow';
	const FLAG_EVENT_BLOCK_PLACE = 'block.place';
	const FLAG_EVENT_BLOCK_UPDATE = 'block.update';
	const FLAG_EVENT_ENTITY_DAMAGE = 'entity.damage';
	const FLAG_EVENT_ENTITY_EXPLOSION = 'entity.explosion';
	const FLAG_EVENT_ENTITY_REGAIN_HEALTH = 'entity.regain-health';
	const FLAG_EVENT_ENTITY_TELEPORT = 'entity.teleport';
	const FLAG_EVENT_PLAYER_CRAFT = 'player.craft';
	const FLAG_EVENT_PLAYER_FLIGHT = 'player.flight';
	const FLAG_EVENT_PLAYER_INTERACT = 'player.interact';
	const FLAG_EVENT_PLAYER_ITEM_DROP = 'player.item-drop';
	const FLAG_EVENT_PLAYER_ITEM_PICKUP = 'player.item-pickup';
	const FLAG_EVENT_PLAYER_HUNGER = 'player.hunger';
	const FLAG_EVENT_PLAYER_SPRINT = 'player.sprint';

	const FLAG_WHITELIST_APPLY_ITEMS = 'whitelist.apply-items';
	const FLAG_WHITELIST_APPLY_EFFECTS = 'whitelist.apply-effects';
}