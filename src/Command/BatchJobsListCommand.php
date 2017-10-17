<?php


namespace Kaliop\BatchBundle\Command;


use Kaliop\BatchBundle\DependencyInjection\Compiler\RegisterJobsPass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BatchJobsListCommand extends ContainerAwareCommand
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
        $registry = $this->getContainer()
            ->get(RegisterJobsPass::REGISTRY_ID);

        $rows = [];
        foreach ($registry->allByTypes() as $type => $jobs) {
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
