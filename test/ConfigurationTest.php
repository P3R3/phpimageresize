<?php


require_once 'Configuration.php';
require_once 'TestUtils.php';

class ConfigurationTest extends PHPUnit_Framework_TestCase {


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

        $this->assertEquals('./cache/', $configuration->obtainOutputFolder());
    }

    public function obtainDownloadFolder() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals('./cache/remote/', $configuration->obtainDownloadFolder());
    }

    public function testObtainConvertPath() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals('convert', $configuration->obtainConvertPath());
    }

    public function testObtainOutputFileName() {
        $configuration = TestUtils::mockConfiguration();

        $this->assertEquals(TestUtils::OUT_FILE, $configuration->obtainOutputFileName());
    }

    public function testWithCrop() {
        $opts = array(
            'crop' => true
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);
        $configuration = new Configuration($opts);

        $this->assertEquals(true, $configuration->withCrop());
    }
    public function testWithoutCrop() {
        $opts = array(
            'crop' => false
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);
        $configuration = new Configuration($opts);

        $this->assertEquals(false, $configuration->withCrop());
    }

    public function testWithScale() {
        $opts = array(
            'scale' => true
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);
        $configuration = new Configuration($opts);

        $this->assertEquals(true, $configuration->withScale());
    }
    public function testWithoutScale() {
        $opts = array(
            'scale' => false
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);
        $configuration = new Configuration($opts);

        $this->assertEquals(false, $configuration->withScale());
    }


    public function test_WhenRequiredOuputFileName_ThenOK() {
        $defaults = array(
            'output-filename' => null,
            'w' => 125,
            'h' => 125);

        $configuration = new Configuration($defaults);
    }
    public function test_WhenRequiredWidth_ThenOK() {
        $defaults = array(
            'output-filename' => '/out/file',
            'w' => null,
            'h' => 125);

        $configuration = new Configuration($defaults);
    }
    public function test_WhenRequiredHeight_ThenOK() {
        $defaults = array(
            'output-filename' => '/out/file',
            'w' => 125,
            'h' => null);

        $configuration = new Configuration($defaults);
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function test_WhenNoneRequired_ThenError() {
        $defaults = null;

        $configuration = new Configuration($defaults);
    }


    public function testObtainOutputFilePath_WhenOutputFileNameInformed() {
        $configuration = TestUtils::mockConfiguration();
        $sourceFilePath = './cache/remote/mf.jpg';

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('pathinfo')
            ->willReturn(array('extension' => 'jpg'));
        $configuration->injectFileSystem($stub);

        $outputFilePath = $configuration->obtainOutputFilePath($sourceFilePath);

        $this->assertEquals($configuration->obtainOutputFileName(), $outputFilePath);
    }
    public function testObtainOutputFilePath_WhenOutputFileNameNotInformed() {
        $opts = array(
            'scale' => false,
            'output-filename' => null,
            'w' => 125,
            'h' => 125);
        $opts=array_merge(TestUtils::mockRequired(), $opts);
        $configuration = new Configuration($opts);
        $sourceFilePath = './cache/remote/mf.jpg';

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('pathinfo')
            ->willReturn(array('extension' => 'jpg'));
        $configuration->injectFileSystem($stub);

        $outputFilePath = $configuration->obtainOutputFilePath($sourceFilePath);

        $this->assertEquals('./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h125.jpg', $outputFilePath);
    }
    public function testObtainOutputFilePath_WhenAllInformedExceptOutputFileName() {
        $opts = array(
            'crop' => true,
            'scale' => true,
            'output-filename' => null,
            'w' => 125,
            'h' => 125);
        $configuration = new Configuration($opts);
        $sourceFilePath = './cache/remote/mf.jpg';

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('pathinfo')
            ->willReturn(array('extension' => 'jpg'));
        $configuration->injectFileSystem($stub);

        $outputFilePath = $configuration->obtainOutputFilePath($sourceFilePath);

        $this->assertEquals('./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h125_cp_sc.jpg', $outputFilePath);
    }


}

?>
