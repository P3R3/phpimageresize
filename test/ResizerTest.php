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
    public function testOptionalCollaboration() {
        $resizer = new Resizer('nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer(TestUtils::mockConfiguration()));
    }


    public function testCreateNewPath() {
        $resizer = new Resizer(TestUtils::mockConfiguration());
    }

    //

    public function testDoResize_WhenNotPanoramicAndAllInformedExceptScale() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => false,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "125" -size "125x126" xc:"red" +swap -gravity center -composite -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126_cp.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(112, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }
    public function testDoResize_WhenPanoramicAndAllInformedExceptScale() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => false,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "x126" -size "125x126" xc:"red" +swap -gravity center -composite -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126_cp.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(512, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }

    public function testDoResize_WhenPanoramicAndAllInformedExceptCropAndScale() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => false,
            Configuration::SCALE_KEY => false,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "125" -size "125x126" xc:"red" +swap -gravity center -composite -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(512, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }


    //


    public function testDoResize_WhenAllInformedAndNotPanoramic() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => true,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "125" -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126_cp_sc.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(112, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }
    public function testDoResize_WhenAllInformedAndPanoramic() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => true,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "x126" -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126_cp_sc.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(512, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }

    public function testDoResize_WhenPanoramicAndAllInformedExceptCrop() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => false,
            Configuration::SCALE_KEY => true,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125,
            Configuration::HEIGHT_KEY => 126);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "125" -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_h126_sc.jpg"', array())
            ->willReturn(0);
        $stub->method('getimagesize')
            ->with($imagePath)
            ->willReturn(array(512, 112));

        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }

    public function testDoResize_WhenAllInformedExceptWidth() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => true,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::HEIGHT_KEY => 125);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -thumbnail x\> -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_h125_cp_sc.jpg"', array())
            ->willReturn(0);
        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }
    public function testDoResize_WhenAllInformedExceptHeight() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::CROP_KEY => true,
            Configuration::SCALE_KEY => true,
            Configuration::CANVAS_COLOR_KEY => 'red',
            Configuration::QUALITY_KEY => 180,
            Configuration::MAX_ONLY_KEY => true,
            Configuration::WIDTH_KEY => 125);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -thumbnail 125\> -quality "180" "./cache/df1555ec0c2d7fcad3a03770f9aa238a_w125_cp_sc.jpg"', array())
            ->willReturn(0);
        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }
    public function testDoResize_WhenDefaultExceptWidth() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::WIDTH_KEY => null,
            );
        $opts=array_merge(TestUtils::mockRequired(), $opts);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -thumbnail x -quality "90" "/out/file"', array())
            ->willReturn(0);
        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }
    public function testDoResize_WhenDefaultExceptHeight() {
        $imagePath = './cache/remote/mf.jpg';
        $opts = array(
            Configuration::HEIGHT_KEY => null,
        );
        $opts=array_merge(TestUtils::mockRequired(), $opts);


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -thumbnail 800 -quality "90" "/out/file"', array())
            ->willReturn(0);
        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }

    public function testDoResize_WhenDefault() {
        $imagePath = './cache/remote/mf.jpg';
        $opts=TestUtils::mockRequired();


        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('md5_file')
            ->willReturn('df1555ec0c2d7fcad3a03770f9aa238a');
        $stub->method('getExtension')
            ->willReturn('.jpg');
        $stub->method('exec')
            ->with('convert "./cache/remote/mf.jpg" -resize "x600" -size "800x600" xc:"transparent" +swap -gravity center -composite -quality "90" "/out/file"', array())
            ->willReturn(0);
        $configuration = new Configuration($opts);
        $configuration->injectFileSystem($stub);
        $resizer = new Resizer($configuration);
        $resizer->injectFileSystem($stub);

        $resizer->doResize($imagePath);
    }


}
