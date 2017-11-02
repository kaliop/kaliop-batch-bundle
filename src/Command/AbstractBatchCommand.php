<?php


namespace Kaliop\BatchBundle\Command;


use Kaliop\BatchBundle\Batch\Job\JobRegistry;
use Kaliop\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 */
abstract class AbstractBatchCommand extends Command
{

    /** @var LoggerInterface */
    protected $logger;

    /** @var JobRegistry */
    protected $jobRegistry;

    /**
     * AbstractCommand constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, $test = null)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    public function setApplication(Application $application = null)
    {
        parent::setApplication($application);
        $this->jobRegistry = $application->getKernel()->getContainer()->get(RegisterJobsPass::REGISTRY_ID);
    }
}
