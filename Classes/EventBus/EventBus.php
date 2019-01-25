<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:25
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


use Labor\Helferlein\Php\Options;

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
	 * The list of registered event handlers by their unique id
	 * @var array
	 */
	protected $handlers = [];
	
	/**
	 * The list of events by their event key
	 * @var array
	 */
	protected $events = [];
	
	public function emit($event, array $options = []) {
		
		// Prepare options
		$options = Options::make($options, [
			"event" => NULL,
			"args"  => [],
		]);
		
		// Ignore if the event is not known
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string");
		if (empty($this->events[$event])) return $options["args"];
		
		// Create event object
		if ($options["event"] === NULL) $e = new $this->defaultEventClass($this, $event, $options["args"]);
		else if (is_object($options["event"])) $e = $options["event"];
		else if (is_string($options["event"])) $e = new $options["event"]($this, $event, $options["args"]);
		if (!$e instanceof EventInterface) throw new InvalidEventException("The given event object/class does not implement the EventInterface!");

		// Loop over all handlers
		foreach ($this->events[$event] as $handlerId) {
			$handler = $this->handlers[$handlerId];
			if (!is_callable($handler)) continue;
			call_user_func($handler, $e);
			if ($e->isPropagationStopped()) break;
		}
		
		// Done
		return $e->getArgs();
	}
	
	public function bind($events, callable $handler) {
		
		// Handle multiple events
		if (is_array($events)) {
			foreach ($events as $event)
				static::bind($event, $handler);
			return;
		}
		
		// Handle a single event
		$event = $events;
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string, or array of strings!");
		$handlerId = static::prepareHandler($event, $handler, TRUE);
		$this->events[$event][$handlerId] = $handlerId;
	}
	
	public function unbind($events, callable $handler = NULL) {
		// Handle multiple events
		if (is_array($events)) {
			foreach ($events as $event)
				static::unbind($event, $handler);
			return;
		}
		
		// Handle a single event
		$event = (string)$events;
		if (!is_string($event)) throw new InvalidEventException("Events can only be defined as string, or array of strings!");
		if (empty($this->events[$event])) return;
		
		if ($handler === NULL) {
			// Remove all handlers
			foreach ($this->events[$event] as $handlerId)
				unset($this->handlers[$handlerId]);
		} else {
			// Remove a single handler
			$handlerId = static::prepareHandler($event, $handler, FALSE);
			unset($this->events[$event][$handlerId]);
			unset($this->handlers[$handlerId]);
		}
		
		// Cleanup
		if (empty($this->events[$event]))
			unset($this->events[$event]);
	}
	
	public function addSubscriber(EventSubscriberInterface $instance) {
		/** @var \Labor\Helferlein\Php\EventBus\EventSubscriptionInterface $subscription */
		$subscription = new $this->eventSubscriptionClass($this, $instance);
		$instance->subscribeToEvents($subscription);
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
		// Handle array with object reference -> Save power, dont serialize the whole object
		else if (is_array($handler) && is_object($handler[0]))
			$handlerId = "b" . md5(serialize([spl_object_hash($handler[0]), $handler[1]]));
		// Serialize array with classname / methodname
		else $handlerId = "c" . md5(serialize($handler));
		$handlerId = $event . $handlerId;
		if (!$store) return $handlerId;
		if (!isset($this->handlers[$handlerId])) $this->handlers[$handlerId] = $handler;
		return $handlerId;
	}
}