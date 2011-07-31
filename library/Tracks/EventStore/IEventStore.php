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
interface Tracks_EventStore_IEventStore
{

    /**
     * Get all the events associated with an entity by guid
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return array
     */
    public function getAllEvents(Tracks_Model_Guid $guid);

    /**
     * Get all events associated with an entity starting from a particular version
     *
     * @param Tracks_Model_Guid $guid    An Entity's GUID
     * @param int  $version That Entity's version number
     *
     * @return array
     */
    public function getEventsFromVersion(Tracks_Model_Guid $guid, $version);

    /**
     * Get the object type associated with a guid
     *
     * @param Tracks_Model_Guid $guid Any GUID
     *
     * @return string
     */
    public function getType(Tracks_Model_Guid $guid);

    /**
     * Save an entity and it's events
     *
     * @param Tracks_Model_Entity $entity An Entity
     *
     * @return null
     */
    public function save(Tracks_Model_Entity $entity);
}
