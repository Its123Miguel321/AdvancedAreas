<?php

namespace Its123Miguel321\AdvancedAreas\area\traits;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\lang\Translatable;
use pocketmine\Server;

trait EffectsHandlingTrait{

	protected array $effects = [];
	protected bool $effectsForWhitelist = false;

	public function hasEffect(EffectInstance|string $effect) : bool{
		if($effect instanceof EffectInstance){
			$effect = ($effect->getType()->getName() instanceof Translatable ? Server::getInstance()->getLanguage()->translate($effect->getType()->getName()) : $effect->getType()->getName()) . '-' . $effect->getEffectLevel();
		}

		$effect = strtolower(str_replace(' ', '_', $effect));

		return in_array($effect, $this->effects);
	}

	public function addEffect(EffectInstance|string $effect) : self{
		if($effect instanceof EffectInstance){
			$effect = ($effect->getType()->getName() instanceof Translatable ? Server::getInstance()->getLanguage()->translate($effect->getType()->getName()) : $effect->getType()->getName()) . '-' . $effect->getEffectLevel();
		}

		$effect = strtolower(str_replace(' ', '_', $effect));

		$this->effects[] = $effect;
		return $this;
	}

	public function setEffects(array $effects) : self{
		$this->resetEffects();

		foreach($effects as $effect){
			$this->addEffect($effect);
		}
		return $this;
	}

	public function removeEffect(EffectInstance|string $effect) : self{
		if($effect instanceof EffectInstance){
			$effect = ($effect->getType()->getName() instanceof Translatable ? Server::getInstance()->getLanguage()->translate($effect->getType()->getName()) : $effect->getType()->getName()) . '-' . $effect->getEffectLevel();
		}

		$effect = strtolower(str_replace(' ', '_', $effect));

		unset($this->effects[array_search($effect, $this->effects)]);
		return $this;
	}

	public function resetEffects() : self{
		$this->effects = [];
		return $this;
	}

	public function getEffects() : array{
		return $this->effects;
	}

	public function getEffectsApplyToWhitelist() : bool{
		return $this->effectsForWhitelist;
	}

	public function setEffectsApplyToWhitelist(bool $apply) : void{
		$this->effectsForWhitelist = $apply;
	}
}