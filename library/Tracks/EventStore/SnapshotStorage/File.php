<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage Tracks_EventStore_SnapshotStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

/**
 * Filesystem-based Snapshot Storage
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage Tracks_EventStore_SnapshotStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class Tracks_EventStore_SnapshotStorage_File implements Tracks_EventStore_ISnapshotStore
{

    /**
     * Constructor
     *
     * @param string $directory Directory path
     */
    public function __construct($directory)
    {
        assert('is_string($directory)');
        assert('file_exists($directory)');
        assert('is_writable($directory)');
        $this->_directory = $directory;
    }

    /**
     * Save an Entity
     *
     * @param Tracks_Model_Entity $entity An Tracks_Model_Entity
     *
     * @return null
     * @see Tracks_EventStore_ISnapshotStore::save()
     */
    public function save(Tracks_Model_Entity $entity)
    {
        file_put_contents(
            $this->_getFilename($entity->getGuid()), serialize($entity)
        );
    }

    /**
     * Load an Entity
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return Tracks_Model_Entity|null
     * @see Tracks_EventStore_ISnapshotStore::load()
     */
    public function load(Tracks_Model_Guid $guid)
    {
        if (file_exists($this->_getFilename($guid))) {
            return unserialize(file_get_contents($this->_getFilename($guid)));
        } else {
            return null;
        }
    }

    /**
     * Get the filename of an Entity by its GUID
     *
     * @param Tracks_Model_Guid $guid An Entity's GUID
     *
     * @return string
     */
    private function _getFilename(Tracks_Model_Guid $guid)
    {
        return $this->_directory
            .DIRECTORY_SEPARATOR
            .(string) $guid
            .'.dat';
    }

    /** @var string */
    private $_directory;
}
