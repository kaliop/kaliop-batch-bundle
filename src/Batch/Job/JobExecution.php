<?php


namespace Kaliop\BatchBundle\Batch\Job;


use Symfony\Component\Console\Output\OutputInterface;
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

    /** @var array */
    protected $config;

    /** @var array */
    protected $stats;

    /** @var OutputInterface */
    protected $output;

    /**
     * JobExecution constructor.
     * @param OutputInterface $output
     * @param array $config
     */
    public function __construct(OutputInterface $output, array $config)
    {
        $this->output = $output;
        $this->config = $this->resolveConfiguration($config);
        $this->stats = [
            self::STAT_TOTAL => 0,
            self::STAT_SUCCESS => 0,
            self::STAT_WARNINGS => 0,
            self::STAT_FAILURES => 0,
        ];
    }

    /**
     * @return OutputInterface
     */
    public function getOutput() : OutputInterface
    {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->config['code'];
    }

    /**
     * @return int
     */
    public function getVerbosity() : int
    {
        return $this->config['verbosity'];
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
     * @param string $key
     * @param int $n
     */
    private function incrementStats(string $key, int $n)
    {
        $this->stats[$key] += $n;
    }

    /**
     * @param array $config
     * @return array
     */
    private function resolveConfiguration(array $config)
    {
        return (new OptionsResolver())
            ->setRequired(['code', 'verbosity'])
            ->setAllowedTypes('code', 'string')
            ->setAllowedValues('verbosity', [
                OutputInterface::VERBOSITY_QUIET,
                OutputInterface::VERBOSITY_NORMAL,
                OutputInterface::VERBOSITY_VERBOSE,
                OutputInterface::VERBOSITY_VERY_VERBOSE,
                OutputInterface::VERBOSITY_DEBUG,
            ])
            ->resolve($config);
    }
}
