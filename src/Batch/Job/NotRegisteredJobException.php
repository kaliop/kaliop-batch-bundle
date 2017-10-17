<?php


namespace Kaliop\BatchBundle\Batch\Job;

/**
 * Class NotRegisteredJobException
 */
class NotRegisteredJobException extends \Exception
{
    /**
     * NotRegisteredJobException constructor.
     * @param string $jobCode
     */
    public function __construct($jobCode)
    {
        $message = sprintf('The job "%s" is not registered', $jobCode);

        parent::__construct($message);
    }
}
