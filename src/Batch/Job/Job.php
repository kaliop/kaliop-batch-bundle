<?php


namespace Kaliop\BatchBundle\Batch\Job;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Job
 */
class Job implements JobInterface
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var array */
    protected $steps;

    /**
     * Job constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param array $steps
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, array $steps)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->steps = $steps;
    }

    /**
     * Execute the job
     */
    final public function execute(JobExecution $jobExecution)
    {
        foreach ($this->steps as $step) {
            $step->execute($jobExecution);
        }
    }
}
