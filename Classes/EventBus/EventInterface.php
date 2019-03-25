<?php
/**
 * User: Martin Neundorfer
 * Date: 23.01.2019
 * Time: 13:05
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php\EventBus;


interface EventInterface {
	
	/**
	 * To keep the constructor clean of elements to inject we call the __initialize method before we
	 * dispatch the event to the handlers. This should make the creation of child event classes a lot easier
	 *
	 * @param \Labor\Helferlein\Php\EventBus\EventBusInterface $bus      The instance of the calling event bus
	 * @param string                                           $eventKey The name of the current event
	 * @param array                                            $args     Arguments that were passed to this event
	 *
	 * @return mixed
	 */
	public function __initialize(EventBusInterface $bus, string $eventKey, array $args);
	
	/**
	 * Returns the instance of the calling event bus
	 * @return \Labor\Helferlein\Php\EventBus\EventBusInterface
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
	 * @return \Labor\Helferlein\Php\EventBus\EventInterface
	 */
	public function setArgs(array $args): EventInterface;
	
	/**
	 * Returns the current arguments for this event as a reference.
	 *
	 * @return mixed
	 */
	public function &getArgs();
	
}