<?php


namespace Kaliop\BatchBundle\Batch\Event;

/**
 * Interface EventInterface
 */
interface EventInterface
{
    /** Job execution events */
    const BEFORE_JOB_EXECUTION = 'kaliop_batch.before_job_execution';
    const JOB_EXECUTION_STOPPED = 'kaliop_batch.job_execution_stopped';
    const JOB_EXECUTION_INTERRUPTED = 'kaliop_batch.job_execution_interrupted';
    const JOB_EXECUTION_FATAL_ERROR = 'kaliop_batch.job_execution_fatal_error';
    const BEFORE_JOB_STATUS_UPGRADE = 'kaliop_batch.before_job_status_upgrade';
    const AFTER_JOB_EXECUTION = 'kaliop_batch.after_job_execution';

    /** Step execution events */
    const BEFORE_STEP_EXECUTION = 'kaliop_batch.before_step_execution';
    const STEP_EXECUTION_SUCCEEDED = 'kaliop_batch.step_execution_succeeded';
    const STEP_EXECUTION_INTERRUPTED = 'kaliop_batch.step_execution_interrupted';
    const STEP_EXECUTION_ERRORED = 'kaliop_batch.step_execution_errored';
    const STEP_EXECUTION_COMPLETED = 'kaliop_batch.step_execution_completed';
    const INVALID_ITEM = 'kaliop_batch.invalid_item';
}
