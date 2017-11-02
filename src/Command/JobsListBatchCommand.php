<?php


namespace Kaliop\BatchBundle\Command;


use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JobsListBatchCommand extends AbstractBatchCommand
{
    /**
     * Command configuration
     */
    public function configure()
    {
        $this
            ->setName('kaliop:batch:job-list')
            ->setDescription('List registered batch jobs')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $rows = [];
        foreach ($this->jobRegistry->allByTypes() as $type => $jobs) {
            foreach (array_keys($jobs) as $code) {
                $rows[] = [$code, $type];
            }
        }

        (new Table($output))
            ->setHeaders(['Job code', 'Type'])
            ->setRows($rows)
            ->render()
        ;

        return 0;
    }
}
