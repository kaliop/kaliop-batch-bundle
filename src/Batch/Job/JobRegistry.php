<?php

namespace Kaliop\BatchBundle\Batch\Job;


/**
 * Class JobRegistry
 */
class JobRegistry
{
    /** @var JobInterface[] */
    protected $jobs = [];

    /** @var JobInterface[][] */
    protected $jobsByType = [];

    /**
     * @param JobInterface $job
     * @param string       $code
     *
     * @throws \Exception
     */
    public function register(JobInterface $job, $code, $type)
    {
        if (isset($this->jobs[$code])) {
            throw new \Exception(
                sprintf('The job "%s" is already registered', $code)
            );
        }
        $this->jobs[$code] = $job;
        $this->jobsByType[$type][$code] = $job;
    }

    /**
     * @param string $jobCode
     *
     * @throws \Exception
     *
     * @return JobInterface
     */
    public function get($jobCode)
    {
        if (!isset($this->jobs[$jobCode])) {
            throw new NotRegisteredJobException($jobCode);
        }

        return $this->jobs[$jobCode];
    }

    /**
     * @return JobInterface[]
     */
    public function all()
    {
        return $this->jobs;
    }

    /**
     * @param string $jobType
     *
     * @throws \Exception
     *
     * @return JobInterface[]
     */
    public function allByType($jobType = null)
    {
        if (!isset($this->jobsByType[$jobType])) {
            throw new \Exception(
                sprintf('There is no registered job with the type "%s"', $jobType)
            );
        }

        return $this->jobsByType[$jobType];
    }

    /**
     * @return JobInterface[][]
     */
    public function allByTypes()
    {
        return $this->jobsByType;
    }
}
