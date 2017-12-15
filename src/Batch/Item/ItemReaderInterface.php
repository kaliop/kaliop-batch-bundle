<?php


namespace Kaliop\BatchBundle\Batch\Item;
use Kaliop\BatchBundle\Batch\Job\JobExecution;

/**
 * Interface ItemReaderInterface
 * @package Kaliop\BatchBundle\Batch\Item
 */
interface ItemReaderInterface
{
    public function read(JobExecution $jobExecution);
}
