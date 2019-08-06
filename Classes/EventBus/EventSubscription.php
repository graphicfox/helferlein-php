<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 14:17
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


use Labor\Helferlein\Php\Exceptions\HelferleinInvalidArgumentException;

class EventSubscription implements EventSubscriptionInterface {
	
	/**
	 * @var \Labor\Helferlein\Php\EventBus\EventBusInterface
	 */
	protected $bus;
	
	/**
	 * @var \Labor\Helferlein\Php\EventBus\EventSubscriberInterface
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