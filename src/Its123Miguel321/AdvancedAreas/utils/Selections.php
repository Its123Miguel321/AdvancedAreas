<?php

namespace Its123Miguel321\AdvancedAreas\utils;

use pocketmine\world\Position;

class Selections{
	
	public function __construct(
		private ?Position $posA = null,
		private ?Position $posB = null
	){}

	public function getPosA() : ?Position{ return $this->posA; }

	public function getPosB() : ?Position{ return $this->posB; }
	

	public function setPosA(?Position $pos = null) : void{
		$this->posA = $pos;
	}

	
	public function setPosB(?Position $pos = null) : void{
		$this->posB = $pos;
	}
}