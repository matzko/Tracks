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
 * Example Domain Aggregate Root
 *
 * @category   Tracks
 * @package    Examples
 * @subpackage Example1
 * @author     Sean Crystal <sean.crystal@gmail.com>
 * @copyright  2011 Sean Crystal
 * @license    http://www.opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @link       https://github.com/spiralout/Tracks
 */
class Employer extends \Tracks\Model\AggregateRoot
{
    public function __construct()
    {
        $this->employees = new \Tracks\Model\EntityList;
        $this->registerEvents();
    }

    public function create($name)
    {
        $guid = \Tracks\Model\Guid::create();
        $this->applyEvent(new EventEmployerCreated($guid, $name));
        return $guid;
    }

    public function addNewEmployee($name, $title)
    {
        $employeeGuid = \Tracks\Model\Guid::create();
        $this->applyEvent(new EventEmployeeAdded($this->getGuid(), $employeeGuid, $name));
        $this->employees->find($employeeGuid)->changeTitle($title);
        return $employeeGuid;
    }

    public function changeEmployeeTitle(\Tracks\Model\Guid $employeeGuid, $title)
    {
        if ($employee = $this->employees->find($employeeGuid)) {
            $employee->changeTitle($title);
        }
    }

    protected function onEmployerCreated(EventEmployerCreated $event)
    {
        $this->guid = $event->guid;
        $this->name = $event->name;
    }

    protected function onEmployeeAdded(EventEmployeeAdded $event)
    {
        $this->employees->add(new Employee($event->employeeGuid, $event->name));
    }

    private function registerEvents()
    {
        $this->registerEvent('EventEmployerCreated', 'onEmployerCreated');
        $this->registerEvent('EventEmployeeAdded', 'onEmployeeAdded');
    }

    public $name;
    public $employees;
}

class EventEmployerCreated extends \Tracks\Event\Base
{
    public function __construct($guid, $name)
    {
        parent::__construct($guid);
        $this->name = $name;
    }

    public $name;
}

class EventEmployeeAdded extends \Tracks\Event\Base
{
    public function __construct(\Tracks\Model\Guid $guid, $employeeGuid, $name)
    {
        parent::__construct($guid);
        $this->employeeGuid = $employeeGuid;
        $this->name = $name;
    }

    public $employeeGuid;
    public $name;
}


