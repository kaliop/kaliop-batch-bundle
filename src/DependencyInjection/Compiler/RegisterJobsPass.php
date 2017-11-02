<?php


namespace Kaliop\BatchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterJobsPass
 */
class RegisterJobsPass implements CompilerPassInterface
{
    const REGISTRY_ID = 'kaliop_batch.job.job_registry';

    const SERVICE_TAG = 'kaliop_batch.job';

    const DEFAULT_JOB_TYPE = 'default';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::REGISTRY_ID)) {
            $definition = (new Definition('Kaliop\BatchBundle\Batch\Job\JobRegistry'))->setPublic(true);
            $container->setDefinition(
                self::REGISTRY_ID,
                $definition
            );
        }

        $registryDefinition = $container->getDefinition(self::REGISTRY_ID);
        foreach ($container->findTaggedServiceIds(self::SERVICE_TAG) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (empty($tag['code'])) {
                    throw new \Exception(sprintf(
                        'Missing code for service with tag %s',
                        self::SERVICE_TAG
                    ));
                }
                $type = isset($tag['type']) ? $tag['type'] : self::DEFAULT_JOB_TYPE;
                $job = new Reference($serviceId);
                $registryDefinition->addMethodCall('register', [$job, $tag['code'], $type]);
            }
        }
    }
}
