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
 * Repository to load and save domain event-based entities
 *
 * @category  Tracks
 * @package   EventStore
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class Tracks_EventStore_Repository
{

    const SNAPSHOT_FREQUENCY = 100;

    /**
     * Constructor
     *
     * @param IEventStore    $eventStore    The Event Store
     * @param Tracks_EventHandler_IEventRouter   $router        The Event Router
     * @param ISnapshotStore $snapshotStore The Snapshot Store
     *
     * @return null
     */
    public function __construct(
        IEventStore $eventStore,
        Tracks_EventHandler_IEventRouter $router,
        ISnapshotStore $snapshotStore
    ) {
        $this->eventStore = $eventStore;
        $this->snapshotStore = $snapshotStore;
        $this->router = $router;
        $this->snapshotFrequency = self::SNAPSHOT_FREQUENCY;
    }

    /**
     * Load an entity by a guid
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_AggregateRoot
     */
    public function load(Tracks_Model_Guid $guid)
    {
        if (is_null($aggregateRoot = $this->_loadEntity($guid))) {
            return null;
        }

        if (($children = $aggregateRoot->getAllChildEntities())) {
            foreach ($children as &$child) {
                $child = $this->_loadEntity($child->getGuid(), $child);
            }
        }

        return $aggregateRoot;
    }

    /**
     * Set the frequency with which to take snapshots of entities
     *
     * @param int $numEvents The snapshot threshhold as a max number of events
     *                       to allow before a snapshot happens
     *
     * @return null
     */
    public function setSnapshotFrequency($numEvents = self::SNAPSHOT_FREQUENCY)
    {
        assert('is_int($numEvents)');
        assert('$numEvents > 0');
        $this->snapshotFrequency = $numEvents;
    }

    /**
     * Save an aggregate root, and call event handlers for all new events
     *
     * @param Tracks_Model_AggregateRoot $aggregateRoot An Aggregate Root
     *
     * @return null
     */
    public function save(Tracks_Model_AggregateRoot $aggregateRoot)
    {
        if (count($aggregateRoot->getAllAppliedEvents()) == 0) {
            return;
        }

        $this->eventStore->save($aggregateRoot);
        $this->_routeEvents($aggregateRoot);
        $this->_updateVersionsAndClearEvents($aggregateRoot);
        $this->_saveSnapshots($aggregateRoot);
    }

    /**
     * Store an entity in the identity map
     *
     * @param Tracks_Model_Entity $entity An Entity
     *
     * @return null
     */
    private function _storeInIdentityMap(Tracks_Model_Entity $entity)
    {
        $this->_identityMap[(string) $entity->getGuid()] = $entity;
    }

    /**
     * Attempt to load an entity from the identity map
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_Entity
     */
    private function _loadFromIdentityMap(Tracks_Model_Guid $guid)
    {
        return isset($this->_identityMap[(string) $guid])
            ? $this->_identityMap[(string) $guid]
            : null;
    }

    /**
     * Load an entity from it's event history
     *
     * @param Tracks_Model_Guid   $guid   An Entity's GUID
     * @param Tracks_Model_Entity $entity That Entity
     *
     * @return Tracks_Model_Entity
     */
    private function _loadFromHistory(Tracks_Model_Guid $guid, Tracks_Model_Entity $entity = null)
    {
        $events = $this->eventStore->getAllEvents($guid);

        if (is_null($entity)) {
            if (is_null($modelClass = $this->eventStore->getType($guid))) {
                return null;
            }

            $entity = new $modelClass;
        }

        $entity->loadHistory($events);

        return $entity;
    }

    /**
     * Load an entity into memory
     *
     * @param Tracks_Model_Guid   $guid   An Entity's GUID
     * @param Tracks_Model_Entity $entity That Entity
     *
     * @return Tracks_Model_Entity
     */
    private function _loadEntity(Tracks_Model_Guid $guid, Tracks_Model_Entity $entity = null)
    {
        $loadedEntity = $this->_loadFromIdentityMap($guid);

        if (is_null($loadedEntity)) {
            $loadedEntity = $this->_loadFromSnapshot($guid);
        }

        if (is_null($loadedEntity)) {
            $loadedEntity = $this->_loadFromHistory($guid, $entity);
        }

        if (is_null($loadedEntity)) {
            return null;
        }

        $this->_storeInIdentityMap($loadedEntity);

        return $loadedEntity;
    }

    /**
     * Route all new events on an aggregate to their appropriate handlers
     *
     * @param Tracks_Model_AggregateRoot $aggregateRoot An Aggregate Root
     *
     * @return null
     */
    private function _routeEvents(Tracks_Model_AggregateRoot $aggregateRoot)
    {
        foreach ($aggregateRoot->getAllAppliedEvents() as $event) {
            $this->router->route($event);
        }
    }

    /**
     * Attempt to load an entity from a snapshot
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_Entity
     */
    private function _loadFromSnapshot(Tracks_Model_Guid $guid)
    {
        if (is_null($entity = $this->snapshotStore->load($guid))) {
            return null;
        }

        $entity->loadHistory(
            $this->eventStore->getEventsFromVersion(
                $entity->getGuid(),
                $entity->getVersion() + 1
            )
        );

        return $entity;
    }

    /**
     * Update an entity's in-memory version and clear applied events
     *
     * This should be called after an entity's events have been saved.
     *
     * @param Tracks_Model_AggregateRoot $aggregateRoot An Aggregate Root
     *
     * @return null
     */
    private function _updateVersionsAndClearEvents(Tracks_Model_AggregateRoot $aggregateRoot)
    {
        foreach ($aggregateRoot->getAllEntities() as $entity) {
            $entity->incVersion(count($entity->getAppliedEvents()));
            $entity->clearAppliedEvents();
        }
    }

    /**
     * Attempt to save a snapshot for all entites in an aggregate root
     *
     * @param Tracks_Model_AggregateRoot $aggregateRoot An Aggregate Root
     *
     * @return null
     */
    private function _saveSnapshots(Tracks_Model_AggregateRoot $aggregateRoot)
    {
        foreach ($aggregateRoot->getAllEntities() as $entity) {
            $this->_saveSnapshot($entity);
        }
    }

    /**
     * Try to save a snapshot of an entity if frequency determines it is time
     *
     * @param Tracks_Model_Entity $entity An Entity
     *
     * @return null
     */
    private function _saveSnapshot(Tracks_Model_Entity $entity)
    {
        $state = clone $entity;
        $state->clearAppliedEvents();

        $snapshot = $this->snapshotStore->load($state->getGuid());

        if ((!$snapshot && $state->getVersion() >= $this->_snapshotFrequency)
            || ($snapshot && ($state->getVersion() - $snapshot->getVersion()) >= $this->_snapshotFrequency)
        ) {
            $this->snapshotStore->save($entity);
        }
    }

    /** @var int */
    private $_snapshotFrequency;

    /** @var array */
    private $_identityMap = array();
}
