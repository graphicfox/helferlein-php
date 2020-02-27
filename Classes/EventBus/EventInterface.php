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


interface EventInterface {
	
	/**
	 * To keep the constructor clean of elements to inject we call the __initialize method before we
	 * dispatch the event to the handlers. This should make the creation of child event classes a lot easier
	 *
	 * @param \Neunerlei\Helferlein\Php\EventBus\EventBusInterface $bus      The instance of the calling event bus
	 * @param string                                               $eventKey The name of the current event
	 * @param array                                                $args     Arguments that were passed to this event
	 *
	 * @return mixed
	 */
	public function __initialize(EventBusInterface $bus, string $eventKey, array $args);
	
	/**
	 * Returns the instance of the calling event bus
	 * @return \Neunerlei\Helferlein\Php\EventBus\EventBusInterface
	 */
	public function getBus(): EventBusInterface;
	
	/**
	 * Returns the current event name.
	 *
	 * @return string
	 */
	public function getEventKey(): string;
	
	/**
	 * Use this method if you want to stop the propagation of the event.
	 *
	 * @return EventInterface
	 */
	public function stopPropagation(): EventInterface;
	
	/**
	 * Returns true if the propagation of this event should be stopped
	 *
	 * @return bool
	 */
	public function isPropagationStopped(): bool;
	
	/**
	 * Sets the arguments by removing the old ones
	 *
	 * @param array $args
	 *
	 * @return \Neunerlei\Helferlein\Php\EventBus\EventInterface
	 */
	public function setArgs(array $args): EventInterface;
	
	/**
	 * Returns the current arguments for this event as a reference.
	 *
	 * @return mixed
	 */
	public function &getArgs();
	
}