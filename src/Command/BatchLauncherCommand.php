<?php


namespace Kaliop\BatchBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class BatchLauncherCommand
 */
class BatchLauncherCommand extends Command
{
    const PROGRESS_TOTAL = 'total';
    const PROGRESS_SUCCESS = 'success';
    const PROGRESS_ERRORS = 'errors';

    /** @var array */
    private $progressData = [
        self::PROGRESS_TOTAL => 0,
        self::PROGRESS_SUCCESS => 0,
        self::PROGRESS_ERRORS => 0,
    ];

    private $consolePath;
    private $phpBinaryPath;

    /**
     * BatchLauncherCommand constructor.
     * @param KernelInterface $kernel
     * @param string|null $phpBinaryPath
     */
    public function __construct(KernelInterface $kernel, string $phpBinaryPath = null)
    {
        $this->consolePath = $kernel->getRootDir() . '/../bin/console';

        $this->phpBinaryPath = $phpBinaryPath;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('kaliop:batch:launch')
            ->setDescription('Batch command launcher')
            ->addArgument('code', InputArgument::REQUIRED, 'Job code')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Job configuration parameters', '{}');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->assertValidPhp();
        $jobCode = $input->getArgument('code');
        $config = $input->getOption('config');
        $stopExecution = false;
        $offset = 0;
        $env = $input->getOption('env');

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $progress = new ProgressBar($output);
            $progress->setFormat('Info: %message% - Memory usage: %memory%');
            $progress->start();
        }

        while (!$stopExecution) {
            $job = sprintf("%s %s kaliop:batch:job %s --config='%s' --offset=%s --env='%s'",
                $this->phpBinaryPath,
                $this->consolePath,
                $jobCode,
                $config,
                $offset,
                $env);

            $process = new Process($job);
            $process->setTimeout(500);
            $process->mustRun();
            $res = json_decode($process->getOutput(), true);
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $this->updateProgress($res, $progress);
            }
            $stopExecution = $res['finished'];
            $offset += $res['total'];
        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $progress->finish();
            $output->writeln('');
        }

        return 0;
    }

    protected function updateProgress(array $data = null, ProgressBar $progress)
    {
        if ($data) {
            $this->progressData[self::PROGRESS_TOTAL] += $data[self::PROGRESS_TOTAL];
            $this->progressData[self::PROGRESS_SUCCESS] += $data[self::PROGRESS_SUCCESS];
            $this->progressData[self::PROGRESS_ERRORS] += $data[self::PROGRESS_ERRORS];
        }

        $message = sprintf(
            'Items treated: %s - Success: %s - Errors: %s',
            $this->progressData[self::PROGRESS_TOTAL],
            $this->progressData[self::PROGRESS_SUCCESS],
            $this->progressData[self::PROGRESS_ERRORS]
        );
        $progress->setMessage($message);
        $progress->advance();
    }

    /**
     *
     */
    private function assertValidPhp()
    {
        $givenPath = $this->phpBinaryPath;
        if (!$this->phpBinaryPath) {
            $phpBinaryFinder = new PhpExecutableFinder();
            $this->phpBinaryPath = $phpBinaryFinder->find();
        }

        if (!$this->phpBinaryPath) {
            throw new \InvalidArgumentException('
                Can\'t find php binary file,
                please provide it in bundle configuration,
                php_binary_path: path,
                provided path is: ' . $givenPath
            );
        }
    }
}
