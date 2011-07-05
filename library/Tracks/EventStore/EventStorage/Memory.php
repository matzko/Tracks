<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage EventStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore\EventStorage;
use \Tracks\EventStore\IEventStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

/**
 * In-Memory event store implementation
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage EventStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class Memory implements IEventStore
{

    /**
     * Get all the events associated with an entity by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return array
     */
    public function getAllEvents(Guid $guid)
    {
        return $this->_events[(string) $guid];
    }

    /**
     * Get all events associated with an entity starting from a particular version
     *
     * @param Guid $guid    An Entity's GUID
     * @param int  $version That Entity's version number
     *
     * @return array
     */
    public function getEventsFromVersion(Guid $guid, $version)
    {
        assert('is_int($version)');
        return array_slice($this->_events[(string) $guid], $version);
    }

    /**
     * Save an entity and it's events
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity)
    {
        foreach ($entity->getAllEntities() as $provider) {
            $this->_createEntity($provider);
        }

        foreach ($entity->getAllAppliedEvents() as $event) {
            $this->events[(string) $event->getGuid()][] = clone $event;
            $this->_incVersion($event->getGuid());
        }
    }

    /**
     * Get the object type associated with a guid
     *
     * @param Guid $guid Any GUID
     *
     * @return string
     */
    public function getType(Guid $guid)
    {
        return $this->entities[(string) $guid]['type'];
    }

    /**
     * Create a new entity entry if it doesn't already exist
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    private function _createEntity(Entity $entity)
    {
        if (!isset($this->_entities[(string) $entity->getGuid()])) {
            $this->_entities[(string) $entity->getGuid()] = array(
            'type' => get_class($entity),
            'version' => 0,
            'guid' => $entity->getGuid());
        }

        if (!isset($this->_events[(string) $entity->getGuid()])) {
            $this->_events[(string) $entity->getGuid()] = array();
        }
    }

    /**
     * Increment the version of an entity
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return null
     */
    private function _incVersion(Guid $guid)
    {
        isset($this->entities[(string) $guid])
            ? $this->entities[(string) $guid]['version']++
            : $this->entities[(string) $guid] = array('version' => 1);
    }

    /**
     * @var array
     */
    private $_events = array();

    /**
     * @var array
     */
    private $_entities = array();
}
