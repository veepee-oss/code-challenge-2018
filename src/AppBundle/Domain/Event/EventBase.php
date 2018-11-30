<?php

namespace AppBundle\Domain\Event;

/**
 * Interface EventBase
 *
 * @package AppBundle\Domain\Event
 */
interface EventBase
{
    /**
     * Serializes the event into a string
     *
     * @return string
     */
    public function serialize() : string;

    /**
     * Unserializes the event from a string
     *
     * @param string $event
     */
    public function unserialize(string $event) : void;

    /**
     * Returns the type of the event
     *
     * @return string
     */
    public function type() : string;

    /**
     * Returns the UUID of the event
     *
     * @return string
     */
    public function uuid() : string;

    /**
     * Returns the timestamp of the event
     *
     * @return \DateTime
     */
    public function timestamp() : \DateTime;
}
