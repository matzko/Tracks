<?php
/**
 * Interface for event stores
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

interface IEventStore {
	
   public function getAllEvents(Guid $guid);
	public function getEventsFromVersion(Guid $guid, $version);
   public function getType(Guid $guid);
   public function save(Entity $entity);
}
