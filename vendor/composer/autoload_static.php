<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita1696f8ae0bb4263df72ee28bd7f2c32
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LINE\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LINE\\' => 
        array (
            0 => __DIR__ . '/..' . '/linecorp/line-bot-sdk/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita1696f8ae0bb4263df72ee28bd7f2c32::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita1696f8ae0bb4263df72ee28bd7f2c32::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
