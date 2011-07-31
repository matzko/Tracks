<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

/**
 * Domain Entity base class
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
abstract class Tracks_Model_Entity
{

    /**
     * Load an array of events onto this entity
     *
     * @param array $history Set of Events
     *
     * @return null
     */
    public function loadHistory(array $history)
    {
        foreach ($history as $event) {
            $this->handleDomainEvent($event);
            $this->version++;
        }
    }

    /**
     * Get this entity's guid
     *
     * @return Tracks_Model_Guid
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Get this entity's current version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Increment this entity's version
     *
     * @param int $increment Amount by which to increment the Entity's version
     *
     * @return null
     */
    public function incVersion($increment = 1)
    {
        assert('is_int($increment)');
        $this->version += $increment;
    }

    /**
     * Get all newly applied events to this entity
     *
     * @return array
     */
    public function getAppliedEvents()
    {
        return $this->appliedEvents;
    }

    /**
     * Get all newly applied events to this entity and it's children
     *
     * @return array
     */
    public function getAllAppliedEvents()
    {
        $events = array();

        foreach ($this as $property) {
            if ($property instanceof Tracks_Model_Entity || $property instanceof Tracks_Model_EntityList) {
                $events = array_merge($events, $property->getAllAppliedEvents());
            }
        }

        return array_merge($this->appliedEvents, $events);
    }

    /**
     * Clear all applied events on this entity
     *
     * @return null
     */
    public function clearAppliedEvents()
    {
        $this->appliedEvents = array();
    }

    /**
     * Clear all applied events on this entity and it's children
     *
     * @return null
     */
    public function clearAllAppliedEvents()
    {
        foreach ($this as $property) {
            if ($property instanceof Entity || $property instanceof Tracks_Model_EntityList) {
                $property->clearAllAppliedEvents();
            }
        }

        $this->appliedEvents = array();
    }

    /**
     * Has this entity been created?
     *
     * @return boolean
     */
    public function isCreated()
    {
        return !empty($this->guid);
    }

    /**
     * Has this entity been deleted?
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Get all child entites of this entity
     *
     * @return array
     */
    public function getAllChildEntities()
    {
        $entities = array();

        foreach ($this as $property) {
            if ($property instanceof Entity || $property instanceof Tracks_Model_EntityList) {
                $entities = array_merge($entities, $property->getAllEntities());
            }
        }

        return $entities;
    }

    /**
     * Get all entities in the graph, starting from this one
     *
     * @return array
     */
    public function getAllEntities()
    {
        return array_merge(array($this), $this->getAllChildEntities());
    }

    /**
     * Find a handler method registered with an event and call it if it exists
     *
     * @param Tracks_Event_Base $event An Event
     *
     * @return null
     */
    protected function handleDomainEvent(Tracks_Event_Base $event)
    {
        if ($handler_name = $this->_getHandlerName($event)) {
            $this->$handler_name($event);
        }
    }

    /**
     * Apply a new event to this domain object
     *
     * @param Tracks_Event_Base $event An Event
     *
     * @return null
     */
    protected function applyEvent(Tracks_Event_Base $event)
    {
        $this->handleDomainEvent($event);
        $this->appliedEvents[] = $event;
    }

    /**
     * Register an event handler on this domain object
     *
     * @param string $eventName     Classname of an Event
     * @param string $handlerMethod Method name to register
     *
     * @return null
     */
    protected function registerEvent($eventName, $handlerMethod)
    {
        assert('is_string($eventName)');
        assert('is_string($handlerMethod)');
        assert('method_exists($this, $handlerMethod)');

        if (isset($this->handlers[$eventName])) {
            throw new Tracks_Exception_HandlerAlreadyRegistered(
                get_class($this),
                $eventName,
                $this->handlers[$eventName]
            );
        }

        $this->handlers[$eventName] = $handlerMethod;
    }

    /**
     * Get the name of the handler method for an event
     *
     * @param Tracks_Event_Base $event An Event
     *
     * @return string
     */
    private function _getHandlerName(Tracks_Event_Base $event)
    {
        return isset($this->handlers[get_class($event)])
            ? $this->handlers[get_class($event)]
            : null;
    }

    /** @var Tracks_Model_Guid */
    protected $guid;

    /** @var boolean */
    protected $deleted = false;

    /** @var int */
    protected $version = 0;

    /** @var array */
    protected $appliedEvents = array();

    /** @var array */
    protected $handlers = array();
}

