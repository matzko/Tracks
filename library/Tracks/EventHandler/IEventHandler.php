<?php
/**
 * Interface for event handlers
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventHandler;

interface IEventHandler {
	
   public function execute(\Tracks\Event\Base $event);
}
