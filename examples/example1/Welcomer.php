<?php
/**
 * Example Event Handler
 *
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

class Welcomer implements \Tracks\EventHandler\IEventHandler {

   public function execute(\Tracks\Event\Base $event) {
      echo "Welcome aboard, {$event->name}!\n";
   }
}