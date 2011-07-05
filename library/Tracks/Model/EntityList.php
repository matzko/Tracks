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

namespace Tracks\Model;
use Tracks\Model\Entity;

/**
 * Entity list domain object
 *
 * Should be used to model a list of entities instead of a plain array.
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class EntityList
implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * Add an entity to the list
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function add(Entity $entity)
    {
        $this->_items[(string) $entity->getGuid()] = $entity;
        $this->_guids[] = (string) $entity->getGuid();
    }

    /**
     * Find an entity by it's guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return Tracks\Model\Entity
     */
    public function find(Guid $guid)
    {
        if (isset($this->_items[(string) $guid])) {
            return $this->_items[(string) $guid];
        } else {
            return null;
        }
    }

    /**
     * Remove an entity from the list by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return null
     */
    public function remove(Guid $guid)
    {
        $this->_guids = array_splice(
            $this->_guids,
            array_search((string) $guid, $this->_guids) - 1,
            1
        );
        unset($this->_items[(string) $guid]);
    }

    /**
     * Get all newly applied events to entites in this list
     *
     * @return array
     */
    public function getAllAppliedEvents()
    {
        $events = array();

        foreach ($this->_items as $item) {
            $events = array_merge($events, $item->getAllAppliedEvents());
        }

        return $events;
    }

    /**
     * Clear all applied events on entites in this list
     *
     * @return null
     */
    public function clearAllAppliedEvents()
    {
        foreach ($this->_items as $item) {
            $item->clearAllAppliedEvents();
        }
    }

    /**
     * Get all entities in this list
     *
     * @return array
     */
    public function getAllChildEntities()
    {
        return $this->getAllEntities();
    }

    /**
     * Get all entites in this list
     *
     * @return array
     */
    public function getAllEntities()
    {
        $entities = array();

        foreach ($this->_items as $entity) {
            $entities = array_merge($entities, $entity->getAllEntities());
        }

        return $entities;
    }


    //
    // ArrayAccess
    //

    /**
     * offsetExists
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return boolean
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($guid)
    {
        return array_key_exists((string) $guid, $this->_items);
    }

    /**
     * offsetGet
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return Entity
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($guid)
    {
        return $this->find($guid);
    }

    /**
     * offsetSet
     *
     * @param mixed $offset Any
     * @param mixed $value  Any
     *
     * @return null
     * @throws LogicException
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException('Use EntityList::add() instead of setting an index');
    }

    /**
     * offsetUnset
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return null
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($guid)
    {
        $this->remove($guid);
    }


    //
    // Iterator
    //

    /**
     * current
     *
     * @return Entity
     * @see Iterator::current()
     */
    public function current()
    {
        return $this->_items[$this->_guids[$this->_cursor]];
    }

    /**
     * key
     *
     * @return Guid
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->_guids[$this->_cursor];
    }

    /**
     * next
     *
     * @return null
     * @see Iterator::next()
     */
    public function next()
    {
        $this->_cursor++;
    }

    /**
     * rewind
     *
     * @return null
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->_cursor = 0;
    }

    /**
     * valid
     *
     * @return boolean
     * @see Iterator::valid()
     */
    public function valid()
    {
        return array_key_exists($this->_cursor, $this->_guids);
    }


    //
    // Countable
    //

    /**
     * count
     *
     * @return int
     * @see Countable::count()
     */
    public function count()
    {
        return count($this->_items);
    }

    /** @var array */
    private $_items = array();

    /** @var array */
    private $_guids = array();

    /** @var int */
    private $_cursor = 0;
}
