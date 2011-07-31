<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Tracks_Exception
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

/**
 * Handler Already Registered
 *
 * An attempt to register an event on a domain that has already been registered.
 *
 * @category  Tracks
 * @package   Tracks_Exception
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class Tracks_Exception_HandlerAlreadyRegistered extends Tracks_Exception_Base
{
    /**
     * Constructor
     *
     * @param string $domainClass     Classname of domain with the registered event
     * @param string $eventClass      Classname of event attempted to register
     * @param string $existingHandler Classname of Event Handler
     *
     * @return null
     */
    public function __construct($domainClass, $eventClass, $existingHandler)
    {
        assert('is_string($domainClass)');
        assert('is_string($eventClass)');
        assert('is_string($existingHandler)');
        parent::__construct(
        	'Handler already registered on object of type '.$domainClass
            .' for event '.$eventClass.': '.$existingHandler
        );
    }
}
