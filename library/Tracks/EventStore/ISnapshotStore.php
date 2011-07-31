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
 * Interface for snapshot stores
 *
 * @category  Tracks
 * @package   EventStore
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
interface Tracks_EventStore_ISnapshotStore
{
    /**
     * Load an Entity
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_Entity|null
     */
    public function load(Tracks_Model_Guid $guid);

    /**
     * Save an Entity
     *
     * @param Tracks_Model_Entity $entity An Entity
     *
     * @return null
     */
    public function save(Tracks_Model_Entity $entity);
}
