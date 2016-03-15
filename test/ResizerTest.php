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

    public function testObtainLocallyCachedFilePath() {
        $configuration = TestUtils::mockConfiguration();
        $httpUrlImage = new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($httpUrlImage, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');

        $stub->method('file_exists')
            ->willReturn(true);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testLocallyCachedFilePathFail() {
        $configuration = TestUtils::mockConfiguration();
        $httpUrlImage = new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($httpUrlImage, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);

        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testCreateNewPath() {
        $resizer = new Resizer(new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler'), TestUtils::mockConfiguration());
    }

}
