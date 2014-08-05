<?php

namespace Frigg\FlightBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ImportCommand extends ContainerAwareCommand
{
    /**
    * Configuration of command
    **/
    protected function configure()
    {
        $this
            ->setName('frigg:import')
            ->setDescription('Import data from Avinor')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'What to import (flight, airline, airport, etc)'
            )
            ->addOption(
                'from',
                null,
                InputOption::VALUE_REQUIRED,
                'How many hours in the past (default 1)',
                1
            )
            ->addOption(
                'to',
                null,
                InputOption::VALUE_REQUIRED,
                'How many hours in the future (default 7)',
                24
            )
            ->addOption(
                'updates',
                null,
                InputOption::VALUE_NONE,
                'Only updates since last run'
            );
    }

    /**
    * Execute command
    * @var InputInterface $input
    * @var OutputInterface $output
    **/
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timer = microtime(true);
        $container = $this->getContainer();
        $type = $input->getArgument('type');

        switch($type) {
            case 'flight':
                $output->writeln('Running flight importer');
                $time = array();
                $time['from'] = (int) $input->getOption('from');
                $time['to'] = (int) $input->getOption('to');
                $importer = $container->get('frigg.flight.import');
                $importer->setUpdates($input->getOption('updates'));
                $importer->setTime($time);
                $importer->run();
                $output->writeln($importer->output());
                break;

            case 'flight_status':
                $output->writeln('Running status importer');
                $importer = $container->get('frigg.status.import');
                $importer->run();
                $output->writeln($importer->output());
                break;

            case 'airport':
                $output->writeln('Running airport importer');
                $importer = $container->get('frigg.airport.import');
                $importer->run();
                $output->writeln($importer->output());
                break;

            case 'airline':
                $output->writeln('Running airline importer');
                $importer = $container->get('frigg.airline.import');
                $importer->run();
                $output->writeln($importer->output());
                break;

            default:
                $output->writeln(sprintf('Unknown type %s.', $type));
                break;
        }

        $output->writeln(sprintf('Took %.02f seconds', ((microtime(true) - $timer))));
    }
}
