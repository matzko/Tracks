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
use Tracks\EventStore\ISnapshotStore;
use Tracks\Model\Entity, Tracks\Model\Guid;


/**
 * Filesystem-based Snapshot Storage
 *
 * @category   Tracks
 * @package    EventStore
 * @subpackage SnapshotStorage
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class File implements ISnapshotStore
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
     * @param Entity $entity An Entity
     *
     * @return null
     * @see Tracks\EventStore.ISnapshotStore::save()
     */
    public function save(Entity $entity)
    {
        file_put_contents(
            $this->_getFilename($entity->getGuid()), serialize($entity)
        );
    }

    /**
     * Load an Entity
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return Entity|null
     * @see Tracks\EventStore.ISnapshotStore::load()
     */
    public function load(Guid $guid)
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
     * @param Guid $guid An Entity's GUID
     *
     * @return string
     */
    private function _getFilename(Guid $guid)
    {
        return $this->_directory
            .DIRECTORY_SEPARATOR
            .(string) $guid
            .'.dat';
    }

    /** @var string */
    private $_directory;
}