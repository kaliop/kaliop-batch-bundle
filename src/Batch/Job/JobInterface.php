<?php


namespace Kaliop\BatchBundle\Batch\Job;

/**
 * Interface JobInterface
 */
interface JobInterface
{
    public function execute(JobExecution $jobExecution);
}
