<?php


namespace Kaliop\BatchBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractCommand
 */
abstract class AbstractCommand extends Command
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
