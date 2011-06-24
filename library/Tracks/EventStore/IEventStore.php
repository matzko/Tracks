<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   EventStore
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

/**
 * Interface for event stores
 *
 * @category  Tracks
 * @package   EventStore
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
interface IEventStore
{

    /**
     * Get all the events associated with an entity by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return array
     */
    public function getAllEvents(Guid $guid);

    /**
     * Get all events associated with an entity starting from a particular version
     *
     * @param Guid $guid    An Entity's GUID
     * @param int  $version That Entity's version number
     *
     * @return array
     */
    public function getEventsFromVersion(Guid $guid, $version);

    /**
     * Get the object type associated with a guid
     *
     * @param Guid $guid Any GUID
     *
     * @return string
     */
    public function getType(Guid $guid);

    /**
     * Save an entity and it's events
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity);
}
