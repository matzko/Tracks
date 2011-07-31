<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

/**
 * Interface for event routers
 *
 * @category  Tracks
 * @package   EventHandler
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
interface Tracks_EventHandler_IEventRouter
{
    /**
     * Route an event
     *
     * @param Tracks_Event_Base $event An Event
     *
     * @return null
     */
    public function route(Tracks_Event_Base $event);

    /**
     * Add an event handler to the routing table
     *
     * The 2nd argument may be either an instantiated object, or the name of a
     * class to instantiate. In the second case, the class should not have any
     * required parameters on it's constructor.
     *
     * @param string               $eventClass The Event classname
     * @param IEventHandler|string $handler    An EventHandler
     *
     * @return null
     */
	public function addHandler($eventClass, $handler);
}
