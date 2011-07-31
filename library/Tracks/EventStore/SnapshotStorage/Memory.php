<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage SnapshotStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

/**
 * In-Memory snapshot store implementation
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage SnapshotStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class Tracks_EventStore_SnapshotStorage_Memory implements Tracks_EventStore_ISnapshotStore
{

    /**
     * Load a snapshot by guid
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_Entity
     */
    public function load(Tracks_Model_Guid $guid)
    {
        if (isset($this->_snapshots[(string) $guid])) {
            return $this->_snapshots[(string) $guid];
        } else {
            return null;
        }
    }

    /**
     * Save a snapshot of an entity
     *
     * @param Tracks_Model_Entity $entity An Entity
     *
     * @return null
     */
    public function save(Tracks_Model_Entity $entity)
    {
        $this->_snapshots[(string) $entity->getGuid()] = clone $entity;
    }

    /** @var array */
    private $_snapshots = array();
}
