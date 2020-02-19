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

namespace Labor\Helferlein\Php\EventBus;


use Labor\Helferlein\Php\Options\Options;
use Psr\Container\ContainerInterface;

class EventBus implements EventBusInterface {
	
	/**
	 * The class of the default event to create if no event is given
	 * @var string
	 */
	public $defaultEventClass = Event::class;
	
	/**
	 * The class of the default event subscription helper
	 * @var string
	 */
	public $eventSubscriptionClass = EventSubscription::class;
	
	/**
	 * The class of the default lazy proxy objects
	 * @var string
	 */
	public $eventSubscriptionLazyProxyClass = LazyEventSubscriptionProxy::class;
	
	/**
	 * The list of registered event handlers by their unique id
	 * @var array
	 */
	protected $handlers = [];
	
	/**
	 * The list of events by their event key
	 * @var array
	 */
	protected $events = [];
	
	/**
	 * The container instance to load lazy instances with
	 * @var ContainerInterface|null
	 */
	protected $container;
	
	/**
	 * The internal bus which is passed to event's and subscribers
	 * This should in the most cases be identical with $this
	 * @var \Labor\Helferlein\Php\EventBus\EventBusInterface
	 */
	protected $bus;
	
	/**
	 * If given, can be a callable which replaces the default event factory
	 * @var callable|null
	 */
	protected $eventFactory;
	
	/**
	 * EventBus constructor.
	 */
	public function __construct() {
		$this->bus = $this;
	}
	
	/**
	 * @inheritdoc
	 * @throws \Labor\Helferlein\Php\EventBus\InvalidEventException
	 * @throws \Labor\Helferlein\Php\Options\InvalidDefinitionException
	 * @throws \Labor\Helferlein\Php\Options\InvalidOptionException
	 */
	public function emit($event, array $options = []) {
		
		// Prepare options
		$options = Options::make($options, [
			"event" => [
				"type"    => ["null", EventInterface::class, "callable"],
				"default" => NULL,
			],
			"args"  => [
				"type"    => ["array"],
				"default" => [],
			],
		]);
		
		// Ignore if the event is not known
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string");
		if (empty($this->events[$event])) return $options["args"];
		
		// Create event object
		$e = $this->makeEvent($event, $options["event"], $options["args"]);
		
		// Loop over all handlers
		foreach ($this->events[$event] as $priority => $handlers) {
			foreach ($handlers as $handlerId) {
				$handler = $this->handlers[$handlerId];
				if (!is_callable($handler)) continue;
				call_user_func($handler, $e);
				if ($e->isPropagationStopped()) break;
			}
		}
		
		// Done
		return $e->getArgs();
	}
	
	/**
	 * @inheritdoc
	 * @throws \Labor\Helferlein\Php\EventBus\InvalidEventException
	 */
	public function bind($events, callable $handler, array $options = []) {
		// Bind multiple events
		if (is_array($events)) {
			foreach ($events as $event)
				$this->bind($event, $handler, $options);
			return $this->bus;
		}
		
		// Prepare options
		$options = Options::make($options, [
			"priority" => [
				"type"    => ["int"],
				"default" => 0,
			],
		]);
		
		// Bind a single event
		$event = $events;
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string, or array of strings!");
		$handlerId = $this->prepareHandler($event, $handler, TRUE);
		$this->events[$event][$options["priority"]][$handlerId] = $handlerId;
		ksort($this->events[$event]);
		$this->events[$event] = array_reverse($this->events[$event], TRUE);
		
		// Done
		return $this->bus;
	}
	
	/**
	 * @inheritdoc
	 * @throws \Labor\Helferlein\Php\EventBus\InvalidEventException
	 */
	public function unbind($events, callable $handler = NULL) {
		// Handle multiple events
		if (is_array($events)) {
			foreach ($events as $event)
				$this->unbind($event, $handler);
			return $this->bus;
		}
		
		// Handle a single event
		$event = (string)$events;
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string, or array of strings!");
		if (empty($this->events[$event])) return $this->bus;
		
		if ($handler === NULL) {
			// Remove all handlers
			foreach ($this->events[$event] as $priority => $handlers)
				foreach ($handlers as $handlerId)
					unset($this->handlers[$handlerId]);
			$this->events[$event] = NULL;
		} else {
			// Remove a single handler
			$handlerId = $this->prepareHandler($event, $handler, FALSE);
			foreach ($this->events[$event] as $priority => $handlers) {
				unset($this->events[$event][$priority][$handlerId]);
				if (empty($this->events[$event][$priority])) unset($this->events[$event][$priority]);
			}
			unset($this->handlers[$handlerId]);
		}
		
		// Cleanup
		if (empty($this->events[$event]))
			unset($this->events[$event]);
		
		// Done
		return $this->bus;
	}
	
