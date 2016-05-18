<?php

namespace Ser\Command;

use Ser\Common\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ClearReportsCommand
 * @package Ser\Command
 */
class ClearReportsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('amqp:consumer-logfile')
            ->setDescription('Handling uploaded log files')
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'Set for debug consumer process'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        var_dump($input->getOption('debug'));
        $output->writeln('Consumer');
    }
}