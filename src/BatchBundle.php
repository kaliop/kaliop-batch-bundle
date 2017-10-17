<?php


namespace Kaliop\BatchBundle;


use Kaliop\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BatchBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterJobsPass());
    }
}
