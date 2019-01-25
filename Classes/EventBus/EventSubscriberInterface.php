<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:18
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


interface EventSubscriberInterface {
	
	/**
	 * Should add all subscribed events of this object to the given subscription using the subscribe() method.
	 *
	 * @param EventSubscriptionInterface $subscription
	 *
	 * @return void
	 */
	public function subscribeToEvents(EventSubscriptionInterface $subscription);
	
}