	/**
	 * @inheritdoc
	 */
	public function addSubscriber(EventSubscriberInterface $instance) {
		/** @var \Labor\Helferlein\Php\EventBus\EventSubscriptionInterface $subscription */
		$subscription = new $this->eventSubscriptionClass($this->bus, $instance);
		$instance->subscribeToEvents($subscription);
		
		// Done
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function addLazySubscriber(string $lazySubscriberClass, ?callable $factory = NULL) {
		// Check if the class implements the required interface
		if (!in_array(LazyEventSubscriberInterface::class, class_implements($lazySubscriberClass)))
			throw new InvalidEventException("The given lazy subscriber: " . $lazySubscriberClass . " does not implement the required interface: " . LazyEventSubscriberInterface::class);
		
		// Make sure we have a factory
		if (!is_callable($factory)) {
			$factory = function (string $className, ?ContainerInterface $container) {
				if (!is_null($container) && $container->has($className)) return $container->get($className);
				return new $className();
			};
		}
		
		// Create an outer wrap around the factory
		$factoryWrap = function () use ($factory, $lazySubscriberClass) {
			return call_user_func($factory, $lazySubscriberClass, $this->container);
		};
		
		// Create the proxy
		$proxy = new $this->eventSubscriptionLazyProxyClass($factoryWrap);
		/** @var \Labor\Helferlein\Php\EventBus\EventSubscriptionInterface $subscription */
		$subscription = new $this->eventSubscriptionClass($this->bus, $proxy);
		call_user_func([$lazySubscriberClass, "subscribeToEvents"], $subscription);
		
		// Done
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setEventFactory(callable $factory) {
		$this->eventFactory = $factory;
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setContainer(ContainerInterface $container) {
		$this->container = $container;
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setBusInstance(EventBusInterface $bus) {
		$this->bus = $bus;
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getHandlersForEvent($event): array {
		// Skip if there are no handlers for this event
		if (empty($this->events[$event])) return [];
		
		// Flatten the priority list
		$list = [];
		foreach ($this->events[$event] as $priority => $handlers)
			foreach ($handlers as $handlerId)
				$list[] = $this->handlers[$handlerId];
		
		// Done
		return $list;
	}
	
	/**
	 * Used to convert a given handler callable into a unique string hash value.
	 *
	 * @param string                         $event   The event for which the handler is used
	 * @param callable|\Closure|object|array $handler The handler to convert into an id
	 * @param bool                           $store   If set to true, the handler will be registered in the $handlers
	 *                                                list
	 *
	 * @return string
	 */
	protected function prepareHandler(string $event, $handler, bool $store = FALSE): string {
		// Handle closures
		if (is_object($handler)) $handlerId = "a" . spl_object_hash($handler);
		// Handle array with object reference -> callable like [$obj, "method]
		// -> Save power, dont serialize the whole object
		else if (is_array($handler) && count($handler) === 2 && is_object($handler[0]))
			$handlerId = "b" . md5(serialize([spl_object_hash($handler[0]), $handler[1]]));
		// Serialize array with classname / method name
		else $handlerId = "c" . md5(serialize($handler));
		$handlerId = $event . $handlerId;
		if (!$store) return $handlerId;
		if (!isset($this->handlers[$handlerId])) $this->handlers[$handlerId] = $handler;
		return $handlerId;
	}
	
	/**
	 * Internal helper to create a new event instance,
	 * either using our default method or the supplied custom method
	 *
	 * @param string $event      The name of the event to instantiate the object for
	 * @param mixed  $givenEvent The given event we may want to use
	 * @param array  $args       The given arguments we should pass to the event
	 *
	 * @return \Labor\Helferlein\Php\EventBus\EventInterface
	 * @throws \Labor\Helferlein\Php\EventBus\InvalidEventException
	 */
	protected function makeEvent(string $event, $givenEvent, array $args): EventInterface {
		// Prepare the default factory
		$defaultFactory = function () use ($event, $args, $givenEvent) {
			if ($givenEvent === NULL) return new $this->defaultEventClass();
			if (is_callable($givenEvent)) return call_user_func($givenEvent, $this->bus, $event, $args);
			if (is_object($givenEvent)) return $givenEvent;
			if (is_string($givenEvent)) return new $givenEvent();
			throw new InvalidEventException("Could not instantiate a new event object!");
		};
		
		// Check if we have a custom factory or use the default
		if (!empty($this->eventFactory))
			$e = call_user_func($this->eventFactory, $this->bus, $event, $givenEvent, $args, $defaultFactory, $this->container);
		else
			$e = $defaultFactory();
		if (!$e instanceof EventInterface) throw new InvalidEventException("The given event object/class does not implement the EventInterface!");
		
		// Finalize the event
		$e->__initialize($this, $event, $args);
		return $e;
	}
	
}