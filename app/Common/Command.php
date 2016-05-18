<?php
namespace Ser\Common;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Command
 * @package Ser\Common
 */
abstract class Command extends SymfonyCommand implements ContainerAwareInterface
{
    /** @var  ContainerInterface */
    protected $container;

    /** @inheritdoc */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}