<?php


namespace Kaliop\BatchBundle\Command;


use Kaliop\BatchBundle\Batch\Job\JobExecution;
use Kaliop\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BatchCommand extends AbstractCommand
{
    const EXIT_SUCCESS_CODE = 0;
    const EXIT_ERROR_CODE = 1;
    const EXIT_WARNING_CODE = 2;

    public function configure()
    {
        $this
            ->setName('kaliop:batch:job')
            ->setDescription('Launch a registered job instance')
            ->addArgument('code', InputArgument::REQUIRED, 'Job code');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $logger = $container->get('batch.logger');
        $style = new OutputFormatterStyle('yellow', 'black');
        $output->getFormatter()->setStyle('warning', $style);

        $jobCode = $input->getArgument('code');
        $jobExecution = new JobExecution($output, [
            'code' => $jobCode,
            'verbosity' => $output->getVerbosity(),
        ]);

        try {
            $jobInstance = $container
                ->get(RegisterJobsPass::REGISTRY_ID)
                ->get($jobCode);
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf('<info>Job "%s" started</info>', $jobCode));
            }
            $jobInstance->execute($jobExecution);

            $stats = $jobExecution->getStats();
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf('<info>Job "%s" executed</info>', $jobCode));
                $output->writeln(sprintf(
                    '<info>Total items: %d. Success: %d.</info> <warning>Warnings: %d.</warning> <error>Failures: %d</error>',
                    $stats['total'],
                    $stats['success'],
                    $stats['warnings'],
                    $stats['failures']
                ));
            }
            $logger->info(sprintf(
                'Total items: %d. Success: %d. Warnings: %d. Failures: %d',
                $stats['total'],
                $stats['success'],
                $stats['warnings'],
                $stats['failures']
            ));

            return self::EXIT_SUCCESS_CODE;

        } catch (\Exception $e) {
            $logger->error(sprintf(
               '%s',
               $e->getMessage()
            ));

            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return self::EXIT_ERROR_CODE;
        }
    }
}
