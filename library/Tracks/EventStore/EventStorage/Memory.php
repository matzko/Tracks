<?php
/**
 * In-Memory event store implementation
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore\EventStorage;
use \Tracks\EventStore\IEventStore;
use \Tracks\Model\Guid, \Tracks\Model\Entity;

class Memory implements IEventStore {

   /**
    * Get all the events associated with an entity by guid
    * 
    * @param Guid $guid
    * @return array
    */
   public function getAllEvents(Guid $guid) {
      return $this->events[(string) $guid];
   }

   /** 
    * Get all events associated with an entity starting from a particular version
    * 
    * @param Guid $guid
    * @param int $version
    * @return array 
    */
	public function getEventsFromVersion(Guid $guid, $version) {
		assert('is_int($version)');
		
		return array_slice($this->events[(string) $guid], $version);
	}

   /**
    * Save an entity and it's events
    * 
    * @param Entity $entity 
    */
   public function save(Entity $entity) {
      foreach ($entity->getAllEntities() as $provider) {
         $this->createEntity($provider);
      }

      foreach ($entity->getAllAppliedEvents() as $event) {
         $this->events[(string) $event->getGuid()][] = clone $event;
         $this->incVersion($event->getGuid());
      }
   }

   /**
    * Get the object type associated with a guid
    * 
    * @param Guid $guid
    * @return string
    */
   public function getType(Guid $guid) {
      return $this->entities[(string) $guid]['type'];
   }

   /**
    * Create a new entity entry if it doesn't already exist
    * 
    * @param Entity $entity 
    */
   private function createEntity(Entity $entity) {
      if (!isset($this->entities[(string) $entity->getGuid()])) {
         $this->entities[(string) $entity->getGuid()] = array(
            'type' => get_class($entity),
            'version' => 0,
            'guid' => $entity->getGuid());
      }

      if (!isset($this->events[(string) $entity->getGuid()])) {
         $this->events[(string) $entity->getGuid()] = array();
      }
   }

   /**
    * Increment the version of an entity
    *
    * @param Guid $guid 
    */
   private function incVersion(Guid $guid) {
      $this->entities[(string) $guid]['version']++;
   }

   /** @var array */
   private $events = array();
   
   /** @var array */
   private $entities = array();
}
