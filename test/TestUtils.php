<?php

/**
 * Created by PhpStorm.
 * User: Pere
 * Date: 15/03/2016
 * Time: 21:31
 */
class TestUtils
{
    const OUT_FILE = '/out/file';

    public static function mockRequired() {
        $defaults = array(
            'output-filename' => self::OUT_FILE,
            'width' => 800,
            'height' => 600);
        return $defaults;
    }

    public static function mockConfiguration() {
        $required = TestUtils::mockRequired();
        return new Configuration($required);
    }
}