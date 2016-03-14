<?php

/*
* This file is part of the silex2swagger library.
*
* (c) Martin Rademacher <mano@radebatz.net>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Radebatz\Console\Command;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Swagger\Logger;
use Radebatz\Silex\Swagger\Silex2SwaggerAnalysis;
use Radebatz\Silex\Swagger\Silex2SwaggerConverter;

class PsrLogger extends AbstractLogger {
    protected $logger;

    /**
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = [])
    {
        if (in_array($level, [LogLevel::NOTICE, LogLevel::INFO])) {
            $this->logger->notice($message);
        } else {
            $this->logger->warning($message);
        }
    }
}

/**
 * Silex 2 Swagger command.
 */
class Silex2SwaggerCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
        ->setName('silex2swagger:build')
        ->setDescription('Build swagger.json')
        ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Output file; if empty stdout will be used', null)
        ->addOption('path', null, InputOption::VALUE_REQUIRED, 'Source path', './src')
        ->addOption('namespace', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Additional annotation namespaces to process', [])
        ->setHelp(<<<EOT
Build swagger.json.
EOT
        )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');
        $path = $input->getOption('path');
        $namespaces = $input->getOption('namespace');

        $verbose = $input->getOption('verbose');
        Logger::getInstance()->log = function ($entry, $type) use ($verbose, $output) {
            if (!$verbose) {
                return;
            }

            if ($entry instanceof Exception) {
                $entry = $entry->getMessage();
            }
            foreach ((array) $entry as $message) {
                $output->writeln(sprintf('%s: %s', $type, $message));
            }
        };

        $logger = !$verbose ? null : new PsrLogger(Logger::getInstance());

        $swagger = \Swagger\scan($path, ['analysis' => new Silex2SwaggerAnalysis([], null, new Silex2SwaggerConverter(new Application(), null, $logger), $namespaces)]);

        if ($file) {
            file_put_contents($file, json_encode($swagger, JSON_PRETTY_PRINT));
        } else {
            $output->writeln(json_encode($swagger, JSON_PRETTY_PRINT));
        }
    }
}
