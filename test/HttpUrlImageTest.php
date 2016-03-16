<?php
require_once 'HttpUrlImage.php';

class HttpUrlImageTest extends PHPUnit_Framework_TestCase {

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $httpUrlImage = new HttpUrlImage($url);

        $this->assertEquals($expected, $httpUrlImage->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $url = 'https://example.com';

        $httpUrlImage = new HttpUrlImage($url);

        $this->assertTrue($httpUrlImage->isHttpProtocol());

        $httpUrlImage = new HttpUrlImage('ftp://example.com');

        $this->assertFalse($httpUrlImage->isHttpProtocol());

        $httpUrlImage = new HttpUrlImage(null);

        $this->assertFalse($httpUrlImage->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $httpUrlImage = new HttpUrlImage($url);

        $this->assertEquals('mf.jpg', $httpUrlImage->obtainFileName());
    }




    public function testDownloadTo_ObtainLocallyCachedFilePath() {
        $httpUrlImage = new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler');

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');

        $stub->method('file_exists')
            ->willReturn(true);

        $httpUrlImage->injectFileSystem($stub);

        $expirationTime = 20;

        $this->assertEquals('./cache/remote/mf.jpg', $httpUrlImage->downloadTo('./cache/remote/', $expirationTime));

    }

    public function testDownloadTo_LocallyCachedFilePathFail() {
        $httpUrlImage = new HttpUrlImage('http://martinfowler.com/mf.jpg?query=hello&s=fowler');

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);

        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $httpUrlImage->injectFileSystem($stub);

        $expirationTime = 20;

        $this->assertEquals('./cache/remote/mf.jpg', $httpUrlImage->downloadTo('./cache/remote/', $expirationTime));

    }
    /**
     * @expectedException RuntimeException
     */
    public function testDownloadTo_FallbackFail() {
        $httpUrlImage = new HttpUrlImage('file://martinfowler.com/mf.jpg?query=hello&s=fowler');

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(false);


        $httpUrlImage->injectFileSystem($stub);

        $expirationTime = 20;

        $httpUrlImage->downloadTo('./cache/remote/', $expirationTime);

    }

    public function testDownloadTo_ReferenceToServerFile() {
        $httpUrlImage = new HttpUrlImage('images/dog.jpg');

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);


        $httpUrlImage->injectFileSystem($stub);

        $expirationTime = 20;

        $this->assertEquals('images/dog.jpg', $httpUrlImage->downloadTo('./cache/remote/', $expirationTime));
    }


}
