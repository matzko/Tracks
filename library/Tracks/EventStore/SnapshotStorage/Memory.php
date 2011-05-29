<?php
/**
 * In-Memory snapshot store implementation
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore\SnapshotStorage;
use \Tracks\EventStore\ISnapshotStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

class Memory implements ISnapshotStore {
	
   /**
    * Load a snapshot by guid
    * 
    * @param Guid $guid
    * @return Tracks\Model\Entity
    */
	public function load(Guid $guid) {
		if (isset($this->snapshots[(string) $guid])) {
			return $this->snapshots[(string) $guid];
		} else {
			return NULL;
		}
	}
	
   /**
    * Save a snapshot of an entity
    * 
    * @param Entity $entity 
    */
   public function save(Entity $entity) {
		$this->snapshots[(string) $entity->getGuid()] = clone $entity;
	}
	
   /** @var array */
	private $snapshots = array();
}