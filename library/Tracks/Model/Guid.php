<?php
/**
 * Guid implementation. All entites must have a guid.
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\Model;

class Guid {

   /**
    * Constructor
    * 
    * @param string $guid 
    */
   public function __construct($guid = NULL) {
      $this->guid = $guid;
   }

   /**
    * Return a string representation of this guid
    * 
    * @return string
    */
   public function __toString() {
      return $this->guid;
   }

   /**
    * Guid factory method
    * 
    * @return \Tracks\Model\Guid
    */
   static public function create() {
      return new self(uniqid('', TRUE));
   }

   /** @var string */
   public $guid;
}
