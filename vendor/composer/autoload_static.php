<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7356d1b746522ef12c2f628fe003741d
{
    public static $files = array (
        'c5e08fc8ba5fbedece65fe3c0d6c543d' => __DIR__ . '/..' . '/atk4/atk4/lib/static.php',
    );

    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/..' . '/atk4/atk4/lib',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->fallbackDirsPsr0 = ComposerStaticInit7356d1b746522ef12c2f628fe003741d::$fallbackDirsPsr0;

        }, null, ClassLoader::class);
    }
}
