<?php


namespace Kaliop\BatchBundle\Batch\Step;

use Kaliop\BatchBundle\Batch\Job\JobExecution;

/**
 * Interface StepInterface
 */
interface StepInterface
{
    public function execute(JobExecution $jobExecution);
}
