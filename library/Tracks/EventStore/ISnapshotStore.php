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

namespace Tracks\EventStore;
use Tracks\Model\Guid, Tracks\Model\Entity;

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
interface ISnapshotStore
{
    /**
     * Load an Entity
     *
     * @param Guid $guid An Entity's GUID
     *
     * @return Entity|null
     */
    public function load(Guid $guid);

    /**
     * Save an Entity
     *
     * @param Entity $entity An Entity
     *
     * @return null
     */
    public function save(Entity $entity);
}
