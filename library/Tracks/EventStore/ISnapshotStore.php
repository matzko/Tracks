<?php
/**
 * Interface for snapshot stores
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore;
use Tracks\Model\Guid, Tracks\Model\Entity;

interface ISnapshotStore {
	
   public function load(Guid $guid);
   public function save(Entity $entity);
}
