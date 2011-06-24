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

namespace Tracks\EventStore\SnapshotStorage;
use \Tracks\EventStore\ISnapshotStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

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
class Memory implements ISnapshotStore
{

    /**
     * Load a snapshot by guid
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return Tracks\Model\Entity
     */
    public function load(Guid $guid)
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
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity)
    {
        $this->_snapshots[(string) $entity->getGuid()] = clone $entity;
    }

    /** @var array */
    private $_snapshots = array();
}