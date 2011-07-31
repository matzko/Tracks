<?php
/**
 * Tracks CQRS Framework
 *
 * PHP Version 5.3
 *
 * @category  Tracks
 * @package   Event
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */

/**
 * Domain Event base class
 *
 * @category  Tracks
 * @package   Event
 * @author    Sean Crystal <sean.crystal@gmail.com>
 * @copyright 2011 Sean Crystal
 * @license   http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link      https://github.com/spiralout/Tracks
 */
abstract class Tracks_Event_Base
{

    /**
     * Constructor
     *
     * @param Tracks_Model_Guid $guid Event GUID
     *
     * @return null
     */
    public function __construct(Tracks_Model_Guid $guid)
    {
        $this->guid = $guid;
    }

    /**
     * Get the entity guid this event is associated with
     *
     * @return Tracks_Model_Guid
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /** @var Tracks_Model_Guid */
    public $guid;
}
