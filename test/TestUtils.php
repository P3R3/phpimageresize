<?php


class TestUtils
{
    const OUT_FILE = '/out/file';

    public static function mockRequired() {
        $defaults = array(
            'output-filename' => self::OUT_FILE,
            'w' => 800,
            'h' => 600);
        return $defaults;
    }

    public static function mockConfiguration() {
        $required = TestUtils::mockRequired();
        return new Configuration($required);
    }
}