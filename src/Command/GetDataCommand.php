<?php

namespace App\Command;

use App\Entity\WeatherLogEntry;
use App\Service\OpenWeatherClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetDataCommand extends Command
{
    protected static $defaultName = 'app:get-data';

    private OpenWeatherClient $openWeatherClient;
    private EntityManagerInterface $entityManager;

    public function __construct(OpenWeatherClient $openWeatherClient, EntityManagerInterface $entityManager)
    {
        $this->openWeatherClient = $openWeatherClient;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->openWeatherClient->getData();

        $location = $data['name'];
        $temparature = $data['main']['temp'];
        $humidity = $data['main']['humidity'];

        $logEntry = new WeatherLogEntry();

        $logEntry->setLocation($location)
            ->setTemparature($temparature)
            ->setHumidity($humidity)
            ->setDatetime(new \DateTime());

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();

        $message = sprintf(
           'Momentane temparatur in %s beträgt %s °C und die Luftfeuchtigkeit beträgt %s %%',
            $location,
            $temparature,
            $humidity
        );

        $output->writeln($message);

        return 1;
    }
}
