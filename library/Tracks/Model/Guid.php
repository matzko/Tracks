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
class Tracks_Model_Guid
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
     * @return Tracks_Model_Guid
     */
    static public function create()
    {
        return new self(Tracks_Model_Guid::uuid());
    }

    /**
     * UUID v4
     *
     * @return string
     * @link   http://www.php.net/manual/en/function.uniqid.php#94959
     * @link   http://www.ietf.org/rfc/rfc4122.txt
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /** @var string */
    public $guid;
}
