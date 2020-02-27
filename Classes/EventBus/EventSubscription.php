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


use Neunerlei\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException;

class EventSubscription implements EventSubscriptionInterface {
	
	/**
	 * @var \Neunerlei\Helferlein\Php\EventBus\EventBusInterface
	 */
	protected $bus;
	
	/**
	 * @var \Neunerlei\Helferlein\Php\EventBus\EventSubscriberInterface
	 */
	protected $subscriber;
	
	/**
	 * @inheritDoc
	 */
	public function __construct(EventBusInterface $bus, EventSubscriberInterface $subscriber) {
		$this->bus = $bus;
		$this->subscriber = $subscriber;
	}
	
	/**
	 * @inheritDoc
	 */
	public function subscribe($events, string $method, array $options = []): EventSubscriptionInterface {
		if (!$this->subscriber instanceof LazyEventSubscriptionProxy && !method_exists($this->subscriber, $method))
			throw new HelferleinInvalidArgumentException("Could not subscribe method: \"" . $method .
				"\" to handle an event, because it is not publicly available");
		
		$this->bus->bind($events, [$this->subscriber, $method], $options);
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBus(): EventBusInterface {
		return $this->bus;
	}
	
}