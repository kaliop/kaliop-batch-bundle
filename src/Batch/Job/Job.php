<?php


namespace Kaliop\BatchBundle\Batch\Job;


use Kaliop\BatchBundle\Batch\Step\ItemStep;
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
     * @param \Kaliop\BatchBundle\Batch\Job\JobExecution $jobExecution
     */
    final public function execute(JobExecution $jobExecution)
    {
        /** @var ItemStep $step */
        foreach ($this->steps as $step) {
            $step->execute($jobExecution);
        }
    }
}
