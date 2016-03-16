<?php

require_once 'Resizer.php';
require_once 'HttpUrlImage.php';
require_once 'Configuration.php';
require_once 'TestUtils.php';

date_default_timezone_set('Europe/Berlin');


class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNecessaryCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCollaboration() {
        $resizer = new Resizer(new HttpUrlImage(''), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer(new HttpUrlImage(''), TestUtils::mockConfiguration()));
    }


    public function testCreateNewPath() {
        $resizer = new Resizer(new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler'), TestUtils::mockConfiguration());
    }

}
