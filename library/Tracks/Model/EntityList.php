<?php
/**
 * Entity list domain object. Should be used to model a list of entities instead of a plain array
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\Model;

class EntityList implements \ArrayAccess, \Iterator, \Countable {

   /**
    * Add an entity to the list
    * 
    * @param Entity $entity 
    */
   public function add(Entity $entity) {
      $this->items[(string) $entity->getGuid()] = $entity;
      $this->guids[] = (string) $entity->getGuid();
   }

   /**
    * Find an entity by it's guid
    * 
    * @param Guid $guid
    * @return Tracks\Model\Entity
    */
   public function find(Guid $guid) {
      if (isset($this->items[(string) $guid])) {
         return $this->items[(string) $guid];
      } else {
         return NULL;
      }
   }

   /**
    * Remove an entity from the list by guid
    * 
    * @param Guid $guid 
    */
   public function remove(Guid $guid) {
      $this->guids = array_splice($this->guids, array_search((string) $guid, $this->guids) - 1, 1);
      unset($this->items[(string) $guid]);
   }

   /**
    * Get all newly applied events to entites in this list
    * 
    * @return array
    */
   public function getAllAppliedEvents() {
      $events = array();

      foreach ($this->items as $item) {         
         $events = array_merge($events, $item->getAllAppliedEvents());
      }

      return $events;
   }

   /**
    * Clear all applied events on entites in this list
    */
   public function clearAllAppliedEvents() {
      foreach ($this->items as $item) {
         $item->clearAllAppliedEvents();
      }
   }

   /**
    * Get all entities in this list
    * 
    * @return array
    */
   public function getAllChildEntities() {
      return $this->getAllEntities();
   }
   
   /**
    * Get all entites in this list
    * 
    * @return array
    */
   public function getAllEntities() {
      $entities = array();

      foreach ($this->items as $entity) {
         $entities = array_merge($entities, $entity->getAllEntities());
      }

      return $entities;
   }


   //
   // ArrayAccess
   //

   public function offsetExists($guid) {
      return array_key_exists((string) $guid, $this->items);
   }

   public function offsetGet($guid) {
      return $this->find($guid);
   }

   public function offsetSet($offset, $value) {
      throw new Exception('Use EntityList::add() instead of setting an index');
   }

   public function offsetUnset($guid) {
      $this->remove($guid);
   }


   //
   // Iterator
   //

   public function current() {
      return $this->items[$this->guids[$this->cursor]];
   }

   public function key() {
      return $this->guids[$this->cursor];
   }

   public function next() {
      $this->cursor++;
   }

   public function rewind() {
      $this->cursor = 0;
   }

   public function valid() {
      return array_key_exists($this->cursor, $this->guids);
   }


   //
   // Countable
   //

   public function count() {
      return count($this->items);
   }

   /** @var array */
   private $items = array();

   /** @var array */
   private $guids = array();

   /** @var int */
   private $cursor = 0;
}
