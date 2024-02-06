<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenWeatherClient
{
    private ParameterBagInterface $parameterBag;
    private HttpClientInterface $client;

    public function __construct(ParameterBagInterface $parameterBag, HttpClientInterface $client) {
        $this->parameterBag = $parameterBag;
        $this->client = $client;
    }

    public function getData(): array
    {
        $host = $this->parameterBag->get('app.open-weather.host');
        $latitude = $this->parameterBag->get('app.open-weather.latitude');
        $longitude = $this->parameterBag->get('app.open-weather.longitude');
        $apiKey = $this->parameterBag->get('app.open-weather.key');

        $url = sprintf(
            '%s?lat=%s&lon=%s&appid=%s&units=metric',
            $host,
            $latitude,
            $longitude,
            $apiKey
        );

        $response = $this->client->request(
            'GET',
            $url,
        );

        return json_decode($response->getContent(), true);
    }
}
