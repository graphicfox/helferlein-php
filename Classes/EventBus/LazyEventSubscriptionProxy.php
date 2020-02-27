<?php
/**
 * Copyright 2020 LABOR.digital
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
 * Last modified: 2019.09.12 at 13:38
 */

namespace Neunerlei\Helferlein\Php\EventBus;


class LazyEventSubscriptionProxy implements EventSubscriberInterface {
	/**
	 * The factory to create the instance with
	 * @var callable
	 */
	protected $factory;
	
	/**
	 * The instance of the event subscriber after it was required by at least one event
	 * @var mixed
	 */
	protected $instance;
	
	/**
	 * LazyEventSubscriptionProxy constructor.
	 *
	 * @param callable $factory
	 */
	public function __construct(callable $factory) {
		$this->factory = $factory;
	}
	
	/**
	 * Fake the interface...
	 *
	 * @param \Neunerlei\Helferlein\Php\EventBus\EventSubscriptionInterface $subscription
	 */
	public function subscribeToEvents(EventSubscriptionInterface $subscription) {
		// Silence
	}
	
	/**
	 * Handle the request proxying...
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		if (empty($this->instance)) $this->instance = call_user_func($this->factory);
		return call_user_func_array([$this->instance, $name], $arguments);
	}
}