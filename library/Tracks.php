<?php
/**
 * Tracks CQRS Framework
 * 
 * @author Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 * @link https://github.com/spiralout/Tracks
 */


/**
 * Tracks library autoloader
 * 
 * @param string $className 
 */
function tracksAutoloader($className) {
   $file = __DIR__ .'/'. str_replace('\\', '/', $className) .'.php';
   
   if (file_exists($file)) {
      include $file;
   }
}

spl_autoload_register('tracksAutoloader');