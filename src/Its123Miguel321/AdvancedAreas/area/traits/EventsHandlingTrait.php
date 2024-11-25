<?php

namespace Its123Miguel321\AdvancedAreas\area\traits;

trait EventsHandlingTrait{

	protected array $events = [];

	public function getEventValue(string $event) : bool{
		return $this->events[$event];
	}

	public function setEventValue(string $event, bool $value) : self{
		$this->events[$event] = $value;
		return $this;
	}

	public function toggleEventValue(string $event) : self{
		$this->setEventValue($event, !$this->getEventValue($event));
		return $this;
	}

	public function setEvents(array $events) : self{
		$this->events = $events;
		return $this;
	}

	public function getEvents() : array{
		return $this->events;
	}
}