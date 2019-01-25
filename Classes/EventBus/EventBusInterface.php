<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:06
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


interface EventBusInterface {
	/**
	 * Emits one, or multiple events.
	 * This will call all bound handlers registered using the bind() method.
	 *
	 * @param array|string $event   Either an event as a string, or a list of events as array of string
	 * @param array        $options Additional options:
	 *                              - event: string|object Can be used to override the default Event instance using
	 *                              either a class or object of type EventInterface
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
	 *
	 * @return mixed
	 */
	public function bind($events, callable $handler);
	
	/**
	 * Removes either a single handler, or all handlers from the given events
	 *
	 * @param array|string $events  Either an event as a string, or a list of events as array of string
	 * @param callable|NULL $handler A callback which was previously registered using bind()
	 *
	 * @return mixed
	 */
	public function unbind($events, callable $handler = NULL);
	
	/**
	 * Adds the handlers registered in an eventsubscriber to the event bus
	 *
	 * @param \Labor\Helferlein\Php\EventBus\EventSubscriberInterface $instance
	 *
	 * @return mixed
	 */
	public function addSubscriber(EventSubscriberInterface $instance);
}