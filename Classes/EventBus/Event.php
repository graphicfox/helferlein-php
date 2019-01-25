<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:49
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


class Event implements EventInterface {
	
	/**
	 * The executing event bus
	 * @var \Labor\Helferlein\Php\EventBus\EventBusInterface
	 */
	protected $bus;
	
	/**
	 * The event key
	 * @var string
	 */
	protected $eventKey;
	
	/**
	 * The passed arguments for this event
	 * @var array
	 */
	protected $args;
	
	/**
	 * True if the propagation of the event was stopped
	 * @var bool
	 */
	protected $stopPropagation = false;
	
	/**
	 * @inheritDoc
	 */
	public function __construct(EventBusInterface $bus, string $eventKey, array $args) {
		$this->eventKey = $eventKey;
		$this->args = $args;
		$this->bus = $bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getBus(): EventBusInterface {
		return $this->bus;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEventKey(): string {
		return $this->eventKey;
	}
	
	/**
	 * @inheritDoc
	 */
	public function stopPropagation(): EventInterface {
		$this->stopPropagation = true;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isPropagationStopped(): bool {
		return $this->stopPropagation;
	}
	
	/**
	 * @inheritDoc
	 */
	public function setArgs(array $args): EventInterface{
		$this->args = $args;
		return $this;
	}
	
	/**
	 * @inheritDoc
	 */
	public function &getArgs() {
		return $this->args;
	}
	
}