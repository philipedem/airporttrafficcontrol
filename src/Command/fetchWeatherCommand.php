<?php

namespace App\Command;

use App\Repository\WeatherRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:fetch-weather')]
class fetchWeatherCommand extends Command
{
    private $weatherRespository;

    protected static $defaultName = 'app:fetch-weather';

    public function __construct(WeatherRepository $respository)
    {
        $this->weatherRespository = $respository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Get current weather forecast updates around the Airport')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = $this->weatherRespository->fetchWeatherUpdates();
        //
        $io->success(sprintf('Current weather update obtained: "%s"', $count));
        //
        return Command::SUCCESS;
    }
}