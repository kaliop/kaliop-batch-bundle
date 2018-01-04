<?php


namespace Kaliop\BatchBundle\Command;


use Kaliop\BatchBundle\Batch\Job\JobRegistry;
use Kaliop\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->jobRegistry = $this->getApplication()->getKernel()->getContainer()->get(RegisterJobsPass::REGISTRY_ID);
    }
}
