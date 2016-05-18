<?php
namespace Ser;

use OldSound\RabbitMqBundle\OldSoundRabbitMqBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class App
 * @package Ser
 */
class SerKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles()
    {
        return [
            new OldSoundRabbitMqBundle(),
            new SerBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}