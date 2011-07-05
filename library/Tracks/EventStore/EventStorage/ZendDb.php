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
use Tracks\EventStore\IEventStore;
use Tracks\Model\Guid, Tracks\Model\Entity;

/**
 * Zend_Db based implementation of the event store.
 *
 * Requires two tables in a relational database compatible with Zend_Db. See
 * the schema directory for SQL to create the necessary tables.
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage EventStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class ZendDb implements IEventStore
{

    /**
     * Constructor
     *
     * @param \Zend_Db_Adapter_Abstract $dbh A Zend Database Adapter
     *
     * @return null
     */
    public function __construct(\Zend_Db_Adapter_Abstract $dbh)
    {
        $this->_dbh = $dbh;
    }

    /**
     * Get all events associated with a guid
     *
     * @param Guid $guid Any GUID
     *
     * @return array
     */
    public function getAllEvents(Guid $guid)
    {
        $select = $this->_dbh->select()
            ->from('event', array('*'))
            ->where('guid = ?', (string) $guid)
            ->order('date_created')
            ->order('id');
        $rows = $this->_dbh->fetchAll($select);

        $events = array();
        foreach ($rows as $row) {
            $events[] = unserialize($row['data']);
        }

        return $events;
    }

    /**
     * Save an entity to the data store
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity)
    {
        foreach ($entity->getAllEntities() as $child) {
            $this->_createEntity($child);
        }

        foreach ($entity->getAllAppliedEvents() as $event) {
            $this->_createEvent($event);
            $this->_incVersion($event->getGuid());
        }
    }

    /**
     * Get events associated with a guid, starting with a specific version number
     *
     * @param Guid $guid    An Entity's GUID
     * @param int  $version That Entity's version number
     *
     * @return array
     */
    public function getEventsFromVersion(Guid $guid, $version)
    {
        assert('is_int($version)');
        $select = $this->_dbh->select()
            ->from('event', array('*'))
            ->where('guid = ?', (string) $guid)
            ->order('date_created')
            ->order('id')
            ->limit($version, PHP_INT_MAX);
        $rows = $this->_dbh->fetchAll($select);

        $events = array();
        foreach ($rows as $row) {
            $events[] = unserialize($row['data']);
        }

        return $events;
    }

    /**
     * Get the object type of an entity
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return string
     */
    public function getType(Guid $guid)
    {
        if (is_null($row = $this->_getEntityByGuid($guid))) {
            return null;
        }

        return $row['type'];
    }

    /**
     * Create an event record
     *
     * @param \Tracks\Event\Base $event An Event
     *
     * @return null
     */
    private function _createEvent(\Tracks\Event\Base $event)
    {
        $this->_dbh->insert(
        	'event',
            array('guid' => $event->getGuid(), 'data' => serialize($event))
        );
    }

    /**
     * Create an entity record
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    private function _createEntity(Entity $entity)
    {
        if (is_null($this->_getEntityByGuid($entity->getGuid()))) {
            $this->_dbh->insert(
            	'entity',
                array(
                	'guid' => (string) $entity->getGuid(),
                	'type' => get_class($entity)
                )
            );
        }
    }

    /**
     * Get the entity record by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return array
     */
    private function _getEntityByGuid(Guid $guid)
    {
        $select = $this->_dbh->select()
            ->from('event', array('*'))
            ->where('guid = ? ', (string) $guid);
        $row = $this->_dbh->fetchRow($select);

        if (empty($row)) {
            return null;
        }
        return $row;
    }

    /**
     * Increment the version of an entity in the data store
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return null
     */
    private function _incVersion(Guid $guid)
    {
        $this->_dbh->update(
        	'entity',
            array('version' => new \Zend_Db_Expr('(version + 1)')),
            array('guid = ?' => (string) $guid)
        );
    }

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_dbh;
}
