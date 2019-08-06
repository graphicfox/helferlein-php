<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:22
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


interface EventSubscriptionInterface {
	
	/**
	 * SubscriptionManager constructor.
	 *
	 * @param EventBusInterface        $bus
	 * @param EventSubscriberInterface $subscriber
	 */
	public function __construct(EventBusInterface $bus, EventSubscriberInterface $subscriber);
	
	/**
	 * Subscribes a given method to the list of events
	 *
	 * @param array|string $events   The events to trigger this callback for. Multiple events can be separated via
	 *                               empty space, or by using an array of multiple values
	 * @param string       $method   The name of a method that should be subscribed to the given events.
	 *                               NOTE: The method has to be public and available on the current instance
	 * @param array        $options  Additional options
	 *                               - priority: int (0) Can be used to define the order of handlers when bound on the
	 *                               same event. 0 is the default the + range is a higher priority (earlier) the - range
	 *                               is a lower priority (later)
	 *
	 * @return \Labor\Helferlein\Php\EventBus\EventSubscriptionInterface
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public function subscribe($events, string $method, array $options = []): EventSubscriptionInterface;
	
	/**
	 * Returns the event manager instance
	 *
	 * @return EventBusInterface
	 */
	public function getBus(): EventBusInterface;
}