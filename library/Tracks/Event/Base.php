<?php
/**
 * Domain Event base class
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\Event;
use Tracks\Model\Guid;

abstract class Base {
	
   /**
    * Constructor
    * 
    * @param Guid $guid 
    */
   public function __construct(Guid $guid) {
      $this->guid = $guid;
   }
   
   /**
    * Get the entity guid this event is associated with
    * 
    * @return Tracks\Model\Guid
    */
   public function getGuid() {
      return $this->guid;
   }
   
   /** @var \Tracks\Model\Guid */
   public $guid;
}
