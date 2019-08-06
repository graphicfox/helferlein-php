<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:06
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


use Psr\Container\ContainerInterface;

interface EventBusInterface {
	/**
	 * Emits one, or multiple events.
	 * This will call all bound handlers registered using the bind() method.
	 *
	 * @param array|string $event   Either an event as a string, or a list of events as array of string
	 * @param array        $options Additional options:
	 *                              - event: string|object|callable Can be used to override the default Event instance
	 *                              using either a class or object of type EventInterface
	 *                              - args: array A list of arguments which will be passed to all handlers and can be
	 *                              read/filtered using the $event->getArgs() method
	 *
	 * @return array
	 */
	public function emit($event, array $options = []);
	
	/**
	 * Binds a handler to a single, or multiple events
	 *
	 * @param array|string $events  Either an event as a string, or a list of events as array of string
	 * @param callable     $handler A callback which is executed when the matching event is emitted
	 * @param array        $options Additional options
	 *                              - priority: int (0) Can be used to define the order of handlers when bound on the
	 *                              same event. 0 is the default the + range is a higher priority (earlier) the - range
	 *                              is a lower priority (later)
	 *
	 * @return $this
	 */
	public function bind($events, callable $handler, array $options = []);
	
	/**
	 * Removes either a single handler, or all handlers from the given events
	 *
	 * @param array|string  $events  Either an event as a string, or a list of events as array of string
	 * @param callable|NULL $handler A callback which was previously registered using bind()
	 *
	 * @return $this
	 */
	public function unbind($events, callable $handler = NULL);
	
	/**
	 * Adds the handlers registered in an event subscriber to the event bus
	 *
	 * @param \Labor\Helferlein\Php\EventBus\EventSubscriberInterface $instance
	 *
	 * @return $this
	 */
	public function addSubscriber(EventSubscriberInterface $instance);
	
	/**
	 * Adds the handlers registered in an event subscriber to the event bus
	 *
	 * @param string        $lazySubscriberClass The class which should be subscribed to the events
	 * @param callable|null $factory             An optional factory to create the subscriber class with when it is
	 *                                           required It will receive the name of the class, the instance of the
	 *                                           bus and if set the instance of the container
	 *
	 * @return $this
	 */
	public function addLazySubscriber(string $lazySubscriberClass, ?callable $factory = NULL);
	
	/**
	 * Can be used to change the way how the event objects are created internally.
	 * The callback will retrieve the event bus, event name, the given event, the given arguments and the original
	 * factory and should return an object of type EventInterface
	 *
	 * @param callable $factory
	 *
	 * @return $this
	 */
	public function setEventFactory(callable $factory);
	
	/**
	 * Returns the list of all event handlers (ordered by their priority; high to low) for a single event
	 *
	 * @param $event
	 *
	 * @return array
	 */
	public function getHandlersForEvent($event): array;
	
	/**
	 * If the container is given, it will be used to retrieve the lazy subscriber instances when required.
	 * It will also be provided as forth parameter when you supply a custom $factory to setEventFactory()
	 *
	 * @param \Psr\Container\ContainerInterface $container
	 *
	 * @return $this
	 */
	public function setContainer(ContainerInterface $container);
	
	/**
	 * Can be used to override the event bus instance that is passed to events or subscriber instances.
	 * This becomes quite useful if you want to use the bus inside a wrapper class.
	 *
	 * @param \Labor\Helferlein\Php\EventBus\EventBusInterface $bus
	 *
	 * @return $this
	 */
	public function setBusInstance(EventBusInterface $bus);
}