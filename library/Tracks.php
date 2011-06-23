<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Tracks
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */


/**
 * Tracks library autoloader
 *
 * @param string $className Class name
 *
 * @return null
 */
function tracksAutoloader($className)
{
    $file = __DIR__
        .DIRECTORY_SEPARATOR
        .str_replace('\\', DIRECTORY_SEPARATOR, $className)
        .'.php';

    if (file_exists($file)) {
        include $file;
    }
}

spl_autoload_register('tracksAutoloader');