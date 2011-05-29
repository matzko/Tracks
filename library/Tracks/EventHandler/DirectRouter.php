<?php
/**
 * Routes directly to an event handler object in the same process
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventHandler;

class DirectRouter implements IEventRouter {

   /**
    * Route an event
    * 
    * @param Tracks\Event\Base $event 
    */
   public function route(\Tracks\Event\Base $event) {
		if (isset($this->handlers[get_class($event)])) {
      	foreach ($this->handlers[get_class($event)] as $handler) {
				if (is_string($handler)) {
					$handler = new $handler;
				}
			
         	$handler->execute($event);
			}
      }
   }

   /**
    * Add an event handler to the routing table
    * The 2nd argument may be either an instantiated object, or the name of a class to instantiate
    * In the second case, the class should not have any required parameters on it's constructor
    * 
    * @param string $eventClass
    * @param IEventHandler|string $handler 
    */
	public function addHandler($eventClass, $handler) {
		if (is_object($handler) && !($handler instanceof \Tracks\EventHandler\IEventHandler)) {
			throw new Exception('Event handlers must implement \Tracks\EventHandler\IEventHandler');
		}
		
		if (!isset($this->handlers[$eventClass])) {
			$this->handlers[$eventClass] = array();
		}
		
		$this->handlers[$eventClass][] = $handler;
	}
   
   /** @var array */
	private $handlers = array();
}
