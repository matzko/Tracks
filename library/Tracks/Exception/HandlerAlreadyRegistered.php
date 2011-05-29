<?php
/**
 * Exception thrown when trying to register an event on a domain that has already been registered
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\Exception;

class HandlerAlreadyRegistered extends Base {
   
   /**
    * Constructor
    * 
    * @param string $domainClass
    * @param string $eventClass
    * @param string $existingHandler 
    */
   public function __construct($domainClass, $eventClass, $existingHandler) {
      parent::__construct("Handler already registered on object of type {$domainClass} for event {$eventClass}: {$existingHandler}");
   }
}