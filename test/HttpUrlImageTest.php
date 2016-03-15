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

}
