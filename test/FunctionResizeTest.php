<?php


require_once 'function.resize.php';
require_once 'TestUtils.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {


    private $defaults = array(
        'crop' => false,
        'scale' => 'false',
        'thumbnail' => false,
        'maxOnly' => false,
        'canvas-color' => 'transparent',
        'cacheFolder' => './cache/',
        'remoteFolder' => './cache/remote/',
        'quality' => 90,
        'cache_http_minutes' => 20,
    );



    public function testOpts()
    {
        $configuration = TestUtils::mockConfiguration();
        $this->assertInstanceOf('Configuration', $configuration);
    }

    public function testOptsDefaults() {
        $defaults = array_merge(TestUtils::mockRequired(), $this->defaults);

        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals($defaults, $configuration->asHash());
    }

    public function testDefaultsNotOverwriteConfiguration() {

        $opts = array(
            'thumbnail' => true,
            'maxOnly' => true
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);

        $configuration = new Configuration($opts);
        $configured = $configuration->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }

    public function testObtainCache() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals('./cache/', $configuration->obtainCache());
    }

    public function testObtainRemote() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals('./cache/remote/', $configuration->obtainRemote());
    }

    public function testObtainConvertPath() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals('convert', $configuration->obtainConvertPath());
    }

    public function testObtainOutputFileName() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals(TestUtils::OUT_FILE, $configuration->obtainOutputFileName());
    }

    public function test_WhenRequiredOuputFileName_ThenOK() {
        $defaults = array(
            'output-filename' => null,
            'width' => 125,
            'height' => 125);

        $configuration = new Configuration($defaults);
    }
    public function test_WhenRequiredWidth_ThenOK() {
        $defaults = array(
            'output-filename' => '/out/file',
            'width' => null,
            'height' => 125);

        $configuration = new Configuration($defaults);
    }
    public function test_WhenRequiredHeight_ThenOK() {
        $defaults = array(
            'output-filename' => '/out/file',
            'width' => 125,
            'height' => null);

        $configuration = new Configuration($defaults);
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function test_WhenNoneRequired_ThenError() {
        $defaults = null;

        $configuration = new Configuration($defaults);
    }


}

?>
