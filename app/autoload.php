<?php
use Symfony\Component\ClassLoader\ClassMapGenerator;
use Composer\Autoload\ClassLoader;

/** @var ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

$loader->addPsr4('Ser\\', __DIR__ . '/');


/*
 * Below example for load non psr0, psr4 libraries
 */
//$classMapCache = __DIR__ . '/cache/class_map.php';
//if (!file_exists($classMapCache)) {
//    ClassMapGenerator::dump([
//        __DIR__ . '/somePath',
//    ], $classMapCache);
//}
///** @var array $classMap */
//$classMap = require $classMapCache;
//$loader->addClassMap($classMap);
