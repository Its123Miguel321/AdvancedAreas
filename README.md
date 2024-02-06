## General
A PocketMine-MP plugin which lets you create advanced areas!

## Features
### Priority
Every area has its own priority. 
### Control Events
You can disable/enable events for each area!
- Block Breaking
- Block Placing
- Item Drop
- Item Pickup
- Entity Damage
- Entity Regain Health
- Much more events...!

### Effects
Effects are given when entering the area and cleared when they leave! You can also set it so it applies to the whitelisted player's!

### Banned Items
If an item is banned in the area, they can not be used! You can also set it so it applies to the whitelisted player's!

### Whitelist
Every area has its own whitelist!

## Config
```yml
# Configuration file for AdvancedAreas by Its123Miguel321

# Config file version (Do not edit this!)
config-version: 1.0


# The data provider is where the area data is stored.
# Accepted types of data providers: yaml, json, and sqlite
data-provider: json


# The AdvancedAreas wand item.
# Default wand item: feather
wand-item: feather


# When entering an area, the name will be shown
show-area-name: true
# How the area name will be shown
# Accepted types: title, popup, message
area-name-type: popup


# These are the default settings for areas!
# DO NOT remove any of the events!
default-area-settings:
 priority: 5
 banned-items: []
 events:
  block.break: true
  block.decay: true
  block.grow: true
  block.place: true
  block.update: true
  entity.damage: true
  entity.explosion: true
  entity.regain-health: true
  entity.teleport: true
  player.craft: true
  player.flight: true
  player.interact: true
  player.item-drop: true
  player.item-pickup: true
  player.sprint: true
  player.hunger: true
 effects: []
 whitelist: []
```

## For Developers
These are the most used parts of the API!

Checks if a position is inside an area!
```php
AreasAPI::isInside(Position $pos);
```

Checks if a position is in an area by name!
```php
AreasAPI::inAreaByName(Position $pos, string $name);
```

Checks if the current position is still in the same area!
```php
AreasAPI::inSameArea(Position $current, Area $previous);
```

Get the areas the position is in!
```php
$areas = AreasAPI::getAreasIn(Position $pos);
```

How to create areas!
```php
AdvancedAreas::getInstance()->getProvider()->create(
	string $displayName, 
	Selections $selections, 
	int $priority = -100, 
	array $whitelist = [], 
	array $events = [], 
	array $effects = [], 
	array $items = [], 
	bool $itemsForWhitelist = false, 
	bool $effectsForWhitelist = false
);
```

How to delete areas!
```php
AdvancedAreas::getInstance()->getProvider()->delete(Area $area);
```

How to get all areas!
```php
AdvancedAreas::getInstance()->getProvider()->getAll();
```

## Suggestions
If you have any suggestions, contact me on discord! **(Its123Miguel321)**

## Bug Report
If you have found a bug, please create an [Issue](https://github.com/Its123Miguel321/AdvancedAreas/issues/new)!