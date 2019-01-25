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
	 * @param array        $options  Additional options for this handler
	 *                               - params: ARRAY default([]) Additional parameters to be given to the handler.
	 *                               Please note, that the "on-params" will be given AFTER the "trigger-params".
	 *                               - priority: INT default(0) The priority of this handler. Positive and negative
	 *                               values are supported. The highest value is the lowest priority, the lowest
	 *                               (including negative) value is the highest priority.
	 *                               - once: BOOL default(FALSE) If set to true, this handler will only be executed
	 *                               once and then removed from the list. NOTE: This will ONLY remove the handler for a
	 *                               SINGLE (the currently called) event. If you want your handler to remove itself
	 *                               from more events, define this in your handler!
	 *
	 * @return \Labor\Helferlein\Php\EventBus\EventSubscriptionInterface
	 * @throws \Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException
	 */
	public function subscribe($events, string $method): EventSubscriptionInterface;
	
	/**
	 * Returns the event manager instance
	 *
	 * @return EventBusInterface
	 */
	public function getBus(): EventBusInterface;
}