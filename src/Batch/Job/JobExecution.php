<?php


namespace Kaliop\BatchBundle\Batch\Job;


use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class JobExecution
 */
class JobExecution
{
    const STAT_TOTAL = 'total';
    const STAT_SUCCESS = 'success';
    const STAT_WARNINGS = 'warnings';
    const STAT_FAILURES = 'failures';
    const STAT_FINISHED = 'finished';

    /** @var \Kaliop\BatchBundle\Batch\Job\JobParameters */
    protected $jobParameters;

    /** @var array */
    protected $stats;

    /** @var OutputInterface */
    protected $output;

    /**
     * JobExecution constructor.
     * @param OutputInterface                             $output
     * @param \Kaliop\BatchBundle\Batch\Job\JobParameters $jobParameters
     */
    public function __construct(OutputInterface $output, JobParameters $jobParameters)
    {
        $this->output = $output;
        $this->jobParameters = $this->resolveConfiguration($jobParameters);
        $this->stats = [
            self::STAT_TOTAL => 0,
            self::STAT_SUCCESS => 0,
            self::STAT_WARNINGS => 0,
            self::STAT_FAILURES => 0,
            self::STAT_FINISHED => false,
        ];
    }

    public function getJobParameters() : JobParameters
    {
        return $this->jobParameters;
    }

    /**
     * @return array
     */
    public function getStats() : array
    {
        return $this->stats;
    }

    /**
     * @param int $n
     * @return JobExecution
     */
    public function incrementTotal(int $n = 1) : JobExecution
    {
        $this->incrementStats(self::STAT_TOTAL, $n);

        return $this;
    }

    /**
     * @param int $n
     * @return JobExecution
     */
    public function incrementSuccess(int $n = 1) : JobExecution
    {
        $this->incrementStats(self::STAT_SUCCESS, $n);

        return $this;
    }

    /**
     * @param int $n
     * @return JobExecution
     */
    public function incrementWarnings(int $n = 1) : JobExecution
    {
        $this->incrementStats(self::STAT_WARNINGS, $n);

        return $this;
    }

    /**
     * @param int $n
     * @return JobExecution
     */
    public function incrementFailures(int $n = 1) : JobExecution
    {
        $this->incrementStats(self::STAT_FAILURES, $n);

        return $this;
    }

    /**
     * @param int $n
     * @return JobExecution
     */
    public function decrementTotal(int $n = 1) : JobExecution
    {
        $this->incrementTotal(-$n);

        return $this;
    }

    /**
     * @param bool $finished
     * @return JobExecution
     */
    public function setFinished(bool $finished) : JobExecution
    {
        $this->stats[self::STAT_FINISHED] = $finished;

        return $this;
    }

    /**
     * @param string $key
     * @param int $n
     */
    private function incrementStats(string $key, int $n)
    {
        $this->stats[$key] += $n;
    }

    /**
     * @param \Kaliop\BatchBundle\Batch\Job\JobParameters $jobParameters
     * @return \Kaliop\BatchBundle\Batch\Job\JobParameters
     */
    private function resolveConfiguration(JobParameters $jobParameters) : JobParameters
    {
        return new JobParameters(
            (new OptionsResolver())
                ->setRequired(['job_code', 'verbosity', 'offset'])
                ->setDefaults([
                    'options' => ''
                ])
                ->setAllowedTypes('job_code', 'string')
                ->setAllowedTypes('verbosity', 'int')
                ->setAllowedTypes('offset', 'string')
                ->setAllowedTypes('options', 'string')
                ->setAllowedValues('verbosity', [
                    OutputInterface::VERBOSITY_QUIET,
                    OutputInterface::VERBOSITY_NORMAL,
                    OutputInterface::VERBOSITY_VERBOSE,
                    OutputInterface::VERBOSITY_VERY_VERBOSE,
                    OutputInterface::VERBOSITY_DEBUG,
                ])
                ->setNormalizer('offset', function(Options $options, $value) {
                    return (int) $value;
                })
                ->setNormalizer('options', function(Options $options, $value) {
                    return json_decode($value, true);
                })
                ->resolve($jobParameters->toArray())
        );
    }
}
