<?php
/**
 * Repository to load and save domain event-based entities
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore;
use Tracks\EventHandler\IEventRouter;
use Tracks\Model\AggregateRoot, Tracks\Model\Entity, Tracks\Model\Guid;

class Repository {
	
   const SNAPSHOT_FREQUENCY = 100;

   /**
    * Constructor
    * 
    * @param IEventStore $eventStore
    * @param IEventRouter $router
    * @param ISnapshotStore $snapshotStore 
    */
   public function __construct(IEventStore $eventStore, IEventRouter $router, ISnapshotStore $snapshotStore) {
      $this->eventStore = $eventStore;
		$this->snapshotStore = $snapshotStore;
		$this->router = $router;
		$this->snapshotFrequency = self::SNAPSHOT_FREQUENCY;
   }

   /**
    * Load an entity by a guid
    * 
    * @param Guid $guid
    * @return \Tracks\Model\AggregateRoot
    */
   public function load(Guid $guid) {
      if (is_null($aggregateRoot = $this->loadEntity($guid))) {
         return NULL;
      }

      if (($children = $aggregateRoot->getAllChildEntities())) {         
         foreach ($children as &$child) {
            $child = $this->loadEntity($child->getGuid(), $child);
         }            
      }

      return $aggregateRoot;
   }

   /**
    * Set the frequency with which to take snapshots of entities
    * 
    * @param int $numEvents 
    */
	public function setSnapshotFrequency($numEvents = self::SNAPSHOT_FREQUENCY) {
      assert('$numEvents > 0');
      
		$this->snapshotFrequency = $numEvents;
	}

   /**
    * Save an aggregate root, and call event handlers for all new events
    * 
    * @param AggregateRoot $aggregateRoot
    */
   public function save(AggregateRoot $aggregateRoot) {
      if (count($aggregateRoot->getAllAppliedEvents()) == 0) {
			return;	
		}

      $this->eventStore->save($aggregateRoot);
		$this->routeEvents($aggregateRoot);
      $this->updateVersionsAndClearEvents($aggregateRoot);
      $this->saveSnapshots($aggregateRoot);
   }

   /**
    * Store an entity in the identity map
    * 
    * @param Entity $entity 
    */
   private function storeInIdentityMap(Entity $entity) {
      $this->identityMap[(string) $entity->getGuid()] = $entity;
   }

   /**
    * Attempt to load an entity from the identity map
    * 
    * @param Guid $guid
    * @return \Tracks\Model\Entity
    */
   private function loadFromIdentityMap(Guid $guid) {
      return (isset($this->identityMap[(string) $guid]) ? $this->identityMap[(string) $guid] : NULL);
   }

   /**
    * Load an entity from it's event history
    * 
    * @param Guid $guid
    * @param Entity $entity
    * @return \Tracks\Model\Entity
    */
   private function loadFromHistory(Guid $guid, Entity $entity = NULL) {
      $events = $this->eventStore->getAllEvents($guid);
      
      if (is_null($entity)) {
	      if (is_null($modelClass = $this->eventStore->getType($guid))) {
            return NULL;
         }
         
			$entity = new $modelClass;	
		}
		
      $entity->loadHistory($events);

      return $entity;
   }

   /**
    * Load an entity into memory
    * 
    * @param Guid $guid
    * @param Entity $entity
    * @return Tracks\Model\Entity
    */
   private function loadEntity(Guid $guid, Entity $entity = NULL) {
      $loadedEntity = $this->loadFromIdentityMap($guid);

      if (is_null($loadedEntity)) {
         $loadedEntity = $this->loadFromSnapshot($guid);
      }

      if (is_null($loadedEntity)) {
         $loadedEntity = $this->loadFromHistory($guid, $entity);
      }

      if (is_null($loadedEntity)) {
         return NULL;
      }
      
      $this->storeInIdentityMap($loadedEntity);

      return $loadedEntity;
   }

   /**
    * Route all new events on an aggregate to their appropriate handlers
    * 
    * @param AggregateRoot $aggregateRoot 
    */
   private function routeEvents(AggregateRoot $aggregateRoot) {
      foreach ($aggregateRoot->getAllAppliedEvents() as $event) {
         $this->router->route($event);
      }
   }

   /**
    * Attempt to load an entity from a snapshot
    * 
    * @param Guid $guid
    * @return \Tracks\Model\Entity
    */
   private function loadFromSnapshot(Guid $guid) {
      if (is_null($entity = $this->snapshotStore->load($guid))) {
         return NULL;
      }

		$entity->loadHistory($this->eventStore->getEventsFromVersion($entity->getGuid(), $entity->getVersion() + 1));

      return $entity;
   }

   /**
    * Update an entity's in-memory version and clear applied events
    * This should be called after an entity's events have been saved
    */
   private function updateVersionsAndClearEvents(AggregateRoot $aggegrateRoot) {
      foreach ($aggegrateRoot->getAllEntities() as $entity) {
         $entity->incVersion(count($entity->getAppliedEvents()));
         $entity->clearAppliedEvents();
      }
   }

   /**
    * Attempt to save a snapshot for all entites in an aggregate root
    * 
    * @param AggregateRoot $aggregateRoot 
    */
   private function saveSnapshots(AggregateRoot $aggregateRoot) {
      foreach ($aggregateRoot->getAllEntities() as $entity) {
         $this->saveSnapshot($entity);
      }
   }

   /**
    * Try to save a snapshot of an entity if frequency determines it is time
    * 
    * @param Entity $entity 
    */
   private function saveSnapshot(Entity $entity) {
      $state = clone $entity;
      $state->clearAppliedEvents();

      $snapshot = $this->snapshotStore->load($state->getGuid());

      if ((!$snapshot && $state->getVersion() >= $this->snapshotFrequency)
         || ($snapshot && ($state->getVersion() - $snapshot->getVersion()) >= $this->snapshotFrequency)
      ) {
         echo 'Saving snapshot', PHP_EOL;
			$this->snapshotStore->save($entity);
      }
   }

	/** @var int */
	private $snapshotFrequency;
   
   /** @var array */
   private $identityMap = array();
}
