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

require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'bootstrap.php';
require_once 'Employer.php';
require_once 'Employee.php';
require_once 'Position.php';
require_once 'Welcomer.php';

$router = new Tracks_EventHandler_DirectRouter;
$router->addHandler('EventEmployeeAdded', 'Welcomer');

$repository = new Tracks_EventStore_Repository(
    new Tracks_EventStore_EventStorage_Memory,
    $router,
    new Tracks_EventStore_SnapshotStorage_Memory
);

$employer = new Employer;
$employerGuid = $employer->create('Planet Express');
$leelaGuid = $employer->addNewEmployee('Turanga Leela', 'Captain');
$fryGuid = $employer->addNewEmployee('Philip Fry', 'Delivery Boy');

$repository->save($employer);


$employer = $repository->load($employerGuid);
$employer->changeEmployeeTitle($fryGuid, 'Narwhal Trainer');

$repository->save($employer);

echo PHP_EOL.$employer->name.PHP_EOL;

foreach ($employer->employees as $employee) {
    echo '  - '.$employee->name.', '.$employee->position->title.PHP_EOL;
}
