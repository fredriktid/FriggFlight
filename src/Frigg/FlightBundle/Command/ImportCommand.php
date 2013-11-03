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
    protected function configure()
    {
        $this
            ->setName('frigg_flight:import')
            ->setDescription('Import flights')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'What to import (e.g. flights)?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $timer = microtime(true);
        $container = $this->getContainer();
        $type = $input->getArgument('type');

        switch($type) {
            case 'flight':
                $output->writeln('Running flight importer');
                $importer = $container->get('frigg_flight.flight_import');
                $importer->run();
                echo $importer;
                break;

            case 'flight_status':
                $output->writeln('Running flight status importer');
                $importer = $container->get('frigg_flight.flight_status_import');
                $importer->run();
                echo $importer;
                break;

            case 'airport':
                $output->writeln('Running airport importer');
                $importer = $container->get('frigg_flight.airport_import');
                $importer->run();
                echo $importer;
                break;

            case 'airline':
                $output->writeln('Running airline importer');
                $importer = $container->get('frigg_flight.airline_import');
                $importer->run();
                echo $importer;
                break;

            default:
                $output->writeln('Unknown type');
                break;
        }

        $output->writeln(sprintf('Took %.02f ms', ((microtime(true) - $timer) * 1000)));
    }
}
