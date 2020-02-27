<?php
/**
 * Copyright 2020 Martin Neundorfer (Neunerlei)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Last modified: 2020.02.27 at 10:42
 */

namespace Neunerlei\Helferlein\Php\EventBus;


class Event implements EventInterface {
	
	/**
	 * True if the __initialize method was called
	 * @var bool
	 */
	protected $initialized = FALSE;
	
	/**
	 * The executing event bus
	 * @var \Neunerlei\Helferlein\Php\EventBus\EventBusInterface
	 */
	protected $bus;
	
	/**
	 * The event key
	 * @var string
	 */
	protected $eventKey;
	
	/**
	 * The passed arguments for this event
	 * @var array
	 */
	protected $args;
	
	/**
	 * True if the propagation of the event was stopped
	 * @var bool
	 */
	protected $stopPropagation = FALSE;
	
	/**
	 * @inheritDoc
	 */
	public function __initialize(EventBusInterface $bus, string $eventKey, array $args) {
		if ($this->initialized) return;
		$this->initialized = TRUE;
		$this->eventKey = $eventKey;
		$this->args = $args;
		$this->bus = $bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBus(): EventBusInterface {
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEventKey(): string {
		return $this->eventKey;
	}
	
	/**
	 * @inheritDoc
	 */
	public function stopPropagation(): EventInterface {
		$this->stopPropagation = TRUE;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isPropagationStopped(): bool {
		return $this->stopPropagation;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setArgs(array $args): EventInterface {
		$this->args = $args;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function &getArgs() {
		return $this->args;
	}
}