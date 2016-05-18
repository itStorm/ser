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

    // @todo временно тут задем переместит в parameters.yml
    const REPORTS_PATH = '/home/vagrant/wwwroot/ser/reports';
    const OLD_REPORT_TIME_DIFF = 600; // 10 min

    protected function configure()
    {
        $this
            ->setName('reports:clear-files')
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
        $debug = $input->getOption('debug');
        $currentTimestamp = time();

        $directoryIterator = new \DirectoryIterator(static::REPORTS_PATH);

        foreach ($directoryIterator as $item) {
            if (!$item->getFileInfo()->isFile()
                || in_array($item->getFilename(), ['.gitignore'])
                || $currentTimestamp - $item->getATime() < static::OLD_REPORT_TIME_DIFF
            ) {
                continue;
            }

            $filePath = static::REPORTS_PATH . '/' . $item->getFilename();
            unlink(static::REPORTS_PATH . '/' . $item->getFilename());
            if ($debug) {
                $output->writeln("Delete file {$filePath}");
            }
        }
    }
}