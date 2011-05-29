<?php
/**
 * Example Domain Value Object
 *
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

class Position {

   public function __construct($title) {
      $this->title = $title;
   }

   public $title;
}
 
