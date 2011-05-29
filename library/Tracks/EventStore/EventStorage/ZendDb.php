<?php
/**
 * Zend_Db based implementation of the event store. 
 * Requires two tables in a relational database compatible with Zend_Db
 * See the schema directory for SQL to create the necessary tables
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventStore\EventStorage;
use Tracks\EventStore\IEventStore;
use Tracks\Model\Guid, Tracks\Model\Entity;

class ZendDb implements IEventStore {
   
   /**
    * Constructor
    * 
    * @param \Zend_Db_Adapter_Abstract $dbh 
    */
   public function __construct(\Zend_Db_Adapter_Abstract $dbh) {
      $this->dbh = $dbh;
   }

   /**
    * Get all events associated with a guid
    * 
    * @param Guid $guid
    * @return array
    */
   public function getAllEvents(Guid $guid) {
      $events = array();
      
      $rows = $this->dbh->fetchAll('SELECT * FROM event WHERE guid = ? ORDER BY date_created, id', (string) $guid);
      
      foreach ($rows as $row) {
         $events[] = unserialize($row['data']);
      }
      
      return $events;      
   }
   
   /**
    * Save an entity to the data store
    * 
    * @param Entity $entity 
    */
   public function save(Entity $entity) {
      foreach ($entity->getAllEntities() as $child) {
         $this->createEntity($child);
      }

      foreach ($entity->getAllAppliedEvents() as $event) {
         $this->createEvent($event);
         $this->incVersion($event->getGuid());
      }
   }

   /**
    * Get events associated with a guid, starting with a specific version number
    * 
    * @param Guid $guid
    * @param int $version
    * @return array 
    */
	public function getEventsFromVersion(Guid $guid, $version) {
      $events = array();
      
      $rows = $this->dbh->fetchAll('SELECT * FROM event WHERE guid = ? OFFSET ? ORDER BY date_created, id', array((string) $guid, $version));
      
      foreach ($rows as $row) {
         $events[] = unserialize($row['data']);
      }
      
      return $events; 
   }

   /**
    * Get the object type of an entity
    * 
    * @param Guid $guid
    * @return string
    */
   public function getType(Guid $guid) {
      if (is_null($row = $this->getEntityByGuid($guid))) {
         return NULL;
      }
      
      return $row['type'];
   }
   
   /**
    * Create an event record
    * 
    * @param \Tracks\Event\Base $event 
    */
   private function createEvent(\Tracks\Event\Base $event) {
      $this->dbh->insert('event', array('guid' => $event->getGuid(), 'data' => serialize($event)));
   }
   
   /**
    * Create an entity record
    * 
    * @param Entity $entity 
    */
   private function createEntity(Entity $entity) {
      if (is_null($this->getEntityByGuid($entity->getGuid()))) {
         $this->dbh->insert('entity', array('guid' => (string) $entity->getGuid(), 'type' => get_class($entity)));
      }
   }
   
   /**
    * Get the entity record by guid
    * 
    * @param Guid $guid
    * @return array
    */
   private function getEntityByGuid(Guid $guid) {
      if (($row = $this->dbh->fetchRow('SELECT * FROM entity WHERE guid = ?', array((string) $guid)))) {
         return $row;
      } else {
         return NULL;
      }      
   }
   
   /**
    * Increment the version of an entity in the data store
    * 
    * @param Guid $guid 
    */
   private function incVersion(Guid $guid) {
      $this->dbh->update('entity', array('version' => new \Zend_Db_Expr('(version + 1)')), array('guid = ?' => (string) $guid));
   }
   
   /** @var Zend_Db_Adapter_Abstract */
   private $dbh;
}
