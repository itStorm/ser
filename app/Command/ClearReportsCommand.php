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

    /** @var string */
    protected $path;
    /** @var  int */
    protected $oldReportTimeDiff;

    /** @inheritdoc */
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

    /** @inheritdoc */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->path = $this->getContainer()->getParameter('ser.reports.path');
        $this->oldReportTimeDiff = $this->getContainer()->getParameter('ser.reports.old_file_time_diff');
    }

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $debug = $input->getOption('debug');
        $currentTimestamp = time();

        $directoryIterator = new \DirectoryIterator($this->path);

        foreach ($directoryIterator as $item) {
            if (!$item->getFileInfo()->isFile()
                || in_array($item->getFilename(), ['.gitignore'])
                || $currentTimestamp - $item->getATime() < $this->oldReportTimeDiff
            ) {
                continue;
            }

            $filePath = $this->path . '/' . $item->getFilename();
            unlink($this->path . '/' . $item->getFilename());
            if ($debug) {
                $output->writeln("Delete file {$filePath}");
            }
        }
    }
}