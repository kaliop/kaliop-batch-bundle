<?php


namespace Kaliop\BatchBundle\Batch\Step;


use Kaliop\BatchBundle\Batch\Job\JobExecution;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractStep
 */
abstract class AbstractStep implements StepInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * AbstractStep constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    abstract protected function doExecute(JobExecution $jobExecution);

    final public function execute(JobExecution $jobExecution)
    {
        $this->doExecute($jobExecution);
    }
}
