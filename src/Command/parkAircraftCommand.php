<?php

namespace App\Command;

use App\Repository\AircraftsRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:aircrafts-park')]
class parkAircraftCommand extends Command
{
    private $aircraftRespository;

    protected static $defaultName = 'app:aircrafts-park';

    public function __construct(AircraftsRepository $respository)
    {
        $this->aircraftRespository = $respository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ground Crew parking every LANDED aircraft')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = $this->aircraftRespository->parkLandedAircrafts();
        //
        $io->success(sprintf('Total of "%s" Aircrafts parked gracefully.', $count));
        //
        return Command::SUCCESS;
    }
}