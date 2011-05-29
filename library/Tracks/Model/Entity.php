<?php
/**
 * Domain Entity base class
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\Model;
use Tracks\Exception\HandlerAlreadyRegistered;

abstract class Entity {

   /**
    * Load an array of events onto this entity
    * 
    * @param array $history 
    */
   public function loadHistory(array $history) {
      foreach ($history as $event) {
         $this->handleDomainEvent($event);
         $this->version++;
      }
   }

   /**
    * Get this entity's guid
    * 
    * @return Tracks\Model\Guid
    */
   public function getGuid() {
      return $this->guid;
   }

   /**
    * Get this entity's current version
    * 
    * @return int
    */
   public function getVersion() {
      return $this->version;
   }

   /**
    * Increment this entity's version
    */
   public function incVersion($increment = 1) {
      $this->version += $increment;
   }

   /**
    * Get all newly applied events to this entity
    * 
    * @return array
    */
   public function getAppliedEvents() {
      return $this->appliedEvents;
   }

   /**
    * Get all newly applied events to this entity and it's children
    * 
    * @return array
    */
   public function getAllAppliedEvents() {
      $events = array();

      foreach ($this as $property) {
         if ($property instanceof Entity || $property instanceof EntityList) {
            $events = array_merge($events, $property->getAllAppliedEvents());
         }
      }

      return array_merge($this->appliedEvents, $events);
   }

   /**
    * Clear all applied events on this entity
    */
   public function clearAppliedEvents() {
      $this->appliedEvents = array();
   }

   /**
    * Clear all applied events on this entity and it's children
    */   
   public function clearAllAppliedEvents() {
      foreach ($this as $property) {
         if ($property instanceof Entity || $property instanceof EntityList) {
            $property->clearAllAppliedEvents();
         }
      }

      $this->appliedEvents = array();
   }

   /**
    * Has this entity been created?
    * 
    * @return boolean
    */
   public function isCreated() {
      return !empty($this->guid);
   }

   /**
    * Has this entity been deleted?
    * 
    * @return boolean
    */
   public function isDeleted() {
      return $this->deleted;
   }

   /**
    * Get all child entites of this entity
    * 
    * @return array
    */
   public function getAllChildEntities() {
      $entities = array();

      foreach ($this as $property) {
         if ($property instanceof Entity || $property instanceof EntityList) {
            $entities = array_merge($entities, $property->getAllEntities());
         }
      }

      return $entities;
   }

   /**
    * Get all entities in the graph, starting from this one
    * 
    * @return array
    */
   public function getAllEntities() {
      return array_merge(array($this), $this->getAllChildEntities());
   }

   /**
    * Find a handler method registered with an event and call it if it exists
    * 
    * @param \Tracks\Event\Base $event 
    */
   protected function handleDomainEvent(\Tracks\Event\Base $event) {
      if ($handler_name = $this->getHandlerName($event)) {
         $this->$handler_name($event);
      }
   }

   /**
    * Apply a new event to this domain object
    * 
    * @param \Tracks\Event\Base $event
    */
   protected function applyEvent(\Tracks\Event\Base $event) {
      $this->handleDomainEvent($event);
      $this->appliedEvents[] = $event;
   }

   /**
    * Register an event handler on this domain object
    * 
    * @param string $eventName
    * @param string $handlerMethod 
    */
   protected function registerEvent($eventName, $handlerMethod) {
      assert('method_exists($this, $handlerMethod)');
      
      if (isset($this->handlers[$eventName])) {
         throw new HandlerAlreadyRegistered(get_class($this), $eventName, $this->handlers[$eventName]);
      }
      
      $this->handlers[$eventName] = $handlerMethod;
   }
   
   /**
    * Get the name of the handler method for an event
    * 
    * @param \Tracks\Event\Base $event
    * @return string
    */
   private function getHandlerName(\Tracks\Event\Base $event) {
      return (isset($this->handlers[get_class($event)]) ? $this->handlers[get_class($event)] : NULL);
   }
   
   /** @var \Tracks\Model\Guid */
   protected $guid;

   /** @var boolean */
   protected $deleted = FALSE;

   /** @var int */
   protected $version = 0;

   /** @var array */
   protected $appliedEvents = array();

   /** @var array */
   protected $handlers = array();
}

