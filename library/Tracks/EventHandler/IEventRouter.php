<?php
/**
 * Interface for event routers
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */

namespace Tracks\EventHandler;

interface IEventRouter {
	
   public function route(\Tracks\Event\Base $event);
	public function addHandler($eventClass, $handler);
}
