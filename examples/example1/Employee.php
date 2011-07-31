<?php
/**
 * Example 1
 *
 * PHP Version 5.3
 *
 * @category   Tracks
 * @package    Examples
 * @subpackage Example1
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */

/**
 * Example Domain Entity
 *
 * @category   Tracks
 * @package    Examples
 * @subpackage Example1
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class Employee extends Tracks_Model_Entity
{
    public function __construct($guid, $name)
    {
        $this->guid = $guid;
        $this->name = $name;
        $this->registerEvents();
    }

    public function changeTitle($title)
    {
        $this->applyEvent(new EventEmployeeChangeTitle($this->getGuid(), $title));
    }

    public function onChangeTitle(EventEmployeeChangeTitle $event)
    {
        $this->position = new Position($event->title);
    }

    private function registerEvents()
    {
        $this->registerEvent('EventEmployeeChangeTitle', 'onChangeTitle');
    }

    public $name;
    public $position;
}

class EventEmployeeChangeTitle extends Tracks_Event_Base
{
    public function __construct($guid, $title)
    {
        parent::__construct($guid);
        $this->title = $title;
    }

    public $title;
}
