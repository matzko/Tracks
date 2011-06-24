<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

namespace Tracks\Model;

/**
 * Globally-Unique Identifier (GUID) implementation
 *
 * All entites must have a guid.
 *
 * @category  Tracks
 * @package   Model
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
class Guid
{

    /**
     * Constructor
     *
     * @param string|null $guid A GUID
     *
     * @return null
     */
    public function __construct($guid = null)
    {
        assert('is_string($guid) || is_null($guid)');
        $this->guid = $guid;
    }

    /**
     * Return a string representation of this guid
     *
     * @return string
     */
    public function __toString()
    {
        return $this->guid;
    }

    /**
     * Guid factory method
     *
     * @return \Tracks\Model\Guid
     */
    static public function create()
    {
        return new self(uniqid('', true));
    }

    /** @var string */
    public $guid;
}
